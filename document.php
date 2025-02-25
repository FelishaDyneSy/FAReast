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
        var_dump($row); // Debug the OTP retrieval
        var_dump($row['otp']); // Check the actual OTP value
        $has_otp = !empty($row['otp']); // If OTP exists, set to true
    } else {
        var_dump("No OTP found for this user."); // Debug if no OTP is found
    }
}

// Debugging output (remove in production)
var_dump($has_otp);

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
                            <p id="otpError" class="text-danger mt-2" style="display:none;">Invalid OTP. Try again.</p>
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
        </div>

    </div>

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
        const response = await fetch("http://localhost/concept/api/send_otp.php", { // Adjust API URL
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
        const response = await fetch("http://localhost/concept/api/verify_otp.php", { // Adjust API URL
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, otp })
        });

        const data = await response.json();
        if (data.status === "success") {
            alert("OTP verified successfully!");
            // You can add redirection or further processing here
            document.getElementById("otpModal").classList.remove("show");
            document.querySelector(".modal-backdrop").remove();
            setTimeout(() => location.reload(), 1500);
        } else {
            document.getElementById("otpError").style.display = "block";
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to verify OTP. Try again.");
    }
});


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
