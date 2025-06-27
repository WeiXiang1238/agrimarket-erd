// Order functionality for AgriMarket Shop
document.addEventListener('DOMContentLoaded', function() {
    // Load order history on page load
    loadOrderHistory();
});

function loadOrderHistory() {
    const orderHistoryContents = document.getElementById('order-history-contents');
    if (!orderHistoryContents) return;
    
    // Show loading state
    orderHistoryContents.innerHTML = '<div class="loading">Loading order history...</div>';
    
    fetch('/agrimarket-erd/v1/shop/order.php?action=get_orders')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayOrderHistory(data.orders);
        } else {
            orderHistoryContents.innerHTML = '<p class="error">Failed to load order history</p>';
        }
    })
    .catch(error => {
        console.error('Error loading order history:', error);
        orderHistoryContents.innerHTML = '<p class="error">An error occurred while loading order history</p>';
    });
}

function displayOrderHistory(orders) {
    const orderHistoryContents = document.getElementById('order-history-contents');
    if (!orderHistoryContents) return;
    
    if (!orders || orders.length === 0) {
        orderHistoryContents.innerHTML = '<p class="empty-orders">No orders found</p>';
        return;
    }
    
    let html = '<div class="order-list">';
    
    orders.forEach(order => {
        const orderDate = new Date(order.order_date).toLocaleDateString();
        const statusClass = getStatusClass(order.status);
        
        html += `
            <div class="order-item">
                <div class="order-header">
                    <h4>Order #${order.order_number}</h4>
                    <span class="order-status ${statusClass}">${order.status}</span>
                </div>
                <div class="order-details">
                    <p><strong>Date:</strong> ${orderDate}</p>
                    <p><strong>Total:</strong> RM ${parseFloat(order.final_amount).toFixed(2)}</p>
                    <p><strong>Items:</strong> ${order.item_count || 0}</p>
                </div>
                <div class="order-actions">
                    <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(${order.order_id})">
                        View Details
                    </button>
                    ${order.status === 'delivered' ? `
                        <button class="btn btn-sm btn-success" onclick="reviewOrder(${order.order_id})">
                            Review
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    orderHistoryContents.innerHTML = html;
}

function getStatusClass(status) {
    switch(status) {
        case 'pending': return 'status-pending';
        case 'confirmed': return 'status-confirmed';
        case 'processing': return 'status-processing';
        case 'shipped': return 'status-shipped';
        case 'delivered': return 'status-delivered';
        case 'cancelled': return 'status-cancelled';
        case 'returned': return 'status-returned';
        default: return 'status-pending';
    }
}

function viewOrderDetails(orderId) {
    // Redirect to order details page
    window.location.href = `/agrimarket-erd/v1/shop/order.php?action=view&order_id=${orderId}`;
}

function reviewOrder(orderId) {
    // Redirect to review page
    window.location.href = `/agrimarket-erd/v1/shop/review.php?order_id=${orderId}`;
} 