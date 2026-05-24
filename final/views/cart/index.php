<?php require 'views/layouts/header.php'; ?>
<style>
.cart-page{max-width:1050px;margin:110px auto 5rem;padding:0 1.5rem}
.cart-table{width:100%;border-collapse:collapse}
.cart-table th{padding:.8rem 1rem;text-align:left;border-bottom:1px solid rgba(255,255,255,.08);color:#64748b;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em}
.cart-table td{padding:.9rem 1rem;border-bottom:1px solid rgba(255,255,255,.05);color:#cbd5e1;vertical-align:middle}
.qty-ctrl{display:inline-flex;align-items:center;gap:0;border:1px solid #334155;border-radius:8px;overflow:hidden}
.qty-ctrl button{background:#1e293b;border:none;color:#f59e0b;padding:5px 12px;cursor:pointer;font-size:1.1rem;font-weight:bold;transition:background .15s}
.qty-ctrl button:hover{background:#334155}
.qty-ctrl span{background:#0f172a;padding:4px 14px;font-size:.95rem;font-weight:600;color:white;min-width:36px;text-align:center}
.btn-rm{background:transparent;border:1px solid rgba(239,68,68,.4);color:#f87171;padding:5px 12px;border-radius:7px;cursor:pointer;font-size:.82rem;transition:all .2s}
.btn-rm:hover{background:rgba(239,68,68,.15);border-color:#ef4444}
.btn-clr{background:transparent;border:1px solid #334155;color:#64748b;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:.85rem;transition:all .2s}
.btn-clr:hover{border-color:#ef4444;color:#f87171}
.cart-summary{background:var(--bg-card);border:1px solid var(--glass-border);border-radius:16px;padding:1.8rem;position:sticky;top:110px}
.srow{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid rgba(255,255,255,.06)}
.srow:last-child{border:none}
.wallet-badge{background:rgba(16,185,129,.15);border:1px solid #10b981;border-radius:10px;padding:.6rem .9rem;margin-bottom:1rem;font-size:.83rem;color:#34d399}
.free-ship{background:rgba(59,130,246,.1);border:1px solid #3b82f6;border-radius:8px;padding:.55rem .9rem;margin:.6rem 0;font-size:.83rem;color:#60a5fa}
</style>
<?php
$cartItems = $data['cartItems'] ?? [];
$total = $data['total'] ?? 0;
$walletBalance = $data['wallet_balance'] ?? 0;
$discount = $_SESSION['discount'] ?? 0;
$afterDiscount = $total * (1 - $discount / 100);
$walletUsed = min($walletBalance, $afterDiscount);
$codAmount = max(0, $afterDiscount - $walletUsed);
?>
<div class="cart-page">
  <div class="section-header" style="margin-bottom:1.5rem">
    <h2>🛒 Shopping Cart</h2>
    <div class="accent-line"></div>
  </div>

  <?php if(!empty($cartItems)): ?>
  <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start">

    <!-- Cart Items -->
    <div style="flex:2;min-width:320px;background:var(--bg-card);border:1px solid var(--glass-border);border-radius:16px;padding:1.5rem">
      <!-- Header row -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem">
        <p style="color:#64748b;font-size:.88rem;margin:0"><?= count($cartItems) ?> item<?= count($cartItems)!==1?'s':'' ?> in cart</p>
        <form method="POST" action="/cart/clear" style="display:inline">
          <button type="submit" class="btn-clr" onclick="return confirm('Clear entire cart?')">🗑 Clear All</button>
        </form>
      </div>

      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cartItems as $item): $lineTotal = $item['price'] * $item['cart_quantity']; ?>
          <tr>
            <td style="display:flex;align-items:center;gap:.9rem">
              <img src="<?= htmlspecialchars($item['image_url'] ?? '/assets/img/hero_1.png') ?>" style="width:52px;height:52px;object-fit:cover;border-radius:9px;flex-shrink:0">
              <div>
                <strong style="color:white;display:block"><?= htmlspecialchars($item['name']) ?></strong>
                <?php if(!empty($item['variant_key']) && $item['variant_key'] !== 'Default'): ?>
                  <small style="color:#a78bfa"><?= htmlspecialchars($item['variant_key']) ?></small>
                <?php endif ?>
                <?php if(($item['stock'] ?? 99) <= 5): ?>
                  <small style="color:#f87171">⚠ Only <?= $item['stock'] ?> left!</small>
                <?php endif ?>
              </div>
            </td>
            <td style="color:#a78bfa;font-weight:600">Rs <?= number_format($item['price'], 2) ?></td>
            <td>
              <div class="qty-ctrl">
                <form method="POST" action="/cart/update" style="display:inline">
                  <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                  <input type="hidden" name="action" value="decrease">
                  <button type="submit">−</button>
                </form>
                <span><?= $item['cart_quantity'] ?></span>
                <form method="POST" action="/cart/update" style="display:inline">
                  <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                  <input type="hidden" name="action" value="increase">
                  <button type="submit">+</button>
                </form>
              </div>
            </td>
            <td style="color:#f59e0b;font-weight:bold">Rs <?= number_format($lineTotal, 2) ?></td>
            <td>
              <form method="POST" action="/cart/update">
                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                <input type="hidden" name="action" value="remove">
                <button type="submit" class="btn-rm">Remove</button>
              </form>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>

      <!-- Coupon -->
      <div style="margin-top:1.5rem;padding-top:1.2rem;border-top:1px solid rgba(255,255,255,.08)">
        <form action="/cart/promo" method="POST" style="display:flex;gap:.8rem;flex-wrap:wrap">
          <input type="text" name="promo_code" placeholder="Promo / Coupon Code"
                 style="flex:1;min-width:160px;background:rgba(0,0,0,.25);border:1px solid var(--glass-border);color:white;border-radius:8px;padding:.7rem .9rem;font-size:.9rem">
          <button type="submit" class="btn btn-outline" style="padding:.7rem 1.4rem">Apply</button>
        </form>
        <?php if(isset($_SESSION['promo_error'])): ?>
          <p style="color:#f87171;margin-top:.4rem;font-size:.85rem"><?= $_SESSION['promo_error']; unset($_SESSION['promo_error']); ?></p>
        <?php endif ?>
        <?php if(isset($_SESSION['promo_success'])): ?>
          <p style="color:#34d399;margin-top:.4rem;font-size:.85rem">✓ <?= $_SESSION['promo_success']; unset($_SESSION['promo_success']); ?></p>
        <?php endif ?>
      </div>
    </div>

    <!-- Summary -->
    <div style="flex:1;min-width:280px">
      <div class="cart-summary">
        <h3 style="color:white;margin-bottom:1.2rem">Order Summary</h3>

        <div class="srow">
          <span style="color:#64748b">Subtotal</span>
          <span>Rs <?= number_format($total, 2) ?></span>
        </div>
        <?php if($discount > 0): ?>
        <div class="srow">
          <span style="color:#34d399">Coupon (<?= $discount ?>% off)</span>
          <span style="color:#34d399">- Rs <?= number_format($total * $discount/100, 2) ?></span>
        </div>
        <?php endif ?>
        <div class="srow">
          <span style="color:#60a5fa">🚚 Delivery</span>
          <span style="color:#60a5fa">Free</span>
        </div>
        <div class="free-ship">🚚 Free delivery on all orders! No minimum required.</div>

        <?php if($walletBalance > 0): ?>
        <div class="wallet-badge">💰 Wallet Balance: Rs <?= number_format($walletBalance, 2) ?>
          <?php if($walletUsed > 0): ?>
            <br><small>Rs <?= number_format($walletUsed, 2) ?> will be used from wallet</small>
          <?php endif ?>
        </div>
        <?php endif ?>

        <div class="srow" style="border-top:1px solid rgba(255,255,255,.1);margin-top:.5rem;padding-top:.8rem">
          <strong style="color:white;font-size:1.1rem">Total After Discount</strong>
          <strong style="color:#f59e0b;font-size:1.2rem">Rs <?= number_format($afterDiscount, 2) ?></strong>
        </div>
        <?php if($walletUsed > 0): ?>
        <div class="srow">
          <span style="color:#34d399">💰 Wallet Pays</span>
          <span style="color:#34d399">- Rs <?= number_format($walletUsed, 2) ?></span>
        </div>
        <div class="srow">
          <strong style="color:white">Cash on Delivery</strong>
          <strong style="color:#f59e0b">Rs <?= number_format($codAmount, 2) ?></strong>
        </div>
        <?php endif ?>

        <a href="<?= isset($_SESSION['user_id']) ? '/checkout' : '/auth/login' ?>"
           class="btn btn-primary btn-large" style="width:100%;text-align:center;margin-top:1.3rem;display:block">
          Proceed to Checkout →
        </a>
        <a href="/products" style="display:block;text-align:center;margin-top:.8rem;color:#64748b;font-size:.85rem;text-decoration:none">← Continue Shopping</a>
      </div>
    </div>
  </div>

  <?php else: ?>
  <div style="text-align:center;background:var(--bg-card);padding:4rem;border-radius:16px;border:1px solid var(--glass-border)">
    <p style="font-size:3rem;margin-bottom:.5rem">🛒</p>
    <p style="color:var(--text-secondary);font-size:1.2rem;margin-bottom:1.5rem">Your cart is empty</p>
    <a href="/products" class="btn btn-primary">Browse Products</a>
  </div>
  <?php endif ?>
</div>
<?php require 'views/layouts/footer.php'; ?>
