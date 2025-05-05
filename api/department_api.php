<?php
header("Content-Type: application/json");
require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getDepartmentById($conn, $_GET['id']);
    } else {
        getAllDepartments($conn);
    }
} elseif ($method == 'POST') {
    createDepartment($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateDepartment($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
}
 elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteDepartment($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

function getAllDepartments($conn) {
    $result = $conn->query("SELECT * FROM departments");
    $departments = [];

    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }

    echo json_encode($departments);
}

function getDepartmentById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Department not found"]);
    }
}

function createDepartment($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || empty(trim($data['name']))) {
        echo json_encode(["error" => "Department name is required"]);
        return;
    }

    $departmentName = trim($data['name']);

    // Check if department already exists (case-insensitive)
    $stmt = $conn->prepare("SELECT id FROM departments WHERE UPPER(name) = UPPER(?)");
    $stmt->bind_param("s", $departmentName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["error" => "Department already exists"]);
        return;
    }
    
    $stmt->close();

    // Insert new department
    $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->bind_param("s", $departmentName);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Department created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create department"]);
    }

    $stmt->close();
}


function updateDepartment($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || empty(trim($data['name']))) {
        echo json_encode(["error" => "Department name is required"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $data['name'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Department updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update department"]);
    }
}


function deleteDepartment($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Department deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete department"]);
    }
}
?>
