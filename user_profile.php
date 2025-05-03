<?php
session_start();
include 'db_connect.php';

$user_id = htmlspecialchars($_SESSION['user']['user_id'] ?? null); // 防止 XSS
if (!$user_id) {
    header("Location: first_page.php"); // Redirect to login page if user is not logged in
    exit();
}

// Fetch user information from the database
$sql = "SELECT user_id, user_name, email, phone, photo, created_at, updated_at FROM user_info WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = htmlspecialchars($_POST['user_name'] ?? $user['user_name']); // 防止 XSS
    $email = htmlspecialchars($_POST['email'] ?? $user['email']); // 防止 XSS
    $phone = htmlspecialchars($_POST['phone'] ?? $user['phone']); // 防止 XSS
    $photo = $user['photo'];

    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img_file/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $photo = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        } else {
            echo "File is not an image.";
            exit();
        }
    }

    // Update user information in the database
    $sql = "UPDATE user_info SET user_name = ?, email = ?, phone = ?, photo = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $user_name, $email, $phone, $photo, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully";
        // Refresh user data
        $user['user_name'] = $user_name;
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['photo'] = $photo;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        .navigate {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #444;
            position: fixed;
            bottom: 0;
            left: 0;
            height: 100%;
            width: 15%;
            text-align: left;
        }

        .item {
            display: block;
            width: 100%;
            margin: 8px 0;
            padding-left: 15px;
        }

        .item a {
            display: block;
            color: white;
            text-decoration: none;
            width: calc(100% - 30px);
            margin: 0;
            transition: all 0.3s;
            font-size: 18px;
            padding: 12px 15px;
        }

        .item a:hover:not(.active) {
            background-color: #a7bf69;
            transform: scale(1.05); 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-left: 5px;
        }

        .nav-title {
            color: antiquewhite;
            font-size: 32px;
            margin-left: 60px;
            padding: 15px 0;
            letter-spacing: 2px;
        }
        .nav-title:hover {
            transform: translateX(10px);
            color: #a7bf69;
        }

        .main-content {
            margin-left: 15%;
            padding: 15px;
            position: relative;
        }

        .profile-table {
            width: 100%;
            border: 3px solid #333;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 2px solid #666;
            padding: 12px;
            text-align: left;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 0; /* Make the image square */
        }

        .userid_text {
            color: white;
            font-size: 20px;
            margin-top: 10px;
        }

        a.active {
            background-color: #a7bf69;
            color: white;
        }
    </style>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('profile-image-preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <nav class="navigate">
        <div class="user-info" style="text-align: center;">
            <a href="user_profile.php" target="_self"><img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo" width="100" height="100"></a>
            <?php echo "<div class='userid_text'>" . htmlspecialchars($user['user_id']) . "</div>"; ?>
        </div>
        <h2 class="nav-title">Client</h2>
        <ul>
            <li class="item"><a class="active" href="user_profile.php" style="color:#fff;font-size: 15px;">User Profile</a></li>
            <li class="item"><a href="client_menu.php" style="color:#fff;font-size: 15px;">Client Menu</a></li>
            <li class="item"><a href="shopping_cart.php" style="color:#fff;font-size: 15px;">Shopping Cart</a></li>
            <li class="item"><a href="order_details_client.php" style="color:#fff;font-size: 15px;">View Order</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h2>User Profile</h2>
        <form action="user_profile.php" method="post" enctype="multipart/form-data">
            <table class="profile-table">
                <tr>
                    <th>User ID</th>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><input type="text" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required></td>
                </tr>
                <tr>
                    <th>Profile Image</th>
                    <td>
                        <img id="profile-image-preview" src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Image Preview" class="profile-image"/>
                        <br>
                        <label for="profile-image" class="profile-image-label">Change Profile Image</label>
                        <input type="file" id="profile-image" name="profile_image" accept="image/*" onchange="previewImage(event)">
                    </td>
                </tr>
            </table>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>