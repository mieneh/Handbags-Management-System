<?php
session_start();
include '../database/connect.php';

$currentCategoryId = "";
// Lấy thông tin sản phẩm từ cơ sở dữ liệu dựa trên ID sản phẩm
if (isset($_GET['id'])) {
    $productID = $_GET['id'];
    $productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    // Truy vấn lấy thông tin sản phẩm
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE ID = :id");
    $stmt->execute(['id' => $productID]);
    $product = $stmt->fetch();
    $currentCategoryId = $product['categoryID'];
    // Nếu sản phẩm không tồn tại, chuyển hướng về trang chủ
    if (!$product) {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}

?>
<?php include 'header.php';?>

<link rel="stylesheet" href="css/product.css">

<section class="section">
    <div class="product-container">
        <div class="product-images">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        </div>

        <div class="product-details">
            <h2><?php echo $product['name']; ?></h2>
            <p class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
            <p class="status" style="color: <?php echo ($product['stock'] > 0) ? 'green' : 'red'; ?>;">
                Tình trạng: <?php echo ($product['stock'] > 0) ? 'Còn hàng' : 'Hết hàng'; ?>
            </p>

            <div class="quantity">
                <form action="cart-add.php" method="POST">
                    <input type="hidden" name="productID" value="<?php echo $productID; ?>">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="submit" class="btn add-to-cart">Thêm vào giỏ hàng</button>
                </form>
            </div>

            <div class="description">
                <h3>Mô tả</h3>
                <p><?php echo $product['description']; ?></p>
            </div>
        </div>
    </div>

    <!-- Mua kèm khuyến mãi -->
    <?php if ($currentCategoryId != 3): // Kiểm tra categoryID ?>
        <section class="promo-section">
            <h3 style="margin-bottom: 10px;">Mua kèm khuyến mãi</h3>
            <div class="promo-items">
                <?php 
                    // Truy vấn lấy 3 sản phẩm ngẫu nhiên có categoryID = 3
                    $stmt = $pdo->prepare("SELECT * FROM Products WHERE categoryID = 3 ORDER BY RAND() LIMIT 3");
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($products as $product): 
                ?>
                    <div class="box">
                        <a href="product-detail.php?id=<?php echo $product['ID']; ?>">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </a>
                        <h2><?php echo $product['name']; ?></h2>
                        <span>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                        <form action="cart-add.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="productID" value="<?php echo $product['ID']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn" style="background: none; border: none; cursor: pointer;">
                                <i class='bx bxs-cart-add'></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; // Kết thúc kiểm tra categoryID ?>
    <!-- Sản phẩm tương tự -->
    <section class="promo-section">
        <h3 style="margin-bottom: 10px;">Sản phẩm tương tự</h3>
        <div class="promo-items">
            <?php 
                // Truy vấn lấy 3 sản phẩm ngẫu nhiên cùng categoryID, loại trừ sản phẩm hiện tại
                $stmt = $pdo->prepare("SELECT * FROM Products WHERE categoryID = :categoryID AND ID != :currentID ORDER BY RAND() LIMIT 4");
                $stmt->execute(['categoryID' => $currentCategoryId, 'currentID' => $productID]);
                $similarProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($similarProducts as $similarProduct): 
            ?>
                <div class="box">
                    <a href="product-detail.php?id=<?php echo $similarProduct['ID']; ?>">
                        <img src="<?php echo $similarProduct['image']; ?>" alt="<?php echo htmlspecialchars($similarProduct['name']); ?>">
                    </a>
                    <h2><?php echo htmlspecialchars($similarProduct['name']); ?></h2>
                    <span>Giá: <?php echo number_format($similarProduct['price'], 0, ',', '.'); ?> VNĐ</span>
                    <form action="cart-add.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="productID" value="<?php echo $similarProduct['ID']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn" style="background: none; border: none; cursor: pointer;">
                            <i class='bx bxs-cart-add'></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</section>
<?php include 'footer.php';?>