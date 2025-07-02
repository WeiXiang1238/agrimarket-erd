// Inventory Management JavaScript

let currentPage = 1;
let totalPages = 1;
let productsData = [];
let categories = [];
let isEditing = false;

// Load inventory on page load
document.addEventListener('DOMContentLoaded', function () {
    loadInventory();
    loadCategories();

    // Add real-time search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadInventory();
            }, 500);
        });
    }

    // Add filter change listeners
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            currentPage = 1;
            loadInventory();
        });
    }

    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function () {
            currentPage = 1;
            loadInventory();
        });
    }
});

// Load inventory data
function loadInventory() {
    const tableContent = document.getElementById('inventoryTableContent');
    if (!tableContent) return;

    tableContent.innerHTML = `
        <div class="loading">
            <i class="fas fa-spinner fa-spin"></i>
            Loading inventory data...
        </div>
    `;

    const formData = new FormData();
    formData.append('action', 'get_products_for_inventory');
    formData.append('page', currentPage);
    formData.append('limit', document.getElementById('limitSelect')?.value || 10);
    formData.append('csrf_token', CSRF_TOKEN);

    // Add filters
    const search = document.getElementById('searchInput')?.value || '';
    const category = document.getElementById('categoryFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';

    if (search) formData.append('search', search);
    if (category) formData.append('category', category);
    if (status) formData.append('status', status);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productsData = data.products || [];
                totalPages = Math.ceil(data.total / (document.getElementById('limitSelect')?.value || 10));
                renderInventoryTable();
                renderPagination();
                populateProductSelects();
            } else {
                tableContent.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${data.message || 'Failed to load inventory data'}</p>
                </div>
            `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableContent.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Failed to load inventory data</p>
            </div>
        `;
        });
}

// Load categories for filters
function loadCategories() {
    const formData = new FormData();
    formData.append('action', 'get_categories');
    formData.append('csrf_token', CSRF_TOKEN);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                categories = data.categories || [];
                populateCategoryFilter();
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

// Render inventory table
function renderInventoryTable() {
    const tableContent = document.getElementById('inventoryTableContent');
    if (!tableContent) return;

    if (productsData.length === 0) {
        tableContent.innerHTML = `
            <div class="no-data">
                <i class="fas fa-box-open"></i>
                <p>No products found</p>
            </div>
        `;
        return;
    }

    let html = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    productsData.forEach(product => {
        const stockStatus = getStockStatus(product.stock_quantity);
        const stockBadgeClass = getStockBadgeClass(product.stock_quantity);

        html += `
            <tr>
                <td>
                    <div class="product-info">
                        <strong>${escapeHtml(product.name)}</strong>
                        <small>${escapeHtml(product.vendor_name || 'N/A')}</small>
                    </div>
                </td>
                <td>${escapeHtml(product.category_name || 'Uncategorized')}</td>
                <td>
                    <span class="stock-badge ${stockBadgeClass}">
                        ${product.stock_quantity}
                    </span>
                </td>
                <td>${stockStatus}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn restock" onclick="quickRestock(${product.product_id})" title="Restock">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="action-btn reduce" onclick="quickReduceStock(${product.product_id})" title="Reduce Stock">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button class="action-btn history" onclick="viewHistory(${product.product_id})" title="View History">
                            <i class="fas fa-history"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
        <div class="pagination" id="pagination"></div>
    `;

    tableContent.innerHTML = html;
    renderPagination();
}

// Render pagination
function renderPagination() {
    const pagination = document.getElementById('pagination');
    if (!pagination || totalPages <= 1) return;

    let html = '';

    // Previous button
    html += `<button onclick="changePage(${currentPage - 1})" ${currentPage <= 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i> Previous
    </button>`;

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changePage(${i})" ${i === currentPage ? 'class="active"' : ''}>${i}</button>`;
    }

    // Next button
    html += `<button onclick="changePage(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled' : ''}>
        Next <i class="fas fa-chevron-right"></i>
    </button>`;

    pagination.innerHTML = html;
}

// Change page
function changePage(page) {
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        loadInventory();
    }
}

// Populate category filter
function populateCategoryFilter() {
    const select = document.getElementById('categoryFilter');
    if (!select) return;

    let html = '<option value="">All Categories</option>';
    categories.forEach(category => {
        html += `<option value="${category.category_id}">${escapeHtml(category.name)}</option>`;
    });
    select.innerHTML = html;
}

// Populate product selects
function populateProductSelects() {
    const restockSelect = document.getElementById('restockProductId');
    const reduceSelect = document.getElementById('reduceProductId');
    const bulkSelect = document.getElementById('bulkProductSelect');

    let html = '<option value="">Select a product</option>';
    productsData.forEach(product => {
        html += `<option value="${product.product_id}" data-stock="${product.stock_quantity}">${escapeHtml(product.name)}</option>`;
    });

    if (restockSelect) {
        restockSelect.innerHTML = html;
        // Remove existing listeners and add new one for restock select
        if (restockSelect._changeHandler) {
            restockSelect.removeEventListener('change', restockSelect._changeHandler);
        }
        restockSelect._changeHandler = function () {
            updateCurrentStock('restock');
        };
        restockSelect.addEventListener('change', restockSelect._changeHandler);
    }

    if (reduceSelect) {
        reduceSelect.innerHTML = html;
        // Remove existing listeners and add new one for reduce select
        if (reduceSelect._changeHandler) {
            reduceSelect.removeEventListener('change', reduceSelect._changeHandler);
        }
        reduceSelect._changeHandler = function () {
            updateCurrentStock('reduce');
        };
        reduceSelect.addEventListener('change', reduceSelect._changeHandler);
    }

    if (bulkSelect) bulkSelect.innerHTML = html;
}

// Update current stock display
function updateCurrentStock(type) {
    const select = document.getElementById(type === 'restock' ? 'restockProductId' : 'reduceProductId');
    const stockInput = document.getElementById(type === 'restock' ? 'currentStock' : 'reduceCurrentStock');

    console.log(`updateCurrentStock called for type: ${type}`);
    console.log('Select element:', select);
    console.log('Stock input element:', stockInput);

    if (select && stockInput) {
        const selectedOption = select.options[select.selectedIndex];
        console.log('Selected option:', selectedOption);

        if (selectedOption) {
            const stock = selectedOption.getAttribute('data-stock') || '0';
            console.log('Stock value from data-stock:', stock);
            stockInput.value = stock;
            console.log('Stock input value set to:', stockInput.value);
        } else {
            console.log('No option selected');
            stockInput.value = '0';
        }
    } else {
        console.log('Select or stock input element not found');
    }
}

// Get stock status
function getStockStatus(stock) {
    if (stock <= 0) return 'Out of Stock';
    if (stock <= 10) return 'Low Stock';
    return 'In Stock';
}

// Get stock badge class
function getStockBadgeClass(stock) {
    if (stock <= 0) return 'out-of-stock';
    if (stock <= 10) return 'low-stock';
    return 'in-stock';
}

// Modal functions
function openRestockModal() {
    document.getElementById('restockModal').style.display = 'block';
    document.getElementById('restockForm').reset();
    populateProductSelects();
}

function closeRestockModal() {
    document.getElementById('restockModal').style.display = 'none';
}

function openReduceStockModal() {
    document.getElementById('reduceStockModal').style.display = 'block';
    document.getElementById('reduceStockForm').reset();
    populateProductSelects();
}

function closeReduceStockModal() {
    document.getElementById('reduceStockModal').style.display = 'none';
}

function openBulkRestockModal() {
    document.getElementById('bulkRestockModal').style.display = 'block';
    document.getElementById('bulkProductsList').innerHTML = '';
    populateProductSelects();
}

function closeBulkRestockModal() {
    document.getElementById('bulkRestockModal').style.display = 'none';
}

// Submit restock
function submitRestock() {
    const form = document.getElementById('restockForm');
    const formData = new FormData(form);

    if (!formData.get('product_id')) {
        showNotification('Please select a product', 'error');
        return;
    }

    if (!formData.get('quantity') || formData.get('quantity') <= 0) {
        showNotification('Please enter a valid quantity', 'error');
        return;
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text().then(text => {
                console.log('Raw response:', text);
                if (!text) {
                    throw new Error('Empty response from server');
                }
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + text);
                }
            });
        })
        .then(data => {
            console.log('Parsed data:', data);
            if (data.success) {
                showNotification('Product restocked successfully', 'success');
                closeRestockModal();
                loadInventory();
                refreshNotifications(); // Refresh notifications after restock
            } else {
                showNotification(data.message || 'Failed to restock product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to restock product: ' + error.message, 'error');
        });
}

// Submit reduce stock
function submitReduceStock() {
    const form = document.getElementById('reduceStockForm');
    const formData = new FormData(form);

    if (!formData.get('product_id')) {
        showNotification('Please select a product', 'error');
        return;
    }

    if (!formData.get('quantity') || formData.get('quantity') <= 0) {
        showNotification('Please enter a valid quantity', 'error');
        return;
    }

    if (!formData.get('reason')) {
        showNotification('Please select a reason', 'error');
        return;
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Stock reduced successfully', 'success');
                closeReduceStockModal();
                loadInventory();
                refreshNotifications(); // Refresh notifications after stock reduction
            } else {
                showNotification(data.message || 'Failed to reduce stock', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to reduce stock', 'error');
        });
}

// Quick restock
function quickRestock(productId) {
    const product = productsData.find(p => p.product_id == productId);
    if (!product) return;

    openRestockModal();
    document.getElementById('restockProductId').value = productId;
    document.getElementById('currentStock').value = product.stock_quantity;
    document.getElementById('restockQuantity').focus();
}

// Quick reduce stock
function quickReduceStock(productId) {
    const product = productsData.find(p => p.product_id == productId);
    if (!product) return;

    openReduceStockModal();
    document.getElementById('reduceProductId').value = productId;
    document.getElementById('reduceCurrentStock').value = product.stock_quantity;
    document.getElementById('reduceQuantity').focus();
}

// View history
function viewHistory(productId) {
    const product = productsData.find(p => p.product_id == productId);
    if (!product) return;

    const formData = new FormData();
    formData.append('action', 'get_inventory_history');
    formData.append('product_id', productId);
    formData.append('csrf_token', CSRF_TOKEN);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showHistoryModal(product, data.history);
            } else {
                showNotification(data.message || 'Failed to load history', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load history', 'error');
        });
}

// Show history modal
function showHistoryModal(product, history) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'block';

    let historyHtml = '';
    if (history && history.length > 0) {
        history.forEach(item => {
            historyHtml += `
                <div class="history-item ${item.action_type}">
                    <div class="history-header">
                        <span class="history-action">${item.action_type === 'restock' ? 'Restock' : 'Reduce Stock'}</span>
                        <span class="history-date">${formatDate(item.created_at)}</span>
                    </div>
                    <div class="history-details">
                        Quantity: ${item.quantity} | Reason: ${item.reason || 'N/A'}
                    </div>
                    <div class="history-user">
                        By: ${item.user_name || 'System'}
                    </div>
                </div>
            `;
        });
    } else {
        historyHtml = '<p>No history available for this product.</p>';
    }

    modal.innerHTML = `
        <div class="modal-content large">
            <div class="modal-header">
                <h3><i class="fas fa-history"></i> Inventory History - ${escapeHtml(product.name)}</h3>
                <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
            </div>
            <div class="modal-body">
                ${historyHtml}
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

// Add bulk product
function addBulkProduct() {
    const select = document.getElementById('bulkProductSelect');
    const productId = select.value;

    console.log('Adding bulk product, selected ID:', productId);

    if (!productId) {
        showNotification('Please select a product', 'error');
        return;
    }

    const product = productsData.find(p => p.product_id == productId);
    if (!product) {
        console.error('Product not found in productsData:', productId);
        showNotification('Product not found', 'error');
        return;
    }

    console.log('Found product:', product.name);

    const list = document.getElementById('bulkProductsList');
    const existingItem = list.querySelector(`[data-product-id="${productId}"]`);

    if (existingItem) {
        showNotification('Product already added to bulk restock', 'error');
        return;
    }

    const item = document.createElement('div');
    item.className = 'bulk-product-item';
    item.setAttribute('data-product-id', productId);

    item.innerHTML = `
        <div class="bulk-product-header">
            <span class="bulk-product-name">${escapeHtml(product.name)}</span>
            <button class="remove-bulk-product" onclick="removeBulkProduct(${productId})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bulk-product-fields">
            <div class="form-group">
                <label>Current Stock</label>
                <input type="number" value="${product.stock_quantity}" readonly>
            </div>
            <div class="form-group">
                <label>Quantity to Add *</label>
                <input type="number" name="bulk_quantity_${productId}" min="1" required>
            </div>
        </div>
    `;

    list.appendChild(item);
    select.value = '';

    console.log('Bulk product added successfully. Total items in list:', list.children.length);
}

// Remove bulk product
function removeBulkProduct(productId) {
    const item = document.querySelector(`[data-product-id="${productId}"]`);
    if (item) {
        item.remove();
    }
}

// Clear bulk products
function clearBulkProducts() {
    document.getElementById('bulkProductsList').innerHTML = '';
}

// Submit bulk restock
function submitBulkRestock() {
    const products = document.querySelectorAll('.bulk-product-item');
    console.log('Bulk restock products found:', products.length);

    if (products.length === 0) {
        showNotification('Please add products to bulk restock', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'bulk_restock');
    formData.append('csrf_token', CSRF_TOKEN);
    formData.append('reason', document.getElementById('bulkRestockReason')?.value || 'Bulk restock');

    const bulkData = [];
    let hasError = false;

    products.forEach((product, index) => {
        const productId = product.getAttribute('data-product-id');
        const quantityInput = product.querySelector(`input[name="bulk_quantity_${productId}"]`);
        const quantity = quantityInput.value;

        console.log(`Product ${index + 1}: ID=${productId}, Quantity=${quantity}`);

        if (!quantity || quantity <= 0) {
            showNotification('Please enter valid quantities for all products', 'error');
            hasError = true;
            return;
        }

        bulkData.push({
            product_id: productId,
            quantity: parseInt(quantity)
        });
    });

    if (hasError) return;

    console.log('Bulk data to send:', bulkData);
    formData.append('products', JSON.stringify(bulkData));

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text().then(text => {
                console.log('Raw response:', text);
                if (!text) {
                    throw new Error('Empty response from server');
                }
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + text);
                }
            });
        })
        .then(data => {
            console.log('Parsed bulk restock response:', data);
            if (data.success) {
                showNotification('Bulk restock completed successfully', 'success');
                closeBulkRestockModal();
                loadInventory();
                refreshNotifications(); // Refresh notifications after bulk restock
            } else {
                showNotification(data.message || 'Failed to complete bulk restock', 'error');
            }
        })
        .catch(error => {
            console.error('Bulk restock error:', error);
            showNotification('Failed to complete bulk restock: ' + error.message, 'error');
        });
}

// Refresh data
function refreshData() {
    loadInventory();
    refreshNotifications();
    showNotification('Data refreshed', 'success');
}

// Refresh notifications
function refreshNotifications() {
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=refresh_notifications&csrf_token=' + encodeURIComponent(CSRF_TOKEN)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification badge
                const badge = document.querySelector('.notification-badge');
                if (data.unreadCount > 0) {
                    if (badge) {
                        badge.textContent = data.unreadCount;
                    } else {
                        // Create badge if it doesn't exist
                        const notifBtn = document.getElementById('notificationBtn');
                        if (notifBtn) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.textContent = data.unreadCount;
                            notifBtn.appendChild(newBadge);
                        }
                    }
                } else if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing notifications:', error);
        });
}

// Show low stock products
function showLowStockProducts() {
    document.getElementById('statusFilter').value = 'low_stock';
    currentPage = 1;
    loadInventory();
}

// Show out of stock products
function showOutOfStockProducts() {
    document.getElementById('statusFilter').value = 'out_of_stock';
    currentPage = 1;
    loadInventory();
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 400px;
        animation: slideInRight 0.3s ease-out;
    `;

    // Add keyframe animation
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex: 1;
            }
            .notification-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 0.25rem;
                border-radius: 0.25rem;
                transition: background-color 0.2s ease;
            }
            .notification-close:hover {
                background: rgba(255, 255, 255, 0.2);
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
} 