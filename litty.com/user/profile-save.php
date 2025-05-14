<?php
session_start();
include '../database/connect.php';

$userID = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Lấy thông tin người dùng hiện tại
$stmt = $pdo->prepare("SELECT * FROM Users WHERE ID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $address = $_POST['address'];

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

        $name = strtr($name, $transliterationTable);
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        $name = preg_replace('/\s+/', '-', $name);
        return $name;
    }

    // Kiểm tra nếu có ảnh được upload
    $imagePath = $user['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Xóa ảnh cũ nếu có
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
        // Tạo đường dẫn mới cho ảnh
        $imagePath = '../img/' . formatFileName($fullname) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        // Di chuyển file upload tới thư mục chỉ định
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // Cập nhật thông tin người dùng bao gồm ảnh
            $stmt = $pdo->prepare("UPDATE Users SET fullname = ?, email = ?, phone = ?, city = ?, district = ?, ward = ?, address = ?, image = ? WHERE ID = ?");
            $stmt->execute([$fullname, $email, $phone, $city, $district, $ward, $address, $imagePath, $userID]);
        } else {
            echo 'Lỗi upload ảnh.';
        }
    } else {
        // Cập nhật thông tin người dùng mà không thay đổi ảnh
        $stmt = $pdo->prepare("UPDATE Users SET fullname = ?, email = ?, phone = ?, city = ?, district = ?, ward = ?, address = ? WHERE ID = ?");
        $stmt->execute([$fullname, $email, $phone, $city, $district, $ward, $address, $userID]);
    }

    // Chuyển hướng sau khi lưu
    header('Location: profile.php');
    exit();
}
?>