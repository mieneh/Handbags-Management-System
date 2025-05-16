<?php
session_start();
include '../database/connect.php';

$userID = $_SESSION['user'] ?? null;

if (!$userID) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT Orders.*, SUM(OrderItems.quantity) AS quantity
    FROM Orders
    LEFT JOIN OrderItems ON Orders.ID = OrderItems.orderID
    WHERE Orders.userID = ?
    GROUP BY Orders.ID
    ORDER BY Orders.created DESC
");
$stmt->execute([$userID]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete_order'])) {
    $orderID = $_GET['delete_order'];

    // Kiểm tra xem đơn hàng có tồn tại và thuộc về người dùng đang đăng nhập hay không
    $checkStmt = $pdo->prepare("SELECT * FROM Orders WHERE ID = ? AND userID = ?");
    $checkStmt->execute([$orderID, $userID]);

    if ($checkStmt->rowCount() > 0) {
        // Lấy thông tin đơn hàng để lấy mã giảm giá
        $order = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $discountCode = $order['discountCode'] ?? null;

        // Nếu có mã giảm giá thì cập nhật lại limitUse
        if ($discountCode) {
            $updateDiscountStmt = $pdo->prepare("UPDATE Discounts SET limitUse = limitUse + 1 WHERE ID = ?");
            $updateDiscountStmt->execute([$discountCode]);
        }

        // Xóa các mục trong OrderItems liên quan đến đơn hàng
        $deleteItemsStmt = $pdo->prepare("DELETE FROM OrderItems WHERE orderID = ?");
        $deleteItemsStmt->execute([$orderID]);

        // Xóa đơn hàng
        $deleteOrderStmt = $pdo->prepare("DELETE FROM Orders WHERE ID = ?");
        $deleteOrderStmt->execute([$orderID]);

        // Chuyển hướng trở lại trang danh sách đơn hàng với thông báo
        header('Location: order.php?message=Đơn hàng đã được xóa thành công.');
        exit();
    } else {
        // Xử lý nếu đơn hàng không tồn tại
        header('Location: order.php?error=Đơn hàng không tồn tại hoặc không thuộc về bạn.');
        exit();
    }
}
?>

<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/order.css">

<section class="section">
    <div class="heading">
        <h2>Danh Sách Đơn Hàng</h2>
    </div>
    <div class="order-container">
        <?php if (count($orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Đơn hàng</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="order-detail.php?order_code=<?php echo htmlspecialchars($order['ID']); ?>">
                                    <?php echo htmlspecialchars($order['ID']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                            <td><?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo htmlspecialchars($order['orderstatus']); ?></td>
                            <td><?php echo htmlspecialchars($order['created']); ?></td>
                            <td>
                                <form action="" method="GET" style="display:inline;">
                                    <input type="hidden" name="delete_order" value="<?php echo htmlspecialchars($order['ID']); ?>">
                                    <button type="submit" 
                                        <?php if ($order['orderstatus'] !== 'pending') echo 'disabled'; ?>
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');"
                                            style="background: var(--main-color);  padding: 5px 10px; color: white; border: none; border-radius: 4px; 
                                                cursor: <?php echo ($order['orderstatus'] === 'pending') ? 'pointer' : 'not-allowed'; ?>; 
                                                opacity: <?php echo ($order['orderstatus'] === 'pending') ? '1' : '0.5'; ?>;
                                            "
                                        >X
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        <?php else: ?>
            <p>Hiện tại bạn chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </div>    
</section>

<?php include 'footer.php'; ?>