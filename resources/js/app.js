import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import Swal from 'sweetalert2';
import { Chart as ChartJS } from 'chart.js/auto';

// Initialize Alpine.js
window.Alpine = Alpine;

// Initialize global variables
window.Swal = Swal;
window.Chart = Chart;

// Toast Notification System
class Toast {
    constructor() {
        this.container = null;
        this.initializeContainer();
    }

    initializeContainer() {
        this.container = document.createElement('div');
        this.container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(this.container);
    }

    show(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast ${type} show`;
        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${this.getIcon(type)}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto flex-shrink-0 text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        this.container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    getIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle text-green-500 text-lg"></i>',
            error: '<i class="fas fa-exclamation-circle text-red-500 text-lg"></i>',
            warning: '<i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>',
            info: '<i class="fas fa-info-circle text-blue-500 text-lg"></i>'
        };
        return icons[type] || icons.info;
    }
}

window.Toast = new Toast();

// Cart Management System
class CartManager {
    constructor() {
        this.cartKey = 'passsna_cart';
        this.cart = this.getCart();
    }

    getCart() {
        const cart = localStorage.getItem(this.cartKey);
        return cart ? JSON.parse(cart) : [];
    }

    saveCart() {
        localStorage.setItem(this.cartKey, JSON.stringify(this.cart));
        this.updateCartCount();
    }

    updateCartCount() {
        const count = this.cart.reduce((total, item) => total + item.quantity, 0);
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        }
    }

    addItem(item) {
        const existingItem = this.cart.find(i =>
            i.id === item.id &&
            i.size === item.size &&
            JSON.stringify(i.toppings) === JSON.stringify(item.toppings)
        );

        if (existingItem) {
            existingItem.quantity += item.quantity;
        } else {
            this.cart.push({
                ...item,
                cartId: Date.now() + Math.random().toString(36).substr(2, 9)
            });
        }

        this.saveCart();
        Toast.show('Item added to cart!', 'success');
    }

    removeItem(cartId) {
        this.cart = this.cart.filter(item => item.cartId !== cartId);
        this.saveCart();
        Toast.show('Item removed from cart!', 'success');
    }

    updateQuantity(cartId, quantity) {
        const item = this.cart.find(item => item.cartId === cartId);
        if (item) {
            item.quantity = Math.max(1, quantity);
            this.saveCart();
        }
    }

    clearCart() {
        this.cart = [];
        this.saveCart();
        Toast.show('Cart cleared!', 'info');
    }

    getTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getItemCount() {
        return this.cart.length;
    }
}

window.CartManager = new CartManager();

// Reservation System
class ReservationSystem {
    constructor() {
        this.initializeDatePicker();
        this.initializeTimeSlots();
    }

    initializeDatePicker() {
        const dateInput = document.getElementById('reservation-date');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
            dateInput.value = today;
        }
    }

    initializeTimeSlots() {
        const timeSelect = document.getElementById('reservation-time');
        if (timeSelect) {
            const slots = this.generateTimeSlots('18:00', '22:00', 30);
            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = slot;
                timeSelect.appendChild(option);
            });
        }
    }

    generateTimeSlots(start, end, interval) {
        const slots = [];
        let [startHour, startMinute] = start.split(':').map(Number);
        let [endHour, endMinute] = end.split(':').map(Number);

        let currentHour = startHour;
        let currentMinute = startMinute;

        while (currentHour < endHour || (currentHour === endHour && currentMinute < endMinute)) {
            const time = `${currentHour.toString().padStart(2, '0')}:${currentMinute.toString().padStart(2, '0')}`;
            slots.push(time);

            currentMinute += interval;
            if (currentMinute >= 60) {
                currentHour++;
                currentMinute -= 60;
            }
        }

        return slots;
    }

    checkAvailability(date, time, guests) {
        // This would typically make an API call
        return new Promise((resolve) => {
            setTimeout(() => {
                const available = Math.random() > 0.3; // Mock availability
                resolve(available);
            }, 500);
        });
    }
}

window.ReservationSystem = new ReservationSystem();

// Menu Filter System
class MenuFilter {
    constructor() {
        this.activeCategory = 'all';
        this.searchQuery = '';
        this.initializeFilters();
    }

    initializeFilters() {
        // Category filter
        document.querySelectorAll('.category-filter').forEach(button => {
            button.addEventListener('click', (e) => {
                const category = e.target.dataset.category;
                this.filterByCategory(category);
            });
        });

        // Search filter
        const searchInput = document.getElementById('menu-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.applyFilters();
            });
        }
    }

    filterByCategory(category) {
        this.activeCategory = category;
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });
        this.applyFilters();
    }

    applyFilters() {
        const items = document.querySelectorAll('.menu-item');
        items.forEach(item => {
            const category = item.dataset.category;
            const name = item.dataset.name.toLowerCase();
            const description = item.dataset.description.toLowerCase();

            const categoryMatch = this.activeCategory === 'all' || category === this.activeCategory;
            const searchMatch = this.searchQuery === '' ||
                name.includes(this.searchQuery) ||
                description.includes(this.searchQuery);

            item.classList.toggle('hidden', !(categoryMatch && searchMatch));
        });
    }
}

window.MenuFilter = new MenuFilter();

// Chart Manager for Dashboard
class ChartManager {
    constructor() {
        this.charts = new Map();
    }

    createSalesChart(elementId, data) {
        const ctx = document.getElementById(elementId);
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Sales',
                    data: data.values,
                    borderColor: '#DC2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });

        this.charts.set(elementId, chart);
    }

    createCategoryChart(elementId, data) {
        const ctx = document.getElementById(elementId);
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#DC2626',
                        '#FBBF24',
                        '#10B981',
                        '#3B82F6',
                        '#8B5CF6',
                        '#EC4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        this.charts.set(elementId, chart);
    }

    createPeakTimesChart(elementId, data) {
        const ctx = document.getElementById(elementId);
        if (!ctx) return;

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Orders',
                    data: data.values,
                    backgroundColor: 'rgba(251, 191, 36, 0.6)', // primary-yellow
                    borderColor: '#FBBF24',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        this.charts.set(elementId, chart);
    }

    destroyAll() {
        this.charts.forEach(chart => chart.destroy());
        this.charts.clear();
    }
}

window.ChartManager = new ChartManager();

// Alpine.js Components
document.addEventListener('alpine:init', () => {
    // Cart Component
    Alpine.data('cart', () => ({
        items: [],
        total: 0,
        subtotal: 0,
        tax: 0,
        deliveryCharge: 0,
        promoCode: '',
        discount: 0,

        init() {
            this.loadCart();
            this.calculateTotals();
        },

        loadCart() {
            this.items = CartManager.cart;
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            this.tax = this.subtotal * 0.1; // 10% tax
            this.deliveryCharge = this.subtotal > 50 ? 0 : 5; // Free delivery over $50
            this.total = this.subtotal + this.tax + this.deliveryCharge - this.discount;
        },

        increaseQuantity(item) {
            item.quantity++;
            CartManager.updateQuantity(item.cartId, item.quantity);
            this.calculateTotals();
        },

        decreaseQuantity(item) {
            if (item.quantity > 1) {
                item.quantity--;
                CartManager.updateQuantity(item.cartId, item.quantity);
                this.calculateTotals();
            }
        },

        removeItem(item) {
            CartManager.removeItem(item.cartId);
            this.loadCart();
            this.calculateTotals();
        },

        clearCart() {
            CartManager.clearCart();
            this.loadCart();
            this.calculateTotals();
        },

        applyPromoCode() {
            if (this.promoCode === 'PASSSNA10') {
                this.discount = this.subtotal * 0.1; // 10% discount
                Toast.show('Promo code applied!', 'success');
            } else {
                this.discount = 0;
                Toast.show('Invalid promo code', 'error');
            }
            this.calculateTotals();
        }
    }));

    // Menu Item Component
    Alpine.data('menuItem', (item) => ({
        showDetails: false,
        selectedSize: 'regular',
        selectedToppings: [],
        quantity: 1,

        get price() {
            let price = item.price;
            if (item.sizes && item.sizes[this.selectedSize]) {
                price = item.sizes[this.selectedSize];
            }
            return price;
        },

        get totalPrice() {
            return this.price * this.quantity;
        },

        addToCart() {
            CartManager.addItem({
                id: item.id,
                name: item.name,
                price: this.price,
                quantity: this.quantity,
                size: this.selectedSize,
                toppings: this.selectedToppings,
                image: item.image
            });
        },

        toggleTopping(topping) {
            const index = this.selectedToppings.indexOf(topping);
            if (index > -1) {
                this.selectedToppings.splice(index, 1);
            } else {
                this.selectedToppings.push(topping);
            }
        }
    }));

    // Reservation Component
    Alpine.data('reservation', () => ({
        date: new Date().toISOString().split('T')[0],
        time: '18:00',
        guests: 2,
        table: '',
        specialRequests: '',
        availableTables: [],

        async checkAvailability() {
            try {
                const response = await fetch('/api/check-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        date: this.date,
                        time: this.time,
                        guests: this.guests
                    })
                });

                const data = await response.json();
                this.availableTables = data.tables;
                return data.available;
            } catch (error) {
                Toast.show('Error checking availability', 'error');
                return false;
            }
        },

        async submitReservation() {
            if (!this.table) {
                Toast.show('Please select a table', 'error');
                return;
            }

            try {
                const response = await fetch('/api/reservations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        date: this.date,
                        time: this.time,
                        guests: this.guests,
                        table: this.table,
                        special_requests: this.specialRequests
                    })
                });

                const data = await response.json();

                if (data.success) {
                    Toast.show('Reservation created successfully!', 'success');
                    // Reset form
                    this.guests = 2;
                    this.table = '';
                    this.specialRequests = '';
                }
            } catch (error) {
                Toast.show('Error creating reservation', 'error');
            }
        }
    }));

    // Custom Meal Builder Component
    Alpine.data('customMeal', () => ({
        base: 'rice',
        riceType: 'white',
        noodleType: 'egg',
        protein: 'chicken',
        vegetables: [],
        sauces: [],
        extras: [],

        availableVegetables: [
            { id: 1, name: 'Carrots', price: 1 },
            { id: 2, name: 'Broccoli', price: 1.5 },
            { id: 3, name: 'Bell Peppers', price: 1 },
            { id: 4, name: 'Mushrooms', price: 2 },
            { id: 5, name: 'Spinach', price: 1.5 }
        ],

        availableSauces: [
            { id: 1, name: 'Teriyaki', price: 0.5 },
            { id: 2, name: 'Soy Sauce', price: 0 },
            { id: 3, name: 'Sweet & Sour', price: 0.5 },
            { id: 4, name: 'Garlic Sauce', price: 0.75 }
        ],

        availableExtras: [
            { id: 1, name: 'Extra Cheese', price: 1 },
            { id: 2, name: 'Fried Egg', price: 1.5 },
            { id: 3, name: 'Spring Onions', price: 0.5 }
        ],

        get totalPrice() {
            let price = 10; // Base price

            // Add vegetable prices
            price += this.vegetables.reduce((sum, veg) => {
                const vegetable = this.availableVegetables.find(v => v.id === veg);
                return sum + (vegetable?.price || 0);
            }, 0);

            // Add sauce prices
            price += this.sauces.reduce((sum, sauce) => {
                const sauceObj = this.availableSauces.find(s => s.id === sauce);
                return sum + (sauceObj?.price || 0);
            }, 0);

            // Add extra prices
            price += this.extras.reduce((sum, extra) => {
                const extraObj = this.availableExtras.find(e => e.id === extra);
                return sum + (extraObj?.price || 0);
            }, 0);

            return price;
        },

        toggleVegetable(vegId) {
            const index = this.vegetables.indexOf(vegId);
            if (index > -1) {
                this.vegetables.splice(index, 1);
            } else {
                this.vegetables.push(vegId);
            }
        },

        toggleSauce(sauceId) {
            const index = this.sauces.indexOf(sauceId);
            if (index > -1) {
                this.sauces.splice(index, 1);
            } else {
                this.sauces.push(sauceId);
            }
        },

        toggleExtra(extraId) {
            const index = this.extras.indexOf(extraId);
            if (index > -1) {
                this.extras.splice(index, 1);
            } else {
                this.extras.push(extraId);
            }
        },

        addToCart() {
            const meal = {
                id: 'custom-' + Date.now(),
                name: 'Custom ' + this.base.charAt(0).toUpperCase() + this.base.slice(1) + ' Meal',
                price: this.totalPrice,
                quantity: 1,
                custom: true,
                ingredients: {
                    base: this.base,
                    protein: this.protein,
                    vegetables: this.vegetables.map(id =>
                        this.availableVegetables.find(v => v.id === id)?.name
                    ),
                    sauces: this.sauces.map(id =>
                        this.availableSauces.find(s => s.id === id)?.name
                    ),
                    extras: this.extras.map(id =>
                        this.availableExtras.find(e => e.id === id)?.name
                    )
                }
            };

            CartManager.addItem(meal);
        }
    }));

    // Admin Dashboard Component
    Alpine.data('adminDashboard', () => ({
        stats: {
            revenue: 0,
            orders: 0,
            reservations: 0,
            growth: 0
        },
        recentOrders: [],
        chartData: null,

        async init() {
            await this.loadStats();
            await this.loadRecentOrders();
            await this.loadChartData();
        },

        async loadStats() {
            try {
                const response = await fetch('/api/admin/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        async loadChartData() {
            try {
                const response = await fetch('/api/admin/chart-data');
                const data = await response.json();
                this.chartData = data;
                this.initializeCharts();
            } catch (error) {
                console.error('Error loading chart data:', error);
                // Fallback to hardcoded if API fails
                this.initializeCharts();
            }
        },

        async loadRecentOrders() {
            try {
                const response = await fetch('/api/admin/recent-orders');
                const data = await response.json();
                this.recentOrders = data;
            } catch (error) {
                console.error('Error loading recent orders:', error);
            }
        },

        initializeCharts() {
            if (this.chartData) {
                // Use dynamic data
                if (this.chartData.weekly_sales) {
                    ChartManager.createSalesChart('salesChart', {
                        labels: this.chartData.weekly_sales.map(d => d.day),
                        values: this.chartData.weekly_sales.map(d => d.sales)
                    });
                }

                if (this.chartData.peak_order_times) {
                    ChartManager.createPeakTimesChart('peakTimesChart', this.chartData.peak_order_times);
                }

                // Category chart data would need similar mapping if available in API
                const categoryData = {
                    labels: ['Main Course', 'Appetizers', 'Desserts', 'Drinks', 'Specials'],
                    values: [40, 20, 15, 15, 10]
                };
                ChartManager.createCategoryChart('categoryChart', categoryData);
            } else {
                // Fallback to hardcoded data
                const salesData = {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    values: [1200, 1900, 1500, 2100, 1800, 2400, 2000]
                };

                const categoryData = {
                    labels: ['Main Course', 'Appetizers', 'Desserts', 'Drinks', 'Specials'],
                    values: [40, 20, 15, 15, 10]
                };

                ChartManager.createSalesChart('salesChart', salesData);
                ChartManager.createCategoryChart('categoryChart', categoryData);
            }
        },

        updateOrderStatus(orderId, status) {
            fetch(`/api/admin/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Toast.show('Order status updated!', 'success');
                    this.loadRecentOrders();
                }
            })
            .catch(error => {
                Toast.show('Error updating order status', 'error');
            });
        }
    }));

    // Dining Section Component
    Alpine.data('diningSection', () => ({
        tableNumber: '',
        showPasswordModal: false,
        password: '',
        isInDiningSection: false,

        enterDiningSection() {
            if (!this.tableNumber) {
                Toast.show('Please enter table number', 'error');
                return;
            }

            // Check if table exists and is available
            fetch(`/api/tables/${this.tableNumber}/check`)
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        this.isInDiningSection = true;
                        localStorage.setItem('dining_table', this.tableNumber);
                        Toast.show(`Welcome to Table ${this.tableNumber}!`, 'success');
                    } else {
                        Toast.show('Table is not available', 'error');
                    }
                });
        },

        exitDiningSection() {
            this.showPasswordModal = true;
        },

        async confirmExit() {
            try {
                const response = await fetch('/dining/verify-exit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ password: this.password })
                });

                const data = await response.json();

                if (data.success) {
                    this.isInDiningSection = false;
                    this.tableNumber = '';
                    this.password = '';
                    this.showPasswordModal = false;
                    localStorage.removeItem('dining_table');
                    Toast.show('Exited dining section', 'success');
                    window.location.href = '/customer/dashboard';
                } else {
                    Toast.show('Incorrect password', 'error');
                    this.password = '';
                }
            } catch (error) {
                Toast.show('Verification failed', 'error');
            }
        }
    }));
});

// Utility Functions
window.Utils = {
    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    // Format date
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },

    // Format time
    formatTime(timeString) {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Generate PDF
    async generatePDF(elementId, filename = 'document.pdf') {
        const { jsPDF } = window.jspdf;
        const element = document.getElementById(elementId);

        if (!element) {
            Toast.show('Element not found', 'error');
            return;
        }

        try {
            const pdf = new jsPDF('p', 'mm', 'a4');
            await pdf.html(element, {
                callback: function(pdf) {
                    pdf.save(filename);
                },
                x: 10,
                y: 10,
                width: 190,
                windowWidth: element.scrollWidth
            });
        } catch (error) {
            Toast.show('Error generating PDF', 'error');
        }
    },

    // Copy to clipboard
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Toast.show('Copied to clipboard!', 'success');
        }).catch(err => {
            Toast.show('Failed to copy', 'error');
        });
    }
};

// Image Upload Handler
class ImageUploader {
    constructor(inputId, previewId) {
        this.input = document.getElementById(inputId);
        this.preview = document.getElementById(previewId);

        if (this.input && this.preview) {
            this.initialize();
        }
    }

    initialize() {
        this.input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.previewImage(file);
            }
        });

        // Drag and drop support
        this.preview.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.preview.classList.add('border-primary-red', 'bg-red-50');
        });

        this.preview.addEventListener('dragleave', () => {
            this.preview.classList.remove('border-primary-red', 'bg-red-50');
        });

        this.preview.addEventListener('drop', (e) => {
            e.preventDefault();
            this.preview.classList.remove('border-primary-red', 'bg-red-50');

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.input.files = e.dataTransfer.files;
                this.previewImage(file);
            }
        });
    }

    previewImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.preview.src = e.target.result;
            this.preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Initialize on DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Alpine.js
    Alpine.start();

    // Initialize image uploaders
    document.querySelectorAll('[data-image-upload]').forEach(element => {
        const inputId = element.dataset.inputId;
        const previewId = element.dataset.previewId;
        new ImageUploader(inputId, previewId);
    });

    // Initialize tooltips
    const tooltipTriggers = document.querySelectorAll('.has-tooltip');
    tooltipTriggers.forEach(trigger => {
        trigger.addEventListener('mouseenter', function() {
            const tooltip = this.querySelector('.tooltip');
            if (tooltip) {
                const rect = this.getBoundingClientRect();
                tooltip.style.top = `${rect.bottom + 5}px`;
                tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
            }
        });
    });

    // Lazy loading images
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Find invalid fields
                const invalidFields = this.querySelectorAll(':invalid');
                invalidFields.forEach(field => {
                    field.classList.add('border-red-500');

                    // Show error message
                    const errorSpan = field.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('form-error')) {
                        errorSpan.textContent = field.validationMessage;
                    }
                });

                // Scroll to first invalid field
                if (invalidFields.length > 0) {
                    invalidFields[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });

        // Clear validation on input
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('border-red-500');
                const errorSpan = this.nextElementSibling;
                if (errorSpan && errorSpan.classList.contains('form-error')) {
                    errorSpan.textContent = '';
                }
            });
        });
    });

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-auto-hide').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Initialize countdown timers
    document.querySelectorAll('[data-countdown]').forEach(element => {
        const endTime = new Date(element.dataset.countdown).getTime();

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                element.innerHTML = 'Offer Expired';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            element.innerHTML = `
                <span class="bg-primary-red text-white px-2 py-1 rounded">${days}d</span>
                <span class="bg-primary-yellow text-gray-800 px-2 py-1 rounded">${hours}h</span>
                <span class="bg-navy-blue text-white px-2 py-1 rounded">${minutes}m</span>
                <span class="bg-gray-800 text-white px-2 py-1 rounded">${seconds}s</span>
            `;
        };

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });

    // Initialize parallax effect for hero sections
    const heroSections = document.querySelectorAll('.parallax-hero');
    heroSections.forEach(section => {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            section.style.transform = `translate3d(0, ${rate}px, 0)`;
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('[data-print]');
    printButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.dataset.print;
            const element = document.getElementById(target);
            if (element) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print - PaSSSna Restaurant</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                @media print { .no-print { display: none; } }
                            </style>
                        </head>
                        <body>
                            ${element.innerHTML}
                            <script>
                                window.onload = () => window.print();
                            </script>
                        </body>
                    </html>
                `);
                printWindow.document.close();
            }
        });
    });

    // Initialize quantity selectors
    document.querySelectorAll('.quantity-selector').forEach(selector => {
        const minusBtn = selector.querySelector('[data-action="decrease"]');
        const plusBtn = selector.querySelector('[data-action="increase"]');
        const input = selector.querySelector('input[type="number"]');

        minusBtn?.addEventListener('click', () => {
            const value = parseInt(input.value) || 1;
            input.value = Math.max(1, value - 1);
            input.dispatchEvent(new Event('change'));
        });

        plusBtn?.addEventListener('click', () => {
            const value = parseInt(input.value) || 1;
            input.value = value + 1;
            input.dispatchEvent(new Event('change'));
        });
    });

    // Confirmation dialogs
    document.querySelectorAll('[data-confirm]').forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Are you sure?';

            e.preventDefault();

            Swal.fire({
                title: 'Confirmation',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = this.closest('form');
                    if (form) {
                        form.submit();
                    } else {
                        const href = this.getAttribute('href');
                        if (href) {
                            window.location.href = href;
                        }
                    }
                }
            });
        });
    });
});

// Export modules
export { Alpine, Chart, Swal, CartManager, ReservationSystem, MenuFilter, ChartManager };
