// Vendor Directory JavaScript

let currentPage = 1;
let isLoading = false;
let currentSearchLogId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    // Add search on Enter key
    document.getElementById('searchInput').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            searchVendors();
        }
    });

    // Add filter change listeners
    document.getElementById('tierFilter').addEventListener('change', function () {
        searchVendors();
    });
});

function searchVendors(page = 1) {
    if (isLoading) return;

    isLoading = true;
    currentPage = page;

    const searchTerm = document.getElementById('searchInput').value.trim();
    const tier = document.getElementById('tierFilter').value;

    const formData = new FormData();
    formData.append('action', 'search_vendors');
    formData.append('page', page);
    formData.append('limit', 12);
    formData.append('search', searchTerm);
    formData.append('subscription_tier', tier);

    // Show loading state
    document.getElementById('vendorsGrid').innerHTML =
        '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Loading vendors...</p></div>';

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentSearchLogId = data.search_log_id || null;
                displayVendors(data.vendors);
                updatePagination(data.page, data.totalPages);
                updateURL(searchTerm, tier, page);
            } else {
                document.getElementById('vendorsGrid').innerHTML =
                    '<div class="no-vendors"><i class="fas fa-exclamation-circle"></i><h3>Error</h3><p>' + (data.message || 'Failed to load vendors') + '</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('vendorsGrid').innerHTML =
                '<div class="no-vendors"><i class="fas fa-exclamation-circle"></i><h3>Error</h3><p>Failed to load vendors. Please try again.</p></div>';
        })
        .finally(() => {
            isLoading = false;
        });
}

function loadVendors(page = 1) {
    searchVendors(page);
}

function displayVendors(vendors) {
    const grid = document.getElementById('vendorsGrid');

    if (!vendors || vendors.length === 0) {
        grid.innerHTML = `
            <div class="no-vendors">
                <i class="fas fa-store-slash"></i>
                <h3>No vendors found</h3>
                <p>Try adjusting your search criteria or browse all vendors.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = vendors.map((vendor, index) => `
        <div class="vendor-card" onclick="trackVendorClick(${vendor.vendor_id}, ${index + 1})">
            <div class="vendor-avatar-section">
                <div class="vendor-avatar">
                    ${vendor.business_name ? vendor.business_name.charAt(0).toUpperCase() : 'V'}
                </div>
                <div class="tier-badge ${vendor.subscription_tier || 'basic'}">
                    ${(vendor.subscription_tier || 'basic').charAt(0).toUpperCase() + (vendor.subscription_tier || 'basic').slice(1)}
                </div>
                <div class="vendor-actions-overlay">
                    <button class="btn-icon" onclick="event.stopPropagation(); viewVendorProducts(${vendor.vendor_id})" title="View Products">
                        <i class="fas fa-shopping-bag"></i>
                    </button>
                    <button class="btn-icon" onclick="event.stopPropagation(); contactVendor('${escapeHtml(vendor.user_email || '')}')" title="Contact Vendor">
                        <i class="fas fa-envelope"></i>
                    </button>
                </div>
            </div>
            
            <div class="vendor-info">
                <h3 class="vendor-name">${escapeHtml(vendor.business_name || 'Unknown Vendor')}</h3>
                
                <div class="vendor-contact-info">
                    ${vendor.user_email ? `
                        <div class="vendor-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:${escapeHtml(vendor.user_email)}" onclick="event.stopPropagation()">${escapeHtml(vendor.user_email)}</a>
                        </div>
                    ` : ''}
                    ${vendor.contact_number ? `
                        <div class="vendor-contact-item">
                            <i class="fas fa-phone"></i>
                            <span>${escapeHtml(vendor.contact_number)}</span>
                        </div>
                    ` : ''}
                    ${vendor.address ? `
                        <div class="vendor-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${escapeHtml(vendor.address)}</span>
                        </div>
                    ` : ''}
                    ${vendor.website_url ? `
                        <div class="vendor-contact-item">
                            <i class="fas fa-globe"></i>
                            <a href="${escapeHtml(vendor.website_url)}" target="_blank" onclick="event.stopPropagation()">Visit Website</a>
                        </div>
                    ` : ''}
                </div>
                
                ${vendor.description ? `
                    <div class="vendor-description">${escapeHtml(vendor.description)}</div>
                ` : ''}
                
                <div class="vendor-actions-bottom">
                    <button class="btn-view-products" onclick="event.stopPropagation(); viewVendorProducts(${vendor.vendor_id})">
                        <i class="fas fa-shopping-bag"></i> View Products
                    </button>
                    <button class="btn-contact" onclick="event.stopPropagation(); contactVendor('${escapeHtml(vendor.user_email || '')}')">
                        <i class="fas fa-envelope"></i> Contact
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function trackVendorClick(vendorId, clickPosition) {
    if (!currentSearchLogId) return;

    const formData = new FormData();
    formData.append('action', 'track_vendor_click');
    formData.append('search_log_id', currentSearchLogId);
    formData.append('vendor_id', vendorId);
    formData.append('click_position', clickPosition);

    fetch('', {
        method: 'POST',
        body: formData
    }).catch(error => {
        console.log('Click tracking failed:', error);
    });
}

function viewVendorProducts(vendorId) {
    window.location.href = `/agrimarket-erd/v1/products/?vendor_id=${vendorId}`;
}

function contactVendor(email) {
    if (email) {
        window.location.href = `mailto:${email}`;
    } else {
        alert('Contact information not available for this vendor.');
    }
}

function updatePagination(currentPage, totalPages) {
    const pagination = document.getElementById('pagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <button onclick="searchVendors(${currentPage - 1})" ${currentPage <= 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i> Previous
        </button>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            paginationHTML += `<button class="active">${i}</button>`;
        } else if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            paginationHTML += `<button onclick="searchVendors(${i})">${i}</button>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            paginationHTML += `<span>...</span>`;
        }
    }

    // Next button
    paginationHTML += `
        <button onclick="searchVendors(${currentPage + 1})" ${currentPage >= totalPages ? 'disabled' : ''}>
            Next <i class="fas fa-chevron-right"></i>
        </button>
    `;

    pagination.innerHTML = paginationHTML;
}

function updateURL(search, tier, page) {
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (tier) params.set('subscription_tier', tier);
    if (page > 1) params.set('page', page);

    const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.pushState({}, '', newURL);
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('tierFilter').value = '';
    searchVendors(1);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
} 