require('./bootstrap');

window.Vue = require('vue');
import App from './App.vue';
import Vuex from 'vuex';
import router from './routes';
import 'admin-lte/dist/css/adminlte.css';
import {initialize} from './service/General';

//vuex
Vue.use(Vuex);
import storeData from './store';
const store = new Vuex.Store(storeData);

//v-form
import { Form, HasError, AlertError } from 'vform';
window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('select-component', require('./components/admin/views/SelectComponent.vue'));

initialize(router , store);
const app = new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App)
});
