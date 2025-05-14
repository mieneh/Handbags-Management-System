<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $discountID = $_POST['id'];
    $discountQuery = "DELETE FROM Discounts WHERE ID = ?";
    $discountStmt = $pdo->prepare($discountQuery);
    $discountStmt->execute([$discountID]);
    $discount = $discountStmt->fetch(PDO::FETCH_ASSOC);

    if (!$discount) {
        $_SESSION['success'] = "Mã khuyến mãi đã được xóa thành công!";
        header('Location: discount.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Mã khuyến mãi không hợp lệ.";
    header('Location: discount.php');
    exit();
}

?>