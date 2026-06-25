<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($user['name']) . '!';
        
        if ($user['role'] === 'admin') {
            redirect('../admin.php');
        } else {
            redirect('../dashboard.php');
        }
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
        redirect('../login.php');
    }
} else {
    redirect('../login.php');
}
