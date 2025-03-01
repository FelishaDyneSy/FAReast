<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getReportById($conn, $_GET['id']);
    } elseif (isset($_GET['accounting_id'])) {
        getReportsByAccountingId($conn, $_GET['accounting_id']);
    } else {
        getAllReports($conn);
    }
} elseif ($method == 'POST') {
    createReport($conn);
}elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateReport($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for update"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteReport($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

function getAllReports($conn) {
    $result = $conn->query("SELECT * FROM reports");
    $reports = [];

    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    // Count total reports
    $countResult = $conn->query("SELECT COUNT(*) as total_count FROM reports");
    $countRow = $countResult->fetch_assoc();
    $totalCount = $countRow['total_count'];

    // Return JSON response
    echo json_encode([
        "count" => $totalCount,
        "reports" => $reports
    ]);
}


function updateReport($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['report_name']) || empty(trim($data['report_name'])) ||
        !isset($data['details']) || empty(trim($data['details'])) ||
        !isset($data['accounting_id']) || empty($data['accounting_id'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE reports SET report_name = ?, details = ?, accounting_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $data['report_name'], $data['details'], $data['accounting_id'], $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["message" => "Report updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update report or no changes made"]);
    }
}


function getReportsByAccountingId($conn, $accounting_id) {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE accounting_id = ?");
    $stmt->bind_param("i", $accounting_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    echo json_encode($reports);
}

function getReportById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Report not found"]);
    }
}

function createReport($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['report_name']) || empty(trim($data['report_name'])) ||
        !isset($data['details']) || empty(trim($data['details'])) ||
        !isset($data['accounting_id']) || empty($data['accounting_id'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO reports (report_name, details, accounting_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $data['report_name'], $data['details'], $data['accounting_id']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Report created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create report"]);
    }
}

function deleteReport($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Report deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete report"]);
    }
}
?>
