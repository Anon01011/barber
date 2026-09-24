<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POS Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Point of Sale (POS) system.
    | You can modify these values to customize the behavior of your POS.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Tax Rate
    |--------------------------------------------------------------------------
    |
    | This value determines the default tax rate that will be applied to all
    | sales in the POS system. The value should be a number between 0 and 100.
    | Default is set to 10% (10.00).
    |
    */
    'tax_rate' => env('POS_TAX_RATE', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |
    | These values determine how currency is displayed throughout the POS.
    |
    */
    'currency' => [
        'symbol' => env('CURRENCY_SYMBOL', '$'),
        'code' => env('CURRENCY_CODE', 'USD'),
        'decimal_places' => env('CURRENCY_DECIMAL_PLACES', 2),
        'thousand_separator' => env('CURRENCY_THOUSAND_SEPARATOR', ','),
        'decimal_separator' => env('CURRENCY_DECIMAL_SEPARATOR', '.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Settings
    |--------------------------------------------------------------------------
    |
    | These values determine the default settings for receipts.
    |
    */
    'receipt' => [
        'show_logo' => true,
        'logo_path' => 'images/logo.png',
        'show_business_info' => true,
        'business_name' => env('APP_NAME', 'Salon CMS'),
        'business_address' => env('BUSINESS_ADDRESS', '123 Salon Street, City'),
        'business_phone' => env('BUSINESS_PHONE', '(123) 456-7890'),
        'business_email' => env('BUSINESS_EMAIL', 'info@example.com'),
        'show_tax_id' => true,
        'tax_id_label' => 'Tax ID',
        'tax_id_number' => env('TAX_ID_NUMBER', ''),
        'footer_text' => 'Thank you for your business!',
    ],

    /*
    |--------------------------------------------------------------------------
    | Barcode Settings
    |--------------------------------------------------------------------------
    |
    | These values determine how barcodes are generated and displayed.
    |
    */
    'barcode' => [
        'enabled' => true,
        'type' => 'C128', // C39, C128, EAN13, EAN8, UPCA, UPCE
        'width' => 2,
        'height' => 50,
        'text' => true, // Show text below barcode
    ],

    /*
    |--------------------------------------------------------------------------
    | Printer Settings
    |--------------------------------------------------------------------------
    |
    | These values determine the default printer settings for receipts.
    |
    */
    'printer' => [
        'enabled' => false,
        'printer_name' => env('PRINTER_NAME', 'POS-80'),
        'paper_width' => 80, // in characters
        'copy_count' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | These values determine the default settings for invoices.
    |
    */
    'invoice' => [
        'prefix' => 'INV-',
        'next_number' => 1000,
        'due_days' => 30,
        'terms' => 'Payment due within 30 days',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | These are the available payment methods in the POS system.
    | You can add, remove, or modify these as needed.
    |
    */
    'payment_methods' => [
        'cash' => [
            'label' => 'Cash',
            'enabled' => true,
            'requires_change' => true,
        ],
        'card' => [
            'label' => 'Credit/Debit Card',
            'enabled' => true,
            'processor' => env('CARD_PROCESSOR', 'stripe'), // stripe, square, etc.
            'require_cvv' => true,
        ],
        'bank_transfer' => [
            'label' => 'Bank Transfer',
            'enabled' => true,
            'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
            'account_name' => env('BANK_ACCOUNT_NAME', ''),
            'bank_name' => env('BANK_NAME', ''),
        ],
        'wallet' => [
            'label' => 'Wallet',
            'enabled' => true,
        ],
        'other' => [
            'label' => 'Other',
            'enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Customer
    |--------------------------------------------------------------------------
    |
    | These values determine the default customer used for walk-in sales.
    |
    */
    'default_customer' => [
        'name' => 'Walk-in Customer',
        'email' => 'walkin@example.com',
        'phone' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | These values determine security-related settings for the POS.
    |
    */
    'security' => [
        'void_requires_authorization' => true,
        'refund_requires_authorization' => true,
        'discount_requires_authorization' => true,
        'void_reasons' => [
            'Customer Cancellation',
            'Duplicate Transaction',
            'System Error',
            'Other',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | These values determine integration settings with other systems.
    |
    */
    'integrations' => [
        'accounting' => [
            'enabled' => false,
            'provider' => env('ACCOUNTING_PROVIDER', 'quickbooks'), // quickbooks, xero, etc.
            'sync_customers' => true,
            'sync_products' => true,
            'sync_invoices' => true,
            'sync_payments' => true,
        ],
        'inventory' => [
            'enabled' => true,
            'auto_update' => true,
            'alert_threshold' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Settings
    |--------------------------------------------------------------------------
    |
    | These values determine the UI behavior of the POS.
    |
    */
    'ui' => [
        'theme' => 'light', // light, dark, or system
        'show_images' => true,
        'show_descriptions' => true,
        'enable_sounds' => true,
        'default_view' => 'grid', // grid or list
        'items_per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Settings
    |--------------------------------------------------------------------------
    |
    | These values determine how POS data is backed up.
    |
    */
    'backup' => [
        'enabled' => true,
        'frequency' => 'daily', // daily, weekly, monthly
        'keep_backups' => 30, // Number of backups to keep
        'storage' => 'local', // local, s3, etc.
    ],
];
