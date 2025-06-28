// Role & Permission Management JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Get DOM elements
    const editRoleBtns = document.querySelectorAll('.edit-role-btn');
    const permissionEditor = document.getElementById('permission-editor');
    const noRoleSelected = document.getElementById('no-role-selected');
    const permissionForm = document.getElementById('permission-form');
    const cancelEditBtn = document.getElementById('cancel-edit');

    // Role card elements
    const roleCards = document.querySelectorAll('.role-card');

    // Form elements
    const editRoleId = document.getElementById('edit-role-id');
    const editRoleName = document.getElementById('edit-role-name');
    const editRoleDescription = document.getElementById('edit-role-description');

    let currentSelectedRole = null;

    // Add click handlers to role cards
    roleCards.forEach(card => {
        card.addEventListener('click', function () {
            const roleId = this.getAttribute('data-role-id');
            selectRole(roleId, this);
        });
    });

    // Add click handlers to edit buttons
    editRoleBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent card click
            const roleId = this.getAttribute('data-role-id');
            const roleCard = this.closest('.role-card');
            selectRole(roleId, roleCard);
        });
    });

    // Cancel edit handler
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function () {
            hidePermissionEditor();
        });
    }

    // Form submission handler
    if (permissionForm) {
        permissionForm.addEventListener('submit', function (e) {
            // Optional: Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            }
        });
    }

    /**
     * Select a role and load its permissions
     */
    function selectRole(roleId, roleCard) {
        // Update UI
        setActiveRoleCard(roleCard);
        showLoading();

        // Fetch role details via AJAX
        fetchRoleDetails(roleId)
            .then(roleData => {
                if (roleData) {
                    displayRolePermissions(roleData);
                } else {
                    showError('Failed to load role details');
                }
            })
            .catch(error => {
                console.error('Error fetching role details:', error);
                showError('Failed to load role details');
            });
    }

    /**
     * Set active role card
     */
    function setActiveRoleCard(activeCard) {
        // Remove active class from all cards
        roleCards.forEach(card => {
            card.classList.remove('active');
        });

        // Add active class to selected card
        if (activeCard) {
            activeCard.classList.add('active');
            currentSelectedRole = activeCard.getAttribute('data-role-id');
        }
    }

    /**
     * Show loading state
     */
    function showLoading() {
        hideNoSelection();
        permissionEditor.style.display = 'none';

        // Create loading element if it doesn't exist
        let loadingElement = document.querySelector('.loading');
        if (!loadingElement) {
            loadingElement = document.createElement('div');
            loadingElement.className = 'loading';
            loadingElement.innerHTML = '<i class="fas fa-spinner"></i> Loading role details...';
            document.querySelector('.permissions-edit-section').appendChild(loadingElement);
        }

        loadingElement.style.display = 'flex';
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        const loadingElement = document.querySelector('.loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }

    /**
     * Show error message
     */
    function showError(message) {
        hideLoading();
        hidePermissionEditor();

        // Create or update error element
        let errorElement = document.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message alert alert-error';
            document.querySelector('.permissions-edit-section').appendChild(errorElement);
        }

        errorElement.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        errorElement.style.display = 'flex';

        // Hide error after 5 seconds
        setTimeout(() => {
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }, 5000);
    }

    /**
     * Fetch role details from server
     */
    async function fetchRoleDetails(roleId) {
        try {
            const response = await fetch(`?action=get_role_details&role_id=${roleId}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Fetch error:', error);
            return null;
        }
    }

    /**
     * Display role permissions in the editor
     */
    function displayRolePermissions(roleData) {
        hideLoading();
        hideNoSelection();

        // Update form fields
        if (editRoleId) editRoleId.value = roleData.role_id;
        if (editRoleName) editRoleName.textContent = roleData.role_name;
        if (editRoleDescription) {
            editRoleDescription.textContent = roleData.description || 'No description available';
        }

        // Clear all checkboxes first
        const checkboxes = permissionForm.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Check permissions that the role has
        if (roleData.permissions && Array.isArray(roleData.permissions)) {
            roleData.permissions.forEach(permission => {
                const checkbox = permissionForm.querySelector(
                    `input[data-permission-id="${permission.permission_id}"]`
                );
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        // Show the permission editor
        showPermissionEditor();
    }

    /**
     * Show permission editor
     */
    function showPermissionEditor() {
        hideNoSelection();
        permissionEditor.style.display = 'block';
    }

    /**
     * Hide permission editor
     */
    function hidePermissionEditor() {
        permissionEditor.style.display = 'none';
        showNoSelection();
        setActiveRoleCard(null);
        currentSelectedRole = null;
    }

    /**
     * Show no selection message
     */
    function showNoSelection() {
        if (noRoleSelected) {
            noRoleSelected.style.display = 'flex';
        }
    }

    /**
     * Hide no selection message
     */
    function hideNoSelection() {
        if (noRoleSelected) {
            noRoleSelected.style.display = 'none';
        }
    }

    /**
     * Handle checkbox changes for better UX
     */
    const permissionCheckboxes = document.querySelectorAll('.permission-item input[type="checkbox"]');
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const permissionItem = this.closest('.permission-item');
            if (this.checked) {
                permissionItem.classList.add('selected');
            } else {
                permissionItem.classList.remove('selected');
            }
        });
    });

    /**
     * Add select all/none functionality for modules
     */
    function addModuleSelectAll() {
        const moduleHeaders = document.querySelectorAll('.permission-module h4');

        moduleHeaders.forEach(header => {
            const selectAllBtn = document.createElement('button');
            selectAllBtn.type = 'button';
            selectAllBtn.className = 'btn-link module-select-all';
            selectAllBtn.innerHTML = '<i class="fas fa-check-square"></i> Select All';
            selectAllBtn.style.cssText = 'margin-left: auto; font-size: 0.8rem; color: #3498db; background: none; border: none; cursor: pointer;';

            const selectNoneBtn = document.createElement('button');
            selectNoneBtn.type = 'button';
            selectNoneBtn.className = 'btn-link module-select-none';
            selectNoneBtn.innerHTML = '<i class="fas fa-square"></i> None';
            selectNoneBtn.style.cssText = 'margin-left: 0.5rem; font-size: 0.8rem; color: #7f8c8d; background: none; border: none; cursor: pointer;';

            header.appendChild(selectAllBtn);
            header.appendChild(selectNoneBtn);

            const module = header.closest('.permission-module');
            const checkboxes = module.querySelectorAll('input[type="checkbox"]');

            selectAllBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });

            selectNoneBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });
        });
    }

    // Initialize module select all functionality
    addModuleSelectAll();

    /**
     * Keyboard navigation support
     */
    document.addEventListener('keydown', function (e) {
        // ESC to cancel editing
        if (e.key === 'Escape' && permissionEditor.style.display === 'block') {
            hidePermissionEditor();
        }

        // Enter to submit form when focused on submit button
        if (e.key === 'Enter' && e.target.classList.contains('btn-success')) {
            e.target.click();
        }
    });

    /**
     * Auto-save warning when leaving page with unsaved changes
     */
    let formChanged = false;

    if (permissionForm) {
        const formInputs = permissionForm.querySelectorAll('input[type="checkbox"]');
        formInputs.forEach(input => {
            input.addEventListener('change', function () {
                formChanged = true;
            });
        });

        permissionForm.addEventListener('submit', function () {
            formChanged = false;
        });
    }

    window.addEventListener('beforeunload', function (e) {
        if (formChanged) {
            const message = 'You have unsaved changes. Are you sure you want to leave?';
            e.returnValue = message;
            return message;
        }
    });

    // Initialize with first role if needed (optional)
    // Uncomment the following lines if you want to auto-select the first role
    // if (roleCards.length > 0) {
    //     const firstRole = roleCards[0];
    //     const firstRoleId = firstRole.getAttribute('data-role-id');
    //     selectRole(firstRoleId, firstRole);
    // }
}); 