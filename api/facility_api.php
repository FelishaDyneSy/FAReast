<?php
header("Content-Type: application/json");
require_once "db.php";



$method = $_SERVER['REQUEST_METHOD'];




// === ADMIN: Get all facilities ===
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'all_facilities') {
    $result = $conn->query("SELECT * FROM facilities");
    $facilities = [];
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
    echo json_encode($facilities);
    exit();
}

// === ADMIN: Delete facility ===
if ($method === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'delete_facility' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if the facility is in use
    $stmt = $conn->prepare("SELECT COUNT(*) FROM facility_requests WHERE facility_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(["error" => "Cannot delete facility. It is currently in use."]);
        exit();
    }

    // Proceed with deletion
    $stmt = $conn->prepare("DELETE FROM facilities WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Facility deleted."]);
    } else {
        echo json_encode(["error" => "Failed to delete facility."]);
    }

    exit();
}



// === ADMIN: View all requests ===
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'admin_view') {
    getAllRequests($conn);
    exit();
}

// === USER: View their own requests ===
if ($method === 'GET' && isset($_GET['user_id'])) {
    getUserRequests($conn, $_GET['user_id']);
    exit();
}

// === USER: Get available facilities ===
if ($method === 'GET' && !isset($_GET['action'])) {
    $sql = "SELECT * FROM facilities WHERE availability_status = 'available'";
    $result = $conn->query($sql);

    $facilities = [];
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }

    echo json_encode($facilities);
    exit();
}

// === USER: Submit a facility request ===
if ($method === 'POST' && !isset($_GET['action'])) {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("INSERT INTO facility_requests (user_id, facility_id, date_requested, purpose) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $data['user_id'], $data['facility_id'], $data['date_requested'], $data['purpose']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Request submitted successfully."]);
    } else {
        echo json_encode(["error" => "Failed to submit request."]);
    }

    exit();
}

// === ADMIN: Add a new facility ===
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add_facility') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || empty($data['name'])) {
        echo json_encode(["error" => "Facility name is required."]);
        exit();
    }

    $name = $data['name'];
    $status = $data['availability_status'] ?? 'available';
    $description = $data['description'] ?? null;

    $stmt = $conn->prepare("INSERT INTO facilities (name, availability_status, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $status, $description);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Facility added successfully."]);
    } else {
        echo json_encode(["error" => "Failed to add facility."]);
    }

    exit();
}



// === ADMIN: Update request status and notify user ===
if ($method === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'update_status') {
    updateRequestStatus($conn);
    exit();
}

// === Functions ===
function getAllRequests($conn) {
    $sql = "SELECT fr.*, f.name AS facility_name 
            FROM facility_requests fr 
            JOIN facilities f ON fr.facility_id = f.id 
            ORDER BY fr.date_requested DESC";
    $result = $conn->query($sql);

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    echo json_encode($requests);
}

function getUserRequests($conn, $userId) {
    $stmt = $conn->prepare("SELECT fr.*, f.name AS facility_name 
                            FROM facility_requests fr
                            JOIN facilities f ON fr.facility_id = f.id
                            WHERE fr.user_id = ?
                            ORDER BY fr.date_requested DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    echo json_encode($requests);
}

function updateRequestStatus($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'], $data['status'])) {
        echo json_encode(["error" => "Missing request ID or status"]);
        return;
    }

    $id = intval($data['id']);
    $status = $conn->real_escape_string($data['status']);

    $stmt = $conn->prepare("UPDATE facility_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Status updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update status"]);
    }
}

?>
