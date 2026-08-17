# Panier React

Portage en React du composant panier de l'application principale (voir `../assets/components/App.vue`), pour valider que les compétences acquises en Vue.js (state, listes réactives, persistance) se transposent à React.

Même comportement que l'original : ajout/suppression de produits, calcul du total, persistance via `localStorage`, validation de commande.

## Stack

- React 19 + Vite
- Hooks : `useState` (dont l'initialisation paresseuse pour le chargement du panier), `useEffect` (synchronisation avec `localStorage`)

## Lancer le projet

```bash
npm install
npm run dev
```
