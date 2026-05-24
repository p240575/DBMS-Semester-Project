<?php
// Fix notifications view - mark as read AND show notifications
if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
require_once 'config/database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM Notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark ALL as read so the bell badge clears immediately
$db->prepare("UPDATE Notifications SET is_read = 1 WHERE user_id = ?")->execute([$_SESSION['user_id']]);
?>
<?php require 'views/layouts/header.php'; ?>
<div style="max-width:800px;margin:110px auto 5rem;padding:0 1.5rem">
  <div class="section-header" style="margin-bottom:1.5rem">
    <h2>🔔 All Notifications</h2>
    <div class="accent-line"></div>
  </div>

  <?php if(empty($notifs)): ?>
  <div style="text-align:center;background:var(--bg-card);padding:3rem;border-radius:14px;color:var(--text-secondary)">
    <p style="font-size:4rem;margin-bottom:.5rem">🔕</p>
    <p style="font-size:1.2rem">No notifications yet.</p>
    <p style="font-size:.9rem;color:#475569;margin-top:.5rem">You'll be notified about orders, returns, and more.</p>
  </div>
  <?php else: ?>
  <?php foreach($notifs as $n): ?>
  <div style="background:var(--bg-card);border:1px solid var(--glass-border);border-radius:10px;padding:1rem 1.2rem;margin-bottom:.7rem;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
    <p style="color:#cbd5e1;font-size:.9rem;line-height:1.6;margin:0"><?= htmlspecialchars($n['message']) ?></p>
    <span style="color:#475569;font-size:.73rem;white-space:nowrap;margin-top:.15rem;flex-shrink:0"><?= date('M d, h:i A', strtotime($n['created_at'])) ?></span>
  </div>
  <?php endforeach ?>
  <?php endif ?>

  <div style="margin-top:1.5rem">
    <a href="/user/profile" style="color:#64748b;text-decoration:none;font-size:.88rem">← Back to Profile</a>
  </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
