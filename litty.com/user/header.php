<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Website Litty</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&family=Sedgwick+Ave&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    </head>
    <body>
        <header>
            <a href="index.php" class="logo"><img src="../img/logo.jpeg" alt=""></a>
            <i class='bx bx-menu' id="menu-icon"></i>
            <ul class="navbar">
                <li><a href="index.php">Trang Chủ</a></li>
                <li><a href="about.php">Giới thiệu</a></li>
                <li><a href="product.php">Sản Phẩm</a></li>
                <li><a href="design-product.php">Thiết Kế</a></li>
            </ul>

            <div class="header-icon">
                <i class='bx bx-search' id="search-icon"></i>
                <a href="cart.php"><i class='bx bxs-cart-alt'><span>
                    <?php 
                        $totalQuantity = 0; 
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $totalQuantity += $item['quantity'];
                            }
                        }
                        echo $totalQuantity;
                    ?>
                    </span></i>
                </a>
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="dropbtn"><i class='bx bx-chevron-down'></i></a>
                        <div class="dropdown-content">
                            <a href="profile.php"><i class='bx bxs-user' ></i>Trang cá nhân</a>
                            <a href="order.php"><i class='bx bxs-shopping-bag'></i>Đơn hàng</a>
                            <a href="logout.php"><i class='bx bxs-log-out'></i>Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php"><i class='bx bxs-log-in'></i></a>
                <?php endif; ?>
            </div>
            <div class="search-box">
                <form action="order-detail.php" method="GET">
                    <input type="search" name="order_code" placeholder="Nhập mã đơn hàng...">
                    <button type="submit" style="display: none;"></button>
                </form>
            </div>

            <!-- <div class="search-box">
                <input type="search" name="" id="" placeholder="Nhập mã đơn hàng...">
            </div> -->
        </header>