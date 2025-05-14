<?php
session_start();
include '../database/connect.php'; // Đảm bảo đường dẫn đúng

// Kiểm tra xem dữ liệu có được gửi đến không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ yêu cầu POST
    $productID = isset($_POST['productID']) ? $_POST['productID'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    $shape = isset($_POST['shape']) ? $_POST['shape'] : '';
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $text = isset($_POST['text']) ? $_POST['text'] : '';
    $colorText = isset($_POST['colorText']) ? $_POST['colorText'] : '';
    $image = isset($_POST['image']) ? $_POST['image'] : '';

    // Khởi tạo giỏ hàng nếu chưa tồn tại
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Lấy thông tin sản phẩm từ database
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE ID = :id");
    $stmt->execute(['id' => $productID]);
    $product = $stmt->fetch();

    // Kiểm tra sản phẩm tồn tại
    if ($product) {
        // Lấy categoryID của sản phẩm
        $categoryID = $product['categoryID'];

        // Kiểm tra sản phẩm có category thuộc 2
        if ($categoryID == 2) {
            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            if (isset($_SESSION['cart'][$productID])) {
                // Nếu có, kiểm tra các thuộc tính khác
                $existingItem = $_SESSION['cart'][$productID];
                if ($existingItem['shape'] === $shape && $existingItem['color'] === $color && 
                    $existingItem['text'] === $text && $existingItem['colorText'] === $colorText && 
                    $existingItem['image'] === $image) {
                    // Cộng thêm số lượng
                    $_SESSION['cart'][$productID]['quantity'] += $quantity;
                } else {
                   // Nếu khác, tạo một giỏ hàng mới
                    $_SESSION['cart'][$productID . '-' . uniqid()] = [
                        'id' => $productID,
                        'name' => $product['name'],
                        'price' => ($product['price'] - $product['price']*($product['promotionalprice']/100)),
                        'quantity' => $quantity,
                        'shape' => $shape,
                        'color' => $color,
                        'text' => $text,
                        'colorText' => $colorText,
                        'image' => $image
                    ];
                }
            } else {
                // Nếu chưa có, thêm mới sản phẩm với thông tin đầy đủ
                $_SESSION['cart'][$productID] = [
                    'id' => $productID,
                    'name' => $product['name'],
                    'price' => ($product['price'] - $product['price']*($product['promotionalprice']/100)),
                    'quantity' => $quantity,
                    'shape' => $shape,
                    'color' => $color,
                    'text' => $text,
                    'colorText' => $colorText,
                    'image' => $image
                ];
            }
            echo "Sản phẩm đã được thêm vào giỏ hàng!";
        } else {
            // Nếu sản phẩm không thuộc category 2
            if (isset($_SESSION['cart'][$productID])) {
                // Nếu đã tồn tại, cộng thêm số lượng
                $_SESSION['cart'][$productID]['quantity'] += $quantity;
            } else {
                // Nếu chưa có, thêm mới sản phẩm với thông tin đơn giản
                $_SESSION['cart'][$productID] = [
                    'id' => $productID,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image']
                ];
            }
            echo "Sản phẩm đã được thêm vào giỏ hàng!";
        }

        // Lưu vào bảng Cart (đối với người dùng đã đăng nhập)
        if (isset($_SESSION['userID'])) {
            $stmt = $pdo->prepare("INSERT INTO Cart (userID, productID, quantity) VALUES (:userID, :productID, :quantity)
                                   ON DUPLICATE KEY UPDATE quantity = quantity + :quantity");
            $stmt->execute(['userID' => $_SESSION['userID'], 'productID' => $productID, 'quantity' => $quantity]);
        }
    } else {
        echo 'Sản phẩm không tồn tại trong cơ sở dữ liệu.';
        exit();
    }
}
// Quay lại trang trước đó
$previous_page = $_SERVER['HTTP_REFERER'];
header("Location: $previous_page");
exit();
?>
