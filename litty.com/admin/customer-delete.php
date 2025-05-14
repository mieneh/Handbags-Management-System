<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = $_POST['id'];
    $customerQuery = "DELETE FROM Users WHERE ID = ?";
    $customerStmt = $pdo->prepare($customerQuery);
    $customerStmt->execute([$customerID]);
    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        $_SESSION['success'] = "Khách hàng đã được xóa thành công!";
        header('Location: customer.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Mã Khách hàng không hợp lệ.";
    header('Location: customer.php');
    exit();
}

?>