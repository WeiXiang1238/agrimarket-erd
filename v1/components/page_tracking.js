/**
 * Page Visit Tracking JavaScript
 * Tracks page leave events and updates visit duration
 */

// Track page visibility changes
let pageHidden = false;
let startTime = Date.now();

// Function to send visit duration update
function updateVisitDuration() {
    const duration = Math.floor((Date.now() - startTime) / 1000); // Duration in seconds

    // Send update via AJAX
    fetch('/agrimarket-erd/v1/analytics/update-visit-duration.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `duration=${duration}`
    })
        .catch(error => {
            console.log('Visit duration update failed:', error);
        });
}

// Track page visibility changes
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        pageHidden = true;
        updateVisitDuration();
    } else {
        pageHidden = false;
        startTime = Date.now();
    }
});

// Track page unload (user leaves page)
window.addEventListener('beforeunload', function () {
    if (!pageHidden) {
        updateVisitDuration();
    }
});

// Track page focus/blur
window.addEventListener('blur', function () {
    if (!pageHidden) {
        updateVisitDuration();
    }
});

// Track when user navigates away
window.addEventListener('pagehide', function () {
    if (!pageHidden) {
        updateVisitDuration();
    }
});

// Track mouse leaving window (optional)
document.addEventListener('mouseleave', function (e) {
    if (e.clientY <= 0) { // Mouse left from top of window
        // Don't update duration immediately, just mark as potential leave
        setTimeout(() => {
            if (document.hidden || !document.hasFocus()) {
                updateVisitDuration();
            }
        }, 5000); // Wait 5 seconds to see if user returns
    }
});

// Track tab switching
window.addEventListener('focus', function () {
    if (pageHidden) {
        pageHidden = false;
        startTime = Date.now();
    }
});

// Track session storage for cross-tab tracking
window.addEventListener('storage', function (e) {
    if (e.key === 'page_visit_start') {
        startTime = parseInt(e.newValue) || Date.now();
    }
});

// Store start time in session storage for cross-tab tracking
if (typeof (Storage) !== "undefined") {
    sessionStorage.setItem('page_visit_start', startTime.toString());
}

// Function to manually track specific events
function trackEvent(eventName, eventData = {}) {
    fetch('/agrimarket-erd/v1/analytics/track-event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event: eventName,
            data: eventData,
            timestamp: Date.now()
        })
    })
        .catch(error => {
            console.log('Event tracking failed:', error);
        });
}

// Track scroll depth
let maxScrollDepth = 0;
window.addEventListener('scroll', function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = Math.round((scrollTop / scrollHeight) * 100);

    if (scrollPercent > maxScrollDepth) {
        maxScrollDepth = scrollPercent;

        // Track significant scroll milestones
        if (maxScrollDepth >= 25 && maxScrollDepth < 50) {
            trackEvent('scroll_25_percent');
        } else if (maxScrollDepth >= 50 && maxScrollDepth < 75) {
            trackEvent('scroll_50_percent');
        } else if (maxScrollDepth >= 75 && maxScrollDepth < 100) {
            trackEvent('scroll_75_percent');
        } else if (maxScrollDepth >= 100) {
            trackEvent('scroll_100_percent');
        }
    }
});

// Track time on page milestones
const timeMilestones = [30, 60, 120, 300, 600]; // seconds
let milestoneIndex = 0;

setInterval(function () {
    const timeOnPage = Math.floor((Date.now() - startTime) / 1000);

    if (milestoneIndex < timeMilestones.length && timeOnPage >= timeMilestones[milestoneIndex]) {
        trackEvent('time_on_page', {
            seconds: timeMilestones[milestoneIndex]
        });
        milestoneIndex++;
    }
}, 1000);

// Track clicks on important elements
document.addEventListener('click', function (e) {
    const target = e.target;

    // Track product clicks
    if (target.closest('.product-card') || target.closest('[data-product-id]')) {
        const productId = target.closest('[data-product-id]')?.dataset.productId;
        trackEvent('product_click', { product_id: productId });
    }

    // Track add to cart clicks
    if (target.closest('.add-to-cart') || target.closest('[data-action="add-to-cart"]')) {
        trackEvent('add_to_cart_click');
    }

    // Track checkout clicks
    if (target.closest('.checkout-btn') || target.closest('[data-action="checkout"]')) {
        trackEvent('checkout_click');
    }

    // Track search clicks
    if (target.closest('.search-btn') || target.closest('[data-action="search"]')) {
        trackEvent('search_click');
    }
});

// Export functions for manual tracking
window.PageTracking = {
    trackEvent: trackEvent,
    updateVisitDuration: updateVisitDuration,
    getTimeOnPage: function () {
        return Math.floor((Date.now() - startTime) / 1000);
    },
    getScrollDepth: function () {
        return maxScrollDepth;
    }
}; 