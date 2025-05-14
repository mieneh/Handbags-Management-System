<?php
    include '../database/connect.php';
    $query = "SELECT * FROM Products";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'header.php';?>

<link rel="stylesheet" href="css/about.css">

<section class="section about" id="about">
    <div class="about-img">
        <img src="../img/bag.png" alt="">
    </div>
    <div class="about-text">
        <h2 style="text-align: center; margin-bottom: 30px;">Shop Litty</h2>
        <h3>1. TÊN Ý TƯỞNG</h3>
        <p><strong>Tên dự án:</strong> Sản xuất và kinh doanh túi xách LITTY</p>
        <p><strong>Sản phẩm:</strong> Túi chần bông (Puffer bag)</p>
        <p><strong>Ngành lĩnh vực kinh doanh:</strong> Thời trang</p>
        <h3 style="margin-top: 30px;">2. MÔ TẢ DỰ ÁN</h3>
        <p>Dự án Sản xuất và kinh doanh túi xách <strong><em>LITTY</em></strong> ấp ủ ra đời với mong muốn mang đến cho khách hàng những món quà độc đáo và tiện dụng. Sản phẩm đầu tiên mà <strong><em>LITTY</em></strong> tự hào ra mắt chính là túi chần bông (puffer bag) – một sản phẩm nổi bật với vẻ ngoài bồng bềnh, xinh xắn, mang lại sự tiện ích cho khách hàng, đặc biệt là phái nữ, bao gồm sinh viên, dân văn phòng và những người yêu thích phong cách năng động, hiện đại.</p>
        <p>Túi chần bông của <strong><em>LITTY</em></strong> được làm từ loại vải chống nước mang lại khả năng bảo vệ cho các vật dụng bên trong, chất liệu này còn giúp giữ cho bề mặt túi luôn sạch sẽ, dễ dàng lau chùi, tăng tuổi thọ sử dụng. Và đặc biệt hơn, lớp chần bông sẽ giúp túi giữ được form dáng gọn gàng, nhẹ nhàng ngay cả khi chứa nhiều đồ vật. Đặc điểm này mang lại sự thoải mái cho người sử dụng, không gây cảm giác nặng nề hoặc cồng kềnh, phù hợp với nhiều hoàn cảnh sử dụng như đi học, đi làm, hay đi dạo phố.</p>
        <p><strong><em>LITTY</em></strong> không chỉ dừng lại ở việc cung cấp một chiếc túi thông thường, mà còn đi kèm với các phụ kiện trang trí như charm (móc khóa trang trí) và túi thơm. Những chi tiết nhỏ này không chỉ giúp chiếc túi thêm phần xinh xắn và cá tính mà còn mang lại hương thơm dễ chịu cho khách hàng.</p>
        <p><strong><em>LITTY</em></strong> hiểu rằng, sự cá nhân hóa là yếu tố ngày càng quan trọng trong việc thu hút khách hàng, đặc biệt là thế hệ trẻ. Vì vậy, ngoài việc cung cấp các mẫu túi có sẵn, website chính thức của <strong><em>LITTY</em></strong> còn có tính năng tùy chỉnh thiết kế túi. Khách hàng có thể tự do thiết kế túi, từ việc lựa chọn hình dạng, màu sắc đến việc thêm chữ khắc riêng như tên mình hoặc tên người nhận quà. Tính năng này tạo ra trải nghiệm mua sắm độc đáo và cá nhân hóa, giúp mỗi chiếc túi trở nên đặc biệt và mang dấu ấn riêng của từng khách hàng.</p>
    </div>
</section>
<?php include 'footer.php';?>