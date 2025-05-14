<?php
session_start();
include '../database/connect.php';

$orderQuery = "SELECT * FROM Orders ORDER BY created DESC";
$orderStmt = $pdo->prepare($orderQuery);
$orderStmt->execute();
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    $updateQuery = "UPDATE Orders SET orderstatus = :newStatus WHERE ID = :orderId";
    $updateStmt = $pdo->prepare($updateQuery);
    if ($updateStmt->execute([':newStatus' => $newStatus, ':orderId' => $orderId])) {
        $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công.';
    } else {
        $_SESSION['error'] = 'Cập nhật trạng thái đơn hàng thất bại.';
    }

    header("Location: order.php");
    exit();
}
?>

<?php include 'header.php';?>

<style>
    .breadcrumb {
        margin-top: 7px;
    }
</style>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Đơn hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Đơn hàng</li>
                </ol>
            </nav>
        </div>
    </div>
    <div>
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert' id='success-notification'>
                    <i class='bi bi-check-circle me-1'></i>" 
                    . $_SESSION['success'] . 
                    "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' id='error-notification'>
                    <i class='bi bi-exclamation-octagon me-1'></i>" 
                    . $_SESSION['error'] . 
                    "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            unset($_SESSION['error']);
        }
        ?>

        <script>
            setTimeout(function() {
                var successNotification = document.getElementById('success-notification');
                if (successNotification) {
                    successNotification.style.display = 'none';
                }

                var errorNotification = document.getElementById('error-notification');
                if (errorNotification) {
                    errorNotification.style.display = 'none';
                }
            }, 1500);
        </script>
    </div>

    <section class="section mt-3">
        <div class="row" style="margin-top: 26px;">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Trạng thái</th>
                                    <th class="text-center align-middle">Tổng</th>
                                    <th class="text-center align-middle">Ngày tạo</th>
                                    <th class="text-center align-middle">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $index => $order): ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $order['ID']; ?></td>
                                        <td class="text-center align-middle">
                                            <form method="POST" action="">
                                                <input type="hidden" name="order_id" value="<?php echo $order['ID']; ?>">
                                                <input type="hidden" name="update_status" value="1">
                                                <select name="new_status" onchange="this.form.submit()" class="form-select">
                                                    <option value="pending" <?php echo ($order['orderstatus'] == 'pending') ? 'selected' : ''; ?>>Đang chờ</option>
                                                    <option value="processing" <?php echo ($order['orderstatus'] == 'processing') ? 'selected' : ''; ?>>Đang xử lý</option>
                                                    <option value="completed" <?php echo ($order['orderstatus'] == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
                                                    <option value="canceled" <?php echo ($order['orderstatus'] == 'canceled') ? 'selected' : ''; ?>>Đã hủy</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="text-center align-middle"><?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</td>
                                        <td class="text-center align-middle"><?php echo date('d/m/Y H:i', strtotime($order['created'])); ?></td>
                                        <td class="text-center align-middle">
                                            <a class="btn btn-info" href="order-detail.php?id=<?php echo $order['ID']; ?>"><i class="bi bi-eye"></i></a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $order['ID']; ?>"><i class="bi bi-trash"></i></button>
                                        </td>

                                        <div class="modal fade" id="deleteModal<?php echo $order['ID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn xóa đơn hàng này không?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <form action="order-delete.php" method="post" class="d-inline">
                                                            <input type="hidden" name="id" value="<?php echo $order['ID']; ?>">
                                                            <button type="submit" class="btn btn-danger">Xóa</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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