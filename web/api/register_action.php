<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $plan = $_POST['plan'];
    $billing_cycle = isset($_POST['billing_cycle']) ? $_POST['billing_cycle'] : 'lifetime';

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        redirect('../register.php?plan=' . $plan);
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email is already registered.';
        redirect('../register.php?plan=' . $plan);
    }

    // Hash password and create user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$name, $email, $hashed_password])) {
        $user_id = $pdo->lastInsertId();
        
        // Create license
        $status = ($plan === 'pro') ? 'temporary' : 'active';
        $actual_billing = ($plan === 'pro') ? $billing_cycle : 'lifetime';
        
        $stmt_lic = $pdo->prepare("INSERT INTO licenses (user_id, plan_type, billing_cycle, status) VALUES (?, ?, ?, ?)");
        $stmt_lic->execute([$user_id, $plan, $actual_billing, $status]);

        // Auto login
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'user';
        $_SESSION['success'] = 'Account created successfully!';
        
        redirect('../dashboard.php');
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        redirect('../register.php?plan=' . $plan);
    }
} else {
    redirect('../register.php');
}
