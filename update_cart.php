<?php
include 'db_connect.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$itemId = intval($data['itemId'] ?? 0);
$quantity = intval($data['quantity'] ?? 0);

// Check if itemId and quantity are valid
if ($itemId <= 0 || $quantity < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid item ID or quantity']);
    exit;
}

// get item details
$stmt = $conn->prepare("SELECT price, name FROM menu_items WHERE ID = ?");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo json_encode(['status' => 'error', 'message' => 'Item not found']);
    exit;
}

if ($quantity > 0) {

    $total = $item['price'] * $quantity;
    $stmt = $conn->prepare("
        INSERT INTO shopping_cart 
            (id, name, price, Quantity, total_price, user_id)
        VALUES 
            (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            Quantity = VALUES(Quantity),
            total_price = VALUES(total_price)
    ");
    $stmt->bind_param("issdis", $itemId, $item['name'], $item['price'], $quantity, $total, $user_id);
} else {
    
    $stmt = $conn->prepare("DELETE FROM shopping_cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>