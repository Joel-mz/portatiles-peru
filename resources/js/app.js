import './bootstrap';
import Alpine from 'alpinejs';

// Global Cart & Theme Store with Alpine
document.addEventListener('alpine:init', () => {
    // Theme Manager
    Alpine.store('theme', {
        dark: localStorage.getItem('nexora_theme') === 'dark',
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('nexora_theme', this.dark ? 'dark' : 'light');
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        init() {
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });

    // Cart Store
    Alpine.store('cart', {
        items: JSON.parse(localStorage.getItem('nexora_cart') || '[]'),
        isOpen: false,

        save() {
            localStorage.setItem('nexora_cart', JSON.stringify(this.items));
        },

        addItem(product, quantity = 1) {
            const existing = this.items.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += quantity;
            } else {
                this.items.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    price: parseFloat(product.price),
                    image: product.image,
                    slug: product.slug,
                    quantity: quantity,
                    stock: product.stock
                });
            }
            this.save();
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: `"${product.name}" agregado al carrito`, type: 'success' }
            }));
        },

        removeItem(productId) {
            this.items = this.items.filter(item => item.id !== productId);
            this.save();
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: 'Producto eliminado del carrito', type: 'info' }
            }));
        },

        updateQuantity(productId, quantity) {
            const item = this.items.find(i => i.id === productId);
            if (item) {
                item.quantity = Math.max(1, quantity);
                if (item.stock && item.quantity > item.stock) {
                    item.quantity = item.stock;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Stock máximo disponible alcanzado (${item.stock} uds)`, type: 'warning' }
                    }));
                }
                this.save();
            }
        },

        clear() {
            this.items = [];
            this.save();
        },

        get count() {
            return this.items.reduce((total, item) => total + item.quantity, 0);
        },

        get subtotal() {
            return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        get total() {
            return this.subtotal;
        }
    });
});

window.Alpine = Alpine;
Alpine.start();
