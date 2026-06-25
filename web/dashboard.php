<?php
require_once 'config.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM licenses WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id]);
$license = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="bg-gradient"></div>
    
    <nav>
        <a href="index.php" class="logo"><?= APP_NAME ?></a>
        <div class="nav-links">
            <span style="margin-right: 1rem; color: var(--text-muted);">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <?php if(isAdmin()): ?>
                <a href="admin.php" class="btn btn-outline" style="margin-right: 10px;">Admin Panel</a>
            <?php endif; ?>
            <a href="api/logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>Your Dashboard</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="margin-top: 1rem;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 2rem; margin-top: 2rem; flex-wrap: wrap;">
            <!-- License Info Card -->
            <div class="card" style="flex: 1; text-align: left; min-width: 300px;">
                <h3>Current Plan Details</h3>
                <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 1rem 0;">
                
                <?php if($license): ?>
                    <p style="margin-bottom: 0.5rem;"><strong>Plan:</strong> Kaviz <?= ucfirst($license['plan_type']) ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Billing:</strong> <?= ucfirst($license['billing_cycle']) ?></p>
                    <p style="margin-bottom: 1rem;">
                        <strong>Status:</strong> 
                        <span class="status-badge status-<?= $license['status'] ?>">
                            <?= ucfirst($license['status']) ?>
                        </span>
                    </p>

                    <?php if($license['plan_type'] === 'pro' && $license['status'] === 'temporary'): ?>
                        <div class="alert alert-warning" style="background: rgba(245, 158, 11, 0.1); border: 1px solid #fbbf24; color: #fbbf24; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                            <h4 style="margin-bottom: 0.5rem;"><i class="fas fa-exclamation-triangle"></i> Action Required</h4>
                            <p style="font-size: 0.9rem; margin-bottom: 1rem;">Your Pro license is currently <strong>temporary</strong>. To activate it permanently, please deposit the amount to our bank account and send the receipt via WhatsApp.</p>
                            <a href="https://wa.me/<?= str_replace('+', '', WHATSAPP_NUMBER) ?>?text=Hello%2C%20I%20want%20to%20activate%20my%20Kaviz%20Pro%20account.%20Email:%20<?= urlencode($_SESSION['user_email']) ?>" 
                               target="_blank" class="btn btn-primary" style="display: block; text-align: center; font-size: 0.9rem;">
                                <i class="fab fa-whatsapp"></i> Send Receipt on WhatsApp
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($license['plan_type'] === 'free'): ?>
                        <div style="margin-top: 2rem;">
                            <p style="margin-bottom: 1rem; color: var(--text-muted); font-size: 0.9rem;">Ready for unlimited accounts?</p>
                            <form action="api/subscribe_action.php" method="POST">
                                <input type="hidden" name="plan" value="pro">
                                <select name="billing_cycle" style="width: 100%; padding: 0.8rem; border-radius: 8px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); color: white; margin-bottom: 1rem; outline: none;">
                                    <option value="monthly">Monthly (Rs. 250)</option>
                                    <option value="annual">Annual (Rs. 2500)</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="width: 100%;">Upgrade to Pro</button>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <p style="color: var(--text-muted);">No active plan found.</p>
                <?php endif; ?>
            </div>

            <!-- Features Card -->
            <div class="card" style="flex: 2; text-align: left; min-width: 300px;">
                <h3><?= APP_NAME ?> Manager</h3>
                <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 1rem 0;">
                
                <?php if($license && ($license['status'] === 'active' || $license['status'] === 'temporary')): ?>
                    <div style="text-align: center; padding: 2rem 0;">
                        <i class="fab fa-whatsapp" style="font-size: 4rem; color: #25D366; margin-bottom: 1rem;"></i>
                        <h4>Connect Your WhatsApp</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem; margin-bottom: 2rem;">
                            Scan the QR code to connect your account and start automating.
                        </p>
                        <button class="btn btn-primary">Open Web Client</button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        Your license has been cancelled. Please upgrade to continue using the service.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
