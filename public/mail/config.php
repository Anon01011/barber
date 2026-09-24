<?php
/**
 * InteliQ Mailer Configuration
 * Configure your SMTP server credentials and recipient details below.
 */

return [
    // SMTP Server Settings
    'smtp_host'       => 'smtp.gmail.com',         // e.g., smtp.gmail.com, mail.yourdomain.com, smtp.hostinger.com
    'smtp_port'       => 587,                      // 587 for TLS / STARTTLS, 465 for SSL, 25 for non-secure
    'smtp_encryption' => 'tls',                    // 'tls', 'ssl', or 'none'
    'smtp_username'   => 'your-email@gmail.com',   // Your SMTP username / email address
    'smtp_password'   => 'your-app-password',      // Your SMTP password / App password

    // Sender Details (From)
    'from_email'      => 'sales@tecnoviq.biz',
    'from_name'       => 'InteliQ Queue Systems',

    // Primary Recipient (Where inquiry emails will be delivered)
    'admin_email'     => 'sales@tecnoviq.biz',
    'admin_name'      => 'InteliQ Sales Team',

    // Notification Preferences
    'send_auto_reply' => true,                     // Send an instant confirmation email to the prospect
    'email_subject'   => 'New Free Branch Inquiry - InteliQ',
    'debug_mode'      => false,                    // Set to true to view SMTP connection logs in response
];
