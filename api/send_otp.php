<?php
include 'db.php'; // Include database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(["status" => "error", "message" => "Missing email"]);
    exit();
}

$email = $data['email'];
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Save OTP in the database
$stmt = $conn->prepare("UPDATE users SET otp=?, otp_expiry=? WHERE email=?");
$stmt->bind_param("sss", $otp, $expiry, $email);
$stmt->execute();

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sanchezlando333@gmail.com';
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
