<?php
session_start();
include '../database/connect.php';

// Thống kê số lượng sản phẩm
$productCountQuery = "SELECT COUNT(*) AS total FROM Products";
$productCountStmt = $pdo->prepare($productCountQuery);
$productCountStmt->execute();
$productCount = $productCountStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Thống kê số lượng đơn hàng
$orderCountQuery = "SELECT COUNT(*) AS total FROM Orders";
$orderCountStmt = $pdo->prepare($orderCountQuery);
$orderCountStmt->execute();
$orderCount = $orderCountStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Thống kê số lượng người dùng
$userCountQuery = "SELECT COUNT(*) AS total FROM Users";
$userCountStmt = $pdo->prepare($userCountQuery);
$userCountStmt->execute();
$userCount = $userCountStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Thống kê tổng doanh thu
$revenueQuery = "SELECT SUM(total) AS totalRevenue FROM Orders WHERE orderstatus = 'completed'";
$revenueStmt = $pdo->prepare($revenueQuery);
$revenueStmt->execute();
$totalRevenue = $revenueStmt->fetch(PDO::FETCH_ASSOC)['totalRevenue'];

// Thống kê đơn hàng mới
$recentOrdersQuery = "SELECT * FROM Orders ORDER BY created DESC LIMIT 5";
$recentOrdersStmt = $pdo->prepare($recentOrdersQuery);
$recentOrdersStmt->execute();
$recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê sản phẩm bán chạy
$topProductsQuery = "SELECT Products.ID, Products.name, Products.stock AS quantity, SUM(OrderItems.quantity) AS totalSold 
                     FROM OrderItems 
                     JOIN Products ON OrderItems.productID = Products.ID 
                     GROUP BY OrderItems.productID 
                     ORDER BY totalSold DESC LIMIT 5";
$topProductsStmt = $pdo->prepare($topProductsQuery);
$topProductsStmt->execute();
$topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn doanh thu sản phẩm bán chạy nhất
$topSellingProductsQuery = "SELECT Products.name AS productName, SUM(OrderItems.quantity) AS totalSold, SUM(OrderItems.price * OrderItems.quantity) AS totalRevenue 
                            FROM OrderItems 
                            JOIN Orders ON OrderItems.orderID = Orders.ID 
                            JOIN Products ON OrderItems.productID = Products.ID 
                            WHERE Orders.orderstatus = 'completed' 
                            GROUP BY productName 
                            ORDER BY totalSold DESC 
                            LIMIT 10"; // Giới hạn số sản phẩm hiển thị (top 10)
$topSellingProductsStmt = $pdo->prepare($topSellingProductsQuery);
$topSellingProductsStmt->execute();
$topSellingProductsData = $topSellingProductsStmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê doanh thu theo danh mục
$categoryRevenueQuery = "SELECT Categories.name AS categoryName, SUM(Orders.total) AS totalRevenue 
                        FROM Orders 
                        JOIN OrderItems ON Orders.ID = OrderItems.orderID 
                        JOIN Products ON OrderItems.productID = Products.ID 
                        JOIN Categories ON Products.categoryID = Categories.ID 
                        WHERE Orders.orderstatus = 'completed' 
                        GROUP BY categoryName";
$categoryRevenueStmt = $pdo->prepare($categoryRevenueQuery);
$categoryRevenueStmt->execute();
$categoryRevenueData = $categoryRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

// Biểu đồ doanh thu theo ngày trong tháng hiện tại
$currentMonth = date('m');
$currentYear = date('Y');
$dailyRevenueQuery = "
    SELECT DAY(created) AS day, 
           SUM(total) AS totalRevenue 
    FROM Orders 
    WHERE orderstatus = 'completed' 
    AND MONTH(created) = :currentMonth 
    AND YEAR(created) = :currentYear 
    GROUP BY DAY(created) 
    ORDER BY DAY(created)";
$dailyRevenueStmt = $pdo->prepare($dailyRevenueQuery);
$dailyRevenueStmt->execute([':currentMonth' => $currentMonth, ':currentYear' => $currentYear]);
$dailyRevenue = $dailyRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

// Thống kê doanh thu theo tháng và danh mục
$monthlyCategoryRevenueQuery = "SELECT DATE_FORMAT(Orders.created, '%Y-%m') AS orderMonth, Categories.name AS categoryName, SUM(Orders.total) AS totalRevenue 
                                 FROM Orders 
                                 JOIN OrderItems ON Orders.ID = OrderItems.orderID 
                                 JOIN Products ON OrderItems.productID = Products.ID 
                                 JOIN Categories ON Products.categoryID = Categories.ID 
                                 WHERE Orders.orderstatus = 'completed' 
                                 GROUP BY orderMonth, categoryName 
                                 ORDER BY orderMonth DESC";
$monthlyCategoryRevenueStmt = $pdo->prepare($monthlyCategoryRevenueQuery);
$monthlyCategoryRevenueStmt->execute();
$monthlyCategoryRevenueData = $monthlyCategoryRevenueStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'header.php'; ?>

<style>
    .card-title {
        margin-top: -15px;
        margin-left: 5px;
        font-weight: bold;
        font-size: 20px;
        font-family: "Nunito", sans-serif;
    }
    .table {
        margin-top: -5px;
    }
    .breadcrumb {
        margin-top: 7px;
    }
</style>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Trang chủ</h1>
        <div class="d-flex justify-content-between align-items-center">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Litty</a></li>
                    <li class="breadcrumb-item active">Trang chủ</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section" style="margin-top: -10px;">
        <div class="row">

            <!-- Tổng sản phẩm -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm <span>| Tổng số</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="bi bi-bag text-primary" style="font-size: 2rem;"></div>
                            <div class="ps-3">
                                <h6><?php echo $productCount; ?></h6>
                                <span class="text-muted small">Sản phẩm</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng đơn hàng -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng <span>| Tổng số</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="bi bi-cart text-success" style="font-size: 2rem;"></div>
                            <div class="ps-3">
                                <h6><?php echo $orderCount; ?></h6>
                                <span class="text-muted small">Đơn hàng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng người dùng -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Người dùng <span>| Tổng số</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="bi bi-people text-warning" style="font-size: 2rem;"></div>
                            <div class="ps-3">
                                <h6><?php echo $userCount; ?></h6>
                                <span class="text-muted small">Người dùng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tổng doanh thu -->
            <div class="col-lg-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu <span>| Hoàn thành</span></h5>
                        <div class="d-flex align-items-center">
                            <div class="bi bi-currency-dollar text-danger" style="font-size: 2rem;"></div>
                            <div class="ps-3">
                                <h6><?php echo number_format($totalRevenue, 0, ',', '.'); ?> VNĐ</h6>
                                <span class="text-muted small">Tổng doanh thu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng mới nhất -->
            <div class="col-lg-6">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng mới</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Mã đơn</th>
                                    <th scope="col">Tên khách hàng</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['ID']; ?></td>
                                        <td><?php echo $order['guestname'] ?: 'User ' . $order['userID']; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($order['created'])); ?></td>
                                        <td><?php echo ucfirst($order['orderstatus']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm bán chạy nhất -->
            <div class="col-lg-6">
                <div class="card top-products overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm bán chạy</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Tên sản phẩm</th>
                                    <th scope="col" class="text-center">Đã bán</th>
                                    <th scope="col" class="text-center">Còn lại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProducts as $product): ?>
                                    <tr>
                                        <td><?php echo $product['ID']; ?></td>
                                        <td><?php echo $product['name']; ?></td>
                                        <td class="text-center"><?php echo $product['totalSold']; ?></td>
                                        <td class="text-center"><?php echo $product['quantity'] - $product['totalSold']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Biểu đồ doanh thu sản phẩm bán chạy nhất -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card chart overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu sản phẩm bán chạy nhất</h5>
                        <canvas id="topSellingProductsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card chart overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu theo danh mục</h5>
                        <canvas id="categoryRevenuePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ doanh thu theo ngày trong tháng -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card revenue-chart">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu theo ngày trong tháng <?php echo date('m/Y'); ?></h5>
                        <canvas id="revenueChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ tổng doanh thu theo tháng và danh mục -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card chart overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Tổng doanh thu theo tháng và danh mục</h5>
                        <canvas id="monthlyCategoryRevenueChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

<?php include 'footer.php'; ?>

<script>
    function number_format(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep,
            dec = (typeof decPoint === 'undefined') ? '.' : decPoint,
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        return (prec ? toFixedFix(n, prec) : '' + Math.round(n)).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1' + sep).replace('.', dec);
    }

    // Biểu đồ doanh thu sản phẩm bán chạy nhất
    const ctx4 = document.getElementById('topSellingProductsChart').getContext('2d');
    const productNames = <?php echo json_encode(array_column($topSellingProductsData, 'productName')); ?>;
    const productRevenues = <?php echo json_encode(array_column($topSellingProductsData, 'totalRevenue')); ?>;

    // Mảng màu cho các cột
    const colors = [
        'rgba(255, 99, 132, 0.5)', // Màu đỏ
        'rgba(54, 162, 235, 0.5)', // Màu xanh da trời
        'rgba(255, 206, 86, 0.5)', // Màu vàng
        'rgba(75, 192, 192, 0.5)', // Màu xanh lá
        'rgba(153, 102, 255, 0.5)', // Màu tím
        'rgba(255, 159, 64, 0.5)', // Màu cam
        'rgba(201, 203, 207, 0.5)', // Màu xám
        'rgba(255, 0, 255, 0.5)',   // Màu hồng
        'rgba(0, 255, 255, 0.5)',   // Màu cyan
        'rgba(128, 0, 128, 0.5)'    // Màu tím đậm
    ];

    // Tạo mảng màu tương ứng với số lượng sản phẩm
    const backgroundColors = productNames.map((_, index) => colors[index % colors.length]);

    const topSellingProductsChart = new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: productNames,
            datasets: [{
                label: 'Sản phẩm bán chạy nhất',
                data: productRevenues,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(color => color.replace('0.5', '1')), // Đổi độ trong suốt cho đường viền
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${number_format(value, 0, ',', '.')} VNĐ`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    }
                },
                x: {
                    ticks: {
                        // Xoay nhãn trục x 90 độ
                        minRotation: 90,
                        maxRotation: 90
                    }
                }
            },
        }
    });

    // Biểu đồ doanh thu theo danh mục
    const ctx1 = document.getElementById('categoryRevenuePieChart').getContext('2d');
    const categoryNames = <?php echo json_encode(array_column($categoryRevenueData, 'categoryName')); ?>;
    const categoryRevenues = <?php echo json_encode(array_column($categoryRevenueData, 'totalRevenue')); ?>;

    const categoryRevenuePieChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: categoryNames,
            datasets: [{
                label: 'Doanh thu theo danh mục',
                data: categoryRevenues,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(color => color.replace('0.5', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${number_format(value, 0, ',', '.')} VNĐ`;
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ doanh thu theo ngày
    const ctx2 = document.getElementById('revenueChart').getContext('2d');
    const days = <?php echo json_encode(array_column($dailyRevenue, 'day')); ?>;
    const dailyRevenues = <?php echo json_encode(array_column($dailyRevenue, 'totalRevenue')); ?>;

    const revenueChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Doanh thu theo ngày',
                data: dailyRevenues,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                backgroundColor: 'rgba(75, 192, 192, 0.5)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Ngày'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${number_format(value, 0, ',', '.')} VNĐ`;
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ doanh thu theo tháng và danh mục
    const ctx3 = document.getElementById('monthlyCategoryRevenueChart').getContext('2d');
    const monthlyData = <?php echo json_encode($monthlyCategoryRevenueData); ?>;
    const months = [...new Set(monthlyData.map(item => item.orderMonth))];
    const categories = [...new Set(monthlyData.map(item => item.categoryName))];
    const revenueData = {};
    categories.forEach(category => {
        revenueData[category] = Array(months.length).fill(0); // Khởi tạo mảng doanh thu cho mỗi tháng
    });
    monthlyData.forEach(item => {
        const monthIndex = months.indexOf(item.orderMonth);
        if (monthIndex !== -1) {
            revenueData[item.categoryName][monthIndex] += parseFloat(item.totalRevenue);
        }
    });
    const datasets = categories.map((category, index) => ({
        label: category,
        data: revenueData[category],
        backgroundColor: colors[index % colors.length], // Sử dụng màu sắc khác nhau
        borderColor: colors[index % colors.length].replace(/0.5/, '1'), // Màu viền
        borderWidth: 1
    }));
    const monthlyCategoryChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: months,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tháng'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${number_format(value, 0, ',', '.')} VNĐ`;
                        }
                    }
                }
            }
        }
    });
</script>