<?php
session_start();
include '../database/connect.php';

// Lấy thông tin sản phẩm theo ID
if (isset($_GET['id'])) {
    $productID = $_GET['id'];
    $productQuery = "SELECT * FROM Products WHERE ID = ?";
    $productStmt = $pdo->prepare($productQuery);
    $productStmt->execute([$productID]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error'] = "Sản phẩm không tồn tại.";
        header('Location: product.php');
        exit();
    }
} else {
    $_SESSION['error'] = "ID sản phẩm không hợp lệ.";
    header('Location: product.php');
    exit();
}

// Lấy danh sách danh mục từ bảng Categories
$categoryQuery = "SELECT * FROM Categories";
$categoryStmt = $pdo->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm để chuyển đổi tên sản phẩm thành định dạng file
function formatFileName($name) {
    $transliterationTable = [
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'ä' => 'a', 'ǟ' => 'a', 'å' => 'a', 'ā' => 'a', 'ą' => 'a',
        'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ướ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        'đ' => 'd',
    ];

    // Chuyển tên sản phẩm sang không dấu
    $name = strtr($name, $transliterationTable);
    $name = mb_strtolower($name);
    $name = preg_replace('/[^a-z0-9\s]/', '', $name);
    $name = preg_replace('/\s+/', '-', $name);
    return $name;
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $categoryID = $_POST['categoryID'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $promotional = $_POST['promotional'];

    // Kiểm tra trùng tên sản phẩm
    $checkQuery = "SELECT COUNT(*) FROM Products WHERE name = ? AND ID != ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$name, $productID]);
    $productExists = $checkStmt->fetchColumn();

    if ($productExists > 0) {
        $_SESSION['error'] = "Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.";
        header("Location: product-edit.php?id=$productID");
        exit();
    }

    // Kiểm tra nếu có ảnh được upload
    $imagePath = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $imagePath = '../img/' . formatFileName($name) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    }

    // Cập nhật dữ liệu sản phẩm vào CSDL
    $query = "UPDATE Products SET name = ?, categoryID = ?, description = ?, price = ?, stock = ?, promotional = ?, promotionalprice = ?, image = ? WHERE ID = ?";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$name, $categoryID, $description, $price, $stock, $promotional == '0' ? 'no' : 'yes', $promotional == '0' ? null : $promotional, $imagePath, $productID])) {
        // Nếu có ảnh mới, di chuyển file ảnh vào thư mục
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }
        $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
        header('Location: product.php');
        exit();
    } else {
        $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật sản phẩm.";
        header('Location: product-edit.php?id=$productID');
        exit();
    }
}
?>

<?php include 'header.php';?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Chỉnh sửa sản phẩm</h1>
            <div class="d-flex justify-content-between align-items-center">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                        <li class="breadcrumb-item"><a href="product.php">Sản phẩm</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa sản phẩm</li>
                    </ol>
                </nav>
                <div>
                    <a class="btn btn-primary" style="width: 80px" href="product.php"><i class="bi bi-arrow-left"></i></a>
                </div>
            </div>
        </div>

        <div>
            <?php
            if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert' id='success-notification'>
                        <i class='bi bi-check-circle me-1' style='text-algin:center'></i>" 
                        . $_SESSION['success'] . 
                        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                unset($_SESSION['success']);
            }

            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' id='error-notification'>
                        <i class='bi bi-exclamation-octagon me-1' style='text-algin:center'></i>" 
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

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" enctype="multipart/form-data">
                        <div class="col-md-10">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label for="id" class="form-label">ID</label>
                            <input type="text" id="id" class="form-control" value="<?php echo $product['ID']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="categoryID" class="form-label">Danh mục</label>
                            <select id="categoryID" name="categoryID" class="form-select" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['ID']; ?>" <?php echo ($product['categoryID'] == $category['ID']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="stock" class="form-label">Số lượng</label>
                            <input type="number" id="stock" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="price" class="form-label">Giá</label>
                            <input type="number" id="price" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="3" required><?php echo $product['description']; ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="promotional" class="form-label">Giảm giá</label>
                            <select id="promotional" class="form-select" name="promotional" required>
                                <option value="0" <?php echo ($product['promotional'] == 'no') ? 'selected' : ''; ?>>Không</option>
                                <?php for ($i = 5; $i <= 100; $i += 5): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($product['promotionalprice'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>%
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" id="image" name="image" class="form-control" onchange="previewImage()">
                            <img id="preview" src="<?php echo $product['image']; ?>" alt="Ảnh xem trước" style="margin-top: 10px; max-width: 200px;">
                        </div>
                        <div class="col-12">
                            <label for="created" class="form-label">Ngày tạo</label>
                            <input type="text" id="created" class="form-control" value="<?php echo $product['created']; ?>" disabled>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" style="width: 60px"><i class="bi bi-save"></i></button>
                            <button type="reset" class="btn btn-secondary" style="width: 60px"><i class="bi bi-arrow-counterclockwise"></i></button>
                        </div>
                    </form>  
                </div>
            </div>
        </section>

        <script>
        function previewImage() {
            const file = document.getElementById('image').files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
        </script>

    </main>

<?php include 'footer.php'; ?>