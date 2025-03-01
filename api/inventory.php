<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection (make sure to set up your database connection in this file)

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getProductById($conn, $_GET['id']);
    } else {
        getAllProducts($conn);
    }
} elseif ($method == 'POST') {
    createProduct($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateProduct($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for update"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteProduct($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

// Function to get all products
function getAllProducts($conn) {
    $result = $conn->query("SELECT * FROM inventory");
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
}

// Function to get a product by its ID
function getProductById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Product not found"]);
    }
}

// Function to create a new product
function createProduct($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate incoming data
    if (!isset($data['product_name']) || empty(trim($data['product_name'])) ||
        !isset($data['category']) || empty(trim($data['category'])) ||
        !isset($data['price']) || empty($data['price']) ||
        !isset($data['stock_quantity']) || empty($data['stock_quantity']) ||
        !isset($data['reorder_level']) || empty($data['reorder_level']) ||
        !isset($data['sku']) || empty($data['sku']) ||
        !isset($data['supplier']) || empty($data['supplier']) ||
        !isset($data['last_restocked']) || empty($data['last_restocked']) ||
        !isset($data['status']) || empty($data['status']) ||
        !isset($data['description']) || empty($data['description'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    // Insert data into the inventory table
    $stmt = $conn->prepare("INSERT INTO inventory (product_name, category, price, stock_quantity, reorder_level, sku, supplier, last_restocked, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiisssss", $data['product_name'], $data['category'], $data['price'], $data['stock_quantity'], $data['reorder_level'], $data['sku'], $data['supplier'], $data['last_restocked'], $data['status'], $data['description']);

    if ($stmt->execute()) {
        // Return the ID of the newly created product
        echo json_encode(["message" => "Product created successfully", "product_id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create product"]);
    }
}

// Function to update an existing product
function updateProduct($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate incoming data
    if (!isset($data['product_name']) || empty(trim($data['product_name'])) ||
        !isset($data['category']) || empty(trim($data['category'])) ||
        !isset($data['price']) || empty($data['price']) ||
        !isset($data['stock_quantity']) || empty($data['stock_quantity']) ||
        !isset($data['reorder_level']) || empty($data['reorder_level']) ||
        !isset($data['sku']) || empty($data['sku']) ||
        !isset($data['supplier']) || empty($data['supplier']) ||
        !isset($data['last_restocked']) || empty($data['last_restocked']) ||
        !isset($data['status']) || empty($data['status']) ||
        !isset($data['description']) || empty($data['description'])) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    // Update product in the database
    $stmt = $conn->prepare("UPDATE inventory SET product_name = ?, category = ?, price = ?, stock_quantity = ?, reorder_level = ?, sku = ?, supplier = ?, last_restocked = ?, status = ?, description = ? WHERE product_id = ?");
    $stmt->bind_param("ssdiisssssi", $data['product_name'], $data['category'], $data['price'], $data['stock_quantity'], $data['reorder_level'], $data['sku'], $data['supplier'], $data['last_restocked'], $data['status'], $data['description'], $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["message" => "Product updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update product or no changes made"]);
    }
}

// Function to delete a product
function deleteProduct($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM inventory WHERE product_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Product deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete product"]);
    }
}
?>
