// Analytics Dashboard JavaScript

// Global variables
let searchTrendsChart, pageVisitTrendsChart, salesTrendsChart, pageVisitTypesChart;
let currentTimeframe = '30 days';
let currentPageType = 'product'; // 'product' or 'general'

// Initialize charts
function initializeCharts() {
    initializeSearchTrendsChart();
    initializePageVisitTrendsChart();
    initializeSalesTrendsChart();
    initializePageVisitTypesChart();
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

// Initialize Page Visit Types Chart
function initializePageVisitTypesChart() {
    const ctx = document.getElementById('pageVisitTypesChart');
    if (!ctx) return;

    pageVisitTypesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Page Visits',
                data: [],
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#06b6d4',
                    '#6b7280'
                ],
                borderColor: [
                    '#2563eb',
                    '#059669',
                    '#d97706',
                    '#dc2626',
                    '#7c3aed',
                    '#0891b2',
                    '#4b5563'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function updateCharts() {
    loadPageVisitTrendsByType();
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
                    'product_name', 'category', 'vendor_name', 'search_count',
                    'unique_searchers', 'clicks', 'click_through_rate'
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
                    'business_name', 'contact_email', 'search_count', 'unique_searchers',
                    'product_clicks', 'click_rate'
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
                populateVisitedPagesTable('visitedPagesTable', data.data);
            }
        })
        .catch(error => {
            console.error('Error loading visited pages:', error);
        });
}

// Load General Most Visited Pages
function loadGeneralMostVisitedPages() {
    const timeframe = document.getElementById('visitedPagesTimeframe')?.value || currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_visited_pages_general&timeframe=${encodeURIComponent(timeframe)}&limit=10`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateGeneralVisitedPagesTable('visitedPagesTable', data.data);
            }
        })
        .catch(error => {
            console.error('Error loading general visited pages:', error);
        });
}

// Populate visited pages table with product-specific data
function populateVisitedPagesTable(tableId, data) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    // Clear existing data
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No data available</td></tr>';
        return;
    }

    // Populate rows
    data.forEach(row => {
        const tr = document.createElement('tr');

        // Page/Product
        const td1 = document.createElement('td');
        td1.innerHTML = `<strong>${row.product_name || 'N/A'}</strong>`;
        tr.appendChild(td1);

        // Category
        const td2 = document.createElement('td');
        td2.textContent = row.category || 'N/A';
        tr.appendChild(td2);

        // Vendor
        const td3 = document.createElement('td');
        td3.textContent = row.vendor_name || 'N/A';
        tr.appendChild(td3);

        // Visit Count
        const td4 = document.createElement('td');
        td4.innerHTML = `<span class="badge badge-primary">${parseInt(row.visit_count).toLocaleString()}</span>`;
        tr.appendChild(td4);

        // Unique Visitors
        const td5 = document.createElement('td');
        td5.textContent = parseInt(row.unique_visitors).toLocaleString();
        tr.appendChild(td5);

        // Avg Duration
        const td6 = document.createElement('td');
        td6.textContent = formatDuration(row.avg_visit_duration);
        tr.appendChild(td6);

        // Logged In Visits
        const td7 = document.createElement('td');
        td7.innerHTML = `<span class="badge badge-success">${parseInt(row.logged_in_visits).toLocaleString()}</span>`;
        tr.appendChild(td7);

        // Anonymous Visits
        const td8 = document.createElement('td');
        td8.innerHTML = `<span class="badge badge-warning">${parseInt(row.anonymous_visits).toLocaleString()}</span>`;
        tr.appendChild(td8);

        // Bounce Rate
        const td9 = document.createElement('td');
        const bounceRate = parseFloat(row.bounce_rate || 0);
        const bounceClass = bounceRate > 70 ? 'badge-danger' : bounceRate > 50 ? 'badge-warning' : 'badge-success';
        td9.innerHTML = `<span class="badge ${bounceClass}">${bounceRate.toFixed(1)}%</span>`;
        tr.appendChild(td9);

        tbody.appendChild(tr);
    });
}

// Populate general visited pages table
function populateGeneralVisitedPagesTable(tableId, data) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    // Clear existing data
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No data available</td></tr>';
        return;
    }

    // Populate rows
    data.forEach(row => {
        const tr = document.createElement('tr');

        // Page/Product
        const td1 = document.createElement('td');
        td1.innerHTML = `<strong>${row.page_title || row.page_url || 'N/A'}</strong><br><small class="text-muted">${row.page_type}</small>`;
        tr.appendChild(td1);

        // Category (Page Type)
        const td2 = document.createElement('td');
        td2.innerHTML = `<span class="badge badge-info">${row.page_type}</span>`;
        tr.appendChild(td2);

        // Vendor (N/A for general pages)
        const td3 = document.createElement('td');
        td3.textContent = 'N/A';
        tr.appendChild(td3);

        // Visit Count
        const td4 = document.createElement('td');
        td4.innerHTML = `<span class="badge badge-primary">${parseInt(row.visit_count).toLocaleString()}</span>`;
        tr.appendChild(td4);

        // Unique Visitors
        const td5 = document.createElement('td');
        td5.textContent = parseInt(row.unique_visitors).toLocaleString();
        tr.appendChild(td5);

        // Avg Duration
        const td6 = document.createElement('td');
        td6.textContent = formatDuration(row.avg_visit_duration);
        tr.appendChild(td6);

        // Logged In Visits
        const td7 = document.createElement('td');
        td7.innerHTML = `<span class="badge badge-success">${parseInt(row.logged_in_visits).toLocaleString()}</span>`;
        tr.appendChild(td7);

        // Anonymous Visits
        const td8 = document.createElement('td');
        td8.innerHTML = `<span class="badge badge-warning">${parseInt(row.anonymous_visits).toLocaleString()}</span>`;
        tr.appendChild(td8);

        // Bounce Rate
        const td9 = document.createElement('td');
        const bounceRate = parseFloat(row.bounce_rate || 0);
        const bounceClass = bounceRate > 70 ? 'badge-danger' : bounceRate > 50 ? 'badge-warning' : 'badge-success';
        td9.innerHTML = `<span class="badge ${bounceClass}">${bounceRate.toFixed(1)}%</span>`;
        tr.appendChild(td9);

        tbody.appendChild(tr);
    });
}

// Load Most Ordered Products
function loadMostOrderedProducts() {
    const timeframe = document.getElementById('orderedProductsTimeframe').value;
    const tableBody = document.querySelector('#orderedProductsTable tbody');

    // Show loading state
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_most_ordered_products&timeframe=${timeframe}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const columns = [
                    { key: 'product_name', format: (val) => val },
                    { key: 'category', format: (val) => val },
                    { key: 'vendor_name', format: (val) => val || 'N/A' },
                    { key: 'order_count', format: (val) => formatNumber(val) },
                    { key: 'total_quantity_sold', format: (val) => formatNumber(val) },
                    { key: 'total_revenue', format: (val) => formatCurrency(val) },
                    { key: 'avg_price_per_unit', format: (val) => formatCurrency(val) }
                ];
                populateTable('orderedProductsTable', data.data, columns);
            } else {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No data available</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading most ordered products:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
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
    if (!table) {
        console.error('Table not found:', tableId);
        return;
    }

    const tbody = table.querySelector('tbody');
    if (!tbody) {
        console.error('Table body not found for:', tableId);
        return;
    }

    // Clear existing data
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + columns.length + '" class="text-center">No data available</td></tr>';
        return;
    }

    // Populate rows
    data.forEach((row, index) => {
        const tr = document.createElement('tr');
        columns.forEach(column => {
            const td = document.createElement('td');
            let value = '';

            // Handle both string and object column formats
            if (typeof column === 'string') {
                // Simple string format - use as key
                value = row[column] || '';
            } else if (typeof column === 'object' && column.key) {
                // Object format with key and optional format function
                value = row[column.key] || '';
                if (column.format) {
                    value = column.format(value);
                }
            }

            // Format specific columns based on column name/key
            const columnKey = typeof column === 'string' ? column : column.key;
            if (columnKey === 'search_count' || columnKey === 'unique_searchers' || columnKey === 'clicks' || columnKey === 'product_clicks') {
                value = parseInt(value || 0).toLocaleString();
            } else if (columnKey === 'click_rate' || columnKey === 'ctr' || columnKey === 'click_through_rate') {
                value = parseFloat(value || 0).toFixed(2) + '%';
            } else if (columnKey === 'price') {
                value = 'RM ' + parseFloat(value || 0).toFixed(2);
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
        case 'page_visit_types':
            chart = pageVisitTypesChart;
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

        // Update page views
        if (stats.visits) {
            const pageViewsElement = document.getElementById('totalPageViews');
            const pageViewsChangeElement = document.getElementById('pageViewsChange');
            if (pageViewsElement) {
                pageViewsElement.textContent = parseInt(stats.visits.total_page_views || 0).toLocaleString();
            }
            if (pageViewsChangeElement) {
                pageViewsChangeElement.textContent = `${parseInt(stats.visits.unique_pages_visited || 0).toLocaleString()} unique pages`;
            }
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

// Toggle between product pages and general pages
function togglePageType(type) {
    currentPageType = type;

    // Update button states
    document.getElementById('productPagesBtn').classList.toggle('active', type === 'product');
    document.getElementById('generalPagesBtn').classList.toggle('active', type === 'general');

    // Load appropriate data
    if (type === 'product') {
        loadMostVisitedPages();
    } else {
        loadGeneralMostVisitedPages();
    }
}

// Load Page Visit Trends by Type
function loadPageVisitTrendsByType() {
    const timeframe = currentTimeframe;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_page_visit_trends_by_type&timeframe=${encodeURIComponent(timeframe)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && pageVisitTypesChart) {
                updatePageVisitTypesChart(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading page visit trends by type:', error);
        });
}

// Update Page Visit Types Chart
function updatePageVisitTypesChart(data) {
    if (!pageVisitTypesChart || !data) return;

    // Group data by page type and sum visits
    const pageTypeData = {};
    data.forEach(item => {
        if (!pageTypeData[item.page_type]) {
            pageTypeData[item.page_type] = 0;
        }
        pageTypeData[item.page_type] += parseInt(item.visit_count || 0);
    });

    // Sort by visit count
    const sortedData = Object.entries(pageTypeData)
        .sort(([, a], [, b]) => b - a)
        .slice(0, 7); // Top 7 page types

    const labels = sortedData.map(([type]) => type);
    const visitCounts = sortedData.map(([, count]) => count);

    pageVisitTypesChart.data.labels = labels;
    pageVisitTypesChart.data.datasets[0].data = visitCounts;
    pageVisitTypesChart.update();
} 