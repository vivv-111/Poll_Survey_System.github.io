<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user']['user_id'] ?? null;
$user_name = $_SESSION['user']['user_name'] ?? null;
if (!$user_id) {
    header("Location: first_page.php"); // Redirect to login page if user is not logged in
    exit();
}

// Fetch user information from the database
$sql = "SELECT user_id, photo FROM user_info WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
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
</head>
<body>
    <nav class="navigate">
        <div class="user-info" style="text-align: center;">
            <a href="user_profile.php" target="_self"><img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo" width="100" height="100"></a>
            <?php echo "<div class='userid_text'>" . htmlspecialchars($user['user_id']) . "</div>"; ?>
        </div>
        <h2 class="nav-title">Client</h2>
        <ul>
            <li class="item"><a href="user_profile.php" style="color:#fff;font-size: 15px;">User Profile</a></li>
            <li class="item"><a href="client_menu.php" style="color:#fff;font-size: 15px;">Client Menu</a></li>
            <li class="item"><a href="shopping_cart.php" style="color:#fff;font-size: 15px;">Shopping Cart</a></li>
            <li class="item"><a class="active" href="order_details_client.php" style="color:#fff;font-size: 15px;">View Order</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h2>Order Details Client</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Menu Item ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT order_id, menu_item_id, name, quantity, price FROM order_details WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $totalAmount = 0;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $totalAmount += $row["price"] * $row["quantity"];
                        echo "<tr>
                                <td>" . $row["order_id"]. "</td>
                                <td>" . $row["menu_item_id"]. "</td>
                                <td>" . $row["name"]. "</td>
                                <td>" . $row["quantity"]. "</td>
                                <td>" . number_format($row["price"], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No order details found for User ID: $user_id</td></tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">Total Amount:</td>
                    <td><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>