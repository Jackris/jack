import { createApp } from 'vue'

createApp({
    data() {
        return {
            message: 'Привет Vueвув!'
        };
    }
}).mount('#app');

const {createApp1} = Vue;

createApp1({
    data() {
        return {
            titleClass: 'title'
        };
    }
}).mount('#app1')


