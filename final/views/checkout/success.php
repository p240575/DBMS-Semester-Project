<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); padding: 4rem; border-radius: 16px; border: 1px solid var(--glass-border); text-align: center; max-width: 600px;">
        <div style="font-size: 5rem; margin-bottom: 1rem;">🎉</div>
        <h1 style="color: var(--success); margin-bottom: 1rem; font-size: 2.5rem;">Purchase Complete!</h1>
        <p style="color: var(--text-secondary); font-size: 1.2rem; margin-bottom: 2rem; line-height: 1.6;">
            Thank you for shopping with NexShop. Your order has been successfully placed and is now being processed. 
            <br><br>
            <strong style="color: white; font-size: 1.4rem;">Estimated Delivery: 3 to 5 Business Days</strong>
        </p>
        <a href="/products" class="btn btn-primary btn-large">Continue Shopping</a>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
