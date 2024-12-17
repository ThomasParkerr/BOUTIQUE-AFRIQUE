CREATE DATABASE AfriqueBotique;
USE AfriqueBotique;

-- Table for Products (should be created first since other tables reference it)
CREATE TABLE AfriqueBotique_Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    product_type VARCHAR(255) NOT NULL,
    item_type VARCHAR(255) NOT NULL,
    discounted_price DECIMAL(10, 2) DEFAULT NULL  
);

-- Table for Users (for Signup and Login)
CREATE TABLE AfriqueBotique_Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    address TEXT,
    phone_number VARCHAR(15),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Cart (for Cart functionality)
CREATE TABLE AfriqueBotique_Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    checkout ENUM('Yes', 'No') DEFAULT 'No', -- 'Yes' if paid, 'No' otherwise
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES AfriqueBotique_Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES AfriqueBotique_Products(id) ON DELETE CASCADE
);

-- Table for Wishlist (for Wishlist functionality)
CREATE TABLE AfriqueBotique_Wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    checkout ENUM('Yes', 'No') DEFAULT 'No', -- 'Yes' if moved to cart/paid, 'No' otherwise
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES AfriqueBotique_Users(user_id),
    FOREIGN KEY (product_id) REFERENCES AfriqueBotique_Products(id)
);

CREATE TABLE AfriqueBotique_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,        -- Unique identifier for each payment record
    user_id INT NOT NULL,                     -- User ID associated with the payment (foreign key)
    full_name VARCHAR(255) NOT NULL,           -- Full name of the customer
    address VARCHAR(255) NOT NULL,             -- Address of the customer
    city VARCHAR(100) NOT NULL,                -- City of the customer
    zip_code VARCHAR(20) NOT NULL,            -- Zip code of the customer
    country VARCHAR(100) NOT NULL,            -- Country of the customer
    payment_method ENUM('Credit Card', 'Bank Transfer') NOT NULL, -- Payment method selected
    card_number VARCHAR(19),                  -- Card number (only if Credit Card is selected)
    holder_name VARCHAR(255),                 -- Cardholder's name (only if Credit Card is selected)
    bank_name VARCHAR(255),                   -- Bank name (only if Bank Transfer is selected)
    account_number VARCHAR(255),              -- Bank account number (only if Bank Transfer is selected)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Timestamp when the record is created
    FOREIGN KEY (user_id) REFERENCES AfriqueBotique_Users(user_id) -- Foreign key constraint linking to the 'users' table
);



-- Table for Gallery (for displaying product images)
CREATE TABLE AfriqueBotique_Gallery (
    gallery_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES AfriqueBotique_Products(id)
);

CREATE TABLE AfriqueBotique_Sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    discount_percentage DECIMAL(5, 2) NOT NULL CHECK (discount_percentage BETWEEN 0 AND 100),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES AfriqueBotique_Products(id) ON DELETE CASCADE
);

CREATE TABLE afriquebotique_loyaltypoints (
    user_id INT NOT NULL,                   -- User's ID (foreign key from users table)
    points INT NOT NULL DEFAULT 0,          -- Loyalty points the user has
    tier VARCHAR(50) NOT NULL DEFAULT 'Bronze', -- The loyalty tier of the user
    PRIMARY KEY (user_id),                  -- User ID is the primary key
    FOREIGN KEY (user_id) REFERENCES AfriqueBotique_Users(user_id) -- Assuming there is a users table with user_id as primary key
);

-- Table for Admins (to manage access to the admin page)
CREATE TABLE AfriqueBotique_Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each admin
    username VARCHAR(50) NOT NULL UNIQUE,    -- Admin username (must be unique)
    password VARCHAR(255) NOT NULL,          -- Admin password (hashed for security)
    email VARCHAR(100) NOT NULL,             -- Admin email for recovery or contact
    full_name VARCHAR(100),                  -- Full name of the admin
    role ENUM('SuperAdmin', 'Admin') DEFAULT 'Admin', -- Role of the admin
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp of admin creation
);


INSERT INTO AfriqueBotique_Admin (username, password, email, full_name, role) 
VALUES ('admin', 'IAMADMIN123!', 'admin@gmail.com', 'First Admin', 'SuperAdmin');


INSERT INTO AfriqueBotique_Products (
    name, 
    description, 
    image_url, 
    price, 
    stock_quantity, 
    product_type, 
    item_type
) VALUES 
('African Jewelry', "Stylish African-inspired jewelry piece.", '../assets/images/AfricaJewelry.jpg', 25.99, 50, 'Women', 'Jewelry'),
('African Print Bag', "Vibrant African print handbag.", '../assets/images/African Print Bag.png', 45.50, 30, 'Women', 'Bags'),
('Blue Yellow Green Ankara Dress', "Elegant Ankara print dress.", '../assets/images/ankaradress.png', 55.99, 25, 'Women', 'Ankara'),
('Blue and Red Ankara dress', "Stylish and flattering African-inspired dress.", '../assets/images/cutedress.jpg', 60.00, 30, 'Women', 'Ankara'),
('Black and yellow Dashiki', "Classic African dashiki pattern.", '../assets/images/dashiki.jpg', 35.50, 40, 'Women', 'Dashiki'),
('Simple Black Headwrap', "Colorful patterned head wrap.", '../assets/images/headwrap5.jpg', 12.99, 50, 'Women', 'Headwrap'),
('Red Bold African Necklace', "Vibrant African print clothing.", '../assets/images/dripdrip.jpg', 22.00, 35, 'Women', 'Jewelry'),
('Bright Yellow Brown Ankara Dress', "Model wearing African-inspired fashion.", '../assets/images/fashionwoman.jpg', 50.00, 20, 'Women', 'Ankara'),
('Red and Orange Bold African Jewelry', "Assorted African-inspired jewelry pieces.", '../assets/images/jewelry.jpg', 18.50, 40, 'Women', 'Jewelry'),
('Dark and Bright Jewelry', "Additional African jewelry designs.", '../assets/images/jewelryyyyy.jpg', 19.99, 25, 'Women', 'Jewelry'),
('Black Kaftan', "Traditional African kaftan garment.", '../assets/images/kaftan.png', 70.00, 15, 'Men', 'Kaftan'),
('Yellow Kente', "African-print t-shirt design.", '../assets/images/kentee.jpg', 25.99, 30, 'Women', 'Kente'),
('Kente skirt', "Patterned African-style skirt.", '../assets/images/kenteskirt.jpg', 40.00, 20, 'Women', 'Kente'),
('Red and Blue Ankara Long Dress', "Flowing African-style maxi dress.", '../assets/images/longdress.jpg', 65.00, 18, 'Women', 'Ankara'),
('African duffel bag', "Mens African-inspired fashion look.", '../assets/images/menafricanbag.jpg', 50.00, 25, 'Men', 'Bags'),
('Fanged Wooden necklace', "Decorative African-style mens necklace.", '../assets/images/mennecklace.jpg', 15.50, 40, 'Men', 'Jewelry'),
('Brown Kente', "Further mens African-style clothing.", '../assets/images/menkente4.jpg', 48.00, 20, 'Men', 'Kente'),
('Blue dashiki', "Mens dashiki shirt design.", '../assets/images/mendashiki1.jpg', 35.50, 40, 'Men', 'Dashiki'),
('Red dashiki', "Alternate mens dashiki design.", '../assets/images/mendashiki2.jpg', 36.00, 30, 'Men', 'Dashiki'),
('African styled duffel bag', "Further mens African fashion item.", '../assets/images/menafricanbag3.jpg', 60.00, 20, 'Men', 'Bags'),
('Yellow duffel bag', "Additional mens African fashion item.", '../assets/images/menafricanbag2.jpg', 55.00, 30, 'Men', 'Bags'),
('Black dashiki', "Additional mens dashiki style.", '../assets/images/mendashiki3.jpg', 38.00, 35, 'Men', 'Dashiki'),
('Grey black dashiki', "Further mens dashiki variation.", '../assets/images/mendashiki4.jpg', 37.00, 30, 'Men', 'Dashiki'),
('White dashiki', "More mens dashiki fashion.", '../assets/images/mendashiki5.jpg', 39.00, 25, 'Men', 'Dashiki'),
('Blue kente', "African-inspired mens attire.", '../assets/images/menkente1.jpg', 42.00, 20, 'Men', 'Kente'),
('Multi colored kente', "Alternate mens African fashion.", '../assets/images/menkente2.jpg', 44.00, 25, 'Men', 'Kente'),
('White dominant multi colored kente', "Additional mens African look.", '../assets/images/menkente3.jpg', 45.00, 30, 'Men', 'Kente'),
('Brown Kente', "Further mens African-style clothing.", '../assets/images/menkente4.jpg', 48.00, 20, 'Men', 'Kente'),
('Red Yellow Green Kente', "More mens African-inspired fashion.", '../assets/images/menkente5.jpg', 50.00, 18, 'Men', 'Kente'),
('Beaded Wooden Necklace', "Alternate mens African necklace design.", '../assets/images/mennecklace2.jpg', 14.99, 35, 'Men', 'Jewelry'),
('Wawa Necklace', "Additional mens African jewelry piece.", '../assets/images/mennecklace3.jpg', 16.00, 30, 'Men', 'Jewelry'),
('Men Dashiki', "Mens dashiki clothing item.", '../assets/images/mensdashiki.jpg', 35.50, 40, 'Men', 'Dashiki'),
('Metal Facemask Rings', "Assorted African-inspired finger rings.", '../assets/images/rings.png', 12.00, 50, 'Men', 'Jewelry'),
('Cowry shell Rings', "Further African ring designs.", '../assets/images/ringss.png', 13.00, 45, 'Men', 'Jewelry'),
('Adinkra symbols rings', "Additional African ring styles.", '../assets/images/ringssss.png', 14.00, 40, 'Men', 'Jewelry'),
('White and Black Smock Styled Ankara', "Traditional African smock garment.", '../assets/images/smock.png', 60.00, 20, 'Women', 'Ankara'),
('Red Women African Bag', "Stylish African print handbag for women.", '../assets/images/womenafrican bag.jpg', 45.00, 20, 'Women', 'Bags'),
('Purple Women African Bag', "Alternate African womens bag design.", '../assets/images/purplebag.jpg', 50.00, 25, 'Women', 'Bags'),
('Red and Brown Women African Bag', "Additional African womens bag style.", '../assets/images/redbag.jpg', 48.00, 20, 'Women', 'Bags'),
('Blue Women African Bag', "Further African womens bag option.", '../assets/images/bluebag.jpg', 52.00, 15, 'Women', 'Bags'),
('African Patterned Ankara', "Womens Ankara print dress or outfit.", '../assets/images/womenankara1.jpg', 55.00, 25, 'Women', 'Ankara'),
('Blue and Red Ankara', "Alternate Ankara-inspired womens fashion.", '../assets/images/womenankara2.jpg', 56.00, 20, 'Women', 'Ankara'),
('Light Brown and Green', "Additional Ankara-style womens clothing.", '../assets/images/womenankara3.jpg', 57.00, 18, 'Women', 'Ankara'),
('Red and White Ankara', "Further Ankara-inspired womens attire.", '../assets/images/womenankara4.jpg', 58.00, 16, 'Women', 'Ankara'),
('Orange Dashiki', "Womens dashiki dress or blouse.", '../assets/images/womendashiki1.jpg', 35.00, 22, 'Women', 'Dashiki'),
('Pink Dashiki', "Alternate womens dashiki fashion.", '../assets/images/womendashiki2.jpg', 36.00, 20, 'Women', 'Dashiki'),
('Red Dashiki', "Womens dashiki dress or blouse.", '../assets/images/womendashiki3.jpg', 37.00, 18, 'Women', 'Dashiki'),
('Purple Dashiki', "Alternate womens dashiki fashion.", '../assets/images/womendashiki4.jpg', 38.00, 15, 'Women', 'Dashiki'),
('Black Dashiki', "Womens dashiki dress or blouse.", '../assets/images/womendashiki5.jpg', 39.00, 12, 'Women', 'Dashiki'),
('Short multi-colored kente', "Additional Kente-style womens clothing.", '../assets/images/womenkente2.jpg', 50.00, 15, 'Women', 'Kente'),
('Blue and Green necklace', "Womens decorative African-style necklace.", '../assets/images/womennecklace2..jpg', 20.00, 30, 'Women', 'Jewelry'),
('Brown headwrap', "Vibrant patterned head wrap.", '../assets/images/headwrap1.jpg', 15.99, 50, 'Women', 'Headwrap'),
('Blue headwrap', "Alternate African head wrap design.", '../assets/images/headwrap2.jpg', 17.00, 45, 'Women', 'Headwrap'),
('Dark brown headwrap', "Further head wrap style.", '../assets/images/headwrap4.jpg', 16.50, 48, 'Women', 'Headwrap'),
('Dark Green Headwrap', "Additional African-inspired head wrap.", '../assets/images/headwrap3.jpg', 18.00, 40, 'Women', 'Headwrap'),
('Red thick bead necklace', "Decorative African necklace for women.", '../assets/images/womennecklace.jpg', 23.99, 50, 'Women', 'Jewelry'),
('Short Pattern Kente', "Womens Kente cloth dress or skirt.", '../assets/images/womenkente1.jpg', 29.99, 100, 'Women', 'Kente'),
('Purple Kente', "Further Kente-inspired womens attire.", '../assets/images/womenkente3.jpg', 39.99, 75, 'Women', 'Kente'),
('Blue Green Yellow Kente', "Alternate Kente cloth womens fashion.", '../assets/images/womenkente4.jpg', 25.99, 50, 'Women', 'Kente'),
('Traditional necklace', "Alternate African necklace design for women.", '../assets/images/womennecklace3.jpg', 24.99, 110, 'Women', 'Jewelry'),
('Blue and Black necklace', "Additional African necklace style for women.", '../assets/images/womennecklace4.jpg', 21.99, 90, 'Women', 'Jewelry'),
('Dark Blue Ankara', "Complement the blue of the moon.", '../assets/images/maleankara1.jpg', 65.00, 25, 'Men', 'Ankara'),
('Light blue Ankara', "Light Blue for a brighter fashion.", '../assets/images/maleankara2.jpg', 66.00, 20, 'Men', 'Ankara'),
('Orange with a Hint of Black Ankara', "Bright Orange for more bold representation", '../assets/images/maleankara3.jpg', 77.00, 18, 'Men', 'Ankara'),
('Black and white Striped Ankara', "Black and White Unite in perfect Harmony", '../assets/images/maleankara4.jpg', 85.00, 16, 'Men', 'Ankara'),
('Red and Black Oriental Ankara', "Red and Black Oriental style merge with Ankara to form this masterpiece", '../assets/images/maleankara5.jpg', 45.00, 25, 'Men', 'Ankara');

INSERT INTO AfriqueBotique_Sales (product_id, discount_percentage, start_date, end_date)
VALUES
    (1, 50.00, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY)), -- 50% off for 7 days
    (55, 40.00, NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY)), -- 40% off for 5 days
    (3, 30.00, NOW(), DATE_ADD(NOW(), INTERVAL 10 DAY)), -- 30% off for 10 days
    (7, 25.00, NOW(), DATE_ADD(NOW(), INTERVAL 4 DAY)), -- 25% off for 4 days
    (11, 20.00, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY)), -- 20% off for 3 days
    (49, 15.00, NOW(), DATE_ADD(NOW(), INTERVAL 8 DAY)), -- 15% off for 8 days
    (12, 10.00, NOW(), DATE_ADD(NOW(), INTERVAL 6 DAY)), -- 10% off for 6 days
    (40, 35.00, NOW(), DATE_ADD(NOW(), INTERVAL 9 DAY)), -- 35% off for 9 days
    (35, 45.00, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY)), -- 45% off for 7 days
    (30, 50.00, NOW(), DATE_ADD(NOW(), INTERVAL 12 DAY)); 

