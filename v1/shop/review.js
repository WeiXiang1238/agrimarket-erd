// Customer Review Management Script
let currentOrderItems = [];
let currentOrderId = null;
let currentVendorId = null;

// Initialize review functionality when page loads
document.addEventListener('DOMContentLoaded', function () {
    loadCustomerReviews();
    setupReviewEventListeners();
});

// Setup event listeners
function setupReviewEventListeners() {
    // Rating star interactions
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('rating-star')) {
            handleStarClick(e.target);
        }
    });

    // Star hover effects
    document.addEventListener('mouseover', function (e) {
        if (e.target.classList.contains('rating-star')) {
            handleStarHover(e.target);
        }
    });

    document.addEventListener('mouseout', function (e) {
        if (e.target.classList.contains('rating-star')) {
            clearStarHover(e.target);
        }
    });
}

// Load customer's reviews
async function loadCustomerReviews() {
    try {
        showLoading('reviews-contents');

        // Get customer's reviews
        const response = await fetch(`review.php?action=get_customer_reviews`);
        const data = await response.json();

        if (data.success) {
            displayCustomerReviews(data.reviews || []);
        } else {
            showError('reviews-contents', data.message || 'Failed to load reviews');
        }
    } catch (error) {
        console.error('Error loading reviews:', error);
        showError('reviews-contents', 'Error loading reviews');
    }
}

// Display customer reviews
function displayCustomerReviews(reviews) {
    const container = document.getElementById('reviews-contents');

    if (!reviews || reviews.length === 0) {
        container.innerHTML = `
            <div class="no-data">
                <i class="fas fa-star"></i>
                <h3>No Reviews Yet</h3>
                <p>You haven't written any reviews yet. Check your delivered orders to leave reviews!</p>
                <button class="btn btn-primary" onclick="loadDeliveredOrders()">
                    <i class="fas fa-clipboard-list"></i> View Orders to Review
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="reviews-header">
            <h3>My Reviews (${reviews.length})</h3>
            <button class="btn btn-primary" onclick="loadDeliveredOrders()">
                <i class="fas fa-plus"></i> Write New Review
            </button>
        </div>
        <div class="reviews-list">
            ${reviews.map(review => createReviewCard(review)).join('')}
        </div>
    `;
}

// Create review card HTML
function createReviewCard(review) {
    const type = review.vendor_review_id ? 'vendor' : 'product';
    const itemName = type === 'product' ? review.product_name : review.vendor_name;
    const rating = review.rating || 0;

    return `
        <div class="review-card">
            <div class="review-header">
                <div class="review-item-info">
                    <h4>${escapeHtml(itemName)}</h4>
                    <span class="review-type-badge">${type === 'product' ? 'Product' : 'Vendor'} Review</span>
                </div>
                <div class="review-rating">
                    ${generateStars(rating)}
                    <span class="rating-text">${rating}/5</span>
                </div>
            </div>
            
            ${review.title ? `<h5 class="review-title">${escapeHtml(review.title)}</h5>` : ''}
            
            <div class="review-content">
                <p>${escapeHtml(review.comment || 'No comment provided')}</p>
                
                ${review.pros ? `
                    <div class="pros-cons">
                        <div class="pros">
                            <h6><i class="fas fa-thumbs-up"></i> Pros</h6>
                            <p>${escapeHtml(review.pros)}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${review.cons ? `
                    <div class="pros-cons">
                        <div class="cons">
                            <h6><i class="fas fa-thumbs-down"></i> Cons</h6>
                            <p>${escapeHtml(review.cons)}</p>
                        </div>
                    </div>
                ` : ''}
            </div>
            
            <div class="review-meta">
                <span class="review-date">
                    <i class="fas fa-calendar"></i>
                    ${formatDate(review.created_at)}
                </span>
                <span class="review-status status-${review.is_approved ? 'approved' : 'pending'}">
                    <i class="fas fa-${review.is_approved ? 'check-circle' : 'clock'}"></i>
                    ${review.is_approved ? 'Approved' : 'Pending Approval'}
                </span>
                ${review.is_verified_purchase ? `
                    <span class="verified-badge">
                        <i class="fas fa-shield-alt"></i> Verified Purchase
                    </span>
                ` : ''}
            </div>
        </div>
    `;
}

// Load delivered orders for review
async function loadDeliveredOrders() {
    // Redirect to order management page with review intent
    window.location.href = '../order-management/?show_review_modal=true';
}

// Handle star rating clicks
function handleStarClick(star) {
    const rating = parseInt(star.dataset.rating);
    const container = star.closest('.stars');
    const stars = container.querySelectorAll('.rating-star');

    // Update visual stars
    stars.forEach((s, index) => {
        if (index < rating) {
            s.classList.remove('far');
            s.classList.add('fas');
            s.style.color = '#f59e0b';
        } else {
            s.classList.remove('fas');
            s.classList.add('far');
            s.style.color = '#d1d5db';
        }
    });

    // Store rating
    container.dataset.rating = rating;
}

// Handle star hover
function handleStarHover(star) {
    const rating = parseInt(star.dataset.rating);
    const container = star.closest('.stars');
    const stars = container.querySelectorAll('.rating-star');

    stars.forEach((s, index) => {
        if (index < rating) {
            s.style.color = '#fbbf24';
        }
    });
}

// Clear star hover
function clearStarHover(star) {
    const container = star.closest('.stars');
    const currentRating = parseInt(container.dataset.rating);
    const stars = container.querySelectorAll('.rating-star');

    stars.forEach((s, index) => {
        if (index < currentRating) {
            s.style.color = '#f59e0b';
        } else {
            s.style.color = '#d1d5db';
        }
    });
}

// Utility functions
function generateStars(rating, size = '') {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    let html = '';

    // Full stars
    for (let i = 0; i < fullStars; i++) {
        html += `<i class="fas fa-star ${size}"></i>`;
    }

    // Half star
    if (hasHalfStar) {
        html += `<i class="fas fa-star-half-alt ${size}"></i>`;
    }

    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        html += `<i class="far fa-star ${size}"></i>`;
    }

    return html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showLoading(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="loading-indicator">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading...</p>
            </div>
        `;
    }
}

function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${escapeHtml(message)}</p>
            </div>
        `;
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${escapeHtml(message)}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
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