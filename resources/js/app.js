import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

window.refreshIcons = () => {
    try {
        createIcons({ icons });
    } catch (e) {
        console.error('Failed to render Lucide icons', e);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.refreshIcons();
});

document.addEventListener('alpine:initialized', () => {
    window.refreshIcons();
});

Alpine.start();
