# NexShop - Premium E-Commerce System

NexShop is a modern, high-end, custom-built e-commerce web application featuring a robust custom Model-View-Controller (MVC) architecture in native PHP. The application provides an elegant glassmorphic dark-theme user experience, detailed customer accounts, a dual wallet/COD payment system, a bidirectional chat messenger, real-time trigger-based inventory adjustments, and a comprehensive return and refund workflow with automated financial integrations.

---

## Key Features

### Customer Features
- **Authentication & Security:** Secure registration and multi-factor login capabilities with cryptographically hashed passwords.
- **Dynamic Cart & Checkout:** Real-time quantity adjustments directly in the cart, coupon application (`NEX10`, `SALE20`, `LUXURY50`), and automated delivery charge exemptions (Free Delivery).
- **Dual Payment Methods:** Integrated system allowing customers to pay with Cash on Delivery (COD), their local NexShop Wallet, or combine both if the wallet balance does not fully cover the total.
- **Address Management:** Multiple delivery addresses with default/active address selection toggles.
- **Reviews & Ratings:** Interactive ratings (1 to 5 stars) with user comments. Responses left by administrators are shown directly below comments.
- **Order Tracking:** Follow order status updates: `Pending` -> `Confirmed` -> `Shipped` -> `Delivered` -> `Cancelled`.
- **48-Hour Return & Refund System:**
  - Active for exactly 48 hours after receiving the product.
  - Requires descriptive justifications and product image attachments.
  - Automatically generates a local returning facility address when approved by the admin.
  - Wallet balances are automatically credited upon successful admin verification.

### Admin Dashboard Features
- **Real-Time Financial Metrics:** Displays gross revenue, total refunds issued, and net revenue. Refunds automatically deduct from the main dashboard revenue.
- **Order Cancellation & Restocking:** Admins can cancel any order with custom justification. Cancelling an order automatically refunds wallet expenditures and restores the products' inventory stocks.
- **Return Request Management:** View pending/approved/returning/inspected requests. Reject or approve returns, verify the items, and finalize refunds.
- **Product & Variant Editing:** Add and edit products, manage size/pricing variant keys, adjust stock thresholds, and upload default imagery.
- **Interactive Review Reply:** Reply directly to customer feedback. Replies propagate instantly to the product review feeds.
- **Global User Notifications:** Instantly dispatch notification alerts to individual customer profiles.

---

## Architecture & Project Structure

The project strictly follows a clean **Model-View-Controller (MVC)** architectural pattern:

```text
DB Project/
├── config/
│   └── database.php         # PDO-based MySQL connection manager
├── controllers/
│   ├── Controller.php        # Base controller with View rendering functions
│   ├── HomeController.php    # Handles landing page, static pages, and reviews
│   ├── AuthController.php    # Customer registration, logins, and logouts
│   ├── ProductsController.php# Product search, category filtering, detail view
│   ├── CartController.php    # Cart quantity increments/decrements & coupon logic
│   ├── CheckoutController.php# Direct order placement & wallet-COD split payment logic
│   ├── UserController.php    # Purchases, return claims, notifications, profile
│   └── AdminController.php   # Admin control panel, metrics, review replies, order edits
├── models/
│   ├── User.php             # Customer data manipulation and auth procedures
│   ├── Product.php          # Front-facing product queries & search mechanisms
│   └── Admin.php            # Analytics extraction, order overrides, return lifecycle
├── views/
│   ├── layouts/
│   │   ├── header.php       # Global top bar, notification counter, search field
│   │   └── footer.php       # Bottom footer layout and global styles
│   ├── auth/                # Login and register pages
│   ├── cart/                # Cart listing, coupon input panel
│   ├── checkout/            # Checkout form & address selector
│   ├── products/            # Search results and detail view pages
│   ├── user/                # Profile, order logs, chat, notification logs
│   ├── admin/               # Dashboard pages, review managers, return lists
│   └── home.php             # Interactive luxury store landing page
├── assets/
│   ├── css/
│   │   ├── style.css        # Main glassmorphic styles and dark-mode system
│   │   └── forms.css        # Form elements styling
│   └── img/                 # Interface imagery and product assets
├── index.php                # Core application router entry point
├── router.php               # Local development server routing utility
├── database.sql             # Unified final database schema file
├── seed.sql                 # Comprehensive database seed file
├── seeder.php               # Helper PHP script to seed mock products
├── seed_reviews.php         # Helper PHP script to seed mock reviews
└── credentials.php          # Utility script to view/reset customer passwords
```

---

## Database Schema Details

The database is built on **MySQL/MariaDB** and consists of **18 integrated tables** and **5 triggers** for automation.

### Core Tables
1. **`admins`**: Administrative accounts for dashboard access.
2. **`users`**: Customer registry, complete with dynamic `wallet_balance` columns.
3. **`categories`**: Product catalog categorizations.
4. **`products`**: General product catalog entries.
5. **`productvariants`**: Product size, color, or style variations mapped to individual pricing and stock columns.
6. **`productcategories`**: Many-to-many relationship mapping categories to products.
7. **`productimages`** & **`variantimages`**: Default and secondary product display image links.
8. **`addresses`**: Saved client shipping locations.
9. **`coupons`**: Discount coupons linked to order processes.
10. **`cart`** & **`cartitems`**: In-database shopping cart storage.
11. **`orders`**: Transaction records containing status, payments, and delivery agents.
12. **`orderitems`**: Specific items ordered, preserving transaction-time prices.
13. **`orderaddresses`**: Snapshotted address structure for each specific order to preserve history.
14. **`returnrequests`**: Lifecycles of product returns (image attachments, status, refund totals, warehouse return addresses).
15. **`reviews`**: Product ratings, comments, and admin responses.
16. **`notifications`**: User-specific dashboard notifications.
17. **`messages`**: Customer-admin chat records.
18. **`paymentmethods`**, **`accounts`** & **`payments`**: Underlying transaction registers.

### Triggers
- **`insert_orderitems`**: Automatically increments the total order amount as items are added to an order.
- **`update_orderitems`**: Updates the total order amount automatically when items are updated.
- **`delete_orderitems`**: Decrements the total order amount when items are removed.
- **`order_confirm`**: Decreases the variant stock level immediately when an order transitions to `confirmed`.
- **`order_cancel`**: Automatically returns stock to inventory if a confirmed order is transitioned to `cancelled`.

---

## Installation & Setup

### Prerequisites
1. **XAMPP / WampServer / MAMP** (Apache with PHP 8.0+ and MySQL/MariaDB).
2. Rewrite engine (`mod_rewrite`) enabled on Apache.

### Database Setup
1. Open phpMyAdmin or your preferred SQL editor.
2. Import the unified database schema:
   ```sql
   SOURCE C:/path/to/project/database.sql;
   ```
3. Import the sample dataset to populate categories, products, variants, and default coupons:
   ```sql
   SOURCE C:/path/to/project/seed.sql;
   ```
4. Seed mock reviews and products by calling the helper scripts:
   ```bash
   php seeder.php
   php seed_reviews.php
   ```
5. View and reset customer credentials by visiting:
   `http://localhost:8000/credentials.php` (or your local equivalent).

### Application Launch
- **Built-in PHP Server (Recommended):** Run this command inside the project directory:
  ```bash
  php -S localhost:8000 router.php
  ```
  Visit the application in your browser at `http://localhost:8000`.

### Credentials
- **Admin Account:**
  - **Email/Username:** `admin` or `admin@gmail.com`
  - **Password:** `admin123`
- **Customer Registration:** Use the login/register screen to dynamically register standard users, which automatically sets up their respective shopping carts.
