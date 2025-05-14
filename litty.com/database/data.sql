CREATE TABLE Categories (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE
);

INSERT INTO Categories (name) VALUES 
    ('Túi chần bông'),
    ('Túi thiết kế'),
    ('Phụ kiện trang trí');

CREATE TABLE Products (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE, 
    categoryID INT,
    description TEXT,
    price DECIMAL(10, 2),
    stock INT,
    image VARCHAR(255),
    promotional ENUM('yes', 'no') DEFAULT 'no',
    promotionalprice DECIMAL(10, 0),
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoryID) REFERENCES Categories(ID)
);

CREATE TABLE Colors (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE
);

INSERT INTO Colors (name) VALUES  
    ('white'), ('black'), ('pink'), ('blue'), ('green'), ('purple');

CREATE TABLE Shapes (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE,
    description TEXT
);

INSERT INTO Shapes (name, description) VALUES  
    ('square', '36 x 36'), ('rectangle', '36 x 26');

CREATE TABLE ProductColors (
    productID INT,
    colorID INT,
    FOREIGN KEY (productID) REFERENCES Products(ID) ON DELETE CASCADE,
    FOREIGN KEY (colorID) REFERENCES Colors(ID) ON DELETE CASCADE
);

CREATE TABLE ProductShapes (
    productID INT,
    shapeID INT,
    FOREIGN KEY (productID) REFERENCES Products(ID) ON DELETE CASCADE,
    FOREIGN KEY (shapeID) REFERENCES Shapes(ID) ON DELETE CASCADE
);

DELIMITER //

CREATE TRIGGER auto_assign_color_shape
AFTER INSERT ON Products
FOR EACH ROW
BEGIN
    IF NEW.categoryID = 2 THEN
        INSERT INTO ProductColors (productID, colorID)
        SELECT NEW.ID, Colors.ID FROM Colors;

        INSERT INTO ProductShapes (productID, shapeID)
        SELECT NEW.ID, Shapes.ID FROM Shapes;
    END IF;
END//

DELIMITER ;

INSERT INTO Products (name, categoryID, description, price, stock, image) VALUES
    ('Túi chần bông tiện lợi', 1, 'Túi chần bông tiện lợi với nhiều ngăn chứa, giúp bạn sắp xếp đồ dùng một cách dễ dàng. Phù hợp cho các buổi đi làm hoặc đi học, giúp bạn giữ mọi thứ gọn gàng và ngăn nắp.', 135000, 100, '../img/tui-chan-bong-tien-loi.png'),
    ('Túi chần bông khủng long', 1, 'Túi chần bông cỡ lớn, chứa được nhiều vật dụng, phù hợp cho dân văn phòng.', 155000, 100, '../img/tui-chan-bong-khung-long.png'),
    ('Túi chần bông họa tiết hoa', 1, 'Túi chần bông với họa tiết hoa nhã nhặn, mang lại cảm giác tươi mới và dịu dàng. Lý tưởng cho các buổi dạo phố hay đi dã ngoại, túi giúp bạn dễ dàng phối hợp với nhiều loại trang phục.', 185000, 100, '../img/tui-chan-bong-hoa-tiet-hoa.png'),
    ('Túi chần bông trắng sang trọng', 1, 'Túi chần bông màu trắng sang trọng, dễ dàng kết hợp với bất kỳ trang phục nào. Thiết kế đơn giản nhưng thanh lịch, thích hợp cho các dịp tiệc tùng hoặc đi làm.', 200000, 100, '../img/tui-chan-bong-trng-sang-trong.png'),
    ('Túi chần bông màu hồng pastel', 1, 'Túi chần bông với màu pastel nhẹ nhàng, tạo cảm giác dịu dàng và nữ tính. Lựa chọn hoàn hảo cho các cô gái yêu thích phong cách dễ thương và lãng mạn.', 150000, 100, '../img/tui-chan-bong-mau-hong-pastel.png'),
    ('Túi chần bông thể thao', 1, 'Túi chần bông thể thao, chắc chắn và tiện lợi, lý tưởng cho những buổi tập gym hoặc đi chơi thể thao. Với kích thước rộng rãi, bạn có thể mang theo mọi thứ cần thiết mà không lo thiếu chỗ.', 125000, 100, '../img/tui-chan-bong-the-thao.png'),
    ('Móc khóa bông hoa', 3, 'Móc khóa bông hoa được thiết kế với hình dáng dễ thương, phù hợp để trang trí cho chìa khóa, balo, hay túi xách của bạn. Với chất liệu mềm mại và màu sắc tươi sáng, sản phẩm không chỉ giúp bạn giữ chìa khóa một cách an toàn mà còn làm nổi bật phong cách của bạn.', 10000, 10, '../img/moc-khoa-bong-hoa.jpeg'),
    ('Móc khóa ngôi sao', 3, 'Móc khóa ngôi sao mang đến vẻ đẹp lấp lánh với thiết kế hình ngôi sao độc đáo. Được làm từ chất liệu bền và chắc chắn, sản phẩm này sẽ giúp bạn giữ chìa khóa một cách an toàn và phong cách. Móc khóa này không chỉ là một món phụ kiện hữu ích mà còn là một món quà tuyệt vời cho những người yêu thích cái đẹp.', 15000, 10, '../img/moc-khoa-ngoi-sao.jpeg'),
    ('Móc khóa túi mù', 3, 'Móc khóa túi mù là một phụ kiện nhỏ gọn và tinh tế, phù hợp để trang trí cho chìa khóa, túi xách, hoặc balo của bạn. Sản phẩm này có thiết kế đơn giản nhưng mang lại sự dễ thương và thu hút. Được làm từ chất liệu bền chắc, móc khóa túi mù có màu sắc dịu dàng và kiểu dáng mềm mại, giúp tạo điểm nhấn tinh tế cho các vật dụng cá nhân của bạn. Đây cũng là một món quà đáng yêu dành tặng bạn bè và người thân.', 12000, 10, '../img/moc-khoa-tui-mu.jpg'),
    ('Túi thơm lavender', 3, 'Túi thơm hương lavender, tạo hương dễ chịu trong túi.', 10.00, 20, '../img/tui-thom-lavender.jpg'),
    ('Túi thơm oải hương', 3, 'Túi thơm ỏai hương được làm từ chất liệu vải tự nhiên, chứa đựng hương thơm nhẹ nhàng, thư giãn từ ỏi. Sản phẩm không chỉ mang lại mùi hương dễ chịu cho không gian sống mà còn giúp xua đuổi côn trùng, tạo cảm giác thoải mái, dễ chịu. Chiếc túi này có thể được đặt trong tủ quần áo, xe hơi hoặc bất kỳ không gian nào bạn muốn thêm một chút hương thơm tự nhiên. Với thiết kế nhỏ gọn và dễ thương, túi thơm ỏi hương không chỉ là một sản phẩm hữu ích mà còn là món quà tuyệt vời cho bạn bè và người thân.', 10.00, 20, '../img/tui-thom-oai-huong.png'),
    ('Mẫu 1', 2, '', 150000, 100, '../img/mau-1.jpg'),
    ('Mẫu 2', 2, '', 150000, 100, '../img/mau-2.jpg'),
    ('Mẫu 3', 2, '', 150000, 100, '../img/mau-3.jpg');

CREATE TABLE Users (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255),
    city TEXT,
    district TEXT,
    ward TEXT,
    address TEXT,
    image VARCHAR(255) DEFAULT "../img/admin.jpg",
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Orders (
    ID varchar(6) PRIMARY KEY,
    userID INT,
    guestname VARCHAR(100),
    guestaddress TEXT,
    guestphone VARCHAR(20),
    total DECIMAL(10, 2),
    orderstatus ENUM('pending', 'processing', 'completed', 'canceled') DEFAULT 'pending',
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(ID) ON DELETE SET NULL
);

CREATE TABLE OrderItems (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    orderID varchar(6),
    productID INT,
    quantity INT,
    price DECIMAL(10, 2),
    shape varchar(50),
    color varchar(50),
    text text,
    colorText varchar(50),
    image VARCHAR(255),
    FOREIGN KEY (orderID) REFERENCES Orders(ID) ON DELETE CASCADE,
    FOREIGN KEY (productID) REFERENCES Products(ID)
);

CREATE TABLE Cart (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    userID INT,
    sessionID VARCHAR(255),
    productID INT,
    quantity INT,
    shape varchar(50),
    color varchar(50),
    text text,
    colorText varchar(50),
    image VARCHAR(255),
    FOREIGN KEY (userID) REFERENCES Users(ID) ON DELETE SET NULL,
    FOREIGN KEY (productID) REFERENCES Products(ID)
);

CREATE TABLE Payment (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    orderID INT,
    payment_method ENUM('credit_card', 'paypal', 'cash_on_delivery'),
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (orderID) REFERENCES Orders(ID) ON DELETE CASCADE
);

CREATE TABLE Discounts (
    ID VARCHAR(50) PRIMARY KEY,
    discount DECIMAL(5, 2) NOT NULL,
    sDate DATE NOT NULL,
    edate DATE NOT NULL,
    limitUse INT NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
