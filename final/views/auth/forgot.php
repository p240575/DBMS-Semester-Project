<?php require 'views/layouts/header.php'; ?>
<div class="auth-container" style="margin-top: 100px;">
    <div class="auth-card">
        <h2>Reset Password</h2>
        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">Enter your email to change your password.</p>
        <?php if(isset($data['error'])): ?>
            <p class="error" style="color: var(--danger); text-align: center; margin-bottom: 1rem;"><?= $data['error'] ?></p>
        <?php endif; ?>
        <form method="POST" action="/auth/forgot">
            <div class="form-group">
                <label>Registered Email</label>
                <input type="email" name="email" required class="form-control" placeholder="example@gmail.com">
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required class="form-control" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Change Password</button>
        </form>
        <p class="text-center mt-3"><a href="/auth/login" style="color: var(--accent-secondary);">Back to Login</a></p>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
