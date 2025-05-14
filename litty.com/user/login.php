<?php
    session_start();
    include '../database/connect.php'; 
    
    // Kiểm tra nếu người dùng đã đăng xuất
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Truy vấn lấy thông tin người dùng
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['ID'];
            // Kiểm tra nếu email là admin@gmail.com
            if ($email == 'admin@gmail.com') {
                $_SESSION['success'] = "Welcome, Admin!";
                header("Location: ../admin/index.php");
            } else {
                $_SESSION['success'] = "You have logged in successfully!";
                header("Location: index.php");
            }
            // Xử lý "Ghi nhớ tài khoản"
            if (isset($_POST['remember'])) {
                setcookie('email', $email, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
            } else {
                if (isset($_COOKIE['email'])) {
                    setcookie('email', '', time() - 3600, "/");
                }
                if (isset($_COOKIE['password'])) {
                    setcookie('password', '', time() - 3600, "/");
                }
            } exit();
        } else {
            $_SESSION['error'] = "Invalid email or password!";
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
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
                        <h5>Đăng Nhập</h5>
                        <p class="small">Nhập email và mật khẩu để đăng nhập</p>
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
                    </div>

                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Mật Khẩu</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="rememberMe" name="remember">
                            <label for="rememberMe">Ghi nhớ tài khoản</label>
                        </div>

                        <button type="submit" class="btn" name="login">Đăng Nhập</button>

                        <p class="small">Chưa có tài khoản? <a href="register.php">Tạo tài khoản</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>