<?php
session_start();
include '../database/connect.php';

// Kiểm tra đăng nhập
$userID = $_SESSION['user'] ?? null;
if (!$userID) {
    header("Location: login.php");
    exit();
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Lấy thông tin user
$stmt = $pdo->prepare("SELECT * FROM Users WHERE ID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

$fullname = htmlspecialchars($user['fullname']);
$email = htmlspecialchars($user['email']);
$phone = htmlspecialchars($user['phone']);
$city = htmlspecialchars($user['city']);
$district = htmlspecialchars($user['district']);
$ward = htmlspecialchars($user['ward']);
$address = htmlspecialchars($user['address']);
$image = htmlspecialchars($user['image']);

// Đọc dữ liệu địa phương từ CSV
function readLocationsFromFile($filePath) {
    $locations = [];

    if (($handle = fopen($filePath, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($data) >= 3) {
                $city = trim($data[1]);
                $district = trim($data[2]);
                $ward = $data[3] ?? '';

                if (!isset($locations[$city])) {
                    $locations[$city] = [];
                }

                if (!isset($locations[$city][$district])) {
                    $locations[$city][$district] = [];
                }

                if ($ward && !in_array($ward, $locations[$city][$district])) {
                    $locations[$city][$district][] = $ward;
                }
            }
        }
        fclose($handle);
    }

    return $locations;
}

$filePath = '../database/tinh-thanh-vietnam.csv';
$locations = readLocationsFromFile($filePath);
?>

<?php include 'header.php';?>
<link rel="stylesheet" href="css/profile.css">

<section class="section profile" id="profile">
    <div class="heading">
        <h2>Trang cá nhân</h2>
    </div>

    <div class="profile-container">
        <div class="row">
            <div class="column left">
                <h4><?php echo $fullname; ?></h4>
                <?php $profileImage = !empty($image) ? $image : '../img/admin.jpg'; ?>
                <img id="profileImage" src="<?php echo $profileImage; ?>" alt="<?php echo $fullname; ?>" style="width: 70%;">
                <div class="form-group">
                    <input type="file" id="image" name="image" accept="image/*" disabled>
                </div>
            </div>

            <div class="column right">
                <h4>Thông tin cá nhân</h4>

                <form id="profileForm" method="post" action="profile-save.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="row">
                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <input type="text" id="name" name="name" value="<?php echo $fullname; ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="city">Tỉnh / Thành phố</label>
                            <select id="city" name="city" disabled>
                                <option value="" disabled selected>Chọn Tỉnh / Thành</option>
                                <?php foreach ($locations as $cityName => $districts): ?>
                                    <option value="<?php echo $cityName; ?>" <?php echo ($cityName == $city) ? 'selected' : ''; ?>>
                                        <?php echo $cityName; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="district">Quận / Huyện</label>
                            <select id="district" name="district" disabled>
                                <option value="" disabled>Chọn Quận / Huyện</option>
                                <?php if (!empty($city) && isset($locations[$city])): ?>
                                    <?php foreach ($locations[$city] as $districtName => $wards): ?>
                                        <option value="<?php echo $districtName; ?>" <?php echo ($districtName == $district) ? 'selected' : ''; ?>>
                                            <?php echo $districtName; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ward">Phường / Xã</label>
                            <select id="ward" name="ward" disabled>
                                <option value="" disabled>Chọn Phường / Xã</option>
                                <?php if (!empty($district) && isset($locations[$city][$district])): ?>
                                    <?php foreach ($locations[$city][$district] as $wardName): ?>
                                        <option value="<?php echo $wardName; ?>" <?php echo ($wardName == $ward) ? 'selected' : ''; ?>>
                                            <?php echo $wardName; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" id="address" name="address" value="<?php echo $address; ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <button type="button" id="editBtn">Cập nhật</button>
                            <button type="submit" id="saveBtn" style="display:none;">Lưu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>   
</section>

<script>
    var locations = <?php echo json_encode($locations); ?>;

    document.getElementById('editBtn').addEventListener('click', function () {
        document.querySelectorAll('#profileForm input, #profileForm select').forEach(function (input) {
            input.disabled = false;
        });

        document.getElementById('image').disabled = false;
        document.getElementById('editBtn').style.display = 'none';
        document.getElementById('saveBtn').style.display = 'inline-block';
    });

    document.getElementById('image').addEventListener('change', function (event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('profileImage').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>

<?php include 'footer.php';?>