document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.menu-card');
    cards.forEach((card, index) => {
        // Reset opacity to 0 initially so the animation handles the fade-in
        card.style.opacity = '0';
        // Trigger animation
        card.style.animation = `itemFadeIn 0.5s ease forwards ${index * 0.1}s`;
    });
});

// Quick View Modal Logic
function openQuickView(element) {
    const card = element.closest('.menu-card');
    if (!card) return;

    const title = card.getAttribute('data-name');
    const priceLbp = card.querySelector('.price-lbp').textContent;
    const priceUsd = card.querySelector('.price-usd').textContent;
    const imgEl = card.querySelector('.menu-card-img');
    const descEl = card.querySelector('.menu-card-desc');

    document.getElementById('qv-title').textContent = title;
    document.getElementById('qv-price-lbp').textContent = priceLbp;
    document.getElementById('qv-price-usd').textContent = priceUsd;
    document.getElementById('qv-price-lbp').style.display = card.querySelector('.price-lbp').style.display;
    document.getElementById('qv-price-usd').style.display = card.querySelector('.price-usd').style.display;

    const qvImgContainer = document.getElementById('qv-img-container');
    const qvImg = document.getElementById('qv-img');
    
    if (imgEl) {
        qvImg.src = imgEl.src;
        qvImgContainer.style.display = 'block';
    } else {
        qvImgContainer.style.display = 'none';
        qvImg.src = '';
    }

    const qvDesc = document.getElementById('qv-ingredients');
    if (descEl) {
        qvDesc.textContent = descEl.textContent;
        // Format ingredients to break lines correctly
        if(descEl.textContent.includes(',')) {
            qvDesc.innerHTML = descEl.textContent.split(',').map(i => i.trim()).join('<br>');
        }
    } else {
        qvDesc.textContent = '';
    }

    const modal = document.getElementById('quickview-modal');
    modal.style.display = 'flex';
    // Small delay to allow display flex to apply before opacity transition
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeQuickView() {
    const modal = document.getElementById('quickview-modal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
}

// Close modal if clicked outside of content
window.addEventListener('click', function(event) {
    const modal = document.getElementById('quickview-modal');
    if (event.target === modal) {
        closeQuickView();
    }
});
