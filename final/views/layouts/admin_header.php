<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NexShop</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; background: #0f172a; color: white; }
        .admin-sidebar { width: 250px; background: #1e293b; padding: 2rem; border-right: 1px solid #334155; }
        .admin-sidebar a { display: block; color: #cbd5e1; text-decoration: none; padding: 0.75rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; transition: 0.2s; }
        .admin-sidebar a:hover { background: #334155; color: white; }
        .admin-sidebar h2 { color: #f59e0b; margin-bottom: 2rem; font-size: 1.5rem; }
        .admin-main { flex: 1; padding: 2rem; overflow-y: auto; }
        .admin-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #334155; }
        .admin-header-bar h1 { margin: 0; font-size: 1.8rem; }
    </style>
</head>
<body>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2>NexShop Admin</h2>
        <a href="/admin/index">📊 Dashboard</a>
        <a href="/admin/products">📦 Products</a>
        <a href="/admin/orders">🛒 Orders</a>
        <a href="/admin/returns">🔄 Returns</a>
        <a href="/admin/customers">👥 Customers</a>
        <a href="/admin/messages" style="display:flex; justify-content:space-between; align-items:center;">
            <span>💬 Messages</span>
            <?php
            try {
                require_once 'config/database.php';
                $dbSidebar = (new Database())->getConnection();
                $stmtBadge = $dbSidebar->query("SELECT COUNT(*) FROM Messages WHERE sender_role = 'customer' AND is_read = 0");
                $unread = $stmtBadge->fetchColumn();
                if ($unread > 0) echo "<span style='background:#ef4444; color:white; font-size:0.7rem; padding:1px 7px; border-radius:12px; font-weight:bold;'>$unread</span>";
            } catch(Exception $e) {}
            ?>
        </a>
        <a href="/admin/coupons">🏷 Coupons</a>
        <a href="/admin/reviews">⭐ Reviews</a>
        <a href="/auth/logout" style="color: #ef4444; margin-top: 2rem;">🚪 Logout</a>
    </div>
    <div class="admin-main">
        <div class="admin-header-bar">
            <h1>Welcome, Admin</h1>
        </div>
