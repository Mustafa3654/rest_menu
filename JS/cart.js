class Cart {
    constructor() {
        try {
            const stored = localStorage.getItem('restaurant_cart');
            this.items = stored ? JSON.parse(stored) : [];
            if (!Array.isArray(this.items)) this.items = [];
        } catch(e) {
            this.items = [];
        }
    }

    init() {
        if (!this.isCartEnabled()) {
            this.teardownCartUI();
            return;
        }
        this.renderCartButton();
        this.updateButtons();
        this.updateCheckoutButtonUI();
    }

    isCartEnabled() {
        // menu.php sets window.cartEnabled; default to enabled if missing.
        if (typeof window.cartEnabled === 'undefined') return true;
        return window.cartEnabled === true || window.cartEnabled === 1 || window.cartEnabled === '1';
    }

    teardownCartUI() {
        // Remove any injected controls and hide modal/button if present.
        document.querySelectorAll('.cart-controls').forEach(el => (el.innerHTML = ''));

        const floatingBtn = document.getElementById('floating-cart-btn');
        if (floatingBtn) floatingBtn.remove();

        const modal = document.getElementById('cart-modal');
        if (modal) modal.remove();
    }

    updateCheckoutButtonUI() {
        const btnText = document.getElementById('checkout-btn-text');
        const btnIcon = document.getElementById('checkout-btn-icon');
        const method = window.orderMethod || 'whatsapp';

        if (btnText && btnIcon) {
            if (method === 'sms') {
                btnText.textContent = 'Send Order via SMS';
                btnIcon.className = 'fas fa-sms';
            } else {
                btnText.textContent = 'Order on WhatsApp';
                btnIcon.className = 'fab fa-whatsapp';
            }
        }
    }

    addItem(item) {
        const existingItem = this.items.find(i => i.id === item.id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({ ...item, quantity: 1 });
        }
        this.save();
        this.updateButtons();
        this.renderCartButton();
        this.showToast('Item added to cart');
    }

    removeItem(itemId) {
        const index = this.items.findIndex(i => i.id === itemId);
        if (index > -1) {
            if (this.items[index].quantity > 1) {
                this.items[index].quantity -= 1;
            } else {
                this.items.splice(index, 1);
            }
            this.save();
            this.updateButtons();
            this.renderCartButton();
            // If cart is open, re-render it
            if(document.getElementById('cart-modal').style.display === 'flex') {
                this.renderCartModal();
            }
        }
    }

    save() {
        localStorage.setItem('restaurant_cart', JSON.stringify(this.items));
    }

    // Helper to calculate totals
    calculateTotals() {
        let usd = 0;
        this.items.forEach(item => {
            if (item.priceUsd) {
                usd += item.priceUsd * item.quantity;
            }
        });
        return { usd };
    }

    updateButtons() {
        // Update Add/Qty buttons on the menu
        const cards = document.querySelectorAll('.menu-card');
        cards.forEach(card => {
            const id = card.getAttribute('data-id');
            const item = this.items.find(i => i.id === id);
            const btnContainer = card.querySelector('.cart-controls');
            
            if (btnContainer) {
                if (item) {
                    btnContainer.innerHTML = `
                        <button onclick="cart.removeItem('${id}')" class="qty-btn minus">-</button>
                        <span class="qty-display">${item.quantity}</span>
                        <button onclick="cart.addItemFromCard('${id}')" class="qty-btn plus">+</button>
                    `;
                    card.classList.add('in-cart');
                } else {
                    btnContainer.innerHTML = `
                        <button onclick="cart.addItemFromCard('${id}')" class="add-btn">
                            <i class="fas fa-shopping-cart"></i> Add
                        </button>
                    `;
                    card.classList.remove('in-cart');
                }
            }
        });
    }
    
    // Helper for onclick events which pass IDs as strings
    addItemFromCard(id) {
        const card = document.querySelector(`.menu-card[data-id="${id}"]`);
        if (!card) return;
        
        const name = card.getAttribute('data-name');
        const category = card.getAttribute('data-category');
        const priceUsd = parseFloat(card.getAttribute('data-price-usd')) || 0;
        const priceSuffix = card.getAttribute('data-price-suffix') || '';
        
        this.addItem({
            id,
            name,
            category,
            priceUsd,
            priceSuffix
        });
    }

    renderCartButton() {
        let btn = document.getElementById('floating-cart-btn');
        const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
        
        if (totalItems === 0) {
            if (btn) btn.style.display = 'none';
            return;
        }

        if (!btn) {
            btn = document.createElement('div');
            btn.id = 'floating-cart-btn';
            btn.onclick = () => this.openCart();
            document.body.appendChild(btn);
        }

        btn.style.display = 'flex';
        btn.innerHTML = `
            <div class="cart-icon-wrapper">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge">${totalItems}</span>
            </div>
            <span class="view-cart-text">View Cart</span>
        `;
    }

    openCart() {
        const modal = document.getElementById('cart-modal');
        this.renderCartModal();
        modal.style.display = 'flex';
    }

    closeCart() {
        const modal = document.getElementById('cart-modal');
        modal.style.display = 'none';
    }

    renderCartModal() {
        const container = document.getElementById('cart-items-container');
        const totalsContainer = document.getElementById('cart-totals');
        
        if (this.items.length === 0) {
            container.innerHTML = '<div class="empty-cart-msg">Your cart is empty</div>';
            totalsContainer.innerHTML = '';
            return;
        }

        let html = '';
        this.items.forEach(item => {
            let priceDisplay = '';
            if (item.priceUsd > 0) {
                priceDisplay = `$${(item.priceUsd * item.quantity).toFixed(2)}`;
                if (item.priceSuffix) priceDisplay += ` ${item.priceSuffix}`;
            }
            
            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${priceDisplay}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button onclick="cart.removeItem('${item.id}')" class="qty-btn minus small">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="cart.addItemFromCard('${item.id}')" class="qty-btn plus small">+</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;

        const totals = this.calculateTotals();
        let totalHtml = '';
        if (totals.usd > 0) totalHtml += `<div>Total USD: <strong>$${totals.usd.toFixed(2)}</strong></div>`;
        totalsContainer.innerHTML = totalHtml;
    }

    checkout() {
        if (this.items.length === 0) return;

        // Check for Combo Items Warning
        const hasCombo = this.items.some(item => 
            item.category && item.category.toLowerCase().includes('combo')
        );

        if (hasCombo) {
            const confirmed = confirm("⚠️ Warning: The items you chose from the Combo category will not be ready for an hour. Do you still want to proceed with your order?");
            if (!confirmed) return;
        }

        let message = "Hello, I would like to order:\n\n";
        this.items.forEach(item => {
            if (item.priceUsd > 0) {
                const unitPrice = '$' + item.priceUsd.toFixed(2) + (item.priceSuffix ? ' ' + item.priceSuffix : '');
                const lineTotal = '$' + (item.priceUsd * item.quantity).toFixed(2);
                message += `* ${item.quantity} x ${item.name}   (${unitPrice}) = ${lineTotal}\n`;
            }
        });

        const totals = this.calculateTotals();
        message += "\n";
        if (totals.usd > 0) message += `Total: $${totals.usd.toFixed(2)}\n`;

        // Add Customer Details
        const customerName = document.getElementById('customer-name').value.trim();
        const customerPhone = document.getElementById('customer-phone').value.trim();

        if (!customerName || !customerPhone) {
            this.showToast('Please enter your name and phone number');
            return;
        }

        message += `\nName: ${customerName}`;
        message += `\nPhone: ${customerPhone}`;

        // Get phone number from a global variable set in php, strip non-digits
        const phone = (window.restaurantPhone || '').replace(/\D/g, ''); 
        const method = window.orderMethod || 'whatsapp';
        
        if (method === 'sms') {
            const url = `sms:+${phone}?body=${encodeURIComponent(message)}`;
            window.location.href = url;
        } else {
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }
    }

    showToast(msg) {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
}

// Initialize when safely possible
let cart = new Cart();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => cart.init());
} else {
    cart.init();
}

