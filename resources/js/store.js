import {getLocalUser} from './service/AuthService';
import Axios from 'axios';
const user = getLocalUser();

export default{
    state:{
        currentUser: user,
        isLoggedIn: !!user,
        authError: null,
        categories: []
    },
    getters:{
        currentUser(state){
            return state.currentUser;
        },
        authError(state){
            return state.authError;
        },
        getCategories(state){
            return state.categories;
        }
            
    },
    actions:{
        login(context){
            context.commit("login");
        },
        getCategories(context){
            Axios.get('/api/v2/allCategories')
            .then(response=>{
                //console.log(response);
                context.commit('getCategories' ,response.data.data);
            }).catch(error=>{
                console.log(error);
            });
        }
    },
    mutations:{
        login(state) {
            state.authError = null;
        },
        loginSuccess(state, payload) {
            state.authError = null;
            state.isLoggedIn = true;
            state.currentUser = Object.assign({}, payload.user, { token: payload.access_token , expires_in: payload.expires_in });
            localStorage.setItem("user", JSON.stringify(state.currentUser));
        },
        loginFail(state , payload){
            state.authError = payload.error;
        },
        registerFail(state , payload){
            state.authError = payload.error;
        },
        logout(state){
            localStorage.removeItem('user');
            state.isLoggedIn = false;
            state.currentUser = null;
        },
        getCategories(state , payload)
        {
            return state.categories = payload;
        }
    }
}