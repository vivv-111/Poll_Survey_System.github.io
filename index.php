<?php
ini_set('session.gc_maxlifetime', 1800);
session_set_cookie_params(1800);
session_start();
include 'db_connect.php';


$allowed_files = [
    'client_menu.php',
    'user_profile.php',
    'add_item.php',
    'register1.php'
];


if (isset($_GET['page'])) {
    $page = basename($_GET['page']); 
    if (in_array($page, $allowed_files)) {
        include $page;
    } else {
        echo "Invalid page request.";
        exit();
    }
}

$error = '';
$max_attempts = 2; 
$lockout_time = 1 * 60; 

if (isset($_SESSION['error'])) {
    $error = htmlspecialchars($_SESSION['error']); 
    unset($_SESSION['error']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values
    $user_id = trim($_POST['user_id'] ?? ''); 
    $password = trim($_POST['password'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $user_remember = isset($_POST["user_remember"]) ? htmlspecialchars($_POST["user_remember"]) : "off";

    if ($user_remember !== "off") {
        setcookie("userid", urlencode($user_id), time() + 86400, "/"); 
        setcookie("usertype", urlencode($user_type), time() + 86400, "/"); 
    }

    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $result = $stmt->get_result();
    $attempt_data = $result->fetch_assoc();

    if ($attempt_data) {
        $attempts = intval($attempt_data['attempts']);
        $last_attempt = strtotime($attempt_data['last_attempt']);

        if ($attempts >= $max_attempts && (time() - $last_attempt) < $lockout_time) {
            $error = 'Too many failed login attempts. Please try again later.';
            echo "<p style='color: red;'>$error</p>";
            exit();
        }
    }

    // Basic validation
    if (empty($user_id) || empty($password)) {
        $error = 'User ID and password cannot be empty';
    } else {
        // Determine the table to query based on user type
        if ($user_type == 'Restaurant') {
            $stmt = $conn->prepare("SELECT * FROM admin_info WHERE admin_id =1104");
                        $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user['admin_password'])) {
                
                $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $stmt->bind_param("s", $ip_address);
                $stmt->execute();

                // Set session
                $_SESSION['user'] = [
                    'id' => intval($user['id']), 
                    'user_id' => htmlspecialchars($user['admin_id']), 
                    'user_name' => htmlspecialchars($user['admin_name']) 
                ];
                header("Location: admin_index.php"); // Redirect to dashboard page after successful login
                exit();
            } else {
                $error = 'Invalid admin ID or password';
            }
        } else {
            $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
            $stmt->bind_param("s", $user_id); 
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                
                $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $stmt->bind_param("s", $ip_address);
                $stmt->execute();

                // Set session
                $_SESSION['user'] = [
                    'id' => intval($user['id']), 
                    'user_id' => htmlspecialchars($user['user_id']), 
                    'user_name' => htmlspecialchars($user['user_name']) 
                ];
                header("Location: client_menu.php"); // Redirect to dashboard page after successful login
                exit();
            } else {
                $error = 'Invalid user ID or password';
            }
        }
    }

    
    if (!empty($error)) {
        if ($attempt_data) {
            $stmt = $conn->prepare("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())");
        }
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login page</title>
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
            max-width: 1000px; 
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
            margin-right: 20px;
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
            margin-left: 240px;
        }
        input[type="checkbox"] {
            transform: scale(1.5);
            border-radius: 25px;
        }

        .login-and-register-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 10px;
        }
    </style>
    <script>
        function switch_user() {
            var checkbox = document.getElementById('user_type_checkbox');
            var userType = document.getElementById('user_type');
            var switchText = document.getElementById('switch_the_user');
            if (checkbox.checked) {
                userType.value = 'Restaurant';
                switchText.innerHTML = 'User: Restaurant';
            } else {
                userType.value = 'Consumer';
                switchText.innerHTML = 'User: Consumer';
            }
        }

        function getCookie(name) {
            let cookies = document.cookie.split("; "); // Split all cookies
            for (let i = 0; i < cookies.length; i++) {
                let cookiePair = cookies[i].split("="); // Split name and value
                if (cookiePair[0] === name) {
                    return decodeURIComponent(cookiePair[1]); // Return decoded value
                }
            }
            return null; // Return null if not found
        }
        
        // Example Usage:
        let useridCookie = getCookie("userid");
        let usertypeCookie = getCookie("usertype");

          document.addEventListener("DOMContentLoaded", function() {
            
            if(useridCookie){
              if(usertypeCookie == "Restaurant")
              window.location.href = "admin_index.php";
            else {
                if(usertypeCookie == "Consumer"){
              window.location.href = "client_menu.php";
                }
            }
        }
          });
    </script>
</head>
<body id="page">
    <h1 style="text-align: center;font-family:'Lucida Handwriting';">Restaurant Login</h1>
    <div class="form-makeup">
        <div class="form_backgroud_color">
            <form action="index.php" method="post" id="loginform" style="text-align:center;">
                <div class="image-preview-container">
                    <img class="moving-img" height="100px" width="250px" src="img_file/indian.webp" style="border-radius: 10px;">
                </div>
                <div class="form-fields">
                    <input class="text-input" type="text" name="user_id" id="user_id" placeholder="Login ID or Email" padding="1rem" required>
                    <br>
                    <input class="text-input" type="password" name="password" id="password" placeholder="Password" required>
                    <br>
                    <label class="toggle-switch">
                        <input type="checkbox" id="user_type_checkbox" onclick="switch_user()">
                        <span class="slider" id="switch_the_user" style="font-family: Verdana, sans-serif;">User: Consumer</span>
                    </label>
                    <br class="br_higher">
                    <input style="border: 10px;" type="checkbox" name="user_remember" id="user_remember" value="on"><b style="font-family: Arial;">  Remember me</b>
                    <input type="hidden" id="user_type" name="user_type" value="Consumer">
                    <br class="br_higher">
                    <button class="login-and-register-button" type="submit" style="font-family: Verdana, sans-serif;" id="loginbutton">Login</button>
                    <b>
                        <p class="changepagelinkstyle-login">Don't have an account?
                            <a href="register1.php">Click here and Register it!</a>
                        </p>
                    </b>
                </div>
            </form>
        </div>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>