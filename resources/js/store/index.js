import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    role: localStorage.getItem('role') || null,
    loading: false,
    error: null
  },
  getters: {
    isLoggedIn: state => !!state.token,
    currentUser: state => state.user,
    userRole: state => state.role,
    isLoading: state => state.loading,
    hasError: state => !!state.error,
    error: state => state.error
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user;
    },
    SET_TOKEN(state, token) {
      state.token = token;
    },
    SET_ROLE(state, role) {
      state.role = role;
    },
    SET_LOADING(state, loading) {
      state.loading = loading;
    },
    SET_ERROR(state, error) {
      state.error = error;
    },
    CLEAR_USER(state) {
      state.user = null;
      state.token = null;
      state.role = null;
    }
  },
  actions: {
    login({ commit }, credentials) {
      commit('SET_LOADING', true);
      commit('SET_ERROR', null);
      
      return axios.post('/api/login', credentials)
        .then(response => {
          const { user, token } = response.data.data;
          
          localStorage.setItem('token', token);
          localStorage.setItem('user', JSON.stringify(user));
          localStorage.setItem('role', user.role);
          
          commit('SET_USER', user);
          commit('SET_TOKEN', token);
          commit('SET_ROLE', user.role);
          
          return response;
        })
        .catch(error => {
          commit('SET_ERROR', error.response ? error.response.data.message : 'Login failed');
          throw error;
        })
        .finally(() => {
          commit('SET_LOADING', false);
        });
    },
    logout({ commit }) {
      commit('SET_LOADING', true);
      
      return axios.post('/api/logout')
        .then(() => {
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          localStorage.removeItem('role');
          
          commit('CLEAR_USER');
          
          return true;
        })
        .catch(error => {
          commit('SET_ERROR', error.response ? error.response.data.message : 'Logout failed');
          throw error;
        })
        .finally(() => {
          commit('SET_LOADING', false);
        });
    },
    fetchUser({ commit }) {
      commit('SET_LOADING', true);
      
      return axios.get('/api/user')
        .then(response => {
          const user = response.data.data;
          
          localStorage.setItem('user', JSON.stringify(user));
          localStorage.setItem('role', user.role);
          
          commit('SET_USER', user);
          commit('SET_ROLE', user.role);
          
          return user;
        })
        .catch(error => {
          commit('SET_ERROR', error.response ? error.response.data.message : 'Failed to fetch user');
          throw error;
        })
        .finally(() => {
          commit('SET_LOADING', false);
        });
    },
    changePassword({ commit }, data) {
      commit('SET_LOADING', true);
      commit('SET_ERROR', null);
      
      return axios.post('/api/change-password', data)
        .then(response => {
          return response.data;
        })
        .catch(error => {
          commit('SET_ERROR', error.response ? error.response.data.message : 'Failed to change password');
          throw error;
        })
        .finally(() => {
          commit('SET_LOADING', false);
        });
    }
  }
});
