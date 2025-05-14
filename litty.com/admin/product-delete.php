<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = $_POST['id'];
    $productQuery = "SELECT * FROM Products WHERE ID = ?";
    $productStmt = $pdo->prepare($productQuery);
    $productStmt->execute([$productID]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);

    $imagePath = $product['image'];
    $deleteQuery = "DELETE FROM Products WHERE ID = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);

    if ($deleteStmt->execute([$productID])) {
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $_SESSION['success'] = "Sản phẩm đã được xóa thành công!";
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi xóa sản phẩm.";
    }
    header('Location: product.php');
    exit();
} else {
    $_SESSION['error'] = "Mã sản phẩm không hợp lệ.";
    header('Location: product.php');
    exit();
}

?>