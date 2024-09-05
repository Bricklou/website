---
title: "Let's play with MediaPipe"
date: "2024-09-05"
featured_image:
  caption: "MediaPipe logo"
  image: mediapipe.png
tags:
  - adonisjs
  - mediapipe
  - preact
  - web-components
toc: true
draft: true
---

Cet avril dernier, quelque temps après la sortie officielle de la mise à jours de [AdonisJS 6](https://adonisjs.com/), j'ai eu l'occasion de travailler sur un projet personnel qui m'a permis de découvrir et d'expérimenter avec [MediaPipe](https://mediapipe.dev/), une bibliothèque open-source de Google pour le traitement de flux multimédia.

<!--more-->

## Introduction

Dans le cadre de mon alternance, j'ai pu découvrir par le biais de mon tuteur un projet de recherche de Google nommé [MediaPipe](https://ai.google.dev/edge/mediapipe/solutions/guide). Une bibliothèque open-source qui permet de traiter des flux multimédia en temps réel. Cela va de la détection d'objets, de visages, de mains, de pose, de mouvements, etc.

Mon tuteur s'en servait pour proposer du _background replacement_ lors de ses visioconférences. C'est-à-dire, remplacer l'arrière-plan d'une vidéo par une image ou une vidéo de son choix. Permettant ainsi de cacher son environnement de travail.

Par curiosité, je me suis donc lancé dans un mini-projet pour découvrir et expérimenter avec cette bibliothèque.

## Le choix de la stack

Étant donné l'aspect découverte du projet, je suis parti sur quelque chose de totalement nouveau pour moi.
Tout d'abord, même si cela n'est pas nécessaire, j'ai lancé un projet [AdonisJS 6](https://adonisjs.com/) pour le backend. Bien entendu, je ne me suis pas juste contenté de lancer un projet avec les outils par défaut. J'ai également remplacé le moteur de rendu [Edge](https://edgejs.dev/) qui est par défaut dans Adonis, par [`@kitajs/html`](https://github.com/kitajs/html), un moteur me permettant de transformer des composants JSX en page HTML. La mise en place fut légèrement compliquée, mais une fois configuré, la prise en main était très simple et agréable à utiliser.

C'est maintenant ici que la partie la plus intéressante arrive : le code frontend qui s'occupera de gérer Mediapipe. Pour cela, je suis resté sur un visuel succinct avec [Tailwind](https://tailwindcss.com). Par contre, pour afficher mon interface avec Mediapipe, je me suis pensé sur les [Web Components](https://developer.mozilla.org/fr/docs/Web/Web_Components) avec l'utilisation de [Preact](https://preactjs.com/) pour la partie JavaScript.

Forcément, je suis conscient que cette stack n'a strictement aucun sens pour un projet de production. Mais pour un projet personnel, cela m'a permis de découvrir de nouvelles technologies et de m'amuser à les utiliser. 🙃

## La mise en place

### Kitajs

Comme dit précédemment, j'ai utilisé [`@kitajs/html`](https://github.com/kitajs/html) pour gérer mes pages HTML. Pour cela, j'ai suivi les explication fournit dans [ce post de blog](https://adonisjs.com/blog/use-tsx-for-your-template-engine).

Tout d'abord, j'ai installé le package :

```bash
pnpm install @kitajs/html -D
pnpm install @kitajs/ts-html-plugin -D
```

Ensuite, j'ai configuré mon fichier `tsconfig.json` pour ajouter le plugin typescript :

```json
// tsconfig.json
{
  // ...
  "compilerOptions": {
    // ...
    // on configure le moteur JSX pour typescript
    "jsx": "react",
    "jsxFactory": "Html.createElement",
    "jsxFragmentFactory": "Html.Fragment",
    "plugins": [{ "name": "@kitajs/ts-html-plugin" }]
  },
  "exclude": ["resources"]
}
```

Ce plugin va permet de faire comprendre à Typescript comme interpréter les éléments JSX.

Et pour finir, dans un contrôleur `app/app_controller.ts` et ma vue, j'ai pu utiliser le moteur de rendu :

```ts
// app/app_controller.ts
import { Home } from '../../resources/views/pages/home.js'

export default class AppsController {
  index() {
    return <Home />
  }
}
```

```tsx
// resources/views/pages/home.tsx

import { App } from "../layouts/app.js";

export function Home() {
  return (
    <App>
      <div class="relative">
        <div class="container mx-auto p-8 z-10 absolute top-0 left-0 w-full">
          <h1 class="font-bold text-2xl">Move your hands</h1>
        </div>

        <div class="w-screen h-screen">
          <video-container />
        </div>
      </div>
    </App>
  );
}
```

### Web Components

Vous avez dû remarqué dans la partie précédente que j'ai utilisé une balise `video-container`. Cette balise est un Web Component que j'ai créé pour gérer l'affichage de la vidéo et l'initialisation de Mediapipe.

La configuration d'un Web Component est assez simple, en particulier avec Preact. Il suffit de créer un composant Preact et de l'enregistrer en tant que Web Component. Voici un exemple de composant :

```tsx
// resources/components/my-component.tsx
import { JSX } from "preact";
import register from "preact-custom-element";

function MyComponent(): JSX.Element {
  return <div>Hello World</div>;
}

register(MyComponent, "video-container");
```

Bien entendu, pour permettre à typescript et kita de comprendre que notre composant existe et est valide, il nous faut ajouter des informations dans un fichier de déclaration :

```ts
// types/kita.ts
declare global {
  namespace JSX {
    interface IntrinsicElements {
      ["video-container"]: HtmlTag;
    }
  }
}
```

Et c'est tout ! Notre composant est maintenant utilisable dans notre page HTML. (Attention, il faut bien sûr penser à importer le script de notre composant dans notre page HTML et le déclarer dans la configuration Vite).

### Mediapipe

Je ne pense pas m'étendre sur utilisation de MediaPipe dans mon code, mais j'aimerais quand même aborder certains points. Par exemple, la manière dont j'ai géré l'initialisation de Mediapipe.

Tout d'abord, j'ai créé un fichier utilitaire pour gérer l'initialisation de Mediapipe :

```ts
// resources/ts/utils/vision.ts
import { FilesetResolver, GestureRecognizer } from "@mediapipe/tasks-vision";

export async function gestureRecogniser() {
  // On récupère le module de vision
  const vision = await FilesetResolver.forVisionTasks(
    "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@latest/wasm",
  );

  // On crée le GestureRecognizer
  return await GestureRecognizer.createFromOptions(vision, {
    baseOptions: {
      modelAssetPath:
        "https://storage.googleapis.com/mediapipe-tasks/gesture_recognizer/gesture_recognizer.task",
      delegate: "GPU",
    },
    runningMode: "VIDEO",
    numHands: 2,
  });
}
```

Ensuite, j'ai utilisé ce fichier dans mon composant Web Component pour initialiser Mediapipe :

```tsx
function VideoContainer(): JSX.Element {
  const [visionRecogniser, setVision] = useState<GestureRecognizer>();
  const [visionData, setVisionData] = useState<GestureRecognizerResult | null>(
    null,
  );

  useEffect(() => {
    // On charge le vision recogniser
    const fetchVision = async () => {
      console.log("Loading vision recogniser");
      const { gestureRecogniser } = await import("../utils/vision");
      setVision(await gestureRecogniser());
    };

    fetchVision();
  }, []);

  let text: string | null = null;

  // On extrait les informations de Mediapipe pour les afficher
  if (visionData && visionData.gestures.length > 0) {
    const categoryName = visionData.gestures[0][0].categoryName;
    const categoryScore = (visionData.gestures[0][0].score * 100).toFixed(2);
    const handedness = visionData.handedness[0][0].displayName;

    text = `${categoryName} (${categoryScore}%) - ${handedness}`;
  }

  if (!visionRecogniser) {
    return <p>Loading...</p>;
  }

  // On retourne le composant
  return (
    <div class="relative mx-auto p-4">
      <WebcamProvider>
        <div class="relative w-fit mx-auto">
          <Video vision={visionRecogniser} onVisionData={setVisionData} />
          <Canvas data={visionData} />
        </div>
      </WebcamProvider>

      {visionData && <p class="absolute z-10 bottom-4">{text}</p>}
    </div>
  );
}
```

Et voilà, notre composant est maintenant capable de gérer Mediapipe.
Le composant `<Video/>` s'occupe de récupérer le flux vidéo de la webcam et de le traiter avec Mediapipe. Le composant `<Canvas/>` s'occupe de dessiner les informations récupérées par Mediapipe sur le flux vidéo. Et le composant `<WebcamProvider/>` s'occupe de gérer l'accès à la webcam.

À partir de là, il ne reste plus qu'à ajouter les styles et les scripts nécessaires pour que notre composant fonctionne correctement.

## Conclusion

Ce projet fut très intéressant sur de nombreux point.

Tout d'abord, je me suis rendu compte que l'utilisation de JSX avec Kita comme moteur de rendu était vraiment séduisant en comparaison à ce qui existe déjà avec EdgeJS. La capacité à proposer des composants facilement réutilisables et _typescript-friendly_ est vraiment un plus !

Ensuite, j'ai pu découvrir les Web Components et leur utilisation avec Preact. Même si je n'ai pas pu exploiter pleinement leur potentiel, j'ai pu voir à quel point ils étaient simples à mettre en place et à utiliser. Cela m'a permit de me rendre compte que l'utilisation de gros framework en Single-Page-App (tel que React, Vue ou Angular) n'était pas une nécessité pour avoir de l'interactivité sur un site web. Les Web Components peuvent très bien faire le travail s'il est bien utilisé.

Et pour finir, j'ai vraiment été surpris par la simplicité d'utilisation de la librairie MediaPipe, et la facilité pour mettre cette dernière en place dans un projet. Même si je n'ai pas pu exploiter pleinement les capacités de cette librairie, j'ai pu voir son potentiel.

Je vous invite à aller voir le code de ce projet sur mon [Github](https://github.com/Bricklou/media-pipe-demo/) pour voir comment j'ai mis en place tout cela.
