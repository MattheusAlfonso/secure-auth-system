<?php
require_once 'security.php';

send_security_headers();
start_secure_session();

if (($_SESSION['authenticated'] ?? false) === true) {
    header('Location: dashboard.php');
    exit();
}

// Mensagens fixas (allowlist): o valor do parâmetro nunca é refletido na página.
$errors = [
    'invalid'      => 'Usuário ou senha incorretos!',
    'unauthorized' => 'Acesso negado! Faça login primeiro.',
    'expired'      => 'Sessão expirada. Faça login novamente.',
    'locked'       => 'Muitas tentativas falhas. Tente novamente em 15 minutos.',
];
$error_message = $errors[$_GET['error'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto 2 - Sistema de Autenticação Segura</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>🔒 Portal de Acesso Seguro</h2>
        <p>Autenticação com Defesa contra Session Fixation & Broken Access Control</p>
        <!-- 1. BLOCO DE ALERTAS DE ERRO -->
        <?php if ($error_message): ?>
            <div class="alert error"><?= e($error_message) ?></div>
        <?php endif; ?>
        <!-- 2. BLOCO DE SUCESSO DO LOGOUT -->
        <?php if (isset($_GET['logged_out'])): ?>
            <div class="alert success">
                ✓ Sessão encerrada com sucesso e destruída no servidor.
            </div>
        <?php endif; ?>
        <!-- ENVIO SEGURO VIA POST PARA PREVENIR VAZAMENTO NA URL/LOGS -->
        <form action="login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário" required autocomplete="username" maxlength="64">
            </div>

            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required autocomplete="current-password" maxlength="128">
            </div>
            <button type="submit">Autenticar Sistema</button>
        </form>
        <div class="credentials-box">
            <p><strong>Credenciais para Teste de Acesso:</strong></p>
            <p><strong>Usuário:</strong> <code>analista_sec</code></p>
            <p><strong>Senha:</strong> <code>SenhaSegura@2026</code></p>
        </div>
    </div>
</body>
</html>
