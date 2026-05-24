<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; min-height: 60vh;">
    <div class="section-header">
        <h2><?= htmlspecialchars($data['category']['name'] ?? 'Category') ?> Products</h2>
        <p style="color: var(--text-secondary);"><?= htmlspecialchars($data['category']['description'] ?? '') ?></p>
        <div class="accent-line" style="margin-top: 1rem;"></div>
    </div>
    <div class="category-grid">
        <?php if(!empty($data['products'])): ?>
            <?php foreach($data['products'] as $p): ?>
                <div class="category-card" style="padding: 1rem; text-align: left;">
                    <img src="<?= htmlspecialchars($p['image_url'] ?? '/assets/img/default.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 1rem; aspect-ratio: 1; object-fit: cover;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: white;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: var(--accent-secondary); font-weight: bold; font-size: 1.1rem; margin-bottom: 1rem;">$<?= number_format($p['price'] ?? 0, 2) ?></p>
                    <a href="/products/show/<?= $p['product_id'] ?>" class="btn btn-primary" style="width: 100%">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
