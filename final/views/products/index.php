<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px;">
    <div class="section-header">
        <h2><?= htmlspecialchars($data['title'] ?? 'All Products') ?></h2>
        <div class="accent-line"></div>
    </div>
    
    <?php if(isset($data['categories'])): ?>
    <div class="category-filters" style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="/products" class="btn <?= empty($data['current_category']) ? 'btn-primary' : 'btn-outline' ?>" style="border-radius: 20px;">All Products</a>
        <?php foreach($data['categories'] as $cat): ?>
            <a href="/products?category=<?= $cat['category_id'] ?>" class="btn <?= ($data['current_category'] == $cat['category_id']) ? 'btn-primary' : 'btn-outline' ?>" style="border-radius: 20px;">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="category-grid">
        <?php if(!empty($data['products'])): ?>
            <?php foreach($data['products'] as $p): ?>
                <div class="category-card" style="padding: 1rem; text-align: left;">
                    <img src="<?= htmlspecialchars($p['image_url'] ?? '/assets/img/default.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 1rem; aspect-ratio: 1; object-fit: cover;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: white;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: var(--accent-secondary); font-weight: bold; font-size: 1.1rem; margin-bottom: 1rem;">Rs <?= number_format($p['price'] ?? 0, 2) ?></p>
                    <a href="/products/show/<?= $p['product_id'] ?>" class="btn btn-primary" style="width: 100%">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
