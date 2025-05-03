<?php
include 'db_connect.php';
session_start();

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

// Retrieve items from the shopping cart
$sql = "SELECT id, name, price, Quantity, (Quantity * price) AS total_price FROM shopping_cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $orderItems = [];
    $totalAmount = 0;

    while($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
        $totalAmount += $row['total_price'];
    }

    // Check if there is an existing order for the same user within the last 2 hours
    $stmt = $conn->prepare("SELECT order_id, total_amount FROM orders WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $existingOrder = $stmt->get_result()->fetch_assoc();

    if ($existingOrder) {
        // Update the existing order's total amount
        $newTotalAmount = $existingOrder['total_amount'] + $totalAmount;
        $orderId = $existingOrder['order_id'];

        $stmt = $conn->prepare("UPDATE orders SET total_amount = ? WHERE order_id = ?");
        $stmt->bind_param("di", $newTotalAmount, $orderId);
        $stmt->execute();
    } else {
        // Insert a new order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $user_id, $totalAmount);
        $stmt->execute();
        $orderId = $stmt->insert_id;
    }

    // Insert or update order_details table
    foreach ($orderItems as $item) {
        // Check if the item already exists in the order_details
        $stmt = $conn->prepare("SELECT quantity, price FROM order_details WHERE order_id = ? AND menu_item_id = ?");
        $stmt->bind_param("ii", $orderId, $item['id']);
        $stmt->execute();
        $existingItem = $stmt->get_result()->fetch_assoc();

        if ($existingItem) {
            // Update the existing item's quantity and price
            $newQuantity = $existingItem['quantity'] + $item['Quantity'];
            $newPrice = $existingItem['price'] + ($item['price'] * $item['Quantity']);
            $stmt = $conn->prepare("UPDATE order_details SET quantity = ?, price = ? WHERE order_id = ? AND menu_item_id = ?");
            $stmt->bind_param("idii", $newQuantity, $newPrice, $orderId, $item['id']);
        } else {
            // Insert a new item into order_details
            $stmt = $conn->prepare("INSERT INTO order_details (order_id, menu_item_id, name, quantity, price, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissdi", $orderId, $item['id'], $item['name'], $item['Quantity'], $item['price'], $user_id);
        }

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert/update order item: ' . $stmt->error]);
            exit;
        }
    }

    // Clear the shopping cart
    $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to clear shopping cart: ' . $stmt->error]);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => "Order submitted successfully! Total: $" . number_format($totalAmount, 2)]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Shopping cart is empty']);
}

$conn->close();
?>

