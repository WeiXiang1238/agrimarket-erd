// Products Page JavaScript

let currentPage = 1;
let isLoading = false;

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    initializeEventListeners();
    updateCartCount();
});

function initializeEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchProducts();
            }, 500);
        });
    }

    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', searchProducts);
    }

    // Close modals when clicking outside
    window.addEventListener('click', function (event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Escape key to close modals
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllModals();
        }
    });
}

// Search and filter products
function searchProducts(page = 1) {
    if (isLoading) return;

    isLoading = true;
    currentPage = page;

    const searchTerm = document.getElementById('searchInput').value;
    const categoryId = document.getElementById('categoryFilter').value;

    const formData = new FormData();
    formData.append('action', 'get_products');
    formData.append('page', page);
    formData.append('limit', 12);
    formData.append('search', searchTerm);
    formData.append('category_id', categoryId);

    // Show loading state
    const productsGrid = document.getElementById('productsGrid');
    productsGrid.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading products...</div>';

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
                updatePagination(data.page, data.totalPages);
                updateURL(searchTerm, categoryId, page);
            } else {
                showNotification('Failed to load products: ' + (data.message || 'Unknown error'), 'error');
                productsGrid.innerHTML = '<div class="no-products"><h3>Error loading products</h3><p>Please try again later.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load products. Please try again.', 'error');
            productsGrid.innerHTML = '<div class="no-products"><h3>Error loading products</h3><p>Please try again later.</p></div>';
        })
        .finally(() => {
            isLoading = false;
        });
}

// Display products in grid
function displayProducts(products) {
    const productsGrid = document.getElementById('productsGrid');

    if (!products || products.length === 0) {
        productsGrid.innerHTML = `
            <div class="no-products">
                <div class="no-products-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h3>No products found</h3>
                <p>Try adjusting your search or filter criteria.</p>
            </div>
        `;
        return;
    }

    const productsHTML = products.map(product => `
        <div class="product-card" data-product-id="${product.product_id}">
            <div class="product-image">
                <img src="${product.image_path || '/agrimarket-erd/uploads/products/default-product.jpg'}" 
                     alt="${escapeHtml(product.name)}"
                     onerror="this.src='/agrimarket-erd/uploads/products/default-product.jpg'">
                
                ${product.is_discounted ? `
                    <div class="discount-badge">
                        ${product.discount_percent}% OFF
                    </div>
                ` : ''}
                
                <div class="product-actions">
                    <button class="btn-icon btn-primary" 
                            onclick="viewProductDetails(${product.product_id})"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon btn-success" 
                            onclick="quickAddToCart(${product.product_id})"
                            title="Quick Add to Cart"
                            ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
            </div>
            
            <div class="product-info">
                <h3 class="product-name">${escapeHtml(product.name)}</h3>
                <p class="product-vendor">by ${escapeHtml(product.vendor_name || 'Unknown Vendor')}</p>
                <p class="product-category">${escapeHtml(product.category_name || 'Uncategorized')}</p>
                
                <div class="product-pricing">
                    <span class="current-price">RM ${parseFloat(product.selling_price).toFixed(2)}</span>
                    ${product.is_discounted ? `
                        <span class="original-price">RM ${parseFloat(product.base_price).toFixed(2)}</span>
                    ` : ''}
                </div>
                
                <div class="product-stock">
                    ${product.stock_quantity > 0 ? `
                        <span class="stock-available">
                            <i class="fas fa-check-circle"></i>
                            ${product.stock_quantity} in stock
                        </span>
                    ` : `
                        <span class="stock-out">
                            <i class="fas fa-times-circle"></i>
                            Out of stock
                        </span>
                    `}
                </div>
                
                <div class="product-actions-bottom">
                    <button class="btn btn-primary btn-add-cart" 
                            onclick="showAddToCartModal(${product.product_id})"
                            ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    productsGrid.innerHTML = productsHTML;
}

// Update pagination
function updatePagination(currentPage, totalPages) {
    // This would update pagination if we're using AJAX
    // For now, we're using server-side pagination
}

// Update URL without page reload
function updateURL(search, categoryId, page) {
    const url = new URL(window.location);

    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }

    if (categoryId) {
        url.searchParams.set('category_id', categoryId);
    } else {
        url.searchParams.delete('category_id');
    }

    if (page > 1) {
        url.searchParams.set('page', page);
    } else {
        url.searchParams.delete('page');
    }

    window.history.replaceState({}, '', url);
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    searchProducts(1);
}

// Quick add to cart (quantity = 1)
function quickAddToCart(productId) {
    addToCart(productId, 1);
}

// Show add to cart modal
function showAddToCartModal(productId) {
    // First get product details
    const formData = new FormData();
    formData.append('action', 'get_product_details');
    formData.append('product_id', productId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAddToCartModal(data.product);
            } else {
                showNotification('Failed to load product details: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load product details', 'error');
        });
}

// Display add to cart modal
function displayAddToCartModal(product) {
    const modalContent = `
        <div class="add-to-cart-form">
            <div class="product-summary">
                <h3>${escapeHtml(product.name)}</h3>
                <p class="price">RM ${parseFloat(product.selling_price).toFixed(2)}</p>
                <p class="stock">Stock available: ${product.stock_quantity}</p>
            </div>
            
            <div class="quantity-selector">
                <label>Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" onclick="updateModalQuantity(-1)" id="decreaseBtn">-</button>
                    <input type="number" id="modalQuantity" value="1" min="1" max="${product.stock_quantity}" 
                           onchange="validateModalQuantity()">
                    <button type="button" onclick="updateModalQuantity(1)" id="increaseBtn">+</button>
                </div>
            </div>
            
            <div class="total-price">
                <strong>Total: RM <span id="modalTotal">${parseFloat(product.selling_price).toFixed(2)}</span></strong>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeAddToCart()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddToCart(${product.product_id}, ${product.selling_price})">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        </div>
    `;

    document.getElementById('addToCartContent').innerHTML = modalContent;
    document.getElementById('addToCartModal').style.display = 'block';
}

// Update quantity in modal
function updateModalQuantity(change) {
    const quantityInput = document.getElementById('modalQuantity');
    const currentQuantity = parseInt(quantityInput.value);
    const newQuantity = currentQuantity + change;
    const maxQuantity = parseInt(quantityInput.max);

    if (newQuantity >= 1 && newQuantity <= maxQuantity) {
        quantityInput.value = newQuantity;
        validateModalQuantity();
    }
}

// Validate quantity in modal
function validateModalQuantity() {
    const quantityInput = document.getElementById('modalQuantity');
    const quantity = parseInt(quantityInput.value);
    const maxQuantity = parseInt(quantityInput.max);

    // Update button states
    document.getElementById('decreaseBtn').disabled = quantity <= 1;
    document.getElementById('increaseBtn').disabled = quantity >= maxQuantity;

    // Update total price
    const pricePerUnit = parseFloat(document.querySelector('.price').textContent.replace('RM ', ''));
    const total = quantity * pricePerUnit;
    document.getElementById('modalTotal').textContent = total.toFixed(2);
}

// Confirm add to cart from modal
function confirmAddToCart(productId, price) {
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    addToCart(productId, quantity);
    closeAddToCart();
}

// Add product to cart
function addToCart(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Added ${quantity} item(s) to cart successfully!`, 'success');
                updateCartCount();
            } else {
                showNotification('Failed to add to cart: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add to cart. Please try again.', 'error');
        });
}

// View product details modal
function viewProductDetails(productId) {
    const formData = new FormData();
    formData.append('action', 'get_product_details');
    formData.append('product_id', productId);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProductDetailsModal(data.product);
            } else {
                showNotification('Failed to load product details: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load product details', 'error');
        });
}

// Display product details modal
function displayProductDetailsModal(product) {
    const modalContent = `
        <div class="product-details">
            <div class="product-details-image">
                <img src="../../${product.image_path || '../../agrimarket-erd/uploads/products/default-product.jpg'}" 
                     alt="${escapeHtml(product.name)}"
                     onerror="this.src='../../agrimarket-erd/uploads/products/default-product.jpg'">
            </div>
            
            <div class="product-details-info">
                <h3>${escapeHtml(product.name)}</h3>
                
                <div class="price">
                    RM ${parseFloat(product.selling_price).toFixed(2)}
                    ${product.is_discounted ? `
                        <span class="original-price">RM ${parseFloat(product.base_price).toFixed(2)}</span>
                        <span class="discount-badge">${product.discount_percent}% OFF</span>
                    ` : ''}
                </div>
                
                <div class="description">
                    ${escapeHtml(product.description || 'No description available.')}
                </div>
                
                <div class="meta">
                    <div class="meta-item">
                        <label>Vendor:</label>
                        <span>${escapeHtml(product.vendor_name || 'Unknown')}</span>
                    </div>
                    <div class="meta-item">
                        <label>Category:</label>
                        <span>${escapeHtml(product.category_name || 'Uncategorized')}</span>
                    </div>
                    <div class="meta-item">
                        <label>Stock:</label>
                        <span class="${product.stock_quantity > 0 ? 'stock-available' : 'stock-out'}">
                            ${product.stock_quantity > 0 ? `${product.stock_quantity} available` : 'Out of stock'}
                        </span>
                    </div>
                    <div class="meta-item">
                        <label>Packaging:</label>
                        <span>${escapeHtml(product.packaging || 'Standard')}</span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeProductDetails()">Close</button>
                    <button type="button" class="btn btn-primary" 
                            onclick="showAddToCartModal(${product.product_id}); closeProductDetails();"
                            ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `;

    document.getElementById('productDetailsContent').innerHTML = modalContent;
    document.getElementById('productDetailsModal').style.display = 'block';
}

// Update cart count in header
function updateCartCount() {
    // This would make an AJAX call to get current cart count
    // For now, we'll just update on page load and after adding items
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_cart_count'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cartCount').textContent = data.count || 0;
            }
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
        });
}

// Modal management functions
function closeProductDetails() {
    document.getElementById('productDetailsModal').style.display = 'none';
}

function closeAddToCart() {
    document.getElementById('addToCartModal').style.display = 'none';
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
}

// Utility functions
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Hide notification after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
} 