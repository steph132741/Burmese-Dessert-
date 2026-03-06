CREATE DATABASE IF NOT EXISTS burmese_desserts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE burmese_desserts;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  short_description VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  stock INT DEFAULT 50,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  address VARCHAR(255) NOT NULL,
  city VARCHAR(80) NOT NULL,
  note TEXT,
  delivery_method VARCHAR(20) NOT NULL DEFAULT 'pickup',
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  status VARCHAR(40) NOT NULL DEFAULT 'Preparing',
  public_token VARCHAR(40) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  subject VARCHAR(120) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(120) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO products (name, slug, short_description, description, price, image, is_featured) VALUES
('Mont Let Saung', 'mont-let-saung', 'Coconut milk, agar noodles, and pandan jelly over shaved ice.', 'A refreshing Burmese dessert layered with coconut milk, pandan jelly, and delicate agar noodles. Served chilled and lightly sweetened.', 2500, 'assets/img/mont-let-saung.svg', 1),
('Shwe Gyi Mont', 'shwe-gyi-mont', 'Golden semolina cake with coconut and jaggery.', 'Traditional Shwe Gyi Mont, a soft semolina cake enriched with coconut and palm sugar, finished with toasted sesame.', 3200, 'assets/img/shwe-gyi-mont.svg', 1),
('Mont Lone Yae Paw', 'mont-lone-yae-paw', 'Glutinous rice balls with molten jaggery.', 'Glutinous rice dumplings with liquid palm sugar centers, rolled in fragrant coconut for a warm, gooey bite.', 2800, 'assets/img/mont-lone-yae-paw.svg', 1),
('Mont Si Kyet', 'mont-si-kyet', 'Sticky rice with coconut and roasted peanuts.', 'A rich, chewy dessert made with sticky rice, coconut cream, and crushed roasted peanuts.', 2200, 'assets/img/mont-si-kyet.svg', 0),
('Mont Kywe Thee', 'mont-kywe-thee', 'Crispy fritters with palm sugar glaze.', 'Golden fritters tossed in palm sugar syrup and sprinkled with sesame for a crunchy finish.', 2600, 'assets/img/mont-kywe-thee.svg', 0),
('Mont Hin Gar Mont', 'mont-hin-gar-mont', 'Rice cake with coconut and jaggery drizzle.', 'Soft rice cakes topped with coconut cream and a slow-cooked jaggery drizzle for a balanced sweetness.', 2400, 'assets/img/mont-hin-gar-mont.svg', 0),
('Mont Lon Ma Yei', 'mont-lon-ma-yei', 'Steamed coconut treats with banana leaf aroma.', 'Steamed coconut treats wrapped in banana leaves, fragrant and gently sweet.', 2300, 'assets/img/mont-lon-ma-yei.svg', 0),
('Semolina Halwa', 'semolina-halwa', 'Spiced semolina pudding with cashew.', 'Creamy semolina halwa cooked with cardamom, ghee, and cashews for a comforting dessert.', 3000, 'assets/img/semolina-halwa.svg', 0);
