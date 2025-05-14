<?php
session_start();
include '../database/connect.php';

// Kiểm tra xem mã đơn hàng có trong URL không
if (isset($_GET['order_code'])) {
    $orderCode = $_GET['order_code'];
    
    // Truy vấn để lấy thông tin đơn hàng từ mã đơn hàng
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE ID = :order_code");
    $stmt->execute(['order_code' => $orderCode]);
    $order = $stmt->fetch();

    if ($order) {
        // Truy vấn để lấy chi tiết sản phẩm từ đơn hàng, bao gồm thông tin bổ sung nếu category = 2
        $stmt = $pdo->prepare("SELECT Products.name, OrderItems.image, Products.categoryID, OrderItems.quantity, OrderItems.price, 
                                      OrderItems.shape, OrderItems.color, OrderItems.text, OrderItems.colorText 
                               FROM OrderItems 
                               JOIN Products ON OrderItems.productID = Products.ID 
                               WHERE OrderItems.orderID = :order_id");
        // $stmt = $pdo->prepare("SELECT * FROM OrderItems WHERE orderID = :order_id");
        $stmt->execute(['order_id' => $order['ID']]);
        $orderItems = $stmt->fetchAll();
    } else {
        $error = "Mã đơn hàng không tồn tại.";
    }
}
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/order.css">

<section class="section">
    <div class="heading">
        <h2>Chi Tiết Đơn Hàng</h2>
    </div>
    <div class="order-details-list">
        <?php if (isset($order)): ?>
            <div class="product-list">
                <h3>Sản Phẩm</h3>
                <ul>
                    <?php foreach ($orderItems as $item): ?>
                        <li class="product-item">
                            <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                        
                            <div class="product-info">
                                <h4>Sản phẩm: <?= htmlspecialchars($item['name']); ?></h4>
                                <p><strong>Số lượng:</strong> <?= htmlspecialchars($item['quantity']); ?></p>
                                <p><strong>Giá:</strong> <?= htmlspecialchars(number_format($item['price'], 0, ',', '.')); ?> VND</p>
                                <?php if ($item['categoryID'] == 2): ?>
                                    <div class="customization-info">
                                        <p><strong>Khắc chữ:</strong> <?= htmlspecialchars($item['text']); ?></p>
                                        <p><strong>Màu chữ:</strong> <?= htmlspecialchars($item['colorText']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="order-info">
                <div class="order-product-info">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="order-product-info-list">
                        <p><strong>Mã đơn hàng: <span style="color: #d9534f;"><?= htmlspecialchars($order['ID']); ?></span></strong></p>
                        <p><strong>Ngày đặt hàng:</strong> <?= htmlspecialchars($order['created']); ?></p>
                        <p><strong>Trình trạng: <span style="color: #d9534f;"><?= htmlspecialchars($order['orderstatus']); ?></span></strong></p>
                        <p><strong>Tổng tiền:</strong> <?= htmlspecialchars(number_format($order['total'], 0, ',', '.')); ?> VND</p>
                    </div>
                </div>
                
                <div class="order-customer-info">
                    <h3>Thông tin khách hàng:</h3>
                    <div class="order-customer-info-list">
                        <p><strong>Họ và tên:</strong> <?= htmlspecialchars($order['guestname']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['guestphone']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['guestaddress']); ?></p>
                    </div>
                </div>
            </div>
        <?php elseif (isset($error)): ?>
            <p><?= htmlspecialchars($error); ?></p>
        <?php else: ?>
            <p>Vui lòng nhập mã đơn hàng để xem chi tiết.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
