<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = $_POST['id'];
    $orderQuery = "DELETE FROM Orders WHERE ID = ?";
    $orderStmt = $pdo->prepare($orderQuery);
    $orderStmt->execute([$orderID]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['success'] = "Mã đơn hàng đã được xóa thành công!";
        header('Location: order.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Mã đơn hàng không hợp lệ.";
    header('Location: order.php');
    exit();
}

?>