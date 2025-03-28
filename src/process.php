<?php
session_start();

// Define the admin email and confirmation email subject
$admin_email = "help@helloatomtech.com"; // Replace with your admin email
$subject_admin = "Help Request - AtomTech";
$subject_user = "Confirmation of Your Help Request - AtomTech";

// Rate Limiting: Check if user is submitting too quickly (e.g., within 10 seconds)
$time_difference = time() - $_SESSION['last_submit_time'] ?? 0;

if ($time_difference < 10) {
  // Display an alert and exit the script
  echo "<script>alert('Please wait before submitting again.'); window.history.back();</script>";
  exit;
}

$_SESSION['last_submit_time'] = time(); // Update the last submission time

// Collect form data
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$existingClient = trim($_POST['existingClient']);
$contactMethods = isset($_POST['contact']) ? implode(", ", $_POST['contact']) : 'None';
$services = isset($_POST['services']) ? implode(", ", $_POST['services']) : 'None';
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

if (empty($phone) || !preg_match("/^\d{10}$/", $phone)) {
  echo "<script>alert('Please enter a valid phone number (10 digits).'); window.history.back();</script>";
  exit;
}

if (empty($existingClient)) {
  echo "<script>alert('Please indicate if you are an existing client.'); window.history.back();</script>";
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
  Phone: $phone
  Existing Client: $existingClient
  Contact Methods: $contactMethods
  Services Requested: $services
  Message: $message
";

// User confirmation email content
$user_message = "
  Hello $name!

  Thanks for reaching out. Typically you can expect a response within a few hours. If not, please know it could take up to 24-hours to reply. If this is an emergency please give me a call at (413) 497-8324. I look forward to helping you as soon as possible. Please do not reply to this email, it is monitored for incoming help requests via our contact form only.

  Here is a copy of the message you sent for your records:

  Name: $name
  Email: $email
  Phone: $phone
  Existing Client: $existingClient
  Contact Methods: $contactMethods
  Services Requested: $services
  Message: $message

  Best regards,
  AJ - AtomTech
  helloatomtech.com | help@helloatomtech.com | 413.497.8324
";

// Admin headers
$headers_admin = "From: help@helloatomtech.com\r\n";
$headers_admin .= "Reply-To: $email\r\n";
$headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";

// User headers
$headers_user = "From: help@helloatomtech.com\r\n";
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
  echo "<script>alert('There was an error sending your form details to the admin.'); window.history.back();</script>";
  exit;
}
