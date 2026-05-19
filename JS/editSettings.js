function openTab(tabId, btn) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    // Remove active class
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    
    // Show selected tab
    document.getElementById(tabId).style.display = 'block';
    if(btn) btn.classList.add('active');

    // Hide the main save button if gallery tab is open (since it has its own forms)
    const saveContainer = document.getElementById('save-settings-container');
    if (saveContainer) {
        if (tabId === 'tab-gallery') {
            saveContainer.style.display = 'none';
        } else {
            saveContainer.style.display = 'flex';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Check for the body data attribute to auto-open gallery
    if (document.body.getAttribute('data-open-gallery') === 'true') {
        const galleryBtn = document.getElementById('gallery-tab-btn');
        if (galleryBtn) {
            openTab('tab-gallery', galleryBtn);
        }
    }
});
