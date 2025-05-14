<?php
session_start();
include '../database/connect.php'; 

// Kiểm tra xem đã có userID trong URL hay chưa
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Lấy thông tin khách hàng từ cơ sở dữ liệu
    $sql = "SELECT * FROM Users WHERE ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $userID);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nếu không tìm thấy khách hàng, chuyển hướng về trang danh sách
    if (!$customer) {
        header('Location: customer.php');
        exit();
    }
}

// Xử lý form gửi lên
// Xử lý form gửi lên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $address = $_POST['address'];

    // Kiểm tra nếu email mới khác email cũ
    $currentEmail = $customer['email'];
    if ($email !== $currentEmail) {
        // Kiểm tra xem email mới đã tồn tại chưa
        $sqlCheckEmail = "SELECT COUNT(*) FROM Users WHERE email = :email";
        $stmtCheckEmail = $pdo->prepare($sqlCheckEmail);
        $stmtCheckEmail->bindParam(':email', $email);
        $stmtCheckEmail->execute();
        $emailExists = $stmtCheckEmail->fetchColumn();

        if ($emailExists) {
            $_SESSION['error'] = "Email đã tồn tại. Vui lòng chọn email khác.";
        } else {
            // Cập nhật thông tin khách hàng
            $sql = "UPDATE Users SET fullname = :fullname, email = :email, phone = :phone, city = :city, district = :district, ward = :ward, address = :address WHERE ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':ward', $ward);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':id', $userID);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Cập nhật thông tin thành công!";
                header('Location: customer.php');
                exit();
            } else {
                $_SESSION['error'] = "Lỗi khi cập nhật thông tin!";
            }
        }
    } else {
        // Nếu email không thay đổi, thực hiện cập nhật
        $sql = "UPDATE Users SET fullname = :fullname, phone = :phone, city = :city, district = :district, ward = :ward, address = :address WHERE ID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':ward', $ward);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $userID);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
            header('Location: customer.php');
            exit();
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật thông tin!";
        }
    }
}

// Đọc dữ liệu tỉnh/thành phố, quận/huyện và phường/xã từ file CSV
function readLocationsFromFile($filePath) {
    $locations = [];
    
    if (($handle = fopen($filePath, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($data) >= 3) {
                $city = trim($data[1]);
                $district = trim($data[2]);
                $ward = isset($data[3]) ? trim($data[3]) : '';

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
        <h1>Chỉnh sửa khách hàng</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item"><a href="customer.php">Khách hàng</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa khách hàng</li>
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
                <form class="row g-3" action="customer-edit.php?id=<?php echo $userID; ?>" method="POST">
                    <div class="col-md-12">
                        <label for="fullname" class="form-label">Họ & Tên</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($customer['fullname']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Số Điện Thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" pattern="[+]{1}[0-9]{11,14}" required>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city" class="form-label">Tỉnh / Thành phố</label>
                            <select id="city" name="city" class="form-select" required>
                                <option value="" disabled selected>Chọn Tỉnh / Thành</option>
                                <?php foreach ($locations as $city => $districts): ?>
                                    <option value="<?php echo htmlspecialchars($city); ?>" <?php echo $customer['city'] === $city ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($city); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="district" class="form-label">Quận / Huyện</label>
                            <select id="district" name="district" class="form-select" required>
                                <option value="" disabled selected>Chọn Quận / Huyện</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ward" class="form-label">Phường / Xã</label>
                            <select id="ward" name="ward" class="form-select" required>
                                <option value="" disabled selected>Chọn Phường / Xã</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="address" class="form-label">Địa Chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
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

    // Tự động điền quận/huyện và phường/xã nếu có dữ liệu
    window.onload = function() {
        var selectedCity = "<?php echo htmlspecialchars($customer['city']); ?>";
        var selectedDistrict = "<?php echo htmlspecialchars($customer['district']); ?>";
        var selectedWard = "<?php echo htmlspecialchars($customer['ward']); ?>";

        document.getElementById('city').value = selectedCity;
        document.getElementById('city').dispatchEvent(new Event('change'));

        if (selectedDistrict) {
            document.getElementById('district').value = selectedDistrict;
            document.getElementById('district').dispatchEvent(new Event('change'));
        }

        if (selectedWard) {
            document.getElementById('ward').value = selectedWard;
        }
    };
</script>

<?php include 'footer.php';?>