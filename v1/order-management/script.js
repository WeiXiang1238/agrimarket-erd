// Order Management JavaScript

function filterOrders() {
    const statusFilter = document.getElementById('statusFilter');
    const status = statusFilter.value;
    const currentUrl = new URL(window.location);

    if (status) {
        currentUrl.searchParams.set('status', status);
    } else {
        currentUrl.searchParams.delete('status');
    }

    currentUrl.searchParams.set('page', '1'); // Reset to first page
    window.location.href = currentUrl.toString();
}

// Admin-specific functions
function toggleBulkActions() {
    const panel = document.getElementById('bulkActionsPanel');
    if (panel) {
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.order-select:checked');
    const countElement = document.getElementById('selectedCount');
    if (countElement) {
        countElement.textContent = `${checkboxes.length} orders selected`;
    }
}

function bulkUpdateStatus() {
    const selected = getSelectedOrders();
    if (selected.length === 0) {
        showNotification('Please select at least one order', 'warning');
        return;
    }

    // Populate selected orders list
    const ordersList = document.getElementById('selectedOrdersList');
    if (ordersList) {
        ordersList.innerHTML = `
            <h4>Selected Orders (${selected.length}):</h4>
            <div class="selected-orders">
                ${selected.map(id => `<span class="order-tag">#${id}</span>`).join('')}
            </div>
        `;
    }

    document.getElementById('bulkStatusModal').classList.add('show');
}

function closeBulkStatusModal() {
    document.getElementById('bulkStatusModal').classList.remove('show');
}

function bulkExport() {
    const selected = getSelectedOrders();
    if (selected.length === 0) {
        showNotification('Please select at least one order', 'warning');
        return;
    }
    exportOrders(selected);
}

function bulkGenerateReport() {
    const selected = getSelectedOrders();
    if (selected.length === 0) {
        showNotification('Please select at least one order', 'warning');
        return;
    }
    generateOrderReport(selected);
}

function exportOrders(orderIds = null) {
    showLoader();

    const params = new URLSearchParams();
    params.append('action', 'export_orders');
    if (orderIds && orderIds.length > 0) {
        params.append('order_ids', orderIds.join(','));
    }

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `orders_export_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showNotification('Orders exported successfully!', 'success');
        })
        .catch(error => {
            showNotification('Export failed', 'error');
            console.error('Export error:', error);
        })
        .finally(() => hideLoader());
}

function viewCustomerProfile(customerId) {
    if (!customerId || customerId === 0) {
        showNotification('Customer information not available', 'warning');
        return;
    }
    window.open(`/agrimarket-erd/v1/customer-management/?customer_id=${customerId}`, '_blank');
}

function viewVendorProfile(vendorId) {
    if (!vendorId || vendorId === 0) {
        showNotification('Vendor information not available', 'warning');
        return;
    }
    window.open(`/agrimarket-erd/v1/vendor-management/?vendor_id=${vendorId}`, '_blank');
}

function viewOrderAnalytics(orderId) {
    const modal = document.getElementById('orderAnalyticsModal');
    const content = document.getElementById('orderAnalyticsContent');

    modal.classList.add('show');
    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading analytics...</div>';

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_order_analytics&order_id=${orderId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderAnalytics(data.analytics);
            } else {
                content.innerHTML = `<div class="error">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="error">Failed to load analytics</div>';
            console.error('Analytics error:', error);
        });
}

function displayOrderAnalytics(analytics) {
    const content = document.getElementById('orderAnalyticsContent');
    content.innerHTML = `
        <div class="analytics-dashboard">
            <div class="analytics-section">
                <h3>Order Performance</h3>
                <div class="analytics-grid">
                    <div class="analytics-card">
                        <h4>Processing Time</h4>
                        <p>${analytics.processing_time || 'N/A'}</p>
                    </div>
                    <div class="analytics-card">
                        <h4>Customer Satisfaction</h4>
                        <p>${analytics.customer_rating || 'N/A'}</p>
                    </div>
                    <div class="analytics-card">
                        <h4>Vendor Response</h4>
                        <p>${analytics.vendor_response_time || 'N/A'}</p>
                    </div>
                </div>
            </div>
            
            <div class="analytics-section">
                <h3>Order Timeline</h3>
                <div class="timeline">
                    ${analytics.timeline ? analytics.timeline.map(event => `
                        <div class="timeline-event">
                            <div class="timeline-date">${event.date}</div>
                            <div class="timeline-content">${event.event}</div>
                        </div>
                    `).join('') : '<p>No timeline data available</p>'}
                </div>
            </div>
        </div>
    `;
}

function closeOrderAnalyticsModal() {
    document.getElementById('orderAnalyticsModal').classList.remove('show');
}

function adminCancelOrder(orderId) {
    document.getElementById('adminCancelOrderId').value = orderId;
    document.getElementById('adminCancelModal').classList.add('show');
}

function closeAdminCancelModal() {
    document.getElementById('adminCancelModal').classList.remove('show');
}

function generateOrderReport(orderIds) {
    showLoader();

    const params = new URLSearchParams();
    params.append('action', 'generate_report');
    params.append('order_ids', orderIds.join(','));

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Report generated successfully!', 'success');
                if (data.report_url) {
                    window.open(data.report_url, '_blank');
                }
            } else {
                showNotification('Report generation failed', 'error');
            }
        })
        .catch(error => {
            showNotification('Report generation failed', 'error');
            console.error('Report error:', error);
        })
        .finally(() => hideLoader());
}

function getSelectedOrders() {
    const checkboxes = document.querySelectorAll('.order-select:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const content = document.getElementById('orderDetailsContent');

    // Show modal and loading state
    modal.classList.add('show');
    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading order details...</div>';

    // Fetch order details
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_order_details&order_id=${orderId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order);
            } else {
                content.innerHTML = `<div class="error">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="error">Failed to load order details</div>';
            console.error('Error:', error);
        });
}

function displayOrderDetails(order) {
    const content = document.getElementById('orderDetailsContent');

    content.innerHTML = `
        <div class="order-detail-section">
            <h3>Order Information</h3>
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">#${order.order_id}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">${new Date(order.order_date).toLocaleDateString()}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status:</span>
                <span class="detail-value">
                    <span class="payment-status payment-${order.payment_status.toLowerCase()}">${order.payment_status}</span>
                </span>
            </div>
            ${order.tracking_number ? `
                <div class="detail-row">
                    <span class="detail-label">Tracking Number:</span>
                    <span class="detail-value">${order.tracking_number}</span>
                </div>
            ` : ''}
        </div>

        <div class="order-detail-section">
            <h3>Vendor Information</h3>
            <div class="detail-row">
                <span class="detail-label">Business Name:</span>
                <span class="detail-value">${order.vendor_name}</span>
            </div>
            ${order.vendor_email ? `
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">${order.vendor_email}</span>
                </div>
            ` : ''}
            ${order.vendor_phone ? `
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">${order.vendor_phone}</span>
                </div>
            ` : ''}
        </div>

        <div class="order-detail-section">
            <h3>Order Items</h3>
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>RM ${parseFloat(item.price_at_purchase).toFixed(2)}</td>
                            <td>RM ${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>

        <div class="order-detail-section">
            <h3>Order Summary</h3>
            <div class="detail-row">
                <span class="detail-label">Subtotal:</span>
                <span class="detail-value">RM ${parseFloat(order.total_amount).toFixed(2)}</span>
            </div>
            ${(order.shipping_fee || 0) > 0 ? `
                <div class="detail-row">
                    <span class="detail-label">Shipping:</span>
                    <span class="detail-value">RM ${parseFloat(order.shipping_fee || 0).toFixed(2)}</span>
                </div>
            ` : ''}
            ${order.tax_amount > 0 ? `
                <div class="detail-row">
                    <span class="detail-label">Tax:</span>
                    <span class="detail-value">RM ${parseFloat(order.tax_amount).toFixed(2)}</span>
                </div>
            ` : ''}
            <div class="detail-row" style="border-top: 2px solid var(--border-color); margin-top: 0.5rem; padding-top: 0.5rem;">
                <span class="detail-label"><strong>Total:</strong></span>
                <span class="detail-value"><strong>RM ${parseFloat(order.final_amount).toFixed(2)}</strong></span>
            </div>
        </div>
    `;
}

function closeOrderDetails() {
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.remove('show');
}

function trackOrder(orderId) {
    const modal = document.getElementById('trackingModal');
    const content = document.getElementById('trackingContent');

    // Show modal and loading state
    modal.classList.add('show');
    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading tracking information...</div>';

    // Fetch tracking data
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=track_order&order_id=${orderId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTracking(data.tracking);
            } else {
                content.innerHTML = `<div class="error">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="error">Failed to load tracking information</div>';
            console.error('Error:', error);
        });
}

function displayTracking(tracking) {
    const content = document.getElementById('trackingContent');

    content.innerHTML = `
        <div class="order-detail-section">
            <h3>Order Summary</h3>
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">#${tracking.order_id}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vendor:</span>
                <span class="detail-value">${tracking.vendor_name}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Items:</span>
                <span class="detail-value">${tracking.item_count} item(s)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value">RM ${parseFloat(tracking.final_amount).toFixed(2)}</span>
            </div>
            ${tracking.tracking_number ? `
                <div class="detail-row">
                    <span class="detail-label">Tracking Number:</span>
                    <span class="detail-value">${tracking.tracking_number}</span>
                </div>
            ` : ''}
        </div>

        <div class="order-detail-section">
            <h3>Tracking Timeline</h3>
            <div class="tracking-timeline">
                ${tracking.timeline.map(step => `
                    <div class="timeline-item">
                        <div class="timeline-marker ${step.completed ? 'completed' : (step.current ? 'current' : 'pending')}">
                            ${step.completed ? '<i class="fas fa-check"></i>' : (step.current ? '<i class="fas fa-clock"></i>' : '<i class="fas fa-circle"></i>')}
                        </div>
                        <div class="timeline-content">
                            <h4>${step.label}</h4>
                            <p>${step.status}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

function closeTracking() {
    const modal = document.getElementById('trackingModal');
    modal.classList.remove('show');
}

function cancelOrder(orderId) {
    const reason = prompt('Please provide a reason for cancellation (optional):') || 'Customer request';

    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }

    showLoader();

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=cancel_order&order_id=${orderId}&reason=${encodeURIComponent(reason)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload(); // Refresh to update order status
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error cancelling order', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

function reorderItems(orderId) {
    if (!confirm('Add all items from this order to your cart?')) {
        return;
    }

    showLoader();

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=reorder&order_id=${orderId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Optionally redirect to cart
                setTimeout(() => {
                    window.location.href = '/agrimarket-erd/v1/shopping-cart/';
                }, 2000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error processing reorder', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

function updateOrderStatus(orderId) {
    const modal = document.getElementById('updateStatusModal');
    const orderIdField = document.getElementById('updateOrderId');

    orderIdField.value = orderId;
    modal.classList.add('show');
}

function closeUpdateStatus() {
    const modal = document.getElementById('updateStatusModal');
    modal.classList.remove('show');
}

// Form submission for status update
document.addEventListener('DOMContentLoaded', function () {
    const updateForm = document.getElementById('updateStatusForm');
    if (updateForm) {
        updateForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'update_status');

            showLoader();

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeUpdateStatus();
                        location.reload(); // Refresh to show updated status
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error updating order status', 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    hideLoader();
                });
        });
    }

    // Admin bulk status form
    const bulkStatusForm = document.getElementById('bulkStatusForm');
    if (bulkStatusForm) {
        bulkStatusForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const selectedOrders = getSelectedOrders();

            if (selectedOrders.length === 0) {
                showNotification('No orders selected', 'warning');
                return;
            }

            formData.append('action', 'bulk_update_status');
            formData.append('order_ids', selectedOrders.join(','));

            showLoader();

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeBulkStatusModal();
                        location.reload();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error updating orders', 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    hideLoader();
                });
        });
    }

    // Admin cancel form
    const adminCancelForm = document.getElementById('adminCancelForm');
    if (adminCancelForm) {
        adminCancelForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'admin_cancel_order');

            showLoader();

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeAdminCancelModal();
                        location.reload();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error cancelling order', 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    hideLoader();
                });
        });
    }

    // Select all orders functionality
    const selectAllCheckbox = document.getElementById('selectAllOrders');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const orderCheckboxes = document.querySelectorAll('.order-select');
            orderCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
});

// Close modals when clicking outside
window.addEventListener('click', function (event) {
    const modals = ['orderDetailsModal', 'trackingModal', 'updateStatusModal'];

    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && event.target === modal) {
            modal.classList.remove('show');
        }
    });
});

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Loader Functions
function showLoader() {
    const loader = document.createElement('div');
    loader.className = 'loader-overlay';
    loader.innerHTML = `
        <div class="loader-spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
    `;
    loader.id = 'pageLoader';
    document.body.appendChild(loader);
}

function hideLoader() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.remove();
    }
}

// Add CSS for notification and loader if not already included
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        z-index: 1001;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
        border-left: 4px solid;
    }
    
    .notification.success { border-left-color: #10b981; }
    .notification.error { border-left-color: #ef4444; }
    .notification.info { border-left-color: #3b82f6; }
    
    .notification i {
        font-size: 1.25rem;
    }
    
    .notification.success i { color: #10b981; }
    .notification.error i { color: #ef4444; }
    .notification.info i { color: #3b82f6; }
    
    .notification button {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: #9ca3af;
        margin-left: auto;
    }
    
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1002;
    }
    
    .loader-spinner {
        color: white;
        font-size: 2rem;
    }
    
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
    
    .loading, .error {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
    
    .error {
        color: #ef4444;
    }
`;

document.head.appendChild(style); 