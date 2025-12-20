// assets/app.js
import { createApp } from 'vue';
import App from './components/App.vue';

// On vérifie si l'élément #app existe avant de monter l'application
const appElement = document.querySelector('#app');

if (appElement) {
    createApp(App).mount('#app');
}