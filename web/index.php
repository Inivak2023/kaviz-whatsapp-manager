<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - The Ultimate WhatsApp Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="bg-gradient"></div>

    <nav>
        <a href="index.php" class="logo"><?= APP_NAME ?></a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#pricing">Pricing</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
                <a href="api/logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <h1>Supercharge Your WhatsApp Marketing</h1>
        <p>Manage unlimited WhatsApp accounts, automate messages, and scale your business with the most powerful platform built for professionals.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">Start For Free</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">Go to Dashboard</a>
        <?php endif; ?>
    </header>

    <section id="pricing" class="pricing">
        <div class="pricing-header">
            <h2>Simple, Transparent Pricing</h2>
            <p style="color: var(--text-muted); margin-top: 1rem;">Choose the plan that fits your needs.</p>
        </div>

        <div class="pricing-cards">
            <!-- Free Plan -->
            <div class="card">
                <h3>Kaviz Free</h3>
                <div class="price">Rs. 0<span>/lifetime</span></div>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Perfect for getting started.</p>
                <ul class="features">
                    <li><i class="fas fa-check-circle"></i> 1 WhatsApp Account</li>
                    <li><i class="fas fa-check-circle"></i> Basic Messaging</li>
                    <li><i class="fas fa-check-circle"></i> Standard Support</li>
                </ul>
                <a href="register.php?plan=free" class="btn btn-outline" style="width: 100%;">Choose Free</a>
            </div>

            <!-- Pro Plan -->
            <div class="card pro">
                <h3>Kaviz Pro</h3>
                <div class="price">Rs. 250<span>/mo</span></div>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Or Rs. 2500/year (Save 16%)</p>
                <ul class="features">
                    <li><i class="fas fa-star"></i> <strong>Unlimited</strong> WhatsApp Accounts</li>
                    <li><i class="fas fa-star"></i> Advanced Automation</li>
                    <li><i class="fas fa-star"></i> Priority Support</li>
                    <li><i class="fas fa-star"></i> API Access</li>
                </ul>
                <a href="register.php?plan=pro" class="btn btn-primary" style="width: 100%;">Upgrade to Pro</a>
            </div>
        </div>
    </section>

    <footer style="text-align: center; padding: 3rem; color: var(--text-muted); border-top: 1px solid var(--glass-border); margin-top: 4rem;">
        <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
    </footer>
</body>
</html>
