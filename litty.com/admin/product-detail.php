<?php
session_start();
include '../database/connect.php';

$productId = $_GET['id'];

// Truy vấn thông tin sản phẩm
$query = "
    SELECT p.*, c.name AS categoryName
    FROM Products p
    JOIN Categories c ON p.categoryID = c.ID
    WHERE p.ID = :id
";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error'] = "Sản phẩm không tồn tại.";
    header('Location: product.php');
    exit;
}

// Truy vấn màu sắc nếu sản phẩm thuộc categoryID = 2
$productColors = [];
$productShapes = [];
if ($product['categoryID'] == 2) {
    // Lấy các màu sắc
    $colorQuery = "
        SELECT Colors.name AS colorName
        FROM ProductColors
        JOIN Colors ON ProductColors.colorID = Colors.ID
        WHERE ProductColors.productID = :id
    ";
    $colorStmt = $pdo->prepare($colorQuery);
    $colorStmt->execute([':id' => $productId]);
    $productColors = $colorStmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy các kiểu dáng
    $shapeQuery = "
        SELECT Shapes.name AS shapeName
        FROM ProductShapes
        JOIN Shapes ON ProductShapes.shapeID = Shapes.ID
        WHERE ProductShapes.productID = :id
    ";
    $shapeStmt = $pdo->prepare($shapeQuery);
    $shapeStmt->execute([':id' => $productId]);
    $productShapes = $shapeStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'header.php'; ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Chi tiết sản phẩm</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="product.php">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Chi tiết sản phẩm</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="product.php"><i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid mb-2" style="max-width: 60%; height: auto;">
                        <p class="card-title fs-3 text-danger"><?php echo htmlspecialchars($product['name']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin chi tiết:</h5>
                        <div class="card-text">
                            <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($product['categoryName']); ?></p>
                            <p><strong>Giá:</strong> <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
                            <p><strong>Số lượng:</strong> <?php echo $product['stock']; ?></p>
                            <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                            <?php if ($product['categoryID'] == 2): ?>
                                <p><strong>Màu sắc có sẵn:</strong>
                                <?php foreach ($productColors as $color): ?>
                                    <?php echo htmlspecialchars($color['colorName']); ?>
                                <?php endforeach; ?></p>
                                <p><strong>Kiểu dáng có sẵn:</strong>
                                <?php foreach ($productShapes as $shape): ?>
                                    <?php echo htmlspecialchars($shape['shapeName']); ?>
                                <?php endforeach; ?></p>
                            <?php endif; ?>
                            <p><strong>Khuyến mãi:</strong> <?php echo $product['promotional'] === 'yes' ? 'Có' : 'Không'; ?></p>
                            <?php if ($product['promotional'] === 'yes'): ?>
                                <p><strong>Khuyến mãi:</strong> <?php echo number_format($product['promotionalprice'], 0, ',', '.'); ?>%</p>
                                <p><strong>Giá khuyến mãi:</strong> <?php echo number_format(($product['price'] - ($product['price'] * $product['promotionalprice']/100)), 0, ',', '.'); ?> VNĐ</p></p>
                            <?php endif; ?>
                            <p><strong>Ngày tạo:</strong> <?php echo htmlspecialchars($product['created']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>