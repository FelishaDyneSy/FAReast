<?php
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
?>
<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
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
    <title>far-east-cafe</title>
    <style>
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
    <!-- ============================================================== -->
    <!-- main wrapper -->
    <!-- ============================================================== -->
    <div class="dashboard-main-wrapper">
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
                <a class="navbar-brand" href="dashboard.php">Admin</a>
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
                                <a class="dropdown-item" href="visitorAccount.php"><i class="fas fa-user mr-2"></i>Account</a>
                               
                                <a class="dropdown-item" onclick="logout()" style="cursor: pointer;"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
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
                                        <!--  HR Documents -->
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
                                                 Finance Documents
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
                                                      <li class="nav-item">
                                            <a class="nav-link" href="adminSchedule.php">Visitor Appointment Scheduling</a>
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
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid dashboard-content ">
                    <!-- ============================================================== -->
                    <!-- pageheader  -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header">
                                <h2 class="pageheader-title">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                                <p class="pageheader-text">Nulla euismod urna eros, sit amet scelerisque torton lectus vel mauris facilisis faucibus at enim quis massa lobortis rutrum.</p>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">E-Commerce Dashboard </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end pageheader  -->
                    <!-- ============================================================== -->
                   <div class="container mt-4">
    <h2>Visitor Appointment Scheduling</h2>
    <form id="appointmentForm">
        <input type="hidden" id="id"> <!-- Hidden input for updating -->
        <div class="mb-3">
            <label for="visitor_name" class="form-label">Visitor Name</label>
            <input type="text" class="form-control" id="visitor_name" required>
        </div>
        <div class="mb-3">
            <label for="visitor_email" class="form-label">Visitor Email</label>
            <input type="email" class="form-control" id="visitor_email" required>
        </div>
        <div class="mb-3">
            <label for="appointment_date" class="form-label">Appointment Date</label>
            <input type="date" class="form-control" id="appointment_date" required>
        </div>
        <div class="mb-3">
            <label for="appointment_time" class="form-label">Appointment Time</label>
            <input type="time" class="form-control" id="appointment_time" required>
        </div>
        <button type="submit" class="btn btn-primary">Schedule Appointment</button>
        <button type="button" class="btn btn-warning d-none" id="updateBtn">Update Appointment</button>
        <button type="button" class="btn btn-secondary d-none" id="cancelBtn">Cancel</button>
    </form>

    <h3 class="mt-4">Appointments List</h3>
    <ul id="appointmentList" class="list-group"></ul>
</div>
            </div>
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <div class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                             Copyright  2018 far-east-cafe. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
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
            <!-- ============================================================== -->
            <!-- end footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- jquery 3.3.1 -->
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
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>

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
    loadDepartments();

});


async function loadDepartmentsSidebar() {
    try {
        const response = await fetch('http://localhost/FAReast-cafe/api/department_api.php');
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
        const response = await fetch('http://localhost/FAReast-cafe/api/department_api.php');
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
        const response = await fetch(`http://localhost/FAReast-cafe/api/document.php?department_id=${departmentId}`);
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
            documentLink.href = `http://localhost/FAReast-cafe/document.php?id=${doc.id}`;
            documentLink.textContent = doc.title || `Document ${doc.id}`;

            documentItem.appendChild(documentLink);
            documentList.appendChild(documentItem);
        });
    } catch (error) {
        console.error(`Error loading documents for department ${departmentId}:`, error);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications();
});

async function fetchNotifications() {
    try {
        const response = await fetch("http://localhost/FAReast-cafe/api/reports.php", {
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
    const userId = <?php echo json_encode($_SESSION['id']); ?>; // Get user ID from session
    fetchUserDetails(userId); // Fetch user details when page is ready
});

async function fetchUserDetails(userId) {
    try {
        const response = await fetch(`http://localhost/FAReast-cafe/api/user_details_api.php?user_id=${userId}`);
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

 document.getElementById("appointmentForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const appointmentId = document.getElementById("appointment_id").value;

    const formData = {
        visitor_name: document.getElementById("visitor_name").value,
        visitor_email: document.getElementById("visitor_email").value,
        appointment_date: document.getElementById("appointment_date").value,
        appointment_time: document.getElementById("appointment_time").value
    };

    const url = "http://localhost/FAReast-cafe/api/appointment.php";
    const method = appointmentId ? "PUT" : "POST";

    if (appointmentId) formData.id = appointmentId; // Include ID for update

    fetch(url, {
        method: method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message, "success");
        resetForm();
        loadAppointments();
    })
    .catch(error => showToast("Error: " + error.message, "error"));
});

function loadAppointments() {
    fetch("http://localhost/FAReast-cafe/api/appointment.php")
    .then(response => response.json())
    .then(data => {
        let list = document.getElementById("appointmentList");
        list.innerHTML = "";
        data.data.forEach(appointment => {
            list.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${appointment.visitor_name} - ${appointment.appointment_date} (${appointment.status})
                    <div>
                        <button class="btn btn-sm btn-warning" onclick="editAppointment(${appointment.id}, '${appointment.visitor_name}', '${appointment.visitor_email}', '${appointment.appointment_date}', '${appointment.appointment_time}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAppointment(${appointment.id})">Delete</button>
                    </div>
                </li>`;
        });
    });
}

function editAppointment(id, name, email, date, time) {
    document.getElementById("id").value = id;
    document.getElementById("visitor_name").value = name;
    document.getElementById("visitor_email").value = email;
    document.getElementById("appointment_date").value = date;
    document.getElementById("appointment_time").value = time;

    document.querySelector("button[type='submit']").classList.add("d-none");
    document.getElementById("updateBtn").classList.remove("d-none");
    document.getElementById("cancelBtn").classList.remove("d-none");
}

document.getElementById("updateBtn").addEventListener("click", function() {
    document.getElementById("appointmentForm").dispatchEvent(new Event("submit"));
});

document.getElementById("cancelBtn").addEventListener("click", function() {
    resetForm();
});

function deleteAppointment(id) {
    if (!confirm("Are you sure you want to delete this appointment?")) return;

    fetch("http://localhost/FAReast-cafe/api/appointment.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        showToast(data.message, "success");
        loadAppointments();
    })
    .catch(error => showToast("Error: " + error.message, "error"));
}

function resetForm() {
    document.getElementById("appointmentForm").reset();
    document.getElementById("appointment_id").value = "";

    document.querySelector("button[type='submit']").classList.remove("d-none");
    document.getElementById("updateBtn").classList.add("d-none");
    document.getElementById("cancelBtn").classList.add("d-none");
}

function showToast(message, type) {
    alert(`${type.toUpperCase()}: ${message}`);
}

document.addEventListener("DOMContentLoaded", loadAppointments);

        

        


async function logout() {
            await fetch("logout.php", { method: "POST", credentials: "include" });
            window.location.href = "http://localhost/FAReast-cafe/login.php";
        }

    </script>
</body>
 
</html>