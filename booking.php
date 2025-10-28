<?php
// Basic booking handler for cPanel (PHP 7+). Adjust mail settings as needed.
// NOTE: For production harden with CAPTCHA, rate limiting, server-side logging, and input length constraints.

// Configuration
$TO_EMAIL = 'info@premiersalon.example'; // Change to real destination
$SUBJECT_PREFIX = 'Salon Booking Request: ';
$ALLOW_ORIGINS = ['https://example.com','http://localhost']; // Adjust if using fetch() CORS in future

// Helpers
function respond_json($status, $message, $extra = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

function sanitize($val) {
    return trim(filter_var($val, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}

// Simple spam / method checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

// Honeypot
if (!empty($_POST['company'])) {
    // Silent discard
    http_response_code(204);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
$service = sanitize($_POST['service'] ?? '');
$appointment_date = sanitize($_POST['appointment_date'] ?? '');
$appointment_time = sanitize($_POST['appointment_time'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

$errors = [];
if (strlen($name) < 2) $errors[] = 'Name is required.';
if (!$email) $errors[] = 'Valid email required.';
if (!preg_match('/[0-9\-+() ]{7,}/', $phone)) $errors[] = 'Phone invalid.';
if ($service === '') $errors[] = 'Service required.';
if ($appointment_date === '') $errors[] = 'Date required.';
if ($appointment_time === '') $errors[] = 'Time required.';

// Basic date sanity (not in the past)
if ($appointment_date) {
    $today = new DateTime('today');
    $d = DateTime::createFromFormat('Y-m-d', $appointment_date);
    if (!$d) {
        $errors[] = 'Date format invalid.';
    } elseif ($d < $today) {
        $errors[] = 'Date must be today or later.';
    }
}

if ($errors) {
    // Graceful fallback: if JS form expects HTML redirect, show list
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        respond_json('error', 'Validation failed', ['errors' => $errors]);
    } else {
        echo '<!DOCTYPE html><html><body><h2>There were issues:</h2><ul>';
        foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
        echo '</ul><p><a href="contact.html">Go back</a></p></body></html>';
        exit;
    }
}

// Compose email
$subject = $SUBJECT_PREFIX . $service . ' - ' . $name;
$body = "Booking Request\n=================\n" .
        "Name: $name\n" .
        "Phone: $phone\n" .
        "Email: $email\n" .
        "Service: $service\n" .
        "Preferred Date: $appointment_date\n" .
        "Preferred Time: $appointment_time\n" .
        "Message: $message\n\n" .
        "Meta: IP=$ip UA=$ua\n";
$headers = [
    'From: noreply@premiersalon.example', // update with SPF-safe domain
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

$mailSent = @mail($TO_EMAIL, $subject, $body, implode("\r\n", $headers));

if (!$mailSent) {
    $failMsg = 'Could not send email at this time.';
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        respond_json('error', $failMsg);
    } else {
        echo '<!DOCTYPE html><html><body><h2>' . htmlspecialchars($failMsg) . '</h2><p>Please call us directly.</p><p><a href="contact.html">Back</a></p></body></html>';
        exit;
    }
}

// Success
if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
    respond_json('ok', 'Booking request received. We will confirm shortly.');
} else {
    echo '<!DOCTYPE html><html><body><h2>Thank you!</h2><p>Your booking request has been received. We will contact you to confirm.</p><p><a href="index.html">Return Home</a></p><a href="https://wa.me/9999" style="position:fixed;bottom:20px;right:20px;background:#25D366;color:#fff;padding:12px 15px;border-radius:50%;font-size:20px;text-decoration:none;line-height:1;box-shadow:0 4px 14px -4px rgba(0,0,0,.5);" aria-label="Chat on WhatsApp">W</a></body></html>';
}
