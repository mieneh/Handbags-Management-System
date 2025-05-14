<?php
session_start();
include '../database/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ form
    $id = $_POST['id'];
    $discount = $_POST['discount'];
    $sDate = $_POST['sDate'];
    $eDate = $_POST['eDate'];
    $limitUse = $_POST['limitUse'];

    try {
        // Cập nhật thông tin mã khuyến mãi
        $query = "UPDATE Discounts SET discount = ?, sDate = ?, eDate = ?, limitUse = ? WHERE ID = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$discount, $sDate, $eDate, $limitUse, $id]);

        $_SESSION['success'] = 'Cập nhật mã khuyến mãi thành công.';
        header('Location: discount.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        header('Location: discount.php');
        exit();
    }
}

// Lấy thông tin mã khuyến mãi
$id = $_GET['id'];
$query = "SELECT * FROM Discounts WHERE ID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$discount = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$discount) {
    $_SESSION['error'] = 'Mã khuyến mãi không tồn tại.';
    header('Location: discount.php');
    exit();
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
        <h1>Chỉnh sửa khuyến mãi</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="discount.php">Mã khuyến mãi</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa khuyến mãi</li>
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
                <form class="row g-3" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($discount['ID']); ?>">
                    <div class="col-md-6">
                        <label for="id" class="form-label">Mã khuyến mãi</label>
                        <input type="text" class="form-control" name="id" value="<?php echo htmlspecialchars($discount['ID']); ?>"disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="discount" class="form-label">Giá trị giảm (%)</label>
                        <input type="number" class="form-control" name="discount" value="<?php echo htmlspecialchars($discount['discount']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="sDate" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control" name="sDate" value="<?php echo htmlspecialchars($discount['sDate']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="eDate" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control" name="eDate" value="<?php echo htmlspecialchars($discount['eDate']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="limitUse" class="form-label">Giới hạn sử dụng</label>
                        <input type="number" class="form-control" name="limitUse" value="<?php echo htmlspecialchars($discount['limitUse']); ?>" required>
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