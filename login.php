<?php
require_once 'security.php';

send_security_headers();
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requisição inválida (token CSRF ausente ou incorreto).');
}

if (is_locked_out()) {
    header('Location: index.php?error=locked');
    exit();
}

$input_user = trim((string)($_POST['username'] ?? ''));
$input_pass = (string)($_POST['password'] ?? '');

// password_verify() roda SEMPRE, mesmo com usuário errado. Assim o tempo de resposta
// é o mesmo nos dois casos e o atacante não descobre quais usuários existem (timing attack).
$user_ok = hash_equals($usuarios_db['username'], $input_user);
$pass_ok = password_verify($input_pass, $usuarios_db['password_hash']);

if ($user_ok && $pass_ok) {
    clear_attempts();
    session_regenerate_id(true); // defesa contra Session Fixation
    $_SESSION['authenticated'] = true;
    $_SESSION['username']      = $input_user;
    $_SESSION['created_at']    = time();
    $_SESSION['last_activity'] = time();
    unset($_SESSION['csrf_token']); // novo token após o login
    header('Location: dashboard.php');
    exit();
}

register_failed_attempt();
header('Location: index.php?error=invalid');
exit();
