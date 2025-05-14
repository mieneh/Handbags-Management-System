<?php
session_start();
include '../database/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $id = $_POST['id'];
    $discount = $_POST['discount'];
    $sDate = $_POST['sDate'];
    $eDate = $_POST['eDate'];
    $limitUse = $_POST['limitUse'];

    // Kiểm tra mã khuyến mãi đã tồn tại chưa
    $check_id = "SELECT * FROM Discounts WHERE ID = :id";
    $stmt = $pdo->prepare($check_id);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Mã khuyến mãi đã tồn tại!";
    } else {
        // Chèn dữ liệu vào cơ sở dữ liệu
        $sql = "INSERT INTO Discounts (ID, discount, sDate, eDate, limitUse) VALUES (:id, :discount, :sDate, :eDate, :limitUse)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':sDate', $sDate);
        $stmt->bindParam(':eDate', $eDate);
        $stmt->bindParam(':limitUse', $limitUse);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Thêm mã khuyến mãi thành công!";
            header('Location: discount.php');
            exit();
        } else {
            $_SESSION['error'] = "Lỗi khi thêm mã khuyến mãi!";
        }
    }
}
?>

<?php include 'header.php'; ?>

<style>
    .breadcrumb {
        margin-top: 7px;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Thêm khuyến mãi</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="discount.php">Mã khuyến mãi</a></li>
                    <li class="breadcrumb-item active">Thêm khuyến mãi</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="discount.php"><i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card" style="margin-top: -10px;">
            <div class="card-body">
                <form class="row g-3" action="discount-add.php" method="POST">
                    <div class="col-md-6">
                        <label for="id" class="form-label">Mã khuyến mãi</label>
                        <input type="text" class="form-control" id="id" name="id" required>
                    </div>
                    <div class="col-md-6">
                        <label for="discount" class="form-label">Giá trị giảm (%)</label>
                        <input type="number" step="0.01" class="form-control" id="discount" name="discount" required>
                    </div>
                    <div class="col-md-6">
                        <label for="sDate" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control" id="sDate" name="sDate" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="col-md-6">
                        <label for="eDate" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control" id="eDate" name="eDate" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="col-md-6">
                        <label for="limitUse" class="form-label">Số lương</label>
                        <input type="number" class="form-control" id="limitUse" name="limitUse" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" style="width: 60px"><i class="bi bi-save"></i></button>
                        <button type="reset" class="btn btn-secondary" style="width: 60px"><i class="bi bi-arrow-counterclockwise"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>