<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';
include 'db.php'; // Include database connection

header("Content-Type: application/json");

session_start(); // Start session to keep OTP in memory for a session

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(["status" => "error", "message" => "Missing email"]);
    exit();
}

$email = $data['email'];

// Generate OTP
$otp = rand(100000, 999999); // Generate OTP
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes")); // Set OTP expiry time

// Insert OTP into otp_verification table
$stmt = $conn->prepare("INSERT INTO otp_verification (email, otp, otp_expiry) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $otp, $expiry);
$stmt->execute();

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sanchezlando333@gmail.com'; // Use your email
    $mail->Password = 'uuhh gsuh vqoc etqa'; // ⚠️ Use App Passwords instead
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('sanchezlando333@gmail.com', 'Far East Cafe');
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your OTP code is: $otp";

    if ($mail->send()) {
        echo json_encode(["status" => "success", "message" => "OTP sent"]);
    } else {
        echo json_encode(["status" => "error", "message" => "OTP not sent"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
}
?>
