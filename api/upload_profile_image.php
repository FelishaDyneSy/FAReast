<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php'; // Database connection

$base_url = 'http://localhost/concept/api/uploads/'; // Base URL for uploaded images

// Handle File Upload (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_FILES['profile_picture']) || !isset($_POST['user_id'])) {
        echo json_encode(["success" => false, "message" => "Missing image or user ID"]);
        exit;
    }

    $userId = intval($_POST['user_id']);
    $uploadDir = "uploads/"; // Ensure this folder exists and is writable

    // Generate a unique file name
    $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
    $targetPath = $uploadDir . $fileName;
    $image_url = $base_url . $fileName; // Full URL of the uploaded image

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        // Update database
        $query = "UPDATE user_details SET profile_picture = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $image_url, $userId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Profile image uploaded successfully", "image_url" => $image_url]);
        } else {
            echo json_encode(["success" => false, "message" => "Database update failed"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "File upload failed"]);
    }
}

// Handle Base64 Image Update (PUT)
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id']) || !isset($data['profile_picture'])) {
        echo json_encode(["success" => false, "message" => "Missing image or user ID"]);
        exit;
    }

    $userId = intval($data['user_id']);
    $profile_picture = $data['profile_picture'];

    // Save Base64 Image
    $upload_result = saveBase64Image($profile_picture);
    if (isset($upload_result['error'])) {
        echo json_encode($upload_result);
        exit;
    }
    $image_url = $upload_result['file_path'];

    // Update database
    $query = "UPDATE user_details SET profile_picture = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $image_url, $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Profile picture updated successfully", "image_url" => $image_url]);
    } else {
        echo json_encode(["success" => false, "message" => "Database update failed"]);
    }

    $stmt->close();
}

$conn->close();

// Function to Save Base64 Image
function saveBase64Image($base64String)
{
    global $base_url;
    $uploadDir = "uploads/"; // Ensure this folder exists and is writable

    // Validate Base64 format
    if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
        return ["error" => "Invalid Base64 format. Must start with 'data:image/...;base64,'"];
    }

    // Extract file extension (e.g., jpeg, png)
    $imageType = $matches[1];
    $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
    $base64String = str_replace(' ', '+', $base64String); // Fix space encoding

    // Generate a unique file name
    $fileName = "profile_" . time() . "." . $imageType;
    $filePath = $uploadDir . $fileName;
    $fullUrl = $base_url . $fileName;

    // Decode and save image
    $imageData = base64_decode($base64String);
    if (!$imageData) {
        return ["error" => "Failed to decode Base64 image."];
    }

    if (!file_put_contents($filePath, $imageData)) {
        return ["error" => "Failed to save image file."];
    }

    return ["file_path" => $fullUrl];
}
?>
