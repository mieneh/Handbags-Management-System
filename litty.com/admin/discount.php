<?php
    session_start();
    include '../database/connect.php';

    $query = "SELECT * FROM Discounts ORDER BY created DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<style>
    /* .breadcrumb {
        margin-top: 5px;
    } */
</style>

<main id="main" class="main">
    
    <div class="pagetitle">
        <h1>Mã khuyến mãi</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Mã khuyến mãi</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="discount-add.php"><i class="bi bi-plus-lg"></i></a>
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
                                    <th class="text-center align-middle">Mã khuyến mãi</th>
                                    <th class="text-center align-middle">Giảm giá</th>
                                    <th class="text-center align-middle">Ngày bắt đầu</th>
                                    <th class="text-center align-middle">Ngày kết thúc</th>
                                    <th class="text-center align-middle">Giới hạn sử dụng</th>
                                    <th class="text-center align-middle">Ngày tạo</th>
                                    <th class="text-center align-middle">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($discounts as $discount): ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($discount['ID']); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars(number_format($discount['discount'], 0)) . '%'; ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($discount['sDate']); ?></td>
                                        <td class="text-center align-middle"><?php echo isset($discount['eDate']); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($discount['limitUse']); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($discount['created']); ?></td>
                                        <td>
                                            <a class="btn btn-success" href="discount-edit.php?id=<?php echo $discount['ID']; ?>"><i class="bi bi-pen"></i></a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $discount['ID']; ?>"><i class="bi bi-trash"></i></button>
                                        </td>

                                        <div class="modal fade" id="deleteModal<?php echo $discount['ID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn xóa mã khuyến mãi này không?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <form action="discount-delete.php" method="post" class="d-inline">
                                                            <input type="hidden" name="id" value="<?php echo $discount['ID']; ?>">
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

<!-- <i class="bi bi-x"></i> -->