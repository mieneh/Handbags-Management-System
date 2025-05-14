<?php
    session_start();
    include '../database/connect.php';

    // Kiểm tra nếu ID đơn hàng được truyền vào
    if (!isset($_GET['id'])) {
        $_SESSION['error'] = "Không tìm thấy đơn hàng!";
        header("Location: order.php");
        exit();
    }

    $orderId = $_GET['id'];

    // Lấy thông tin đơn hàng
    $orderQuery = "SELECT * FROM Orders WHERE ID = :orderId";
    $orderStmt = $pdo->prepare($orderQuery);
    $orderStmt->execute([':orderId' => $orderId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['error'] = "Đơn hàng không tồn tại!";
        header("Location: order.php");
        exit();
    }

    $orderItemsQuery = "SELECT Products.name AS product_name, OrderItems.image, Products.categoryID, OrderItems.quantity, OrderItems.price, 
        OrderItems.shape, OrderItems.color, OrderItems.text, OrderItems.colorText 
    FROM OrderItems 
    JOIN Products ON OrderItems.productID = Products.ID 
    WHERE OrderItems.orderID = :orderId";

    $orderItemsStmt = $pdo->prepare($orderItemsQuery);
    $orderItemsStmt->execute([':orderId' => $orderId]);
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<style>
    .card-title {
        margin-top: -15px;
        margin-left: 5px;
        font-weight: bold;
        font-size: 20px;
        font-family: "Nunito", sans-serif;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Chi tiết sản phẩm</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="order.php">Đơn hàng</a></li>
                    <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="order.php"><i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">ID đơn hàng: <span style="font-weight: bold; font-size: 20px; color: red;"><?php echo $order['ID']; ?></span></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Trạng thái:</strong> <?php echo ucfirst($order['orderstatus']); ?></li>
                                    <li class="list-group-item"><strong>Tổng tiền:</strong> <?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</li>
                                    <li class="list-group-item"><strong>Ngày tạo:</strong> <?php echo date('d/m/Y', strtotime($order['created'])); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Khách hàng:</strong> <?php echo $order['guestname'] ? $order['guestname'] : $order['username']; ?></li>
                                    <li class="list-group-item"><strong>Số điện thoại:</strong> <?php echo $order['guestphone'] ? $order['guestphone'] : 'Thông tin từ tài khoản'; ?></li>
                                    <li class="list-group-item"><strong>Địa chỉ:</strong> <?php echo $order['guestaddress'] ? $order['guestaddress'] : 'Thông tin từ tài khoản'; ?></li>
                                </ul>
                            </div>
                        </div>


                        <h5 class="card-title mt-4">Danh sách sản phẩm</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">Hình ảnh</th>
                                    <th class="text-center">Tên sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Giá</th>
                                    <th class="text-center">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>" class="img-fluid" style="max-width: 80px;">
                                        </td>
                                        <td class="align-middle">
                                            <?php echo $item['product_name']; ?>
                                            <?php if ($item['categoryID'] == 2): ?>
                                                <ul class="list-unstyled mb-0">
                                                    <li><strong>Khắc chữ:</strong> <?php echo $item['text']; ?></li>
                                                    <li><strong>Màu chữ:</strong> <?php echo $item['colorText']; ?></li>
                                                </ul>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle"><?php echo $item['quantity']; ?></td>
                                        <td class="text-center align-middle"><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                        <td class="text-center align-middle"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>