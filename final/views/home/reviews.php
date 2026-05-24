<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; padding-bottom: 5rem; max-width: 1200px; margin-left: auto; margin-right: auto;">
    <div class="section-header">
        <h2>All Customer Reviews</h2>
        <div class="accent-line"></div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <?php if(!empty($data['reviews'])): ?>
            <?php foreach($data['reviews'] as $r): ?>
                <div style="background: var(--bg-card); padding: 2rem; border-radius: 12px; border: 1px solid var(--glass-border); transition: transform 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='var(--accent-primary)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='var(--glass-border)';">
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
                    <p style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 1rem;">"<?= htmlspecialchars($r['comment']) ?>"</p>
                    <p style="color: var(--accent-secondary); font-size: 0.9rem;">Purchased: <a href="/products/show/<?= $r['product_id'] ?>" style="color: inherit; text-decoration: underline;"><?= htmlspecialchars($r['product_name']) ?></a></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-secondary); text-align: center; grid-column: 1 / -1;">No reviews found.</p>
        <?php endif; ?>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
