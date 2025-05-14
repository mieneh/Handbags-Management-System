<?php
    session_start();
    include '../database/connect.php';
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE categoryID = :categoryID ORDER BY RAND() LIMIT 4");
    $stmt->execute(['categoryID' => 1]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php';?>

<link rel="stylesheet" href="css/index.css">

    <!-- Ảnh Trang Chủ -->
    <section class="home" id="home">
        <div class="home-text">
            <h3>Túi Thời Trang</h3>
            <h1>Trông <span>Phong Cách</span><br><span>Hãy </span>Phong Cách</h1>
            <p>Hãy mua ngay cho mình hoặc những người thân yêu của bạn ngay chiếc túi xinh yêu của nhà Litty nhé!</p>
            <a href="product.php" class="btn">Mua Ngay</a>
        </div>
        <div class="home-img">
            <img src="../img/home.png" alt="">
        </div>
    </section>

        <!-- Giới Thiệu -->
    <section class="about" id="about">
        <div class="about-img">
            <img src="../img/bag.png" alt="">
        </div>
        <div class="about-text">
            <h2 style="margin-bottom: 10px;">Shop Litty</h2>
            <p>Dự án Sản xuất và kinh doanh túi xách <strong><em>LITTY</em></strong> ấp ủ ra đời với mong muốn mang đến cho khách hàng những món quà độc đáo và tiện dụng. Sản phẩm đầu tiên mà <strong><em>LITTY</em></strong> tự hào ra mắt chính là túi chần bông (puffer bag) – một sản phẩm nổi bật với vẻ ngoài bồng bềnh, xinh xắn, mang lại sự tiện ích cho khách hàng, đặc biệt là phái nữ, bao gồm sinh viên, dân văn phòng và những người yêu thích phong cách năng động, hiện đại.</p>
            <p style="margin-top: 28px;"><a href="about.php" class="btn" >Tìm hiểu thêm</a></p>
        </div>
    </section>

    <!-- Sản Phẩm -->
    <section class="products" id="products">
        <div class="heading">
            <h2>Sản Phẩm Phổ Biến</h2>
        </div>
        <div class="products-container">
            <?php foreach ($products as $product): ?>
                <div class="box">
                    <a href="product-detail.php?id=<?php echo $product['ID']; ?>">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                    <h2><?php echo $product['name']; ?></h2>
                    <span>Giá: <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                    <form action="cart-add.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="productID" value="<?php echo $product['ID']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn" style="background: none; border: none; cursor: pointer;">
                            <i class='bx bxs-cart-add'></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>  
        </div>
    </section>

    <style>
        
  /* Container Carousel */
  .carousel-container {
    width: 100%;
    max-width: 800px;
    margin: auto;
    position: relative;
    overflow: hidden;
  }

  /* Style cho các Slide */
  .carousel-slide {
    display: flex;
    transition: transform 0.5s ease-in-out;
    width: 300%; /* Ba hình ảnh, mỗi ảnh 100% */
  }

  /* Slide Hình Ảnh */
  .carousel-slide img {
    width: 100%;
    height: auto;
  }

  /* Dot Indicators */
  .dots-container {
    text-align: center;
    margin-top: 10px;
  }

  .dot {
    display: inline-block;
    height: 12px;
    width: 12px;
    margin: 0 4px;
    background-color: #ccc;
    border-radius: 50%;
    cursor: pointer;
  }

  .dot.active {
    background-color: #333;
  }
    </style>
    <section class="product" id="design">
        <div class="heading">
            <h2>Thiết Kế Túi Xách Độc Đáo</h2>                
        </div>
        <div class="products-design" style="margin-top: 15px; display: flex; flex-wrap: wrap; justify-content: space-between;">
            <div style="flex: 0.65;">
                <p style="margin-top: 30px; margin-bottom: 15px;">Nếu bạn đang tìm kiếm những chiếc túi thực sự <strong>độc đáo</strong> và mang dấu ấn cá nhân, <strong>Nhà Litty</strong> chính là nơi dành cho bạn! 🎨 Tại đây, bạn có thể <strong>tự tay thiết kế</strong> những mẫu túi yêu thích của mình, từ hình dáng cho đến màu sắc, để tạo ra sản phẩm mang đậm <strong>cá tính riêng</strong>.</p>
                <h3 style="margin-bottom: 10px;">🌈 Khám phá tự do sáng tạo</h3>
                <p style="margin-bottom: 15px;">Chúng tôi cung cấp hàng loạt mẫu mã và màu sắc đa dạng, từ những thiết kế <strong>cổ điển</strong> đến <strong>hiện đại</strong>, giúp bạn dễ dàng chọn lựa. Hãy thử nghiệm với những màu sắc mà bạn yêu thích và tạo nên một chiếc túi xinh xắn theo phong cách riêng của mình!</p>

                <h3 style="margin-bottom: 10px;">✍️ Thêm chi tiết cá nhân</h3>
                <p style="margin-bottom: 5px;">Ngoài việc chọn mẫu và màu sắc, bạn còn có thể thêm các chi tiết cá nhân như:</p>
                    <ul>
                        <li style="margin-left: 30px;"><em>✓ Chữ khắc riêng ✨</em></li>
                        <li style="margin-left: 30px;"><em>✓ Hình thêu tên 🎉</em></li>
                        <li style="margin-bottom: 15px; margin-left: 30px;"><em>✓ Biểu tượng yêu thích 💖</em></li>
                    </ul>
                </p>

                <h3 style="margin-bottom: 10px;">👛 Đội Ngũ Thiết Kế Chuyên Nghiệp</h3>
                <p style="margin-bottom: 15px;">Chúng tôi luôn sẵn sàng hỗ trợ bạn trong quá trình thiết kế, đảm bảo rằng mỗi sản phẩm đều mang đến sự hài lòng và phù hợp với mong đợi của bạn. <strong>Litty</strong> không chỉ tạo ra sản phẩm, mà còn giúp bạn thể hiện <strong>phong cách sống</strong> và <strong>cá tính</strong> riêng biệt!</p>

                <p style="margin-bottom: 15px;">🎊 Chúng tôi rất mong được đồng hành cùng bạn trong việc <strong>khẳng định bản thân</strong> qua những chiếc túi mang đậm phong cách riêng!</p>
                
                <div style="margin-top: 30px;">
                    <a href="design-product.php" class="btn">Thiết kế ngay</a>
                </div>
            </div>
            <div style="flex: 0.35;">
                <div class="carousel-container">
                    <div class="carousel-slide" id="carouselSlide">
                        <img style="width: 100%; max-width: 500px; border-radius: 8px; display: block; margin: 0;" src="../img/customizations/square-purple-v1.png" alt="Image 1">
                        <img style="width: 100%; max-width: 500px; border-radius: 8px; display: block; margin: 0;" src="../img/customizations/square-purple-v2.png" alt="Image 2">
                        <img style="width: 100%; max-width: 500px; border-radius: 8px; display: block; margin: 0;" src="../img/customizations/square-purple-v3.png" alt="Image 3">
                    </div>
                </div>

                <div class="dots-container">
                    <span class="dot active" onclick="moveSlide(0)"></span>
                    <span class="dot" onclick="moveSlide(1)"></span>
                    <span class="dot" onclick="moveSlide(2)"></span>
                </div>
            </div>
        </div>
    </section>
    <script>
  // JavaScript cho Carousel
  let currentSlide = 0;
  const slides = document.querySelectorAll(".carousel-slide img");
  const dots = document.querySelectorAll(".dot");

  function showSlide(index) {
    const slideWidth = slides[0].clientWidth;
    document.getElementById("carouselSlide").style.transform = `translateX(-${index * slideWidth}px)`;
    dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  // Tự động chuyển slide sau mỗi 3 giây
  setInterval(nextSlide, 3000);

  // Chuyển đến slide cụ thể khi nhấn vào dot
  function moveSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
  }
</script>
    <!-- Các khách hàng đã review về chất lượng sản phẩm -->
        <section class="customers" id="customers">
            <div class="heading">
                <h2>Khách Hàng Của Chúng Tôi</h2>
            </div>
            <div class="customers-container">
                <div class="box">
                    <img src="../img/custumer1.jpg" alt="Đánh giá của khách hàng">
                    <h2>Hoàng Bích Hà</h2>
                    <p>Túi xách LITTY có thiết kế thật sự nổi bật, giúp tôi tự tin hơn khi ra ngoài. Màu sắc và kiểu dáng rất thời trang!</p>
                    <div class="stars">
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star-half'></i>
                    </div>
                </div>
                <div class="box">
                    <img src="../img/custumer2.jpg" alt="Đánh giá của khách hàng">
                    <h2>Trương Kim Quyên</h2>
                    <p>Chất liệu của túi rất bền và chống nước, tôi không còn lo lắng khi đi ra ngoài trời mưa. Đúng là một sản phẩm chất lượng!</p>
                    <div class="stars">
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star-half'></i>
                    </div>
                </div>
                <div class="box">
                    <img src="../img/custumer3.jpg" alt="Đánh giá của khách hàng">
                    <h2>Đoàn Thị Minh Hương</h2>
                    <p>Túi rất nhẹ và có không gian rộng rãi, tôi có thể chứa nhiều đồ mà không cảm thấy nặng nề. Thực sự rất tiện lợi cho cuộc sống bận rộn của tôi!</p>
                    <div class="stars">
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star'></i>
                        <i class='bx bxs-star-half'></i>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="newsletter">
            <h2>Đăng ký ngay để nhận ưu đãi đặc biệt</h2>
            <!-- Form -->
            <form action="">
                <input type="email" placeholder="Nhập Email Của Bạn..." required>
                <input type="submit" value="Đăng Ký" class="email-btn">
            </form>
        </section>
<?php include 'footer.php';?>