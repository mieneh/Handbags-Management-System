<?php
session_start();
include '../database/connect.php';

// Xử lý cập nhật số lượng sản phẩm khi thay đổi giá trị
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    if (isset($_POST['productID'], $_POST['quantity'])) {
        $productID = $_POST['productID'];
        $new_quantity = (int)$_POST['quantity'];
        // Nếu số lượng hợp lệ thì cập nhật giỏ hàng
        if ($new_quantity > 0) {
            $_SESSION['cart'][$productID]['quantity'] = $new_quantity;
        } else {
            // Nếu số lượng <= 0 thì xóa sản phẩm khỏi giỏ hàng
            unset($_SESSION['cart'][$productID]);
        }
        // Sau khi xử lý, điều hướng lại về trang giỏ hàng để tránh refresh gửi lại form
        header('Location: cart.php');
        exit();
    }
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$showNotesColumn = false;
foreach ($cart as $item) {
    if (isset($item['price'], $item['quantity'])) {
        $total += $item['price'] * $item['quantity'];
    }
}
foreach ($cart as $item) {
    if (!empty($item['text']) || !empty($item['colorText'])) {
        $showNotesColumn = true;
        break;
    }
}

?>

<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/cart.css">

<section class="section">
    <div class="heading">
        <h2>Giỏ hàng</h2>
    </div>
    <div class="cart-container">
        <?php if (count($cart) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <?php
                            if ($showNotesColumn) {
                                echo '<th>Ghi chú</th>';
                            }
                        ?>
                        <th>Tổng cộng</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <?php if (isset($item['id'], $item['name'], $item['price'], $item['quantity'])): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100px; height: auto;"></td>
                                <?php  
                                    if (!empty($item['shape']) || !empty($item['color'])) {
                                        // Chỉ hiển thị hình dạng và màu sắc nếu ít nhất một trong hai thuộc tính không rỗng
                                        echo '<td>' . htmlspecialchars(ucfirst($item['name']) . ' - ' . ucfirst($item['shape']) . ' - ' . ucfirst($item['color'])) . '</td>';
                                    } else {
                                        echo '<td>' . htmlspecialchars(ucfirst($item['name'])) . '</td>';
                                    }
                                ?>

                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <!-- Form tự động cập nhật số lượng khi người dùng thay đổi -->
                                    <form method="post" action="cart.php">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                        <input type="hidden" name="productID" value="<?php echo $item['id']; ?>">
                                    </form>
                                </td>
                                <?php if ($showNotesColumn) {
                                        $notes = [];
                                        if (!empty($item['text'])) {
                                            $notes[] = 'Khắc chữ: ' . htmlspecialchars($item['text']);
                                        }
                                        if (!empty($item['colorText'])) {
                                            $notes[] = 'Màu chữ: ' . htmlspecialchars($item['colorText']);
                                        }
                                        echo '<td>' . implode('<br>', $notes) . '</td>';
                                    }
                                ?>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <!-- Form xóa sản phẩm -->
                                    <form method="post" action="cart-delete.php">
                                        <input type="hidden" name="productID" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="quantity" value="0">X</button>
                                    </form>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Sản phẩm không hợp lệ.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h3>
            <a href="checkout.php" class="btn">Thanh toán</a>
        <?php else: ?>
            <p>Giỏ hàng của bạn trống.</p>
        <?php endif; ?>
    </div>    
</section>

<?php include 'footer.php'; ?>