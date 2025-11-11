/**
 * Export Report Function - Dùng chung cho tất cả báo cáo
 * 
 * Usage in Blade view:
 * 1. Add id="filterForm" to the filter form
 * 2. Add data-report-type="sinh-vien" to the form or body
 * 3. Call exportReport(event, 'excel') or exportReport(event, 'pdf') from onclick
 */

function exportReport(event, type) {
    event.preventDefault();
    
    // Get current filters from form
    const form = document.getElementById('filterForm');
    if (!form) {
        console.error('Form with id="filterForm" not found');
        return;
    }
    
    const formData = new FormData(form);
    
    // Build query string
    const params = new URLSearchParams();
    
    // Get report type from data attribute or form
    let reportType = form.dataset.reportType || form.getAttribute('data-report-type');
    
    // If not found in form, try to extract from URL
    if (!reportType) {
        const path = window.location.pathname;
        const parts = path.split('/');
        reportType = parts[parts.length - 1] || 'sinh-vien';
    }
    
    params.append('loai', reportType);
    
    // Add all form data to params
    for (const [key, value] of formData.entries()) {
        if (value && value.trim() !== '') {
            params.append(key, value);
        }
    }
    
    // Determine base URL based on current route
    let baseUrl = '';
    const currentPath = window.location.pathname;
    
    if (currentPath.includes('/dao-tao/')) {
        // Đào tạo routes
        baseUrl = type === 'excel' 
            ? '/dao-tao/bao-cao/export-excel'
            : '/dao-tao/bao-cao/export-pdf';
    } else if (currentPath.includes('/giang-vien/')) {
        // Giảng viên routes
        baseUrl = type === 'excel'
            ? '/giang-vien/bao-cao/export-excel'
            : '/giang-vien/bao-cao/export-pdf';
    } else {
        console.error('Unknown route context');
        return;
    }
    
    // Build full URL with query string
    const fullUrl = baseUrl + '?' + params.toString();
    
    // Open in new window/tab
    window.open(fullUrl, '_blank');
}

/**
 * Export with custom filters
 * @param {Event} event 
 * @param {String} type - 'excel' or 'pdf'
 * @param {Object} customFilters - Additional filters to add
 */
function exportReportWithFilters(event, type, customFilters = {}) {
    event.preventDefault();
    
    const form = document.getElementById('filterForm');
    if (!form) {
        console.error('Form with id="filterForm" not found');
        return;
    }
    
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    // Get report type
    let reportType = form.dataset.reportType || form.getAttribute('data-report-type');
    if (!reportType) {
        const path = window.location.pathname;
        const parts = path.split('/');
        reportType = parts[parts.length - 1] || 'sinh-vien';
    }
    
    params.append('loai', reportType);
    
    // Add form data
    for (const [key, value] of formData.entries()) {
        if (value && value.trim() !== '') {
            params.append(key, value);
        }
    }
    
    // Add custom filters
    for (const [key, value] of Object.entries(customFilters)) {
        if (value && value.toString().trim() !== '') {
            params.append(key, value);
        }
    }
    
    // Determine URL
    const currentPath = window.location.pathname;
    let baseUrl = '';
    
    if (currentPath.includes('/dao-tao/')) {
        baseUrl = type === 'excel' 
            ? '/dao-tao/bao-cao/export-excel'
            : '/dao-tao/bao-cao/export-pdf';
    } else if (currentPath.includes('/giang-vien/')) {
        baseUrl = type === 'excel'
            ? '/giang-vien/bao-cao/export-excel'
            : '/giang-vien/bao-cao/export-pdf';
    }
    
    const fullUrl = baseUrl + '?' + params.toString();
    window.open(fullUrl, '_blank');
}
