<?php
include 'db_connect.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Order ID is required.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details Admin</title>
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
</head>
<body>
    <nav class="navigate">
        <br>
        <h2 class="nav-title">Restaurant</h2>
        <ul>
            <li class="item"><a href="admin_index.php" style="color:#fff;font-size: 15px;">Home</a></li>
            <li class="item"><a href="add_item.php" style="color:#fff;font-size: 15px;">Add Item</a></li>
            <li class="item"><a href="manage_items.php" style="color:#fff;font-size: 15px;">Manage Items</a></li>
            <li class="item"><a href="manage_user.php" style="color:#fff;font-size: 15px;">Manage Users</a></li>
            <li class="item"><a class="active" href="view_orders.php" style="color:#fff;font-size: 15px;">View Orders</a></li>
            <li class="item"><a href="order_details.php" style="color:#fff;font-size: 15px;">Order Details</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h2>Order Details for Order ID: <?php echo htmlspecialchars($order_id); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Order ID</th>
                    <th>Menu Item ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT user_id, order_id, menu_item_id, name, quantity, price FROM order_details WHERE order_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $totalAmount = 0;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $totalAmount += $row["price"] * $row["quantity"];
                        echo "<tr>
                                <td>" . $row["user_id"]. "</td>
                                <td>" . $row["order_id"]. "</td>
                                <td>" . $row["menu_item_id"]. "</td>
                                <td>" . $row["name"]. "</td>
                                <td>" . $row["quantity"]. "</td>
                                <td>" . number_format($row["price"], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No order details found for Order ID: $order_id</td></tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Amount:</td>
                    <td><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>