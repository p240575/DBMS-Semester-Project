<?php require 'views/layouts/admin_header.php'; ?>
<style>
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #1e293b; border-radius: 8px; overflow: hidden; }
th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; vertical-align: top; }
th { background: #0f172a; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.8rem; }
.addr-chip { background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 0.4rem 0.7rem; margin-bottom: 0.4rem; font-size: 0.8rem; line-height: 1.5; }
.addr-chip.default { border-color: #10b981; }
.default-badge { background: rgba(16,185,129,0.2); color: #34d399; font-size: 0.7rem; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
</style>

<div class="admin-dashboard">
    <div style="margin-bottom: 1.5rem;">
        <h2>Manage Customers</h2>
        <p style="color:#64748b; margin-top:0.3rem;">Total: <?= count($data['customers']) ?> customers</p>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name & Email</th>
                    <th>Phone</th>
                    <th>All Addresses</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['customers'] as $customer): ?>
                <tr>
                    <td style="color:#64748b;"><?= $customer['user_id'] ?></td>
                    <td>
                        <span style="font-weight:bold; color:white;"><?= htmlspecialchars($customer['user_name']) ?></span><br>
                        <small style="color:#64748b;"><?= htmlspecialchars($customer['email']) ?></small>
                    </td>
                    <td style="color:#94a3b8;"><?= htmlspecialchars($customer['phone']) ?></td>
                    <td style="min-width:220px;">
                        <?php if(!empty($customer['addresses'])): ?>
                            <?php foreach($customer['addresses'] as $addr): ?>
                                <div class="addr-chip <?= $addr['is_default'] ? 'default' : '' ?>">
                                    <?php if($addr['is_default']): ?><span class="default-badge">Default</span><?php endif; ?>
                                    <strong><?= htmlspecialchars($addr['full_name']) ?></strong> · <?= htmlspecialchars($addr['phone']) ?><br>
                                    <?= htmlspecialchars($addr['address_line']) ?><br>
                                    <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['province']) ?> <?= htmlspecialchars($addr['zipcode'] ?? '') ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color:#475569;">No addresses</span>
                        <?php endif; ?>
                    </td>
                    <td style="color:#64748b;"><?= date('M d, Y', strtotime($customer['registered_at'])) ?></td>
                    <td>
                        <form method="POST" action="/admin/send_notice/<?= $customer['user_id'] ?>" style="margin-bottom: 0.5rem; display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <input type="text" name="message" placeholder="Notice..." required 
                                   style="padding:4px 8px; border-radius:6px; border:1px solid #334155; background:#0f172a; color:white; width:130px; font-size:0.8rem;">
                            <button type="submit" style="background:#3b82f6; border:none; color:white; padding:5px 10px; border-radius:6px; cursor:pointer; font-size:0.8rem;">Send</button>
                        </form>
                        <form method="POST" action="/admin/delete_customer/<?= $customer['user_id'] ?>">
                            <button type="submit" style="background:#ef4444; border:none; color:white; padding:5px 10px; border-radius:6px; cursor:pointer; font-size:0.8rem; width:100%;"
                                    onclick="return confirm('Delete this customer and all their data?');">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($data['customers'])): ?>
                <tr><td colspan="6" style="text-align:center; color:#475569; padding:2rem;">No customers yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layouts/admin_footer.php'; ?>
