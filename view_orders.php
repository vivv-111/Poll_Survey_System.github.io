<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders</title>
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
        <h2>View Client's Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Order ID</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Total Amount</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT user_id, order_id, total_amount, created_at, updated_at FROM orders";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["user_id"]. "</td>
                                <td>" . $row["order_id"]. "</td>
                                <td>" . $row["created_at"]. "</td>
                                <td>" . $row["updated_at"]. "</td>
                                <td>" . number_format($row["total_amount"], 2) . "</td>
                                <td><button onclick='viewOrderDetails(" . $row["order_id"] . ")'>View Details</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            window.location.href = 'view_orders_admin.php?order_id=' + orderId;
        }
    </script>
</body>
</html>