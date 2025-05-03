<?php
include 'db_connect.php';


$admin_id = '1104';
$admin_name = 'Admin';
$admin_email = 'admin@qq.com';
$admin_password = 'admin';
$admin_phone = '123456';

// use password_hash() function to hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);


$sql = "INSERT INTO admin_info (admin_id, admin_name, admin_email, admin_password, admin_phone) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss",$admin_id, $admin_name, $admin_email, $hashed_password, $admin_phone);

if ($stmt->execute()) {
    echo "Admin record inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>