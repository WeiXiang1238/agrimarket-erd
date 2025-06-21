# AgriMarket Components & Styling Guide

## Overview
This directory contains the global styling system for the AgriMarket application. The components use a utility-first approach with consistent design tokens and reusable classes.

**✅ IMPORTANT**: The global `main.css` is now included in ALL pages throughout the AgriMarket project, ensuring consistent styling across the entire application.

## 🎨 Global Styling Standards

### Font Size Scale (rem-based)
We use a consistent font size scale throughout the project:

```css
/* Headings */
.fs-1 { font-size: 2.5rem; }    /* Main titles */
.fs-2 { font-size: 2rem; }      /* Page titles */
.fs-3 { font-size: 1.75rem; }   /* Section titles */
.fs-4 { font-size: 1.5rem; }    /* Subsection titles */
.fs-5 { font-size: 1.25rem; }   /* Card titles */
.fs-6 { font-size: 1rem; }      /* Body text */

/* Body & Utility */
.fs-small { font-size: 0.875rem; } /* Small text */
.fs-xs { font-size: 0.75rem; }     /* Extra small text */
```

### Border Radius Standards
Consistent border radius values for cohesive design:

```css
.rounded-1 { border-radius: 0.25rem; }   /* 4px - Small elements */
.rounded-2 { border-radius: 0.5rem; }    /* 8px - Form inputs */
.rounded-3 { border-radius: 0.75rem; }   /* 12px - Cards, buttons */
.rounded-4 { border-radius: 1rem; }      /* 16px - Large cards */
.rounded-5 { border-radius: 1.5rem; }    /* 24px - Modal dialogs */
.rounded-pill { border-radius: 50rem; }  /* Full rounded */
```

### Spacing System (rem-based)
Standardized spacing using rem units:

```css
/* Margins & Padding */
.p-1 { padding: 0.25rem; }     /* 4px */
.p-2 { padding: 0.5rem; }      /* 8px */
.p-3 { padding: 1rem; }        /* 16px */
.p-4 { padding: 1.5rem; }      /* 24px */
.p-5 { padding: 3rem; }        /* 48px */

/* Same pattern for margins: .m-1, .m-2, etc. */
/* Directional: .mt-*, .mb-*, .ml-*, .mr-*, .pt-*, .pb-*, .pl-*, .pr-* */
```

### Color Palette
Consistent color system across all components:

```css
/* Primary Colors */
--primary: #3b82f6;      /* Blue */
--secondary: #64748b;    /* Slate */
--success: #10b981;      /* Green */
--danger: #ef4444;       /* Red */
--warning: #f59e0b;      /* Amber */
--info: #06b6d4;         /* Cyan */
--light: #f1f5f9;        /* Light gray */
--dark: #1e293b;         /* Dark gray */
```

## 📁 File Structure

```
v1/components/
├── main.css          # Global utility classes and components (AUTO-INCLUDED)
├── header.css        # Header-specific styles
├── header.php        # Header component
├── sidebar.css       # Sidebar-specific styles  
├── sidebar.php       # Sidebar component
└── README.md         # This file
```

## 🚀 CSS Loading Order

### Automatic Global Inclusion
The `main.css` file is **automatically included** in all pages with the following order:

```html
<!-- 1. Global styles (included automatically) -->
<link rel="stylesheet" href="../components/main.css">

<!-- 2. Component-specific styles (auto-included by components) -->
<!-- header.css, sidebar.css loaded by components -->

<!-- 3. Page-specific styles -->
<link rel="stylesheet" href="style.css">

<!-- 4. External libraries -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
```

### Pages with Global CSS:
✅ **Dashboard** (`v1/dashboard/index.php`)  
✅ **User Management** (`v1/user-management/index.php`)  
✅ **Vendor Management** (`v1/vendor-management/index.php`)  
✅ **Login** (`v1/auth/login/index.php`)  
✅ **Register** (`v1/auth/register/index.php`)  
✅ **Forgot Password** (`v1/auth/forgot-password/index.php`)  

## 🎯 Usage Guide

### 1. Global Styles (Automatic)
**No action needed!** Global styles are automatically included in all pages.

### 2. Button Standardization
Use consistent button classes:

```html
<!-- Primary button -->
<button class="btn btn-primary">Save Changes</button>

<!-- Secondary button -->
<button class="btn btn-secondary">Cancel</button>

<!-- Button sizes -->
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Regular</button>
<button class="btn btn-primary btn-lg">Large</button>

<!-- Icon buttons -->
<button class="btn-icon btn-primary">
    <i class="fas fa-edit"></i>
</button>
```

### 3. Card Components
Standardized card structure:

```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
    </div>
    <div class="card-body">
        <p class="card-text">Card content goes here.</p>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Action</button>
    </div>
</div>
```

### 4. Form Elements
Consistent form styling:

```html
<div class="form-group">
    <label class="form-label">Email Address</label>
    <input type="email" class="form-control" placeholder="Enter email">
</div>

<div class="form-group">
    <label class="form-label">Country</label>
    <select class="form-select">
        <option>Choose...</option>
        <option>Option 1</option>
    </select>
</div>
```

### 5. Alert Messages
Standardized alert styling:

```html
<div class="alert alert-success">
    <strong>Success!</strong> Your changes have been saved.
</div>

<div class="alert alert-danger">
    <strong>Error!</strong> Please check your input.
</div>
```

### 6. Badges & Status Indicators
Consistent badge styling:

```html
<!-- Status badges -->
<span class="badge badge-success">Active</span>
<span class="badge badge-danger">Inactive</span>

<!-- Role badges -->
<span class="badge badge-primary">Admin</span>
<span class="badge badge-warning">Vendor</span>
```

### 7. Utility Classes
Quick styling with utility classes:

```html
<!-- Flexbox utilities -->
<div class="d-flex justify-between align-center">
    <h3>Title</h3>
    <button class="btn btn-primary">Action</button>
</div>

<!-- Spacing utilities -->
<div class="p-4 mt-3 mb-5">Content with padding and margins</div>

<!-- Text utilities -->
<p class="text-center fs-small text-secondary">Small centered text</p>
```

## 🎯 Design Principles

### 1. Consistency First
- Use established font sizes, spacing, and colors
- Follow the same patterns across all pages
- Reuse existing components when possible

### 2. Mobile-First Responsive
- Design for mobile devices first
- Use relative units (rem, %, vh/vw)
- Test on different screen sizes

### 3. Accessibility
- Maintain proper color contrast
- Use semantic HTML elements
- Include focus states for interactive elements

### 4. Performance
- Minimize custom CSS when global classes exist
- Avoid duplicate styles across files
- Use efficient selectors

## 📊 Breakpoints

Standard responsive breakpoints:

```css
/* Small devices (landscape phones) */
@media (max-width: 576px) { }

/* Medium devices (tablets) */
@media (max-width: 768px) { }

/* Large devices (desktops) */
@media (max-width: 992px) { }

/* Extra large devices (large desktops) */
@media (max-width: 1200px) { }
```

## 🔧 Component Reference

### Available Components:
- **Buttons**: Primary, secondary, success, danger, warning, info, light, dark
- **Cards**: Header, body, footer variations
- **Forms**: Input, select, textarea, labels
- **Alerts**: Success, danger, warning, info
- **Badges**: Color-coded status indicators
- **Modals**: Overlay dialogs with animations
- **Loading**: Spinners and loading states
- **Tables**: Styled data tables
- **Pagination**: Navigation controls

### Layout Utilities:
- **Display**: d-block, d-flex, d-grid, d-none
- **Flexbox**: justify-*, align-*, flex-*
- **Positioning**: position-*, top-*, left-*
- **Spacing**: Margin and padding utilities
- **Sizing**: Width and height utilities

## 🚨 Standards Enforcement

### DO:
✅ Use global utility classes from `main.css` (automatically available)  
✅ Follow the established font size scale  
✅ Use rem units for consistency  
✅ Maintain the color palette  
✅ Follow mobile-first responsive design  

### DON'T:
❌ Create duplicate button styles  
❌ Use pixel values for font sizes  
❌ Override global styles unnecessarily  
❌ Use inconsistent spacing values  
❌ Ignore responsive design principles  

## 📝 Adding New Pages

When creating new pages in the AgriMarket project:

1. **Global CSS is automatic**: `main.css` should be included in all new pages
2. **Follow the CSS order**: Global → Component → Page-specific → External
3. **Use utility classes**: Leverage existing classes before creating custom styles
4. **Test responsiveness**: Verify mobile compatibility
5. **Follow naming conventions**: Use established patterns

### New Page Template:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - AgriMarket Solutions</title>
    <!-- Global styles (required) -->
    <link rel="stylesheet" href="../components/main.css">
    <!-- Page-specific styles -->
    <link rel="stylesheet" href="style.css">
    <!-- External libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Your content here -->
</body>
</html>
```

## 🔍 Quality Checklist

Before submitting code:

- [ ] Global styles automatically included (`main.css`)
- [ ] Font sizes use established scale
- [ ] Spacing uses rem units
- [ ] Colors match the palette
- [ ] Components follow established patterns
- [ ] Mobile responsiveness tested
- [ ] No duplicate styles
- [ ] Accessibility considered

## 📞 Support

For questions about the styling system or component usage, please refer to:
- This README for guidelines
- `main.css` for available classes
- Existing implementations for examples

---

**Last Updated**: December 2024  
**Version**: 2.1  
**Maintainer**: AgriMarket Development Team 