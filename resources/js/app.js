import Alpine from 'alpinejs'
import popup from './modules/popup.js';

window.Alpine = Alpine
Alpine.data('popup', popup);
Alpine.start();

import './bootstrap';
import './modules/maps.js';
import './forms/contact/app.js';
import './forms/course/app.js';