import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Scroll-triggered reveal animations
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.section-reveal');

    if (revealElements.length === 0) return;

    // Initially hide elements that should be revealed on scroll
    revealElements.forEach(el => {
        el.style.animationPlayState = 'paused';
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => observer.observe(el));
});
