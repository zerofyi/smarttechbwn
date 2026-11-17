<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free shipping threshold (₹)
    |--------------------------------------------------------------------------
    |
    | Orders with (subtotal - discount) at or above this value ship free.
    | Otherwise the flat shipping rate below applies. A zero subtotal
    | (empty cart) never charges shipping.
    |
    */
    'free_shipping_threshold' => env('SHOP_FREE_SHIPPING_THRESHOLD', 999),

    /*
    |--------------------------------------------------------------------------
    | Flat shipping rate (₹)
    |--------------------------------------------------------------------------
    */
    'shipping_flat' => env('SHOP_SHIPPING_FLAT', 49),

    /*
    |--------------------------------------------------------------------------
    | GST rate
    |--------------------------------------------------------------------------
    |
    | Store prices are GST-inclusive, so this rate is used only to break
    | the GST component out of an inclusive subtotal for display and for
    | invoices: tax = subtotal - subtotal / (1 + gst_rate).
    |
    */
    'gst_rate' => 0.18,

    /*
    |--------------------------------------------------------------------------
    | Cash on delivery
    |--------------------------------------------------------------------------
    */
    'cod_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Razorpay
    |--------------------------------------------------------------------------
    |
    | Empty key = Razorpay disabled; checkout shows COD only and nothing
    | that touches Razorpay may crash. Keys come from the environment only.
    |
    */
    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Return window (days from delivery)
    |--------------------------------------------------------------------------
    */
    'return_window_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Coupons
    |--------------------------------------------------------------------------
    |
    | type: percent (value = %, capped at max_discount) or flat (value = ₹).
    | A coupon only applies when the cart subtotal reaches min_subtotal.
    |
    */
    'coupons' => [
        'WELCOME10' => [
            'type' => 'percent',
            'value' => 10,
            'min_subtotal' => 999,
            'max_discount' => 500,
        ],
        'FLAT100' => [
            'type' => 'flat',
            'value' => 100,
            'min_subtotal' => 999,
        ],
    ],

];
