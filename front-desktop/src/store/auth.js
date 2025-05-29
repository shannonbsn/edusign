import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
    state: {
        token: localStorage.getItem('authToken') || null,
        user: null
    },
    mutations: {
        SET_TOKEN(state, token) {
            state.token = token;
            localStorage.setItem('authToken', token);
        },
        SET_USER(state, user) {
            state.user = user;
        },
        LOGOUT(state) {
            state.token = null;
            state.user = null;
            localStorage.removeItem('authToken');
        }
    },
    actions: {
        async login({ commit }, credentials) {
            try {
                const response = await axios.post('http://localhost:8000/api/login', credentials);
                commit('SET_TOKEN', response.data.token);
            } catch (error) {
                throw new Error('Échec de la connexion');
            }
        },

        async fetchUser({ commit, state }) {
            if (state.token) {
                try {
                    const response = await axios.get('http://localhost:8000/api/user', {
                        headers: { Authorization: `Bearer ${state.token}` }
                    });
                    commit('SET_USER', response.data);
                } catch (error) {
                    commit('LOGOUT');
                }
            }
        },

        async logout({ commit }) {
            await axios.post('http://localhost:8000/api/logout');
            commit('LOGOUT');
        }
    },
    getters: {
        isAuthenticated: state => !!state.token,
        user: state => state.user
    }
});