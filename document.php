<?php
include 'api/db.php';

session_start();
if (!isset($_SESSION['id']) || strtolower($_SESSION['department_name']) !== 'admin') {
    header("Location: login.html");
    exit();
}

$userId = $_SESSION['id'];

// Pass variables to JavaScript
echo "<script>
    const userId = " . json_encode($userId) . ";
    
</script>";
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
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    
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

    .close-icon {
    transition: transform 0.2s ease, color 0.2s ease; /* Smooth transition */
    color: #888; /* Default color */
}

.close-icon:hover {
    transform: scale(1.2); /* Slightly enlarge the icon */
    color: red; /* Change color on hover */
}
</style>

</head>
<body>

<div class="dashboard-main-wrapper">

<div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
                <a class="navbar-brand" href="index.php">Admin</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-right-top">
                        <li class="nav-item">
                            <div id="custom-search" class="top-search-bar">
                                <input class="form-control" type="text" placeholder="Search..">
                            </div>
                        </li>
                        <li class="nav-item dropdown notification">
                            <a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                                <li>
                                <div class="notification-title"> Notification</div>
                                
                    <div class="notification-list">
                        <div class="list-group" id="notificationContainer">
                            <!-- Notifications will be dynamically added here -->
                        </div>
                    </div>

                                </li>
                                <li>
                                    <div class="list-footer"> <a href="#">View all notifications</a></div>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown nav-user">
                        <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img id="profilePreview" src="" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name"> <?php echo htmlspecialchars($_SESSION['email']); ?></h5>
                                   
                                </div>
                                <a class="dropdown-item" href="account.php"><i class="fas fa-user mr-2"></i>Account</a>
                               
                                <a class="dropdown-item" onclick="logout()" style="cursor: pointer;"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        
        <div class="nav-left-sidebar sidebar-dark">
            <div class="menu-list">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav flex-column">
                            <li class="nav-divider">
                                Menu
                            </li>
                            <li class="nav-item ">
                            <a class="nav-link active" href="dashboard.php"><i class="fa fa-fw fa-user-circle"></i>Dashboard <span class="badge badge-success">6</span></a>
                              
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-2" aria-controls="submenu-2"><i class="fas fa-fw fa-file-alt"></i>Documents</a>
                                <div id="submenu-2" class="collapse submenu" >
                                    <ul class="nav flex-column" id="departmentList">
                                        <!-- 👥 HR Documents -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-hr" aria-controls="submenu-hr">
                                                👥 HR Documents
                                            </a>
                                            <div id="submenu-hr" class="collapse submenu">
                                                <ul class="nav flex-column" id="documentList">
                                                    
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="employee-records.html">Employee Records</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="attendance-payroll.html">Attendance & Payroll</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="performance-compliance.html">Performance & Compliance</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="recruitment-hiring.html">Recruitment & Hiring</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <!-- 💰 Finance Documents -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-finance" aria-controls="submenu-finance">
                                                💰 Finance Documents
                                            </a>
                                            <div id="submenu-finance" class="collapse submenu">
                                                <ul class="nav flex-column">
                                                   
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="accounting-reports.html">Accounting & Reports</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="transactions-approvals.html">Transactions & Approvals</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="tax-compliance.html">Tax & Compliance</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="asset-management.html">Asset Management</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <!-- 🚚 Logistics Documents -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-logistics" aria-controls="submenu-logistics">
                                                🚚 Logistics Documents
                                            </a>
                                            <div id="submenu-logistics" class="collapse submenu">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="logistics-dashboard.html">Logistics Dashboard</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="inventory-stock.html">Inventory & Stock</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="shipping-delivery.html">Shipping & Delivery</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="supplier-order-management.html">Supplier & Order Management</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <!-- 🏢 Administrative Documents -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-admin" aria-controls="submenu-admin">
                                                🏢 Administrative Documents
                                            </a>
                                            <div id="submenu-admin" class="collapse submenu">
                                                <ul class="nav flex-column">
                                                    
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="corporate-legal.html">Corporate & Legal</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="office-management.html">Office Management</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="customer-vendor-relations.html">Customer & Vendor Relations</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="it-security.html">IT & Security</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        
                                    </ul>
                                </div>
                            </li>
                            
           
                           
                          
                            <li class="nav-divider">
                                Features
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-6" aria-controls="submenu-6"><i class="fas fa-fw fa-window-maximize"></i> Pages </a>
                                <div id="submenu-6" class="collapse submenu" >
                                    <ul class="nav flex-column">

                                       <li class="nav-item">
                                            <a class="nav-link" href="createDocument.php">Create Documents</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Landing Page</a>
                                        </li>
                                        
                                        <li class="nav-item">
                                            <a class="nav-link" href="login.php">Login</a>
                                        </li>
                                       
                                        
                                        
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-7" aria-controls="submenu-7">
                                    <i class="fas fa-fw fa-cogs"></i> Settings <span class="badge badge-secondary">New</span>
                                </a>
                                <div id="submenu-7" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-user" aria-controls="submenu-user">
                                                User Mananagement
                                            </a>
                                            <div id="submenu-user" class="collapse submenu">
                                                <ul class="nav flex-column">

                                                    <li class="nav-item">
                                                        <a class="nav-link" href="user-management.php"> Department & Role</a>
                                                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-department" aria-controls="submenu-department"> Lists of Department</a>

                                                        <div id="submenu-department" class="collapse submenulist">
                                                            <ul class="nav flex-column" id="department-List">
                                                                <!-- Dynamic Departments will be added here -->
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    
                                                    
                                                </ul>
                                            </div>
                                        </li>
                                       
                                    </ul>
                                </div>
                            </li>
                            
                           
                        </ul>
                    </div>
                </nav>
            </div>
        </div>


 <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce" style="margin-bottom: 18rem;">
                <div class="container-fluid dashboard-content ">
                    <!-- ============================================================== -->
                    <!-- pageheader  -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header">
                                
                                <p class="pageheader-text">Nulla euismod urna eros, sit amet scelerisque torton lectus vel mauris facilisis faucibus at enim quis massa lobortis rutrum.</p>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a>
                                           </li>
                                            <li class="breadcrumb-item active" aria-current="page"></li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

   

            
        


           </div>


           <?php
    // Assuming $document is defined somewhere before this check
    if ($document['title'] === "Shipping & Delivery") :
    ?>



 <div class="container" style="margin-top: -23rem;">
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

        <div id="documentContent" class="<?php echo $has_otp ? 'blurred' : ''; ?>">

            <div class="table-responsive">
        <table id="shippingTable" class="table table-striped table-bordered table-hover">
            <thead class="table-primary">
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

   
        </div>

    </div>

    <?php 
    elseif ($document['title'] === "Inventory & Stock") : 
    ?>


  <div class="container" style="margin-top: -23rem;">
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

        <div id="documentContent" class="<?php echo $has_otp ? 'blurred' : ''; ?>">
           

    <div class="table-responsive">
        <table id="inventoryTable" class="table table-striped table-bordered table-hover">
            <thead class="table-primary">
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

    
</div>


 <?php 
    elseif ($document['title'] === "Budget Summary") : 
    ?>

<div class="container" style="margin-top: -23rem;">
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

        <div id="documentContent" class="<?php echo $has_otp ? 'blurred' : ''; ?>">
            


    <div class="table-responsive">
        <table id="summary_table" class="table table-striped table-bordered table-hover">
            <thead class="table-primary">
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

  
</div>



<?php 
    elseif ($document['title'] === "Employee Records") : 
    ?>

 <div class="container" style="margin-top: -23rem;">
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

        <div id="documentContent" class="<?php echo $has_otp ? 'blurred' : ''; ?>">
            


    <div class="table-responsive">
        <table id="employee_table" class="table table-striped table-bordered table-hover">
        <thead class="table-primary">
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

    
</div>




<?php 
    elseif ($document['title'] === "Accounting & Reports") : 
    ?>
 <div class="container" style="margin-top: -23rem;">
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

        <div id="documentContent" class="<?php echo $has_otp ? 'blurred' : ''; ?>">
          


            <div class="table-responsive">
    <div class="row">
        <!-- Report Table -->
        <div class="col-md-6">
            <h4 class="text-center">Reports</h4> <!-- Label for Report Table -->
            <table id="report_table" class="table table-striped table-bordered table-hover ">
                <thead class="table-primary">
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
            <h4 class="text-center ">Accounting</h4> <!-- Label for Accounting Table -->
            <table id="accounting_table" class="table table-striped table-bordered table-hover">
                <thead class="table-primary">
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









    <?php
    
    else :
        echo "<p>No shipping information available.</p>";
    endif;
    ?>


















            <div class="footer" style="margin-top: 20px;" >
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                             Copyright © 2018 far-east-cafe. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="text-md-right footer-links d-none d-sm-block">
                                <a href="javascript: void(0);">About</a>
                                <a href="javascript: void(0);">Support</a>
                                <a href="javascript: void(0);">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
        tableBody.innerHTML = ""; // Clear previous data

        // Ensure the response contains shipping deliveries and is an array
        if (data.shipping_deliveries && Array.isArray(data.shipping_deliveries) && data.shipping_deliveries.length > 0) {
            data.shipping_deliveries.forEach(shipment => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${shipment.shipment_id || 'N/A'}</td>
                    <td>${shipment.order_id || 'N/A'}</td>
                    <td>${shipment.customer_id || 'N/A'}</td>
                    <td>${shipment.shipping_address || 'N/A'}</td>
                    <td>${shipment.shipping_method || 'N/A'}</td>
                    <td>${shipment.shipping_cost || 'N/A'}</td>
                    <td>${shipment.shipping_date ? new Date(shipment.shipping_date).toLocaleDateString() : 'N/A'}</td>
                    <td>${shipment.estimated_delivery_date ? new Date(shipment.estimated_delivery_date).toLocaleDateString() : 'N/A'}</td>
                    <td>${shipment.delivery_status || 'N/A'}</td>
                    <td>${shipment.tracking_number || 'N/A'}</td>
                    <td>${shipment.delivery_date ? new Date(shipment.delivery_date).toLocaleDateString() : 'N/A'}</td>
                    <td>${shipment.delivery_notes || 'N/A'}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            console.warn("No shipping data available.");
        }
    } catch (error) {
        console.error("Error fetching shipping data:", error);
    }
}

// Fetch shipping data on page load
document.addEventListener("DOMContentLoaded", fetchShippingData);






        async function fetchInventoryData() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/inventory.php');
        const data = await response.json();

        const tableBody = document.querySelector("#inventoryTable tbody");
        
        tableBody.innerHTML = ""; // Clear previous data

        // Check if API response contains products
        if (data.products && Array.isArray(data.products) && data.products.length > 0) {
            data.products.forEach(product => {
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
                    <td>${product.last_restocked ? new Date(product.last_restocked).toLocaleDateString() : 'N/A'}</td>
                    <td>${product.status || 'N/A'}</td>
                    <td>${product.description || 'N/A'}</td>
                `;
                tableBody.appendChild(row);
            });
        }
    } catch (error) {
        console.error("Error fetching inventory data:", error);
    }
}

// Fetch inventory data when the page loads
document.addEventListener("DOMContentLoaded", fetchInventoryData);




async function fetchBudgetSummary() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/summary_api.php');
        const data = await response.json();

        const tableBody = document.querySelector("#summary_table tbody");
        const noDataMessage = document.getElementById("noInventoryDataMessage");

        tableBody.innerHTML = ""; // Clear previous data

        if (data.budgets && Array.isArray(data.budgets) && data.budgets.length > 0) {
            data.budgets.forEach(product => {
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
                    <td>${totalAmount}</td>
                `;

                tableBody.appendChild(row);
            });

            noDataMessage.style.display = "none";
        } else {
            noDataMessage.style.display = "block";
        }
    } catch (error) {
        console.error("Error fetching inventory data:", error);
        document.getElementById("noInventoryDataMessage").style.display = "block";
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

        // Check if data contains 'reports' array
        if (data.reports && Array.isArray(data.reports) && data.reports.length > 0) {
            data.reports.forEach(report => {
                // Create a row dynamically
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${report.id}</td>
                    <td>${report.report_name}</td>
                    <td>${report.details}</td>
                    <td>${report.accounting_id}</td>
                    <td>${new Date(report.created_at).toLocaleString()}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = "<tr><td colspan='5'>No report data available.</td></tr>";
        }
    } catch (error) {
        console.error("Error fetching report data:", error);
        document.querySelector("#report_table tbody").innerHTML = "<tr><td colspan='5'>Error loading data.</td></tr>";
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





document.addEventListener("DOMContentLoaded", function () {
    loadDepartmentsSidebar()
});

async function loadDepartmentsSidebar() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/department_api.php');
        if (!response.ok) throw new Error('Failed to fetch departments');

        const departments = await response.json();
        const departmentList = document.getElementById('departmentList');
        departmentList.innerHTML = ''; // Clear existing content

        departments.forEach(department => {
            const listItem = document.createElement('li');
            listItem.className = 'nav-item';

            const link = document.createElement('a');
            link.className = 'nav-link';
            link.href = `department.php?id=${department.id}`; // Adjust link to your routing
            link.textContent = department.name;

            listItem.appendChild(link);
            departmentList.appendChild(listItem);
        });
    } catch (error) {
        console.error('Error loading departments:', error);
    }
}



async function logout() {
            await fetch("logout.php", { method: "POST", credentials: "include" });
            window.location.href = "login.php";
        }



        document.addEventListener("DOMContentLoaded", function () {
    const userId = <?php echo json_encode($_SESSION['id']); ?>; // Get user ID from session
    fetchUserDetails(userId); // Fetch user details when page is ready
});

async function fetchUserDetails(userId) {
    try {
        const response = await fetch(`http://localhost/far-east-cafe/api/user_details_api.php?user_id=${userId}`);
        const data = await response.json();

        if (response.ok && data) {
            // Check if a profile picture exists and set the preview image
            if (data.profile_picture) {
                const profileImageUrl = `${data.profile_picture}`;
                document.getElementById("profilePreview").src = profileImageUrl; // Update profile picture
            } else {
                document.getElementById("profilePreview").src = "assets/images/default-avatar.png"; // Fallback image
            }
        } else {
            console.error("Failed to fetch user details:", data.error || "Unknown error");
        }
    } catch (error) {
        console.error("Error fetching user details:", error);
    }
}


document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications();
});

async function fetchNotifications() {
    try {
        const response = await fetch("http://localhost/far-east-cafe/api/reports.php", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        const data = await response.json();
        const container = document.getElementById("notificationContainer");
        container.innerHTML = ""; // Clear previous notifications

        // Check if API returned reports
        if (data.reports && Array.isArray(data.reports) && data.reports.length > 0) {
            data.reports.forEach(report => {
                const notification = document.createElement("div");
                notification.classList.add("list-group-item", "list-group-item-action");

                // Format date
                const formattedDate = new Date(report.created_at).toLocaleString();

                notification.innerHTML = `
                    <div class="notification-info" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <div class="notification-content" style="display: flex; justify-content: center; align-items: center; gap: 20px;">
                            <span class="notification-text">${report.report_name}</span>
                            <i class="fa-solid fa-xmark close-icon" onclick="hideNotification(this)" style="cursor: pointer;"></i>
                        </div>
                        <div class="notification-date" style="text-align: center; margin-top: 10px;">${formattedDate}</div>
                    </div>
                `;
                container.appendChild(notification);
            });
        } else {
            container.innerHTML = `<div class="list-group-item text-center">No new notifications</div>`;
        }
    } catch (error) {
        console.error("Error fetching notifications:", error);
    }
}

function hideNotification(icon) {
    icon.closest(".list-group-item").style.display = "none";
}

// Fetch notifications on page load
document.addEventListener("DOMContentLoaded", fetchNotifications);


document.addEventListener("DOMContentLoaded", function () {
    loadDepartmentsSidebar()
    loadDepartments();

});


async function loadDepartmentsSidebar() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/department_api.php');
        if (!response.ok) throw new Error('Failed to fetch departments');

        const departments = await response.json();
        const departmentList = document.getElementById('department-List');
        departmentList.innerHTML = ''; // Clear existing content

        departments.forEach(department => {
            const listItem = document.createElement('li');
            listItem.className = 'nav-item';

            const link = document.createElement('a');
            link.className = 'nav-link';
            link.href = `department.php?id=${department.id}`; // Adjust link to your routing
            link.textContent = department.name;

            listItem.appendChild(link);
            departmentList.appendChild(listItem);
        });
    } catch (error) {
        console.error('Error loading departments:', error);
    }
}


async function loadDepartments() {
    try {
        const response = await fetch('http://localhost/far-east-cafe/api/department_api.php');
        if (!response.ok) throw new Error("Failed to fetch departments");

        const departments = await response.json();
        const departmentList = document.getElementById("departmentList");
        departmentList.innerHTML = ""; // Clear existing content

        departments.forEach(async (department) => {
            // Create department list item
            const departmentItem = document.createElement("li");
            departmentItem.className = "nav-item";

            // Create department link with toggle functionality
            const departmentLink = document.createElement("a");
            departmentLink.className = "nav-link";
            departmentLink.href = "#";
            departmentLink.setAttribute("data-toggle", "collapse");
            departmentLink.setAttribute("aria-expanded", "false");
            departmentLink.setAttribute("data-target", `#submenu-dept-${department.id}`);
            departmentLink.setAttribute("aria-controls", `submenu-dept-${department.id}`);
            departmentLink.innerHTML = `<i class="fas fa-folder"></i> ${department.name}`;

            // Create collapsible container for documents
            const documentContainer = document.createElement("div");
            documentContainer.id = `submenu-dept-${department.id}`;
            documentContainer.className = "collapse submenu";

            // Create nested document list
            const documentList = document.createElement("ul");
            documentList.className = "nav flex-column ms-3"; // Indentation for nested list
            documentList.id = `documentList-${department.id}`;

            // Append elements
            documentContainer.appendChild(documentList);
            departmentItem.appendChild(departmentLink);
            departmentItem.appendChild(documentContainer);
            departmentList.appendChild(departmentItem);

            // Load documents for this department
            await loadDocuments(department.id, documentList);
        });
    } catch (error) {
        console.error("Error loading departments:", error);
    }
}

async function loadDocuments(departmentId, documentList) {
    try {
        const response = await fetch(`http://localhost/far-east-cafe/api/document.php?department_id=${departmentId}`);
        if (!response.ok) throw new Error(`Failed to fetch documents for department ${departmentId}`);

        const result = await response.json();
        console.log(`Response for department ${departmentId}:`, result); // Debugging

        // Ensure 'data' exists and is an array
        if (!result.data || !Array.isArray(result.data)) {
            console.error(`Unexpected response structure for department ${departmentId}:`, result);
            return;
        }

        // Now loop over 'data' array
        result.data.forEach((doc) => {
            const documentItem = document.createElement("li");
            documentItem.className = "nav-item";

            const documentLink = document.createElement("a");
            documentLink.className = "nav-link";
            documentLink.href = `http://localhost/far-east-cafe/document.php?id=${doc.id}`;
            documentLink.textContent = doc.title || `Document ${doc.id}`;

            documentItem.appendChild(documentLink);
            documentList.appendChild(documentItem);
        });
    } catch (error) {
        console.error(`Error loading documents for department ${departmentId}:`, error);
    }
}

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
