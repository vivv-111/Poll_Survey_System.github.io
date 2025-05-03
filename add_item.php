<?php include 'db_connect.php'; ?>

<title>Add Item</title>

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
        margin-left: 23px;
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

    table {
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

    .prefix td {
        text-align: center;
    }

    .section-header td {
        background-color: #f8f9fa;
        font-size: 1.4em;
        padding: 20px;
        border-bottom: 4px solid #a7bf69;
        font-weight: bold;
    }

    button {
        padding: 5px 12px;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    button:hover {
        background-color: #a7bf69;
        border-color: #8fa358;
    }

    a.active {
            background-color: #a7bf69;
            color: white;
        }
</style>

<script>
document.getElementById('user_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar_preview');
            img.src = e.target.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>

<nav class="navigate">
    <br>
    <h2 class="nav-title">Restaurant</h2>
    <ul>
        <li class="item"><a href="admin_index.php" style="color:#fff;font-size: 15px;">Home</a></li>
        <li class="item"><a class="active" href="add_item.php" style="color:#fff;font-size: 15px;">Add Item</a></li>
        <li class="item"><a href="manage_items.php" style="color:#fff;font-size: 15px;">Manage Items</a></li>
        <li class="item"><a href="manage_user.php" style="color:#fff;font-size: 15px;">Manage Users</a></li>
        <li class="item"><a href="view_orders.php" style="color:#fff;font-size: 15px;">View Orders</a></li>
        <li class="item"><a href="order_details.php" style="color:#fff;font-size: 15px;">Order Details</a></li>
        <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
    </ul>
</nav>

<div class="main-content">
    <h2>Add Menu Item</h2>
    <form action="add_item.php" method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Please upload food's photo</td>
                <td>
                    <input type="file" name="user_image" id="user_image" accept="image/*">
                    <img id="avatar_preview" src="#" alt="Food" style="display:none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                </td>
            </tr>
            <tr>
                <td>Name:</td>
                <td><input type="text" name="name" required></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td>
                    <select id="type" name="type" required>
                        <option value="fried_noodles">Fried Noodles</option>
                        <option value="fried_rice">Fried Rice</option>
                        <option value="soup_noodles">Soup Noodles</option>
                        <option value="curry">Curry</option>
                        <option value="toast">Toast</option>
                        <option value="hamberger">Hamberger</option>
                        <option value="sandwich">Sandwich</option>
                        <option value="desserts">Desserts</option>
                        <option value="drinks">Drinks</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ingredients:</td>
                <td><textarea name="ingredient" required></textarea></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description" required></textarea></td>
            </tr>
            <tr>
                <td>Price:</td>
                <td><input type="number" step="0.01" name="price" min="0" oninput="validity.valid||(value='');" required></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Add Item"></td>
            </tr>
        </table>
    </form>

    <?php
    include 'db_connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST['name']); // 防止 XSS
        $type = htmlspecialchars($_POST['type']); // 防止 XSS
        $ingredient = htmlspecialchars($_POST['ingredient']); // 防止 XSS
        $description = htmlspecialchars($_POST['description']); // 防止 XSS
        $price = floatval($_POST['price']); // 嚴格類型檢查

        // Handle file upload
        $default_avatar = "default_profile_image.png"; // Default avatar filename
        $target_dir = __DIR__ . "/img_file/"; // Use absolute path, ensure it ends with a slash

        // Check if directory exists, if not create it
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                echo "<script>alert('Failed to create directory, please check permissions or path: $target_dir'); window.location.href='../../sign_up_page.php';</script>";
                exit();
            }
        }

        // Set default avatar path
        $target_file = $target_dir . $default_avatar;

        if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
            // Generate a unique filename to avoid conflicts
            $file_extension = strtolower(pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION));
            $unique_filename = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $unique_filename;

            // Validate file type and size
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 5 * 1024 * 1024; // 2MB

            if (!in_array($file_extension, $allowed_types)) {
                echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='../../sign_up_page.php';</script>";
                exit();
            }

            if ($_FILES['user_image']['size'] > $max_file_size) {
                echo "<script>alert('File size must be less than 2MB.'); window.location.href='../../sign_up_page.php';</script>";
                exit();
            }

            // Move uploaded file to target directory
            if (!move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
                echo "<script>alert('Failed to upload avatar, please check permissions or path: $target_file'); window.location.href='../../sign_up_page.php';</script>";
                exit();
            }
        }

        // Calculate relative path
        $relative_path = "img_file/" . basename($target_file); // Fix path concatenation

        $sql = "INSERT INTO menu_items (photo, name, type, Ingredients, description, price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssd", $relative_path, $name, $type, $ingredient, $description, $price);

        if ($stmt->execute()) {
            echo "New item added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
    ?>
</div>