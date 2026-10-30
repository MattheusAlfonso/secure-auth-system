<?php
// Simulação de banco de dados: apenas o HASH é armazenado, nunca a senha em texto puro.
// Hash bcrypt (cost 12) da senha de demonstração exibida em index.php.
// Para gerar outro: php -r "echo password_hash('NovaSenha', PASSWORD_DEFAULT);"
$usuarios_db = [
    'username'      => 'analista_sec',
    'password_hash' => '$2y$12$KPG5ZeppTtJ4osHsmaAX6.6Dygni52oOaGs4srP.h2pYRBYCp3Jry',
];

const IDLE_TIMEOUT     = 300;  // 5 min sem atividade
const ABSOLUTE_TIMEOUT = 1800; // 30 min no máximo por sessão
const MAX_ATTEMPTS     = 5;    // tentativas antes do bloqueio
const LOCKOUT_SECONDS  = 900;  // bloqueio de 15 min
