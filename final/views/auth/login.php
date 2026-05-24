<?php require 'views/layouts/header.php'; ?>
<div class="auth-container" style="margin-top: 100px;">
    <div class="auth-card">
        <h2>Welcome Back</h2>
        <?php if(isset($data['error'])): ?>
            <p class="error" style="color: var(--danger); text-align: center; margin-bottom: 1rem;"><?= $data['error'] ?></p>
        <?php endif; ?>
        <?php if(isset($data['success'])): ?>
            <p class="success" style="color: var(--success); text-align: center; margin-bottom: 1rem;"><?= $data['success'] ?></p>
        <?php endif; ?>
        <form method="POST" action="/auth/login">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" required class="form-control" placeholder="example@gmail.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required class="form-control" placeholder="••••••••">
            </div>
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="/auth/forgot" style="color: var(--accent-secondary); font-size: 0.9rem;">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="/auth/register" style="color: var(--accent-secondary);">Register</a></p>
    </div>
</div>
<?php require 'views/layouts/footer.php'; ?>
