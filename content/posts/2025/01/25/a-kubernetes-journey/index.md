---
title: "A Kubernetes Journey"
date: "2025-01-24"
featured_image:
  caption: "Kubernetes logo"
  image: "kubernetes.png"
tags:
  - kubernetes
  - devops
  - continue-deployment
  - ci-cd
toc: true
---

Bonne année à tous ! 🎉

Pour ce premier article de 2025, nous allons parler DevOps, ou plus particulièrement Kubernetes. Cela sera l'occasion d'aborder des notions d'infrastructures,
de déploiements continue, de gestion de secrets, etc.

<!--more-->

> [!WARNING]
>
> Avant d'aller plus loin, il est important de noter que je ne suis absolument pas un expert et il est fort probable que certaines de mes manières de faire ne
> soient abssoluments pas optimales ou recommendés. N'hésitez pas à me faire part de vos retours pour m'aider à m'améliorer.

## Introduction

Cela fait maintenant plusieurs années que je possède un home-server et il n'a cessé d'évoluer sur le plan technique ou sur celui de la puissance de calcul. Au
début, il y a environs 9 ans, j'ai commencé avec un simple Raspberry Pi 2 sur lequel était installé Docker Swarm. Au fil tu temps, j'ai migré vers deux
Raspberry Pi 3 d'abord avec Docker Swarm puis avec Kubernetes, puis ajouté un Raspberry Pi 4. Ensuite, il y a environ 2 ans, j'ai remplacé ces Raspberry Pi par
deux Oranges Pi 5 équipés de disques NVME. Enfin, l'année dernière, j'ai intégré un PC neuf à mon cluster Kubernetes. Et cette année, j'ai finalement décidé de
débrancher mes Oranges Pi pour ne converver que le PC.

## Le serveur

### Le matériel

Le serveur est un PC monté moi-même pour l'occasion. Il possède les caractéristiques suivantes :

- Processeur : AMD Ryzen 5 5800G
- RAM : 32 Go (2x 16Go DDR5 5200MHz CL40)
- Carte mère: ASRock B650M-H/M.2+
- Alimentation: Cooler Master MWE Gold 550 FM (v2)
- Stockage: 1x SSD NVMe 512 Go
- Boitier: Fractal Design Core 1100

### Le système d'exploitation

Pour le choix du système d'exploitation, j'ai opté pour un choix plutôt exotique à première vue, mais assez intéressant : [NixOS](https://nixos.org/). NixOS est
une distribution Linux basée sur Nix, un gestionnaire de paquets fonctionnel. Cela implique que la configuration du système est déclarative et que les paquets
sont isolés les uns des autres. En plus de me simplifier l'installation et paquets de bases, NixOS me permet aussi de rapidement configurer kubernetes (k3s) et
d'autres services avec assez peu de configuration. Cumulé à cela, j'ai activé la fonctionnalité de [Flake](https://wiki.nixos.org/wiki/Flakes) qui m'ouvre la
voie à encore plus de capacité à la configuration.

### Le déploiement du système

Avant d'utiliser NixOS, toutes mes infrastructures utilisaient toujours une distribution basé sur Debian (Debian et Armbian plus précisément) et toute
l'installation des outils se faisaient par le biais de scripts Ansible.

Ansible et Nix Flake sont deux outils ayant un objectif similaire : permettre un déploiement automatisé et reproductible. Il faut tout de même noter que leur
procédé de fonctionnement est assez différent. Ansible est un outils de déploiement avec une configuration dite "impérative", c'est à dire que l'on décrit les
étapes à suivre pour arriver à un état donné. Nix Flake, quant à lui, est un outil de déploiement avec une configuration dite "déclarative", c'est à dire que
l'on décrit l'état final que l'on souhaite obtenir à la fin.

Cette différence de paradigme est assez importante et c'est ce qui m'a poussé à essayer NixOS. En effet, il faut savoir que jusqu'à présent, je faisais tous mes
déploiements via Ansible, mais au fur et à mesure du temps, le coût de maintenance des scripts devenait de plus en plus important en raison des mises à jours
des nombreuses dépendences nécessaires (k3s, networking, adressage des IPs, etc.). Avec Nix Flake, j'ai n'ai plus trop à me soucier de cela, et mieux encore,
des morceaux de mes configurations sont plus facilement réutilisables. (C'était déjà un peu le cas avec Ansible, mais pas autant).

> [!TIP]
>
> Pour ceux qui souhaitent en savoir plus sur NixOS, je vous recommande de lire le [manuel officiel](https://nixos.org/manual/nixos/stable/).

Voici un petit exemple de configuration Nix pour installer et configurer K3S :

```nix
# Il faut imaginer que ce fichier est importé ailleur dans la configuration, ce qui permet de
# fournir le champ `pkgs` et les autres variables.
{
  pkgs,
  kubeconfigFile,
  tokenFile,
  # Initialize HA cluster using an embedded etcd datastore.
  # If you are configuring an HA cluster with an embedded etcd,
  # the 1st server must have `clusterInit = true`
  # and other servers must connect to it using `serverAddr`.
  #
  # this can be a domain name or an IP address(such as kube-vip's virtual IP)
  masterHost,
  clusterInit ? false,
  kubeletExtraArgs ? [],
  nodeLabels ? [],
  nodeTaints ? [],
  disableFlannel ? true,
  nodeIps,
  ...
}: let
  lib = pkgs.lib;
in {
  # Installation de paquet systèmes
  environment.systemPackages = with pkgs; [
    k9s # k9s est un outil TUI pour kubernetes
    kubectl # CLI officiel pour kubernetes
    kubecolor # coloration syntaxique pour kubectl (facultatif)
    istioctl # outil de ligne de commande pour Istio
    kubernetes-helm # outil de gestion de paquets pour kubernetes
    clusterctl # CLI pour piloter kubernetes depuis son api
  ];

  # Configuration du pare-feu
  networking.firewall = {
    allowedTCPPorts = [
      6443 # k3s: required so that pods can reach the API server (running on port 6443 by default)
    ];
    allowedUDPPorts = [
      8472 # k3s, flannel: required if using multi-node for inter-node networking
    ];
  };

  # Configuration de k3s
  services.k3s = {
    enable = true;
    # On hérite des variables définies plus haut
    inherit package tokenFile clusterInit;
    serverAddr =
      if clusterInit
      then ""
      else "https://${masterHost}:6443";

    # On spécifie le role du noeud
    role = "server";

    # On spécifie les flags à passer à k3s
    # https://docs.k3s.io/cli/server
    extraFlags = let
      flagList =
        [
          "--write-kubeconfig=${kubeconfigFile}"
          "--write-kubeconfig-mode=644"
          "--kube-apiserver-arg='--allow-privileged=true'" # required by kubevirt
          "--data-dir /var/lib/rancher/k3s"
          "--etcd-expose-metrics=true"
          # to enable dual-stack, these flags are required
          # https://docs.k3s.io/networking/basic-network-options#dual-stack-ipv4--ipv6-networking
          "--cluster-cidr=10.42.0.0/16,2001:cafe:42::/56"
          "--service-cidr=10.43.0.0/16,2001:cafe:43::/112"
          # disable some features we don't need
          "--disable-helm-controller" # we use fluxcd instead
          "--disable=traefik" # deploy our own ingress controller instead
          "--disable=servicelb" # we use metallb instead
          "--tls-san=${masterHost}"
          "--node-ip=${lib.concatStringsSep "," nodeIps}"
        ]
        ++ (map (label: "--node-label=${label}") nodeLabels)
        ++ (map (taint: "--node-taint=${taint}") nodeTaints)
        ++ (map (arg: "--kubelet-arg=${arg}") kubeletExtraArgs)
        ++ (lib.optionals disableFlannel ["--flannel-backend=none"])
        ++ (lib.optionals (!disableFlannel) ["--flannel-ipv6-masq=true"]);
    in
      lib.concatStringsSep " " flagList;
  };
}
```

Comme vous pouvez le constater, la configuration est assez lisible dans l'ensemble. Le language Nix permet d'effecter des opérations assez complexes et ce
script en est un exemple. En une cinquantaine de lignes, nous avons une configuration complète pour installer et configurer k3s, configurer le pare-feu, et
installer quelques outils supplémentaires.

Si nous devions refaire la même chose avec Ansible, cela nous prendrait beaucoup plus de lignes (pas tant que ça, mais tout de même) et surtout, cela nous
demanderait de réfléchir en amont à l'ordre d'exécution des tâches à effectuer, à la gestion des erreurs, etc.

> [!CAUTION]
>
> Attention tout de même, NixOS n'est pas une solution miracle et il est possible de se retrouver dans des situations assez complexes si l'on ne fait pas
> attention. Il est donc important de bien comprendre les concepts de base avant de se lancer.
>
> Un autre point à noter, c'est que NixOS, en plus de ne pas réspecter les standards POSIX sur la structure du système de fichier (impliquant la mise en place
> de nombreux bricolages pour faire fonctionner certains programmes), demande une quantité de stockage plus importante que des distributions plus classiques.

## Pourquoi Kubernetes ?

Kubernetes est un orchestrateur de conteneurs open-source qui permet d'automatiser le déploiement, la mise à l'échelle et la gestion des applications
conteneurisées. Il est conçu pour gérer des applications conteneurisées sur un cluster de machines. Il a été conçu à l'origine par Google et est maintenant
maintenu par la Cloud Native Computing Foundation.

Dans mon cas, Kubernetes me permet de déployer et de faire la maintenance rapidement et facilement des applications sur mon serveur. Dans le cas où je déploie
un grand nomrmes de services, avoir Kubernetes me simplifie grandement la gestion de tout cela.

Néanmoins ! Qui dit simplicité au déploiement, dit complexité à la configuration de l'infrastructure. En effet, Kubernetes est un outil très puissant mais qui
peut être difficile à appréhender. Il est donc important de bien comprendre les concepts de base avant de se lancer.

## Processus de déploiement

Jusque là, j'ai surtout parlé de NixOS et de Kubernetes sans être exactement rentré dans les détails
de *pourquoi* c'est effectivement plus simple à utiliser. C'est ce que nous allons voir maintenant.

