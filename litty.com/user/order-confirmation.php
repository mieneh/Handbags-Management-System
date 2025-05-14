<?php
    session_start();
    include '../database/connect.php';

    if (!isset($_SESSION['orderID'])) {
        header('Location: cart.php');
        exit();
    }

    $orderID = $_SESSION['orderID'];
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE ID = ?");
    $stmt->execute([$orderID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtItems = $pdo->prepare("SELECT Products.name, OrderItems.quantity, OrderItems.price 
                                FROM OrderItems 
                                JOIN Products ON OrderItems.productID = Products.ID 
                                WHERE OrderItems.orderID = ?");
    $stmtItems->execute([$orderID]);
    $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/order.css">

<section class="section">
    <div class="heading">
        <h2>Thông Tin Đơn Hàng</h2>
    </div>
    <div class="order-confirmation">
        <p>Cảm ơn bạn đã đặt hàng! Dưới đây là thông tin đơn hàng của bạn:</p>
        <h2>Mã đơn hàng: <?php echo htmlspecialchars($order['ID']); ?></h2>
        <div class="order-wrapper">
            <div class="order-details">
                <h3>Danh sách sản phẩm:</h3>
                <ul>
                    <?php foreach ($orderItems as $item): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong> - 
                        Số lượng: <?php echo $item['quantity']; ?> - 
                        Giá: <?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ
                    </li>
                    <?php endforeach; ?>
                </ul>
                <p class="total">Tổng tiền: <?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</p>
                <p><strong>Trạng thái đơn hàng:</strong> <?php echo htmlspecialchars($order['orderstatus']); ?></p>
                <p><strong>Ngày tạo:</strong> <?php echo htmlspecialchars($order['created']); ?></p>
            </div>

            <!-- Thông tin khách hàng - Cột phải -->
            <div class="customer-info">
                <h3>Thông tin khách hàng:</h3>
                <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['guestname']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['guestphone']); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['guestaddress']); ?></p>
            </div>
        </div>

        <a href="product.php" class="btn">Tiếp tục mua sắm</a>
    </div>
</section>

<?php include 'footer.php'; ?>