<?php

return [
    // Page titles
    'title' => 'Storage Locations',
    'subtitle' => 'Manage your storage locations and track inventory placement',
    'create_subtitle' => 'Create a new storage location',
    'add_location' => 'Add Location',
    'edit_location' => 'Edit Location',
    'create_location' => 'Create Location',
    'update_location' => 'Update Location',
    'delete_location' => 'Delete Location',
    'view_location' => 'View Location',

    // Quick Hierarchy
    'quick_hierarchy' => 'Quick Hierarchy',
    'quick_hierarchy_desc' => 'Create multiple locations at once',
    'rack_name' => 'Rack Name',
    'rack_name_example' => 'e.g., Main Rack',
    'shelf_count' => 'Number of Shelves',
    'bins_per_shelf' => 'Bins per Shelf',
    'bin_capacity' => 'Bin Capacity',
    'preview' => 'Preview',
    'will_create' => 'This will create',
    'total_locations' => 'total locations',
    'rack' => 'Rack',
    'racks' => 'racks',
    'shelf' => 'Shelf',
    'shelves' => 'shelves',
    'bin' => 'Bin',
    'bins' => 'bins',
    'create_hierarchy' => 'Create Hierarchy',
    'hierarchy_created' => 'Hierarchy created successfully!',

    // Location types
    'type' => 'Type',
    'select_type' => 'Select Type',
    'type_rack' => 'Rack',
    'type_shelf' => 'Shelf',
    'type_bin' => 'Bin',
    'type_floor' => 'Floor',
    'type_refrigerator' => 'Refrigerator',
    'type_counter' => 'Counter',
    'type_warehouse' => 'Warehouse',

    // Fields
    'code' => 'Location Code',
    'name' => 'Location Name',
    'name_example' => 'e.g., Main Storage Rack 1',
    'parent_location' => 'Parent Location',
    'none_top_level' => 'None (Top Level)',
    'capacity' => 'Capacity',
    'capacity_help' => 'Maximum number of product batches this location can hold',
    'current_occupancy' => 'Current occupancy',
    'occupancy' => 'Occupancy',
    'temperature_controlled' => 'Temperature Controlled',
    'min_temp' => 'Min Temperature',
    'max_temp' => 'Max Temperature',
    'notes' => 'Notes',
    'notes_placeholder' => 'Optional notes about this location',
    'is_active' => 'Active',

    // Auto-generation
    'auto_generated' => 'Auto-generated',
    'will_be_auto_generated' => 'Will be auto-generated',
    'code_help' => 'Location code will be automatically generated based on type and hierarchy',
    'code_readonly' => 'Location code cannot be changed',
    'type_readonly_has_children' => 'Type cannot be changed because this location has sub-locations',
    'parent_readonly_has_children' => 'Parent cannot be changed because this location has sub-locations',
    'parent_help' => 'Create a hierarchical structure by selecting a parent location',

    // Statistics
    'total_batches' => 'Total Batches',
    'occupied' => 'Occupied',
    'of' => 'of',
    'locations' => 'locations',
    'alerts' => 'Alerts',
    'need_attention' => 'need attention',
    'across_all_locations' => 'across all locations',
    'attention_required' => 'Attention Required',
    'and_more' => 'and :count more...',

    // Search and filter
    'search_locations' => 'Search locations by code, name, or product...',

    // Location hierarchy
    'location_hierarchy' => 'Location Hierarchy',
    'no_locations' => 'No storage locations',
    'get_started' => 'Get started by creating your first storage location',
    'unlimited' => 'Unlimited',
    'no_capacity_limit' => 'No capacity limit',
    'unique_products' => 'unique products',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',

    // Products in location
    'products_stored' => 'Products Stored Here',
    'batch_count' => 'Batches',
    'total_quantity' => 'Total Quantity',
    'oldest_expiry' => 'Oldest Expiry',
    'no_products_stored' => 'No products stored here',
    'no_products_desc' => 'This location is empty. Products will appear here when batches are assigned.',

    // Sub-locations
    'sub_locations' => 'Sub-Locations',

    // Stock movements
    'recent_movements' => 'Recent Stock Movements',
    'date' => 'Date',
    'from' => 'From',
    'to' => 'To',
    'reason' => 'Reason',
    'quantity' => 'Quantity',
    'external' => 'External',

    // Details
    'details' => 'Location Details',
    'full_path' => 'Full Path',
    'created_at' => 'Created At',
    'last_updated' => 'Last Updated',

    // Delete
    'danger_zone' => 'Danger Zone',
    'delete_warning' => 'Once you delete a location, there is no going back. Please be certain.',
    'confirm_delete' => 'Are you sure you want to delete this location? This action cannot be undone.',

    // Optional
    'optional' => 'Optional',

    // Assignment modal
    'assign_location' => 'Assign Location',
    'assign_location_desc' => 'Assign batch to a storage location',
    'batch' => 'Batch',
    'batches' => 'batches',
    'select_location' => 'Select Location',
    'suggested_location' => 'Suggested Location',
    'accept_suggestion' => 'Use Suggested',
    'choose_different' => 'Choose Different',
    'location_assigned' => 'Location assigned successfully',
    'assignment_notes' => 'Assignment Notes',

    // Suggestions
    'suggestion_same_product' => 'Groups with existing batches of this product',
    'suggestion_available_space' => 'Has available space',
    'suggestion_temperature' => 'Meets temperature requirements',

    // Auto assignment
    'auto_assign' => 'Auto-assign (Recommended)',
];
