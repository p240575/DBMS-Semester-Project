
INSERT INTO Admins (username, password, email) VALUES
('admin', '$2y$10$jBhteALjmL3jOZxz7gyxV.0x5EHY.9db6VrK.yk6s2qUnBm/igguO', 'admin@gmail.com');

INSERT INTO Categories (category_id, name, description) VALUES
(1, 'Electronics', 'Premium electronic gadgets and accessories.'),
(2, 'Mens Fashion', 'High-end men apparel and accessories.'),
(3, 'Womens Fashion', 'Designer dresses, bags, and luxury items.'),
(4, 'Home & Office', 'Modern furniture and office equipment.');

-- 24 Products (6 per category)
INSERT INTO Products (product_id, name, description, status) VALUES
(1, 'Smartphone X Pro', 'Latest generation flagship smartphone with AI capabilities.', 'active'),
(2, 'UltraBook 15', 'Thin and light premium laptop for professionals.', 'active'),
(3, 'Noise Cancelling Headphones', 'Industry-leading noise cancellation over-ear headphones.', 'active'),
(4, 'Smartwatch Elite', 'Advanced health tracking and sleek design.', 'active'),
(5, 'Wireless Earbuds', 'True wireless earbuds with rich sound.', 'active'),
(6, '4K Action Camera', 'Capture your adventures in stunning 4K.', 'active'),
(7, 'Classic Navy Suit', 'Premium wool tailored fit suit.', 'active'),
(8, 'Leather Oxford Shoes', 'Handcrafted genuine leather shoes.', 'active'),
(9, 'Chronograph Watch', 'Luxury men timepiece with steel band.', 'active'),
(10, 'Silk Designer Tie', 'Elegant patterned silk tie.', 'active'),
(11, 'Cashmere Sweater', 'Ultra-soft winter essential.', 'active'),
(12, 'Designer Sunglasses', 'Polarized aviator sunglasses.', 'active'),
(13, 'Evening Gown', 'Elegant silk evening dress for special occasions.', 'active'),
(14, 'Designer Handbag', 'Premium leather tote bag.', 'active'),
(15, 'Diamond Necklace', '18k gold necklace with diamond pendant.', 'active'),
(16, 'High Heel Stilettos', 'Classic black leather heels.', 'active'),
(17, 'Summer Floral Dress', 'Lightweight and stylish summer wear.', 'active'),
(18, 'Rose Gold Watch', 'Elegant women luxury watch.', 'active'),
(19, 'Ergonomic Office Chair', 'Premium mesh chair with lumbar support.', 'active'),
(20, 'Standing Desk Pro', 'Motorized height adjustable desk.', 'active'),
(21, 'Smart Coffee Maker', 'Wifi-enabled automatic coffee machine.', 'active'),
(22, 'Ultra-Wide Monitor', '34-inch curved productivity monitor.', 'active'),
(23, 'Mechanical Keyboard', 'Wireless tactile mechanical keyboard.', 'active'),
(24, 'Modern Table Lamp', 'Dimmable LED lamp with wireless charging.', 'active');

INSERT INTO ProductCategories (product_id, category_id) VALUES
(1,1), (2,1), (3,1), (4,1), (5,1), (6,1),
(7,2), (8,2), (9,2), (10,2), (11,2), (12,2),
(13,3), (14,3), (15,3), (16,3), (17,3), (18,3),
(19,4), (20,4), (21,4), (22,4), (23,4), (24,4);

INSERT INTO ProductVariants (variant_id, product_id, price, stock, variant_key) VALUES
(1, 1, 999.00, 50, 'Color: Black'),
(2, 2, 1499.00, 30, 'Color: Silver'),
(3, 3, 349.00, 100, 'Color: Matte Black'),
(4, 4, 299.00, 80, 'Size: 44mm'),
(5, 5, 199.00, 150, 'Color: White'),
(6, 6, 399.00, 60, 'Color: Black'),
(7, 7, 599.00, 20, 'Size: L'),
(8, 8, 249.00, 40, 'Size: 10'),
(9, 9, 899.00, 15, 'Color: Silver'),
(10, 10, 89.00, 100, 'Color: Red'),
(11, 11, 199.00, 50, 'Size: M'),
(12, 12, 159.00, 70, 'Color: Gold'),
(13, 13, 799.00, 10, 'Size: S'),
(14, 14, 1299.00, 25, 'Color: Black'),
(15, 15, 2499.00, 5, 'Material: Gold'),
(16, 16, 349.00, 35, 'Size: 7'),
(17, 17, 129.00, 80, 'Size: M'),
(18, 18, 499.00, 40, 'Color: Rose Gold'),
(19, 19, 499.00, 100, 'Color: Black'),
(20, 20, 699.00, 50, 'Size: 60-inch'),
(21, 21, 249.00, 75, 'Color: Steel'),
(22, 22, 899.00, 30, 'Size: 34-inch'),
(23, 23, 149.00, 120, 'Switch: Brown'),
(24, 24, 89.00, 200, 'Color: White');

INSERT INTO ProductImages (product_id, image_url, is_default) VALUES
(1, '/assets/img/cat_electronics.png', 1),
(2, '/assets/img/hero_1.png', 1),
(3, '/assets/img/headphones.png', 1),
(4, '/assets/img/hero_3.png', 1),
(5, '/assets/img/hero_4.png', 1),
(6, '/assets/img/cat_electronics.png', 1),
(7, '/assets/img/cat_mens_fashion.png', 1),
(8, '/assets/img/hero_2.png', 1),
(9, '/assets/img/hero_1.png', 1),
(10, '/assets/img/cat_mens_fashion.png', 1),
(11, '/assets/img/cat_mens_fashion.png', 1),
(12, '/assets/img/hero_1.png', 1),
(13, '/assets/img/cat_womens_fashion.png', 1),
(14, '/assets/img/hero_2.png', 1),
(15, '/assets/img/cat_womens_fashion.png', 1),
(16, '/assets/img/cat_womens_fashion.png', 1),
(17, '/assets/img/cat_womens_fashion.png', 1),
(18, '/assets/img/hero_1.png', 1),
(19, '/assets/img/chair.png', 1),
(20, '/assets/img/desk.png', 1),
(21, '/assets/img/coffeemaker.png', 1),
(22, '/assets/img/monitor.png', 1),
(23, '/assets/img/keyboard.png', 1),
(24, '/assets/img/cat_home_office.png', 1);
