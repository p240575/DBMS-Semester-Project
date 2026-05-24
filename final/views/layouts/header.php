<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexShop - E-Commerce</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
</head>
<body>
    <div id="scroll-bar" class="scroll-progress"></div>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/home" class="logo"><img src="https://api.iconify.design/mdi/diamond-stone.svg?color=%23f59e0b" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 10px;">Nex<span>Shop</span></a>
            
            <form action="/products/search" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search products..." class="search-input">
                <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1rem;">Search</button>
            </form>

            <div class="nav-links">
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/admin">📊 Dashboard</a>
                    <a href="/auth/logout" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="/home">Home</a>
                    <a href="/products">Products</a>
                    <a href="/home/about">Why NexShop</a>
                    <?php if(isset($_SESSION['user_id'])): 
                        $cartCount = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $qty) { $cartCount += $qty; }
                        }
                        // Unread messages from admin
                        $unreadMsgCount = 0;
                        $unreadNotifCount = 0;
                        try {
                            require_once 'config/database.php';
                            $dbNav = (new Database())->getConnection();
                            $stmtUnread = $dbNav->prepare("SELECT COUNT(*) FROM Messages WHERE user_id = ? AND sender_role = 'admin' AND is_read = 0");
                            $stmtUnread->execute([$_SESSION['user_id']]);
                            $unreadMsgCount = $stmtUnread->fetchColumn();
                            // Only count UNREAD notifications (is_read = 0)
                            $stmtNotif = $dbNav->prepare("SELECT COUNT(*) FROM Notifications WHERE user_id = ? AND is_read = 0");
                            $stmtNotif->execute([$_SESSION['user_id']]);
                            $unreadNotifCount = $stmtNotif->fetchColumn();
                        } catch(Exception $e) {}
                    ?>
                        <a href="/user/purchases">Orders</a>
                        <a href="/user/returns" style="position:relative;">Returns</a>
                        <a href="/cart">Cart <span class="cart-count" style="background: var(--accent-primary); color: #0f172a; padding: 2px 8px; border-radius: 12px; font-weight: bold; margin-left: 5px;"><?= $cartCount ?></span></a>
                        <a href="/user/notifications" style="position:relative;">🔔
                            <?php if($unreadNotifCount > 0): ?>
                                <span style="position:absolute; top:-6px; right:-10px; background:#ef4444; color:white; font-size:0.65rem; min-width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;"><?= $unreadNotifCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/user/messages" style="position:relative;">💬
                            <?php if($unreadMsgCount > 0): ?>
                                <span style="position:absolute; top:-6px; right:-10px; background:#3b82f6; color:white; font-size:0.65rem; min-width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;"><?= $unreadMsgCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/user/profile" style="color: var(--accent-secondary); font-weight: 600;"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                        <a href="/auth/logout" class="btn btn-outline">Logout</a>
                    <?php else: ?>
                        <a href="/auth/login" class="btn btn-primary">Login / Register</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
