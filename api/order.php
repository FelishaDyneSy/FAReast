<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getOrderById($conn, $_GET['id']);
    } else {
        getAllOrders($conn);
    }
} elseif ($method == 'POST') {
    createOrder($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateOrder($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteOrder($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

// Get all orders
function getAllOrders($conn) {
    $result = $conn->query("SELECT * FROM orders");
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
}

// Get order by ID
function getOrderById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Order not found"]);
    }
}

// Create new order
function createOrder($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, order_date, total_amount, order_status, payment_status, shipping_address, billing_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $data['customer_id'], $data['order_date'], $data['total_amount'], $data['order_status'], $data['payment_status'], $data['shipping_address'], $data['billing_address']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Order created successfully", "order_id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create order"]);
    }
}

// Update an order
function updateOrder($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("UPDATE orders SET customer_id = ?, order_date = ?, total_amount = ?, order_status = ?, payment_status = ?, shipping_address = ?, billing_address = ? WHERE order_id = ?");
    $stmt->bind_param("issssssi", $data['customer_id'], $data['order_date'], $data['total_amount'], $data['order_status'], $data['payment_status'], $data['shipping_address'], $data['billing_address'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Order updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update order"]);
    }
}

// Delete an order
function deleteOrder($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Order deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete order"]);
    }
}
?>
