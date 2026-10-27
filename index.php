<?php
session_start();

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto 2 - Sistema de Autenticação Segura</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>🔒 Portal de Acesso Seguro</h2>
        <p>Autenticação com Defesa contra Session Fixation & Broken Access Control</p>
        <!-- 1. BLOCO DE ALERTAS DE ERRO -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
<?php 
                    if ($_GET['error'] === 'invalid') echo " Usuário ou senha incorretos!";
                    if ($_GET['error'] === 'unauthorized') echo " Acesso negado! Faça login primeiro.";
                    if ($_GET['error'] === 'expired') echo " Sessão expirada por inatividade. Faça login novamente.";
                ?>
            </div>
<?php endif; ?>
        <!-- 2. BLOCO DE SUCESSO DO LOGOUT -->
        <?php if (isset($_GET['logged_out'])): ?>
            <div class="alert success">
                ✓ Sessão encerrada com sucesso e destruída no servidor.
            </div>
        <?php endif; ?>
        <!-- ENVIO SEGURO VIA POST PARA PREVENIR VAZAMENTO NA URL/LOGS -->
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário" required autocomplete="off">
</div>

<div class="form-group">
    <label for="password">Senha:</label>
    <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
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