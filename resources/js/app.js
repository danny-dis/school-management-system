import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Alpine from 'alpinejs';

// Import components
import App from './App.vue';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Create Vue app if the element exists
if (document.getElementById('app')) {
    const app = createApp(App);
    
    // Use Pinia for state management
    app.use(createPinia());
    
    // Mount the app
    app.mount('#app');
}

// Global event bus
window.EventBus = {
    events: {},
    
    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    },
    
    emit(event, data) {
        if (this.events[event]) {
            this.events[event].forEach(callback => callback(data));
        }
    },
    
    off(event, callback) {
        if (this.events[event]) {
            this.events[event] = this.events[event].filter(cb => cb !== callback);
        }
    }
};
