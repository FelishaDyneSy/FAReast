<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php"; // Ensure db.php contains your database connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['department_id'])) {
        $department_id = intval($_GET['department_id']);
        if ($department_id > 0) {
            getDocumentsByDepartment($conn, $department_id);
        } else {
            response(400, "Invalid department_id");
        }
    } elseif (isset($_GET['id'])) {
        getDocumentById($conn, $_GET['id']);
    } else {
        getAllDocuments($conn);
    }
} elseif ($method === 'POST') {
    createDocument($conn);
} elseif ($method === 'PUT') {
    if (isset($_GET['id'])) {
        updateDocument($conn, $_GET['id']);
    } else {
        response(400, "Document ID is required for updating");
    }
} elseif ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        deleteDocument($conn, $_GET['id']);
    } else {
        response(400, "Document ID is required for deletion");
    }
} else {
    response(405, "Invalid request method");
}

$conn->close();

function getAllDocuments($conn) {
    $result = $conn->query("SELECT id, title, department_id FROM documents"); // Removed content
    $documents = [];

    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }

    response(200, "Success", $documents);
}

function getDocumentsByDepartment($conn, $department_id) {
    $stmt = $conn->prepare("SELECT id, title, department_id FROM documents WHERE department_id = ?"); // Removed content
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $documents = [];
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }

    response(200, "Success", $documents);
}

function getDocumentById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, title, department_id FROM documents WHERE id = ?"); // Removed content
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        response(200, "Success", $result->fetch_assoc());
    } else {
        response(404, "Document not found");
    }
}

function createDocument($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['title']) || empty(trim($data['title']))) {
        response(400, "Document title is required");
    }
    if (!isset($data['department_id']) || !is_numeric($data['department_id'])) {
        response(400, "Valid department_id is required");
    }

    $stmt = $conn->prepare("INSERT INTO documents (title, department_id) VALUES (?, ?)"); // Removed content
    $stmt->bind_param("si", $data['title'], $data['department_id']);

    if ($stmt->execute()) {
        response(201, "Document created successfully", ["id" => $conn->insert_id]);
    } else {
        response(500, "Failed to create document");
    }
}

function updateDocument($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['title']) || empty(trim($data['title']))) {
        response(400, "Document title is required");
    }
    if (!isset($data['department_id']) || !is_numeric($data['department_id'])) {
        response(400, "Valid department_id is required");
    }

    $stmtCheck = $conn->prepare("SELECT id FROM documents WHERE id = ?");
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        response(404, "Document not found");
    }

    $stmt = $conn->prepare("UPDATE documents SET title = ?, department_id = ? WHERE id = ?"); // Removed content
    $stmt->bind_param("sii", $data['title'], $data['department_id'], $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        response(200, "Document updated successfully", ["updated_id" => $id]);
    } else {
        response(400, "No changes detected or update failed");
    }
}

function deleteDocument($conn, $id) {
    $stmtCheck = $conn->prepare("SELECT id, title FROM documents WHERE id = ?"); // Removed content
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        response(404, "Document not found");
    }

    $document = $resultCheck->fetch_assoc();

    $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        response(200, "Document deleted successfully", $document);
    } else {
        response(400, "Failed to delete document");
    }
}

function response($statusCode, $message, $data = null) {
    http_response_code($statusCode);
    echo json_encode([
        "status" => $statusCode,
        "message" => $message,
        "data" => $data
    ]);
    die();
}
?>
