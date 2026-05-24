<?php require 'views/layouts/header.php'; ?>
<div class="auth-container" style="margin-top: 100px;">
    <div class="auth-card" style="max-width: 600px;">
        <h2>Create Full Account</h2>
        <?php if(isset($data['error'])): ?>
            <p class="error"><?= $data['error'] ?></p>
        <?php endif; ?>
        <form method="POST" action="/auth/register">
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Full Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" required class="form-control" placeholder="John Doe">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Email <span style="color:var(--danger)">*</span></label>
                    <input type="email" name="email" required class="form-control" placeholder="example@gmail.com">
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Phone <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="phone" required class="form-control" placeholder="+1234567890">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" required class="form-control" placeholder="••••••••">
                </div>
            </div>
            
            <h3 style="margin: 1.5rem 0 1rem; color: var(--accent-primary);">Address Details</h3>
            <div class="form-group">
                <label>Address Line <span style="color:var(--danger)">*</span></label>
                <input type="text" name="address_line" required class="form-control" placeholder="123 Street Name, Apt 4B">
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>City <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="city" required class="form-control" placeholder="Lahore">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Province <span style="color:var(--danger)">*</span></label>
                    <select name="province" required class="form-control" style="background-color: #1e293b;">
                        <option value="Punjab">Punjab</option>
                        <option value="Sindh">Sindh</option>
                        <option value="Khyber Pakhtunkhwa">Khyber Pakhtunkhwa</option>
                        <option value="Balochistan">Balochistan</option>
                        <option value="Islamabad Capital Territory">Islamabad</option>
                        <option value="Gilgit Baltistan">Gilgit Baltistan</option>
                        <option value="Azad Kashmir">Azad Kashmir</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Zipcode</label>
                    <input type="text" name="zipcode" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register Full Account</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="/auth/login" style="color: var(--accent-secondary);">Login</a></p>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
