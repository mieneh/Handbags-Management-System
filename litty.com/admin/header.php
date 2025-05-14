<?php
// Check if the user has logged out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../user/index.php");
    exit();
}

// Check access permissions
if (!isset($_SESSION['user'])) {
    header("Location: ../user/login.php");
    exit();
}

// Get user information from session
$userID = $_SESSION['user'];
$query = "SELECT role, email FROM Users WHERE ID = :userID";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
if ($user['role'] !== 'admin') {
    header("Location: ../user/index.php"); // Redirect to main page
    exit();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Litty Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../vendor/simple-datatables/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Sedgwick+Ave&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
</head>
<body>    
        <header id="header" class="header fixed-top d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center"><img src="../img/logo.jpeg" alt=""></a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
            </div><!-- End Logo -->

            <div class="search-bar">
                <form class="search-form d-flex align-items-center" method="POST" action="#">
                    <input type="text" name="query" placeholder="Search" title="Enter search keyword">
                    <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                </form>
            </div><!-- End Search Bar -->

            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">
                    <li class="nav-item dropdown pe-3">

                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#">
                            <img src="../img/admin.jpg" alt="Profile" class="rounded-circle">
                            <span class="ps-2">Admin</span>
                        </a><!-- End Profile Iamge Icon -->
                    </li><!-- End Profile Nav -->
                </ul>
            </nav>
        </header>
        
        <aside id="sidebar" class="sidebar">
            <ul class="sidebar-nav" id="sidebar-nav">
                <li class="nav-item collapsed">
                    <a class="nav-link " href="index.php">
                        <i class="bi bi-grid"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="product.php">
                        <i class="bi bi-menu-button-wide"></i>
                        <span>Sản phẩm</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="order.php">
                        <i class="bi bi-cart"></i>
                        <span>Đơn hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="customer.php">
                        <i class="bi bi-person"></i>
                        <span>Khách hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="inventory.php">
                        <i class="bi bi-shop"></i>
                        <span>Kho hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="payment.php">
                        <i class="bi bi-credit-card"></i>
                        <span>Thanh toán</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="discount.php">
                        <i class="bi bi-gift"></i>
                        <span>Mã khuyến mãi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="logout.php">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Đăng xuất</span>
                    </a>
            </li>
        </ul>
    </aside>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const currentPath = window.location.pathname.split("/").pop();
            const menuLinks = document.querySelectorAll(".sidebar-nav .nav-link");

            menuLinks.forEach(link => {
                if (link.getAttribute("href") === currentPath) {
                    link.classList.add("active");
                } else {
                    link.classList.remove("active");
                }
            });
        });
    </script>

    <style>
        
    .sidebar-nav {
    padding: 0;
    margin: 0;
    list-style: none;
    }

    .sidebar-nav li {
    padding: 0;
    margin: 0;
    list-style: none;
    }

    .sidebar-nav .nav-item {
    margin-bottom: 7px;
    }

    .sidebar-nav .nav-heading {
        font-size: 11px;
        text-transform: uppercase;
        color: #899bbd;
        font-weight: 600;
        margin: 10px 0 5px 15px;
    }

    .sidebar-nav .nav-link {
        background-color: white;
        display: flex;
        align-items: center;
        font-size: 17px;
        font-weight: 600;
        color: #012970;
        transition: 0.3;
        padding: 10px 15px;
        border-radius: 4px;
    }

    .sidebar-nav .nav-content {
    padding: 5px 0 0 0;
    margin: 0;
    list-style: none;
    }

    .sidebar-nav .nav-content a {
    display: flex;
    align-items: center;
    font-size: 14px;
    font-weight: 600;
    color: #012970;
    transition: 0.3;
    padding: 10px 0 10px 40px;
    transition: 0.3s;
    }

    .sidebar-nav .nav-content a i {
    font-size: 6px;
    margin-right: 8px;
    line-height: 0;
    border-radius: 50%;
    }

    </style>