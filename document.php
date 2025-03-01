<?php
include 'api/db.php';

// Start the session to access session variables
session_start();

// Get the document ID from the URL
$document_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get the userId from session
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0; // Ensure userId exists

$has_otp = false; // Initialize OTP as false

if ($userId > 0) {
    // Check if the user has an OTP
    $query = "SELECT otp FROM users WHERE id = ? AND otp IS NOT NULL AND otp <> ''";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId); // Bind user_id
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Debug OTP retrieval
    if ($row) {

        $has_otp = !empty($row['otp']); // If OTP exists, set to true
    } 
}


if ($document_id > 0) {
    // Fetch document details
    $sql_document = "
        SELECT d.id, d.title, d.content, dep.name AS department_name 
        FROM documents d
        LEFT JOIN departments dep ON d.department_id = dep.id
        WHERE d.id = ?";
        
    $stmt = $conn->prepare($sql_document);
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $document_result = $stmt->get_result();
    $document = $document_result->fetch_assoc();
    $stmt->close();

    if (!$document) {
        die("Document not found.");
    }
} else {
    die("Invalid document ID.");
}

$conn->close();
?> 






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['title']); ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <style>
    <?php if (!$has_otp): ?>
        #documentContent {
            filter: blur(10px);
            pointer-events: none;
            user-select: none;
            transition: filter 0.5s ease-in-out;
        }
    <?php else: ?>
        #documentContent {
            filter: none;
            pointer-events: auto;
            user-select: text;
        }
    <?php endif; ?>
</style>

</head>
<body>
<?php
    // Assuming $document is defined somewhere before this check
    if ($document['title'] === "Shipping & Delivery") :
    ?>



<div class="container mt-4">
        <h1><?php echo htmlspecialchars($document['title']); ?></h1>
        <p><strong>Department:</strong> <?php echo htmlspecialchars($document['department_name'] ?? 'Unknown'); ?></p>

        <!-- Privacy Modal -->
        <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="privacyModalLabel">Privacy Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document contains sensitive information. To access, you need an OTP.</p>
                        <p><strong>Click "Generate OTP" to proceed.</strong></p>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-around; align-items: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="generateOtp">Generate OTP</button>
                    </div>
                </div>
            </div>
        </div>

       <!-- OTP Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email to receive a One-Time Password (OTP):</p>
                        <input type="email" id="emailInput" class="form-control mb-2" placeholder="Enter your email">
                        <button class="btn btn-primary w-100" id="sendOtp">Send OTP</button>

                        <div id="otpSection" style="display:none;">
                            <p class="mt-3">Enter the OTP sent to your email:</p>
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="verifyOtp" style="display:none;">Verify OTP</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="documentContent" class="mt-4 <?php echo $has_otp ? 'blurred' : ''; ?>">
            <h2>Content</h2>
            <p><?php echo nl2br(htmlspecialchars($document['content'])); ?></p>

            <div class="table-responsive">
        <table id="shippingTable" class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Shipment ID</th>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Shipping Address</th>
                    <th>Shipping Method</th>
                    <th>Shipping Cost</th>
                    <th>Shipping Date</th>
                    <th>Estimated Delivery Date</th>
                    <th>Delivery Status</th>
                    <th>Tracking Number</th>
                    <th>Delivery Date</th>
                    <th>Delivery Notes</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted dynamically here -->
            </tbody>
        </table>
    </div>

    <p id="noDataMessage" class="alert alert-warning text-center mt-3" style="display: none;">
        No shipping information available.
    </p>
        </div>

    </div>

    <?php 
    elseif ($document['title'] === "Inventory & Stock") : 
    ?>


 <div class="container mt-4">
    <h1><?php echo htmlspecialchars($document['title']); ?></h1>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($document['department_name'] ?? 'Unknown'); ?></p>

     <!-- Privacy Modal -->
     <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="privacyModalLabel">Privacy Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document contains sensitive information. To access, you need an OTP.</p>
                        <p><strong>Click "Generate OTP" to proceed.</strong></p>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-around; align-items: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="generateOtp">Generate OTP</button>
                    </div>
                </div>
            </div>
        </div>

       <!-- OTP Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email to receive a One-Time Password (OTP):</p>
                        <input type="email" id="emailInput" class="form-control mb-2" placeholder="Enter your email">
                        <button class="btn btn-primary w-100" id="sendOtp">Send OTP</button>

                        <div id="otpSection" style="display:none;">
                            <p class="mt-3">Enter the OTP sent to your email:</p>
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="verifyOtp" style="display:none;">Verify OTP</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="documentContent" class="mt-4 <?php echo $has_otp ? 'blurred' : ''; ?>">
            <h2>Content</h2>
            <p><?php echo nl2br(htmlspecialchars($document['content'])); ?></p>


    <div class="table-responsive">
        <table id="inventoryTable" class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                   <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Reorder Level</th>
                    <th>SKU</th>
                    <th>Supplier</th>
                    <th>Last Restocked</th>
                    <th>Status</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <!-- Inventory Data will be inserted dynamically here -->
            </tbody>
        </table>
    </div>

    <p id="noInventoryDataMessage" class="alert alert-warning text-center mt-3" style="display: none;">
        No inventory information available.
    </p>
</div>


 <?php 
    elseif ($document['title'] === "Budget Summary") : 
    ?>

<div class="container mt-4">
    <h1><?php echo htmlspecialchars($document['title']); ?></h1>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($document['department_name'] ?? 'Unknown'); ?></p>

     <!-- Privacy Modal -->
     <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="privacyModalLabel">Privacy Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document contains sensitive information. To access, you need an OTP.</p>
                        <p><strong>Click "Generate OTP" to proceed.</strong></p>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-around; align-items: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="generateOtp">Generate OTP</button>
                    </div>
                </div>
            </div>
        </div>

       <!-- OTP Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email to receive a One-Time Password (OTP):</p>
                        <input type="email" id="emailInput" class="form-control mb-2" placeholder="Enter your email">
                        <button class="btn btn-primary w-100" id="sendOtp">Send OTP</button>

                        <div id="otpSection" style="display:none;">
                            <p class="mt-3">Enter the OTP sent to your email:</p>
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="verifyOtp" style="display:none;">Verify OTP</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="documentContent" class="mt-4 <?php echo $has_otp ? 'blurred' : ''; ?>">
            <h2>Content</h2>
            <p><?php echo nl2br(htmlspecialchars($document['content'])); ?></p>


    <div class="table-responsive">
        <table id="summary_table" class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- Inventory Data will be inserted dynamically here -->
            </tbody>
        </table>
    </div>

    <p id="noInventoryDataMessage" class="alert alert-warning text-center mt-3" style="display: none;">
        No inventory information available.
    </p>
</div>



<?php 
    elseif ($document['title'] === "Employee Records") : 
    ?>

 <div class="container mt-4">
    <h1><?php echo htmlspecialchars($document['title']); ?></h1>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($document['department_name'] ?? 'Unknown'); ?></p>

     <!-- Privacy Modal -->
     <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="privacyModalLabel">Privacy Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document contains sensitive information. To access, you need an OTP.</p>
                        <p><strong>Click "Generate OTP" to proceed.</strong></p>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-around; align-items: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="generateOtp">Generate OTP</button>
                    </div>
                </div>
            </div>
        </div>

       <!-- OTP Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email to receive a One-Time Password (OTP):</p>
                        <input type="email" id="emailInput" class="form-control mb-2" placeholder="Enter your email">
                        <button class="btn btn-primary w-100" id="sendOtp">Send OTP</button>

                        <div id="otpSection" style="display:none;">
                            <p class="mt-3">Enter the OTP sent to your email:</p>
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="verifyOtp" style="display:none;">Verify OTP</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="documentContent" class="mt-4 <?php echo $has_otp ? 'blurred' : ''; ?>">
            <h2>Content</h2>
            <p><?php echo nl2br(htmlspecialchars($document['content'])); ?></p>


    <div class="table-responsive">
        <table id="employee_table" class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Birth Date</th>
        <th>Contact</th>
        <th>Job Position</th>
        <th>Salary</th>
        <th>Department</th>
       
    </tr>
</thead>

            <tbody>
                <!-- Inventory Data will be inserted dynamically here -->
            </tbody>
        </table>
    </div>

    <p id="noInventoryDataMessage" class="alert alert-warning text-center mt-3" style="display: none;">
        No inventory information available.
    </p>
</div>




<?php 
    elseif ($document['title'] === "Accounting & Reports") : 
    ?>
 <div class="container mt-4">
    <h1><?php echo htmlspecialchars($document['title']); ?></h1>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($document['department_name'] ?? 'Unknown'); ?></p>

     <!-- Privacy Modal -->
     <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="privacyModalLabel">Privacy Notice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This document contains sensitive information. To access, you need an OTP.</p>
                        <p><strong>Click "Generate OTP" to proceed.</strong></p>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-around; align-items: center;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="generateOtp">Generate OTP</button>
                    </div>
                </div>
            </div>
        </div>

       <!-- OTP Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email to receive a One-Time Password (OTP):</p>
                        <input type="email" id="emailInput" class="form-control mb-2" placeholder="Enter your email">
                        <button class="btn btn-primary w-100" id="sendOtp">Send OTP</button>

                        <div id="otpSection" style="display:none;">
                            <p class="mt-3">Enter the OTP sent to your email:</p>
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="verifyOtp" style="display:none;">Verify OTP</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="documentContent" class="mt-4 <?php echo $has_otp ? 'blurred' : ''; ?>">
            <h2>Content</h2>
            <p><?php echo nl2br(htmlspecialchars($document['content'])); ?></p>


            <div class="table-responsive">
    <div class="row">
        <!-- Report Table -->
        <div class="col-md-6">
            <h4 class="text-center mt-3">Reports</h4> <!-- Label for Report Table -->
            <table id="report_table" class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Report Name</th>
                        <th>Details</th>
                        <th>Accounting ID</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Report data will go here -->
                </tbody>
            </table>
        </div>

        <!-- Accounting Table -->
        <div class="col-md-6">
            <h4 class="text-center mt-3">Accounting</h4> <!-- Label for Accounting Table -->
            <table id="accounting_table" class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Transaction Code</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Accounting data will go here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<p id="noInventoryDataMessage" class="alert alert-warning text-center mt-3" style="display: none;">
    No inventory information available.
</p>







    <?php
    
    else :
        echo "<p>No shipping information available.</p>";
    endif;
    ?>





















    <script>

         // Toastify function
         function showToast(message, type) {
            Toastify({
                text: message,
                style: {
                    background: type === 'success' 
                        ? "linear-gradient(to right, #00b09b, #96c93d)" 
                        : "linear-gradient(to right, #ff5f6d, #ffc371)"
                },
                duration: 3000,
                close: true
            }).showToast();
        }


        document.addEventListener("DOMContentLoaded", function () {
    // Assuming you have a way to check if hasOtp is true/false, 
    // for example, you can use a data attribute or fetch it from the server.
    
    const hasOtp = <?php echo json_encode($has_otp); ?>; // Get hasOtp value from PHP

    // Show modal only if no OTP is found
    if (!hasOtp) {
        showPrivacyModal();
    }
});

    function showPrivacyModal() {
        const privacyModal = new bootstrap.Modal(document.getElementById('privacyModal'));
        privacyModal.show();
    }

    function otpModal() {
        // Close the Privacy modal first
        const privacyModalEl = document.getElementById('privacyModal');
        const privacyModalInstance = bootstrap.Modal.getInstance(privacyModalEl);
        if (privacyModalInstance) {
            privacyModalInstance.hide();
        }

        // Show the OTP modal
        const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
        otpModal.show();
    }

    // Attach event listener to "Generate OTP" button
    document.getElementById('generateOtp').addEventListener('click', otpModal);

    function unlockContent() {
        document.getElementById("documentContent").classList.add("unlocked");
    }







    document.getElementById("sendOtp").addEventListener("click", async function () {
    const email = document.getElementById("emailInput").value.trim();
    if (!email) {
        alert("Please enter a valid email.");
        return;
    }

    try {
        const response = await fetch("http://localhost/far-east-cafe/api/send_otp.php", { // Adjust API URL
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email })
        });

        const data = await response.json();
        if (data.status === "success") {
            alert("OTP has been sent to your email.");
            document.getElementById("otpSection").style.display = "block"; // Show OTP input
            document.getElementById("verifyOtp").style.display = "inline-block"; // Show Verify OTP button
        } else {
            alert("Error: " + data.message);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to send OTP. Try again.");
    }
});

document.getElementById("verifyOtp").addEventListener("click", async function () {
    const email = document.getElementById("emailInput").value.trim();
    const otp = document.getElementById("otpInput").value.trim();

    if (!otp) {
        alert("Please enter the OTP.");
        return;
    }

    try {
        const response = await fetch("http://localhost/far-east-cafe/api/verify_otp.php", { // Adjust API URL
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, otp })
        });

        const data = await response.json();
        if (data.message) {  // Check if 'message' exists in the response
        alert("OTP verified successfully!");
        // You can add redirection or further processing here
        document.getElementById("otpModal").classList.remove("show");

        setTimeout(() => location.reload(), 1500); // Reload page after 1.5 seconds
    } else {
        alert(data.error || "OTP verification failed. Please try again.");  // Display error message if available
     }
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to verify OTP. Try again.");
    }
});




async function fetchShippingData() {
            try {
                const response = await fetch('http://localhost/far-east-cafe/api/delivery.php');
                const data = await response.json();

                const tableBody = document.querySelector("#shippingTable tbody");
                const noDataMessage = document.getElementById("noDataMessage");

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(shipment => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${shipment.shipment_id || 'N/A'}</td>
                            <td>${shipment.order_id || 'N/A'}</td>
                            <td>${shipment.customer_id || 'N/A'}</td>
                            <td>${shipment.shipping_address || 'N/A'}</td>
                            <td>${shipment.shipping_method || 'N/A'}</td>
                            <td>${shipment.shipping_cost || 'N/A'}</td>
                            <td>${shipment.shipping_date || 'N/A'}</td>
                            <td>${shipment.estimated_delivery_date || 'N/A'}</td>
                            <td>${shipment.delivery_status || 'N/A'}</td>
                            <td>${shipment.tracking_number || 'N/A'}</td>
                            <td>${shipment.delivery_date || 'N/A'}</td>
                            <td>${shipment.delivery_notes || 'N/A'}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    noDataMessage.style.display = "block";
                }
            } catch (error) {
                console.error("Error fetching shipping data:", error);
                document.getElementById("noDataMessage").style.display = "block";
            }
        }

        fetchShippingData();




        async function fetchInventoryData() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/inventory.php');
        const data = await response.json();

        const tableBody = document.querySelector("#inventoryTable tbody");
        const noDataMessage = document.getElementById("noInventoryMessage");

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(product => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${product.product_id || 'N/A'}</td>
                    <td>${product.product_name || 'N/A'}</td>
                    <td>${product.category || 'N/A'}</td>
                    <td>${product.price || 'N/A'}</td>
                    <td>${product.stock_quantity || 'N/A'}</td>
                    <td>${product.reorder_level || 'N/A'}</td>
                    <td>${product.sku || 'N/A'}</td>
                    <td>${product.supplier || 'N/A'}</td>
                    <td>${product.last_restocked || 'N/A'}</td>
                    <td>${product.status || 'N/A'}</td>
                    <td>${product.description || 'N/A'}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            noDataMessage.style.display = "block";
        }
    } catch (error) {
        console.error("Error fetching inventory data:", error);
        document.getElementById("noInventoryMessage").style.display = "block";
    }
}

// Call the function to fetch inventory data
fetchInventoryData();


async function fetchBudgetSummary() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/summary_api.php');
        const data = await response.json();

        const tableBody = document.querySelector("#summary_table tbody");
        const noDataMessage = document.getElementById("noInventoryMessage");

        tableBody.innerHTML = ""; // Clear previous data

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(product => {
                // Calculate Total Amount
                const totalAmount = (product.quantity * product.unit_price).toFixed(2);

                // Create a row dynamically
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.item}</td>
                    <td>${product.description}</td>
                    <td>${product.quantity}</td>
                    <td>${product.unit_price}</td>
                    <td>${totalAmount}</td> <!-- Display Total Amount -->
                `;

                tableBody.appendChild(row);
            });

            noDataMessage.style.display = "none";
        } else {
            noDataMessage.style.display = "block";
        }
    } catch (error) {
        console.error("Error fetching inventory data:", error);
        document.getElementById("noInventoryMessage").style.display = "block";
    }
}

// Fetch data on page load
document.addEventListener("DOMContentLoaded", fetchBudgetSummary);





async function fetchEmployee() {
    try {
        const response = await fetch('https://hr1.gwamerchandise.com/api/employee');
        const data = await response.json();

        const tableBody = document.querySelector("#employee_table tbody");
        const noDataMessage = document.getElementById("noInventoryMessage");

        tableBody.innerHTML = ""; // Clear previous data

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(employee => {
                // Create a row dynamically
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${employee.id}</td>
                    <td>${employee.first_name}</td>
                    <td>${employee.middle_name}</td>
                    <td>${employee.last_name}</td>
                    <td>${employee.email}</td>
                    <td>${employee.gender}</td>
                    <td>${employee.birth_date}</td>
                    <td>${employee.contact}</td>
                    <td>${employee.job_position}</td>
                    <td>${employee.salary}</td>
                    <td>${employee.department}</td>
                   
                `;

                tableBody.appendChild(row);
            });

            noDataMessage.style.display = "none";
        } else {
            noDataMessage.style.display = "block";
        }
    } catch (error) {
        console.error("Error fetching employee data:", error);
        document.getElementById("noInventoryMessage").style.display = "block";
    }
}


// Fetch data on page load
document.addEventListener("DOMContentLoaded", fetchEmployee);




async function fetchReportData() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/reports.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        const tableBody = document.querySelector("#report_table tbody");

        tableBody.innerHTML = ""; // Clear previous data

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(report => {
                // Create a row dynamically
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>
                         ${report.id} 
                        
                    </td>
                    <td>
                    ${report.report_name}
                    </td>
                    <td>
                        ${report.details} 
                        
                    </td>
                    <td>
                    ${report.accounting_id}
                    </td>
                     <td>
                   ${new Date(report.created_at).toLocaleString()}
                    </td>
                `;

                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = "<tr><td colspan='2'>No report data available.</td></tr>";
        }
    } catch (error) {
        console.error("Error fetching report data:", error);
    }
}
document.addEventListener("DOMContentLoaded", fetchReportData);




async function fetchAccountingData() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/accounting.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        const tableBody = document.querySelector("#accounting_table tbody");

        tableBody.innerHTML = ""; // Clear previous data

        if (Array.isArray(data.transactions) && data.transactions.length > 0) {
            data.transactions.forEach(transaction => {
                // Create a row dynamically for each transaction
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>
                         ${transaction.id}
                      
                    </td>
                    <td>
                      ${transaction.transaction_code}
                    </td>
                    <td>
                        ${transaction.amount} 
                       
                    </td>
                    <td>
                   ${transaction.description} 
                    </td>
                    <td>
                      ${transaction.status} 
                    </td>
                    <td>
                  ${new Date(transaction.created_at).toLocaleString()}
                  </td>
                `;

                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = "<tr><td colspan='2'>No accounting data available.</td></tr>";
        }
    } catch (error) {
        console.error("Error fetching accounting data:", error);
    }
}

document.addEventListener("DOMContentLoaded", fetchAccountingData);
</script>

<script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <!-- bootstap bundle js -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <!-- slimscroll js -->
    <script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="assets/libs/js/main-js.js"></script>
    <!-- chart chartist js -->
    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <!-- sparkline js -->
    <script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <!-- morris js -->
    <script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="assets/vendor/charts/morris-bundle/morris.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    


</body>
</html>
