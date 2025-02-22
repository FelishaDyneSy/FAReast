<?php
header("Content-Type: application/json");
require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getRoleById($conn, $_GET['id']);
    } else {
        getAllRoles($conn);
    }
} elseif ($method == 'POST') {
    createRole($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateRole($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteRole($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

function getAllRoles($conn) {
    $result = $conn->query("SELECT roles.id, roles.name, roles.department_id, departments.name AS department_name FROM roles JOIN departments ON roles.department_id = departments.id");
    $roles = [];

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }

    echo json_encode($roles);
}

function getRoleById($conn, $id) {
    $stmt = $conn->prepare("SELECT roles.id, roles.name, roles.department_id, departments.name AS department_name FROM roles JOIN departments ON roles.department_id = departments.id WHERE roles.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Role not found"]);
    }
}

function createRole($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || empty(trim($data['name']))) {
        echo json_encode(["error" => "Role name is required"]);
        return;
    }

    if (!isset($data['department_id']) || !is_numeric($data['department_id'])) {
        echo json_encode(["error" => "Valid department_id is required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO roles (name, department_id) VALUES (?, ?)");
    $stmt->bind_param("si", $data['name'], $data['department_id']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Role created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create role"]);
    }
}

function updateRole($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || empty(trim($data['name']))) {
        echo json_encode(["error" => "Role name is required"]);
        return;
    }

    if (!isset($data['department_id']) || !is_numeric($data['department_id'])) {
        echo json_encode(["error" => "Valid department_id is required"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE roles SET name = ?, department_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $data['name'], $data['department_id'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Role updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update role"]);
    }
}

function deleteRole($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Role deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete role"]);
    }
}
?>
