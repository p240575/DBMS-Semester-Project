<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; padding-bottom: 5rem; max-width: 1200px; margin-left: auto; margin-right: auto;">
    <div style="text-align: center; margin-bottom: 5rem;">
        <h1 style="font-size: 4rem; color: white; margin-bottom: 1rem; background: linear-gradient(to right, var(--accent-primary), var(--accent-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Why NexShop?</h1>
        <p style="font-size: 1.5rem; color: var(--text-secondary); max-width: 800px; margin: 0 auto;">Redefining the standard of luxury e-commerce. We don't just sell products; we deliver an uncompromising lifestyle experience.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; margin-bottom: 5rem;">
        <div style="background: var(--bg-card); padding: 3rem; border-radius: 16px; border: 1px solid var(--glass-border); text-align: center; transition: transform 0.3s; cursor: default;" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 4rem; margin-bottom: 1rem;">💎</div>
            <h3 style="color: white; font-size: 1.8rem; margin-bottom: 1rem;">Unrivaled Authenticity</h3>
            <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.8;">Every single item in our catalog passes through a rigorous 5-step authentication process. We partner directly with premium manufacturers to ensure that you only receive 100% genuine, top-tier products.</p>
        </div>
        
        <div style="background: var(--bg-card); padding: 3rem; border-radius: 16px; border: 1px solid var(--glass-border); text-align: center; transition: transform 0.3s; cursor: default;" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🚀</div>
            <h3 style="color: white; font-size: 1.8rem; margin-bottom: 1rem;">Lightning-Fast Delivery</h3>
            <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.8;">Time is your most valuable asset. Our intelligent logistics network guarantees that your premium items are shipped securely and arrive at your doorstep faster than industry standards.</p>
        </div>

        <div style="background: var(--bg-card); padding: 3rem; border-radius: 16px; border: 1px solid var(--glass-border); text-align: center; transition: transform 0.3s; cursor: default;" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🛡️</div>
            <h3 style="color: white; font-size: 1.8rem; margin-bottom: 1rem;">Ironclad Security</h3>
            <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.8;">Your peace of mind is our priority. With military-grade encryption for all transactions and a strictly enforced privacy policy, shopping with NexShop means your data is untouchable.</p>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.9)); padding: 4rem; border-radius: 24px; border: 1px solid var(--glass-border); display: flex; flex-wrap: wrap; gap: 3rem; align-items: center;">
        <div style="flex: 1; min-width: 300px;">
            <h2 style="font-size: 2.5rem; color: white; margin-bottom: 1.5rem;">The NexShop Promise</h2>
            <p style="color: var(--text-secondary); font-size: 1.2rem; line-height: 1.8; margin-bottom: 2rem;">We built this platform for those who demand excellence. From our impeccably designed interface to our white-glove customer support, every touchpoint is engineered for perfection. When you choose NexShop, you are joining an exclusive club of over 10,000 discerning individuals who never settle for second best.</p>
            <a href="/products" class="btn btn-primary btn-large">Experience the Collection</a>
        </div>
        <div style="flex: 1; min-width: 300px; text-align: center;">
            <img src="https://images.unsplash.com/photo-1542204165-65bf26472b9b?w=600&q=80" style="width: 100%; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        </div>
    </div>

    <section class="featured-section" style="margin-top: 8rem; margin-bottom: -3rem; background: var(--bg-card); padding: 4rem 2rem; border-radius: 16px; border: 1px solid var(--glass-border);">
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
</div>
<?php require 'views/layouts/footer.php'; ?>
