<?php
session_start();
include '../database/connect.php';

$sql = "SELECT p.ID, p.name, p.stock AS initial_quantity, 
               COALESCE(SUM(oi.quantity), 0) AS ordered_quantity 
        FROM Products p 
        LEFT JOIN OrderItems oi ON p.ID = oi.productID 
        GROUP BY p.ID, p.name, p.stock";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý xuất file CSV khi người dùng nhấn nút
if (isset($_POST['export_inventory'])) {
    try {
        // Truy vấn lấy thông tin sản phẩm và số lượng đã đặt
        $sql = "SELECT p.ID, p.name, p.stock AS initial_quantity, 
               COALESCE(SUM(oi.quantity), 0) AS ordered_quantity 
        FROM Products p 
        LEFT JOIN OrderItems oi ON p.ID = oi.productID 
        GROUP BY p.ID, p.name, p.stock";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra nếu không có sản phẩm nào
        if (empty($products)) {
            $_SESSION['error'] = "Không có sản phẩm nào trong kho.";
            header('Location: inventory.php');
            exit();
        }

        // Tạo file CSV
        $csvFile = 'inventory.csv';
        $fp = fopen($csvFile, 'w');

        // Thêm tiêu đề cột
        fputcsv($fp, ['ID', 'Tên sản phẩm', 'Số lượng ban đầu', 'Số lượng đã đặt', 'Tồn kho']);

        // Thêm dữ liệu sản phẩm vào file
        foreach ($products as $product) {
            $initial_quantity = $product['initial_quantity'];
            $ordered_quantity = $product['ordered_quantity'];
            $stock_quantity = $initial_quantity - $ordered_quantity;

            // Ghi vào file CSV
            fputcsv($fp, [
                $product['ID'],
                $product['name'],
                $initial_quantity,
                $ordered_quantity,
                $stock_quantity
            ]);
        }

        fclose($fp);

        // Đặt tiêu đề để tải file CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFile . '"');
        readfile($csvFile);

        // Xóa file sau khi tải
        unlink($csvFile);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        header('Location: inventory.php');
        exit();
    }
}
?>

<?php include 'header.php';?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Kho hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Kho hàng</li>
                </ol>
            </nav>
        
            <div>
                <form action="inventory.php" method="POST">
                    <button type="submit" name="export_inventory" class="btn btn-primary" style="width: 80px"><i class="bi bi-download"></i></button>
                </form>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($products)): ?>
                        <div class="alert alert-warning" role="alert">
                            Không có sản phẩm nào trong kho.
                        </div>
                        <?php else: ?>
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">ID</th>
                                    <th class="text-center align-middle">Tên sản phẩm</th>
                                    <th class="text-center align-middle">Số lượng ban đầu</th>
                                    <th class="text-center align-middle">Số lượng đã đặt</th>
                                    <th class="text-center align-middle">Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): 
                                    $initial_quantity = $product['initial_quantity'];
                                    $ordered_quantity = $product['ordered_quantity'];
                                    $stock_quantity = $initial_quantity - $ordered_quantity; // Tính tồn kho
                                ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($product['ID']); ?></td>
                                        <td class="align-middle"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($initial_quantity); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($ordered_quantity); ?></td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($stock_quantity); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php';?>
