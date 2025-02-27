<?php
header("Content-Type: application/json");
require_once "db.php"; // Include your DB connection

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        getTransactionById($conn, $_GET['id']);
    } else {
        getAllTransactions($conn);
    }
} elseif ($method == 'POST') {
    createTransaction($conn);
} elseif ($method == 'PUT') {
    if (isset($_GET['id'])) {
        updateTransactionStatus($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for updating"]);
    }
} elseif ($method == 'DELETE') {
    if (isset($_GET['id'])) {
        deleteTransaction($conn, $_GET['id']);
    } else {
        echo json_encode(["error" => "ID is required for deletion"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();

function getAllTransactions($conn) {
    $result = $conn->query("SELECT * FROM accounting");
    
    $response = [
        "count" => $result->num_rows,
        "transactions" => []
    ];

    while ($row = $result->fetch_assoc()) {
        $response["transactions"][] = $row;
    }

    echo json_encode($response);
}

function getTransactionById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM accounting WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Transaction not found"]);
    }
}

function createTransaction($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['transaction_code']) || empty(trim($data['transaction_code'])) ||
        !isset($data['amount']) || empty(trim($data['amount'])) ||
        !isset($data['description']) || empty(trim($data['description']))) {
        echo json_encode(["error" => "All fields are required"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO accounting (transaction_code, amount, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $data['transaction_code'], $data['amount'], $data['description']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Transaction created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to create transaction"]);
    }
}

function updateTransactionStatus($conn, $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['status']) || !in_array($data['status'], ['approved', 'denied'])) {
        echo json_encode(["error" => "Invalid status"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE accounting SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $data['status'], $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Transaction updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update transaction"]);
    }
}

function deleteTransaction($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM accounting WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Transaction deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete transaction"]);
    }
}
?>
