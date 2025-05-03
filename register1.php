<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register page</title>
    <link rel="stylesheet" href="assets/css/styles1.css">
    <style>
        .input-file {
            border: 4px solid rgba(0, 0, 0, 0.473);
            padding: 10px;
            margin-bottom: 1rem;
            width: 170px;
        }
        img {
            display: block;
            height: 100vh;
            position: relative;
            width: 100%;
        }
        .form-makeup {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .form_backgroud_color {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1000px; /* 增加底板的寬度 */
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form_backgroud_color form {
            flex: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .image-preview-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 150px
        }
        .image-preview-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: none;
        }
        .form-fields {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 200px
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
    <h1 style="text-align: center;font-family:'Lucida Handwriting';">Register page</h1>
    <div class="form-makeup">
        <div class="form_backgroud_color">
            <form action="register1.php" method="post" enctype="multipart/form-data" style="text-align:center;">
                <div class="image-preview-container">
                    <img id="profile-image-preview" src="#" alt="Profile Image Preview"/>
                    <label for="profile-image" class="profile-image-label">Upload</label>
                    <input type="file" id="profile-image" name="profile_image" accept="image/*" class="text-input" onchange="previewImage(event)">
                    <p><b style="font-family: Arial;">Upload your profile image</b></p>
                </div>
                <div class="form-fields">
                    <input type="text" name="user_id" id="user_id" placeholder="Login ID" required class="text-input">
                    <br>
                    <input type="text" name="user_name" id="user_name" placeholder="Nick name" required class="text-input">
                    <br>
                    <input type="email" name="email" id="email" placeholder="Email" required class="text-input">
                    <br>
                    <input type="password" name="password" id="password" placeholder="Password" required class="text-input">
                    <br>
                    <input type="text" name="phone" id="phone" placeholder="Phone" required class="text-input">
                    <br><br>
                    <button class="login-and-register-button" type="submit">Register</button>
                    <p class="changepagelinkstyle-register"><a href="index.php">Back to login page</a></p>
                </div>
            </form>
        </div>
    </div>
    <?php include 'db_connect.php'; ?>

<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user_id = trim($_POST['user_id']);
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Handle file upload
    $photo = null;
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

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.');</script>";
        exit();
    }

    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database
    $sql = "INSERT INTO user_info (user_id, user_name, email, password, phone, photo, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $user_id, $user_name, $email, $password_hash, $phone, $photo);

    if ($stmt->execute()) {
        echo "<script>alert('New record created successfully');
        window.location.href='index.php';
        </script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
</body>
</html>