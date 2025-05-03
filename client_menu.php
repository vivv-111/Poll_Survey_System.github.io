<?php
session_start();
include 'db_connect.php';

$user_id = htmlspecialchars($_SESSION['user']['user_id'] ?? null); // 防止 XSS
$user_name = htmlspecialchars($_SESSION['user']['user_name'] ?? null); // 防止 XSS
if (!$user_id) {
    header("Location: index.php"); // Redirect to login page if user is not logged in
    exit();
}

// 定義允許包含的文件列表
$allowed_files = [
    'menu_items.php',
    'shopping_cart.php',
    'order_details_client.php'
];

// 檢查用戶輸入的文件是否在允許列表中
if (isset($_GET['action'])) {
    $action = basename($_GET['action']); // 確保只獲取文件名，避免目錄穿越
    if (in_array($action, $allowed_files)) {
        include $action;
    } else {
        echo "Invalid action request.";
        exit();
    }
}

// Fetch user information from the database
$sql = "SELECT user_id, user_name, email, phone, photo FROM user_info WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // 使用參數化查詢
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
    <title>Client Menu</title>
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
            margin-left: 55px;
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

        #quantity-<?= $item['ID'] ?> {
            display: inline-block;
            min-width: 30px;
            text-align: center;
        }

        .submit_shopping_cart {
            text-align: right;
        }

        .enlarge-image {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            border: 2px solid #333;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .enlarge-image img {
            width: 100%;
            height: auto;
        }

        .enlarge-image .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #444;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .toggle-button {
            background-color: #ccc;
            border: 1px solid #ddd;
            font-size: 20px;
            cursor: pointer;
            margin-left: 10px;
            color: #333;
        }

        .toggle-button:hover {
            background-color: #a7bf69;
        }

        .section-header {
            position: relative;
        }

        .section-header .toggle-button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .userid_text {
            color: white;
            font-size: 20px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <script>
        let shoppingCart = {};

        async function updateQuantity(itemId, delta) {
            if(!shoppingCart[itemId]) {
                shoppingCart[itemId] = {
                    quantity: 0,
                    price: parseFloat(document.querySelector(`#price-${itemId}`).textContent)
                };
            }
            
            const newQty = Math.max(0, shoppingCart[itemId].quantity + delta);
            shoppingCart[itemId].quantity = newQty;
            document.getElementById(`quantity-${itemId}`).textContent = newQty;

            try {
                const response = await fetch('update_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        itemId: itemId,
                        quantity: newQty,
                        user_id: "<?php echo $user_id; ?>"
                    })
                });
                
                if(newQty === 0) delete shoppingCart[itemId];
                
            } catch (error) {
                console.error('保存失败:', error);
            }
        }

        async function addToCart(itemId) {
            const quantity = parseInt(document.getElementById(`quantity-${itemId}`).textContent);
            if (quantity > 0) {
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
                    if (result.status !== 'success') {
                        throw new Error('Failed to add item to cart');
                    }
                } catch (error) {
                    console.error('添加到购物车失败:', error);
                    alert('添加到购物车失败，请重试。');
                    return false;
                }
            } else {
                alert('Please select a quantity greater than 0');
                return false;
            }
            return true;
        }

        async function addAllToCart() {
            let hasItems = false;
            for (const itemId in shoppingCart) {
                if (shoppingCart[itemId].quantity > 0) {
                    hasItems = true;
                    const success = await addToCart(itemId);
                    if (!success) return;
                }
            }
            if (!hasItems) {
                alert('We are going to the shopping cart now. Please notice that you have not added any items to the cart yet.');
            }
            window.location.href = 'shopping_cart.php';
        }


        function enlargeImage(src) {
            const enlargeImageContainer = document.getElementById('enlarge-image-container');
            const enlargeImage = document.getElementById('enlarge-image');
            enlargeImage.src = src;
            enlargeImageContainer.style.display = 'block';
        }

        function closeEnlargeImage() {
            const enlargeImageContainer = document.getElementById('enlarge-image-container');
            enlargeImageContainer.style.display = 'none';
        }

        function toggleVisibility(type, button) {
            const rows = document.querySelectorAll(`tr[data-type='${type}']`);
            rows.forEach(row => {
                row.style.display = row.style.display === 'none' ? '' : 'none';
            });
        }

    </script>

    <nav class = "navigate">
        <div class="user-info" style="text-align: center;">
            <a href="user_profile.php" target="_self"><img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo" width="100" height="100"></a>
            <?php echo "<div class='userid_text'>" . htmlspecialchars($user['user_id']) . "</div>"; ?>
        </div>
        
        <h2 class="nav-title">MENU</h2>
        <ul>
            <li class="item"><a href="#fried_noodles" style="color:#fff;font-size: 15px;">Fried Noodles</a></li>
            <li class="item"><a href="#fried_rice" style="color:#fff;font-size: 15px;">Fried Rice</a></li>
            <li class="item"><a href="#soup_noodles" style="color:#fff;font-size: 15px;">Soup Noodles</a></li>
            <li class="item"><a href="#curry" style="color:#fff;font-size: 15px;">Curry</a></li>
            <li class="item"><a href="#toast" style="color:#fff;font-size: 15px;">Toast</a></li>
            <li class="item"><a href="#hamburger" style="color:#fff;font-size: 15px;">Hamburger</a></li>
            <li class="item"><a href="#sandwich" style="color:#fff;font-size: 15px;">Sandwich</a></li>
            <li class="item"><a href="#desserts" style="color:#fff;font-size: 15px;">Desserts</a></li>
            <li class="item"><a href="#drinks" style="color:#fff;font-size: 15px;">Drinks</a></li>
            <li class="item"><a href="logout.php" style="color:#fff;font-size: 15px;">Log Out</a></li>
        </ul>
    </nav>
    <div class="main-content">
        <table border="1">
            <?php
                $type_config = [
                    'fried_noodles' => ['title' => 'Fried Noodles', 'anchor' => 'fried_noodles'],
                    'fried_rice'    => ['title' => 'Fried Rice',    'anchor' => 'fried_rice'],
                    'soup_noodles'  => ['title' => 'Soup Noodles',  'anchor' => 'soup_noodles'],
                    'curry'         => ['title' => 'Curry',         'anchor' => 'curry'],
                    'toast'         => ['title' => 'Toast',         'anchor' => 'toast'],
                    'hamburger'     => ['title' => 'Hamburger',     'anchor' => 'hamburger'],
                    'sandwich'      => ['title' => 'Sandwich',      'anchor' => 'sandwich'],
                    'desserts'      => ['title' => 'Desserts',      'anchor' => 'desserts'],
                    'drinks'        => ['title' => 'Drinks',        'anchor' => 'drinks']
                ];

                $sql = "SELECT ID, Name, Type, Ingredients, Description, Price, photo FROM menu_items";
                $result = $conn->query($sql);

                $grouped_items = [];
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $grouped_items[$row['Type']][] = $row;
                    }
                }

                foreach ($type_config as $type => $config) {
                    echo "<tr id='" . htmlspecialchars($config['anchor']) . "' class='section-header'>
                            <td colspan='7'>
                                " . htmlspecialchars($config['title']) . "
                                <button class='toggle-button' id='toggle-button' onclick='toggleVisibility(\"" . htmlspecialchars($config['anchor']) . "\")'>View ▼</button>
                            </td>
                        </tr>
                        ";
                    
                    echo "<tr class='prefix'>
                        <td>Photo</td>
                        <td>Name</td>
                        <td>Type</td>
                        <td>Ingredients</td>
                        <td>Description</td>
                        <td>Price</td>
                        <td>Action</td>
                    </tr>";

                    if (!empty($grouped_items[$type])) {
                        foreach ($grouped_items[$type] as $item) {
                            echo "<tr data-type='" . htmlspecialchars($config['anchor']) . "'>
                                    <td style='width: 100px; height: 100px;'><img src='" . htmlspecialchars($item['photo']) . "' alt='Food Photo' style='width: 100px; height: 100px; cursor: pointer;' onclick='enlargeImage(\"" . htmlspecialchars($item['photo']) . "\")'></td>
                                    <td>" . htmlspecialchars($item['Name']) . "</td>
                                    <td>" . htmlspecialchars($type_config[$item['Type']]['title']) . "</td>
                                    <td>" . htmlspecialchars($item['Ingredients']) . "</td>
                                    <td>" . htmlspecialchars($item['Description']) . "</td>
                                    <td>" . number_format($item['Price'], 2) . "
                                        <span id='price-" . htmlspecialchars($item['ID']) . "' style='display:none'>" . htmlspecialchars($item['Price']) . "</span>
                                    </td>
                                    <td style='text-align: center;width: 120px; height: 100px;'><button onclick='updateQuantity(" . htmlspecialchars($item['ID']) . ", -1)'>-</button>
                                        <span id='quantity-" . htmlspecialchars($item['ID']) . "'>0</span>
                                        <button onclick='updateQuantity(" . htmlspecialchars($item['ID']) . ", 1)'>+</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Coming Soon</td></tr>";
                    }
                }
                $conn->close();
            ?>
            <tr>
                <td colspan="7" class="submit_shopping_cart">
                <button onclick="addAllToCart()" class="submit-button">Go to Shopping Cart</button>
                </td>
            </tr>
        </table>
    </div>

    <div id="enlarge-image-container" class="enlarge-image">
        <button class="close-btn" onclick="closeEnlargeImage()" >Close</button>
        <img id="enlarge-image" src="" alt="Enlarged Food Photo">
    </div>
</body>
</html>



