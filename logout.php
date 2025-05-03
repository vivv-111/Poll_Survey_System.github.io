<?php
session_start();

// delete all session variables
$_SESSION = array();

// if the session is set, delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destroy the session
session_destroy();

// delete cookies
setcookie("userid", "", time() - 86400, "/");
setcookie("usertype", "", time() - 86400, "/");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <script>
        window.onload = function() {
            alert("You have been logged out.");
            window.location.href = "index.php";
        }
    </script>
</head>
<body>
</body>
</html>