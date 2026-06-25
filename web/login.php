<?php
require_once 'config.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="bg-gradient"></div>
    
    <div class="auth-container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="api/login_action.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Log In</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
                Don't have an account? <a href="register.php" style="color: var(--primary-color);">Sign up</a>
            </p>
        </div>
    </div>
</body>
</html>
