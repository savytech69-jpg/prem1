<?php
// Enhanced booking and career application handler with email functionality
// Handles both service bookings and career applications

// Configuration
$TO_EMAIL = 'savytech69@gmail.com';
$FROM_EMAIL = 'noreply@premiersalon.com';
$FROM_NAME = 'Premier Family Salon';

// Helpers
function respond_json($status, $message, $extra = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

function sanitize($val) {
    return trim(htmlspecialchars($val, ENT_QUOTES, 'UTF-8'));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_phone($phone) {
    // Indian phone number validation
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return preg_match('/^(\+91)?[6-9]\d{9}$/', $phone);
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond_json('error', 'Method Not Allowed');
}

// Honeypot check
if (!empty($_POST['company'])) {
    http_response_code(204);
    exit;
}

// Determine form type
$form_type = sanitize($_POST['form_type'] ?? 'booking');

if ($form_type === 'career_application') {
    // Career Application Form
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $age = sanitize($_POST['age'] ?? '');
    $education = sanitize($_POST['education'] ?? '');
    $program = sanitize($_POST['program'] ?? '');
    $batch = sanitize($_POST['batch'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');

    // Validation
    $errors = [];
    if (strlen($name) < 2) $errors[] = 'Full name is required (minimum 2 characters).';
    if (!validate_email($email)) $errors[] = 'Valid email address is required.';
    if (!validate_phone($phone)) $errors[] = 'Valid phone number is required.';
    if (empty($program)) $errors[] = 'Please select a program.';

    if (!empty($errors)) {
        respond_json('error', implode(' ', $errors));
    }

    // Prepare email content for career application
    $program_names = [
        'intermediate' => 'Intermediate Package - ₹30,000 (6 months)',
        'advanced' => 'Advanced Package - ₹99,000 (12 months)'
    ];

    $batch_names = [
        'morning' => 'Morning (9 AM - 12 PM)',
        'afternoon' => 'Afternoon (2 PM - 5 PM)',
        'evening' => 'Evening (6 PM - 9 PM)',
        'weekend' => 'Weekend (Sat-Sun)'
    ];

    $education_names = [
        '10th' => '10th Standard',
        '12th' => '12th Standard',
        'diploma' => 'Diploma',
        'graduate' => 'Graduate',
        'postgraduate' => 'Post Graduate'
    ];

    $email_subject = '🎓 New Career Application - ' . ($program_names[$program] ?? $program);
    
    $email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #b646ff, #ff3fb3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #b646ff; margin-bottom: 5px; display: block; }
        .value { background: white; padding: 10px; border-radius: 5px; border-left: 3px solid #b646ff; }
        .footer { text-align: center; margin-top: 20px; padding: 20px; color: #666; font-size: 12px; }
        .highlight { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🎓 New Career Application</h1>
            <p style='margin: 10px 0 0 0; opacity: 0.9;'>Premier Family Salon Training Program</p>
        </div>
        <div class='content'>
            <div class='highlight'>
                <strong>Program Applied:</strong> " . ($program_names[$program] ?? htmlspecialchars($program)) . "
            </div>

            <div class='field'>
                <span class='label'>👤 Applicant Name</span>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>

            <div class='field'>
                <span class='label'>📧 Email Address</span>
                <div class='value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
            </div>

            <div class='field'>
                <span class='label'>📱 Phone Number</span>
                <div class='value'><a href='tel:" . htmlspecialchars($phone) . "'>" . htmlspecialchars($phone) . "</a></div>
            </div>

            " . ($age ? "<div class='field'>
                <span class='label'>🎂 Age</span>
                <div class='value'>" . htmlspecialchars($age) . " years</div>
            </div>" : "") . "

            " . ($education ? "<div class='field'>
                <span class='label'>🎓 Education</span>
                <div class='value'>" . ($education_names[$education] ?? htmlspecialchars($education)) . "</div>
            </div>" : "") . "

            " . ($batch ? "<div class='field'>
                <span class='label'>🕐 Preferred Batch</span>
                <div class='value'>" . ($batch_names[$batch] ?? htmlspecialchars($batch)) . "</div>
            </div>" : "") . "

            " . ($message ? "<div class='field'>
                <span class='label'>💬 Why Join?</span>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>" : "") . "

            <div class='field'>
                <span class='label'>📅 Submission Date</span>
                <div class='value'>" . $timestamp . "</div>
            </div>

            <div class='field'>
                <span class='label'>🌐 IP Address</span>
                <div class='value'>" . $ip . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This application was submitted through the Premier Family Salon website career form.</p>
            <p>Please respond to the applicant within 24 hours.</p>
        </div>
    </div>
</body>
</html>
    ";

} else {
    // Service Booking Form
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $service = sanitize($_POST['service'] ?? '');
    $appointment_date = sanitize($_POST['appointment_date'] ?? '');
    $appointment_time = sanitize($_POST['appointment_time'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');

    // Validation
    $errors = [];
    if (strlen($name) < 2) $errors[] = 'Name is required (minimum 2 characters).';
    if (!validate_email($email)) $errors[] = 'Valid email address is required.';
    if (!validate_phone($phone)) $errors[] = 'Valid phone number is required.';
    if (empty($service)) $errors[] = 'Please select a service.';
    if (empty($appointment_date)) $errors[] = 'Appointment date is required.';

    if (!empty($errors)) {
        respond_json('error', implode(' ', $errors));
    }

    // Prepare email content for booking
    $email_subject = '📅 New Service Booking - ' . $service;
    
    $email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #b646ff, #ff3fb3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #b646ff; margin-bottom: 5px; display: block; }
        .value { background: white; padding: 10px; border-radius: 5px; border-left: 3px solid #b646ff; }
        .footer { text-align: center; margin-top: 20px; padding: 20px; color: #666; font-size: 12px; }
        .highlight { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>📅 New Service Booking</h1>
            <p style='margin: 10px 0 0 0; opacity: 0.9;'>Premier Family Salon & Hair Spa</p>
        </div>
        <div class='content'>
            <div class='highlight'>
                <strong>Service Requested:</strong> " . htmlspecialchars($service) . "
            </div>

            <div class='field'>
                <span class='label'>👤 Client Name</span>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>

            <div class='field'>
                <span class='label'>📧 Email Address</span>
                <div class='value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
            </div>

            <div class='field'>
                <span class='label'>📱 Phone Number</span>
                <div class='value'><a href='tel:" . htmlspecialchars($phone) . "'>" . htmlspecialchars($phone) . "</a></div>
            </div>

            <div class='field'>
                <span class='label'>📆 Preferred Date</span>
                <div class='value'>" . htmlspecialchars($appointment_date) . "</div>
            </div>

            " . ($appointment_time ? "<div class='field'>
                <span class='label'>🕐 Preferred Time</span>
                <div class='value'>" . htmlspecialchars($appointment_time) . "</div>
            </div>" : "") . "

            " . ($message ? "<div class='field'>
                <span class='label'>💬 Special Requests</span>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>" : "") . "

            <div class='field'>
                <span class='label'>📅 Booking Submitted</span>
                <div class='value'>" . $timestamp . "</div>
            </div>

            <div class='field'>
                <span class='label'>🌐 IP Address</span>
                <div class='value'>" . $ip . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This booking was submitted through the Premier Family Salon website.</p>
            <p>Please confirm the appointment with the client within 24 hours.</p>
        </div>
    </div>
</body>
</html>
    ";
}

// Send email with HTML content
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . $FROM_NAME . " <" . $FROM_EMAIL . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Log email attempt for debugging
error_log("Attempting to send email to: " . $TO_EMAIL . " | Subject: " . $email_subject);

$email_sent = mail($TO_EMAIL, $email_subject, $email_body, $headers);

if ($email_sent) {
    $success_message = $form_type === 'career_application' 
        ? 'Thank you for your application! We will contact you within 24 hours to discuss your enrollment.'
        : 'Booking request received! We will confirm your appointment within 24 hours.';
    
    error_log("Email sent successfully: " . $email_subject);
    respond_json('success', $success_message);
} else {
    error_log("Email send failed: " . $email_subject . " | Error: " . print_r(error_get_last(), true));
    respond_json('error', 'Failed to send email. Please try again or contact us directly.');
}
?>
