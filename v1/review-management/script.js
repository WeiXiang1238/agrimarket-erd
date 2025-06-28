// Review Management System JavaScript

// Global variables
let currentReviewType = 'product';
let currentPage = 1;
let currentLimit = 10;
let currentFilters = {
    search: '',
    status: '',
    rating: ''
};
let selectedReviews = new Set();

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    initializeEventListeners();
    loadReviews();
    updateStats();
});

// Event listeners setup
function initializeEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            currentFilters.search = this.value;
            currentPage = 1;
            loadReviews();
        }, 300));
    }

    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function () {
            currentFilters.status = this.value;
            currentPage = 1;
            loadReviews();
        });
    }

    const ratingFilter = document.getElementById('ratingFilter');
    if (ratingFilter) {
        ratingFilter.addEventListener('change', function () {
            currentFilters.rating = this.value;
            currentPage = 1;
            loadReviews();
        });
    }

    // Limit selector
    const limitSelect = document.getElementById('limitSelect');
    if (limitSelect) {
        limitSelect.addEventListener('change', function () {
            currentLimit = parseInt(this.value);
            currentPage = 1;
            loadReviews();
        });
    }

    // Select all functionality
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
            reviewCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    selectedReviews.add(checkbox.dataset.reviewId);
                } else {
                    selectedReviews.delete(checkbox.dataset.reviewId);
                }
            });
            updateBulkActions();
        });
    }

    // Form submissions
    const bulkModerationForm = document.getElementById('bulkModerationForm');
    if (bulkModerationForm) {
        bulkModerationForm.addEventListener('submit', handleBulkModeration);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function (event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
}

// Switch between product and vendor reviews
function switchReviewType(type) {
    currentReviewType = type;
    currentPage = 1;
    selectedReviews.clear();

    // Update UI
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-type="${type}"]`).classList.add('active');

    const reviewsTitle = document.getElementById('reviewsTitle');
    if (reviewsTitle) {
        reviewsTitle.textContent = type === 'product' ? 'Product Reviews' : 'Vendor Reviews';
    }

    loadReviews();
    updateBulkActions();
}

// Load reviews from server
function loadReviews() {
    showLoading();

    const params = new URLSearchParams({
        action: 'get_reviews',
        type: currentReviewType,
        page: currentPage,
        limit: currentLimit,
        ...currentFilters
    });

    fetch(`?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReviews(data.reviews);
                displayPagination(data.page, data.totalPages, data.total);
            } else {
                showError('Failed to load reviews: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            showError('Failed to load reviews. Please try again.');
        })
        .finally(() => {
            hideLoading();
        });
}

// Display reviews in table
function displayReviews(reviews) {
    const tableBody = document.getElementById('reviewsTableBody');
    const table = document.getElementById('reviewsTable');
    const noReviews = document.getElementById('noReviews');

    if (!reviews || reviews.length === 0) {
        table.style.display = 'none';
        noReviews.style.display = 'block';
        return;
    }

    table.style.display = 'table';
    noReviews.style.display = 'none';

    tableBody.innerHTML = reviews.map(review => `
        <tr>
            ${canModerate() ? `
            <td>
                <input type="checkbox" class="review-checkbox" 
                       data-review-id="${review.review_id}" 
                       data-review-type="${review.review_type}"
                       onchange="handleReviewSelection(this)">
            </td>
            ` : ''}
            <td>
                <div class="customer-info">
                    <strong>${escapeHtml(review.customer_name || 'Unknown')}</strong>
                    ${review.is_verified_purchase ? '<span class="verified-badge"><i class="fas fa-shield-alt"></i></span>' : ''}
                </div>
            </td>
            <td>
                <div class="entity-info">
                    ${currentReviewType === 'product' ?
            `<strong>${escapeHtml(review.product_name || 'Unknown Product')}</strong><br>
                         <small class="text-muted">by ${escapeHtml(review.vendor_name || 'Unknown Vendor')}</small>` :
            `<strong>${escapeHtml(review.vendor_name || 'Unknown Vendor')}</strong>`
        }
                </div>
            </td>
            <td>
                <div class="rating-display">
                    ${generateStars(review.rating)}
                    <span class="rating-number">${review.rating}/5</span>
                </div>
            </td>
            <td>
                <div class="review-title" title="${escapeHtml(review.title || '')}">
                    ${truncateText(review.title || 'No title', 50)}
                </div>
            </td>
            <td>
                <span class="status-badge status-${getReviewStatus(review).toLowerCase()}">
                    ${getReviewStatus(review)}
                </span>
            </td>
            <td>
                ${review.is_verified_purchase ?
            '<span class="badge badge-success"><i class="fas fa-check"></i> Verified</span>' :
            '<span class="badge badge-secondary">Unverified</span>'
        }
            </td>
            <td>
                <span title="${formatDateTime(review.created_at)}">
                    ${formatDate(review.created_at)}
                </span>
            </td>
            <td>
                <div class="actions">
                    <button class="btn-icon btn-primary" onclick="viewReviewDetails(${review.review_id}, '${review.review_type}')" 
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${canModerate() && getReviewStatus(review) !== 'Approved' ? `
                    <button class="btn-icon btn-success" onclick="moderateReview(${review.review_id}, '${review.review_type}', 'approve')" 
                            title="Approve">
                        <i class="fas fa-check"></i>
                    </button>
                    ${getReviewStatus(review) === 'Pending' ? `
                    <button class="btn-icon btn-warning" onclick="moderateReview(${review.review_id}, '${review.review_type}', 'reject')" 
                            title="Reject">
                        <i class="fas fa-times"></i>
                    </button>
                    ` : ''}
                    ` : ''}
                    ${canDelete() ? `
                    <button class="btn-icon btn-danger" onclick="deleteReview(${review.review_id}, '${review.review_type}')" 
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

// Handle individual review selection
function handleReviewSelection(checkbox) {
    const reviewId = checkbox.dataset.reviewId;

    if (checkbox.checked) {
        selectedReviews.add(reviewId);
    } else {
        selectedReviews.delete(reviewId);
        document.getElementById('selectAll').checked = false;
    }

    updateBulkActions();
}

// Update bulk actions display
function updateBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (!bulkActions || !selectedCount) return;

    const count = selectedReviews.size;
    if (count > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = `${count} review${count !== 1 ? 's' : ''} selected`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// View review details
function viewReviewDetails(reviewId, type) {
    fetch(`?action=get_review_details&review_id=${reviewId}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReviewDetails(data.review);
                document.getElementById('reviewDetailsModal').style.display = 'block';
            } else {
                showError('Failed to load review details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading review details:', error);
            showError('Failed to load review details. Please try again.');
        });
}

// Display review details in modal
function displayReviewDetails(review) {
    const content = document.getElementById('reviewDetailsContent');
    const reviewType = review.review_type || currentReviewType;

    content.innerHTML = `
        <div class="review-details">
            <div class="review-header">
                <div class="review-meta">
                    <div class="rating-large">
                        ${generateStars(review.rating, 'large')}
                        <span class="rating-number-large">${review.rating}/5</span>
                    </div>
                    <div class="review-info">
                        <h4>${escapeHtml(review.title || 'No title')}</h4>
                        <p class="review-author">
                            by <strong>${escapeHtml(review.customer_name || 'Unknown Customer')}</strong>
                            ${review.is_verified_purchase ? '<span class="verified-badge"><i class="fas fa-shield-alt"></i> Verified Purchase</span>' : ''}
                        </p>
                        <p class="review-date">Reviewed on ${formatDateTime(review.created_at)}</p>
                    </div>
                </div>
                
                <div class="review-status">
                    <span class="status-badge status-${getReviewStatus(review).toLowerCase()}">
                        ${getReviewStatusText(review)}
                    </span>
                    ${review.approved_by_name && review.approved_at ?
            `<p class="approval-info">${getReviewStatus(review)} by ${escapeHtml(review.approved_by_name)} on ${formatDateTime(review.approved_at)}</p>` : ''
        }
                </div>
            </div>

            ${reviewType === 'product' ? `
            <div class="reviewed-item">
                <h5><i class="fas fa-box"></i> Product</h5>
                <div class="item-info">
                    <strong>${escapeHtml(review.product_name || 'Unknown Product')}</strong>
                    <br><small class="text-muted">Sold by ${escapeHtml(review.vendor_name || 'Unknown Vendor')}</small>
                </div>
            </div>
            ` : `
            <div class="reviewed-item">
                <h5><i class="fas fa-store"></i> Vendor</h5>
                <div class="item-info">
                    <strong>${escapeHtml(review.vendor_name || 'Unknown Vendor')}</strong>
                </div>
            </div>
            `}

            ${review.order_number ? `
            <div class="order-info">
                <h5><i class="fas fa-receipt"></i> Order</h5>
                <p>Order #${escapeHtml(review.order_number)}</p>
            </div>
            ` : ''}

            <div class="review-content">
                <h5><i class="fas fa-comment"></i> Review</h5>
                <div class="review-text">
                    ${review.comment ? `<p>${escapeHtml(review.comment).replace(/\n/g, '<br>')}</p>` : '<p class="text-muted">No comment provided</p>'}
                </div>
                
                ${review.pros ? `
                <div class="pros-cons">
                    <h6><i class="fas fa-thumbs-up text-success"></i> Pros</h6>
                    <p>${escapeHtml(review.pros).replace(/\n/g, '<br>')}</p>
                </div>
                ` : ''}
                
                ${review.cons ? `
                <div class="pros-cons">
                    <h6><i class="fas fa-thumbs-down text-danger"></i> Cons</h6>
                    <p>${escapeHtml(review.cons).replace(/\n/g, '<br>')}</p>
                </div>
                ` : ''}
            </div>

            ${canModerate() ? `
            <div class="moderation-actions">
                <h5><i class="fas fa-gavel"></i> Moderation Actions</h5>
                <div class="action-buttons">
                    ${getReviewStatus(review) === 'Pending' ? `
                    <button class="btn btn-success" onclick="moderateReview(${review.review_id || review.vendor_review_id}, '${reviewType}', 'approve'); closeReviewDetails();">
                        <i class="fas fa-check"></i> Approve Review
                    </button>
                    <button class="btn btn-warning" onclick="moderateReview(${review.review_id || review.vendor_review_id}, '${reviewType}', 'reject'); closeReviewDetails();">
                        <i class="fas fa-times"></i> Reject Review
                    </button>
                    ` : getReviewStatus(review) === 'Rejected' ? `
                    <button class="btn btn-success" onclick="moderateReview(${review.review_id || review.vendor_review_id}, '${reviewType}', 'approve'); closeReviewDetails();">
                        <i class="fas fa-check"></i> Approve Review
                    </button>
                    ` : `
                    <button class="btn btn-warning" onclick="moderateReview(${review.review_id || review.vendor_review_id}, '${reviewType}', 'reject'); closeReviewDetails();">
                        <i class="fas fa-undo"></i> Unapprove Review
                    </button>
                    `}
                    ${canDelete() ? `
                    <button class="btn btn-danger" onclick="deleteReview(${review.review_id || review.vendor_review_id}, '${reviewType}'); closeReviewDetails();">
                        <i class="fas fa-trash"></i> Delete Review
                    </button>
                    ` : ''}
                </div>
            </div>
            ` : ''}
        </div>
    `;
}

// Moderate individual review
function moderateReview(reviewId, type, action) {
    if (!canModerate()) {
        showError('You do not have permission to moderate reviews');
        return;
    }

    const actionText = action === 'approve' ? 'approve' : 'reject';
    if (!confirm(`Are you sure you want to ${actionText} this review?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'moderate_review');
    formData.append('review_id', reviewId);
    formData.append('type', type);
    formData.append('moderate_action', action);

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(`Review ${actionText}d successfully`);
                loadReviews();
                updateStats();
            } else {
                showError('Failed to moderate review: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error moderating review:', error);
            showError('Failed to moderate review. Please try again.');
        });
}

// Bulk moderation
function bulkModerate(action) {
    if (selectedReviews.size === 0) {
        showError('Please select reviews to moderate');
        return;
    }

    const actionText = action === 'approve' ? 'approve' : 'reject';
    if (!confirm(`Are you sure you want to ${actionText} ${selectedReviews.size} review(s)?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'bulk_moderate');
    formData.append('type', currentReviewType);
    formData.append('moderate_action', action);

    selectedReviews.forEach(reviewId => {
        formData.append('review_ids[]', reviewId);
    });

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || `Reviews ${actionText}d successfully`);
                selectedReviews.clear();
                loadReviews();
                updateStats();
                updateBulkActions();
            } else {
                showError('Failed to moderate reviews: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error in bulk moderation:', error);
            showError('Failed to moderate reviews. Please try again.');
        });
}

// Delete review
function deleteReview(reviewId, type) {
    if (!canDelete()) {
        showError('You do not have permission to delete reviews');
        return;
    }

    if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_review');
    formData.append('review_id', reviewId);
    formData.append('type', type);

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Review deleted successfully');
                loadReviews();
                updateStats();
            } else {
                showError('Failed to delete review: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error deleting review:', error);
            showError('Failed to delete review. Please try again.');
        });
}

// Update statistics
function updateStats() {
    fetch('?action=get_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.stats;
                updateStatElement('totalReviews', stats.total_reviews);
                updateStatElement('pendingReviews', stats.pending_reviews);
                updateStatElement('approvedReviews', stats.approved_reviews);
                updateStatElement('averageRating', parseFloat(stats.average_rating).toFixed(1));
                updateStatElement('verifiedReviews', stats.verified_reviews);
                updateStatElement('recentReviews', stats.reviews_this_month);
            }
        })
        .catch(error => {
            console.error('Error updating statistics:', error);
        });
}

// Quick action functions
function showPendingReviews() {
    document.getElementById('statusFilter').value = 'pending';
    currentFilters.status = 'pending';
    currentPage = 1;
    loadReviews();
}

function openBulkModerationModal() {
    if (selectedReviews.size === 0) {
        showError('Please select reviews to moderate');
        return;
    }
    document.getElementById('bulkModerationModal').style.display = 'block';
}

function closeBulkModerationModal() {
    document.getElementById('bulkModerationModal').style.display = 'none';
}

function closeReviewDetails() {
    document.getElementById('reviewDetailsModal').style.display = 'none';
}

// Handle bulk moderation form submission
function handleBulkModeration(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const action = formData.get('bulk_action');

    if (!action) {
        showError('Please select an action');
        return;
    }

    bulkModerate(action);
    closeBulkModerationModal();
}

// Pagination functions
function displayPagination(currentPage, totalPages, totalItems) {
    const pagination = document.getElementById('reviewsPagination');
    if (!pagination || totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '<div class="pagination-info">';
    paginationHTML += `<span>Showing page ${currentPage} of ${totalPages} (${totalItems} total reviews)</span>`;
    paginationHTML += '</div><div class="pagination-controls">';

    // Previous button
    if (currentPage > 1) {
        paginationHTML += `<button class="btn btn-secondary" onclick="changePage(${currentPage - 1})">
            <i class="fas fa-chevron-left"></i> Previous
        </button>`;
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        paginationHTML += `<button class="btn btn-secondary" onclick="changePage(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += '<span class="pagination-ellipsis">...</span>';
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" 
                          onclick="changePage(${i})">${i}</button>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += '<span class="pagination-ellipsis">...</span>';
        }
        paginationHTML += `<button class="btn btn-secondary" onclick="changePage(${totalPages})">${totalPages}</button>`;
    }

    // Next button
    if (currentPage < totalPages) {
        paginationHTML += `<button class="btn btn-secondary" onclick="changePage(${currentPage + 1})">
            Next <i class="fas fa-chevron-right"></i>
        </button>`;
    }

    paginationHTML += '</div>';
    pagination.innerHTML = paginationHTML;
}

function changePage(page) {
    currentPage = page;
    selectedReviews.clear();
    updateBulkActions();
    loadReviews();
}

// Utility functions
function generateStars(rating, size = 'normal') {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    const sizeClass = size === 'large' ? 'star-large' : '';

    let stars = '';

    // Full stars
    for (let i = 0; i < fullStars; i++) {
        stars += `<i class="fas fa-star ${sizeClass}"></i>`;
    }

    // Half star
    if (hasHalfStar) {
        stars += `<i class="fas fa-star-half-alt ${sizeClass}"></i>`;
    }

    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        stars += `<i class="far fa-star ${sizeClass}"></i>`;
    }

    return stars;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString();
}

function updateStatElement(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

function canModerate() {
    // This should be set based on user roles from PHP
    return window.userCanModerate !== false;
}

function canDelete() {
    // This should be set based on user roles from PHP
    return window.userCanDelete !== false;
}

function showLoading() {
    const loading = document.getElementById('reviewsLoading');
    const table = document.getElementById('reviewsTable');
    const noReviews = document.getElementById('noReviews');

    if (loading) loading.style.display = 'block';
    if (table) table.style.display = 'none';
    if (noReviews) noReviews.style.display = 'none';
}

function hideLoading() {
    const loading = document.getElementById('reviewsLoading');
    if (loading) loading.style.display = 'none';
}

function showSuccess(message) {
    // Implementation depends on your notification system
    alert(message); // Simple fallback
}

function showError(message) {
    // Implementation depends on your notification system
    alert('Error: ' + message); // Simple fallback
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Helper function to determine review status
function getReviewStatus(review) {
    if (review.is_approved == 1) {
        return 'Approved';
    } else if (review.approved_at && review.approved_at !== null && review.approved_at !== '') {
        return 'Rejected';
    } else {
        return 'Pending';
    }
}

// Helper function to get detailed status text
function getReviewStatusText(review) {
    const status = getReviewStatus(review);
    switch (status) {
        case 'Approved':
            return 'Approved';
        case 'Rejected':
            return 'Rejected';
        default:
            return 'Pending Approval';
    }
} 