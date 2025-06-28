// Review functionality for AgriMarket Shop
document.addEventListener('DOMContentLoaded', function () {
    // Load reviews on page load
    loadReviews();

    // Initialize review form submission
    initializeReviewForm();
});

function loadReviews() {
    const reviewsContents = document.getElementById('reviews-contents');
    if (!reviewsContents) return;

    // Show loading state
    reviewsContents.innerHTML = '<div class="loading">Loading reviews...</div>';

    fetch('/agrimarket-erd/v1/shop/review.php?action=get_reviews')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReviews(data.reviews);
            } else {
                reviewsContents.innerHTML = '<p class="error">Failed to load reviews</p>';
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            reviewsContents.innerHTML = '<p class="error">An error occurred while loading reviews</p>';
        });
}

function displayReviews(reviews) {
    const reviewsContents = document.getElementById('reviews-contents');
    if (!reviewsContents) return;

    if (!reviews || reviews.length === 0) {
        reviewsContents.innerHTML = '<p class="empty-reviews">No reviews found</p>';
        return;
    }

    let html = '<div class="review-list">';

    reviews.forEach(review => {
        const reviewDate = new Date(review.created_at).toLocaleDateString();
        const stars = generateStars(review.rating);

        html += `
            <div class="review-item">
                <div class="review-header">
                    <h4>${review.product_name || 'Product Review'}</h4>
                    <div class="review-rating">
                        ${stars}
                        <span class="rating-text">${review.rating}/5</span>
                    </div>
                </div>
                <div class="review-content">
                    ${review.title ? `<h5>${review.title}</h5>` : ''}
                    <p>${review.comment || 'No comment provided'}</p>
                    ${review.pros ? `<p><strong>Pros:</strong> ${review.pros}</p>` : ''}
                    ${review.cons ? `<p><strong>Cons:</strong> ${review.cons}</p>` : ''}
                </div>
                <div class="review-footer">
                    <span class="review-date">${reviewDate}</span>
                    <div class="review-actions">
                        <button class="btn btn-sm btn-primary" onclick="editReview(${review.review_id})">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteReview(${review.review_id})">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    reviewsContents.innerHTML = html;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-warning"></i>';
        } else {
            stars += '<i class="far fa-star text-muted"></i>';
        }
    }
    return stars;
}

function initializeReviewForm() {
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitReview(this);
        });
    }
}

function submitReview(form) {
    const formData = new FormData(form);
    formData.append('action', 'submit_review');

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    // Show loading state
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;

    fetch('/agrimarket-erd/v1/shop/review.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Review submitted successfully!', 'success');
                form.reset();
                loadReviews(); // Reload reviews
            } else {
                showNotification(data.message || 'Failed to submit review', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while submitting review', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
}

function editReview(reviewId) {
    // Redirect to edit review page
    window.location.href = `/agrimarket-erd/v1/shop/review.php?action=edit&review_id=${reviewId}`;
}

function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete this review?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_review');
    formData.append('review_id', reviewId);

    fetch('/agrimarket-erd/v1/shop/review.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Review deleted successfully!', 'success');
                loadReviews(); // Reload reviews
            } else {
                showNotification(data.message || 'Failed to delete review', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting review', 'error');
        });
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
    switch (type) {
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