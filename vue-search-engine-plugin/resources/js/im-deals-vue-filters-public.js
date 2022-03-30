import Vue from 'vue';
import App from './App.vue';
import VueResource from 'vue-resource';
import ScrollLoader from 'vue-scroll-loader';

Vue.use(ScrollLoader);
Vue.use(VueResource);
Vue.http.options.root = window.location.origin;
if(document.getElementById('vue-instant-search-app')){
	new Vue({
		el: '#vue-instant-search-app',
		render: h => h(App),
		data: {}
	});
}
