const scrollContainer = document.getElementById('categoryScroll');

    // Restore scroll on load
    window.addEventListener('DOMContentLoaded', () => {
        const savedScroll = localStorage.getItem('catScrollLeft');
        if (scrollContainer && savedScroll) {
            scrollContainer.scrollLeft = parseInt(savedScroll);
        }
    });
    
    // Save scroll before navigation
    document.querySelectorAll('.category-link').forEach(link => {
        link.addEventListener('click', () => {
            if (scrollContainer) {
                localStorage.setItem('catScrollLeft', scrollContainer.scrollLeft);
            }
        });
    });