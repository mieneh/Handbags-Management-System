<?php
session_start();
include '../database/connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $city = $_POST['city'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $address = $_POST['address'];

    // Kiểm tra xem email đã tồn tại chưa
    $check_email = "SELECT * FROM Users WHERE email = :email";
    $stmt = $pdo->prepare($check_email);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email đã tồn tại!";
    } else {
        // Chèn dữ liệu vào cơ sở dữ liệu
        $sql = "INSERT INTO Users (fullname, email, phone, password, city, district, ward, address) VALUES (:fullname, :email, :phone, :password, :city, :district, :ward, :address)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':ward', $ward);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Đăng ký thành công!";
            header('Location: customer.php');
            exit();
        } else {
            $_SESSION['error'] = "Lỗi khi chèn dữ liệu!";
        }
    }
}

function readLocationsFromFile($filePath) {
    $locations = [];
    
    if (($handle = fopen($filePath, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($data) >= 3) {
                $city = trim($data[1]);
                $district = trim($data[2]);
                $ward = isset($data[3]) ? trim($data[3]) : '';

                // Tạo mảng các quận/huyện và phường/xã cho từng tỉnh/thành phố
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

$filePath = '../database/tinh-thanh-vietnam.csv'; // Đường dẫn tới file CSV của bạn
$locations = readLocationsFromFile($filePath);
?>

<?php include 'header.php';?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Thêm khách hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="customer.php">Khách hàng</a></li>
                    <li class="breadcrumb-item active">Thêm khách hàng</li>
                </ol>
            </nav>
            <div>
                <a class="btn btn-primary" style="width: 80px" href="customer.php"><i class="bi bi-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <div>
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert' id='success-notification'>
                    <i class='bi bi-check-circle me-1'></i>" . $_SESSION['success'] . 
                    "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert' id='error-notification'>
                    <i class='bi bi-exclamation-octagon me-1'></i>" . $_SESSION['error'] . 
                    "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            unset($_SESSION['error']);
        }
        ?>

        <script>
            // Tự động ẩn thông báo sau 3 giây
            setTimeout(function() {
                var successNotification = document.getElementById('success-notification');
                if (successNotification) {
                    successNotification.style.display = 'none'; // Ẩn thông báo thành công
                }

                var errorNotification = document.getElementById('error-notification');
                if (errorNotification) {
                    errorNotification.style.display = 'none'; // Ẩn thông báo lỗi
                }
            }, 1500);
        </script>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form class="row g-3" action="customer-add.php" method="POST">
                    <div class="col-md-12">
                        <label for="fullname" class="form-label">Họ & Tên</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="form-label">Mật Khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Số Điện Thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone" pattern="[+]{1}[0-9]{11,14}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city" class="form-label">Tỉnh / Thành phố</label>
                            <select type="select" id="city" name="city" class="form-select" required>
                                <option value="" disabled selected>Chọn Tỉnh / Thành</option>
                                <?php foreach ($locations as $city => $districts): ?>
                                    <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                                
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="district" class="form-label">Quận / Huyện</label>
                            <select type="select" id="district" name="district" class="form-select" required>
                                <option value="" disabled selected>Chọn Quận / Huyện</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ward" class="form-label">Phường / Xã</label>
                            <select type="select" id="ward" name="ward" class="form-select" required>
                                <option value="" disabled selected>Chọn Phường / Xã</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label">Địa Chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" required>
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

<script>
    // JavaScript để thay đổi các lựa chọn quận/huyện và phường/xã dựa trên tỉnh/thành phố được chọn
    document.getElementById('city').addEventListener('change', function() {
        var districtSelect = document.getElementById('district');
        var wardSelect = document.getElementById('ward');

        // Reset các lựa chọn
        districtSelect.innerHTML = '<option value="" disabled selected>Chọn Quận / Huyện</option>';
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';

        // Lấy quận/huyện từ mảng locations
        var districts = <?php echo json_encode($locations); ?>[this.value];
        if (districts) {
            for (var district in districts) {
                var option = document.createElement('option');
                option.value = district;
                option.text = district;
                districtSelect.add(option);
            }
        }
    });

    document.getElementById('district').addEventListener('change', function() {
        var wardSelect = document.getElementById('ward');
        // Reset phường/xã
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';

        // Lấy phường/xã từ mảng locations
        var wards = <?php echo json_encode($locations); ?>[document.getElementById('city').value][this.value];
        if (wards) {
            wards.forEach(function(ward) {
                var option = document.createElement('option');
                option.value = ward;
                option.text = ward;
                wardSelect.add(option);
            });
        }
    });
</script>

<?php include 'footer.php';?>
