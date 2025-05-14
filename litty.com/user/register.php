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
        header('Location: login.php');
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

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng Ký Tài Khoản</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link rel="stylesheet" href="css/register.css">
</head>
<body>
  <section class="register">
    <div class="inner-container">
      <div class="logo">
        <a href="index.php">
          <img src="../img/logo.jpeg" alt="Logo">
        </a>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="header">
            <h5>Tạo Tài Khoản</h5>
            <p class="small">Nhập thông tin của bạn để tạo tài khoản</p>
          </div>
          <!-- Hiển thị thông báo -->
          <?php
            if (isset($_SESSION['success'])) {
              echo "<div class='alert alert-success' id='success-notification'>" . $_SESSION['success'] . "</div>";
              unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
              echo "<div class='alert alert-danger' id='error-notification'>" . $_SESSION['error'] . "</div>";
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
            }, 3000);
          </script>

          <form action="register.php" method="POST">
            <div class="row">
              <div class="form-group">
                <label for="fullname">Họ & Tên</label>
                <input type="text" id="fullname" name="fullname" required>
              </div>
              <div class="form-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="password" required>
              </div>
            </div>
            <div class="row">
              <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                  <span class="input-group-text">@</span>
                  <input type="email" id="email" name="email" required>
                </div>
              </div>
              <div class="form-group">
                <label for="phone">Số Điện Thoại</label>
                <div class="input-group">
                  <span class="input-group-text">+84</span>
                  <input type="tel" id="phone" name="phone" pattern="[+]{1}[0-9]{11,14}" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group">
                <label for="city">Tỉnh / Thành phố</label>
                <select type="select" id="city" name="city" required>
                  <option value="" disabled selected>Chọn Tỉnh / Thành</option>
                    <?php foreach ($locations as $city => $districts): ?>
                      <option value="<?php echo htmlspecialchars($city); ?>"><?php echo htmlspecialchars($city); ?></option>
                    <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="district">Quận / Huyện</label>
                <select type="select" id="district" name="district" required>
                  <option value="" disabled selected>Chọn Quận / Huyện</option>
                </select>
              </div>
              <div class="form-group">
                <label for="ward" disabled selected>Phường / Xã</label>
                  <select type="select" id="ward" name="ward" required>
                    <option value="">Chọn Phường / Xã</option>
                  </select>
              </div>
            </div>
            <div class="form-group">
                <label for="address">Địa Chỉ</label>
                <input type="text" id="address" name="address" required>
            </div>
            <button type="submit" class="btn">Đăng Ký</button>
            <p class="small">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script>
    var locations = <?php echo json_encode($locations); ?>;
  </script>
  <script src="js/location.js"></script>
</body>
</html>