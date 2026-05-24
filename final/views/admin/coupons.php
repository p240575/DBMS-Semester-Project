<?php require 'views/layouts/admin_header.php'; ?>
<style>
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #1e293b; border-radius: 8px; overflow: hidden; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #334155; color: #cbd5e1; }
th { background: #0f172a; font-weight: 600; }
.form-inline { display: flex; gap: 1rem; margin-bottom: 1rem; background: #1e293b; padding: 1rem; border-radius: 8px; align-items: flex-end; }
</style>

<div class="admin-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Coupons / Discounts</h2>
    </div>

    <form method="POST" action="/admin/add_coupon" class="form-inline">
        <div class="form-group" style="margin: 0;">
            <label>Coupon Code</label>
            <input type="text" name="code" class="form-control" placeholder="e.g. SUMMER20" required>
        </div>
        <div class="form-group" style="margin: 0;">
            <label>Discount Percentage (%)</label>
            <input type="number" step="0.01" name="discount_percent" class="form-control" placeholder="20" required>
        </div>
        <button type="submit" class="btn btn-primary" style="height: 42px;">Add Coupon</button>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Discount (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['coupons'] as $coupon): ?>
                <tr>
                    <td><?= $coupon['coupon_id'] ?></td>
                    <td style="font-weight: bold; color: #f59e0b;"><?= htmlspecialchars($coupon['code']) ?></td>
                    <td><?= number_format($coupon['discount_percent'], 2) ?>%</td>
                    <td>
                        <form method="POST" action="/admin/delete_coupon/<?= $coupon['coupon_id'] ?>" style="display:inline;">
                            <button type="submit" class="btn btn-sm" style="background:#ef4444; border:none; color:white; padding: 5px 10px; border-radius:4px;" onclick="return confirm('Delete this coupon?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layouts/admin_footer.php'; ?>
