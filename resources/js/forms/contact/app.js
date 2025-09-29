import { createApp } from 'vue';
import Contact from './Contact.vue';
const app = createApp({});
app.component('contact-form', Contact);
if (document.getElementById('contact-form')) {
  app.mount('#contact-form');
}
