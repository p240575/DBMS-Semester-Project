<?php require 'views/layouts/admin_header.php'; ?>
<style>
.rev-card{background:#1e293b;border:1px solid #334155;border-radius:10px;overflow:hidden;margin-bottom:1.2rem}
table{width:100%;border-collapse:collapse}
th,td{padding:.8rem 1rem;text-align:left;border-bottom:1px solid #334155;color:#cbd5e1;font-size:.88rem}
th{background:#0f172a;color:#94a3b8;text-transform:uppercase;font-size:.75rem;font-weight:600}
tfoot tr{background:#0f172a}
</style>
<?php
$orders=$data['orders']??[];
$gross=array_sum(array_column($orders,'total_amount'));
$refunds=array_sum(array_filter(array_column($orders,'refund_amount')));
$net=$gross-$refunds;
?>
<div class="admin-dashboard">
<div style="margin-bottom:1.2rem">
  <a href="/admin/index" style="color:#64748b;text-decoration:none;font-size:.88rem">← Dashboard</a>
  <h2 style="margin-top:.4rem">💰 Revenue Breakdown</h2>
  <p style="color:#64748b;margin-top:.3rem">Delivered orders only. Refunds automatically deducted.</p>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.8rem">
  <div style="background:rgba(16,185,129,.1);border:1px solid #10b981;border-radius:12px;padding:1.2rem">
    <p style="color:#94a3b8;font-size:.78rem;text-transform:uppercase;margin:0 0 .3rem">Gross Revenue</p>
    <p style="color:#34d399;font-size:1.8rem;font-weight:800;margin:0">Rs <?=number_format($gross,2)?></p>
    <p style="color:#64748b;font-size:.75rem;margin:.2rem 0 0"><?=count($orders)?> delivered orders</p>
  </div>
  <div style="background:rgba(239,68,68,.1);border:1px solid #ef4444;border-radius:12px;padding:1.2rem">
    <p style="color:#94a3b8;font-size:.78rem;text-transform:uppercase;margin:0 0 .3rem">Total Refunded</p>
    <p style="color:#f87171;font-size:1.8rem;font-weight:800;margin:0">- Rs <?=number_format($refunds,2)?></p>
    <p style="color:#64748b;font-size:.75rem;margin:.2rem 0 0">Credited to wallets</p>
  </div>
  <div style="background:rgba(245,158,11,.1);border:1px solid #f59e0b;border-radius:12px;padding:1.2rem">
    <p style="color:#94a3b8;font-size:.78rem;text-transform:uppercase;margin:0 0 .3rem">Net Revenue</p>
    <p style="color:#f59e0b;font-size:1.8rem;font-weight:800;margin:0">Rs <?=number_format($net,2)?></p>
    <p style="color:#64748b;font-size:.75rem;margin:.2rem 0 0">After refunds</p>
  </div>
</div>
<div class="rev-card">
<table>
<thead><tr><th>#</th><th>Order</th><th>Customer</th><th>Order Amount</th><th>Refund</th><th>Net</th><th>Status</th><th>Delivered</th></tr></thead>
<tbody>
<?php $i=1;foreach($orders as $o):$ref=$o['refund_amount']??0;$net_o=$o['total_amount']-$ref;?>
<tr>
  <td style="color:#64748b"><?=$i++?></td>
  <td style="color:#f59e0b;font-weight:bold">#<?=$o['order_id']?></td>
  <td><?=htmlspecialchars($o['user_name'])?><br><small style="color:#64748b"><?=htmlspecialchars($o['email'])?></small></td>
  <td style="color:#34d399;font-weight:bold">Rs <?=number_format($o['total_amount'],2)?></td>
  <td style="color:<?=$ref>0?'#f87171':'#334155'?>"><?=$ref>0?'- Rs '.number_format($ref,2):'—'?></td>
  <td style="color:<?=$ref>0?'#f59e0b':'#34d399'?>;font-weight:bold">Rs <?=number_format($net_o,2)?></td>
  <td><span style="background:rgba(16,185,129,.2);color:#34d399;padding:2px 9px;border-radius:20px;font-size:.78rem">Delivered</span></td>
  <td style="color:#64748b;font-size:.82rem"><?=!empty($o['delivered_at'])?date('M d, Y',strtotime($o['delivered_at'])):'—'?></td>
</tr>
<?php endforeach;?>
<?php if(empty($orders)):?><tr><td colspan="8" style="text-align:center;color:#475569;padding:2rem">No delivered orders yet.</td></tr><?php endif?>
</tbody>
<tfoot>
<tr>
  <td colspan="3" style="text-align:right;padding:.9rem 1rem;color:#94a3b8;font-weight:600;font-size:.85rem">NET TOTAL REVENUE:</td>
  <td style="color:#34d399;font-weight:bold;font-size:1.1rem;padding:.9rem 1rem">Rs <?=number_format($gross,2)?></td>
  <td style="color:#f87171;font-weight:bold;padding:.9rem 1rem">- Rs <?=number_format($refunds,2)?></td>
  <td style="color:#f59e0b;font-weight:bold;font-size:1.2rem;padding:.9rem 1rem">Rs <?=number_format($net,2)?></td>
  <td colspan="2"></td>
</tr>
</tfoot>
</table>
</div>
</div>
<?php require 'views/layouts/admin_footer.php';?>
