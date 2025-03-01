<?php
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "admin";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}
?>
