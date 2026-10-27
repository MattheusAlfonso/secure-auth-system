<?php
$usuario_valido = "analista_sec";

$senha_pura = "SenhaSegura@2026";

$senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

$usuarios_db = [
    'username' => $usuario_valido,
    'password_hash' => $senha_hash
];
?>