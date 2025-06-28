// Shopping Cart Functionality

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function () {
    // Close modals when clicking outside
    window.addEventListener('click', function (event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
    });

    // Escape key to close modals
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllModals();
        }
    });
});

function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.classList.remove('show');
    });
}

function updateQuantity(cartId, quantity) {
    if (quantity < 1) return;

    showLoader();

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_quantity&cart_id=${cartId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload(); // Refresh to update totals
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error updating quantity', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

function removeItem(cartId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    showLoader();

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_item&cart_id=${cartId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Remove the row from the table
                const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);
                if (row) {
                    row.remove();
                }
                // Refresh page to update totals
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error removing item', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }

    showLoader();

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear_cart'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error clearing cart', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

function addToComparison(productId) {
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_comparison&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Update comparison count in button
                updateComparisonCount(data.comparison_count);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error adding to comparison', 'error');
            console.error('Error:', error);
        });
}

function removeFromComparison(productId) {
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_from_comparison&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateComparisonCount(data.comparison_count);
                loadComparison(); // Refresh comparison modal if open
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error removing from comparison', 'error');
            console.error('Error:', error);
        });
}

function clearComparison() {
    if (!confirm('Are you sure you want to clear your comparison list?')) {
        return;
    }

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear_comparison'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateComparisonCount(0);
                loadComparison(); // Refresh comparison modal
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error clearing comparison', 'error');
            console.error('Error:', error);
        });
}

function updateComparisonCount(count) {
    const button = document.querySelector('button[onclick="showComparison()"]');
    if (button) {
        button.innerHTML = `<i class="fas fa-balance-scale"></i> Compare Products (${count})`;
    }
}

function showComparison() {
    const modal = document.getElementById('comparisonModal');
    modal.classList.add('show');
    loadComparison();
}

function closeComparison() {
    const modal = document.getElementById('comparisonModal');
    modal.classList.remove('show');
}

function loadComparison() {
    // Fetch comparison data and display
    fetch('/agrimarket-erd/v1/api/comparison.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComparison(data.comparison);
            } else {
                document.getElementById('comparisonContent').innerHTML =
                    '<p class="text-center">No products to compare</p>';
            }
        })
        .catch(error => {
            console.error('Error loading comparison:', error);
            document.getElementById('comparisonContent').innerHTML =
                '<p class="text-center text-error">Error loading comparison</p>';
        });
}

function displayComparison(comparison) {
    const content = document.getElementById('comparisonContent');

    if (!comparison.products || comparison.products.length === 0) {
        content.innerHTML = `
            <div class="empty-comparison">
                <i class="fas fa-balance-scale fa-3x"></i>
                <h3>No products to compare</h3>
                <p>Add products to your comparison list from the cart or product pages.</p>
            </div>
        `;
        return;
    }

    let html = `
        <div class="comparison-actions">
            <button class="btn btn-danger" onclick="clearComparison()">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
        <div class="comparison-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
    `;

    comparison.products.forEach(product => {
        html += `<th class="product-column">
            <div class="product-header">
                <img src="../../${product.image_path || 'uploads/products/default-product.png'}" alt="${product.name}">
                <h4>${product.name}</h4>
                <button class="btn-remove" onclick="removeFromComparison(${product.product_id})" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </th>`;
    });

    html += `
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Price</strong></td>
    `;

    comparison.products.forEach(product => {
        html += `<td class="price">RM ${parseFloat(product.selling_price).toFixed(2)}</td>`;
    });

    html += `
                    </tr>
                    <tr>
                        <td><strong>Vendor</strong></td>
    `;

    comparison.products.forEach(product => {
        html += `<td>${product.vendor_name || 'N/A'}</td>`;
    });

    html += `
                    </tr>
                    <tr>
                        <td><strong>Category</strong></td>
    `;

    comparison.products.forEach(product => {
        html += `<td>${product.category_name || product.category || 'N/A'}</td>`;
    });

    html += `
                    </tr>
                    <tr>
                        <td><strong>Stock</strong></td>
    `;

    comparison.products.forEach(product => {
        html += `<td>${product.stock_status || (product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock')}</td>`;
    });

    html += `
                    </tr>
                    <tr>
                        <td><strong>Rating</strong></td>
    `;

    comparison.products.forEach(product => {
        const rating = parseFloat(product.avg_rating || 0);
        html += `<td>
            <div class="rating">
                ${generateStars(rating)}
                <span>(${rating.toFixed(1)})</span>
            </div>
        </td>`;
    });

    html += `
                    </tr>
                    <tr>
                        <td><strong>Actions</strong></td>
    `;

    comparison.products.forEach(product => {
        html += `<td>
            <button class="btn btn-primary btn-sm" onclick="addToCartFromComparison(${product.product_id})">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </td>`;
    });

    html += `
                    </tr>
                </tbody>
            </table>
        </div>
    `;

    // Add comparison metrics if available
    if (comparison.comparison_metrics) {
        const metrics = comparison.comparison_metrics;
        html += `
            <div class="comparison-metrics">
                <h3>Comparison Summary</h3>
                <div class="metrics-grid">
                    <div class="metric">
                        <h4>Price Range</h4>
                        <p>RM ${metrics.price_range.min.toFixed(2)} - RM ${metrics.price_range.max.toFixed(2)}</p>
                    </div>
                    <div class="metric">
                        <h4>Best Value</h4>
                        <p>${metrics.best_value ? metrics.best_value.name : 'N/A'}</p>
                    </div>
                    <div class="metric">
                        <h4>Highest Rated</h4>
                        <p>${metrics.highest_rated ? metrics.highest_rated.name : 'N/A'}</p>
                    </div>
                    <div class="metric">
                        <h4>Lowest Price</h4>
                        <p>${metrics.lowest_price ? metrics.lowest_price.name : 'N/A'}</p>
                    </div>
                </div>
            </div>
        `;
    }

    content.innerHTML = html;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
        } else if (i - 0.5 <= rating) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        } else {
            stars += '<i class="far fa-star"></i>';
        }
    }
    return stars;
}

function addToCartFromComparison(productId) {
    // This would typically call an add to cart API
    showNotification('Product added to cart!', 'success');
}

function proceedToCheckout() {
    const modal = document.getElementById('checkoutModal');
    modal.classList.add('show');
}

function closeCheckout() {
    const modal = document.getElementById('checkoutModal');
    modal.classList.remove('show');
}

// Checkout form handling
document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault();
            processCheckout();
        });
    }

    // Close modals when clicking outside
    window.addEventListener('click', function (e) {
        const comparisonModal = document.getElementById('comparisonModal');
        const checkoutModal = document.getElementById('checkoutModal');

        if (e.target === comparisonModal) {
            closeComparison();
        }
        if (e.target === checkoutModal) {
            closeCheckout();
        }
    });
});

function processCheckout() {
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);

    showLoader();

    // Send the checkout data to the checkout handler
    fetch('checkout.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order placed successfully!', 'success');
                closeCheckout();
                // Redirect to order management page
                setTimeout(() => {
                    window.location.href = data.redirect || '/agrimarket-erd/v1/order-management/';
                }, 2000);
            } else {
                showNotification(data.message || 'Checkout failed', 'error');
            }
        })
        .catch(error => {
            showNotification('Error processing checkout', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            hideLoader();
        });
}

// Utility functions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function showLoader() {
    let loader = document.getElementById('loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'loader';
        loader.className = 'loader-overlay';
        loader.innerHTML = '<div class="loader-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
        document.body.appendChild(loader);
    }
    loader.style.display = 'flex';
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
    }
} 