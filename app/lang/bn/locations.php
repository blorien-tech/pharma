<?php

return [
    // Page titles
    'title' => 'স্টোরেজ লোকেশন',
    'subtitle' => 'আপনার স্টোরেজ লোকেশন পরিচালনা করুন এবং ইনভেন্টরি প্লেসমেন্ট ট্র্যাক করুন',
    'create_subtitle' => 'নতুন স্টোরেজ লোকেশন তৈরি করুন',
    'add_location' => 'লোকেশন যোগ করুন',
    'edit_location' => 'লোকেশন সম্পাদনা',
    'create_location' => 'লোকেশন তৈরি করুন',
    'update_location' => 'লোকেশন আপডেট করুন',
    'delete_location' => 'লোকেশন মুছুন',
    'view_location' => 'লোকেশন দেখুন',

    // Quick Hierarchy
    'quick_hierarchy' => 'দ্রুত হায়ারার্কি',
    'quick_hierarchy_desc' => 'একসাথে একাধিক লোকেশন তৈরি করুন',
    'rack_name' => 'র্যাক নাম',
    'rack_name_example' => 'যেমন, প্রধান র্যাক',
    'shelf_count' => 'শেলফের সংখ্যা',
    'bins_per_shelf' => 'প্রতি শেলফে বিন',
    'bin_capacity' => 'বিন ক্যাপাসিটি',
    'preview' => 'প্রিভিউ',
    'will_create' => 'এটি তৈরি করবে',
    'total_locations' => 'মোট লোকেশন',
    'rack' => 'র্যাক',
    'racks' => 'র্যাক',
    'shelf' => 'শেলফ',
    'shelves' => 'শেলফ',
    'bin' => 'বিন',
    'bins' => 'বিন',
    'create_hierarchy' => 'হায়ারার্কি তৈরি করুন',
    'hierarchy_created' => 'হায়ারার্কি সফলভাবে তৈরি হয়েছে!',

    // Location types
    'type' => 'ধরন',
    'select_type' => 'ধরন নির্বাচন করুন',
    'type_rack' => 'র্যাক',
    'type_shelf' => 'শেলফ',
    'type_bin' => 'বিন',
    'type_floor' => 'ফ্লোর',
    'type_refrigerator' => 'রেফ্রিজারেটর',
    'type_counter' => 'কাউন্টার',
    'type_warehouse' => 'গুদাম',

    // Fields
    'code' => 'লোকেশন কোড',
    'name' => 'লোকেশন নাম',
    'name_example' => 'যেমন, প্রধান স্টোরেজ র্যাক ১',
    'parent_location' => 'প্যারেন্ট লোকেশন',
    'none_top_level' => 'কোনটি নয় (শীর্ষ স্তর)',
    'capacity' => 'ক্যাপাসিটি',
    'capacity_help' => 'এই লোকেশনে সর্বোচ্চ কতটি প্রোডাক্ট ব্যাচ রাখা যাবে',
    'current_occupancy' => 'বর্তমান দখল',
    'occupancy' => 'দখল',
    'temperature_controlled' => 'তাপমাত্রা নিয়ন্ত্রিত',
    'min_temp' => 'সর্বনিম্ন তাপমাত্রা',
    'max_temp' => 'সর্বোচ্চ তাপমাত্রা',
    'notes' => 'নোট',
    'notes_placeholder' => 'এই লোকেশন সম্পর্কে ঐচ্ছিক নোট',
    'is_active' => 'সক্রিয়',

    // Auto-generation
    'auto_generated' => 'স্বয়ংক্রিয়ভাবে তৈরি',
    'will_be_auto_generated' => 'স্বয়ংক্রিয়ভাবে তৈরি হবে',
    'code_help' => 'লোকেশন কোড স্বয়ংক্রিয়ভাবে ধরন এবং হায়ারার্কির উপর ভিত্তি করে তৈরি হবে',
    'code_readonly' => 'লোকেশন কোড পরিবর্তন করা যাবে না',
    'type_readonly_has_children' => 'ধরন পরিবর্তন করা যাবে না কারণ এই লোকেশনের সাব-লোকেশন আছে',
    'parent_readonly_has_children' => 'প্যারেন্ট পরিবর্তন করা যাবে না কারণ এই লোকেশনের সাব-লোকেশন আছে',
    'parent_help' => 'একটি প্যারেন্ট লোকেশন নির্বাচন করে হায়ারার্কি তৈরি করুন',

    // Statistics
    'total_batches' => 'মোট ব্যাচ',
    'occupied' => 'দখলকৃত',
    'of' => 'এর',
    'locations' => 'লোকেশন',
    'alerts' => 'সতর্কতা',
    'need_attention' => 'মনোযোগ প্রয়োজন',
    'across_all_locations' => 'সমস্ত লোকেশন জুড়ে',
    'attention_required' => 'মনোযোগ প্রয়োজন',
    'and_more' => 'এবং আরও :count...',

    // Search and filter
    'search_locations' => 'কোড, নাম বা প্রোডাক্ট দ্বারা লোকেশন খুঁজুন...',

    // Location hierarchy
    'location_hierarchy' => 'লোকেশন হায়ারার্কি',
    'no_locations' => 'কোনো স্টোরেজ লোকেশন নেই',
    'get_started' => 'আপনার প্রথম স্টোরেজ লোকেশন তৈরি করে শুরু করুন',
    'unlimited' => 'সীমাহীন',
    'no_capacity_limit' => 'কোনো ক্যাপাসিটি সীমা নেই',
    'unique_products' => 'ইউনিক প্রোডাক্ট',
    'status' => 'স্ট্যাটাস',
    'active' => 'সক্রিয়',
    'inactive' => 'নিষ্ক্রিয়',

    // Products in location
    'products_stored' => 'এখানে সংরক্ষিত প্রোডাক্ট',
    'batch_count' => 'ব্যাচ',
    'total_quantity' => 'মোট পরিমাণ',
    'oldest_expiry' => 'পুরাতন মেয়াদ',
    'no_products_stored' => 'এখানে কোনো প্রোডাক্ট সংরক্ষিত নেই',
    'no_products_desc' => 'এই লোকেশন খালি। ব্যাচ নির্ধারণ করলে প্রোডাক্ট এখানে দেখা যাবে।',

    // Sub-locations
    'sub_locations' => 'সাব-লোকেশন',

    // Stock movements
    'recent_movements' => 'সাম্প্রতিক স্টক মুভমেন্ট',
    'date' => 'তারিখ',
    'from' => 'থেকে',
    'to' => 'প্রতি',
    'reason' => 'কারণ',
    'quantity' => 'পরিমাণ',
    'external' => 'বাহ্যিক',

    // Details
    'details' => 'লোকেশন বিবরণ',
    'full_path' => 'সম্পূর্ণ পাথ',
    'created_at' => 'তৈরি হয়েছে',
    'last_updated' => 'শেষ আপডেট',

    // Delete
    'danger_zone' => 'ডেঞ্জার জোন',
    'delete_warning' => 'একবার আপনি একটি লোকেশন মুছে ফেললে, ফিরে যাওয়ার কোনো উপায় নেই। অনুগ্রহ করে নিশ্চিত হন।',
    'confirm_delete' => 'আপনি কি নিশ্চিত এই লোকেশন মুছতে চান? এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না।',

    // Optional
    'optional' => 'ঐচ্ছিক',

    // Assignment modal
    'assign_location' => 'লোকেশন নির্ধারণ করুন',
    'assign_location_desc' => 'স্টোরেজ লোকেশনে ব্যাচ নির্ধারণ করুন',
    'batch' => 'ব্যাচ',
    'batches' => 'ব্যাচ',
    'select_location' => 'লোকেশন নির্বাচন করুন',
    'suggested_location' => 'প্রস্তাবিত লোকেশন',
    'accept_suggestion' => 'প্রস্তাবিত ব্যবহার করুন',
    'choose_different' => 'ভিন্ন নির্বাচন করুন',
    'location_assigned' => 'লোকেশন সফলভাবে নির্ধারণ করা হয়েছে',
    'assignment_notes' => 'নির্ধারণ নোট',

    // Suggestions
    'suggestion_same_product' => 'এই প্রোডাক্টের বিদ্যমান ব্যাচের সাথে গ্রুপ করা হয়েছে',
    'suggestion_available_space' => 'উপলব্ধ স্থান আছে',
    'suggestion_temperature' => 'তাপমাত্রা প্রয়োজনীয়তা পূরণ করে',
];
