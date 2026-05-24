<?php require 'views/layouts/header.php'; ?>
<style>
.orders-page{max-width:1100px;margin:110px auto 5rem;padding:0 1.5rem}
.tab-bar{display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1.5rem;background:var(--bg-card);padding:.5rem;border-radius:12px;border:1px solid var(--glass-border)}
.tab-btn{padding:.45rem 1.1rem;border-radius:8px;border:none;cursor:pointer;font-size:.85rem;font-weight:600;background:transparent;color:#64748b;transition:all .2s;text-decoration:none;display:inline-block}
.tab-btn.active,.tab-btn:hover{background:#f59e0b;color:#0f172a}
.tab-count{background:rgba(255,255,255,.15);padding:1px 6px;border-radius:10px;font-size:.72rem;margin-left:3px}
.oc{background:var(--bg-card);border:1px solid var(--glass-border);border-radius:14px;padding:1.4rem;margin-bottom:1.4rem}
.oh{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.7rem;padding-bottom:.9rem;margin-bottom:.9rem;border-bottom:1px solid rgba(255,255,255,.07)}
.sp{font-size:.8rem;font-weight:bold;padding:3px 11px;border-radius:20px}
.s-pending{background:rgba(245,158,11,.2);color:#f59e0b;border:1px solid #f59e0b}
.s-confirmed,.s-shipped{background:rgba(139,92,246,.2);color:#a78bfa;border:1px solid #8b5cf6}
.s-delivered{background:rgba(16,185,129,.2);color:#34d399;border:1px solid #10b981}
.s-cancelled{background:rgba(239,68,68,.2);color:#f87171;border:1px solid #ef4444}
.ir{display:flex;align-items:center;gap:.9rem;padding:.7rem;background:rgba(0,0,0,.2);border-radius:9px;margin-bottom:.4rem}
.ab{margin-top:.9rem;padding:.75rem .95rem;border-radius:9px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.7rem}
.bg{background:#10b981;border:none;color:#fff;padding:7px 17px;border-radius:8px;cursor:pointer;font-weight:600;font-size:.85rem}
.br{background:#ef4444;border:none;color:#fff;padding:6px 14px;border-radius:8px;cursor:pointer;font-size:.83rem}
.by{background:#f59e0b;border:none;color:#0f172a;padding:7px 15px;border-radius:8px;cursor:pointer;font-size:.85rem;font-weight:600}
.tmr{font-family:monospace;font-size:1rem;color:#f59e0b;font-weight:bold}
.mo{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:900;align-items:center;justify-content:center}
.mo.open{display:flex}
.mb{background:#1e293b;border:1px solid #334155;border-radius:14px;padding:1.8rem;width:90%;max-width:480px}
.mb label{display:block;color:#94a3b8;font-size:.83rem;margin-bottom:.35rem;margin-top:.75rem}
.mb textarea,.mb input[type=text]{width:100%;background:#0f172a;border:1px solid #334155;color:#fff;padding:.65rem;border-radius:8px;font-size:.88rem;box-sizing:border-box}
</style>
<?php
if(!isset($_SESSION['user_id'])){header("Location: /auth/login");exit;}
require_once 'config/database.php';
$db=(new Database())->getConnection();
$uid=$_SESSION['user_id'];
// Auto-deliver: 72 hours (3 days) from dispatch
try{
    $db->prepare("UPDATE Orders SET order_status='delivered',delivered_at=NOW() 
                  WHERE user_id=? AND order_status='shipped' 
                  AND TIMESTAMPDIFF(SECOND, COALESCE(dispatched_at,created_at), NOW()) >= 259200")
       ->execute([$uid]);
}catch(Exception $e){}
$so=$db->prepare("SELECT o.* FROM Orders o WHERE o.user_id=? ORDER BY o.created_at DESC");
$so->execute([$uid]);$all=$so->fetchAll(PDO::FETCH_ASSOC);
foreach($all as &$ord){
    $si=$db->prepare("SELECT oi.*,p.name as product_name,p.product_id,pi.image_url,pv.variant_key,pv.price FROM OrderItems oi JOIN ProductVariants pv ON oi.variant_id=pv.variant_id JOIN Products p ON pv.product_id=p.product_id LEFT JOIN ProductImages pi ON p.product_id=pi.product_id AND pi.is_default=1 WHERE oi.order_id=?");
    $si->execute([$ord['order_id']]);$ord['items']=$si->fetchAll(PDO::FETCH_ASSOC);
    $sr=$db->prepare("SELECT * FROM ReturnRequests WHERE order_id=? AND user_id=?");
    $sr->execute([$ord['order_id'],$uid]);$ord['rr']=$sr->fetch(PDO::FETCH_ASSOC);
}unset($ord);
$cnt=['all'=>count($all),'pending'=>0,'shipped'=>0,'delivered'=>0,'cancelled'=>0];
foreach($all as $o){
    if(in_array($o['order_status'],['pending','confirmed']))$cnt['pending']++;
    elseif($o['order_status']==='shipped')$cnt['shipped']++;
    elseif($o['order_status']==='delivered')$cnt['delivered']++;
    elseif($o['order_status']==='cancelled')$cnt['cancelled']++;
}
$tab=$_GET['tab']??'all';
$disp=array_filter($all,function($o)use($tab){
    if($tab==='all')return true;
    if($tab==='pending')return in_array($o['order_status'],['pending','confirmed']);
    return $o['order_status']===$tab;
});
?>
<div class="orders-page">
<div class="section-header" style="margin-bottom:1.1rem;"><h2>My Orders</h2><div class="accent-line"></div></div>
<div class="tab-bar">
<?php foreach(['all'=>'All','pending'=>'Pending','shipped'=>'Shipped','delivered'=>'Received','cancelled'=>'Cancelled'] as $k=>$l):?>
<a href="?tab=<?=$k?>" class="tab-btn <?=$tab===$k?'active':''?>"><?=$l?> <span class="tab-count"><?=$cnt[$k]?></span></a>
<?php endforeach;?>
</div>
<?php if(empty($disp)):?>
<div style="text-align:center;background:var(--bg-card);padding:3rem;border-radius:14px;color:var(--text-secondary);">
<p style="font-size:1.2rem;margin-bottom:1rem;">No orders here yet.</p><a href="/products" class="btn btn-primary">Shop Now</a></div>
<?php endif;?>
<?php foreach($disp as $ord):
$oid=$ord['order_id'];$oT=strtotime($ord['created_at']);
$disAt=!empty($ord['dispatched_at'])?strtotime($ord['dispatched_at']):$oT;
$delAt=!empty($ord['delivered_at'])?strtotime($ord['delivered_at']):time();
$canCancel=$ord['order_status']==='pending'&&(time()-$oT)<3600;
$mLeft=max(0,ceil((3600-(time()-$oT))/60));
// 48 hours = 172800 seconds — receive button unlocks after this
$secFromDispatch=time()-$disAt;
$canRcv=$ord['order_status']==='shipped'&&$secFromDispatch>=172800;
$rcvEnd=$disAt+172800; // unlock timestamp for countdown
// 48 hours = 172800 seconds — return window after delivery
$secFromDelivery=time()-$delAt;
$canRet=$ord['order_status']==='delivered'&&$secFromDelivery<172800&&empty($ord['rr']);
$retHLeft=max(0,ceil((172800-$secFromDelivery)/3600));
$sC=['pending'=>'s-pending','confirmed'=>'s-pending','shipped'=>'s-shipped','delivered'=>'s-delivered','cancelled'=>'s-cancelled'];
$sc=$sC[$ord['order_status']]??'s-pending';
?>
<div class="oc">
<div class="oh">
  <div><span style="color:#64748b;font-size:.8rem;">Order</span> <strong style="color:#f59e0b;"> #<?=$oid?></strong> <span style="color:#475569;font-size:.77rem;margin-left:.8rem;"><?=date('M d,Y h:i A',$oT)?></span></div>
  <div style="display:flex;align-items:center;gap:.7rem;flex-wrap:wrap;">
    <span class="sp <?=$sc?>"><?=ucfirst($ord['order_status']==='delivered'?'Received':$ord['order_status'])?></span>
    <strong style="color:#fff;">Rs <?=number_format($ord['total_amount'],2)?></strong>
    <span style="color:#64748b;font-size:.77rem;">📦 COD</span>
  </div>
</div>
<?php foreach($ord['items'] as $it):?>
<div class="ir">
  <img src="<?=htmlspecialchars($it['image_url']??'/assets/img/hero_1.png')?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;flex-shrink:0">
  <div style="flex:1"><strong style="color:#fff"><?=htmlspecialchars($it['product_name'])?></strong><?php if(!empty($it['variant_key'])&&$it['variant_key']!=='Default'):?><span style="color:#a78bfa;font-size:.78rem;margin-left:.3rem"><?=htmlspecialchars($it['variant_key'])?></span><?php endif?>
  <div style="color:#64748b;font-size:.8rem;margin-top:.1rem"><?=$it['quantity']?> × Rs <?=number_format($it['price']??0,2)?></div></div>
  <a href="/products/show/<?=$it['product_id']?>" style="font-size:.78rem;color:#f59e0b;text-decoration:none">Buy Again →</a>
</div>
<?php endforeach?>
<?php if($ord['order_status']==='shipped'&&!empty($ord['delivery_agent'])):?>
<div style="margin-top:.7rem;background:rgba(139,92,246,.1);border:1px solid #8b5cf6;border-radius:8px;padding:.65rem .95rem;font-size:.86rem;color:#a78bfa">🚚 Dispatched by <strong><?=htmlspecialchars($ord['delivery_agent'])?></strong></div>
<?php endif?>
<?php if($canCancel):?>
<div class="ab" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3)">
  <span style="color:#f59e0b;font-size:.84rem">⏱ Cancel within <?=$mLeft?>min</span>
  <form method="POST" action="/user/cancelOrder/<?=$oid?>"><button class="br" onclick="return confirm('Cancel order?')">Cancel Order</button></form>
</div>
<?php elseif($ord['order_status']==='pending'):?><p style="color:#64748b;font-size:.78rem;font-style:italic;margin-top:.5rem">⏱ Cancellation window closed.</p><?php endif?>
<?php if($ord['order_status']==='shipped'):?>
  <?php if($canRcv):?>
  <div class="ab" style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.3)">
    <div><p style="color:#34d399;margin:0;font-weight:600;font-size:.86rem">✅ Have you received your order?</p>
    <p style="color:#64748b;font-size:.76rem;margin:.25rem 0 0">⚠ If product is faulty, request a return within 2 days of confirming receipt.</p></div>
    <form method="POST" action="/user/confirm_received/<?=$oid?>"><button class="bg" onclick="return confirm('Confirm receipt? Return window opens for 48h after this.')">I Received It ✓</button></form>
  </div>
  <?php else:?>
  <div class="ab" style="background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.3)">
    <div><p style="color:#a78bfa;font-size:.85rem;margin:0">🕐 Receive button unlocks in <span class="tmr" id="t_<?=$oid?>">--:--:--</span></p>
    <p style="color:#64748b;font-size:.76rem;margin:.2rem 0 0">If product is faulty after receiving, you'll have 2 days to request a return.</p></div>
  </div>
  <script>(function(){var e=<?=$rcvEnd*1000?>;function f(){var s=Math.max(0,Math.floor((e-Date.now())/1000)),h=Math.floor(s/3600),m=Math.floor(s%3600/60),ss=s%60,el=document.getElementById('t_<?=$oid?>');if(el)el.textContent=(h+'').padStart(2,'0')+':'+(m+'').padStart(2,'0')+':'+(ss+'').padStart(2,'0');if(s>0)setTimeout(f,1000);else location.reload();}f();})();</script>
  <?php endif?>
<?php endif?>
<?php if($ord['order_status']==='delivered'&&empty($ord['rr'])):?>
  <?php if($canRet):?>
  <div class="ab" style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.22)">
    <span style="color:#94a3b8;font-size:.84rem">⏳ Return window: <strong style="color:#f59e0b"><?=$retHLeft?>h left</strong></span>
    <button class="by" onclick="document.getElementById('rm_<?=$oid?>').classList.add('open')">Request Return / Refund</button>
  </div>
  <div id="rm_<?=$oid?>" class="mo" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="mb">
      <h3 style="color:#fff;margin-bottom:.4rem">🔄 Return & Refund</h3>
      <p style="color:#64748b;font-size:.83rem;margin-bottom:.9rem">Order #<?=$oid?> · Rs <?=number_format($ord['total_amount'],2)?></p>
      <form method="POST" action="/user/request_return/<?=$oid?>" enctype="multipart/form-data">
        <label>Reason for return *</label>
        <textarea name="reason" required rows="3" placeholder="Describe the issue..."></textarea>
        <label>Product photo showing issue *</label>
        <input type="file" name="image" accept="image/*" required style="color:#94a3b8;padding:.35rem 0">
        <div style="display:flex;gap:.7rem;justify-content:flex-end;margin-top:1.1rem">
          <button type="button" onclick="document.getElementById('rm_<?=$oid?>').classList.remove('open')" style="background:transparent;border:1px solid #334155;color:#94a3b8;padding:7px 16px;border-radius:8px;cursor:pointer">Cancel</button>
          <button type="submit" class="br">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
  <?php else:?><p style="color:#475569;font-size:.77rem;font-style:italic;margin-top:.7rem">ℹ Return window closed (48h after delivery).</p><?php endif?>
<?php endif?>
<?php if(!empty($ord['rr'])):$ret=$ord['rr'];$rC=['pending'=>'color:#f59e0b','approved'=>'color:#60a5fa','returning'=>'color:#a78bfa','admin_received'=>'color:#f59e0b','refunded'=>'color:#34d399','rejected'=>'color:#f87171'];?>
<div style="margin-top:.8rem;padding:.75rem .95rem;border-radius:8px;border:1px solid #334155;background:rgba(0,0,0,.2)">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem">
    <span style="color:#94a3b8;font-size:.83rem">🔄 Return Status:</span>
    <strong style="font-size:.83rem;<?=$rC[$ret['status']]??''?>"><?=ucfirst(str_replace('_',' ',$ret['status']))?></strong>
  </div>
  <?php if($ret['status']==='approved'&&!empty($ret['return_address'])):?>
  <div style="margin-top:.5rem;background:rgba(59,130,246,.1);border:1px solid #3b82f6;border-radius:8px;padding:.6rem .8rem">
    <p style="color:#60a5fa;font-size:.8rem;font-weight:600;margin:0">📍 Ship product to:</p>
    <p style="color:#cbd5e1;font-size:.86rem;margin:.2rem 0"><?=htmlspecialchars($ret['return_address'])?></p>
    <form method="POST" action="/user/mark_return_sent/<?=$ret['return_id']?>" style="margin-top:.5rem">
      <button class="by" onclick="return confirm('Confirm you shipped the product back?')">I Sent It Back ✓</button>
    </form>
  </div>
  <?php elseif($ret['status']==='returning'):?><p style="color:#a78bfa;font-size:.84rem;margin-top:.4rem">📦 In transit – admin will inspect on arrival.</p>
  <?php elseif($ret['status']==='admin_received'):?><p style="color:#f59e0b;font-size:.84rem;margin-top:.4rem">🔍 Product received by admin. Inspection in progress.</p>
  <?php elseif($ret['status']==='refunded'):?><div style="margin-top:.4rem;background:rgba(16,185,129,.1);border:1px solid #10b981;border-radius:8px;padding:.55rem .8rem"><p style="color:#34d399;font-weight:600;margin:0">💰 Refund of Rs <?=number_format($ret['refund_amount'],2)?> credited to your wallet!</p></div>
  <?php elseif($ret['status']==='rejected'):?><p style="color:#f87171;font-size:.84rem;margin-top:.4rem">❌ Rejected: <?=htmlspecialchars($ret['admin_note']?:'Product did not meet return conditions.')?></p><?php endif?>
</div>
<?php endif?>
</div>
<?php endforeach?>
</div>
<?php require 'views/layouts/footer.php';?>
