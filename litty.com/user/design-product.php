<?php
    session_start();
    include '../database/connect.php'; 

    function fetchData($pdo, $query) {
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    $products = fetchData($pdo, "SELECT * FROM Products WHERE categoryID = 2");
    $colors = fetchData($pdo, "SELECT * FROM colors ORDER BY ID");
    $shapes = fetchData($pdo, "SELECT * FROM shapes");
?>

<?php include 'header.php';?>

<link rel="stylesheet" href="css/design-products.css">

<section class="section">
    <div class="heading">
        <h2>Túi thiết kế</h2>
    </div>
    <div class="container design-product">
        <div class="left">
            <div class="image-container">
                <img id="product-preview" src="" alt="Túi thiết kế" />
                <div class="text-overlay text1">Nhập tên của bạn</div>
            </div>
        </div>

        <div class="right">
            <div class="price-section">
                <?php if (!empty($products)): 
                    $product = $products[0]; ?>
                    <span class='price'><?= number_format(($product['price'] - $product['price']*($product['promotionalprice']/100)), 0, ',', '.') ?> VNĐ</span>
                    <span class='old-price'><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</span>
                    <input type='hidden' id='availableStock' value='<?= $product['stock'] ?>'>
                <?php endif; ?>
            </div>

            <div class="design-options">
                <div class="text-input">
                    <label for="text">Khắc chữ</label>
                    <input type="text" id="text1" placeholder="Nhập tên của bạn">
                </div>

                <div class="text-input">
                    <label>Màu chữ</label>
                    <div class="text-color-option">
                        <button class="button black selected" onclick="setTextColor('black')"></button>
                        <button class="button white" onclick="setTextColor('white')"></button>
                    </div>
                </div>

                <div class="text-inputs-container">
                    <div class="text-input">
                        <label for="design-template">Mẫu mã</label>
                        <div class="option-buttons">
                            <?php foreach ($products as $index => $product): ?>
                                <button class="button template <?= $index === 0 ? 'selected' : ''; ?>" style="background-image: url('<?= $product['image']; ?>');" onclick="selectDesign('v<?= $index + 1; ?>', <?= $product['ID']; ?>, this)">
                                    <?= $product['name']; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="text-input">
                        <label for="shape">Kiểu dáng</label>
                        <div class="options-buttons">
                            <?php foreach ($shapes as $shape): ?>
                                <button class="button <?= $shape['name']; ?> <?= $shape['name'] === 'square' ? 'selected' : ''; ?>" onclick="selectShape('<?= $shape['name']; ?>', this)"><?= $shape['description']; ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="text-input">
                    <label for="color">Màu sắc</label>
                    <div class="options-buttons">
                        <?php foreach ($colors as $color): ?>
                            <button class="button <?= $color['name']; ?> <?= $color['name'] === 'white' ? 'selected' : ''; ?>" onclick="selectColor('<?= $color['name']; ?>', this)"></button>    
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="quantity-section">
                    <label for="quantity">Số lượng</label>
                    <input type="number" id="quantity" value="1" min="1" max="<?= !empty($product) ? $product['stock'] : 1; ?>">
                </div>
                <div class="button-container">
                    <button class="add-to-cart" id="addToCartButton">Đặt hàng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedDesign = 'v1';
        let selectedShape = 'square';
        let selectedColor = 'white';
        let textColor = 'black';
        let selectedProductId = '12';

        function initializeDefaults() {
            updateTextColor(); 
            updatePreview();
        }

        function selectDesign(design, productId, button) {
            selectedDesign = design;
            selectedProductId = productId;
            updateSelection(button);
            updatePreview();
        }

        function selectShape(shape, button) {
            selectedShape = shape;
            updateSelection(button);
            updatePreview();
        }

        function selectColor(color, button) {
            selectedColor = color;
            updateSelection(button);
            updatePreview();
        }

        function updatePreview() {
            const preview = document.getElementById('product-preview');
            preview.src = `../img/customizations/${selectedShape}-${selectedColor}-${selectedDesign}.png`;
        }

        function updateSelection(selectedButton) {
            const buttons = selectedButton.parentElement.querySelectorAll('.button');
            buttons.forEach(button => button.classList.remove('selected'));
            selectedButton.classList.add('selected');
        }

        document.getElementById('text1').addEventListener('input', function() {
            document.querySelector('.text-overlay.text1').textContent = this.value;
            updateTextColor();
        });

        function setTextColor(color) {
            textColor = color;
            updateTextColor();
        }

        function updateTextColor() {
            const overlay = document.querySelector('.text-overlay.text1');
            overlay.style.color = textColor === 'white' ? '#fff' : '#000';
        }

        document.getElementById('addToCartButton').addEventListener('click', function() {
            const textInput = document.getElementById('text1').value.trim();
            if (textInput === '') {
                alert('Vui lòng nhập tên của bạn.');
                return;
            }
            const quantity = document.getElementById('quantity').value;

            const cartItem = {
                productID: selectedProductId,
                quantity: quantity,
                shape: selectedShape,
                color: selectedColor,
                text: textInput,
                colorText: textColor,
                image: `../img/customizations/${selectedShape}-${selectedColor}-${selectedDesign}.png`
            };

            fetch('cart-add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(cartItem)
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                alert('Đã thêm vào giỏ hàng thành công!');
                window.location.href = 'cart.php';
            })
            .catch(error => console.error('Error:', error));
        });

        initializeDefaults();
    </script>
</section>
<?php include 'footer.php';?>