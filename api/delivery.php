<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getShippingDeliveryById($conn, $_GET['id']);
    } else {
        getAllShippingDeliveries($conn);
    }
} elseif ($method == 'POST') {
    createShippingDelivery($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateShippingDelivery($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteShippingDelivery($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

// Get all shipping deliveries
function getAllShippingDeliveries($conn) {
    $result = $conn->query("SELECT * FROM shipping_delivery");
    $shipping_deliveries = [];
    while ($row = $result->fetch_assoc()) {
        $shipping_deliveries[] = $row;
    }
    echo json_encode($shipping_deliveries);
}

// Get shipping delivery by ID
function getShippingDeliveryById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM shipping_delivery WHERE shipment_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Shipping delivery not found"]);
    }
}

// Create new shipping delivery
function createShippingDelivery($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validation: Check if necessary fields are present
    if (!isset($data['order_id']) || !isset($data['customer_id']) || !isset($data['shipping_address']) || !isset($data['shipping_method']) || !isset($data['shipping_cost']) || !isset($data['shipping_date']) || !isset($data['estimated_delivery_date']) || !isset($data['delivery_status']) || !isset($data['delivery_notes'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    // Prepare the SQL statement to insert data into shipping_delivery table
    $stmt = $conn->prepare("INSERT INTO shipping_delivery (order_id, customer_id, shipping_address, shipping_method, shipping_cost, shipping_date, estimated_delivery_date, delivery_status, delivery_notes, tracking_number, delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssssssss", $data['order_id'], $data['customer_id'], $data['shipping_address'], $data['shipping_method'], $data['shipping_cost'], $data['shipping_date'], $data['estimated_delivery_date'], $data['delivery_status'], $data['delivery_notes'], $data['tracking_number'], $data['delivery_date']);

    // Execute the query and check if the operation was successful
    if ($stmt->execute()) {
        echo json_encode(["message" => "Shipping delivery created successfully", "shipment_id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create shipping delivery"]);
    }
}

function updateShippingDelivery($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Ensure all fields are present
    if (!isset($data['order_id'], $data['customer_id'], $data['shipping_address'], $data['shipping_method'], $data['shipping_cost'], $data['shipping_date'], $data['estimated_delivery_date'], $data['delivery_status'], $data['delivery_notes'], $data['tracking_number'], $data['delivery_date'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    // Prepare the SQL statement to update shipping_delivery
    $stmt = $conn->prepare("UPDATE shipping_delivery SET order_id = ?, customer_id = ?, shipping_address = ?, shipping_method = ?, shipping_cost = ?, shipping_date = ?, estimated_delivery_date = ?, delivery_status = ?, delivery_notes = ?, tracking_number = ?, delivery_date = ? WHERE shipment_id = ?");

    // Ensure all optional fields are handled (null-safe)
    $data['tracking_number'] = $data['tracking_number'] ?? null;
    $data['delivery_date'] = $data['delivery_date'] ?? null;

    // Bind all 12 parameters (11 fields + shipment_id)
    $stmt->bind_param(
        "iisssssssssi",
        $data['order_id'],
        $data['customer_id'],
        $data['shipping_address'],
        $data['shipping_method'],
        $data['shipping_cost'],
        $data['shipping_date'],
        $data['estimated_delivery_date'],
        $data['delivery_status'],
        $data['delivery_notes'],
        $data['tracking_number'],
        $data['delivery_date'],
        $id // This is the shipment_id for WHERE clause
    );

    // Execute the query and check if the operation was successful
    if ($stmt->execute()) {
        echo json_encode(["message" => "Shipping delivery updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update shipping delivery", "details" => $stmt->error]);
    }

    $stmt->close();
}

// Delete shipping delivery
function deleteShippingDelivery($conn, $id) {
    // Prepare the SQL statement to delete a shipping delivery by its ID
    $stmt = $conn->prepare("DELETE FROM shipping_delivery WHERE shipment_id = ?");
    $stmt->bind_param("i", $id);

    // Execute the query and check if the operation was successful
    if ($stmt->execute()) {
        echo json_encode(["message" => "Shipping delivery deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete shipping delivery"]);
    }
}
?>
