import './assets/main.css'
import axios from 'axios';
import { createStore } from 'vuex';
import router from './router/index.js';

import { createApp } from 'vue'
import App from './App.vue'

axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.withCredentials = true;

const store = createStore({
    state: {
        user: null
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user;
        }
    },
    actions: {
        login({ commit }, user) {
            commit('SET_USER', user);
        }
    }
});

const app = createApp(App);
app.use(store);
app.use(router);
app.mount('#app');
