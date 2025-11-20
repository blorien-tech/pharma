<?php

return [
    'title' => 'Alerts & Notifications',
    'subtitle' => 'Monitor stock levels and expiry dates',

    // Tabs
    'low_stock' => 'Low Stock',
    'expiring_soon' => 'Expiring Soon',
    'expired' => 'Expired',

    // Low Stock
    'low_stock_title' => 'Low Stock Alerts',
    'low_stock_desc' => 'Products that need restocking',
    'no_low_stock' => 'No low stock products',
    'no_low_stock_desc' => 'All products are well stocked',
    'stock_threshold' => 'Stock Threshold',
    'current_stock' => 'Current Stock',
    'reorder_level' => 'Reorder Level',
    'needs_restock' => 'Needs Restock',

    // Expiring Soon
    'expiring_soon_title' => 'Expiring Soon',
    'expiring_soon_desc' => 'Batches expiring within 30 days',
    'no_expiring_soon' => 'No batches expiring soon',
    'no_expiring_soon_desc' => 'All batches have sufficient time before expiry',
    'expires_in' => 'Expires in',
    'days' => 'days',
    'expiry_date' => 'Expiry Date',
    'batch_number' => 'Batch Number',
    'batch_qty' => 'Batch Quantity',

    // Expired
    'expired_title' => 'Expired Products',
    'expired_desc' => 'Batches that have already expired',
    'no_expired' => 'No expired batches',
    'no_expired_desc' => 'No products have expired',
    'expired_on' => 'Expired on',
    'expired_since' => 'Expired since',
    'days_ago' => 'days ago',
    'remove_expired' => 'Remove Expired',

    // Product Info
    'product_name' => 'Product Name',
    'product_sku' => 'Product SKU',
    'category' => 'Category',
    'supplier' => 'Supplier',
    'location' => 'Location',

    // Actions
    'restock' => 'Restock',
    'view_product' => 'View Product',
    'add_batch' => 'Add Batch',
    'manage_batches' => 'Manage Batches',
    'create_purchase_order' => 'Create Purchase Order',
    'export_list' => 'Export List',
    'print_list' => 'Print List',

    // Filters
    'filter_category' => 'Filter by Category',
    'all_categories' => 'All Categories',
    'filter_urgency' => 'Filter by Urgency',
    'critical' => 'Critical',
    'warning' => 'Warning',
    'normal' => 'Normal',
    'sort_by' => 'Sort By',
    'sort_stock_asc' => 'Stock (Low to High)',
    'sort_stock_desc' => 'Stock (High to Low)',
    'sort_expiry_asc' => 'Expiry (Nearest First)',
    'sort_expiry_desc' => 'Expiry (Farthest First)',

    // Summary
    'summary' => 'Summary',
    'total_low_stock_products' => 'Total Low Stock Products',
    'total_expiring_batches' => 'Total Expiring Batches',
    'total_expired_batches' => 'Total Expired Batches',
    'value_at_risk' => 'Value at Risk',
    'action_required' => 'Action Required',

    // Messages
    'alert_acknowledged' => 'Alert acknowledged',
    'batch_removed' => 'Expired batch removed successfully',
    'confirm_remove_batch' => 'Are you sure you want to remove this expired batch?',
    'stock_updated' => 'Stock levels updated',
];
