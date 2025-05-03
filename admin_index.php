<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">   
<head>
    <title>Restaurant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 90px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        button {
            display: block;
            width: 250px;
            padding: 20px;
            margin: 15px auto;
            border: none;
            border-radius: 4px;
            background-color: #93c47d;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #38761d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Restaurant Admin</h1><br>
        <button class="add-item" onclick="window.location.href='add_item.php'">Add Item</button>
        <button class="manage-items" onclick="window.location.href='manage_items.php'">Manage Items</button>
        <button class="manage-items" onclick="window.location.href='manage_user.php'">Manage Users</button>
        <button class="view-orders" onclick="window.location.href='view_orders.php'">View Orders</button>
        <button class="order-details" onclick="window.location.href='order_details.php'">Order Details</button>
        <button class="order-details" onclick="window.location.href='logout.php'">Log Out</button>
    </div>
</body>
</html>