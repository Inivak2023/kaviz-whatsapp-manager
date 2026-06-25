<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $plan = $_POST['plan'];
    $billing_cycle = isset($_POST['billing_cycle']) ? $_POST['billing_cycle'] : 'monthly';
    $user_id = $_SESSION['user_id'];

    if ($plan === 'pro') {
        // Create a temporary license or upgrade existing free to temporary pro
        $stmt = $pdo->prepare("INSERT INTO licenses (user_id, plan_type, billing_cycle, status) VALUES (?, ?, ?, 'temporary')");
        if ($stmt->execute([$user_id, $plan, $billing_cycle])) {
            $_SESSION['success'] = 'Your Pro request has been submitted. Please send the payment receipt to activate.';
        } else {
            $_SESSION['error'] = 'Failed to process upgrade. Please try again.';
        }
    }
    
    redirect('../dashboard.php');
} else {
    redirect('../index.php');
}
