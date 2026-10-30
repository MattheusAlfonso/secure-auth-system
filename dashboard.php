<?php
require_once 'security.php';

send_security_headers();
start_secure_session();
require_auth();

$minutes_left = (int)ceil((ABSOLUTE_TIMEOUT - (time() - $_SESSION['created_at'])) / 60);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Restrito - Área Segura</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2> Painel de Controle Interno</h2>
        <p>Acesso Autorizado para o Operador: <strong class="highlight"><?= e($_SESSION['username']) ?></strong></p>
        <div class="alert success">
            ✓ Autenticação validada via Hash password_verify() e proteção contra Session Fixation ativa!
        </div>
        <div class="credentials-box">
            <p><strong>Status da Sessão:</strong></p>
            <!-- O Session ID é um segredo: exibimos só uma impressão digital (hash truncado), nunca o valor real. -->
            <p><strong>Fingerprint da Sessão:</strong> <code><?= e(substr(hash('sha256', session_id()), 0, 12)) ?></code></p>
            <p><strong>Expira em:</strong> <?= $minutes_left ?> min (ou 5 min de inatividade)</p>
            <p><strong>Status do Controle de Acesso:</strong> Protegido contra navegação direta via URL.</p>
        </div><br>
        <form action="logout.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="btn btn-danger">Encerrar Sessão Segura</button>
        </form>
    </div>
</body>
</html>
