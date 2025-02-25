<?php
include 'db.php'; // Include database connection
header("Content-Type: application/json");

$data = json_decode(file_get_contents('php://input'), true);

// Check if the required fields are present
if (!isset($data['email'], $data['otp'])) {
    $response = ['error' => 'Invalid input'];
} else {
    $email = $conn->real_escape_string($data['email']);
    $otp = $conn->real_escape_string($data['otp']);

    // Query to get the OTP and its expiry time from the otp_verification table
    $sql = "SELECT otp, otp_expiry FROM otp_verification WHERE email = '$email'";
    $result = $conn->query($sql);

    // Handle query results
    if ($result === false) {
        $response = ['error' => 'Query failed: ' . $conn->error];
    } elseif ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the OTP is valid and not expired
        if (strtotime($user['otp_expiry']) > time()) {
            if ($otp === $user['otp']) {
                // Insert OTP and otp_expiry into users table (no otp_verified flag)
                $sql_insert = "UPDATE users SET otp = '{$user['otp']}', otp_expiry = '{$user['otp_expiry']}' WHERE email = '$email'";

                // Execute the update and handle success or failure
                if ($conn->query($sql_insert) === TRUE) {
                    // Do not delete the OTP from otp_verification if verification is successful
                    $response = ['message' => 'OTP verified and transferred to users table.'];

                    // Now delete expired OTP entries after verification is successful
                    $conn->query("DELETE FROM otp_verification WHERE otp_expiry < NOW()");
                } else {
                    $response = ['error' => 'Database error: ' . $conn->error];
                }
            } else {
                $response = ['error' => 'Invalid OTP. Please try again.'];
            }
        } else {
            // OTP has expired
            $response = ['error' => 'OTP has expired. Please request a new OTP.'];

            // Automatically delete the OTP entry if expired
            $conn->query("DELETE FROM otp_verification WHERE email = '$email'");
        }
    } else {
        $response = ['error' => "No OTP found for the email address '$email'."];
    }
}

// Return the response as JSON
echo json_encode($response);
?>
