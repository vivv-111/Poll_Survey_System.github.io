<?php
include 'db_connect.php';

$sql = "SELECT user_id, order_id, menu_item_id, name, quantity, price FROM order_details ORDER BY user_id, order_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$current_user_id = null;
$current_order_id = null;
$totalAmount = 0;
$grandTotal = 0; // Calculate the grand total amount of all orders
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
            <li class="item"><a href="view_orders.php" style="color:#fff;font-size: 15px;">View Orders</a></li>
            <li class="item"><a class="active" href="order_details.php" style="color:#fff;font-size: 15px;">Order Details</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h2>Order Details Of Client</h2>
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
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        //if current user_id is different from the previous row, insert the total amount row
                        if ($current_user_id !== null && $current_user_id !== $row["user_id"]) {
                            echo "<tr>
                                    <td colspan='5' style='text-align: right;'>Total Amount for User ID: $current_user_id, Order ID: $current_order_id</td>
                                    <td>" . number_format($totalAmount, 2) . "</td>
                                  </tr>";
                            //reset total amount
                            $totalAmount = 0;
                        }

                        // update current user_id and order_id
                        $current_user_id = $row["user_id"];
                        $current_order_id = $row["order_id"];
                        $totalAmount += $row["price"] * $row["quantity"];
                        $grandTotal += $row["price"] * $row["quantity"];

                        echo "<tr>
                                <td>" . $row["user_id"]. "</td>
                                <td>" . $row["order_id"]. "</td>
                                <td>" . $row["menu_item_id"]. "</td>
                                <td>" . $row["name"]. "</td>
                                <td>" . $row["quantity"]. "</td>
                                <td>" . number_format($row["price"], 2) . "</td>
                              </tr>";
                    }
                    echo "<tr>
                            <td colspan='5' style='text-align: right;'>Total Amount for User ID: $current_user_id, Order ID: $current_order_id</td>
                            <td>" . number_format($totalAmount, 2) . "</td>
                          </tr>";

                    echo "<tr>
                            <td colspan='5' style='text-align: right; font-weight: bold;'>Grand Total Amount</td>
                            <td style='font-weight: bold;'>" . number_format($grandTotal, 2) . "</td>
                          </tr>";
                } else {
                    echo "<tr><td colspan='6'>No order details found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>