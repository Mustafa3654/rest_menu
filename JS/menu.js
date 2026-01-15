document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.menu-card');
    cards.forEach((card, index) => {
        // Reset opacity to 0 initially so the animation handles the fade-in
        card.style.opacity = '0';
        // Trigger animation
        card.style.animation = `itemFadeIn 0.5s ease forwards ${index * 0.1}s`;
    });
});
