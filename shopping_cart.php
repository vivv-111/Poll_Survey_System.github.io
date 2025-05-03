<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user']['user_id'] ?? null;
$user_name = $_SESSION['user']['user_name'] ?? null;
if (!$user_id) {
    header("Location: client_menu.php"); // Redirect to login page if user is not logged in
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
    <title>Shopping Cart</title>
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

        .submit_shopping_cart {
            text-align: right;
        }

        .userid_text {
            color: white;
            font-size: 20px;
            margin-top: 10px;
        }

        .submit-button {
            background-color: #ccc;
            border: 1px solid #ddd;
            font-size: 20px;
            cursor: pointer;
            margin-right: 10px;
            color: #333;
            padding: 10px 40px;
            border-radius: 4px;
        }

        .submit-button:hover {
            background-color: #a7bf69;
        }

        a.active {
            background-color: #a7bf69;
            color: white;
        }


    </style>
    <script>
        let shoppingCart = {};

        async function updateQuantity(itemId, delta) {
            const quantityElement = document.getElementById(`quantity-${itemId}`);
            let quantity = parseInt(quantityElement.textContent);
            quantity = Math.max(0, quantity + delta);
            quantityElement.textContent = quantity;

            shoppingCart[itemId] = quantity;

            try {
                const response = await fetch('update_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        itemId: itemId,
                        quantity: quantity,
                        user_id: "<?php echo $user_id; ?>"
                    })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    location.reload();
                } else {
                    alert('Failed to update cart');
                }
            } catch (error) {
                console.error('Failed to update cart:', error);
                alert('Failed to update cart, please try again.');
            }
        }

        async function submitOrder() {
            try {
                const response = await fetch('submit_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        shoppingCart: shoppingCart,
                        user_id: "<?php echo $user_id; ?>"
                    })
                });
                const result = await response.json();
                alert(result.message);
                if (result.status === 'success') {
                    showUserInfo();
                }
            } catch (error) {
                console.error('提交失败:', error);
                alert('提交订单失败，请重试。');
            }
        }

        function showUserInfo() {
            const userId = "<?php echo $user_id; ?>";
            const userName = "<?php echo $user_name; ?>";
            alert(  `User ID: ${userId}\nUser Name: ${userName}\nYour order has been submitted!`);
            window.location.href = 'shopping_cart.php';
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
            <li class="item"><a href="user_profile.php" style="color:#fff;font-size: 15px;">User Profile</a></li>
            <li class="item"><a href="client_menu.php" style="color:#fff;font-size: 15px;">Client Menu</a></li>
            <li class="item"><a class="active" href="shopping_cart.php" style="color:#fff;font-size: 15px;">Shopping Cart</a></li>
            <li class="item"><a href="order_details_client.php" style="color:#fff;font-size: 15px;">View Order</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <h2>Shopping Cart</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, name, price, Quantity, (Quantity * price) AS total_price FROM shopping_cart WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $totalAmount = 0;

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $totalAmount += $row["total_price"];
                        echo "<tr>
                                <td>" . $row["id"]. "</td>
                                <td>" . $row["name"]. "</td>
                                <td>
                                    <button onclick='updateQuantity(" . $row["id"] . ", -1)'>-</button>
                                    <span id='quantity-" . $row["id"] . "'>" . $row["Quantity"] . "</span>
                                    <button onclick='updateQuantity(" . $row["id"] . ", 1)'>+</button>
                                </td>
                                <td>" . number_format($row["price"], 2) . "</td>
                                <td>" . number_format($row["total_price"], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No items in cart</td></tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">Total:</td>
                    <td><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="submit_shopping_cart">
            <button onclick="submitOrder()" class="submit-button">Buy</button>
        </div>
    </div>
</body>
</html>
