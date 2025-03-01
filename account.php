<?php
session_start();

if (!isset($_SESSION['id']) || strtolower($_SESSION['department_name']) !== 'admin') {
    header("Location: login.html");
    exit();
}

// Assign session variables
$userId = $_SESSION['id'];
$departmentId = $_SESSION['department_id'];
$roleId = $_SESSION['role_id'];
// Pass variables to JavaScript
echo "<script>
    const userId = " . json_encode($userId) . ";
    
</script>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <title>far-east-cafe - Bootstrap 4 Admin Dashboard Template</title>
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
        <div class="container-fluid dashboard-content">
            <div class="row">
                <div class="col-xl-12">
                    <div class="page-header">
                        <p class="pageheader-text">
                            Manage user accounts with ease. Update personal details and upload profile pictures.
                        </p>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Account</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
    <div class="row">
        <!-- User Profile Form (Left Column) -->
        <div class="col-md-6">
            <form id="userProfileForm">
                <div class="card">
                    <div class="card-header">User Profile - <?php echo htmlspecialchars($_SESSION['name']); ?></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="passwordField" name="password">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Department Name</label>
                            <input type="text" class="form-control" name="department_name" readonly>
                        </div>
                        <div class="form-group">
                            <label>Role Assigned</label>
                            <input type="text" class="form-control" name="role_name" readonly>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                       
                    </div>
                </div>
            </form>
        </div>

        <!-- Additional Information Form (Right Column) -->
        <div class="col-md-6">
       <!-- Your existing form -->
       <!-- Main Form -->
<form id="additionalInfoForm" enctype="multipart/form-data">
    <div class="card">
        <div class="card-header">Additional Information</div>
        <div class="card-body">
            <!-- Hidden user_id field -->
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['id']; ?>">

            <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="address">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone">
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" class="form-control" name="date_of_birth">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select class="form-control" name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group text-center">
                <label>Profile Picture</label><br>
                <div style="display: flex; justify-content: center; align-items: center; height: 100px; margin-top: 3px;">
                    <img id="profilePreviews" src="" width="150" style="cursor: pointer; object-fit: fill; width: 200px; height: 100px; border-radius: 5px;">
                    <input type="file" id="profilePicture" class="form-control-file" style="display: none;">
                </div>
            </div>

            <div class="form-group">
                <label>Nationality</label>
                <input type="text" class="form-control" name="nationality">
            </div>
            <div class="form-group">
                <label>Occupation</label>
                <input type="text" class="form-control" name="occupation">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea class="form-control" name="bio"></textarea>
            </div>

            <div class="d-flex justify-content-around align-items-center"style ="margin-top:20px;">
                 <!-- Submit button for normal form submission -->
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-success" id="submitBtn">Submit Additional Information</button>
                </div>

                <!-- Update Button for Modal -->
                <div class="form-group text-center">
                    <button type="button" class="btn btn-primary" id="updateBtn" data-toggle="modal" data-target="#updateModal">Update Information</button>
                </div>

            </div>

           
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Additional Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- The same form inside the modal -->
                <form id="modalAdditionalInfoForm" enctype="multipart/form-data">
                    <!-- Hidden user_id field -->
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" value="">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group text-center">
                        <label>Profile Picture</label><br>
                        <div style="display: flex; justify-content: center; align-items: center; height: 100px; margin-top: 3px;">
                            <img id="profilePic" src="" width="150" style="cursor: pointer; object-fit: fill; width: 200px; height: 100px; border-radius: 5px;">
                            <input type="file" id="modalProfilePicture" class="form-control-file" style="display: none;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nationality</label>
                        <input type="text" class="form-control" name="nationality" value="">
                    </div>
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" class="form-control" name="occupation" value="">
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>

                    <!-- Submit Button inside Modal -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" id="modalSubmitBtn">Update Additional Information</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>
</div>

        </div>
    </div>

    <div class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-6">
                    Copyright © 2018 Concept. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
                </div>
                <div class="col-xl-6 text-md-right">
                    <a href="#">About</a> | <a href="#">Support</a> | <a href="#">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    // Show toast notifications
    function showToast(message, type) {
        Toastify({
            text: message,
            style: {
                background: type === 'success' ? 'linear-gradient(to right, #00b09b, #96c93d)' : 'linear-gradient(to right, #ff5f6d, #ffc371)'
            },
            duration: 3000
        }).showToast();
    }



    document.getElementById('profilePreviews').addEventListener('click', function() {
    document.getElementById('profilePicture').click();  // Trigger the file input when the image is clicked
});

document.getElementById('profilePicture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        const imageUrl = event.target.result;
        const preview = document.getElementById('profilePreviews');
        preview.src = imageUrl;
        preview.style.display = 'block';
        // Show the image
    };

    if (file) {
        reader.readAsDataURL(file); // Read the file as a data URL
    }
});


// Trigger file input when clicking on the image
document.getElementById('profilePic').addEventListener('click', function() {
    document.getElementById('modalProfilePicture').click();
});

document.getElementById('modalProfilePicture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        const imageUrl = event.target.result;
        const preview = document.getElementById('profilePic');
        preview.src = imageUrl;
        preview.style.display = 'block'; // Show the image
    };

    if (file) {
        reader.readAsDataURL(file); // Read the file as a data URL
    }
});






    document.getElementById("togglePassword").addEventListener("click", function () {
        let passwordField = document.getElementById("passwordField");
        let icon = this.querySelector("i");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const userId = <?php echo json_encode($_SESSION['id']); ?>;
        fetchUserData(userId); // Fetch data when page is ready
    });

    async function fetchUserData(userId) {
        try {
            // Fetch user data
            const userResponse = await fetch(`http://localhost/far-east-cafe/api/user_api.php?id=${userId}`);
            const userData = await userResponse.json();
            if (userResponse.ok && userData) {
                document.querySelector("input[name='name']").value = userData.name || "";
                document.querySelector("input[name='email']").value = userData.email || "";
                document.querySelector("input[name='password']").value = userData.password || "";
                document.querySelector("input[name='department_name']").value = userData.department_name || "";
                document.querySelector("input[name='role_name']").value = userData.role_name || "";
            } else {
                console.error('Failed to fetch user data:', userData.error || 'Unknown error');
            }
        } catch (error) {
            console.error("Error fetching user data:", error);
        }
    }
    

    document.getElementById("userProfileForm").addEventListener("submit", async function (e) {
        e.preventDefault(); // Prevent default form submission

        const userId = <?php echo json_encode($_SESSION['id']); ?>;

        const formData = new FormData(this);

        // Prepare data to send
        const data = {
            name: formData.get("name"),
            email: formData.get("email"),
            password: formData.get("password"),
            department_id: <?php echo json_encode($_SESSION['department_id']); ?>,
            role_id: <?php echo json_encode($_SESSION['role_id']); ?>
        };

        try {
            const response = await fetch(`http://localhost/far-east-cafe/api/user_api.php?id=${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (response.ok) {
                showToast('Profile updated successfully!', 'success');
                // Delay before reloading for better UX
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to update profile: ' + error.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while updating the profile', 'error');
        }
    });


    document.addEventListener("DOMContentLoaded", function () {
        const userId = <?php echo json_encode($_SESSION['id']); ?>; // Get user ID from session
        fetchUserDetails(userId); // Fetch user details when page is ready
    });

    async function fetchUserDetails(userId) {
    try {
        const response = await fetch(`http://localhost/far-east-cafe/api/user_details_api.php?user_id=${userId}`);
        const data = await response.json();

        if (response.ok && data) {
            // Populate form fields with data
            document.querySelector("input[name='address']").value = data.address || "";
            document.querySelector("input[name='phone']").value = data.phone || "";
            document.querySelector("input[name='date_of_birth']").value = data.date_of_birth || "";
            document.querySelector("select[name='gender']").value = data.gender || "Male";
            document.querySelector("input[name='nationality']").value = data.nationality || "";
            document.querySelector("input[name='occupation']").value = data.occupation || "";
            document.querySelector("textarea[name='bio']").value = data.bio || "";

            // Check if a profile picture exists and set the preview image
            if (data.profile_picture) {
                const profileImageUrl = `${data.profile_picture}`;
                document.getElementById("profilePreviews").src = profileImageUrl;
                document.getElementById("profilePreview").src = profileImageUrl; // Set the image source
                
            }
        } else {
            console.error("Failed to fetch user details:", data.error || "Unknown error");
        }
    } catch (error) {
        console.error("Error fetching user details:", error);
    }
}






document.addEventListener("DOMContentLoaded", function () {
    const userId = <?php echo json_encode($_SESSION['id']); ?>; // Get user ID from session
    fetchUserDetails(userId); // Fetch user details when page is ready

    // Add click event listener to the Update button
    document.getElementById('updateBtn').addEventListener('click', function() {
        ModalfetchUserDetails(userId); // Fetch user details for the modal
    });
});

async function ModalfetchUserDetails(userId) {
    try {
        const response = await fetch(`http://localhost/far-east-cafe/api/user_details_api.php?user_id=${userId}`);
        const data = await response.json();

        if (response.ok && data) {
            // Populate modal form fields with data
            document.querySelector("#modalAdditionalInfoForm input[name='address']").value = data.address || "";
            document.querySelector("#modalAdditionalInfoForm input[name='phone']").value = data.phone || "";
            document.querySelector("#modalAdditionalInfoForm input[name='date_of_birth']").value = data.date_of_birth || "";
            document.querySelector("#modalAdditionalInfoForm select[name='gender']").value = data.gender || "Male";
            document.querySelector("#modalAdditionalInfoForm input[name='nationality']").value = data.nationality || "";
            document.querySelector("#modalAdditionalInfoForm input[name='occupation']").value = data.occupation || "";
            document.querySelector("#modalAdditionalInfoForm textarea[name='bio']").value = data.bio || "";

            // Check if a profile picture exists and set the preview image
            if (data.profile_picture) {
                const profileImageUrl = `${data.profile_picture}`;
                document.getElementById("profilePic").src = profileImageUrl;
            }
        } else {
            console.error("Failed to fetch user details:", data.error || "Unknown error");
        }
    } catch (error) {
        console.error("Error fetching user details:", error);
    }
}




document.getElementById("additionalInfoForm").addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent the form from submitting traditionally

    const formData = new FormData(this);
    const userId = <?php echo json_encode($_SESSION['id']); ?>;

    // Get user_id from the hidden input
    const Id = document.getElementById("user_id").value;

    // Ensure the userId is appended to the form data (if not already)
    formData.append("user_id", Id);

    // Get the file input element and append the file to the formData if a file is selected
    const profilePicture = document.getElementById("profilePicture").files[0];
    if (profilePicture) {
        formData.append("profile_picture", profilePicture);
    }

    try {
        // Post the form data to update the user's details
        const response = await fetch(`http://localhost/far-east-cafe/api/user_details_api.php?user_id=${userId}`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (response.ok) {
            showToast('Additional information updated successfully!', 'success');
            // Delay before reloading for better UX
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Failed to update additional information: ' + result.error, 'error');
        }
    } catch (error) {
        console.error("Error submitting form:", error);
        showToast('An error occurred while updating the information', 'error');
    }
});







document.getElementById("modalAdditionalInfoForm").addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent default form submission

    const userId = <?php echo json_encode($_SESSION['id']); ?>;
    const formData = new FormData(this);

    // Convert FormData to JSON object
    const data = {
        address: formData.get("address"),
        phone: formData.get("phone"),
        date_of_birth: formData.get("date_of_birth"),
        gender: formData.get("gender"),
        nationality: formData.get("nationality"),
        occupation: formData.get("occupation"),
        bio: formData.get("bio"),
        user_id: userId
    };

    try {
        const response = await fetch('http://localhostc/far-east-cafe/api/user_details_api.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data), // Convert object to JSON string
        });

        const result = await response.json();
        if (response.ok) {
            showToast('Profile updated successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Failed to update profile: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while updating the profile', 'error');
    }
});



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
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>
    <!-- Toastify JS -->
<script src='https://cdn.jsdelivr.net/npm/toastify-js'></script>


    
</body>
</html>