<?php require 'views/layouts/admin_header.php'; ?>
<style>
.rt-card{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:1.3rem;margin-bottom:1.2rem}
.rt-head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.8rem;margin-bottom:1rem}
.badge{font-size:.78rem;font-weight:bold;padding:3px 11px;border-radius:20px}
.s-pending{background:rgba(245,158,11,.2);color:#f59e0b}
.s-approved{background:rgba(59,130,246,.2);color:#60a5fa}
.s-returning{background:rgba(139,92,246,.2);color:#a78bfa}
.s-admin_received{background:rgba(245,158,11,.2);color:#f59e0b}
.s-refunded{background:rgba(16,185,129,.2);color:#34d399}
.s-rejected{background:rgba(239,68,68,.2);color:#f87171}
.action-form{display:flex;flex-direction:column;gap:.5rem;background:rgba(0,0,0,.2);border-radius:10px;padding:1rem;margin-top:.8rem}
.action-form input[type=text],.action-form input[type=number],.action-form textarea{padding:.55rem .8rem;border-radius:8px;border:1px solid #334155;background:#0f172a;color:#fff;font-size:.85rem;width:100%;box-sizing:border-box}
.btn-a{background:#10b981;border:none;color:#fff;padding:7px 18px;border-radius:8px;cursor:pointer;font-weight:600;font-size:.85rem}
.btn-r{background:#ef4444;border:none;color:#fff;padding:7px 18px;border-radius:8px;cursor:pointer;font-size:.85rem}
.btn-p{background:#8b5cf6;border:none;color:#fff;padding:7px 18px;border-radius:8px;cursor:pointer;font-size:.85rem}
.info-row{display:flex;flex-wrap:wrap;gap:.6rem;align-items:center;margin-bottom:.5rem}
</style>

<div class="admin-dashboard">
<div style="margin-bottom:1.3rem">
  <h2>🔄 Returns & Refunds</h2>
  <p style="color:#64748b;margin-top:.3rem">Total: <?=count($data['returns'])?> requests</p>
</div>
<?php if(empty($data['returns'])):?>
<div style="text-align:center;padding:3rem;background:#1e293b;border-radius:12px;color:#475569">No return requests yet.</div>
<?php endif;?>
<?php foreach($data['returns'] as $r):?>
<div class="rt-card">
  <div class="rt-head">
    <div>
      <strong style="color:#f59e0b">Order #<?=$r['order_id']?></strong>
      <span class="badge s-<?=$r['status']?>" style="margin-left:.7rem"><?=ucfirst(str_replace('_',' ',$r['status']))?></span>
    </div>
    <span style="color:#64748b;font-size:.82rem"><?=date('M d, Y',strtotime($r['created_at']))?></span>
  </div>
  <div class="info-row">
    <span style="color:#94a3b8;font-size:.85rem">👤 <?=htmlspecialchars($r['user_name'])?> · <?=htmlspecialchars($r['email'])?></span>
    <span style="color:#34d399;font-weight:bold">Rs <?=number_format($r['total_amount'],2)?></span>
  </div>
  <div style="background:rgba(0,0,0,.2);border-radius:8px;padding:.7rem .9rem;margin-bottom:.6rem">
    <p style="color:#94a3b8;font-size:.8rem;margin:0 0 .2rem;font-weight:600">Reason:</p>
    <p style="color:#cbd5e1;font-size:.86rem;margin:0"><?=htmlspecialchars($r['reason'])?></p>
    <?php if(!empty($r['image_url'])):?>
    <a href="<?=htmlspecialchars($r['image_url'])?>" target="_blank">
      <img src="<?=htmlspecialchars($r['image_url'])?>" style="margin-top:.6rem;width:100px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #334155;cursor:zoom-in">
    </a>
    <?php endif?>
  </div>
  <?php if(!empty($r['return_address'])):?>
  <p style="color:#60a5fa;font-size:.82rem;margin-bottom:.4rem">📍 Return Address: <?=htmlspecialchars($r['return_address'])?></p>
  <?php endif?>
  <?php if(!empty($r['admin_note'])):?>
  <p style="color:#64748b;font-size:.82rem;margin-bottom:.4rem">📝 Note: <?=htmlspecialchars($r['admin_note'])?></p>
  <?php endif?>
  <?php if(!empty($r['refund_amount'])):?>
  <p style="color:#34d399;font-size:.85rem;font-weight:600;margin-bottom:.4rem">💰 Refund: Rs <?=number_format($r['refund_amount'],2)?></p>
  <?php endif?>

  <!-- ACTION PANELS BY STATUS -->
  <?php if($r['status']==='pending'):?>
  <div class="action-form">
    <p style="color:#f59e0b;font-weight:600;font-size:.88rem;margin:0 0 .5rem">✅ Approve Return Request</p>
    <p style="color:#64748b;font-size:.8rem;margin:0 0 .7rem">Default return address is pre-filled. Edit if needed.</p>
    <form method="POST" action="/admin/approve_return/<?=$r['return_id']?>">
      <label style="color:#94a3b8;font-size:.8rem;display:block;margin-bottom:.3rem">Return Address *</label>
      <input type="text" name="return_address" required style="margin-bottom:.5rem"
             value="NexShop Warehouse, Plot 12, Block-C, SITE Industrial Area, Karachi, Sindh 75700, Pakistan">
      <input type="text" name="admin_note" placeholder="Admin note to customer (optional)" style="margin-bottom:.5rem">
      <div style="display:flex;gap:.6rem;flex-wrap:wrap">
        <button type="submit" class="btn-a" onclick="return confirm('Approve and send return address to customer?')">✓ Approve Return</button>
      </div>
    </form>
    <form method="POST" action="/admin/reject_return/<?=$r['return_id']?>" style="margin-top:.7rem;padding-top:.7rem;border-top:1px solid #334155">
      <label style="color:#f87171;font-size:.8rem;display:block;margin-bottom:.3rem">Reject Return Request</label>
      <input type="text" name="admin_note" placeholder="Rejection reason (e.g. Policy violation, item used)" required style="margin-bottom:.5rem">
      <button type="submit" class="btn-r" onclick="return confirm('Reject this return request?')">✗ Reject</button>
    </form>
  </div>

  <?php elseif($r['status']==='returning'):?>
  <div class="action-form">
    <p style="color:#a78bfa;font-size:.86rem;margin:0 0 .5rem">Customer has shipped the product back. Mark as received when it arrives:</p>
    <form method="POST" action="/admin/received_return/<?=$r['return_id']?>">
      <button type="submit" class="btn-p" onclick="return confirm('Mark product as received?')">📬 Mark as Received</button>
    </form>
  </div>

  <?php elseif($r['status']==='admin_received'):?>
  <div class="action-form">
    <p style="color:#f59e0b;font-weight:600;font-size:.88rem;margin:0 0 .5rem">🔍 Product inspection – Issue Refund or Reject</p>
    <form method="POST" action="/admin/process_refund/<?=$r['return_id']?>">
      <input type="number" name="refund_amount" placeholder="Refund amount (Rs)" step="0.01" min="0" max="<?=$r['total_amount']?>" value="<?=$r['total_amount']?>" required style="margin-bottom:.5rem">
      <input type="text" name="admin_note" placeholder="Refund note (e.g. Product verified, full refund issued)" style="margin-bottom:.5rem">
      <button type="submit" class="btn-a" onclick="return confirm('Issue refund to customer wallet?')">💰 Issue Refund</button>
    </form>
    <form method="POST" action="/admin/reject_return/<?=$r['return_id']?>" style="margin-top:.5rem">
      <input type="text" name="admin_note" placeholder="Rejection reason (e.g. Product damaged by user)" required style="margin-bottom:.5rem">
      <button type="submit" class="btn-r" onclick="return confirm('Reject and cancel refund?')">✗ Cancel Refund</button>
    </form>
  </div>

  <?php elseif($r['status']==='refunded'):?>
  <div style="background:rgba(16,185,129,.08);border:1px solid #10b981;border-radius:8px;padding:.65rem .9rem;margin-top:.6rem">
    <p style="color:#34d399;font-size:.85rem;font-weight:600;margin:0">✅ Refund of Rs <?=number_format($r['refund_amount'],2)?> credited to customer wallet.</p>
  </div>

  <?php elseif($r['status']==='rejected'):?>
  <div style="background:rgba(239,68,68,.08);border:1px solid #ef4444;border-radius:8px;padding:.65rem .9rem;margin-top:.6rem">
    <p style="color:#f87171;font-size:.85rem;font-weight:600;margin:0">❌ Return rejected.</p>
  </div>
  <?php endif?>
</div>
<?php endforeach?>
</div>
<?php require 'views/layouts/admin_footer.php';?>
