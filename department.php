<?php
include 'api/db.php';

// Get the department ID from the URL
$department_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($department_id > 0) {
    // Fetch department details
    $sql = "SELECT * FROM departments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $department_result = $stmt->get_result();
    $department = $department_result->fetch_assoc();

    if (!$department) {
        die("Department not found.");
    }

    // Fetch roles under this department
    $sql_roles = "SELECT * FROM roles WHERE department_id = ?";
    $stmt = $conn->prepare($sql_roles);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $roles_result = $stmt->get_result();
} else {
    die("Invalid department ID.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
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
    <title>List of <?php echo htmlspecialchars($department['name']); ?> Department</title>
    
</head>
<body>
<div class="dashboard-main-wrapper">

<div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
                <a class="navbar-brand" href="index.html">Admin</a>
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
                                        <div class="list-group">
                                            <a href="#" class="list-group-item list-group-item-action active">
                                                <div class="notification-info">
                                                    <div class="notification-list-user-img"><img src="assets/images/avatar-2.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                                    <div class="notification-list-user-block"><span class="notification-list-user-name">Jeremy Rakestraw</span>accepted your invitation to join the team.
                                                        <div class="notification-date">2 min ago</div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action">
                                                <div class="notification-info">
                                                    <div class="notification-list-user-img"><img src="assets/images/avatar-3.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                                    <div class="notification-list-user-block"><span class="notification-list-user-name">John Abraham </span>is now following you
                                                        <div class="notification-date">2 days ago</div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action">
                                                <div class="notification-info">
                                                    <div class="notification-list-user-img"><img src="assets/images/avatar-4.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                                    <div class="notification-list-user-block"><span class="notification-list-user-name">Monaan Pechi</span> is watching your main repository
                                                        <div class="notification-date">2 min ago</div>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action">
                                                <div class="notification-info">
                                                    <div class="notification-list-user-img"><img src="assets/images/avatar-5.jpg" alt="" class="user-avatar-md rounded-circle"></div>
                                                    <div class="notification-list-user-block"><span class="notification-list-user-name">Jessica Caruso</span>accepted your invitation to join the team.
                                                        <div class="notification-date">2 min ago</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="list-footer"> <a href="#">View all notifications</a></div>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name">John Abraham </h5>
                                    <span class="status"></span><span class="ml-2">Available</span>
                                </div>
                                <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Account</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-power-off mr-2"></i>Logout</a>
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
                                <a class="nav-link active" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1"><i class="fa fa-fw fa-user-circle"></i>Dashboard <span class="badge badge-success">6</span></a>
                                <div id="submenu-1" class="collapse submenu" >
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-1-2" aria-controls="submenu-1-2">E-Commerce</a>
                                            <div id="submenu-1-2" class="collapse submenu" >
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="index.html">E Commerce Dashboard</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="ecommerce-product.html">Product List</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="ecommerce-product-single.html">Product Single</a>
                                                    </li>
                                                   
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="dashboard-finance.html">Finance</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">HR</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Logistics</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Administrative</a>
                                        </li>
                                       
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-2" aria-controls="submenu-2"><i class="fas fa-fw fa-file-alt"></i>Documents</a>
                                <div id="submenu-2" class="collapse submenu" >
                                    <ul class="nav flex-column">
                                        <!-- 👥 HR Documents -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-hr" aria-controls="submenu-hr">
                                                👥 HR Documents
                                            </a>
                                            <div id="submenu-hr" class="collapse submenu">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="hr-dashboard.html">HR Dashboard</a>
                                                    </li>
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
                                                        <a class="nav-link" href="finance-dashboard.html">Finance Dashboard</a>
                                                    </li>
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
                                                        <a class="nav-link" href="admin-dashboard.html">Admin Dashboard</a>
                                                    </li>
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
                                            <a class="nav-link" href="#">Landing Page</a>
                                        </li>
                                        
                                        <li class="nav-item">
                                            <a class="nav-link" href="pages/login.html">Login</a>
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
                                                        <a class="nav-link" href="user-management.html"> Department & Role</a>
                                                        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-department" aria-controls="submenu-department"> Lists of Department</a>

                                                        <div id="submenu-department" class="collapse submenulist">
                                                            <ul class="nav flex-column" id="departmentList">
                                                                <!-- Dynamic Departments will be added here -->
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    
                                                    
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="pages/notifications.html">Notifications</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="pages/system-logs.html">System Logs</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="pages/general-settings.html">General Settings</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            
                           
                        </ul>
                    </div>
                </nav>
            </div>
</div>





<div class="dashboard-wrapper" >
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
                                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">List of Role in <?php echo htmlspecialchars($department['name']); ?> Department</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            <div class="container" style="margin-left:1rem;">


            <div class="modal fade" id="noRolesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body col-12 d-flex justify-content-center align-items-center">
                   <p> No roles found!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
           </div>
        

    
                <div class="row">
                    <?php
                    if ($roles_result->num_rows > 0) {
                        while ($role = $roles_result->fetch_assoc()) {
                            echo '<div class="col-md-4">';
                            echo '<a href="role_details.php?id=' . $role['id'] . '" class="text-decoration-none text-dark">';
                            echo '<div class="card mb-3 shadow-sm">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($role['name']) . '</h5>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {

                        echo '<div class="col-12 d-flex justify-content-center align-items-center" style="height: 50vh;">';
                        echo '<p class="text-muted fs-4">No roles found.</p>';
                        echo '</div>';
                        // Trigger the modal when no roles are found
                        echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const noRolesModal = new bootstrap.Modal(document.getElementById("noRolesModal"));
                                    noRolesModal.show();
                                });
                              </script>';
                    }
                    ?>
                </div>
             </div>

            
        


           </div>

            <div class="footer" >
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                             Copyright © 2018 Concept. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
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

    </script>
</body>
</html>
