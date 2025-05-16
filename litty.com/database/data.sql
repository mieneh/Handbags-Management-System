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

INSERT INTO Products (ID, name, categoryID, description, price, stock, image, promotional, promotionalprice, created) VALUES
    (1, 'Túi chần bông tiện lợi', 1, 'Túi chần bông tiện lợi với nhiều ngăn chứa, giúp bạn sắp xếp đồ dùng một cách dễ dàng. Phù hợp cho các buổi đi làm hoặc đi học, giúp bạn giữ mọi thứ gọn gàng và ngăn nắp. ', 250000.00, 100, '../img/tui-chan-bong-tien-loi.png', 'no', NULL, '2024-10-30 11:04:02'),
    (2, 'Túi chần bông khủng long', 1, 'Túi chần bông cỡ lớn, chứa được nhiều vật dụng, phù hợp cho dân văn phòng.', 250000.00, 100, '../img/tui-chan-bong-khung-long.png', 'no', NULL, '2024-10-30 11:04:02'),
    (3, 'Túi chần bông họa tiết hoa', 1, 'Túi chần bông với họa tiết hoa nhã nhặn, mang lại cảm giác tươi mới và dịu dàng. Lý tưởng cho các buổi dạo phố hay đi dã ngoại, túi giúp bạn dễ dàng phối hợp với nhiều loại trang phục.', 250000.00, 100, '../img/tui-chan-bong-hoa-tiet-hoa.png', 'no', NULL, '2024-10-30 11:04:02'),
    (4, 'Túi chần bông trắng sang trọng', 1, 'Túi chần bông màu trắng sang trọng, dễ dàng kết hợp với bất kỳ trang phục nào. Thiết kế đơn giản nhưng thanh lịch, thích hợp cho các dịp tiệc tùng hoặc đi làm.', 250000.00, 100, '../img/tui-chan-bong-trng-sang-trong.png', 'no', NULL, '2024-10-30 11:04:02'),
    (5, 'Túi chần bông nâu xinh', 1, 'Túi chần bông với màu nâu xinh nhẹ nhàng, tạo cảm giác dịu dàng và nữ tính. Lựa chọn hoàn hảo cho các cô gái yêu thích phong cách dễ thương và lãng mạn.', 250000.00, 100, '../img/tui-chan-bong-nau-xinh.png', 'no', NULL, '2024-10-30 11:04:02'),
    (6, 'Túi chần bông thể thao', 1, 'Túi chần bông thể thao, chắc chắn và tiện lợi, lý tưởng cho những buổi tập gym hoặc đi chơi thể thao. Với kích thước rộng rãi, bạn có thể mang theo mọi thứ cần thiết mà không lo thiếu chỗ.', 250000.00, 100, '../img/tui-chan-bong-the-thao.png', 'no', NULL, '2024-10-30 11:04:02'),
    (7, 'Móc khóa bông hoa', 3, 'Móc khóa bông hoa được thiết kế với hình dáng dễ thương, phù hợp để trang trí cho chìa khóa, balo, hay túi xách của bạn. Với chất liệu mềm mại và màu sắc tươi sáng, sản phẩm không chỉ giúp bạn giữ chìa khóa một cách an toàn mà còn làm nổi bật phong cách của bạn.', 10000.00, 10, '../img/moc-khoa-bong-hoa.jpeg', 'no', NULL, '2024-10-30 11:04:02'),
    (8, 'Móc khóa ngôi sao', 3, 'Móc khóa ngôi sao mang đến vẻ đẹp lấp lánh với thiết kế hình ngôi sao độc đáo. Được làm từ chất liệu bền và chắc chắn, sản phẩm này sẽ giúp bạn giữ chìa khóa một cách an toàn và phong cách. Móc khóa này không chỉ là một món phụ kiện hữu ích mà còn là một món quà tuyệt vời cho những người yêu thích cái đẹp.', 15000.00, 10, '../img/moc-khoa-ngoi-sao.jpeg', 'no', NULL, '2024-10-30 11:04:02'),
    (9, 'Móc khóa túi mù', 3, 'Móc khóa túi mù là một phụ kiện nhỏ gọn và tinh tế, phù hợp để trang trí cho chìa khóa, túi xách, hoặc balo của bạn. Sản phẩm này có thiết kế đơn giản nhưng mang lại sự dễ thương và thu hút. Được làm từ chất liệu bền chắc, móc khóa túi mù có màu sắc dịu dàng và kiểu dáng mềm mại, giúp tạo điểm nhấn tinh tế cho các vật dụng cá nhân của bạn. Đây cũng là một món quà đáng yêu dành tặng bạn bè và người thân.', 12000.00, 10, '../img/moc-khoa-tui-mu.jpg', 'no', NULL, '2024-10-30 11:04:02'),
    (10, 'Túi thơm lavender', 3, 'Túi thơm hương lavender, tạo hương dễ chịu trong túi.', 10000.00, 20, '../img/tui-thom-lavender.jpg', 'no', NULL, '2024-10-30 11:04:02'),
    (11, 'Túi thơm oải hương', 3, 'Túi thơm ỏai hương được làm từ chất liệu vải tự nhiên, chứa đựng hương thơm nhẹ nhàng, thư giãn từ ỏi. Sản phẩm không chỉ mang lại mùi hương dễ chịu cho không gian sống mà còn giúp xua đuổi côn trùng, tạo cảm giác thoải mái, dễ chịu. Chiếc túi này có thể được đặt trong tủ quần áo, xe hơi hoặc bất kỳ không gian nào bạn muốn thêm một chút hương thơm tự nhiên. Với thiết kế nhỏ gọn và dễ thương, túi thơm ỏi hương không chỉ là một sản phẩm hữu ích mà còn là món quà tuyệt vời cho bạn bè và người thân.', 10000.00, 20, '../img/tui-thom-oai-huong.png', 'no', NULL, '2024-10-30 11:04:02'),
    (12, 'Mẫu 1', 2, ' ', 312500.00, 100, '../img/mau-1.jpg', 'yes', 20.00, '2024-10-30 11:04:02'),
    (13, 'Mẫu 2', 2, ' ', 312500.00, 100, '../img/mau-2.jpg', 'yes', 20.00, '2024-10-30 11:04:02'),
    (14, 'Mẫu 3', 2, ' ', 312500.00, 100, '../img/mau-3.jpg', 'yes', 20.00, '2024-10-30 11:04:02');


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
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role enum('admin','user') NOT NULL DEFAULT 'user'
);

INSERT INTO Users (fullname, email, phone, password, city, district, ward, address, image, created, role) VALUES
('Litty Shop', 'litty@gmail.com', '+84934228023', '$2y$10$Jlem3phfShB4gnyEnWTGq.qdi53eEcOtBUNz7sDT25b5QbQyGosYK', 'Thành phố Hồ Chí Minh', 'Quận 10', 'Phường 02', 'Trần Nhân Tôn', '../img/admin.jpg', '2024-11-04 03:13:47', 'admin'),
('Đinh Phương My', 'dinhphuongmy21012003@gmail.com', '+84374773039', '$2y$10$Ld3itc6FD5g.PXdm/RZWhOur5E/80n0G3Ry3TbL4L/bGHHdhRthTe', 'Thành phố Hồ Chí Minh', 'Quận 7', 'Phường Tân Quy', 'Phòng 407, Số 20C, Đường số 53', '../img/admin.jpg', '2024-11-04 05:17:52', 'user'),
('Hoàng Bích Hà', 'hoangbichha12@gmail.com', '+84937553071', '$2y$10$uN2aXdYZzvU8.Iu4.5WDwuhh5XAavPrPnMOh0e4t0k/1RE.Qm9U3m', 'Thành phố Hồ Chí Minh', 'Quận Tân Phú', 'Phường Phú Trung', '173/2 Khuông Việt', '../img/admin.jpg', '2024-11-03 13:34:54', 'user'),
('Đoàn Thị Minh Hương', 'doanthiminhhuong2003@gmail.com', '+84869427524', '$2y$10$uN2aXdYZzvU8.Iu4.5WDwuhh5XAavPrPnMOh0e4t0k/1RE.Qm9U3m', 'Thành phố Hồ Chí Minh', 'Quận 8', 'Phường 4', '43/20 Dạ Nam', '../img/admin.jpg', '2024-11-03 13:35:59', 'user'),
('Trương Kim Quyên', 'quyentruong157@gmail.com', '+84934228023', '$2y$10$uN2aXdYZzvU8.Iu4.5WDwuhh5XAavPrPnMOh0e4t0k/1RE.Qm9U3m', 'Thành phố Hồ Chí Minh', 'Quận 10', 'Phường 2', '3 Trần Nhân Tôn', '../img/admin.jpg', '2024-11-03 13:37:05', 'user'),
('Trần Nguyễn Chánh Nguyên', 'nguyen.tnc2003@gmail.com', '+84772877264', '$2y$10$uN2aXdYZzvU8.Iu4.5WDwuhh5XAavPrPnMOh0e4t0k/1RE.Qm9U3m', 'Thành phố Hồ Chí Minh', 'Quận Tân Bình', 'Phường 14', '117 Ba Vân', '../img/admin.jpg', '2024-11-03 13:38:06', 'user'),
('Lê Hoàng Nhi', 'lehoangnhi2906@gmai.com', '+84329526712', '$2y$10$uN2aXdYZzvU8.Iu4.5WDwuhh5XAavPrPnMOh0e4t0k/1RE.Qm9U3m', 'Thành phố Hồ Chí Minh', 'Quận 3', 'Phường 5', '441/22 Nguyễn Đình Chiểu', '../img/admin.jpg', '2024-11-03 13:40:47', 'user');

CREATE TABLE Orders (
    ID varchar(6) PRIMARY KEY,
    userID INT,
    guestname VARCHAR(100),
    guestaddress TEXT,
    guestphone VARCHAR(20),
    total DECIMAL(10, 2),
    orderstatus ENUM('pending', 'processing', 'completed', 'canceled') DEFAULT 'pending',
    discountCode VARCHAR(50) DEFAULT NULL,
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

-- CREATE TABLE Payment (
--     ID INT PRIMARY KEY AUTO_INCREMENT,
--     orderID INT,
--     payment_method ENUM('credit_card', 'paypal', 'cash_on_delivery'),
--     payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
--     FOREIGN KEY (orderID) REFERENCES Orders(ID) ON DELETE CASCADE
-- );

CREATE TABLE Discounts (
    ID VARCHAR(50) PRIMARY KEY,
    discount DECIMAL(5, 2) NOT NULL,
    sDate DATE NOT NULL,
    eDate DATE NOT NULL,
    limitUse INT NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);