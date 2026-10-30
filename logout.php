<?php
require_once 'security.php';

send_security_headers();
start_secure_session();

// Logout só via POST com token CSRF: um link ou imagem em outro site
// não consegue derrubar a sessão do usuário.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    http_response_code(405);
    exit('Use o botão "Encerrar Sessão Segura" no painel.');
}

destroy_session();

header('Location: index.php?logged_out=1');
exit();
