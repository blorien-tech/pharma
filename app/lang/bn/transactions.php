<?php

return [
    'title' => 'লেনদেন',
    'subtitle' => 'সব বিক্রয় এবং রিটার্ন দেখুন এবং পরিচালনা করুন',

    // List
    'all_transactions' => 'সব লেনদেন',
    'recent_transactions' => 'সাম্প্রতিক লেনদেন',
    'today_transactions' => 'আজকের লেনদেন',
    'no_transactions' => 'কোনো লেনদেন পাওয়া যায়নি',
    'no_transactions_desc' => 'এখনও কোনো লেনদেন করা হয়নি',

    // Types
    'sale' => 'বিক্রয়',
    'return' => 'রিটার্ন',
    'type' => 'ধরন',

    // Details
    'transaction_details' => 'লেনদেনের বিবরণ',
    'transaction_id' => 'লেনদেন আইডি',
    'transaction_type' => 'লেনদেনের ধরন',
    'transaction_date' => 'লেনদেনের তারিখ',
    'transaction_time' => 'লেনদেনের সময়',

    // Items
    'items' => 'আইটেম',
    'item_details' => 'আইটেম বিবরণ',
    'product_name' => 'পণ্যের নাম',
    'sku' => 'এসকেইউ',
    'quantity' => 'পরিমাণ',
    'unit_price' => 'একক মূল্য',
    'line_total' => 'লাইন মোট',
    'no_items' => 'এই লেনদেনে কোনো আইটেম নেই',

    // Amounts
    'subtotal' => 'সাব-টোটাল',
    'discount' => 'ছাড়',
    'discount_percentage' => 'ছাড় %',
    'discount_amount' => 'ছাড়ের পরিমাণ',
    'total' => 'মোট',
    'grand_total' => 'সর্বমোট',
    'net_amount' => 'নিট পরিমাণ',

    // Payment
    'payment_method' => 'পেমেন্ট পদ্ধতি',
    'payment_status' => 'পেমেন্ট অবস্থা',
    'amount_paid' => 'পরিশোধিত পরিমাণ',
    'change_given' => 'ফেরত',
    'cash' => 'নগদ',
    'card' => 'কার্ড',
    'mobile_payment' => 'মোবাইল পেমেন্ট',
    'credit' => 'ক্রেডিট',
    'paid' => 'পরিশোধিত',
    'pending' => 'অপেক্ষমাণ',
    'refunded' => 'ফেরত দেওয়া হয়েছে',

    // Customer
    'customer' => 'গ্রাহক',
    'customer_name' => 'গ্রাহকের নাম',
    'customer_phone' => 'গ্রাহকের ফোন',
    'anonymous' => 'বেনামী',
    'walk_in_customer' => 'ওয়াক-ইন গ্রাহক',

    // Cashier/User
    'cashier' => 'ক্যাশিয়ার',
    'processed_by' => 'প্রক্রিয়াকারী',
    'user' => 'ব্যবহারকারী',

    // Actions
    'view_transaction' => 'লেনদেন দেখুন',
    'view_details' => 'বিবরণ দেখুন',
    'print_receipt' => 'রসিদ প্রিন্ট করুন',
    'print_invoice' => 'চালান প্রিন্ট করুন',
    'email_receipt' => 'রসিদ ইমেইল করুন',
    'process_return' => 'রিটার্ন প্রক্রিয়া করুন',
    'void_transaction' => 'লেনদেন বাতিল করুন',
    'refund' => 'ফেরত',

    // Returns
    'return_title' => 'রিটার্ন প্রক্রিয়া করুন',
    'return_reason' => 'রিটার্নের কারণ',
    'return_notes' => 'রিটার্ন নোট',
    'return_all_items' => 'সব আইটেম রিটার্ন করুন',
    'return_selected' => 'নির্বাচিত রিটার্ন করুন',
    'confirm_return' => 'রিটার্ন নিশ্চিত করুন',
    'return_processed' => 'রিটার্ন সফলভাবে প্রক্রিয়া করা হয়েছে',
    'cannot_return' => 'এই লেনদেন রিটার্ন করা যাবে না',
    'return_warning' => 'এটি এই লেনদেনের সব আইটেমের জন্য রিটার্ন প্রক্রিয়া করবে',

    // Filters
    'filter_type' => 'ধরন অনুযায়ী ফিল্টার করুন',
    'filter_payment' => 'পেমেন্ট পদ্ধতি অনুযায়ী ফিল্টার করুন',
    'filter_date' => 'তারিখ অনুযায়ী ফিল্টার করুন',
    'all_types' => 'সব ধরন',
    'all_payments' => 'সব পেমেন্ট পদ্ধতি',
    'date_range' => 'তারিখ পরিসীমা',
    'from_date' => 'তারিখ থেকে',
    'to_date' => 'তারিখ পর্যন্ত',
    'apply_filter' => 'ফিল্টার প্রয়োগ করুন',
    'reset_filter' => 'ফিল্টার রিসেট করুন',

    // Statistics
    'total_sales' => 'মোট বিক্রয়',
    'total_returns' => 'মোট রিটার্ন',
    'net_revenue' => 'নিট রাজস্ব',
    'transaction_count' => 'লেনদেন সংখ্যা',
    'average_sale' => 'গড় বিক্রয়',

    // Messages
    'transaction_not_found' => 'লেনদেন পাওয়া যায়নি',
    'return_success' => 'রিটার্ন সফলভাবে প্রক্রিয়া করা হয়েছে',
    'return_error' => 'রিটার্ন প্রক্রিয়া করতে ত্রুটি',
    'void_success' => 'লেনদেন সফলভাবে বাতিল করা হয়েছে',
    'void_error' => 'লেনদেন বাতিল করতে ত্রুটি',
    'receipt_sent' => 'রসিদ সফলভাবে পাঠানো হয়েছে',
    'confirm_void' => 'আপনি কি এই লেনদেন বাতিল করতে চান?',
    'cannot_be_undone' => 'এই কাজটি পূর্বাবস্থায় ফেরানো যাবে না',
];
