<?php require 'views/layouts/header.php'; ?>
<style>
.pay-opt{display:block;padding:1rem;border:2px solid rgba(255,255,255,.1);border-radius:10px;cursor:pointer;margin-bottom:.8rem;transition:all .2s}
.pay-opt:has(input:checked){border-color:#f59e0b;background:rgba(245,158,11,.07)}
.pay-opt input{margin-right:.7rem;accent-color:#f59e0b}
.addr-label{display:block;padding:1rem;border:2px solid rgba(255,255,255,.1);border-radius:9px;cursor:pointer;margin-bottom:.7rem;transition:border-color .2s}
.addr-label:has(input:checked){border-color:#f59e0b}
.sum-row{display:flex;justify-content:space-between;padding:.45rem 0;border-bottom:1px solid rgba(255,255,255,.06);color:#94a3b8;font-size:.9rem}
.sum-row:last-child{border:none}
</style>
<?php
$cartItems     = $data['cartItems']    ?? [];
$total         = $data['total']        ?? 0;   // already after discount
$discount      = $data['discount']     ?? 0;
$walletBalance = $data['wallet_balance'] ?? 0;
$walletUsed    = min($walletBalance, $total);
$codAmount     = max(0, $total - $walletUsed);
?>
<div class="featured-section" style="margin-top:100px;min-height:60vh;max-width:1050px;margin-left:auto;margin-right:auto">
  <div class="section-header"><h2>Confirm Your Order</h2><div class="accent-line"></div></div>

  <form action="/checkout/process" method="POST" style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;margin-top:1.5rem">

    <!-- LEFT: address + payment -->
    <div style="flex:2;min-width:300px;background:var(--bg-card);padding:2rem;border-radius:16px;border:1px solid var(--glass-border)">

      <h3 style="margin-bottom:1rem;color:var(--accent-primary)">📍 Shipping Address</h3>
      <?php if(!empty($data['addresses'])): ?>
        <?php foreach($data['addresses'] as $idx => $addr): ?>
        <label class="addr-label">
          <div style="display:flex;align-items:flex-start;gap:.9rem">
            <input type="radio" name="address_id" value="<?= $addr['address_id'] ?>" <?= $idx===0?'checked':'' ?>>
            <div>
              <strong style="color:white"><?= htmlspecialchars($addr['full_name']) ?></strong>
              <?php if($addr['is_default']): ?><span style="background:#10b981;color:white;font-size:.7rem;padding:1px 6px;border-radius:6px;margin-left:.5rem">Default</span><?php endif ?>
              <p style="color:#64748b;font-size:.85rem;margin:.2rem 0 0">📞 <?= htmlspecialchars($addr['phone']) ?></p>
              <p style="color:#94a3b8;font-size:.85rem;margin:.1rem 0 0"><?= htmlspecialchars($addr['address_line']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['province']) ?> <?= htmlspecialchars($addr['zipcode']) ?></p>
            </div>
          </div>
        </label>
        <?php endforeach ?>
      <?php else: ?>
        <p style="color:#f87171">No saved address. <a href="/user/profile" style="color:#f59e0b">Add one →</a></p>
      <?php endif ?>

      <!-- Delivery Notice -->
      <div style="background:rgba(59,130,246,.1);border:1px solid #3b82f6;border-radius:9px;padding:.7rem 1rem;margin:1rem 0">
        <p style="color:#60a5fa;font-size:.85rem;margin:0">🚚 <strong>Free Delivery</strong> on all orders! No minimum order required. Delivered in 3–5 business days.</p>
      </div>

      <h3 style="margin-top:1.5rem;margin-bottom:1rem;color:var(--accent-secondary)">💳 Payment Method</h3>

      <?php if($walletBalance <= 0): ?>
        <!-- No wallet: only COD -->
        <label class="pay-opt">
          <input type="radio" name="payment_method" value="cod" checked>
          <strong style="color:white">💵 Cash on Delivery</strong>
          <p style="color:#64748b;font-size:.83rem;margin:.2rem 0 0">Pay Rs <?= number_format($total,2) ?> when your order arrives.</p>
        </label>

      <?php else: ?>
        <!-- Has wallet balance: show all 3 options -->
        <?php if($walletBalance >= $total): ?>
        <!-- Wallet covers full amount -->
        <label class="pay-opt">
          <input type="radio" name="payment_method" value="wallet" id="pm_wallet" checked onchange="updatePayInfo()">
          <strong style="color:#34d399">💰 Pay Fully from Wallet</strong>
          <p style="color:#64748b;font-size:.83rem;margin:.2rem 0 0">Rs <?= number_format($total,2) ?> from your wallet (Balance: Rs <?= number_format($walletBalance,2) ?>). Nothing to pay on delivery.</p>
        </label>
        <label class="pay-opt">
          <input type="radio" name="payment_method" value="cod" id="pm_cod" onchange="updatePayInfo()">
          <strong style="color:white">💵 Cash on Delivery</strong>
          <p style="color:#64748b;font-size:.83rem;margin:.2rem 0 0">Pay Rs <?= number_format($total,2) ?> on delivery. Wallet balance kept.</p>
        </label>

        <?php else: ?>
        <!-- Wallet partial -->
        <label class="pay-opt">
          <input type="radio" name="payment_method" value="wallet_cod" id="pm_wallet_cod" checked onchange="updatePayInfo()">
          <strong style="color:#f59e0b">💰+💵 Wallet + Cash on Delivery</strong>
          <p style="color:#64748b;font-size:.83rem;margin:.2rem 0 0">Rs <?= number_format($walletUsed,2) ?> from wallet + Rs <?= number_format($codAmount,2) ?> on delivery.</p>
        </label>
        <label class="pay-opt">
          <input type="radio" name="payment_method" value="cod" id="pm_cod" onchange="updatePayInfo()">
          <strong style="color:white">💵 Cash on Delivery</strong>
          <p style="color:#64748b;font-size:.83rem;margin:.2rem 0 0">Pay full Rs <?= number_format($total,2) ?> on delivery. Wallet balance kept.</p>
        </label>
        <?php endif ?>
      <?php endif ?>
    </div>

    <!-- RIGHT: order summary -->
    <div style="flex:1;min-width:280px;background:var(--bg-card);padding:2rem;border-radius:16px;border:1px solid var(--glass-border);position:sticky;top:110px">
      <h3 style="color:white;margin-bottom:1.2rem">Order Summary</h3>

      <?php foreach($cartItems as $item): ?>
      <div class="sum-row">
        <span><?= htmlspecialchars($item['name']) ?> <span style="color:#64748b">×<?= $item['cart_quantity'] ?></span></span>
        <span>Rs <?= number_format($item['price'] * $item['cart_quantity'], 2) ?></span>
      </div>
      <?php endforeach ?>

      <div style="height:.5rem"></div>
      <?php if($discount > 0): ?>
      <div class="sum-row"><span style="color:#34d399">Discount (<?= $discount ?>%)</span><span style="color:#34d399">- Rs <?= number_format(($data['raw_total']??$total)*$discount/100,2) ?></span></div>
      <?php endif ?>
      <div class="sum-row"><span style="color:#60a5fa">🚚 Delivery</span><span style="color:#60a5fa">FREE</span></div>

      <?php if($walletBalance > 0): ?>
      <div class="sum-row" id="wallet_row">
        <span style="color:#34d399">💰 Wallet</span>
        <span style="color:#34d399" id="wallet_used_display">- Rs <?= number_format($walletUsed,2) ?></span>
      </div>
      <?php endif ?>

      <hr style="border:none;border-top:1px solid rgba(255,255,255,.1);margin:.8rem 0">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem">
        <strong style="color:white;font-size:1rem">Total</strong>
        <strong style="color:#f59e0b;font-size:1.25rem">Rs <?= number_format($total,2) ?></strong>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.3rem" id="cod_row_display">
        <span style="color:#94a3b8;font-size:.85rem">Cash on Delivery</span>
        <span style="color:#f59e0b;font-size:.95rem;font-weight:bold" id="cod_amount_display">Rs <?= number_format($codAmount,2) ?></span>
      </div>

      <button type="submit" class="btn btn-primary btn-large" style="width:100%"
              <?= empty($data['addresses']) ? 'disabled' : '' ?>>
        ✅ Confirm & Place Order
      </button>
      <a href="/cart" style="display:block;text-align:center;margin-top:.8rem;color:#64748b;font-size:.83rem;text-decoration:none">← Back to Cart</a>
    </div>
  </form>
</div>

<script>
var total = <?= $total ?>;
var walletBalance = <?= $walletBalance ?>;
var walletUsed = <?= $walletUsed ?>;
var codAmount = <?= $codAmount ?>;

function updatePayInfo() {
    var pm = document.querySelector('input[name="payment_method"]:checked');
    if (!pm) return;
    var wRow = document.getElementById('wallet_row');
    var wDisp = document.getElementById('wallet_used_display');
    var cDisp = document.getElementById('cod_amount_display');
    if (pm.value === 'wallet') {
        if(wRow) { wRow.style.display='flex'; wDisp.textContent = '- Rs '+walletBalance.toFixed(2); }
        if(cDisp) cDisp.textContent = 'Rs 0.00';
    } else if (pm.value === 'wallet_cod') {
        if(wRow) { wRow.style.display='flex'; wDisp.textContent = '- Rs '+walletUsed.toFixed(2); }
        if(cDisp) cDisp.textContent = 'Rs '+codAmount.toFixed(2);
    } else {
        if(wRow) wRow.style.display='none';
        if(cDisp) cDisp.textContent = 'Rs '+total.toFixed(2);
    }
}

// Highlight selected address
document.querySelectorAll('input[name="address_id"]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('.addr-label').forEach(l => l.style.borderColor = 'rgba(255,255,255,.1)');
        this.closest('.addr-label').style.borderColor = 'var(--accent-primary)';
    });
});
</script>
<?php require 'views/layouts/footer.php'; ?>
