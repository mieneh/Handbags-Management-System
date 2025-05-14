<?php
    session_start();
    include '../database/connect.php';
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE categoryID = :categoryID");
    $stmt->execute(['categoryID' => 1]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'header.php';?>

<link rel="stylesheet" href="css/product.css">

<section class="section products" id="products">
    <div class="heading">
        <h2>Sản Phẩm</h2>
    </div>
    <div class="products-container">
        <?php foreach ($products as $product): ?>
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

<?php include 'footer.php';?>