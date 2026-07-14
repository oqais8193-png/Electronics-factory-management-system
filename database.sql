-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS electronics_factory;
USE electronics_factory;

-- إنشاء جدول المنتجات
CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    specifications TEXT,
    quantity INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    supplier VARCHAR(255),
    manufacturing_date DATE,
    status ENUM('in_stock', 'low_stock', 'out_of_stock') DEFAULT 'in_stock',
    deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- إضافة بيانات أولية
INSERT INTO products (name, category, specifications, quantity, price, supplier, manufacturing_date, status) VALUES
('لوحة أم إنتل Z690', 'مكونات حاسوب', 'مقبس LGA 1700, DDR5, PCIe 5.0, منافذ USB 3.2', 50, 299.99, 'Intel Suppliers', '2023-05-15', 'in_stock'),
('معالج Core i9-13900K', 'معالجات', '24 نواة (8P+16E), تردد 3.0 GHz حتى 5.8 GHz, ذاكرة مخبئة 36MB', 30, 589.99, 'Intel Corporation', '2023-06-10', 'in_stock'),
('كرت شاشة RTX 4090', 'كرت شاشة', '24GB GDDR6X, 16384 وحدة معالجة, تردد 2.52 GHz', 15, 1599.99, 'NVIDIA Partners', '2023-04-20', 'low_stock'),
('ذاكرة DDR5 32GB', 'ذاكرة وصول عشوائي', 'سرعة 5600MHz, تأخير CL36, 32GB (2x16GB)', 80, 199.99, 'Corsair', '2023-07-01', 'in_stock'),
('SSD 2TB NVMe', 'تخزين', 'سرعة قراءة 7000MB/s, سرعة كتابة 6000MB/s, PCIe 4.0', 45, 229.99, 'Samsung Electronics', '2023-05-30', 'in_stock'),
('شاشة 32 بوصة 4K', 'شاشات', 'دقة 3840x2160, 144Hz, تقنية HDR600, منفذ DisplayPort 1.4', 25, 699.99, 'LG Display', '2023-03-22', 'in_stock'),
('لوحة مفاتيح ميكانيكية', 'ملحقات', 'مفاتيح Cherry MX Red, إضاءة RGB, مقاومة للتناثر', 60, 129.99, 'Logitech', '2023-08-05', 'in_stock'),
('ماوس الألعاب', 'ملحقات', '16000 DPI, 8 أزرار قابلة للبرمجة, إضاءة RGB', 75, 79.99, 'Razer Inc.', '2023-07-18', 'in_stock'),
('سماعات رأس لاسلكية', 'ملحقات', 'إلغاء ضوضاء نشط, بطارية 30 ساعة, تقنية Bluetooth 5.2', 40, 199.99, 'Sony', '2023-06-12', 'in_stock'),
('محول طاقة 1000W', 'إمدادات طاقة', 'كفاءة 80+ Platinum, معياري كامل, تبريد هادئ', 35, 249.99, 'Seasonic', '2023-04-15', 'low_stock'),
('كرت صوت خارجي', 'ملحقات', '7.1 قنوات, منفذ USB-C, تحكم بمستوى الصوت', 20, 149.99, 'Creative Labs', '2023-08-20', 'in_stock'),
('كاميرا ويب 4K', 'ملحقات', 'دقة 3840x2160, ميكروفون مدمج, حامل قابل للتعديل', 18, 179.99, 'Logitech', '2023-07-30', 'in_stock'),
('مروحة تبريد líquida', 'تبريد', 'مزدوجة المشعاع 240mm, إضاءة RGB, مضخة هادئة', 28, 159.99, 'Cooler Master', '2023-05-10', 'in_stock'),
('حاسوب محمول للألعاب', 'حواسيب محمولة', 'معالج i7-13700H, كرت شاشة RTX 4070, شاشة 17 بوصة 240Hz', 12, 2199.99, 'ASUS', '2023-08-01', 'low_stock'),
('محول شبكة 10Gb', 'شبكات', 'منفذين 10GbE, متوافق مع PCIe 4.0, دعم تكوين الشبكات', 22, 129.99, 'TP-Link', '2023-06-25', 'in_stock'),
('لوحة أم AMD X670E', 'مكونات حاسوب', 'مقبس AM5, DDR5, PCIe 5.0, منافذ USB4', 38, 349.99, 'ASUS', '2023-06-15', 'in_stock'),
('معالج Ryzen 9 7950X', 'معالجات', '16 نواة, 32 خيط, تردد 4.5 GHz حتى 5.7 GHz, ذاكرة مخبئة 64MB', 28, 699.99, 'AMD', '2023-07-10', 'in_stock'),
('كرت شاشة RX 7900 XTX', 'كرت شاشة', '24GB GDDR6, 6144 وحدة معالجة, تردد 2.3 GHz', 18, 999.99, 'AMD', '2023-05-20', 'in_stock'),
('ذاكرة DDR4 16GB', 'ذاكرة وصول عشوائي', 'سرعة 3200MHz, تأخير CL16, 16GB (2x8GB)', 65, 89.99, 'Kingston', '2023-04-10', 'in_stock'),
('HDD 4TB', 'تخزين', 'سرعة 7200RPM, ذاكرة مخبئة 256MB, منفذ SATA 6Gb/s', 42, 89.99, 'Western Digital', '2023-03-15', 'in_stock'),
('شاشة 27 بوصة QHD', 'شاشات', 'دقة 2560x1440, 165Hz, تقنية HDR400, منفذ HDMI 2.1', 32, 399.99, 'Acer', '2023-02-28', 'in_stock'),
('لوحة مفاتيح لاسلكية', 'ملحقات', 'تصميم منخفض, إضاءة خلفية, بطارية 24 شهراً', 55, 79.99, 'Logitech', '2023-01-20', 'in_stock'),
('ماوس لاسلكي', 'ملحقات', 'تصميم مريح, بطارية 18 شهراً, تقنية Darkfield', 70, 69.99, 'Logitech', '2023-03-10', 'in_stock'),
('سماعات رأس للألعاب', 'ملحقات', '7.1 قنوات افتراضية, ميكروفون قابلة للسحب, إضاءة RGB', 45, 129.99, 'HyperX', '2023-04-05', 'in_stock'),
('محول طاقة 850W', 'إمدادات طاقة', 'كفاءة 80+ Gold, معياري, تبريد هادئ', 40, 149.99, 'Corsair', '2023-05-22', 'in_stock'),
('كرت شبكة لاسلكي', 'شبكات', 'Wi-Fi 6E, Bluetooth 5.2, منفذ PCIe', 30, 59.99, 'TP-Link', '2023-06-18', 'in_stock'),
('كاميرا مراقبة', 'أمن', 'دقة 4K, رؤية ليلية, مقاومة الطقس', 22, 199.99, 'Arlo', '2023-07-25', 'in_stock'),
('مروحة تبريد هوائية', 'تبريد', '120mm, إضاءة RGB, ضوضاء منخفضة', 50, 39.99, 'Noctua', '2023-08-12', 'in_stock'),
('حاسوب مكتبي للأعمال', 'حواسيب مكتبية', 'معالج i5-13400, 16GB RAM, 512GB SSD', 15, 899.99, 'Dell', '2023-08-08', 'low_stock'),
('محول Thunderbolt 4', 'ملحقات', 'منفذ Thunderbolt 4, دعم شحن 100W, نقل بيانات 40Gb/s', 25, 89.99, 'CalDigit', '2023-07-15', 'in_stock'),
('لوحة أم Mini-ITX', 'مكونات حاسوب', 'مقبس LGA 1700, DDR4, PCIe 4.0, تصميم مضغوط', 20, 199.99, 'ASRock', '2023-06-20', 'in_stock'),
('معالج Core i5-13600K', 'معالجات', '14 نواة (6P+8E), تردد 3.5 GHz حتى 5.1 GHz, ذاكرة مخبئة 24MB', 35, 319.99, 'Intel Corporation', '2023-07-05', 'in_stock'),
('كرت شاشة RTX 4060 Ti', 'كرت شاشة', '8GB GDDR6, 4352 وحدة معالجة, تردد 2.31 GHz', 28, 399.99, 'NVIDIA Partners', '2023-08-10', 'in_stock'),
('ذاكرة DDR5 64GB', 'ذاكرة وصول عشوائي', 'سرعة 6000MHz, تأخير CL30, 64GB (2x32GB)', 18, 299.99, 'G.Skill', '2023-07-28', 'in_stock'),
('SSD 4TB SATA', 'تخزين', 'سرعة قراءة 560MB/s, سرعة كتابة 530MB/s, منفذ SATA', 30, 199.99, 'Crucial', '2023-05-05', 'in_stock'),
('شاشة 34 بوصة Ultrawide', 'شاشات', 'دقة 3440x1440, 144Hz, تقنية HDR400, منحنية', 20, 799.99, 'Samsung', '2023-04-18', 'in_stock'),
('لوحة مفاتيح الألعاب', 'ملحقات', 'مفاتيح optical, إضاءة RGB قابلة للتخصيص, منفذ USB', 48, 149.99, 'SteelSeries', '2023-03-25', 'in_stock'),
('ماوس vertical', 'ملحقات', 'تصميم عمودي, مريح للرسغ, 6 أزرار', 38, 89.99, 'Logitech', '2023-02-15', 'in_stock'),
('سماعات رأس بلوتوث', 'ملحقات', 'إلغاء ضوضاء, طي, بطارية 20 ساعة', 42, 129.99, 'JBL', '2023-01-30', 'in_stock'),
('محول طاقة 750W', 'إمدادات طاقة', 'كفاءة 80+ Bronze, شبه معياري, مروحة 120mm', 55, 89.99, 'EVGA', '2023-06-08', 'in_stock'),
('كرت Capture', 'ملحقات', 'تسجيل فيديو 4K, منفذ HDMI, دعم البث المباشر', 16, 199.99, 'Elgato', '2023-07-20', 'low_stock'),
('كاميرا 360 درجة', 'ملحقات', 'دقة 5.7K, مقاومة الماء, دعم الواقع الافتراضي', 12, 399.99, 'Insta360', '2023-08-15', 'low_stock'),
('نظام تبريد بالماء', 'تبريد', 'مخصص, خزان, مضخة, مشعاع 360mm', 10, 299.99, 'EKWB', '2023-07-12', 'low_stock'),
('حاسوب محمول للعمل', 'حواسيب محمولة', 'معالج i7-1355U, 32GB RAM, 1TB SSD, شاشة 14 بوصة', 18, 1499.99, 'Lenovo', '2023-08-05', 'in_stock'),
('محول USB-C Hub', 'ملحقات', '7 منافذ, دعم 4K, منفذ Ethernet, شحن PD', 60, 69.99, 'Anker', '2023-06-30', 'in_stock'),
('لوحة أم AMD B650', 'مكونات حاسوب', 'مقبس AM5, DDR5, PCIe 4.0, منافذ USB 3.2', 32, 229.99, 'Gigabyte', '2023-07-22', 'in_stock'),
('معالج Ryzen 7 7700X', 'معالجات', '8 نواة, 16 خيط, تردد 4.5 GHz حتى 5.4 GHz, ذاكرة مخبئة 32MB', 40, 399.99, 'AMD', '2023-08-12', 'in_stock'),
('كرت شاشة RX 7600', 'كرت شاشة', '8GB GDDR6, 2048 وحدة معالجة, تردد 2.25 GHz', 35, 269.99, 'AMD', '2023-08-18', 'in_stock'),
('ذاكرة DDR4 32GB', 'ذاكرة وصول عشوائي', 'سرعة 3600MHz, تأخير CL18, 32GB (2x16GB)', 45, 99.99, 'Corsair', '2023-05-28', 'in_stock'),
('SSD 1TB NVMe', 'تخزين', 'سرعة قراءة 5000MB/s, سرعة كتابة 4000MB/s, PCIe 4.0', 50, 59.99, 'Western Digital', '2023-04-25', 'in_stock'),
('شاشة 24 بوصة FHD', 'شاشات', 'دقة 1920x1080, 75Hz, تقنية FreeSync, منفذ VGA', 65, 129.99, 'AOC', '2023-03-12', 'in_stock'),
('لوحة مفاتيح الألعاب اللاسلكية', 'ملحقات', 'تقنية 2.4GHz, إضاءة RGB, بطارية 40 ساعة', 30, 159.99, 'Corsair', '2023-02-20', 'in_stock'),
('ماوس الألعاب اللاسلكي', 'ملحقات', 'تقنية LightSpeed, 25000 DPI, بطارية 70 ساعة', 33, 129.99, 'Logitech', '2023-01-15', 'in_stock'),
('سماعات الألعاب اللاسلكية', 'ملحقات', 'تقنية 2.4GHz, إلغاء ضوضاء, إضاءة RGB', 28, 199.99, 'SteelSeries', '2023-06-05', 'in_stock'),
('محول طاقة 1200W', 'إمدادات طاقة', 'كفاءة 80+ Titanium, معياري كامل, ضمان 12 سنة', 15, 349.99, 'Seasonic', '2023-07-30', 'low_stock'),
('كرت شبكة 2.5Gb', 'شبكات', 'منفذ 2.5GbE, متوافق مع PCIe, دفع كبير', 40, 49.99, 'ASUS', '2023-08-22', 'in_stock'),
('كاميرا ويب 1080p', 'ملحقات', 'دقة 1920x1080, 60fps, ميكروفون مزدوج', 55, 69.99, 'Logitech', '2023-07-08', 'in_stock'),
('مروحة تبريد للخادم', 'تبريد', '120mm, ضوضاء منخفضة, تدفق هواء عالي', 25, 29.99, 'Noctua', '2023-06-12', 'in_stock'),
('حاسوب محمول للطلاب', 'حواسيب محمولة', 'معالج i3-1215U, 8GB RAM, 256GB SSD, شاشة 15.6 بوصة', 22, 499.99, 'HP', '2023-08-25', 'in_stock'),
('محول DisplayPort إلى HDMI', 'ملحقات', 'دعم 4K 60Hz, ذهبي, طول 1.8m', 80, 19.99, 'Cable Matters', '2023-05-15', 'in_stock');

-- تحديث حالة المنتجات بناءً على الكمية
UPDATE products SET status = 
    CASE 
        WHEN quantity <= 0 THEN 'out_of_stock'
        WHEN quantity < 10 THEN 'low_stock'
        ELSE 'in_stock'
    END;

-- إنشاء trigger لتحديث الحالة تلقائياً عند تغيير الكمية
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS update_product_status 
BEFORE UPDATE ON products
FOR EACH ROW 
BEGIN
    IF NEW.quantity <= 0 THEN
        SET NEW.status = 'out_of_stock';
    ELSEIF NEW.quantity < 10 THEN
        SET NEW.status = 'low_stock';
    ELSE
        SET NEW.status = 'in_stock';
    END IF;
END$$

DELIMITER ;