<?php
session_start();
header("Content-Type: application/json");
require 'db.php'; // connect to your db

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode(["error" => "Username is required"]);
    exit();
}

if (!isset($data['password']) || strlen($data['password']) < 6) {
    echo json_encode(["error" => "Password must be at least 6 characters"]);
    exit();
}

$name = trim($data['name']);
$password = trim($data['password']);

// Prepare SQL to fetch user by name
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.password, 
           u.department_id, d.name AS department_name, 
           u.role_id, r.name AS role_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.id
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.name = ?
");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if user exists
if (!$user) {
    echo json_encode(["error" => "Invalid username or password"]);
    exit();
}

// ✅ Use password_verify to check hash
if (!password_verify($password, $user['password'])) {
    echo json_encode(["error" => "Invalid username or password"]);
    exit();
}

// ✅ Set session data
$_SESSION['id'] = $user['id'];
$_SESSION['name'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['department_id'] = $user['department_id'];
$_SESSION['department_name'] = $user['department_name'] ?? '';
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['role_name'] = $user['role_name'] ?? '';

// Redirect user based on role
$role = strtolower(trim($user['role_name']));
if ($role === "admin") {
    $redirectUrl = "dashboard.php";
} elseif ($role === "visitor") {
    $redirectUrl = "visitorDashboard.php";
} else {
    $redirectUrl = "404Page.php";
}

// Send back JSON response
echo json_encode([
    "success" => true,
    "id" => $_SESSION['id'],
    "name" => $_SESSION['name'],
    "email" => $_SESSION['email'],
    "department_id" => $_SESSION['department_id'],
    "department_name" => $_SESSION['department_name'],
    "role_id" => $_SESSION['role_id'],
    "role_name" => $_SESSION['role_name'],
    "redirect" => $redirectUrl
]);
exit();
?>
