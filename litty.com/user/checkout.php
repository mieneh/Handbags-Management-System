<?php
session_start();
include '../database/connect.php';

$userID = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$userInfo = null;

if ($userID) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE ID = ?");
    $stmt->execute([$userID]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

foreach ($cart as $item) {
    if (isset($item['price'], $item['quantity'])) {
        $total += $item['price'] * $item['quantity'];
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

$today = date('Y-m-d');
$stmt = $pdo->prepare(" SELECT * FROM Discounts WHERE (sDate <= :today) AND (eDate = '0000-00-00' OR eDate >= :today) AND limitUse > 0");
$stmt->execute(['today' => $today]);
$discountCodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filePath = '../database/tinh-thanh-vietnam.csv';
$locations = readLocationsFromFile($filePath);

?>

<?php include 'header.php';?>

<link rel="stylesheet" href="css/checkoutt.css">
<section class="section">
    <div class="heading">
        <h2>Thanh Toán Đơn Hàng</h2>
    </div>
    <div class="container">
        <form action="checkout-process.php" method="POST">
            <div class="row">
                <!-- Customer Information -->
                <div class="column left">
                    <h4>Thông tin giao hàng</h4>
                    <div class="form-group">
                        <label for="name">Họ và tên</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userInfo['fullname'] ?? ''); ?>" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email'] ?? ''); ?>" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userInfo['address'] ?? ''); ?>" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                    </div>
                    
                    <div class="row" style="flex: 1">
                        <div class="form-group">
                            <label for="city">Tỉnh / Thành phố</label>
                            <select id="city" name="city" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                                <option value="" disabled selected>Chọn Tỉnh / Thành</option>
                                <?php foreach ($locations as $cityName => $districts): ?>
                                    <option value="<?php echo htmlspecialchars($cityName); ?>" <?php echo ($userInfo['city'] ?? '') === $cityName ? 'selected' : ''; ?>><?php echo htmlspecialchars($cityName); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="district">Quận / Huyện</label>
                            <select id="district" name="district" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                                <option value="" disabled selected>Chọn Quận / Huyện</option>
                                <?php if ($userInfo && isset($locations[$userInfo['city']])): ?>
                                    <?php foreach ($locations[$userInfo['city']] as $district => $wards): ?>
                                        <option value="<?php echo htmlspecialchars($district); ?>" <?php echo ($userInfo['district'] ?? '') === $district ? 'selected' : ''; ?>><?php echo htmlspecialchars($district); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ward">Phường / Xã</label>
                            <select id="ward" name="ward" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                                <option value="" disabled selected>Chọn Phường / Xã</option>
                                <?php if ($userInfo && isset($locations[$userInfo['city']][$userInfo['district']])): ?>
                                    <?php foreach ($locations[$userInfo['city']][$userInfo['district']] as $ward): ?>
                                        <option value="<?php echo htmlspecialchars($ward); ?>" <?php echo ($userInfo['ward'] ?? '') === $ward ? 'selected' : ''; ?>><?php echo htmlspecialchars($ward); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userInfo['phone'] ?? ''); ?>" required <?php echo $userInfo ? 'readonly' : ''; ?>>
                    </div>

                    <!-- Shipping Method Section -->
                    <h4>Phương thức vận chuyển</h4>
                    <div class="form-group method">
                        <label for="shipping1">
                            <input type="radio" id="shipping1" name="shipping_method" value="home_delivery" checked>
                            Giao hàng tận nơi (30.000 VNĐ)
                        </label>
                    </div>
                    <div class="form-group method">
                        <label for="shipping2">
                            <input type="radio" id="shipping2" name="shipping_method" value="store_pickup">
                            Nhận tại cửa hàng (Miễn phí)
                        </label>
                    </div>

                    <!-- Payment Method -->
                    <h4>Phương thức thanh toán</h4>
                    <div class="form-group method">
                        <label for="payment1">
                            <input type="radio" id="payment1" name="payment_method" value="cod" checked>
                            Thanh toán khi giao hàng (COD)
                        </label>
                    </div>
                </div>
                <!-- Order Summary -->
                <div class="column right">
                    <h4>Đơn hàng của bạn</h4>
                    <div class="order-summary">
                        <!-- Display order items dynamically -->
                        <?php foreach ($cart as $item): ?>
                            <p><?php echo htmlspecialchars($item['name']); ?>: <?php echo number_format($item['quantity']); ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?> = <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</p>
                        <?php endforeach; ?>
                        <hr>
                        <p>Tạm tính: <span id="price"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span></p>
                        <p id="discount_line" style="display:none;">Giảm giá: <span id="discount_amount">0 VNĐ</span></p>
                        <p>Phí ship: <span id="shipping_fee">30.000 VNĐ</span></p>
                        <h5>Tổng tiền: <span id="total"><?php echo number_format($total + 30000, 0, ',', '.'); ?> VNĐ</span></h5> <!-- Cộng phí ship -->
                    </div>
                    <!-- Discount code -->
                    <div class="form-group">
                        <label for="discount_code">Mã giảm giá</label>
                        <select id="discount_code" name="discount_code">
                            <option value="" data-discount="0">-- Chọn mã giảm giá --</option>
                            <?php foreach ($discountCodes as $code): ?>
                                <option value="<?php echo htmlspecialchars($code['ID']); ?>" data-discount="<?php echo (float)$code['discount']; ?>">
                                    <?php echo htmlspecialchars($code['ID']) . ' - Giảm ' . number_format($code['discount'], 0) . '%';?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="intotal" name="total" value="<?php echo $total + 30000; ?>">
                    </div>
                </div>
            </div>

            <div class="form-group-submit">
                <button type="submit" class="btn-submit">Hoàn tất</button>
            </div>

        </form>
    </div>
</section>
<script>
    var locations = <?php echo json_encode($locations); ?>;
    var totalAmount = <?php echo $total; ?>;
</script>
<script src="js/location.js"></script>
<script src="js/checkout.js"></script>
<?php include 'footer.php';?>