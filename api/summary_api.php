<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your database connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getBudgetById($conn, $_GET['id']);
    } else {
        getAllBudgets($conn);
    }
} elseif ($method == 'POST') {
    createBudget($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateBudget($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for update"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteBudget($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

// Function to calculate total amount
function calculateTotalAmount($quantity, $unit_price) {
    return $quantity * $unit_price;
}

// Function to get all budget items with count
function getAllBudgets($conn) {
    // Query to get all budget items
    $result = $conn->query("SELECT * FROM budget_summary");
    $budgets = [];

    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }

    // Query to get the total count of budget items
    $countResult = $conn->query("SELECT COUNT(*) as total_count FROM budget_summary");
    $countRow = $countResult->fetch_assoc();
    $totalCount = $countRow['total_count'];

    // Return response with count
    echo json_encode([
        "count" => $totalCount,
        "budgets" => $budgets
    ]);
}


// Function to get a budget item by its ID
function getBudgetById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM budget_summary WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Budget item not found"]);
    }
}

// Function to create a new budget item
function createBudget($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate incoming data
    if (!isset($data['item']) || empty(trim($data['item'])) ||
        !isset($data['description']) || empty(trim($data['description'])) ||
        !isset($data['quantity']) || !is_numeric($data['quantity']) ||
        !isset($data['unit_price']) || !is_numeric($data['unit_price'])) {
        echo json_encode(["error" => "All fields (item, description, quantity, unit_price) are required"]);
        return;
    }

    // Calculate total amount using function
    $total_amount = calculateTotalAmount($data['quantity'], $data['unit_price']);

    // Insert data into the Budget_Summary table
    $stmt = $conn->prepare("INSERT INTO budget_summary (item, description, quantity, unit_price, total_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidd", $data['item'], $data['description'], $data['quantity'], $data['unit_price'], $total_amount);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Budget item created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create budget item"]);
    }
}

// Function to update an existing budget item
function updateBudget($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate incoming data
    if (!isset($data['item']) || empty(trim($data['item'])) ||
        !isset($data['description']) || empty(trim($data['description'])) ||
        !isset($data['quantity']) || !is_numeric($data['quantity']) ||
        !isset($data['unit_price']) || !is_numeric($data['unit_price'])) {
        echo json_encode(["error" => "All fields (item, description, quantity, unit_price) are required"]);
        return;
    }

    // Calculate total amount using function
    $total_amount = calculateTotalAmount($data['quantity'], $data['unit_price']);

    // Update the budget item in the database
    $stmt = $conn->prepare("UPDATE budget_summary SET item = ?, description = ?, quantity = ?, unit_price = ?, total_amount = ? WHERE id = ?");
    $stmt->bind_param("ssiddi", $data['item'], $data['description'], $data['quantity'], $data['unit_price'], $total_amount, $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["message" => "Budget item updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update budget item or no changes made"]);
    }
}

// Function to delete a budget item
function deleteBudget($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM budget_summary WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Budget item deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete budget item"]);
    }
}
?>
