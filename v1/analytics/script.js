// Analytics Dashboard JavaScript

// Global variables
let searchTrendsChart, pageVisitTrendsChart, salesTrendsChart;
let currentTimeframe = '30 days';

// Initialize charts
function initializeCharts() {
    initializeSearchTrendsChart();
    initializePageVisitTrendsChart();
    initializeSalesTrendsChart();
}

// Tab switching functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    // Add active class to clicked button
    event.target.classList.add('active');
}

// Global timeframe update
function updateGlobalTimeframe() {
    const timeframe = document.getElementById('globalTimeframe').value;
    currentTimeframe = timeframe;

    // Update all reports with new timeframe
    loadMostSearchedProducts();
    if ((typeof userRole !== 'undefined' && userRole === 'admin') && document.getElementById('searchedVendorsTable')) {
        loadMostSearchedVendors();
    }
    if (document.getElementById('visitedPagesTable')) {
        loadMostVisitedPages();
    }
    if (document.getElementById('orderedProductsTable')) {
        loadMostOrderedProducts();
    }

    // Update charts
    updateCharts();
}

// Refresh all data
function refreshAllData() {
    // Show loading state
    showLoadingState();

    // Reload dashboard analytics
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_dashboard_analytics'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.data);
            }
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
        })
        .finally(() => {
            hideLoadingState();
        });

    // Refresh all reports
    loadMostSearchedProducts();
    if ((typeof userRole !== 'undefined' && userRole === 'admin') && document.getElementById('searchedVendorsTable')) {
        loadMostSearchedVendors();
    }
    if (document.getElementById('visitedPagesTable')) {
        loadMostVisitedPages();
    }
    if (document.getElementById('orderedProductsTable')) {
        loadMostOrderedProducts();
    }
    if (document.getElementById('salesReportTable')) {
        loadSalesReport();
    }

    // Update charts
    updateCharts();
}

// Initialize Search Trends Chart
function initializeSearchTrendsChart() {
    const ctx = document.getElementById('searchTrendsChart');
    if (!ctx) return;

    searchTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Searches',
                data: [],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Initialize Page Visit Trends Chart
function initializePageVisitTrendsChart() {
    const ctx = document.getElementById('pageVisitTrendsChart');
    if (!ctx) return;

    pageVisitTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Page Visits',
                data: [],
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Initialize Sales Trends Chart
function initializeSalesTrendsChart() {
    const ctx = document.getElementById('salesTrendsChart');
    if (!ctx) return;

    salesTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales ($)',
                data: [],
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Update all charts
function updateCharts() {
    // In a real implementation, you would fetch trend data and update charts
    // For now, we'll just log that charts should be updated
    console.log('Charts updated for timeframe:', currentTimeframe);
}

// Load Most Searched Products
function loadMostSearchedProducts() {
    const timeframe = document.getElementById('searchedProductsTimeframe')?.value || currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_searched_products&timeframe=${encodeURIComponent(timeframe)}&limit=10`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable('searchedProductsTable', data.data, [
                    'product_name', 'category_name', 'vendor_name', 'search_count',
                    'unique_searchers', 'clicks', 'ctr'
                ]);
            }
        })
        .catch(error => {
            console.error('Error loading searched products:', error);
        });
}

// Load Most Searched Vendors (Admin only)
function loadMostSearchedVendors() {
    const timeframe = document.getElementById('searchedVendorsTimeframe')?.value || currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_searched_vendors&timeframe=${encodeURIComponent(timeframe)}&limit=10`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable('searchedVendorsTable', data.data, [
                    'vendor_name', 'email', 'search_count', 'unique_searchers',
                    'product_clicks', 'avg_results'
                ]);
            }
        })
        .catch(error => {
            console.error('Error loading searched vendors:', error);
        });
}

// Load Most Visited Pages
function loadMostVisitedPages() {
    const timeframe = document.getElementById('visitedPagesTimeframe')?.value || currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_visited_pages&timeframe=${encodeURIComponent(timeframe)}&limit=10`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable('visitedPagesTable', data.data, [
                    'product_name', 'category_name', 'price', 'vendor_name',
                    'visit_count', 'unique_visitors', 'avg_duration', 'logged_in_visits'
                ]);
            }
        })
        .catch(error => {
            console.error('Error loading visited pages:', error);
        });
}

// Load Most Ordered Products
function loadMostOrderedProducts() {
    const timeframe = document.getElementById('orderedProductsTimeframe')?.value || currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_ordered_products&timeframe=${encodeURIComponent(timeframe)}&limit=10`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable('orderedProductsTable', data.data, [
                    'product_name', 'category_name', 'price', 'vendor_name',
                    'order_count', 'quantity_sold', 'revenue', 'unique_customers'
                ]);
            }
        })
        .catch(error => {
            console.error('Error loading ordered products:', error);
        });
}

// Load Sales Report
function loadSalesReport() {
    const timeframe = document.getElementById('salesReportTimeframe')?.value || 'monthly';

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_sales_report&timeframe=${encodeURIComponent(timeframe)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable('salesReportTable', data.data, [
                    'period', 'total_orders', 'total_revenue', 'avg_order_value',
                    'unique_customers', 'active_vendors', 'success_rate'
                ]);
            }
        })
        .catch(error => {
            console.error('Error loading sales report:', error);
        });
}

// Populate table with data
function populateTable(tableId, data, columns) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    // Clear existing data
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + columns.length + '" class="text-center">No data available</td></tr>';
        return;
    }

    // Populate rows
    data.forEach(row => {
        const tr = document.createElement('tr');
        columns.forEach(column => {
            const td = document.createElement('td');
            let value = row[column] || '';

            // Format specific columns
            if (column.includes('price') || column.includes('revenue')) {
                value = '$' + parseFloat(value).toLocaleString();
            } else if (column.includes('ctr') || column.includes('success_rate')) {
                value = parseFloat(value).toFixed(2) + '%';
            } else if (column.includes('duration')) {
                value = formatDuration(value);
            }

            td.textContent = value;
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
}

// Format duration (seconds to readable format)
function formatDuration(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Export report
function exportReport(reportType) {
    const timeframe = getTimeframeForReport(reportType);

    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'export_data';
    form.appendChild(actionInput);

    const reportInput = document.createElement('input');
    reportInput.name = 'report_type';
    reportInput.value = reportType;
    form.appendChild(reportInput);

    const timeframeInput = document.createElement('input');
    timeframeInput.name = 'timeframe';
    timeframeInput.value = timeframe;
    form.appendChild(timeframeInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Get timeframe for specific report
function getTimeframeForReport(reportType) {
    const timeframeSelectors = {
        'most_searched_products': 'searchedProductsTimeframe',
        'most_searched_vendors': 'searchedVendorsTimeframe',
        'most_visited_pages': 'visitedPagesTimeframe',
        'most_ordered_products': 'orderedProductsTimeframe',
        'sales_report': 'salesReportTimeframe'
    };

    const selectorId = timeframeSelectors[reportType];
    const selector = document.getElementById(selectorId);
    return selector ? selector.value : currentTimeframe;
}

// Export chart
function exportChart(chartType) {
    let chart;
    switch (chartType) {
        case 'search_trends':
            chart = searchTrendsChart;
            break;
        case 'page_visits':
            chart = pageVisitTrendsChart;
            break;
        case 'sales_trends':
            chart = salesTrendsChart;
            break;
    }

    if (chart) {
        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = `${chartType}_chart.png`;
        link.href = url;
        link.click();
    }
}

// Update dashboard stats
function updateDashboardStats(data) {
    if (data.overview) {
        const stats = data.overview;

        // Update searches
        if (stats.searches) {
            document.getElementById('totalSearches').textContent =
                parseInt(stats.searches.total_searches || 0).toLocaleString();
            document.getElementById('searchesChange').textContent =
                `${parseInt(stats.searches.searches_this_week || 0).toLocaleString()} this week`;
        }

        // Update visits
        if (stats.visits) {
            document.getElementById('totalVisits').textContent =
                parseInt(stats.visits.total_visits || 0).toLocaleString();
            document.getElementById('visitsChange').textContent =
                `${parseInt(stats.visits.visits_this_week || 0).toLocaleString()} this week`;
        }

        // Update orders
        if (stats.orders) {
            document.getElementById('totalOrders').textContent =
                parseInt(stats.orders.total_orders || 0).toLocaleString();
            document.getElementById('ordersChange').textContent =
                `${parseInt(stats.orders.orders_this_week || 0).toLocaleString()} this week`;
            document.getElementById('totalRevenue').textContent =
                '$' + parseFloat(stats.orders.total_revenue || 0).toLocaleString();
            document.getElementById('revenueChange').textContent =
                'Avg: $' + parseFloat(stats.orders.avg_order_value || 0).toLocaleString();
        }
    }
}

// Show loading state
function showLoadingState() {
    // Add loading class or spinner
    document.querySelector('.main-content').style.opacity = '0.7';
}

// Hide loading state
function hideLoadingState() {
    document.querySelector('.main-content').style.opacity = '1';
}

// Utility function to format numbers
function formatNumber(num) {
    return parseInt(num || 0).toLocaleString();
}

// Utility function to format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
} 