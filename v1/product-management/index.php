<?php
// Only display errors for development - disable for production
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/ProductService.php';

$authService = new AuthService();
$productService = new ProductService();

// Require authentication and appropriate permission
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();

// Check if user has permission or is admin/vendor
$userRole = $currentUser['role'] ?? '';

$hasAccess = in_array($userRole, ['admin', 'vendor']);

if (!$hasAccess) {
    header('Location: /agrimarket-erd/v1/dashboard/');
    exit;
}
$csrfToken = $authService->generateCSRFToken();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Clear any output buffer to prevent HTML before JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    
    if (!$authService->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    try {
        switch ($_POST['action']) {
            case 'get_products':
                $page = intval($_POST['page'] ?? 1);
                $limit = intval($_POST['limit'] ?? 10);
                
                $filters = [
                    'search' => $_POST['search'] ?? '',
                    'category_id' => $_POST['category_id'] ?? '',
                    'vendor_id' => $_POST['vendor_id'] ?? '',
                    'status' => $_POST['status'] ?? ''
                ];
                
                $result = $productService->getPaginatedProducts(
                    $page, 
                    $limit, 
                    $filters, 
                    $userRole, 
                    $currentUser['user_id']
                );
                
                echo json_encode($result);
                exit;
            
            case 'create_product':
                error_log('Creating product with data: ' . print_r($_POST, true));
                $result = $productService->createProduct($_POST, $userRole, $currentUser['user_id']);
                error_log('Product creation result: ' . print_r($result, true));
                echo json_encode($result);
                exit;
                
            case 'update_product':
                $productId = intval($_POST['product_id']);
                $result = $productService->updateProduct($productId, $_POST);
                echo json_encode($result);
                exit;
                
            case 'toggle_product_status':
                $productId = intval($_POST['product_id']);
                $status = $_POST['status'];
                $result = $productService->toggleProductStatus($productId, $status);
                echo json_encode($result);
                exit;
                
            case 'delete_product':
                $productId = intval($_POST['product_id']);
                $result = $productService->deleteProduct($productId);
                echo json_encode($result);
                exit;
                
            case 'get_product':
                $productId = intval($_POST['product_id']);
                $result = $productService->getProductById($productId, $userRole, $currentUser['user_id']);
                echo json_encode($result);
                exit;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Get data for the page
try {
    // Get product statistics
    $productStats = $productService->getProductStatistics($userRole, $currentUser['user_id']);
    
    // Get categories for dropdown
    $categories = $productService->getCategories();
    
    // Get vendors for dropdown
    $vendors = $productService->getVendors();
    
} catch (Exception $e) {
    $productStats = [
        'total_products' => 0,
        'active_products' => 0,
        'out_of_stock_products' => 0,
        'featured_products' => 0
    ];
    $categories = [];
    $vendors = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = 'Product Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Product Management</h2>
                            <p>Manage products, inventory, and product information</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i>
                            Add New Product
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $productStats['total_products']; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $productStats['active_products']; ?></h3>
                            <p>Active Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $productStats['out_of_stock_products']; ?></h3>
                            <p>Out of Stock</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon featured">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $productStats['featured_products']; ?></h3>
                            <p>Featured Products</p>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search products...">
                        </div>
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($userRole !== 'vendor'): ?>
                        <select id="vendorFilter">
                            <option value="">All Vendors</option>
                            <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo $vendor['vendor_id']; ?>">
                                    <?php echo htmlspecialchars($vendor['business_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">In Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Products List</h3>
                        <div class="table-controls">
                            <select id="limitSelect" onchange="loadProducts()">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="productsTableContent">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading products...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-cube"></i>Add New Product</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalMessage"></div>
                <form id="productForm" enctype="multipart/form-data">
                    <input type="hidden" id="productId" name="product_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Product Name *</label>
                            <input type="text" id="productName" name="name" required 
                                   maxlength="100" placeholder="Enter product name">
                            <div class="error-message" id="productNameError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Packaging</label>
                            <input type="text" id="productPackaging" name="packaging" 
                                   maxlength="100" placeholder="e.g., 1kg bag, 500g box">
                            <div class="error-message" id="productPackagingError"></div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <?php if ($userRole !== 'vendor'): ?>
                        <div class="form-group">
                            <label>Vendor *</label>
                            <select id="productVendor" name="vendor_id" required>
                                <option value="">Select vendor...</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor['vendor_id']; ?>">
                                        <?php echo htmlspecialchars($vendor['business_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="productVendorError"></div>
                        </div>
                        <?php else: ?>
                        <input type="hidden" id="productVendor" name="vendor_id" value="">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Category *</label>
                            <select id="productCategory" name="category_id" required>
                                <option value="">Select category...</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="productCategoryError"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="productDescription" name="description" rows="3" 
                                  placeholder="Enter product description"></textarea>
                        <div class="error-message" id="productDescriptionError"></div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Base Price *</label>
                            <input type="number" id="productUnitPrice" name="unit_price" 
                                   step="0.01" min="0" required placeholder="0.00">
                            <div class="error-message" id="productUnitPriceError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Selling Price *</label>
                            <input type="number" id="productSellingPrice" name="selling_price" 
                                   step="0.01" min="0" required placeholder="0.00">
                            <div class="error-message" id="productSellingPriceError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Stock Quantity</label>
                            <input type="number" id="productStock" name="stock_quantity" 
                                   min="0" placeholder="0">
                            <div class="error-message" id="productStockError"></div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="productDiscounted" name="is_discounted" value="1">
                                Is Discounted
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Discount Percent (%)</label>
                            <input type="number" id="productDiscountPercent" name="discount_percent" 
                                   step="0.01" min="0" max="100" placeholder="0.00">
                            <div class="error-message" id="productDiscountPercentError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label>Product Image *</label>
                            <div class="image-upload-container">
                                <input type="file" id="productImage" name="product_image" 
                                       accept="image/*" onchange="previewImage(this)">
                                <div class="image-preview" id="imagePreview" onclick="document.getElementById('productImage').click()">
                                    <img id="previewImg" src="" alt="Image Preview" style="display: none;">
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <i class="fas fa-image"></i>
                                        <p>Click to upload product image (Required)</p>
                                    </div>
                                    <button type="button" class="remove-image-btn" id="removeImageBtn" 
                                            onclick="removeImage(event)" style="display: none;" 
                                            title="Remove image">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="image-status-indicator" id="imageStatusIndicator" style="display: none;">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Current Image</span>
                                    </div>
                                </div>
                            </div>
                            <div class="error-message" id="productImageError"></div>
                        </div>
                    </div>
                    

                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let isEditing = false;

        // Load products on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Add search functionality
            document.getElementById('searchInput').addEventListener('input', debounce(function() {
                loadProducts(1);
            }, 300));
            
            // Add filter functionality
            document.getElementById('categoryFilter').addEventListener('change', function() {
                loadProducts(1);
            });
            
            const vendorFilter = document.getElementById('vendorFilter');
            if (vendorFilter) {
                vendorFilter.addEventListener('change', function() {
                    loadProducts(1);
                });
            }
            
            document.getElementById('statusFilter').addEventListener('change', function() {
                loadProducts(1);
            });

            // Add real-time validation event listeners
            setupProductValidation();
        });

        // Setup real-time validation for product form
        function setupProductValidation() {
            // Product Name validation
            const productName = document.getElementById('productName');
            productName.addEventListener('blur', function() {
                validateProductName();
            });
            productName.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productNameError');
                }
            });

            // Unit Price validation
            const unitPrice = document.getElementById('productUnitPrice');
            unitPrice.addEventListener('blur', function() {
                validateUnitPrice();
            });
            unitPrice.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productUnitPriceError');
                }
            });

            // Selling Price validation
            const sellingPrice = document.getElementById('productSellingPrice');
            sellingPrice.addEventListener('blur', function() {
                validateSellingPrice();
            });
            sellingPrice.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productSellingPriceError');
                }
            });

            // Stock validation
            const stock = document.getElementById('productStock');
            stock.addEventListener('blur', function() {
                validateStock();
            });
            stock.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productStockError');
                }
            });

            // Category validation
            const categorySelect = document.getElementById('productCategory');
            categorySelect.addEventListener('change', function() {
                validateCategory();
            });
            
            // Vendor validation (only for admin)
            const vendorSelect = document.getElementById('productVendor');
            if (vendorSelect && vendorSelect.tagName === 'SELECT') {
                vendorSelect.addEventListener('change', function() {
                    validateVendor();
                });
            }

            // Packaging validation
            const packaging = document.getElementById('productPackaging');
            packaging.addEventListener('blur', function() {
                validatePackaging();
            });
            packaging.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productPackagingError');
                }
            });

            // Description validation
            const description = document.getElementById('productDescription');
            description.addEventListener('blur', function() {
                validateDescription();
            });
            description.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productDescriptionError');
                }
            });

            // Discount validation
            const discounted = document.getElementById('productDiscounted');
            const discountPercent = document.getElementById('productDiscountPercent');
            
            discounted.addEventListener('change', function() {
                if (this.checked) {
                    discountPercent.focus();
                } else {
                    clearProductError('productDiscountPercentError');
                }
            });

            discountPercent.addEventListener('blur', function() {
                validateDiscountPercent();
            });
            discountPercent.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearProductError('productDiscountPercentError');
                }
            });
        }

        // Load products function
        function loadProducts(page = 1) {
            currentPage = page;
            
            const formData = new FormData();
            formData.append('action', 'get_products');
            formData.append('page', page);
            formData.append('limit', document.getElementById('limitSelect').value);
            formData.append('search', document.getElementById('searchInput').value);
            formData.append('category_id', document.getElementById('categoryFilter').value);
            
            const vendorFilter = document.getElementById('vendorFilter');
            if (vendorFilter) {
                formData.append('vendor_id', vendorFilter.value);
            } else {
                formData.append('vendor_id', '');
            }
            
            formData.append('status', document.getElementById('statusFilter').value);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');

            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    displayProducts(data);
                } else {
                    document.getElementById('productsTableContent').innerHTML = 
                        '<div class="error">Error loading products: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('productsTableContent').innerHTML = 
                    '<div class="error">Error loading products. Please try again.</div>';
            });
        }

        // Display products in table
        function displayProducts(data) {
            let html = `
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>PRODUCT</th>
                            <th>PACKAGING</th>
                            <th>VENDOR</th>
                            <th>CATEGORY</th>
                            <th>PRICE</th>
                            <th>STOCK</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            if (data.products.length === 0) {
                html += '<tr><td colspan="8" class="no-data">No products found</td></tr>';
            } else {
                data.products.forEach(product => {
                    const statusClass = product.status === 'active' ? 'active' : 'warning';
                    const statusText = product.status === 'active' ? 'IN STOCK' : 'OUT OF STOCK';
                    
                    html += `
                        <tr>
                            <td>
                                <div class="product-info">
                                    <strong>${escapeHtml(product.name)}</strong>
                                    ${product.is_discounted == 1 ? '<span class="discount-badge">Discounted</span>' : ''}
                                </div>
                            </td>
                            <td>${escapeHtml(product.packaging || 'N/A')}</td>
                            <td>${escapeHtml(product.vendor_name || 'N/A')}</td>
                            <td>${escapeHtml(product.category_name || 'Uncategorized')}</td>
                            <td>
                                <div class="price-info">
                                    <strong>$${parseFloat(product.selling_price).toFixed(2)}</strong>
                                    ${product.unit_price !== product.selling_price ? 
                                        `<small class="unit-price">Unit: $${parseFloat(product.unit_price).toFixed(2)}</small>` : ''}
                                </div>
                            </td>
                            <td>
                                <span class="stock-badge ${product.stock_quantity <= 10 ? 'low-stock' : 'in-stock'}">
                                    ${product.stock_quantity}
                                </span>
                            </td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editProduct(${product.product_id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-${product.status === 'active' ? 'warning' : 'success'}" 
                                            onclick="toggleProductStatus(${product.product_id}, '${product.status === 'active' ? 'out_of_stock' : 'active'}')" 
                                            title="${product.status === 'active' ? 'Mark Out of Stock' : 'Mark In Stock'}">
                                        <i class="fas fa-${product.status === 'active' ? 'pause' : 'play'}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.product_id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
            
            html += '</tbody></table>';
            
            // Add pagination
            if (data.totalPages > 1) {
                html += generatePagination(data.page, data.totalPages);
            }
            
            document.getElementById('productsTableContent').innerHTML = html;
        }

        // Open create product modal
        function openCreateModal() {
            isEditing = false;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-cube"></i>Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            
            // Reset image preview
            document.getElementById('previewImg').style.display = 'none';
            document.getElementById('previewImg').src = '';
            document.getElementById('uploadPlaceholder').style.display = 'block';
            document.getElementById('removeImageBtn').style.display = 'none';
            document.getElementById('imageStatusIndicator').style.display = 'none';
            
            clearModalErrors();
            document.getElementById('productModal').style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
            
            // Reset image preview
            document.getElementById('previewImg').style.display = 'none';
            document.getElementById('previewImg').src = '';
            document.getElementById('uploadPlaceholder').style.display = 'block';
            document.getElementById('removeImageBtn').style.display = 'none';
            document.getElementById('imageStatusIndicator').style.display = 'none';
            
            clearModalErrors();
        }

        // Save product
        function saveProduct() {
            console.log('saveProduct function called');
            
            // Clear previous messages
            document.getElementById('modalMessage').innerHTML = '';
            
            // Validate form
            if (!validateProductForm()) {
                console.log('Form validation failed');
                document.getElementById('modalMessage').innerHTML = 
                    '<div class="error">Please fix the errors above</div>';
                return;
            }
            
            console.log('Form validation passed');

            const formData = new FormData(document.getElementById('productForm'));
            formData.append('action', isEditing ? 'update_product' : 'create_product');
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            console.log('FormData created, action:', isEditing ? 'update_product' : 'create_product');
            
            // Check if existing image should be removed
            const preview = document.getElementById('previewImg');
            const fileInput = document.getElementById('productImage');
            
            if (isEditing && !preview.hasAttribute('data-existing-image') && !fileInput.files.length) {
                // User removed existing image and didn't upload a new one
                formData.append('remove_image', '1');
            }

            // Show loading state
            const saveButton = document.querySelector('#productForm .btn-primary');
            const originalText = saveButton.textContent;
            saveButton.textContent = isEditing ? 'Updating...' : 'Creating...';
            saveButton.disabled = true;
            
            console.log('Sending fetch request...');
            
            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    showModalMessage(data.message, 'success');
                    setTimeout(() => {
                        closeModal();
                        loadProducts(currentPage);
                    }, 1500);
                } else {
                    showModalMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModalMessage('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Restore button state
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
        }

        // Preview uploaded image
        function previewImage(input) {
            const preview = document.getElementById('previewImg');
            const placeholder = document.getElementById('uploadPlaceholder');
            const errorElement = document.getElementById('productImageError');
            
            // Clear previous errors
            errorElement.textContent = '';
            errorElement.style.display = 'none';
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    errorElement.textContent = 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.';
                    errorElement.style.display = 'block';
                    input.value = '';
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                    document.getElementById('removeImageBtn').style.display = 'none';
                    return;
                }
                
                // Validate file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    errorElement.textContent = 'File size too large. Maximum allowed size is 5MB.';
                    errorElement.style.display = 'block';
                    input.value = '';
                    preview.style.display = 'none';
                    placeholder.style.display = 'block';
                    document.getElementById('removeImageBtn').style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    document.getElementById('removeImageBtn').style.display = 'flex';
                    
                    // Clear existing image data attributes since we're uploading a new image
                    preview.removeAttribute('data-existing-image');
                    document.getElementById('imagePreview').removeAttribute('data-existing-image');
                    
                    // Hide status indicator for new uploads
                    document.getElementById('imageStatusIndicator').style.display = 'none';
                    
                    // Clear image validation error
                    clearProductError('productImageError');
                };
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                placeholder.style.display = 'block';
                document.getElementById('removeImageBtn').style.display = 'none';
            }
        }

        // Remove uploaded image
        function removeImage(event) {
            event.stopPropagation(); // Prevent triggering the file input
            
            const fileInput = document.getElementById('productImage');
            const preview = document.getElementById('previewImg');
            const placeholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImageBtn');
            const errorElement = document.getElementById('productImageError');
            const imagePreview = document.getElementById('imagePreview');
            const statusIndicator = document.getElementById('imageStatusIndicator');
            
            // Clear the file input
            fileInput.value = '';
            
            // Reset the preview
            preview.style.display = 'none';
            preview.src = '';
            placeholder.style.display = 'block';
            removeBtn.style.display = 'none';
            statusIndicator.style.display = 'none';
            
            // Remove existing image data attributes
            preview.removeAttribute('data-existing-image');
            imagePreview.removeAttribute('data-existing-image');
            
            // Clear any errors and validate image requirement
            errorElement.textContent = '';
            errorElement.style.display = 'none';
            
            // Validate image requirement after removal
            setTimeout(() => {
                validateProductImage();
            }, 100);
        }

        // Edit product
        function editProduct(productId) {
            isEditing = true;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i>Edit Product';
            document.getElementById('productId').value = productId;
            clearModalErrors();
            
            // Show loading in modal
            showModalMessage('Loading product details...', 'info');
            
            // Fetch product details
            const formData = new FormData();
            formData.append('action', 'get_product');
            formData.append('product_id', productId);
            formData.append('csrf_token', '<?php echo $csrfToken; ?>');
            
            fetch('', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateEditForm(data.product);
                    document.getElementById('modalMessage').style.display = 'none';
                } else {
                    showModalMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModalMessage('Failed to load product details.', 'error');
            });
            
            document.getElementById('productModal').style.display = 'block';
        }

        // Populate edit form with product data
        function populateEditForm(product) {
            // Clear form first
            document.getElementById('productForm').reset();
            
            // Fill form fields
            document.getElementById('productName').value = product.name || '';
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productPackaging').value = product.packaging || '';
            document.getElementById('productUnitPrice').value = product.base_price || '';
            document.getElementById('productSellingPrice').value = product.selling_price || '';
            document.getElementById('productStock').value = product.stock_quantity || '';
            document.getElementById('productDiscounted').checked = parseInt(product.is_discounted) === 1;
            document.getElementById('productDiscountPercent').value = product.discount_percent || '';
            
            // Set vendor (only for admin users)
            const vendorSelect = document.getElementById('productVendor');
            if (vendorSelect && product.vendor_id) {
                vendorSelect.value = product.vendor_id;
            }
            
            // Set category
            const categorySelect = document.getElementById('productCategory');
            if (categorySelect && product.category_id) {
                categorySelect.value = product.category_id;
            }
            
            // Handle image display
            const preview = document.getElementById('previewImg');
            const placeholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImageBtn');
            const imagePreview = document.getElementById('imagePreview');
            const statusIndicator = document.getElementById('imageStatusIndicator');
            
            if (product.image_path && product.image_path.trim() !== '') {
                // Show existing image
                preview.src = '../../' + product.image_path;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                removeBtn.style.display = 'flex';
                statusIndicator.style.display = 'flex';
                
                // Add data attributes to track if this is an existing image
                preview.setAttribute('data-existing-image', product.image_path);
                imagePreview.setAttribute('data-existing-image', 'true');
            } else {
                // No existing image
                preview.style.display = 'none';
                preview.src = '';
                placeholder.style.display = 'block';
                removeBtn.style.display = 'none';
                statusIndicator.style.display = 'none';
                preview.removeAttribute('data-existing-image');
                imagePreview.removeAttribute('data-existing-image');
            }
        }

        // Toggle product status
        function toggleProductStatus(productId, newStatus) {
            const action = newStatus === 'active' ? 'mark as in stock' : 'mark as out of stock';
            if (confirm(`Are you sure you want to ${action} this product?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_product_status');
                formData.append('product_id', productId);
                formData.append('status', newStatus);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');
                
                fetch('', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadProducts(currentPage);
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }
        }

        // Delete product
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_product');
                formData.append('product_id', productId);
                formData.append('csrf_token', '<?php echo $csrfToken; ?>');
                
                fetch('', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadProducts(currentPage);
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }
        }

        // Utility functions
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

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
        }

        function generatePagination(currentPage, totalPages) {
            let html = '<div class="pagination">';
            
            if (currentPage > 1) {
                html += `<button onclick="loadProducts(${currentPage - 1})" class="btn btn-sm btn-secondary">Previous</button>`;
            }
            
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                html += `<button onclick="loadProducts(${i})" class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}">${i}</button>`;
            }
            
            if (currentPage < totalPages) {
                html += `<button onclick="loadProducts(${currentPage + 1})" class="btn btn-sm btn-secondary">Next</button>`;
            }
            
            html += '</div>';
            return html;
        }

        function showModalMessage(message, type) {
            const messageDiv = document.getElementById('modalMessage');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
        }

        function clearModalErrors() {
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            
            const messageDiv = document.getElementById('modalMessage');
            messageDiv.style.display = 'none';
            
            // Remove error states from form groups
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
        }

        // Product validation functions
        function validateProductForm() {
            let isValid = true;
            clearModalErrors();
            
            // Validate Product Name
            const name = document.getElementById('productName').value.trim();
            if (!name) {
                showProductError('productNameError', 'Product name is required');
                isValid = false;
            } else if (name.length < 2) {
                showProductError('productNameError', 'Product name must be at least 2 characters');
                isValid = false;
            } else if (name.length > 100) {
                showProductError('productNameError', 'Product name must be less than 100 characters');
                isValid = false;
            }
            
            // Validate Unit Price
            const unitPrice = document.getElementById('productUnitPrice').value.trim();
            if (!unitPrice) {
                showProductError('productUnitPriceError', 'Base price is required');
                isValid = false;
            } else {
                const price = parseFloat(unitPrice);
                if (isNaN(price) || price <= 0) {
                    showProductError('productUnitPriceError', 'Base price must be a valid positive number');
                    isValid = false;
                } else if (price > 999999.99) {
                    showProductError('productUnitPriceError', 'Base price cannot exceed $999,999.99');
                    isValid = false;
                }
            }
            
            // Validate Selling Price
            const sellingPrice = document.getElementById('productSellingPrice').value.trim();
            if (!sellingPrice) {
                showProductError('productSellingPriceError', 'Selling price is required');
                isValid = false;
            } else {
                const price = parseFloat(sellingPrice);
                if (isNaN(price) || price <= 0) {
                    showProductError('productSellingPriceError', 'Selling price must be a valid positive number');
                    isValid = false;
                } else if (price > 999999.99) {
                    showProductError('productSellingPriceError', 'Selling price cannot exceed $999,999.99');
                    isValid = false;
                }
                
                // Check if selling price is reasonable compared to unit price
                const unitPriceValue = parseFloat(unitPrice);
                if (!isNaN(unitPriceValue) && price < unitPriceValue * 0.5) {
                    showProductError('productSellingPriceError', 'Selling price seems too low compared to base price');
                    isValid = false;
                }
            }
            
            // Validate Stock Quantity
            const stock = document.getElementById('productStock').value.trim();
            if (stock !== '') {
                const stockNum = parseInt(stock);
                if (isNaN(stockNum) || stockNum < 0) {
                    showProductError('productStockError', 'Stock quantity must be a valid non-negative number');
                    isValid = false;
                } else if (stockNum > 999999) {
                    showProductError('productStockError', 'Stock quantity cannot exceed 999,999');
                    isValid = false;
                }
            }
            
            // Validate Category (required)
            const categorySelect = document.getElementById('productCategory');
            if (!categorySelect.value) {
                showProductError('productCategoryError', 'Please select a category');
                isValid = false;
            }
            
            // Validate Vendor (only for admin users)
            const vendorSelect = document.getElementById('productVendor');
            if (vendorSelect && vendorSelect.tagName === 'SELECT' && !vendorSelect.value) {
                showProductError('productVendorError', 'Please select a vendor');
                isValid = false;
            }
            
            // Validate Packaging (optional but validate if provided)
            const packaging = document.getElementById('productPackaging').value.trim();
            if (packaging && packaging.length > 100) {
                showProductError('productPackagingError', 'Packaging description must be less than 100 characters');
                isValid = false;
            }
            
            // Validate Description (optional but validate if provided)
            const description = document.getElementById('productDescription').value.trim();
            if (description && description.length > 1000) {
                showProductError('productDescriptionError', 'Description must be less than 1000 characters');
                isValid = false;
            }
            
            // Validate Discount Percent (if product is discounted)
            const isDiscounted = document.getElementById('productDiscounted').checked;
            const discountPercent = document.getElementById('productDiscountPercent').value.trim();
            if (isDiscounted) {
                if (!discountPercent) {
                    showProductError('productDiscountPercentError', 'Discount percentage is required when product is marked as discounted');
                    isValid = false;
                } else {
                    const discount = parseFloat(discountPercent);
                    if (isNaN(discount) || discount <= 0 || discount > 100) {
                        showProductError('productDiscountPercentError', 'Discount percentage must be between 0.01 and 100');
                        isValid = false;
                    }
                }
            }
            
            // Validate Product Image (required)
            const fileInput = document.getElementById('productImage');
            const preview = document.getElementById('previewImg');
            const hasExistingImage = preview.hasAttribute('data-existing-image');
            const hasNewImage = fileInput.files && fileInput.files.length > 0;
            
            if (!hasExistingImage && !hasNewImage) {
                showProductError('productImageError', 'Product image is required');
                isValid = false;
            }
            
            return isValid;
        }

        function showProductError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
                errorElement.style.display = 'block';
                
                // Add error state to form group
                const formGroup = errorElement.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.add('error');
                    formGroup.classList.remove('success');
                }
            }
        }

        function clearProductError(elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.remove('show');
                errorElement.style.display = 'none';
                
                // Remove error state from form group
                const formGroup = errorElement.closest('.form-group');
                if (formGroup) {
                    formGroup.classList.remove('error');
                }
            }
        }

        // Individual validation functions for real-time validation
        function validateProductName() {
            const name = document.getElementById('productName').value.trim();
            if (!name) {
                showProductError('productNameError', 'Product name is required');
                return false;
            } else if (name.length < 2) {
                showProductError('productNameError', 'Product name must be at least 2 characters');
                return false;
            } else if (name.length > 100) {
                showProductError('productNameError', 'Product name must be less than 100 characters');
                return false;
            } else {
                clearProductError('productNameError');
                return true;
            }
        }

        function validateUnitPrice() {
            const unitPrice = document.getElementById('productUnitPrice').value.trim();
            if (!unitPrice) {
                showProductError('productUnitPriceError', 'Base price is required');
                return false;
            } else {
                const price = parseFloat(unitPrice);
                if (isNaN(price) || price <= 0) {
                    showProductError('productUnitPriceError', 'Base price must be a valid positive number');
                    return false;
                } else if (price > 999999.99) {
                    showProductError('productUnitPriceError', 'Base price cannot exceed $999,999.99');
                    return false;
                } else {
                    clearProductError('productUnitPriceError');
                    return true;
                }
            }
        }

        function validateSellingPrice() {
            const sellingPrice = document.getElementById('productSellingPrice').value.trim();
            if (!sellingPrice) {
                showProductError('productSellingPriceError', 'Selling price is required');
                return false;
            } else {
                const price = parseFloat(sellingPrice);
                if (isNaN(price) || price <= 0) {
                    showProductError('productSellingPriceError', 'Selling price must be a valid positive number');
                    return false;
                } else if (price > 999999.99) {
                    showProductError('productSellingPriceError', 'Selling price cannot exceed $999,999.99');
                    return false;
                } else {
                    // Check if selling price is reasonable compared to unit price
                    const unitPrice = document.getElementById('productUnitPrice').value.trim();
                    const unitPriceValue = parseFloat(unitPrice);
                    if (!isNaN(unitPriceValue) && price < unitPriceValue * 0.5) {
                        showProductError('productSellingPriceError', 'Selling price seems too low compared to base price');
                        return false;
                    } else {
                        clearProductError('productSellingPriceError');
                        return true;
                    }
                }
            }
        }

        function validateStock() {
            const stock = document.getElementById('productStock').value.trim();
            if (stock !== '') {
                const stockNum = parseInt(stock);
                if (isNaN(stockNum) || stockNum < 0) {
                    showProductError('productStockError', 'Stock quantity must be a valid non-negative number');
                    return false;
                } else if (stockNum > 999999) {
                    showProductError('productStockError', 'Stock quantity cannot exceed 999,999');
                    return false;
                } else {
                    clearProductError('productStockError');
                    return true;
                }
            } else {
                clearProductError('productStockError');
                return true;
            }
        }

        function validateCategory() {
            const categorySelect = document.getElementById('productCategory');
            if (!categorySelect.value) {
                showProductError('productCategoryError', 'Please select a category');
                return false;
            } else {
                clearProductError('productCategoryError');
                return true;
            }
        }

        function validateVendor() {
            const vendorSelect = document.getElementById('productVendor');
            if (vendorSelect && vendorSelect.tagName === 'SELECT' && !vendorSelect.value) {
                showProductError('productVendorError', 'Please select a vendor');
                return false;
            } else {
                clearProductError('productVendorError');
                return true;
            }
        }

        function validatePackaging() {
            const packaging = document.getElementById('productPackaging').value.trim();
            if (packaging && packaging.length > 100) {
                showProductError('productPackagingError', 'Packaging description must be less than 100 characters');
                return false;
            } else {
                clearProductError('productPackagingError');
                return true;
            }
        }

        function validateDescription() {
            const description = document.getElementById('productDescription').value.trim();
            if (description && description.length > 1000) {
                showProductError('productDescriptionError', 'Description must be less than 1000 characters');
                return false;
            } else {
                clearProductError('productDescriptionError');
                return true;
            }
        }

        function validateDiscountPercent() {
            const isDiscounted = document.getElementById('productDiscounted').checked;
            const discountPercent = document.getElementById('productDiscountPercent').value.trim();
            
            if (isDiscounted) {
                if (!discountPercent) {
                    showProductError('productDiscountPercentError', 'Discount percentage is required when product is marked as discounted');
                    return false;
                } else {
                    const discount = parseFloat(discountPercent);
                    if (isNaN(discount) || discount <= 0 || discount > 100) {
                        showProductError('productDiscountPercentError', 'Discount percentage must be between 0.01 and 100');
                        return false;
                    } else {
                        clearProductError('productDiscountPercentError');
                        return true;
                    }
                }
            } else {
                clearProductError('productDiscountPercentError');
                return true;
            }
        }

        function validateProductImage() {
            const fileInput = document.getElementById('productImage');
            const preview = document.getElementById('previewImg');
            const hasExistingImage = preview.hasAttribute('data-existing-image');
            const hasNewImage = fileInput.files && fileInput.files.length > 0;
            
            if (!hasExistingImage && !hasNewImage) {
                showProductError('productImageError', 'Product image is required');
                return false;
            } else {
                clearProductError('productImageError');
                return true;
            }
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 