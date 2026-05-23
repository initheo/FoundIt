import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.lucide = { 
    createIcons: (options = {}) => createIcons({ icons, ...options }),
    icons 
};

// Initialize Alpine
Alpine.start();

// Initialize icons on page load
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
