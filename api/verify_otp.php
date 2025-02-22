<?php
include 'db.php'; // Include database connection
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['otp'])) {
    echo json_encode(["status" => "error", "message" => "Missing email or OTP"]);
    exit();
}

$email = $data['email'];
$otp = $data['otp'];

$stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result) {
    if ($result['otp'] == $otp && strtotime($result['otp_expiry']) > time()) {
        echo json_encode(["status" => "success", "message" => "OTP verified"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid or expired OTP"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
?>
