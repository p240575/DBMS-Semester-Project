<?php require 'views/layouts/header.php'; ?>
<style>
.ret-page{max-width:1000px;margin:110px auto 5rem;padding:0 1.5rem}
.ret-card{background:var(--bg-card);border:1px solid var(--glass-border);border-radius:14px;padding:1.4rem;margin-bottom:1.3rem}
.ret-head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.7rem;padding-bottom:.9rem;margin-bottom:.9rem;border-bottom:1px solid rgba(255,255,255,.07)}
.sp{font-size:.8rem;font-weight:bold;padding:3px 11px;border-radius:20px}
.s-pending{background:rgba(245,158,11,.2);color:#f59e0b;border:1px solid #f59e0b}
.s-approved{background:rgba(59,130,246,.2);color:#60a5fa;border:1px solid #3b82f6}
.s-returning{background:rgba(139,92,246,.2);color:#a78bfa;border:1px solid #8b5cf6}
.s-admin_received{background:rgba(245,158,11,.2);color:#f59e0b;border:1px solid #f59e0b}
.s-refunded{background:rgba(16,185,129,.2);color:#34d399;border:1px solid #10b981}
.s-rejected{background:rgba(239,68,68,.2);color:#f87171;border:1px solid #ef4444}
.by{background:#f59e0b;border:none;color:#0f172a;padding:7px 16px;border-radius:8px;cursor:pointer;font-size:.85rem;font-weight:600}
</style>
<?php
if(!isset($_SESSION['user_id'])){header("Location: /auth/login");exit;}
require_once 'config/database.php';
$db=(new Database())->getConnection();
$stmt=$db->prepare("SELECT r.*,o.total_amount,o.delivered_at FROM ReturnRequests r JOIN Orders o ON r.order_id=o.order_id WHERE r.user_id=? ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$returns=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="ret-page">
  <div class="section-header" style="margin-bottom:1.2rem"><h2>🔄 My Returns & Refunds</h2><div class="accent-line"></div></div>

  <?php if(empty($returns)): ?>
  <div style="text-align:center;background:var(--bg-card);padding:3rem;border-radius:14px;color:var(--text-secondary)">
    <p style="font-size:2.5rem;margin-bottom:.5rem">📦</p>
    <p style="font-size:1.2rem">No return requests yet.</p>
    <p style="font-size:.88rem;color:#475569;margin-top:.3rem">Returns can be requested within 48 hours of receiving your order.</p>
    <a href="/user/purchases" style="display:inline-block;margin-top:1.2rem;background:#f59e0b;color:#0f172a;padding:8px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:.88rem">View My Orders</a>
  </div>
  <?php else: ?>
  <?php $rLabels=['pending'=>'Under Review','approved'=>'Approved – Ship Back','returning'=>'In Transit','admin_received'=>'Inspection','refunded'=>'Refunded','rejected'=>'Rejected']; ?>
  <?php foreach($returns as $r):
    $deliveredAt=!empty($r['delivered_at'])?strtotime($r['delivered_at']):time();
    $returnDeadline=$deliveredAt+(48*3600);
    $hoursLeft=max(0,($returnDeadline-time())/3600);
  ?>
  <div class="ret-card">
    <div class="ret-head">
      <div>
        <strong style="color:#f59e0b">Order #<?= $r['order_id'] ?></strong>
        <span class="sp s-<?= $r['status'] ?>" style="margin-left:.7rem"><?= $rLabels[$r['status']] ?? ucfirst($r['status']) ?></span>
      </div>
      <div style="text-align:right">
        <div style="color:#34d399;font-weight:bold;font-size:.95rem">Rs <?= number_format($r['total_amount'],2) ?></div>
        <small style="color:#64748b"><?= date('M d, Y', strtotime($r['created_at'])) ?></small>
      </div>
    </div>

    <div style="background:rgba(0,0,0,.2);border-radius:8px;padding:.75rem .95rem;margin-bottom:.7rem">
      <p style="color:#94a3b8;font-size:.78rem;font-weight:600;margin:0 0 .2rem;text-transform:uppercase">Your Reason</p>
      <p style="color:#cbd5e1;font-size:.88rem;margin:0"><?= htmlspecialchars($r['reason']) ?></p>
      <?php if(!empty($r['image_url'])): ?>
      <a href="<?= htmlspecialchars($r['image_url']) ?>" target="_blank">
        <img src="<?= htmlspecialchars($r['image_url']) ?>" style="margin-top:.6rem;width:90px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #334155;cursor:zoom-in">
      </a>
      <?php endif ?>
    </div>

    <!-- Status-specific info panels -->
    <?php if($r['status']==='pending'): ?>
    <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:8px;padding:.7rem .9rem">
      <p style="color:#f59e0b;font-size:.86rem;margin:0">⏳ Your request is under admin review. We'll notify you when a decision is made.</p>
    </div>

    <?php elseif($r['status']==='approved'&&!empty($r['return_address'])): ?>
    <div style="background:rgba(59,130,246,.1);border:1px solid #3b82f6;border-radius:8px;padding:.8rem 1rem">
      <p style="color:#60a5fa;font-size:.8rem;font-weight:700;margin:0 0 .3rem;text-transform:uppercase">📍 Return Address</p>
      <p style="color:#cbd5e1;font-size:.9rem;margin:0 0 .3rem;font-weight:600"><?= htmlspecialchars($r['return_address']) ?></p>
      <?php if(!empty($r['admin_note'])): ?><p style="color:#64748b;font-size:.82rem;margin:.3rem 0 .6rem">Note: <?= htmlspecialchars($r['admin_note']) ?></p><?php endif ?>
      <p style="color:#94a3b8;font-size:.8rem;margin:0 0 .8rem">Please pack the product carefully and ship it to the above address. Then click the button below.</p>
      <form method="POST" action="/user/mark_return_sent/<?= $r['return_id'] ?>">
        <button type="submit" class="by" onclick="return confirm('Confirm you have shipped the product to the return address?')">✓ I Sent It Back</button>
      </form>
    </div>

    <?php elseif($r['status']==='returning'): ?>
    <div style="background:rgba(139,92,246,.1);border:1px solid #8b5cf6;border-radius:8px;padding:.7rem .9rem">
      <p style="color:#a78bfa;font-size:.86rem;margin:0">📦 Your return package is on the way. Admin will inspect once received and process your refund.</p>
    </div>

    <?php elseif($r['status']==='admin_received'): ?>
    <div style="background:rgba(245,158,11,.1);border:1px solid #f59e0b;border-radius:8px;padding:.7rem .9rem">
      <p style="color:#fbbf24;font-size:.86rem;margin:0">🔍 Product has been received and is under inspection. Refund decision coming soon.</p>
    </div>

    <?php elseif($r['status']==='refunded'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid #10b981;border-radius:8px;padding:.8rem 1rem">
      <p style="color:#34d399;font-size:.9rem;font-weight:700;margin:0 0 .2rem">💰 Refund Issued!</p>
      <p style="color:#34d399;font-size:1.1rem;font-weight:800;margin:0">Rs <?= number_format($r['refund_amount'],2) ?> added to your NexShop Wallet</p>
      <?php if(!empty($r['admin_note'])): ?><p style="color:#64748b;font-size:.82rem;margin:.3rem 0 0"><?= htmlspecialchars($r['admin_note']) ?></p><?php endif ?>
      <a href="/user/profile" style="display:inline-block;margin-top:.8rem;background:#10b981;color:white;padding:6px 16px;border-radius:8px;text-decoration:none;font-size:.85rem;font-weight:600">View Wallet →</a>
    </div>

    <?php elseif($r['status']==='rejected'): ?>
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.4);border-radius:8px;padding:.7rem .9rem">
      <p style="color:#f87171;font-size:.86rem;font-weight:600;margin:0 0 .2rem">❌ Return Request Rejected</p>
      <p style="color:#94a3b8;font-size:.84rem;margin:0"><?= htmlspecialchars($r['admin_note']?:'Product did not meet our return policy requirements.') ?></p>
    </div>
    <?php endif ?>
  </div>
  <?php endforeach ?>
  <?php endif ?>

  <div style="margin-top:1.5rem;display:flex;gap:1rem">
    <a href="/user/purchases" style="color:#64748b;text-decoration:none;font-size:.88rem">← Back to Orders</a>
    <a href="/user/profile" style="color:#64748b;text-decoration:none;font-size:.88rem">My Profile</a>
  </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
