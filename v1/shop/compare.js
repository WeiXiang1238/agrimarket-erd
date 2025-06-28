// Product comparison functionality for AgriMarket Shop
document.addEventListener('DOMContentLoaded', function() {
    // Initialize comparison functionality
    initializeComparison();
    
    // Load comparison contents
    loadComparisonContents();
});

let comparisonList = [];

function initializeComparison() {
    // Add to comparison button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('compare-btn')) {
            e.preventDefault();
            addToComparison(e.target);
        }
    });
    
    // Clear comparison button
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('clear-comparison')) {
            e.preventDefault();
            clearComparison();
        }
    });
}

function addToComparison(button) {
    const productId = button.getAttribute('data-product-id');
    
    // Check if product is already in comparison
    if (comparisonList.includes(productId)) {
        showNotification('Product is already in comparison list', 'warning');
        return;
    }
    
    // Check if comparison list is full (max 4 products)
    if (comparisonList.length >= 4) {
        showNotification('You can compare up to 4 products at a time', 'warning');
        return;
    }
    
    // Add to comparison list
    comparisonList.push(productId);
    
    // Update button state
    button.textContent = 'Added to Compare';
    button.classList.add('added');
    button.disabled = true;
    
    // Show success message
    showNotification('Product added to comparison', 'success');
    
    // Load comparison contents
    loadComparisonContents();
    
    // Store in localStorage
    localStorage.setItem('comparisonList', JSON.stringify(comparisonList));
}

function removeFromComparison(productId) {
    const index = comparisonList.indexOf(productId);
    if (index > -1) {
        comparisonList.splice(index, 1);
        
        // Update button state
        const button = document.querySelector(`[data-product-id="${productId}"].compare-btn`);
        if (button) {
            button.textContent = 'Compare';
            button.classList.remove('added');
            button.disabled = false;
        }
        
        // Load comparison contents
        loadComparisonContents();
        
        // Update localStorage
        localStorage.setItem('comparisonList', JSON.stringify(comparisonList));
        
        showNotification('Product removed from comparison', 'success');
    }
}

function clearComparison() {
    if (comparisonList.length === 0) {
        showNotification('Comparison list is already empty', 'info');
        return;
    }
    
    if (confirm('Are you sure you want to clear the comparison list?')) {
        // Reset all compare buttons
        document.querySelectorAll('.compare-btn.added').forEach(button => {
            button.textContent = 'Compare';
            button.classList.remove('added');
            button.disabled = false;
        });
        
        // Clear comparison list
        comparisonList = [];
        
        // Load comparison contents
        loadComparisonContents();
        
        // Clear localStorage
        localStorage.removeItem('comparisonList');
        
        showNotification('Comparison list cleared', 'success');
    }
}

function loadComparisonContents() {
    const compareContents = document.getElementById('compare-contents');
    if (!compareContents) return;
    
    if (comparisonList.length === 0) {
        compareContents.innerHTML = '<p class="empty-comparison">No products in comparison list</p>';
        return;
    }
    
    // Show loading state
    compareContents.innerHTML = '<div class="loading">Loading comparison...</div>';
    
    // Fetch product details for comparison
    const promises = comparisonList.map(productId => 
        fetch(`/agrimarket-erd/v1/shop/compare.php?action=get_product&product_id=${productId}`)
        .then(response => response.json())
    );
    
    Promise.all(promises)
    .then(results => {
        const products = results.filter(result => result.success).map(result => result.product);
        displayComparison(products);
    })
    .catch(error => {
        console.error('Error loading comparison:', error);
        compareContents.innerHTML = '<p class="error">Failed to load comparison</p>';
    });
}

function displayComparison(products) {
    const compareContents = document.getElementById('compare-contents');
    if (!compareContents) return;
    
    if (products.length === 0) {
        compareContents.innerHTML = '<p class="empty-comparison">No products to compare</p>';
        return;
    }
    
    let html = `
        <div class="comparison-header">
            <h3>Product Comparison (${products.length} items)</h3>
            <button class="btn btn-sm btn-danger clear-comparison">Clear All</button>
        </div>
        <div class="comparison-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        ${products.map(product => `
                            <th>
                                <div class="product-header">
                                    <img src="${product.image_path || 'https://via.placeholder.com/100'}" alt="${product.name}">
                                    <h4>${product.name}</h4>
                                    <button class="remove-compare" onclick="removeFromComparison(${product.product_id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Price</strong></td>
                        ${products.map(product => `
                            <td>RM ${parseFloat(product.selling_price).toFixed(2)}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td><strong>Stock</strong></td>
                        ${products.map(product => `
                            <td>${product.stock_quantity} units</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td><strong>SKU</strong></td>
                        ${products.map(product => `
                            <td>${product.sku || 'N/A'}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td><strong>Weight</strong></td>
                        ${products.map(product => `
                            <td>${product.weight || 'N/A'}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        ${products.map(product => `
                            <td><span class="status-${product.status}">${product.status}</span></td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td><strong>Actions</strong></td>
                        ${products.map(product => `
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="addToCartFromCompare(${product.product_id})">
                                    Add to Cart
                                </button>
                            </td>
                        `).join('')}
                    </tr>
                </tbody>
            </table>
        </div>
    `;
    
    compareContents.innerHTML = html;
}

function addToCartFromCompare(productId) {
    // Create a temporary form to add to cart
    const form = document.createElement('form');
    form.className = 'add-to-cart-form';
    form.setAttribute('data-product-id', productId);
    
    const quantityInput = document.createElement('input');
    quantityInput.type = 'number';
    quantityInput.name = 'quantity';
    quantityInput.value = '1';
    quantityInput.min = '1';
    
    form.appendChild(quantityInput);
    
    // Trigger add to cart
    const event = new Event('submit', { bubbles: true });
    form.dispatchEvent(event);
    
    showNotification('Product added to cart from comparison', 'success');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.backgroundColor = '#10b981';
            break;
        case 'error':
            notification.style.backgroundColor = '#ef4444';
            break;
        case 'warning':
            notification.style.backgroundColor = '#f59e0b';
            break;
        default:
            notification.style.backgroundColor = '#3b82f6';
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Load comparison list from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedComparison = localStorage.getItem('comparisonList');
    if (savedComparison) {
        comparisonList = JSON.parse(savedComparison);
        
        // Update button states
        comparisonList.forEach(productId => {
            const button = document.querySelector(`[data-product-id="${productId}"].compare-btn`);
            if (button) {
                button.textContent = 'Added to Compare';
                button.classList.add('added');
                button.disabled = true;
            }
        });
    }
}); 