<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection (make sure to set up your database connection in this file)

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getCustomerById($conn, $_GET['id']);
    } else {
        getAllCustomers($conn);
    }
} elseif ($method == 'POST') {
    createCustomer($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateCustomer($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteCustomer($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

// Function to get all customers
function getAllCustomers($conn) {
    $result = $conn->query("SELECT * FROM customers");
    $customers = [];

    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }

    echo json_encode($customers);
}

// Function to get a customer by its ID
function getCustomerById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Customer not found"]);
    }
}

// Function to create a new customer
function createCustomer($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate input data
    if (!isset($data['first_name']) || empty(trim($data['first_name'])) ||
        !isset($data['last_name']) || empty(trim($data['last_name'])) ||
        !isset($data['email']) || empty($data['email']) ||
        !isset($data['phone']) || empty($data['phone']) ||
        !isset($data['shipping_address']) || empty($data['shipping_address']) ||
        !isset($data['billing_address']) || empty($data['billing_address']) ||
        !isset($data['registration_date']) || empty($data['registration_date'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, shipping_address, billing_address, registration_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['shipping_address'], $data['billing_address'], $data['registration_date']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Customer created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create customer"]);
    }
}

// Function to update an existing customer
function updateCustomer($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);



    $stmt = $conn->prepare("UPDATE customers SET first_name = ?, last_name = ?, email = ?, phone = ?, shipping_address = ?, billing_address = ?, registration_date = ? WHERE customer_id = ?");
    $stmt->bind_param("sssssssi", $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['shipping_address'], $data['billing_address'], $data['registration_date'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Customer updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update customer"]);
    }
}

// Function to delete a customer
function deleteCustomer($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Customer deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete customer"]);
    }
}
?>
