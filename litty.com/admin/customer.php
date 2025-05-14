<?php
session_start();
include '../database/connect.php';

// Truy vấn danh sách khách hàng
$customerQuery = "SELECT * FROM Users ORDER BY ID ASC";
$customerStmt = $pdo->prepare($customerQuery);
$customerStmt->execute();
$customers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

// Cập nhật thông tin khách hàng
if (isset($_POST['update_customer'])) {
    $customerId = $_POST['customer_id'];
    $newName = $_POST['new_name'];
    $newEmail = $_POST['new_email'];
    $newPhone = $_POST['new_phone'];
    $newAddress = $_POST['new_address'];

    $updateQuery = "UPDATE Users SET name = :newName, email = :newEmail, phone = :newPhone, address = :newAddress WHERE ID = :customerId";
    $updateStmt = $pdo->prepare($updateQuery);
    if ($updateStmt->execute([':newName' => $newName, ':newEmail' => $newEmail, ':newPhone' => $newPhone, ':newAddress' => $newAddress, ':customerId' => $customerId])) {
        $_SESSION['success'] = 'Cập nhật thông tin khách hàng thành công.';
    } else {
        $_SESSION['error'] = 'Cập nhật thông tin khách hàng thất bại.';
    }

    header("Location: customer.php");
    exit();
}
?>

<?php include 'header.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Khách hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Khách hàng</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="customer-add.php"><i class="bi bi-person-plus-fill"></i></a>
            </div>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Tên</th>
                                    <th class="text-center align-middle">Email</th>
                                    <th class="text-center align-middle">Số điện thoại</th>
                                    <th class="text-center align-middle">Địa chỉ</th>
                                    <th class="text-center align-middle" style="width: 160px">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $index => $customer): ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $customer['ID']; ?></td>
                                        <td class="align-middle"><?php echo htmlspecialchars($customer['fullname']); ?></td>
                                        <td class="align-middle"><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td class="align-middle"><?php echo htmlspecialchars($customer['city']); ?></td>
                                        <td class="align-middle">
                                            <a class="btn btn-info" href="customer-detail.php?id=<?php echo $customer['ID']; ?>"><i class="bi bi-eye"></i></a>
                                            <a class="btn btn-success" href="customer-edit.php?id=<?php echo $customer['ID']; ?>"><i class="bi bi-pen"></i></a>
                                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $customer['ID']; ?>"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- Modal xác nhận xóa -->
                                    <div class="modal fade" id="deleteModal<?php echo $customer['ID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Bạn có chắc chắn muốn xóa khách hàng này không?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <form action="customer-delete.php" method="post" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $customer['ID']; ?>">
                                                        <button type="submit" class="btn btn-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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