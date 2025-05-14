<?php
    session_start();
    // Kiểm tra nếu có sản phẩm cần xóa
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productID'])) {
        $productID = $_POST['productID'];
        // Xóa sản phẩm khỏi giỏ hàng
        if (isset($_SESSION['cart'][$productID])) {
            unset($_SESSION['cart'][$productID]);
        }
        // Chuyển hướng lại về trang giỏ hàng
        header('Location: cart.php');
        exit();
    }
?>
