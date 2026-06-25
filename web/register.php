<?php
require_once 'config.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$plan = isset($_GET['plan']) ? $_GET['plan'] : 'free';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="bg-gradient"></div>
    
    <div class="auth-container">
        <div class="auth-card">
            <h2>Create an Account</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="api/register_action.php" method="POST">
                <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="John Doe">
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>

                <?php if($plan === 'pro'): ?>
                <div class="form-group">
                    <label>Billing Cycle for Pro</label>
                    <select name="billing_cycle">
                        <option value="monthly">Monthly (Rs. 250)</option>
                        <option value="annual">Annual (Rs. 2500 - Save 16%)</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Sign Up</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
                Already have an account? <a href="login.php" style="color: var(--primary-color);">Log in</a>
            </p>
        </div>
    </div>
</body>
</html>
