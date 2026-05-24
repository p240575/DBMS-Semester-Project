<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; padding-bottom: 5rem;">
    <?php if(!empty($data['product'])): $p = $data['product']; ?>
        <div style="display: flex; gap: 4rem; flex-wrap: wrap; background: var(--bg-card); padding: 3rem; border-radius: 16px; border: 1px solid var(--glass-border);">
            <div style="flex: 1; min-width: 300px;">
                <img src="<?= htmlspecialchars($p['image_url'] ?? '/assets/img/default.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; border-radius: 12px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            </div>
            <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: white;"><?= htmlspecialchars($p['name']) ?></h1>
                
                <?php if($data['rating']['review_count'] > 0): ?>
                    <div style="margin-bottom: 1rem; color: #fbbf24; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span><?= str_repeat('★', round($data['rating']['avg_rating'])) ?><?= str_repeat('☆', 5 - round($data['rating']['avg_rating'])) ?></span>
                        <span style="color: var(--text-secondary); font-size: 0.9rem;">(<?= round($data['rating']['avg_rating'], 1) ?> / 5 from <?= $data['rating']['review_count'] ?> reviews)</span>
                    </div>
                <?php endif; ?>

                <p id="product-price" data-base-price="<?= $p['price'] ?? 0 ?>" style="color: var(--accent-secondary); font-size: 2rem; font-weight: 800; margin-bottom: 1.5rem;">Rs <?= number_format($p['price'] ?? 0, 2) ?></p>
                
                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 0.5rem; color: white;">Description</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8; font-size: 1.1rem;"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 0.5rem; color: white;">Specs & Features</h3>
                    <ul style="color: var(--text-secondary); line-height: 1.8; padding-left: 20px;">
                        <li>Premium build quality ensuring long-lasting durability.</li>
                        <li>High-performance materials for a truly luxurious feel.</li>
                        <li>Designed ergonomically to seamlessly fit into your daily life.</li>
                        <li>100% authentic NexShop official certification.</li>
                    </ul>
                </div>
                
                <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem;">
                    <span id="stock-badge" data-base-stock="<?= $p['stock'] ?>" style="padding: 0.5rem 1rem; background: rgba(16, 185, 129, 0.1); color: var(--success); border-radius: 20px; font-weight: 600;">
                        <?= $p['stock'] > 0 ? 'In Stock (' . $p['stock'] . ' available)' : 'Out of Stock' ?>
                    </span>
                    <span style="padding: 0.5rem 1rem; background: rgba(124, 58, 237, 0.1); color: var(--accent-primary); border-radius: 20px; font-weight: 600;">
                        ✔ Best for Official Use
                    </span>
                </div>

                <form action="/cart/add" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                    
                    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                        <div>
                            <label style="color: white; margin-bottom: 0.5rem; display: block; font-weight: bold;">Color</label>
                            <select id="color-select" name="color" onchange="updateVariantDetails()" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 8px; font-size: 1rem; min-width: 150px;">
                                <option value="Black">Midnight Black</option>
                                <option value="White">Pearl White</option>
                                <option value="Blue">Ocean Blue</option>
                                <option value="Red">Crimson Red</option>
                            </select>
                        </div>
                        
                        <?php if($data['has_size']): ?>
                        <div>
                            <label style="color: white; margin-bottom: 0.5rem; display: block; font-weight: bold;">Size</label>
                            <select id="size-select" name="size" onchange="updateVariantDetails()" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 8px; font-size: 1rem; min-width: 150px;">
                                <option value="S" data-price-add="0">Small</option>
                                <option value="M" data-price-add="500" selected>Medium (+ Rs 500)</option>
                                <option value="L" data-price-add="1000">Large (+ Rs 1000)</option>
                                <option value="XL" data-price-add="1500">Extra Large (+ Rs 1500)</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <input type="number" id="quantity-input" name="quantity" value="1" min="1" max="<?= $p['stock'] ?>" style="width: 80px; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: white; border-radius: 8px; padding: 0.5rem; text-align: center; font-size: 1.1rem;">
                        <button type="submit" id="add-to-cart-btn" class="btn btn-primary btn-large" style="flex: 1;" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div style="margin-top: 4rem; background: var(--bg-card); padding: 3rem; border-radius: 16px; border: 1px solid var(--glass-border);">
            <h2 style="color: white; margin-bottom: 2rem; font-size: 2rem;">Customer Reviews</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                <?php if(!empty($data['reviews'])): ?>
                    <?php foreach($data['reviews'] as $r): ?>
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <?php if($r['user_image']): ?>
                                    <img src="<?= htmlspecialchars($r['user_image']) ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; color: #0f172a;">
                                        <?= strtoupper(substr($r['user_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 style="color: white; margin: 0;"><?= htmlspecialchars($r['user_name']) ?></h4>
                                    <div style="color: #fbbf24; font-size: 1rem;">
                                        <?= str_repeat('★', $r['rating']) ?><?= str_repeat('☆', 5 - $r['rating']) ?>
                                    </div>
                                </div>
                            </div>
                            <p style="color: var(--text-secondary); line-height: 1.6; font-style: italic;">"<?= htmlspecialchars($r['comment']) ?>"</p>
                            <?php if(!empty($r['reply'])): ?>
                                <div style="margin-top:0.8rem; background:rgba(245,158,11,0.08); border-left:3px solid #f59e0b; padding:0.6rem 1rem; border-radius:0 8px 8px 0;">
                                    <p style="color:#94a3b8; font-size:0.75rem; margin-bottom:0.2rem; text-transform:uppercase; letter-spacing:0.05em;">🛡 NexShop Official Reply</p>
                                    <p style="color:#cbd5e1; font-size:0.9rem; margin:0;"><?= htmlspecialchars($r['reply']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-secondary);">No reviews yet. Be the first to review this product!</p>
                <?php endif; ?>
            </div>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0;">
            
            <h3 style="color: white; margin-bottom: 1.5rem;">Write a Review</h3>
            <form action="/products/addReview" method="POST" style="max-width: 600px;">
                <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                <div class="form-group">
                    <label style="color: white; display: block; margin-bottom: 0.5rem;">Rating</label>
                    <select name="rating" required style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 8px; font-size: 1rem; width: 100%;">
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Terrible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="color: white; display: block; margin-bottom: 0.5rem;">Review Comment</label>
                    <textarea name="comment" required rows="4" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 8px; font-size: 1rem; width: 100%; resize: vertical;" placeholder="Tell us what you think..."></textarea>
                </div>
                <button type="submit" class="btn btn-outline">Submit Review</button>
            </form>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 5rem;">
            <h2>Product not found</h2>
            <a href="/products" class="btn btn-outline mt-3">Back to Products</a>
        </div>
    <?php endif; ?>
</div>

<script>
function updateVariantDetails() {
    const priceEl = document.getElementById('product-price');
    const basePrice = parseFloat(priceEl.getAttribute('data-base-price'));
    const sizeSelect = document.getElementById('size-select');
    
    let priceAdd = 0;
    if (sizeSelect) {
        priceAdd = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].getAttribute('data-price-add') || 0);
    }
    
    const finalPrice = basePrice + priceAdd;
    priceEl.innerText = 'Rs ' + finalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Simulate dynamic stock variation
    const colorSelect = document.getElementById('color-select');
    const color = colorSelect ? colorSelect.value : '';
    const size = sizeSelect ? sizeSelect.value : '';
    
    const stockBadge = document.getElementById('stock-badge');
    const baseStock = parseInt(stockBadge.getAttribute('data-base-stock'));
    
    // A simple deterministic pseudo-random formula to vary stock
    let stockVariant = baseStock - (color.length * 2) - (size === 'XL' ? 10 : (size === 'L' ? 5 : 0));
    if (stockVariant < 0) stockVariant = 0;
    
    if (stockVariant > 0) {
        stockBadge.innerHTML = 'In Stock (' + stockVariant + ' available)';
        stockBadge.style.color = 'var(--success)';
        stockBadge.style.background = 'rgba(16, 185, 129, 0.1)';
        document.getElementById('quantity-input').max = stockVariant;
        document.getElementById('add-to-cart-btn').disabled = false;
    } else {
        stockBadge.innerHTML = 'Out of Stock';
        stockBadge.style.color = 'var(--danger)';
        stockBadge.style.background = 'rgba(239, 68, 68, 0.1)';
        document.getElementById('add-to-cart-btn').disabled = true;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', updateVariantDetails);
</script>

<?php require 'views/layouts/footer.php'; ?>
