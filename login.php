<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}
$input_user = trim($_POST['username'] ?? '');
$input_pass = $_POST['password'] ?? '';

if ($input_user === $usuarios_db['username'] && password_verify($input_pass, $usuarios_db['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $input_user;
    $_SESSION['last_activity'] = time(); 
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: index.php?error=invalid');
    exit();
}
?>