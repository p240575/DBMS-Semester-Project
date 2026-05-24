<?php require 'views/layouts/header.php'; ?>
<div class="featured-section" style="margin-top: 100px; padding-bottom: 5rem; max-width: 1200px; margin-left: auto; margin-right: auto;">
    <div class="section-header">
        <h2>My Info</h2>
        <div class="accent-line"></div>
    </div>
    
    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        <!-- Personal Details -->
        <div style="flex: 1; min-width: 300px; background: var(--bg-card); padding: 2rem; border-radius: 16px; border: 1px solid var(--glass-border); height: fit-content;">
            <h3 style="color: var(--accent-primary); margin-bottom: 1.5rem;">Personal Details</h3>
            <div style="margin-bottom: 1rem;">
                <label style="color: var(--text-secondary); font-size: 0.9rem;">Full Name</label>
                <p style="color: white; font-size: 1.2rem; font-weight: bold;"><?= htmlspecialchars($data['user']['user_name'] ?? 'N/A') ?></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="color: var(--text-secondary); font-size: 0.9rem;">Email Address</label>
                <p style="color: white; font-size: 1.1rem;"><?= htmlspecialchars($data['user']['email'] ?? 'N/A') ?></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="color: var(--text-secondary); font-size: 0.9rem;">Registered Phone</label>
                <p style="color: white; font-size: 1.1rem;"><?= htmlspecialchars($data['user']['phone'] ?? 'N/A') ?></p>
            </div>
        </div>

        <!-- Wallet -->
        <div style="flex:1; min-width:280px; background:linear-gradient(135deg,rgba(16,185,129,0.15),rgba(16,185,129,0.05)); padding:2rem; border-radius:16px; border:1px solid #10b981; height:fit-content;">
            <h3 style="color:#34d399; margin-bottom:0.5rem;">💰 NexShop Wallet</h3>
            <p style="color:#64748b; font-size:0.85rem; margin-bottom:1.5rem;">Refunds and credits added here automatically.</p>
            <div style="font-size:2.5rem; font-weight:800; color:#34d399; margin-bottom:1rem;">Rs <?= number_format($data['user']['wallet_balance'] ?? 0, 2) ?></div>
            <p style="color:#64748b; font-size:0.82rem;">Available balance. Used automatically on next order.</p>
            <a href="/user/purchases" style="display:inline-block; margin-top:1rem; background:#10b981; color:white; padding:7px 18px; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600;">View Orders →</a>
        </div>

        <!-- Notifications -->
        <div style="flex: 1; min-width: 300px; background: var(--bg-card); padding: 2rem; border-radius: 16px; border: 1px solid var(--glass-border); height: fit-content;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 style="color: var(--accent-primary); margin:0; display:flex; align-items:center; gap:.5rem;">
                    🔔 Notifications
                    <?php if(($data['unread_notifications']??0) > 0): ?>
                        <span style="background:#ef4444; color:white; font-size:.7rem; padding:1px 7px; border-radius:10px; font-weight:bold;"><?= $data['unread_notifications'] ?> new</span>
                    <?php endif; ?>
                </h3>
                <a href="/user/notifications" style="color:#f59e0b; font-size:0.82rem; text-decoration:none; font-weight:600;">View All →</a>
            </div>
            <?php if(!empty($data['notifications'])): ?>
                <?php foreach($data['notifications'] as $notif): ?>
                    <div style="padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.07); margin-bottom: 0.8rem;">
                        <p style="color: white; font-size:0.9rem; line-height:1.5; margin:0;"><?= htmlspecialchars($notif['message']) ?></p>
                        <small style="color: var(--text-secondary);"><?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
                <?php if(($data['total_notifications']??0) > 4): ?>
                    <a href="/user/notifications" style="display:block; text-align:center; margin-top:0.5rem; color:#f59e0b; text-decoration:none; font-size:0.88rem; font-weight:600;">View All <?=$data['total_notifications']?> Notifications →</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: var(--text-secondary);">No notifications yet.</p>
            <?php endif; ?>
        </div>

        <!-- Addresses -->
        <div style="flex: 2; min-width: 300px;">
            <h3 style="color: white; margin-bottom: 1.5rem;">My Addresses</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <?php if(!empty($data['addresses'])): ?>
                    <?php foreach($data['addresses'] as $addr): ?>
                        <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                            <?php if($addr['is_default']): ?>
                                <span style="position: absolute; top: 1rem; right: 1rem; background: var(--success); color: #0f172a; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px; font-weight: bold;">Default</span>
                            <?php endif; ?>
                            <h4 style="color: white; margin-bottom: 0.5rem;"><?= htmlspecialchars($addr['full_name']) ?></h4>
                            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">📞 <?= htmlspecialchars($addr['phone']) ?></p>
                            <p style="color: var(--text-secondary); line-height: 1.5;"><?= htmlspecialchars($addr['address_line']) ?><br>
                            <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['province']) ?> <?= htmlspecialchars($addr['zipcode']) ?></p>
                            <?php if(count($data['addresses']) > 1): ?>
                            <div style="position: absolute; bottom: 1rem; right: 1rem;">
                                <form action="/user/deleteAddress/<?= $addr['address_id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                    <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 0.3rem 0.8rem; border-radius: 8px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='var(--danger)'; this.style.color='white';" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='var(--danger)';">Delete</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-secondary);">No addresses found.</p>
                <?php endif; ?>
            </div>

            <!-- Add Address Form -->
            <div style="background: var(--bg-card); padding: 2rem; border-radius: 16px; border: 1px solid var(--glass-border);">
                <h3 style="color: var(--accent-secondary); margin-bottom: 1.5rem;">Add New Address</h3>
                <form action="/user/addAddress" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label style="color: white;">Full Name</label>
                        <input type="text" name="full_name" required class="form-control" placeholder="John Doe">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label style="color: white;">Phone Number</label>
                        <input type="text" name="phone" required class="form-control" placeholder="+1234567890">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label style="color: white;">Address Line</label>
                        <input type="text" name="address_line" required class="form-control" placeholder="123 Street Name">
                    </div>
                    <div class="form-group">
                        <label style="color: white;">City</label>
                        <input type="text" name="city" required class="form-control" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label style="color: white;">Zipcode</label>
                        <input type="text" name="zipcode" required class="form-control" placeholder="Zip">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label style="color: white;">Province</label>
                        <select name="province" required class="form-control" style="background: rgba(0,0,0,0.2); color: white;">
                            <option value="Punjab">Punjab</option>
                            <option value="Sindh">Sindh</option>
                            <option value="Khyber Pakhtunkhwa">Khyber Pakhtunkhwa</option>
                            <option value="Balochistan">Balochistan</option>
                            <option value="Islamabad Capital Territory">Islamabad Capital Territory</option>
                            <option value="Gilgit Baltistan">Gilgit Baltistan</option>
                            <option value="Azad Kashmir">Azad Kashmir</option>
                        </select>
                    </div>
                    <div style="grid-column: 1 / -1; margin-top: 1rem;">
                        <button type="submit" class="btn btn-outline" style="width: 100%;">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
