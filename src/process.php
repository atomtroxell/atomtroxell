<?php
session_start();

// Define the admin email and confirmation email subject
$admin_email = "atomtroxell@gmail.com"; // Replace with your admin email
$subject_admin = "Website Message - AtomTroxell";
$subject_user = "Confirmation of Your Message - AtomTroxell";

// Rate Limiting: Check if user is submitting too quickly (e.g., within 10 seconds)
$time_difference = time() - $_SESSION['last_submit_time'] ?? 0;

if ($time_difference < 10) {
  // Display an alert and exit the script
  echo "<script>alert('Please wait before submitting again.'); window.history.back();</script>";
  exit;
}

$_SESSION['last_submit_time'] = time(); // Update the last submission time

// 1) Grab the token from POST
$turnstile_token = $_POST['cf-turnstile-response'] ?? '';

// 2) Verify with Cloudflare
$secret = '0x4AAAAAABapub6PTB_psf2zjDYCpe8sPZs';
$response = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
  'http' => [
    'method'  => 'POST',
    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
    'content' => http_build_query([
      'secret'   => $secret,
      'response' => $turnstile_token,
      'remoteip' => $_SERVER['REMOTE_ADDR'],
    ]),
  ]
]));

$result = json_decode($response, true);

// 3) If it failed, bail out
if (!isset($result['success']) || $result['success'] !== true) {
  echo "<script>alert('CAPTCHA verification failed. Please try again.'); window.history.back();</script>";
  exit;
}


// Collect form data
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$message = trim($_POST['message']);

// Honeypot: Check if the honeypot field was filled (this is a sign of bot activity)
if (!empty($_POST['honeypot'])) {
  echo "<script>alert('Spam submission detected.'); window.history.back();</script>";
  exit;
}

// Input Validation: Check for required fields and validate email and phone
if (empty($name)) {
  echo "<script>alert('Please enter your name.'); window.history.back();</script>";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
  exit;
}

if (empty($message)) {
  echo "<script>alert('Please provide some details.'); window.history.back();</script>";
  exit;
}

// Admin email content
$admin_message = "
  You have received a new form submission:

  Name: $name
  Email: $email
  Message: $message
";

// User confirmation email content
$user_message = "
  Hello $name!

  Thanks for reaching out. I'll get back to you as soon as I can!

  Here is a copy of the message you sent for your records:

  Best regards,
  AJ Troxell
  atomtroxell.com | www.atomtroxell.com
";

// Admin headers
$headers_admin = "From: atomtroxell@gmail.com\r\n";
$headers_admin .= "Reply-To: $email\r\n";
$headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";

// User headers
$headers_user = "From: atomtroxell@gmail.com.com\r\n";
$headers_user .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email to the admin
if (mail($admin_email, $subject_admin, $admin_message, $headers_admin)) {
  // Send confirmation email to the user
  if (mail($email, $subject_user, $user_message, $headers_user)) {
    // Redirect to the thank-you page
    header("Location: /thank-you");
    exit;
  } else {
    echo "<script>alert('There was an error sending the confirmation email.'); window.history.back();</script>";
    exit;
  }
} else {
  echo "<script>alert('There was an error sending your form.'); window.history.back();</script>";
  exit;
}
