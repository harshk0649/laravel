/**
 * MyShop - Professional E-commerce JavaScript
 * Handles cart functionality, quantity management, and UI interactions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize functionality
    initQuantityControls();
    initProductCards();
    initModalHandlers();
    initAddToCartAnimations();
    
    // Observe theme changes
    observeThemeChanges();
});

/**
 * Initialize quantity increment/decrement functionality on product cards
 */
function initQuantityControls() {
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            
            if (!productId) {
                console.warn('Quantity button missing data-id attribute');
                return;
            }
            
            const quantitySpan = document.getElementById(`quantity-${productId}`);
            const quantityInput = document.getElementById(`input-quantity-${productId}`);
            
            if (!quantitySpan) {
                console.warn(`Quantity span element not found for product ID: ${productId}`);
                return;
            }
            
            updateQuantity(quantitySpan, quantityInput, action);
        });
    });
}

/**
 * Initialize hover effects and interaction for product cards
 */

function initModalHandlers() {
    // Add event listeners to number inputs in modals
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            const id = this.id.replace('quantity', '');
            const hiddenInput = document.getElementById(`hidden-quantity${id}`);
            
            // Validate input
            let value = parseInt(this.value);
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 99;
            
            // Enforce constraints
            if (isNaN(value) || value < min) {
                value = min;
                this.value = min;
            } else if (value > max) {
                value = max;
                this.value = max;
            }
            
            // Update hidden input if it exists
            if (hiddenInput) {
                hiddenInput.value = value;
            }
        });
    });
    
    // Initialize bootstrap modals with enhanced features
    const productModals = document.querySelectorAll('.modal');
    productModals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            // Focus first input when modal opens
            const firstInput = this.querySelector('input[type="number"]');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        });
    });
}

/**
 * Initialize add to cart button animations and effects
 */
function initAddToCartAnimations() {
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            this.classList.add('btn-loading');
            
            // Remove loading state after animation (in a real app, this would happen after AJAX completion)
            setTimeout(() => {
                this.classList.remove('btn-loading');
                
                // Show cart animation
                animateCartCount();
            }, 800);
        });
    });
}

/**
 * Update quantity value and associated inputs
 * @param {HTMLElement} quantitySpan - The span showing the quantity
 * @param {HTMLElement} quantityInput - The hidden input storing the quantity
 * @param {string} action - Either 'increment' or 'decrement'
 */
function updateQuantity(quantitySpan, quantityInput, action) {
    let currentQty = parseInt(quantitySpan.textContent);
    const maxQty = quantitySpan.getAttribute('data-max') || 99;
    
    if (isNaN(currentQty)) {
        currentQty = 1;
    }
    
    if (action === 'increment' && currentQty < maxQty) {
        currentQty++;
    } else if (action === 'decrement' && currentQty > 1) {
        currentQty--;
    }
    
    // Update displayed value
    quantitySpan.textContent = currentQty;
    
    // Update form input if it exists
    if (quantityInput) {
        quantityInput.value = currentQty;
    }
}

/**
 * Global functions for modal quantity control
 */
window.incrementQty = function(productId) {
    const input = document.getElementById(`quantity${productId}`);
    const hiddenInput = document.getElementById(`hidden-quantity${productId}`);
    
    if (!input) {
        console.warn(`Quantity input not found for product ID: ${productId}`);
        return;
    }
    
    let value = parseInt(input.value);
    const max = parseInt(input.getAttribute('max')) || 99;
    
    if (isNaN(value)) {
        value = 1;
    }
    
    if (value < max) {
        value++;
        input.value = value;
        
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }
};

window.decrementQty = function(productId) {
    const input = document.getElementById(`quantity${productId}`);
    const hiddenInput = document.getElementById(`hidden-quantity${productId}`);
    
    if (!input) {
        console.warn(`Quantity input not found for product ID: ${productId}`);
        return;
    }
    
    let value = parseInt(input.value);
    
    if (isNaN(value)) {
        value = 1;
    }
    
    if (value > 1) {
        value--;
        input.value = value;
        
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }
};

/**
 * Animate the cart counter when adding items
 */
function animateCartCount() {
    const cartCount = document.getElementById('cart-count');
    
    if (cartCount) {
        // Get current count
        let count = parseInt(cartCount.textContent || '0');
        
        // Increment count
        count++;
        
        // Update display
        cartCount.textContent = count;
        
        // Add animation class
        cartCount.classList.add('pop-animation');
        
        // Remove animation class after animation completes
        setTimeout(() => {
            cartCount.classList.remove('pop-animation');
        }, 300);
    }
}

/**
 * Observe and handle theme changes (dark/light mode)
 */
function observeThemeChanges() {
    // Check for theme toggle element
    const themeToggle = document.querySelector('.theme-toggle');
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            this.classList.toggle('dark');
            
            // Save preference to localStorage
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
        });
        
        // Initialize theme from saved preference
        const savedDarkMode = localStorage.getItem('darkMode') === 'true';
        if (savedDarkMode) {
            document.body.classList.add('dark-mode');
            themeToggle.classList.add('dark');
        }
    }
    
    // Check system preference if no saved preference
    if (!localStorage.getItem('darkMode')) {
        const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDarkMode) {
            document.body.classList.add('dark-mode');
            if (themeToggle) {
                themeToggle.classList.add('dark');
            }
        }
    }
}

/**
 * Add CSS class for animation during page transitions
 * @param {string} url - The URL to navigate to
 */
function navigateWithTransition(url) {
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay active';
    document.body.appendChild(overlay);
    
    setTimeout(() => {
        window.location.href = url;
    }, 500);
}