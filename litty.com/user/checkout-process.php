<?php
session_start();
include '../database/connect.php';

// Kiểm tra xem có giỏ hàng không
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php'); // Nếu không có sản phẩm, quay lại giỏ hàng
    exit();
}

// Lấy thông tin từ form
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$address = $_POST['address'] ?? null;
$city = $_POST['city'] ?? null;
$district = $_POST['district'] ?? null;
$ward = $_POST['ward'] ?? '';
$phone = $_POST['phone'] ?? null;
$shipping_method = $_POST['shipping_method'] ?? 'home_delivery'; // Mặc định là giao hàng tận nơi
$payment_method = $_POST['payment_method'] ?? 'cod'; // Mặc định là thanh toán khi giao hàng
$total = isset($_POST['total']) ? (float)$_POST['total'] : 0;
$discountCode = $_POST['discount_code'] ?? null;


// Hàm sinh ID cho đơn hàng
function generateOrderID($pdo) {
    $id = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6)); // Tạo ID ngẫu nhiên 6 ký tự
    // Kiểm tra xem ID đã tồn tại trong bảng Orders hay chưa
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Orders WHERE ID = :id");
    $stmt->execute(['id' => $id]);
    if ($stmt->fetchColumn() > 0) {
        return generateOrderID($pdo); // Tạo ID mới nếu đã tồn tại
    }
    return $id; // Trả về ID mới
}

// Tạo ID đơn hàng
$orderID = generateOrderID($pdo);

$userID = isset($_SESSION['user']) ? $_SESSION['user'] : null;
if ($userID) {
    $stmt = $pdo->prepare("INSERT INTO Orders (ID, guestname, guestaddress, guestphone, total, discountCode, userID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orderID, $name, $address . ', ' . $ward . ', ' . $district . ', ' . $city, $phone, $total, $discountCode, $userID]);
} else {
    $stmt = $pdo->prepare("INSERT INTO Orders (ID, guestname, guestaddress, guestphone, total, discountCode) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orderID, $name, $address . ', ' . $ward . ', ' . $district . ', ' . $city, $phone, $total, $discountCode]);
}

// Cập nhật session để lưu orderID
$_SESSION['orderID'] = $orderID;

// Thêm các sản phẩm vào bảng OrderItems
foreach ($_SESSION['cart'] as $key => $item) {
    if (isset($item['quantity'], $item['price'])) {
        $stmt = $pdo->prepare("INSERT INTO OrderItems (orderID, productID, quantity, price, shape, color, text, colorText, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderID, $item['id'], $item['quantity'], $item['price'], $item['shape'] ?? null, $item['color'] ?? null, $item['text'] ?? null, $item['colorText'] ?? null, $item['image'] ?? null]);
    }
}

// Giảm số lần sử dụng mã giảm giá nếu có
if (!empty($discountCode)) {
    $stmt = $pdo->prepare("UPDATE Discounts SET limitUse = limitUse - 1 WHERE ID = ? AND limitUse > 0");
    $stmt->execute([$discountCode]);
}

// Xóa giỏ hàng sau khi hoàn tất đơn hàng
unset($_SESSION['cart']);

// Chuyển hướng đến trang xác nhận đơn hàng
header('Location: order-confirmation.php');
exit();
?>