<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location:index.php?error=unauthorized');
    exit();
}

$timeout = 300;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        header('Location: index.php?error=expired');
        exit();
}
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Restrito - Área Segura</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2> Painel de Controle Interno</h2>
        <p>Acesso Autorizado para o Operador: <strong style="color: #38bdf8;"><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <div class="alert success">
            ✓ Autenticação validada via Hash password_verify() e proteção contra Session Fixation ativa!
        </div>
        <div class="credentials-box">
            <p><strong>Status da Sessão:</strong></p>
            <p><strong>Session ID Atual:</strong> <code><?= session_id() ?></code></p>
            <p><strong>Status do Controle de Acesso:</strong> Protegido contra navegação direta via URL.</p>
        </div><br>
        <a href="logout.php" class="btn btn-danger">Encerrar Sessão Segura</a>
    </div>
</body>
</html>