# Golden Lotus Burmese Desserts (PHP + MySQL)

## Setup (XAMPP)
1. Start Apache + MySQL in XAMPP.
2. Open phpMyAdmin and import `sql/database.sql`.
3. Update DB credentials in `config/db.php` if needed.
4. Place the project folder in `xamppfiles/htdocs` and open:
   `http://localhost/burmese-desserts/`
5. Create the admin account by visiting:
   `http://localhost/burmese-desserts/admin/seed_admin.php`
 
## If You Already Imported The DB
Run this in phpMyAdmin SQL tab to add delivery fields:
```sql
ALTER TABLE orders
  ADD COLUMN delivery_method VARCHAR(20) NOT NULL DEFAULT 'pickup',
  ADD COLUMN delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  ADD COLUMN status VARCHAR(40) NOT NULL DEFAULT 'Preparing',
  ADD COLUMN public_token VARCHAR(40) NOT NULL;
```
Then run:
```sql
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

```

## Pages
- Home: `index.php`
- Shop: `shop.php`
- Product: `product.php?id=1`
- Cart: `cart.php`
- Checkout: `checkout.php`
- About: `about.php`
- Contact: `contact.php`

## Notes
- Cart is session-based.
- Orders are stored in the `orders` and `order_items` tables.
