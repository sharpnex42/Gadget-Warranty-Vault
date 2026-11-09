-- 1. สร้างตารางผู้ใช้งาน
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);

-- 2. สร้างตารางหมวดหมู่อุปกรณ์
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

-- 3. สร้างตารางร้านค้า
CREATE TABLE Vendors (
    vendor_id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_name VARCHAR(100) NOT NULL,
    location VARCHAR(255)
);

-- 4. สร้างตารางข้อมูลอุปกรณ์
CREATE TABLE Assets (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    asset_name VARCHAR(150) NOT NULL,
    category_id INT,
    vendor_id INT,
    purchase_date DATE,
    warranty_expiry_date DATE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (category_id) REFERENCES Categories(category_id),
    FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id)
);

-- 5. สร้างตารางประวัติการซ่อมบำรุง
CREATE TABLE Maintenance_Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT,
    log_date DATE,
    description TEXT,
    cost DECIMAL(10,2),
    FOREIGN KEY (asset_id) REFERENCES Assets(asset_id)
);