<?php require 'views/layouts/header.php'; ?>

<div class="hero">
    <div class="hero-content">
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <h1 class="hero-title">Welcome Admin</h1>
            <p class="hero-subtitle">Busy schedule? You're still doing good! Keep up the good work managing the platform.</p>
        <?php else: ?>
            <h1 class="hero-title">Experience <br>Unrivaled Luxury</h1>
            <p class="hero-subtitle">Premium quality products curated just for you. Seamless shopping with NexShop. Join our community of over 10,000 satisfied customers enjoying top-tier products, secure payments, and lightning-fast worldwide delivery.</p>
        <?php endif; ?>
        <div class="hero-actions">
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/admin" class="btn btn-primary btn-large">Go to Dashboard</a>
            <?php else: ?>
                <a href="/products" class="btn btn-primary btn-large">Shop Now</a>
                <a href="#featured" class="btn btn-outline btn-large">Explore Categories</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-collage">
        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80" alt="Luxury Item 1" class="collage-img img-1">
        <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=500&q=80" alt="Luxury Item 2" class="collage-img img-2">
        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80" alt="Luxury Item 3" class="collage-img img-3">
        <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&q=80" alt="Luxury Item 4" class="collage-img img-4">
        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80" alt="Luxury Item 5" class="collage-img img-5">
    </div>
</div>

<section id="featured" class="featured-section">
    <div class="section-header">
        <h2>Featured Categories</h2>
        <div class="accent-line"></div>
    </div>
    <div class="category-grid">
        <a href="/category/show/1" class="category-card cat-bg" style="background-image: url('/assets/img/cat_electronics.png');">
            <div class="cat-overlay">
                <h3>Electronics</h3>
            </div>
        </a>
        <a href="/category/show/2" class="category-card cat-bg" style="background-image: url('/assets/img/cat_mens_fashion.png');">
            <div class="cat-overlay">
                <h3>Men's Fashion</h3>
            </div>
        </a>
        <a href="/category/show/3" class="category-card cat-bg" style="background-image: url('/assets/img/cat_womens_fashion.png');">
            <div class="cat-overlay">
                <h3>Women's Fashion</h3>
            </div>
        </a>
        <a href="/category/show/4" class="category-card cat-bg" style="background-image: url('/assets/img/cat_home_office.png');">
            <div class="cat-overlay">
                <h3>Home & Office</h3>
            </div>
        </a>
    </div>
</section>

<section class="featured-section">
    <div class="section-header">
        <h2>Best Selling Products</h2>
        <div class="accent-line"></div>
    </div>
    <div class="category-grid">
        <?php if(!empty($data['bestselling'])): ?>
            <?php foreach($data['bestselling'] as $p): ?>
                <div class="category-card" style="padding: 1rem; text-align: left;">
                    <img src="<?= htmlspecialchars($p['image_url'] ?? '/assets/img/default.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 1rem; aspect-ratio: 1; object-fit: cover;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: white;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: var(--accent-secondary); font-weight: bold; font-size: 1.1rem; margin-bottom: 1rem;">Rs <?= number_format($p['price'] ?? 0, 2) ?></p>
                    <a href="/products/show/<?= $p['product_id'] ?>" class="btn btn-primary" style="width: 100%">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 3rem;">
        <a href="/products/bestselling" class="btn btn-outline btn-large">View All Best Sellers</a>
    </div>
</section>

<section class="featured-section">
    <div class="section-header">
        <h2>Discounted Products</h2>
        <div class="accent-line"></div>
    </div>
    <div class="category-grid">
        <?php if(!empty($data['discounted'])): ?>
            <?php foreach($data['discounted'] as $p): ?>
                <div class="category-card" style="padding: 1rem; text-align: left;">
                    <img src="<?= htmlspecialchars($p['image_url'] ?? '/assets/img/default.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 1rem; aspect-ratio: 1; object-fit: cover;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: white;"><?= htmlspecialchars($p['name']) ?></h3>
                    <p style="color: var(--accent-secondary); font-weight: bold; font-size: 1.1rem; margin-bottom: 1rem;">Rs <?= number_format($p['price'] ?? 0, 2) ?> <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.9rem;">Rs <?= number_format(($p['price'] ?? 0) * 1.2, 2) ?></span></p>
                    <a href="/products/show/<?= $p['product_id'] ?>" class="btn btn-primary" style="width: 100%">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 3rem;">
        <a href="/products/discounted" class="btn btn-outline btn-large">View All Discounted</a>
    </div>
</section>

<section class="featured-section" style="margin-bottom: 5rem;">
    <div class="section-header">
        <h2>Style Inspiration</h2>
        <p style="color: var(--text-secondary);">Trending aesthetics from around the globe.</p>
        <div class="accent-line" style="margin-top:1rem;"></div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1445205170230-053b83016050?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1532453288672-3a27e9be9efd?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
        <a href="/products/trending"><img src="https://images.unsplash.com/photo-1492707892479-7bc8d5a4ee93?w=500&q=80" style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"></a>
    </div>
</section>

<style>
.review-card {
    background: var(--bg-card); padding: 2rem; border-radius: 12px; border: 1px solid var(--glass-border); text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}
.review-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(251, 191, 36, 0.2);
    border-color: var(--accent-primary);
}
</style>

<section class="featured-section" style="margin-bottom: 5rem;">
    <div class="section-header">
        <h2>What Our Customers Say</h2>
        <div class="accent-line"></div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"NexShop completely changed the way I shop online. The quality of products is unparalleled and delivery is insanely fast."</p>
            <h4 style="color: white;">- Michael T.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"I've ordered five times already. The customer service and the premium feel of the platform makes it my go-to store."</p>
            <h4 style="color: white;">- Sarah Jenkins</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"Authentic luxury products that never disappoint. Best online shopping experience I've had in a long time."</p>
            <h4 style="color: white;">- David W.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"Absolutely flawless process. Everything from browsing to the unboxing experience is top notch."</p>
            <h4 style="color: white;">- Emily R.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"Highly recommended. Will never shop anywhere else for my premium fashion and electronics."</p>
            <h4 style="color: white;">- James L.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"The attention to detail and customer care is unmatched. Simply the best."</p>
            <h4 style="color: white;">- Olivia C.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★☆</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"Great selection of luxury goods. Delivery was slightly delayed but the quality made up for it."</p>
            <h4 style="color: white;">- Marcus D.</h4>
        </div>
        <div class="review-card">
            <div style="color: #fbbf24; font-size: 1.5rem; margin-bottom: 1rem;">★★★★★</div>
            <p style="color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">"I'm totally obsessed with my new purchases! Will definitely be recommending NexShop to friends."</p>
            <h4 style="color: white;">- Sophia M.</h4>
        </div>
    </div>
    <div style="text-align: center; margin-top: 3rem;">
        <a href="/home/reviews" class="btn btn-outline btn-large">View All Reviews</a>
    </div>
</section>

<section class="featured-section" style="margin-bottom: 5rem; background: var(--bg-card); padding: 4rem 2rem; border-radius: 16px; border: 1px solid var(--glass-border);">
    <h2 style="text-align: center; margin-bottom: 3rem; color: white;">Contact Us</h2>
    <div style="display: flex; gap: 2rem; justify-content: space-around; text-align: center; flex-wrap: wrap;">
        <div>
            <img src="/assets/img/icon_location.png" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 2px solid var(--accent-primary);">
            <h4 style="margin-bottom: 0.5rem; color: white;">Head Office</h4>
            <p style="color: var(--text-secondary);">123 Luxury Avenue<br>Business District, City 54000</p>
        </div>
        <div>
            <img src="/assets/img/icon_whatsapp.png" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 2px solid var(--accent-secondary);">
            <h4 style="margin-bottom: 0.5rem; color: white;">Phone & WhatsApp</h4>
            <p style="color: var(--text-secondary);">+1 (800) 123-NEXSHOP<br>Mon-Fri, 9am - 6pm</p>
        </div>
        <div>
            <img src="/assets/img/icon_email.png" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; border: 2px solid var(--success);">
            <h4 style="margin-bottom: 0.5rem; color: white;">Email Us</h4>
            <p style="color: var(--text-secondary);">support@nexshop.com<br>contact@nexshop.com</p>
        </div>
    </div>
</section>

<?php require 'views/layouts/footer.php'; ?>
