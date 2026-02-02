class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('restaurant_cart')) || [];
        this.init();
    }

    init() {
        this.renderCartButton();
        this.updateButtons();
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

    getTotal() {
        return this.items.reduce((total, item) => {
            // Priority to LBP, then USD
            // Note: This logic depends on how you want to handle mixed currencies.
            // For now assuming items have one active price or we convert everything to one view.
            // To keep it simple, we'll sum up LBP and USD separately.
            return total; 
        }, { lbp: 0, usd: 0 });
    }
    
    // Helper to calculate totals properly
    calculateTotals() {
        let lbp = 0;
        let usd = 0;
        this.items.forEach(item => {
            if (item.priceLbp) {
                lbp += item.priceLbp * item.quantity;
            } else if (item.priceUsd) {
                usd += item.priceUsd * item.quantity;
            }
        });
        return { lbp, usd };
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
        const priceLbp = parseFloat(card.getAttribute('data-price-lbp')) || 0;
        const priceUsd = parseFloat(card.getAttribute('data-price-usd')) || 0;
        
        this.addItem({
            id,
            name,
            priceLbp,
            priceUsd
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
            if (item.priceLbp > 0) priceDisplay = `${(item.priceLbp * item.quantity).toLocaleString()} LBP`;
            if (item.priceUsd > 0) priceDisplay = `$${(item.priceUsd * item.quantity).toFixed(2)}`;
            
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
        if (totals.lbp > 0) totalHtml += `<div>Total LBP: <strong>${totals.lbp.toLocaleString()} LBP</strong></div>`;
        if (totals.usd > 0) totalHtml += `<div>Total USD: <strong>$${totals.usd.toFixed(2)}</strong></div>`;
        totalsContainer.innerHTML = totalHtml;
    }

    checkout() {
        if (this.items.length === 0) return;

        let message = "New Order:\n\n";
        this.items.forEach(item => {
            message += `- ${item.quantity}x ${item.name}`;
            if (item.priceLbp > 0) message += ` (${(item.priceLbp * item.quantity).toLocaleString()} LBP)`;
            if (item.priceUsd > 0) message += ` ($${(item.priceUsd * item.quantity).toFixed(2)})`;
            message += "\n";
        });

        const totals = this.calculateTotals();
        message += "\nTotal:\n";
        if (totals.lbp > 0) message += `${totals.lbp.toLocaleString()} LBP\n`;
        if (totals.usd > 0) message += `$${totals.usd.toFixed(2)}\n`;

        // Get phone number from a global variable set in php
        const phone = window.restaurantPhone || ''; 
        const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
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

// Initialize
const cart = new Cart();
