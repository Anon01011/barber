<?php
/**
 * InteliQ Mail Handler Endpoint
 * Processes AJAX / POST submissions from the InteliQ Landing Page Contact Form.
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
    exit;
}

// Load Configuration and Mailer
$configPath = __DIR__ . '/config.php';
$mailerPath = __DIR__ . '/SmtpMailer.php';

if (!file_exists($configPath) || !file_exists($mailerPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Configuration or Mailer engine file missing.'
    ]);
    exit;
}

$config = require $configPath;
require_once $mailerPath;

// Parse Input (Support both form-data and JSON payloads)
$input = $_POST;
if (empty($input)) {
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $jsonDecoded = json_decode($rawInput, true);
        if (is_array($jsonDecoded)) {
            $input = $jsonDecoded;
        }
    }
}

// Sanitize & Extract Fields
$fullName      = trim(htmlspecialchars($input['full_name'] ?? '', ENT_QUOTES, 'UTF-8'));
$businessEmail = trim(filter_var($input['business_email'] ?? '', FILTER_SANITIZE_EMAIL));
$phone         = trim(htmlspecialchars($input['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
$company       = trim(htmlspecialchars($input['company'] ?? '', ENT_QUOTES, 'UTF-8'));
$industry      = trim(htmlspecialchars($input['industry'] ?? 'Not Specified', ENT_QUOTES, 'UTF-8'));
$branchCount   = trim(htmlspecialchars($input['branch_count'] ?? '1 Branch (Free)', ENT_QUOTES, 'UTF-8'));
$userMessage   = trim(htmlspecialchars($input['message'] ?? '', ENT_QUOTES, 'UTF-8'));
$inquiryTopic  = trim(htmlspecialchars($input['inquiry_source'] ?? 'Book Free Demo', ENT_QUOTES, 'UTF-8'));

// Validation
$errors = [];
if (empty($fullName)) {
    $errors[] = 'Full Name is required.';
}
if (empty($businessEmail) || !filter_var($businessEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid Work Email address is required.';
}
if (empty($phone)) {
    $errors[] = 'Phone / WhatsApp number is required.';
}
if (empty($company)) {
    $errors[] = 'Company or Organization name is required.';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

// Format Admin Notification HTML Email
$submittedAt = date('d M Y, h:i A (T)');
$clientIp    = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

$adminHtmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Lead: {$inquiryTopic} - InteliQ</title>
</head>
<body style="margin: 0; padding: 24px; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #0d0d0d; color: #ffffff;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 620px; margin: 0 auto; background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
        <!-- Header -->
        <tr>
            <td style="padding: 28px 32px; background: linear-gradient(135deg, #1f0606 0%, #120202 100%); border-bottom: 2px solid #E42127;">
                <div style="float: right; background: #E42127; color: #ffffff; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                    {$inquiryTopic}
                </div>
                <h2 style="margin: 0; font-size: 22px; color: #ffffff; letter-spacing: -0.5px;">Inteli<span style="color: #E42127;">Q</span> Lead Notification</h2>
                <p style="margin: 6px 0 0; color: #8a8a8a; font-size: 13px;">New website inquiry received</p>
            </td>
        </tr>
        
        <!-- Source Badge Strip -->
        <tr>
            <td style="padding: 12px 32px; background-color: #1a0808; border-bottom: 1px solid #2d1111;">
                <span style="display: inline-block; font-size: 11px; font-weight: bold; color: #ff6b6e; text-transform: uppercase; letter-spacing: 1px;">Inquiry Topic / Action:</span>
                <strong style="color: #ffffff; font-size: 13px; margin-left: 6px; background: rgba(228, 33, 39, 0.2); border: 1px solid rgba(228,33,39,0.4); padding: 3px 10px; border-radius: 4px; display: inline-block;">{$inquiryTopic}</strong>
            </td>
        </tr>

        <!-- Details Table -->
        <tr>
            <td style="padding: 32px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="8" style="font-size: 14px; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #222;">
                        <td width="38%" style="color: #888; padding: 10px 0;">Inquiry Purpose:</td>
                        <td style="color: #ff8a8c; font-weight: bold; padding: 10px 0;">{$inquiryTopic}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td width="38%" style="color: #888; padding: 10px 0;">Prospect Name:</td>
                        <td style="color: #fff; font-weight: 600; padding: 10px 0;">{$fullName}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="color: #888; padding: 10px 0;">Work Email:</td>
                        <td style="padding: 10px 0;"><a href="mailto:{$businessEmail}" style="color: #E42127; text-decoration: none; font-weight: 600;">{$businessEmail}</a></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="color: #888; padding: 10px 0;">Phone / WhatsApp:</td>
                        <td style="padding: 10px 0;"><a href="tel:{$phone}" style="color: #fff; text-decoration: none; font-weight: 600;">{$phone}</a></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="color: #888; padding: 10px 0;">Organization:</td>
                        <td style="color: #fff; font-weight: 600; padding: 10px 0;">{$company}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="color: #888; padding: 10px 0;">Industry / Sector:</td>
                        <td style="color: #fff; padding: 10px 0;">{$industry}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #222;">
                        <td style="color: #888; padding: 10px 0;">Branch Scale:</td>
                        <td style="color: #22c55e; font-weight: 600; padding: 10px 0;">{$branchCount}</td>
                    </tr>
                    <tr>
                        <td style="color: #888; padding: 14px 0 6px;" colspan="2">Specific Requirements / Message:</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="background-color: #0d0d0d; border: 1px solid #222; border-radius: 6px; padding: 14px; color: #ddd; font-size: 13px; line-height: 1.6;">
                            {$userMessage}
                        </td>
                    </tr>
                </table>

                <!-- Actions CTA -->
                <div style="margin-top: 28px; text-align: center;">
                    <a href="mailto:{$businessEmail}?subject=Regarding%20Your%20InteliQ%20Inquiry%20- {$company}" style="background-color: #E42127; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">Reply to Prospect Directly &rarr;</a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 16px 32px; background-color: #0a0a0a; border-top: 1px solid #1f1f1f; text-align: center; color: #555; font-size: 11px;">
                Submitted on {$submittedAt} · IP: {$clientIp} · InteliQ Smart Queue Systems
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

// Customer Auto-Reply Email
$customerHtmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>We Received Your Request - InteliQ</title>
</head>
<body style="margin: 0; padding: 24px; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #0d0d0d; color: #ffffff;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background-color: #141414; border: 1px solid #2a2a2a; border-radius: 12px; overflow: hidden;">
        <tr>
            <td style="padding: 32px; background: linear-gradient(135deg, #1f0606 0%, #120202 100%); border-bottom: 2px solid #E42127; text-align: center;">
                <h1 style="margin: 0; font-size: 26px; color: #ffffff;">Inteli<span style="color: #E42127;">Q</span></h1>
                <p style="margin: 8px 0 0; color: #ff8a8c; font-size: 14px; font-weight: 600;">{$inquiryTopic} Request Confirmed</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px; font-size: 15px; line-height: 1.7; color: #cccccc;">
                <p style="margin-top: 0; color: #ffffff;">Dear <strong>{$fullName}</strong>,</p>
                <p>Thank you for reaching out to InteliQ regarding <strong>{$inquiryTopic}</strong> for <strong>{$company}</strong> ({$branchCount}).</p>
                <p>Our solution architecture team is currently reviewing your branch details and will reach out to you within <strong>24 business hours</strong> to guide you through your requirements.</p>
                
                <div style="margin: 28px 0; background-color: #1a0808; border-left: 3px solid #E42127; padding: 16px 20px; border-radius: 4px;">
                    <strong style="color: #ffffff; display: block; margin-bottom: 4px;">What Happens Next?</strong>
                    <span style="font-size: 13px; color: #aaa;">1. A queue consultant verifies your counter & kiosk requirements.<br>2. We configure your cloud / local instance with custom display boards.<br>3. Go live with zero hardware lock-in and dedicated queue analytics.</span>
                </div>

                <p style="font-size: 13px; color: #777;">Need immediate assistance? Feel free to call us at <a href="tel:+919130001927" style="color: #E42127; text-decoration: none;">+91 91300 01927</a> or email <a href="mailto:sales@tecnoviq.biz" style="color: #E42127; text-decoration: none;">sales@tecnoviq.biz</a>.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 16px 32px; background-color: #0a0a0a; border-top: 1px solid #1f1f1f; text-align: center; color: #555; font-size: 11px;">
                &copy; 2025 InteliQ · Smart Queue Management System
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

try {
    $mailer = new SmtpMailer($config);

    // 1. Send Admin Notification Email
    $adminSubject = "[Lead: " . $inquiryTopic . "] " . $company . " (" . $fullName . ")";
    $mailer->send(
        $config['admin_email'],
        $config['admin_name'],
        $adminSubject,
        $adminHtmlBody,
        $config['from_email'],
        $config['from_name'],
        $businessEmail
    );

    // 2. Send Customer Auto-Reply Email (if enabled)
    if (!empty($config['send_auto_reply'])) {
        try {
            $customerSubject = "Your Request for " . $inquiryTopic . " - InteliQ";
            $mailer->send(
                $businessEmail,
                $fullName,
                $customerSubject,
                $customerHtmlBody,
                $config['from_email'],
                $config['from_name'],
                $config['admin_email']
            );
        } catch (Exception $customerMailEx) {
            // Log customer mail error without failing the primary lead submission
            error_log("Customer auto-reply failed: " . $customerMailEx->getMessage());
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your request for a Free Branch setup has been submitted successfully. Our team will contact you shortly.',
        'data'    => [
            'prospect' => $fullName,
            'company'  => $company,
            'source'   => $inquirySource
        ]
    ]);

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Mailer Error: Could not send email. Please check SMTP configuration.',
        'error'   => $e->getMessage()
    ];

    if (!empty($config['debug_mode'])) {
        $response['smtp_logs'] = isset($mailer) ? $mailer->getLogs() : [];
    }

    http_response_code(500);
    echo json_encode($response);
}
