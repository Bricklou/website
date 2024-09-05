---
title: "Projet Archipel"
date: "2023-11-06"
featured_image:
  caption: "Présentation du projet Archipel Project, une implémentation de serveur Minecraft en Rust."
  image: archipel-banner.png
tags:
  - minecraft
  - archipel
  - rust
  - micro-service
  - architecture
toc: true
---

C'est parti, premier post de ce blog !

Aujourd'hui, nous allons parler d'un projet que j'ai démarré avec une petite équipe de gens passionnés. Ce projet se nomme **Archipel Project**, il consiste à réimplémenter un serveur Minecraft en Rust sous forme de micro-services.

<!--more-->

## Introduction

Minecraft est un jeu dans un monde ouvert créé en 2011 par Markus "Notch" Persson et, par la suite, développé par le studio Mojang. Il fait partie de l'un des jeux les plus vendues au monde avec, à ce jour, plus de 238 millions de copies vendues, toute plateforme confondues.

L'un des principaux attraits à ce jeu est la possibilité d'agir librement et de laisser cours à son imagination. Il n'y a ni quêtes, ni schéma narratif, uniquement des blocs générés procéduralement, des ressources à perte de vue, et nous-même.

{{< figure src="./screenshot.webp" caption="Partie en survie en solo" alt="Capture d'écran du jeu Minecraft sur une partie en survie." >}}

Pour en revenir au projet, il fut initié sur un coup de tête (on ne va pas se le cacher 😂). Malgré le succès du jeu, il est loin de fonctionner aux meilleures performances possibles. Beaucoup de personnes se plaignent de la lourdeur et du manque d'optimisation des serveurs Java (mono-threading, grande utilisation de la mémoire, etc.), à tel point que la communauté de modding ont fait leurs propres mods pour corriger cela. C'est donc ici que nous allons intervenir, en tentant de proposer une solution, essayant de pallier ces problèmes, tout en proposant d'autres fonctionnalités.

## Objectif du projet

Comme cité précédemment, notre objectif est de faire une implémentation complète d'un serveur Minecraft dans le langage Rust. Cette dernière doit pouvoir permettre d'avoir un serveur rapide, sécurisé et surtout simple d'utilisation (au plus possible en tout cas).

Pour compléter tout cela, nous sommes partis sur une architecture micro-service, dispatchant ainsi la logique du jeu dans plusieurs petits services qui communiqueront les uns avec les autres : certains stockeront les données du monde, d'autres exécuteront la logique du jeu, et d'autre s'occuperont de gérer la connexion des joueurs et de son authentification. Cette ultra-modularité aura comme point fort de permettre aux créateurs de serveur de n'utiliser que ce dont ils ont besoins pour leur installation, le tout en restant compatible avec le client officiel !

## Qu'est-ce qu'un micro-service ?

Imaginez que vous développez une ville numérique, où chaque fonctionnalité est un petit bâtiment dédié. Chaque bâtiment s'occupe d'une tâche spécifique et fonctionne de manière autonome tout en contribuant au fonctionnement global de la ville. C'est exactement ce que sont les micro-services dans le domaine du développement informatique.

Un micro-service est une unité logicielle autonome conçue pour gérer une tâche spécifique au sein d'une application plus large. Prenons l'exemple d'une application de commerce électronique. Plutôt que de construire une seule entité complexe pour gérer toutes les fonctionnalités, vous divisez l'application en plusieurs micro-services distincts, comme l'authentification des utilisateurs, la gestion du catalogue de produits, le traitement des paiements, et ainsi de suite.

Chaque micro-service a son propre ensemble d'interfaces et sa propre base de données. De plus, il peut être développé, testé et déployé indépendamment des autres micro-services. Cela signifie que si des modifications sont nécessaires dans une fonctionnalité spécifique, elles peuvent être effectuées sans impacter le reste de l'application. C'est comme rénover un bâtiment dans votre ville numérique sans perturber le fonctionnement global.

L'approche des micro-services offre une flexibilité et une évolutivité exceptionnelles. Chaque micro-service peut être ajusté en fonction des besoins sans perturber l'ensemble de l'application. De plus, les équipes de développement peuvent travailler simultanément sur différents micro-services, accélérant ainsi le processus de développement.

En résumé, les micro-services réinventent la façon dont les applications sont conçues et construites. Ils permettent la création d'applications modulaires, flexibles et évolutives tout en facilitant la gestion, la maintenance et les mises à jour continues. À l'instar des bâtiments spécialisés dans une ville, les micro-services sont les éléments fondamentaux d'une architecture moderne et efficace.

En reprenant tous les points expliqués précédemment pour les appliquer au projet, chaque entité aura son rôle à jouer dans l'ensemble de l'infrastructure. Un exemple de service que nous aurons à implémenter sera un serveur de stockage du monde, un proxy pour la connexion des joueurs ou même un serveur d'authentification. Chacune de ces parties vont permettre de composer l'infrastructure de notre serveur de jeu.

## Conclusion

Nous espérons que vous apprécierez ce projet autant que nous. Nous n'en sommes qu'au début et il nous reste beaucoup à faire dessus. Bien entendu, vous pouvez toujours aller jeter un coup d'œil sur la page du projet et même y contribuer à son développement.

Vous pourrez retrouver plus d'informations en visitant la page [Github](https://github.com/archipel-project).

Ainsi que le blog, que nous mettrons à jour au fil du développement avec de nombreux articles de recherches : [Archipel - Dev Blog](https://archipel-project.github.io/dev-blog/).
