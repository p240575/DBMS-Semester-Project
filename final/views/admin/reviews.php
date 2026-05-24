<?php require 'views/layouts/admin_header.php'; ?>
<style>
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #1e293b; border-radius: 8px; overflow: hidden; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #334155; color: #cbd5e1; }
th { background: #0f172a; font-weight: 600; }
</style>

<div class="admin-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Manage Reviews</h2>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer Name</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Admin Reply</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['reviews'] as $review): ?>
                <tr>
                    <td style="font-weight: bold;"><?= htmlspecialchars($review['product_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($review['user_name']) ?></td>
                    <td style="color: #fbbf24;">
                        <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                    </td>
                    <td style="max-width: 250px; word-wrap: break-word;"><?= htmlspecialchars($review['comment']) ?></td>
                    <td style="max-width: 200px; word-wrap: break-word;">
                        <?php if(!empty($review['reply'])): ?>
                            <div style="background: rgba(59, 130, 246, 0.1); padding: 8px; border-radius: 4px; font-size: 0.9rem;">
                                <?= htmlspecialchars($review['reply']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($review['created_at'])) ?></td>
                    <td>
                        <form method="POST" action="/admin/reply_review/<?= $review['review_id'] ?>" style="margin-bottom: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <input type="text" name="reply" placeholder="Write reply..." required style="padding: 4px; border-radius: 4px; border: 1px solid #334155; background: #0f172a; color: white;">
                            <button type="submit" class="btn btn-primary btn-sm" style="background:#10b981; border-color:#10b981; color:white; width: 100%;">Reply</button>
                        </form>
                        <form method="POST" action="/admin/delete_review/<?= $review['review_id'] ?>" style="display:inline;">
                            <button type="submit" class="btn btn-sm" style="background:#ef4444; border:none; color:white; padding: 5px 10px; border-radius:4px; width: 100%;" onclick="return confirm('Delete this review?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'views/layouts/admin_footer.php'; ?>
