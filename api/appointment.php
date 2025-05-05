<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getAppointments($conn);
} elseif ($method === 'POST') {
    createAppointment($conn);
} elseif ($method === 'PUT') {
    updateAppointment($conn);
} elseif ($method === 'DELETE') {
    deleteAppointment($conn);
} else {
    response(405, "Invalid request method");
}

$conn->close();

function getAppointments($conn) {
    $result = $conn->query("SELECT * FROM appointments");
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    response(200, "Success", $appointments);
}

function createAppointment($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['visitor_name']) || empty(trim($data['visitor_name'])) ||
        !isset($data['visitor_email']) || empty(trim($data['visitor_email'])) ||
        !isset($data['appointment_date']) || !isset($data['appointment_time'])) {
        response(400, "All fields are required");
    }

    $stmt = $conn->prepare("INSERT INTO appointments (visitor_name, visitor_email, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['visitor_name'], $data['visitor_email'], $data['appointment_date'], $data['appointment_time']);

    if ($stmt->execute()) {
        response(201, "Appointment created successfully", ["id" => $conn->insert_id]);
    } else {
        response(500, "Failed to create appointment");
    }
}

function updateAppointment($conn) {
    parse_str(file_get_contents("php://input"), $data);
    
    if (!isset($data['id']) || !isset($data['status'])) {
        response(400, "Appointment ID and status are required");
    }

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $data['status'], $data['id']);

    if ($stmt->execute()) {
        response(200, "Appointment updated successfully");
    } else {
        response(500, "Failed to update appointment");
    }
}

function deleteAppointment($conn) {
    parse_str(file_get_contents("php://input"), $data);
    
    if (!isset($data['id'])) {
        response(400, "Appointment ID is required");
    }

    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $data['id']);

    if ($stmt->execute()) {
        response(200, "Appointment deleted successfully");
    } else {
        response(500, "Failed to delete appointment");
    }
}

function response($status, $message, $data = null) {
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    die();
}
?>
