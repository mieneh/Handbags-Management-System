<?php
    session_start();
    include '../database/connect.php';

    $categoryQuery = "SELECT * FROM Categories ORDER BY ID ASC";
    $categoryStmt = $pdo->prepare($categoryQuery);
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    $selectedCategoryId = isset($_GET['category']) ? $_GET['category'] : $categories[0]['ID'];
    $productQuery = "SELECT * FROM Products WHERE categoryID = :categoryID";
    $productStmt = $pdo->prepare($productQuery);
    $productStmt->execute([':categoryID' => $selectedCategoryId]);
    $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php';?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Sản phẩm</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Sản phẩm</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="product-add.php"><i class="bi bi-plus-lg"></i></a>
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

    <ul style="margin-top: -1px;" class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
        <?php foreach ($categories as $index => $category): ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?php echo ($category['ID'] == $selectedCategoryId) ? 'active text-light bg-primary' : ''; ?>" 
                    href="?category=<?php echo $category['ID']; ?>" 
                    type="button" 
                    role="tab" 
                    aria-controls="category<?php echo $index; ?>" 
                    aria-selected="<?php echo ($category['ID'] == $selectedCategoryId) ? 'true' : 'false'; ?>">
                    <?php echo $category['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

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
                                    <th class="text-center align-middle">Giá</th>
                                    <th class="text-center align-middle">Số lượng</th>
                                    <th class="text-center align-middle" style="width: 250px;">Hình ảnh</th>
                                    <th class="text-center align-middle" style="width: 180px">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo $product['ID']; ?></td>
                                        <td class="align-middle"><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td class="text-center align-middle"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</td>
                                        <td class="text-center align-middle"><?php echo htmlspecialchars($product['stock']); ?></td>
                                        <td class="text-center align-middle"><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid" style="max-width: 100px;"></td>
                                        <td class="text-center align-middle">
                                            <a class="btn btn-info" href="product-detail.php?id=<?php echo $product['ID']; ?>"><i class="bi bi-eye"></i></a>
                                            <a class="btn btn-success" href="product-edit.php?id=<?php echo $product['ID']; ?>"><i class="bi bi-pen"></i></a>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $product['ID']; ?>"><i class="bi bi-trash"></i></button>
                                        </td>

                                        <!-- Modal Xác Nhận Xóa -->
                                        <div class="modal fade" id="deleteModal<?php echo $product['ID']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Bạn có chắc chắn muốn xóa sản phẩm này không?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                        <form action="product-delete.php" method="post" class="d-inline">
                                                            <input type="hidden" name="id" value="<?php echo $product['ID']; ?>">
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