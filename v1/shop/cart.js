// Cart functionality for AgriMarket Shop
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart.js loaded, initializing cart...');
    
    // Test AJAX connectivity first
    testAjaxConnection();
    
    // Initialize cart functionality
    initializeCart();
    
    // Always load cart contents via AJAX to get properly formatted data
    loadCartContents();
    
    // Also try to load cart contents after a short delay to ensure DOM is ready
    setTimeout(() => {
        const cartContents = document.getElementById('cart-contents');
        if (cartContents && cartContents.innerHTML === '') {
            console.log('Cart contents empty, reloading...');
            loadCartContents();
        }
    }, 500);
});

function testAjaxConnection() {
    console.log('Testing AJAX connection...');
    fetch('/agrimarket-erd/v1/shop/cart.php?action=test', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Test response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Test response data:', data);
        if (data.success) {
            console.log('AJAX connection working');
            // Test database connection
            testDatabaseConnection();
        } else {
            console.error('AJAX test failed:', data.message);
        }
    })
    .catch(error => {
        console.error('AJAX test error:', error);
    });
}

function testDatabaseConnection() {
    console.log('Testing database connection...');
    fetch('/agrimarket-erd/v1/shop/cart.php?action=test_update', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('DB test response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('DB test response data:', data);
        if (data.success) {
            console.log('Database connection working');
        } else {
            console.error('Database test failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Database test error:', error);
    });
}

function initializeCart() {
    // Add to cart form submission
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('add-to-cart-form')) {
            e.preventDefault();
            addToCart(e.target);
        }
    });
    
    // Update cart quantity
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('cart-quantity')) {
            updateCartQuantity(e.target);
        }
    });
    
    // Remove from cart
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-from-cart')) {
            e.preventDefault();
            removeFromCart(e.target);
        }
    });
}

function addToCart(form) {
    const productId = form.getAttribute('data-product-id');
    const quantityInput = form.querySelector('input[name="quantity"]');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Adding...';
    submitBtn.disabled = true;
    
    // Prepare form data
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    // Send AJAX request
    fetch('/agrimarket-erd/v1/shop/cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Product added to cart successfully!', 'success');
            
            // Update cart count in header
            updateCartBadge(data.cart);
            
            // Reload cart contents
            loadCartContents();
            
            // Reset form
            if (quantityInput) {
                quantityInput.value = 1;
            }
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while adding to cart', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function updateCartQuantity(input) {
    const productId = input.getAttribute('data-product-id');
    const newQuantity = parseInt(input.value);
    
    if (newQuantity < 1) {
        input.value = 1;
        return;
    }
    
    // Update the data attribute for the checkbox
    const checkbox = document.querySelector(`.cart-item-checkbox[data-product-id="${productId}"]`);
    if (checkbox) {
        checkbox.setAttribute('data-quantity', newQuantity);
    }
    
    // Update cart via AJAX
    fetch('/agrimarket-erd/v1/shop/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=update&product_id=${productId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the cart display
            loadCartContents();
            // Update summary
            updateCartSummary();
        } else {
            console.error('Error updating cart:', data.message);
            // Revert the input value
            loadCartContents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert the input value
        loadCartContents();
    });
}

function removeFromCart(button) {
    const productId = button.getAttribute('data-product-id');
    
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);
    
    fetch('/agrimarket-erd/v1/shop/cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart);
            loadCartContents();
            showNotification('Item removed from cart', 'success');
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while removing item', 'error');
    });
}

function loadCartContents() {
    const cartContents = document.getElementById('cart-contents');
    if (!cartContents) {
        console.error('Cart contents element not found!');
        return;
    }
    
    console.log('Loading cart contents...');
    
    // Show loading state
    cartContents.innerHTML = '<div class="loading">Loading cart...</div>';
    
    // Always load via AJAX to get properly formatted cart data with product information
    fetch('/agrimarket-erd/v1/shop/cart.php?action=get', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Cart response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Cart data received:', data);
        if (data.success) {
            displayCartContents(data.cart);
        } else {
            console.error('Cart load failed:', data.message);
            cartContents.innerHTML = '<p class="error">Failed to load cart: ' + (data.message || 'Unknown error') + '</p>';
        }
    })
    .catch(error => {
        console.error('Error loading cart:', error);
        cartContents.innerHTML = '<p class="error">An error occurred while loading cart: ' + error.message + '</p>';
    });
}

function displayCartContents(cart) {
    const cartContents = document.getElementById('cart-contents');
    const cartActions = document.getElementById('cart-actions');
    const cartItemCount = document.getElementById('cart-item-count');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartContents) return;
    
    // Debug: Log the entire cart object
    console.log('=== CART DEBUG ===');
    console.log('Cart object:', cart);
    console.log('Cart type:', typeof cart);
    console.log('Cart keys:', Object.keys(cart));
    console.log('Cart values:', Object.values(cart));
    
    if (!cart || Object.keys(cart).length === 0) {
        cartContents.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any products to your cart yet.</p>
                <a href="/agrimarket-erd/v1/shop/" class="btn btn-primary">
                    <i class="fas fa-store"></i> Start Shopping
                </a>
            </div>
        `;
        if (cartActions) cartActions.style.display = 'none';
        if (cartItemCount) cartItemCount.textContent = '0 items';
        if (cartTotal) cartTotal.textContent = 'Total: RM 0.00';
        return;
    }
    
    let cartTableHtml = `
      <div class="cart-table-wrapper">
      <div class="cart-col-select">
                  <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                  <label for="select-all">Select All</label>
        </div>
        <table class="cart-table">
          <thead>
            <tr class="cart-table-header">
              <th style="width: 250px;">
                
              </th>
              <th style="width: 400px;">Product</th>
              <th style="width: 350px;">Price</th>
              <th style="width: 350px;">Quantity</th>
              <th style="width: 250px;">Subtotal</th>
              <th style="width: 450px;">Actions</th>
            </tr>
          </thead>
          <tbody>
    `;
    
    let total = 0;
    let itemCount = 0;
    
    Object.values(cart).forEach((item, index) => {
        console.log(`=== ITEM ${index + 1} DEBUG ===`);
        console.log('Raw item:', item);
        console.log('Item price (raw):', item.price);
        console.log('Item price type:', typeof item.price);
        console.log('Item quantity (raw):', item.quantity);
        console.log('Item quantity type:', typeof item.quantity);
        
        // Check if price exists
        if (item.price === undefined || item.price === null) {
            console.error(`Price is undefined/null for item ${index + 1}:`, item);
            console.log('Available item properties:', Object.keys(item));
        }
        
        // Ensure price and quantity are numbers
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        const itemTotal = price * quantity;
        
        console.log('Parsed values:', {
            price: price,
            quantity: quantity,
            itemTotal: itemTotal
        });
        
        total += itemTotal;
        itemCount += quantity;
        
        cartTableHtml += `
          <tr class="cart-item">
            <td>
              <input type="checkbox"
    class="cart-item-checkbox"
    data-product-id="${item.product_id}"
    data-price="${price}"
    data-quantity="${quantity}"
    onchange="updateCartSummary()">
            </td>
            <td>
              <div class="cart-product-info">
                <img src="https://via.placeholder.com/60x60" alt="${item.name}" class="cart-product-image" />
                <div>
                  <div class="cart-product-name">${item.name}</div>
                  <div class="cart-product-stock ${item.stock_quantity > 0 ? 'in-stock' : 'out-of-stock'}">
                    ${item.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'}
                  </div>
                </div>
              </div>
            </td>
            <td>RM ${price.toFixed(2)}</td>
            <td>
              <div class="quantity-controls">
                <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, -1)" ${quantity <= 1 ? 'disabled' : ''}>-</button>
                <input type="number" class="cart-quantity" data-product-id="${item.product_id}" value="${quantity}" min="1" max="${item.stock_quantity}" onchange="updateCartQuantity(this)">
                <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, 1)" ${quantity >= item.stock_quantity ? 'disabled' : ''}>+</button>
              </div>
            </td>
            <td>RM ${itemTotal.toFixed(2)}</td>
            <td>
              <button class="btn btn-sm btn-danger remove-from-cart" data-product-id="${item.product_id}" onclick="removeFromCart(this)">
                🗑️
              </button>
            </td>
          </tr>
        `;
    });
    
    console.log('=== FINAL CALCULATION ===');
    console.log('Total:', total);
    console.log('Item count:', itemCount);
    
    cartTableHtml += `
      </tbody>
    </table>
  </div>
</div>
`;
    
    // Create two-column layout
    const summaryHtml = `
                <!-- Cart Summary Section -->
        <div class="cart-summary-bar">
            <div class="cart-summary-total">
                <span class="summary-label">Total (<span id="selected-items-count">0</span> item):</span>
                 <span class="summary-value" id="selected-items-total">RM 0.00</span>
            </div>
            
        </div>
    `;
    
    // Combine into two-column layout
    const twoColumnLayout = `
        <div class="cart-layout">
            <div class="cart-table-section">
                ${cartTableHtml}
            </div>
            <div class="cart-summary-section">
                ${summaryHtml}
            </div>
        </div>
    `;
    
    cartContents.innerHTML = twoColumnLayout;
    if (cartActions) cartActions.style.display = 'flex';
    if (cartItemCount) cartItemCount.textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
    if (cartTotal) cartTotal.textContent = `Total: RM ${total.toFixed(2)}`;
    
    // Update summary after rendering
    updateCartSummary();
}

function updateCartSummary() {
    const selectedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let selectedCount = 0;
    let selectedTotal = 0;
    
    console.log('Updating cart summary, selected checkboxes:', selectedCheckboxes.length);
    
    selectedCheckboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
        const quantity = parseInt(checkbox.getAttribute('data-quantity')) || 0;
        const itemTotal = price * quantity;
        
        console.log('Selected item calculation:', {
            price: price,
            quantity: quantity,
            itemTotal: itemTotal
        });
        
        selectedCount += quantity;
        selectedTotal += itemTotal;
    });
    
    console.log('Summary totals:', { selectedCount, selectedTotal });
    
    // Update summary display
    const selectedItemsCount = document.getElementById('selected-items-count');
    const selectedItemsTotal = document.getElementById('selected-items-total');
    
    if (selectedItemsCount) {
        selectedItemsCount.textContent = selectedCount;
    }
    if (selectedItemsTotal) {
        selectedItemsTotal.textContent = `RM ${selectedTotal.toFixed(2)}`;
    }
    
    // Update checkout button
    updateCheckoutButton();
}

function updateQuantity(productId, change) {
    const quantityInput = document.querySelector(`input[data-product-id="${productId}"].cart-quantity`);
    if (!quantityInput) return;
    
    const newQuantity = parseInt(quantityInput.value) + change;
    if (newQuantity >= 1) {
        quantityInput.value = newQuantity;
        updateCartQuantity(quantityInput);
    }
}

function toggleSelectAll(checkbox) {
    const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');
    itemCheckboxes.forEach(itemCheckbox => {
        itemCheckbox.checked = checkbox.checked;
    });
    updateCartSummary();
}

function updateCheckoutButton() {
    const selectedItems = document.querySelectorAll('.cart-item-checkbox:checked');
    const checkoutBtn = document.querySelector('.btn-success');
    
    if (selectedItems.length > 0) {
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = `Proceed to Checkout (${selectedItems.length} items)`;
    } else {
        checkoutBtn.disabled = true;
        checkoutBtn.textContent = 'Proceed to Checkout';
    }
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'clear_cart');
    
    fetch('/agrimarket-erd/v1/shop/cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart);
            loadCartContents();
            showNotification('Cart cleared successfully!', 'success');
        } else {
            showNotification(data.message || 'Failed to clear cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while clearing cart', 'error');
    });
}

function proceedToCheckout() {
    const selectedItems = document.querySelectorAll('.cart-item-checkbox:checked');
    if (selectedItems.length === 0) {
        showNotification('Please select at least one item to checkout', 'warning');
        return;
    }
    
    const selectedProductIds = Array.from(selectedItems).map(checkbox => checkbox.getAttribute('data-product-id'));
    
    // Store selected items in session storage for checkout page
    sessionStorage.setItem('checkoutItems', JSON.stringify(selectedProductIds));
    
    // Redirect to checkout page
    window.location.href = '/agrimarket-erd/v1/shop/checkout.php';
}

function updateCartBadge(cart) {
    const cartBadge = document.querySelector('.cart-badge');
    if (!cartBadge) return;
    
    let totalItems = 0;
    if (cart && Object.keys(cart).length > 0) {
        Object.values(cart).forEach(item => {
            totalItems += item.quantity || 0;
        });
    }
    
    if (totalItems > 0) {
        cartBadge.textContent = totalItems;
        cartBadge.style.display = 'block';
    } else {
        cartBadge.style.display = 'none';
    }
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