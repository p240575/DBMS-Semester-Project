<?php require 'views/layouts/admin_header.php'; ?>
<style>
.tab-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
.tab-btn { padding: 0.6rem 1.5rem; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #cbd5e1; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.tab-btn.active, .tab-btn:hover { background: #f59e0b; color: #0f172a; border-color: #f59e0b; }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #1e293b; border-radius: 8px; overflow: hidden; }
th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; }
th { background: #0f172a; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; }
.status-badge { padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
.status-pending { background: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid #f59e0b; }
.status-confirmed { background: rgba(59,130,246,0.2); color: #60a5fa; border: 1px solid #3b82f6; }
.status-shipped { background: rgba(139,92,246,0.2); color: #a78bfa; border: 1px solid #8b5cf6; }
.status-delivered { background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid #10b981; }
.status-cancelled { background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid #ef4444; }
.lock-info { color: #f59e0b; font-size: 0.8rem; font-style: italic; }
</style>

<?php
$filter = $_GET['filter'] ?? 'all';
$orders = $data['orders'];

// Separate pending and completed
$pendingOrders = array_filter($orders, fn($o) => in_array($o['order_status'], ['pending', 'confirmed', 'shipped']));
$completedOrders = array_filter($orders, fn($o) => $o['order_status'] === 'delivered' || $o['order_status'] === 'cancelled');

if ($filter === 'pending') $displayOrders = $pendingOrders;
elseif ($filter === 'completed') $displayOrders = $completedOrders;
else $displayOrders = $orders;
?>

<div class="admin-dashboard">
    <div style="margin-bottom: 1.5rem;">
        <h2>Manage Orders</h2>
        <p style="color: #64748b; margin-top: 0.3rem;">
            Total: <?= count($orders) ?> | 
            Pending: <?= count($pendingOrders) ?> | 
            Completed: <?= count($completedOrders) ?>
        </p>
    </div>

    <div class="tab-bar">
        <a href="/admin/orders?filter=all" class="tab-btn <?= $filter === 'all' ? 'active' : '' ?>">All Orders</a>
        <a href="/admin/orders?filter=pending" class="tab-btn <?= $filter === 'pending' ? 'active' : '' ?>">Pending / Active</a>
        <a href="/admin/orders?filter=completed" class="tab-btn <?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach($displayOrders as $order):
                    $orderTime = strtotime($order['created_at']);
                    $oneHourPassed = (time() - $orderTime) >= 3600;
                    $canDispatch = $oneHourPassed && in_array($order['order_status'], ['pending', 'confirmed']);
                    $minutesLeft = max(0, ceil((3600 - (time() - $orderTime)) / 60));
                ?>
                <tr>
                    <td style="color:#64748b;"><?= $i++ ?></td>
                    <td style="font-weight:bold; color:#f59e0b;">#<?= $order['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                    <td style="color:#64748b;"><?= htmlspecialchars($order['email']) ?></td>
                    <td style="font-weight:bold;">Rs <?= number_format($order['total_amount'], 2) ?></td>
                    <td><span style="color:#94a3b8;font-size:.83rem;"><?= htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery') ?></span></td>
                    <td><span class="status-badge status-<?= strtolower($order['order_status']) ?>"><?= ucfirst($order['order_status']) ?></span></td>
                    <td style="color:#64748b;"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <?php if(in_array($order['order_status'], ['pending', 'confirmed'])): ?>
                            <?php if($canDispatch): ?>
                                <form method="POST" action="/admin/dispatch_order/<?= $order['order_id'] ?>" style="display:flex; gap:0.4rem; align-items:center; flex-wrap:wrap;">
                                    <input type="text" name="delivery_agent" placeholder="Agent name" required 
                                           style="padding:5px 8px; border-radius:6px; border:1px solid #334155; background:#0f172a; color:white; width:130px; font-size:0.85rem;">
                                    <button type="submit" style="background:#8b5cf6; border:none; color:white; padding:5px 12px; border-radius:6px; cursor:pointer; font-size:0.85rem;"
                                             onclick="return confirm('Mark as dispatched?');">Dispatch</button>
                                </form>
                            <?php else: ?>
                                <span class="lock-info">🔒 Locked — <?= $minutesLeft ?>m left<br><small>1hr cancellation window</small></span>
                            <?php endif; ?>
                        <?php elseif($order['order_status'] === 'shipped'): ?>
                            <span style="color:#a78bfa; font-size:0.85rem;">📦 Shipped</span>
                            <?php if(!empty($order['delivery_agent'])): ?>
                                <br><small style="color:#64748b;">by <?= htmlspecialchars($order['delivery_agent']) ?></small>
                            <?php endif; ?>
                        <?php elseif($order['order_status'] === 'delivered'): ?>
                            <span style="color:#34d399; font-size:0.85rem;">✅ Delivered</span>
                        <?php elseif($order['order_status'] === 'cancelled'): ?>
                            <span style="color:#f87171; font-size:0.85rem;">❌ Cancelled</span>
                        <?php endif; ?>

                        <?php if(in_array($order['order_status'], ['pending', 'confirmed', 'shipped'])): ?>
                            <form method="POST" action="/admin/cancel_order/<?= $order['order_id'] ?>" style="display:flex; gap:0.4rem; align-items:center; margin-top:0.3rem;">
                                <input type="text" name="reason" placeholder="Reason for cancellation" required 
                                       style="padding:5px 8px; border-radius:6px; border:1px solid #334155; background:#0f172a; color:white; width:150px; font-size:0.85rem;">
                                <button type="submit" style="background:#ef4444; border:none; color:white; padding:5px 12px; border-radius:6px; cursor:pointer; font-size:0.85rem;"
                                        onclick="return confirm('Cancel this order? All wallet funds spent will be refunded.');">Cancel Order</button>
                            </form>
                        <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($displayOrders)): ?>
                <tr><td colspan="10" style="text-align:center; color:#475569; padding:2rem;">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layouts/admin_footer.php'; ?>
