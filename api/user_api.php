<?php
header("Content-Type: application/json");
require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getUserById($conn, $_GET['id']);
    } else {
        getAllUsers($conn);
    }
} elseif ($method == 'POST') {
    createUser($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateUser($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteUser($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

function getAllUsers($conn) {
    $result = $conn->query("SELECT users.id, users.name, users.email, users.department_id, users.role_id, 
                                   departments.name AS department_name, roles.name AS role_name 
                            FROM users 
                            JOIN departments ON users.department_id = departments.id 
                            JOIN roles ON users.role_id = roles.id");
    
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}

function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT users.id, users.name, users.email, users.password, users.department_id, users.role_id, 
                                   departments.name AS department_name, roles.name AS role_name 
                            FROM users 
                            JOIN departments ON users.department_id = departments.id 
                            JOIN roles ON users.role_id = roles.id 
                            WHERE users.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "User not found"]);
    }
}

function createUser($conn) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode(["error" => "Invalid JSON data", "received" => $json]);
        return;
    }

    if (!isset($data['name']) || empty(trim($data['name']))) {
        echo json_encode(["error" => "User name is required"]);
        return;
    }

    if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Valid email is required"]);
        return;
    }

    if (!isset($data['password']) || strlen($data['password']) < 6) {
        echo json_encode(["error" => "Password must be at least 6 characters"]);
        return;
    }

    if (!isset($data['department_id']) || !is_numeric($data['department_id'])) {
        echo json_encode(["error" => "Valid department_id is required"]);
        return;
    }

    if (!isset($data['role_id']) || !is_numeric($data['role_id'])) {
        echo json_encode(["error" => "Valid role_id is required"]);
        return;
    }

    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $data['email']);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        echo json_encode(["error" => "Email already exists"]);
        return;
    }

    $checkEmailStmt->close();

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, department_id, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $data['name'], $data['email'], $hashedPassword, $data['department_id'], $data['role_id']);
    

    if ($stmt->execute()) {
        echo json_encode(["message" => "User created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create user"]);
    }
}


function updateUser($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if JSON data is valid
    if (!$data) {
        echo json_encode(["error" => "Invalid JSON data"]);
        return;
    }

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, department_id = ?, role_id = ? WHERE id = ?");
    $stmt->bind_param("ssiii", $data['name'], $data['email'], $data['department_id'], $data['role_id'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update user"]);
    }
}


function deleteUser($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete user"]);
    }
}
?>
