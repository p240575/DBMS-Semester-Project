<?php require 'views/layouts/admin_header.php'; ?>
<style>
.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: #1e293b;
    padding: 2rem;
    border-radius: 12px;
    border: 1px solid #334155;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: block;
    color: inherit;
}
.stat-card:hover {
    border-color: #f59e0b;
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(245,158,11,0.15);
}
.stat-card h3 { font-size: 0.95rem; color: #94a3b8; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
.stat-card .value { font-size: 2.8rem; font-weight: bold; color: #f59e0b; line-height: 1; }
.stat-card .hint { font-size: 0.75rem; color: #475569; margin-top: 0.5rem; }
.stat-card.danger .value { color: #ef4444; }
.stat-card.success .value { color: #10b981; }
.stat-card.blue .value { color: #3b82f6; }
</style>

<div class="admin-dashboard">
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.8rem;">Welcome Admin 👋</h2>
        <p style="color: #64748b; margin-top: 0.5rem;">Here's an overview of everything going on.</p>
    </div>

    <div class="stat-cards">
        <a href="/admin/orders?filter=all" class="stat-card">
            <h3>Total Orders</h3>
            <div class="value"><?= $data['stats']['total_orders'] ?></div>
            <div class="hint">Click to view all</div>
        </a>
        <a href="/admin/orders?filter=pending" class="stat-card">
            <h3>Pending Orders</h3>
            <div class="value"><?= $data['stats']['pending_orders'] ?? 0 ?></div>
            <div class="hint">Awaiting dispatch</div>
        </a>
        <a href="/admin/orders?filter=completed" class="stat-card success">
            <h3>Completed Orders</h3>
            <div class="value"><?= $data['stats']['completed_orders'] ?? 0 ?></div>
            <div class="hint">Delivered</div>
        </a>
        <a href="/admin/products?filter=lowstock" class="stat-card danger">
            <h3>Low Stock Products</h3>
            <div class="value"><?= $data['stats']['low_stock_products'] ?? 0 ?></div>
            <div class="hint">≤ 5 items left — click to view</div>
        </a>
        <a href="/admin/customers" class="stat-card blue">
            <h3>Total Customers</h3>
            <div class="value"><?= $data['stats']['total_customers'] ?></div>
            <div class="hint">Registered users</div>
        </a>
        <a href="/admin/products" class="stat-card">
            <h3>Total Products</h3>
            <div class="value"><?= $data['stats']['total_products'] ?></div>
            <div class="hint">In catalog</div>
        </a>
        <a href="/admin/revenue" class="stat-card success">
            <h3>Total Revenue</h3>
            <div class="value" style="font-size:1.8rem;">Rs <?= number_format($data['stats']['total_revenue'], 0) ?></div>
            <div class="hint">Click to see full breakdown</div>
        </a>
        <a href="/admin/coupons" class="stat-card">
            <h3>Total Coupons</h3>
            <div class="value"><?= $data['stats']['total_coupons'] ?? 0 ?></div>
            <div class="hint">Active discount codes</div>
        </a>
        <a href="/admin/returns" class="stat-card <?= ($data['stats']['pending_returns']??0) > 0 ? 'danger' : '' ?>">
            <h3>Return Requests</h3>
            <div class="value"><?= $data['stats']['pending_returns'] ?? 0 ?></div>
            <div class="hint">Pending review</div>
        </a>
    </div>
</div>

<?php require 'views/layouts/admin_footer.php'; ?>
