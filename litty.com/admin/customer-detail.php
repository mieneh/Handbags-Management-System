<?php
session_start();
include '../database/connect.php';

// Kiểm tra nếu có ID khách hàng trong URL
if (isset($_GET['id'])) {
    $customerId = $_GET['id'];

    // Truy vấn thông tin khách hàng từ cơ sở dữ liệu
    $sql = "SELECT * FROM Users WHERE ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $customerId);
    $stmt->execute();

    // Kiểm tra xem có dữ liệu không
    if ($stmt->rowCount() > 0) {
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Khách hàng không tồn tại!";
        header('Location: customer.php');
        exit();
    }
} else {
    $_SESSION['error'] = "ID khách hàng không hợp lệ!";
    header('Location: customer.php');
    exit();
}
?>

<?php include 'header.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Chi tiết khách hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="customer.php">Khách hàng</a></li>
                    <li class="breadcrumb-item active">Chi tiết khách hàng</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="customer.php"><i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thông tin khách hàng</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Họ & Tên:</strong> <?php echo htmlspecialchars($customer['fullname']); ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></li>
                    <li class="list-group-item"><strong>Số Điện Thoại:</strong> <?php echo htmlspecialchars($customer['phone']); ?></li>
                    <li class="list-group-item"><strong>Địa Chỉ:</strong> <?php echo htmlspecialchars($customer['address']); ?></li>
                    <li class="list-group-item"><strong>Tỉnh / Thành Phố:</strong> <?php echo htmlspecialchars($customer['city']); ?></li>
                    <li class="list-group-item"><strong>Quận / Huyện:</strong> <?php echo htmlspecialchars($customer['district']); ?></li>
                    <li class="list-group-item"><strong>Phường / Xã:</strong> <?php echo htmlspecialchars($customer['ward']); ?></li>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>