import { createApp } from 'vue';
import EventRegistration from './EventRegistration.vue';
const app = createApp({});
app.component('event-registration', EventRegistration);
if (document.getElementById('event-form')) {
  app.mount('#event-form');
}
