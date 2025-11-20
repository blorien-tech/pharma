<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute গ্রহণ করতে হবে।',
    'active_url' => ':attribute একটি বৈধ URL নয়।',
    'after' => ':attribute অবশ্যই :date এর পরের তারিখ হতে হবে।',
    'after_or_equal' => ':attribute অবশ্যই :date এর সমান বা পরের তারিখ হতে হবে।',
    'alpha' => ':attribute শুধুমাত্র অক্ষর থাকতে পারে।',
    'alpha_dash' => ':attribute শুধুমাত্র অক্ষর, সংখ্যা, ড্যাশ এবং আন্ডারস্কোর থাকতে পারে।',
    'alpha_num' => ':attribute শুধুমাত্র অক্ষর এবং সংখ্যা থাকতে পারে।',
    'array' => ':attribute একটি অ্যারে হতে হবে।',
    'before' => ':attribute অবশ্যই :date এর আগের তারিখ হতে হবে।',
    'before_or_equal' => ':attribute অবশ্যই :date এর সমান বা আগের তারিখ হতে হবে।',
    'between' => [
        'numeric' => ':attribute অবশ্যই :min এবং :max এর মধ্যে হতে হবে।',
        'file' => ':attribute অবশ্যই :min এবং :max কিলোবাইটের মধ্যে হতে হবে।',
        'string' => ':attribute অবশ্যই :min এবং :max অক্ষরের মধ্যে হতে হবে।',
        'array' => ':attribute অবশ্যই :min এবং :max আইটেমের মধ্যে হতে হবে।',
    ],
    'boolean' => ':attribute ক্ষেত্রটি সত্য বা মিথ্যা হতে হবে।',
    'confirmed' => ':attribute নিশ্চিতকরণ মিলছে না।',
    'date' => ':attribute একটি বৈধ তারিখ নয়।',
    'date_equals' => ':attribute অবশ্যই :date এর সমান তারিখ হতে হবে।',
    'date_format' => ':attribute ফর্ম্যাট :format এর সাথে মিলছে না।',
    'different' => ':attribute এবং :other ভিন্ন হতে হবে।',
    'digits' => ':attribute অবশ্যই :digits সংখ্যার হতে হবে।',
    'digits_between' => ':attribute অবশ্যই :min এবং :max সংখ্যার মধ্যে হতে হবে।',
    'dimensions' => ':attribute অবৈধ ছবির মাত্রা আছে।',
    'distinct' => ':attribute ক্ষেত্রে ডুপ্লিকেট মান আছে।',
    'email' => ':attribute একটি বৈধ ইমেইল ঠিকানা হতে হবে।',
    'ends_with' => ':attribute অবশ্যই নিম্নলিখিত দিয়ে শেষ হতে হবে: :values।',
    'exists' => 'নির্বাচিত :attribute অবৈধ।',
    'file' => ':attribute একটি ফাইল হতে হবে।',
    'filled' => ':attribute ক্ষেত্রে একটি মান থাকতে হবে।',
    'gt' => [
        'numeric' => ':attribute অবশ্যই :value এর চেয়ে বড় হতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের চেয়ে বড় হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের চেয়ে বড় হতে হবে।',
        'array' => ':attribute অবশ্যই :value আইটেমের চেয়ে বেশি থাকতে হবে।',
    ],
    'gte' => [
        'numeric' => ':attribute অবশ্যই :value এর সমান বা বড় হতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের সমান বা বড় হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের সমান বা বড় হতে হবে।',
        'array' => ':attribute অবশ্যই :value আইটেম বা তার বেশি থাকতে হবে।',
    ],
    'image' => ':attribute একটি ছবি হতে হবে।',
    'in' => 'নির্বাচিত :attribute অবৈধ।',
    'in_array' => ':attribute ক্ষেত্রটি :other এ বিদ্যমান নেই।',
    'integer' => ':attribute একটি পূর্ণসংখ্যা হতে হবে।',
    'ip' => ':attribute একটি বৈধ IP ঠিকানা হতে হবে।',
    'ipv4' => ':attribute একটি বৈধ IPv4 ঠিকানা হতে হবে।',
    'ipv6' => ':attribute একটি বৈধ IPv6 ঠিকানা হতে হবে।',
    'json' => ':attribute একটি বৈধ JSON স্ট্রিং হতে হবে।',
    'lt' => [
        'numeric' => ':attribute অবশ্যই :value এর চেয়ে ছোট হতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের চেয়ে ছোট হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের চেয়ে ছোট হতে হবে।',
        'array' => ':attribute অবশ্যই :value আইটেমের চেয়ে কম থাকতে হবে।',
    ],
    'lte' => [
        'numeric' => ':attribute অবশ্যই :value এর সমান বা ছোট হতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের সমান বা ছোট হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের সমান বা ছোট হতে হবে।',
        'array' => ':attribute অবশ্যই :value আইটেমের বেশি থাকতে পারবে না।',
    ],
    'max' => [
        'numeric' => ':attribute :max এর চেয়ে বড় হতে পারবে না।',
        'file' => ':attribute :max কিলোবাইটের চেয়ে বড় হতে পারবে না।',
        'string' => ':attribute :max অক্ষরের চেয়ে বড় হতে পারবে না।',
        'array' => ':attribute :max আইটেমের চেয়ে বেশি থাকতে পারবে না।',
    ],
    'mimes' => ':attribute অবশ্যই এই ধরনের ফাইল হতে হবে: :values।',
    'mimetypes' => ':attribute অবশ্যই এই ধরনের ফাইল হতে হবে: :values।',
    'min' => [
        'numeric' => ':attribute কমপক্ষে :min হতে হবে।',
        'file' => ':attribute কমপক্ষে :min কিলোবাইট হতে হবে।',
        'string' => ':attribute কমপক্ষে :min অক্ষর হতে হবে।',
        'array' => ':attribute কমপক্ষে :min আইটেম থাকতে হবে।',
    ],
    'not_in' => 'নির্বাচিত :attribute অবৈধ।',
    'not_regex' => ':attribute ফর্ম্যাট অবৈধ।',
    'numeric' => ':attribute একটি সংখ্যা হতে হবে।',
    'password' => 'পাসওয়ার্ড ভুল।',
    'present' => ':attribute ক্ষেত্রটি উপস্থিত থাকতে হবে।',
    'regex' => ':attribute ফর্ম্যাট অবৈধ।',
    'required' => ':attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_if' => ':other যখন :value হয় তখন :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_unless' => ':other :values এ না থাকলে :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_with' => ':values উপস্থিত থাকলে :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_with_all' => ':values উপস্থিত থাকলে :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_without' => ':values উপস্থিত না থাকলে :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'required_without_all' => ':values কোনোটি উপস্থিত না থাকলে :attribute ক্ষেত্রটি প্রয়োজনীয়।',
    'same' => ':attribute এবং :other মিলতে হবে।',
    'size' => [
        'numeric' => ':attribute অবশ্যই :size হতে হবে।',
        'file' => ':attribute অবশ্যই :size কিলোবাইট হতে হবে।',
        'string' => ':attribute অবশ্যই :size অক্ষর হতে হবে।',
        'array' => ':attribute অবশ্যই :size আইটেম থাকতে হবে।',
    ],
    'starts_with' => ':attribute অবশ্যই নিম্নলিখিত দিয়ে শুরু হতে হবে: :values।',
    'string' => ':attribute একটি স্ট্রিং হতে হবে।',
    'timezone' => ':attribute একটি বৈধ জোন হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে নেওয়া হয়েছে।',
    'uploaded' => ':attribute আপলোড করতে ব্যর্থ হয়েছে।',
    'url' => ':attribute ফর্ম্যাট অবৈধ।',
    'uuid' => ':attribute একটি বৈধ UUID হতে হবে।',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'নাম',
        'email' => 'ইমেইল ঠিকানা',
        'password' => 'পাসওয়ার্ড',
        'phone' => 'ফোন নম্বর',
        'product_name' => 'পণ্যের নাম',
        'quantity' => 'পরিমাণ',
        'price' => 'মূল্য',
        'customer_name' => 'গ্রাহকের নাম',
        'amount' => 'পরিমাণ',
    ],

];
