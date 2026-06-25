<?php
require_once 'config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Fetch all licenses
$stmt = $pdo->query("
    SELECT l.*, u.name, u.email 
    FROM licenses l 
    JOIN users u ON l.user_id = u.id 
    ORDER BY CASE WHEN l.status = 'temporary' THEN 1 ELSE 2 END, l.created_at DESC
");
$licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="bg-gradient"></div>
    
    <nav>
        <a href="index.php" class="logo"><?= APP_NAME ?> Admin</a>
        <div class="nav-links">
            <a href="dashboard.php" class="btn btn-outline" style="margin-right: 10px;">User View</a>
            <a href="api/logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>License Management</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="margin-top: 1rem;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-top: 2rem; width: 100%; text-align: left;">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Plan</th>
                            <th>Billing</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($licenses as $lic): ?>
                        <tr>
                            <td><?= htmlspecialchars($lic['name']) ?></td>
                            <td><?= htmlspecialchars($lic['email']) ?></td>
                            <td><span style="text-transform: capitalize;"><?= $lic['plan_type'] ?></span></td>
                            <td><span style="text-transform: capitalize;"><?= $lic['billing_cycle'] ?></span></td>
                            <td>
                                <span class="status-badge status-<?= $lic['status'] ?>">
                                    <?= ucfirst($lic['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($lic['created_at'])) ?></td>
                            <td>
                                <?php if($lic['status'] === 'temporary' || $lic['status'] === 'cancelled'): ?>
                                    <form action="api/admin_license_action.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="license_id" value="<?= $lic['id'] ?>">
                                        <input type="hidden" name="action" value="active">
                                        <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">Activate</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if($lic['status'] === 'temporary' || $lic['status'] === 'active'): ?>
                                    <form action="api/admin_license_action.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="license_id" value="<?= $lic['id'] ?>">
                                        <input type="hidden" name="action" value="cancelled">
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.8rem; font-size: 0.8rem; color: #ef4444; border-color: #ef4444;" onclick="return confirm('Are you sure you want to cancel this license?');">Cancel</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($licenses)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">No licenses found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
