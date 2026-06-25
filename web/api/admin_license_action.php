<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $license_id = $_POST['license_id'];
    $action = $_POST['action'];

    if (in_array($action, ['active', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE licenses SET status = ? WHERE id = ?");
        if ($stmt->execute([$action, $license_id])) {
            $_SESSION['success'] = 'License status updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update license.';
        }
    }
    
    redirect('../admin.php');
} else {
    redirect('../index.php');
}
