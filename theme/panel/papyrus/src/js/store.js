import Vue from 'vue';
import Vuex from 'vuex';
import $http from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        LANG: PINOOX.LANG,
        user: {},
        configs: {},
        isLoading: false,
        isTransition: true,
        countTranslate:0,
    },
    getters: {},
    mutations: {
        updateDirections: (state, direction) => {
            document.body.className = direction;
        },
    },
    actions: {

    }
});