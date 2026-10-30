<?php
// Camada de segurança compartilhada: headers, sessão, CSRF, controle de acesso e rate limiting.
require_once 'config.php';

function send_security_headers(): void
{
    header("Content-Security-Policy: default-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: no-referrer');
    header('Cache-Control: no-store');
}

function start_secure_session(): void
{
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,     // JavaScript não lê o cookie (reduz impacto de XSS)
        'samesite' => 'Strict', // cookie não é enviado em requisições de outros sites (CSRF)
    ]);
    ini_set('session.use_strict_mode', '1'); // rejeita IDs de sessão que o servidor não gerou
    ini_set('session.use_only_cookies', '1');
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function destroy_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);
    }
    session_destroy();
}

// Controle de acesso centralizado: toda página restrita chama esta função.
function require_auth(): void
{
    if (($_SESSION['authenticated'] ?? false) !== true) {
        header('Location: index.php?error=unauthorized');
        exit();
    }

    $now     = time();
    $idle    = $now - ($_SESSION['last_activity'] ?? 0) > IDLE_TIMEOUT;
    $expired = $now - ($_SESSION['created_at'] ?? 0) > ABSOLUTE_TIMEOUT;

    if ($idle || $expired) {
        destroy_session();
        header('Location: index.php?error=expired');
        exit();
    }
    $_SESSION['last_activity'] = $now;
}

// Rate limiting por IP, persistido no servidor. Ficar só na sessão não funciona:
// o atacante simplesmente descarta o cookie a cada tentativa.
function attempts_file(): string
{
    return sys_get_temp_dir() . '/auth_attempts_' . hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown') . '.json';
}

function login_attempts(): array
{
    $data = @json_decode((string)@file_get_contents(attempts_file()), true);
    return is_array($data) ? $data : ['count' => 0, 'locked_until' => 0];
}

function is_locked_out(): bool
{
    return login_attempts()['locked_until'] > time();
}

function register_failed_attempt(): void
{
    $data = login_attempts();
    $data['count']++;
    if ($data['count'] >= MAX_ATTEMPTS) {
        $data = ['count' => 0, 'locked_until' => time() + LOCKOUT_SECONDS];
    }
    file_put_contents(attempts_file(), json_encode($data), LOCK_EX);
}

function clear_attempts(): void
{
    @unlink(attempts_file());
}
