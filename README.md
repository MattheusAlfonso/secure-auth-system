[ 🇺🇸 English ](README.md) | [ 🇪🇸 Español ](README.es.md) | [ 🇧🇷 Português ](README.pt.md)

---

# Secure Authentication & Session Management (PHP)

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![OWASP](https://img.shields.io/badge/OWASP-A01%20%7C%20A07-red)
![Focus](https://img.shields.io/badge/focus-AppSec-0284c7)

A login → restricted dashboard → logout flow built to show the controls behind a secure authentication system: password hashing, session hardening, centralized access control, brute-force protection and CSRF defense.

![Dashboard Preview](preview.png)

## Threat model

| Threat | OWASP Top 10 (2021) | Mitigation | Where |
|---|---|---|---|
| Password leak from storage | A02 Cryptographic Failures | Only a **bcrypt hash (cost 12)** is stored; checked with `password_verify()` | `config.php`, `login.php` |
| Brute force / credential stuffing | A07 Identification & Auth Failures | 5 failed attempts per IP → 15 min lockout, stored **server-side** | `security.php` |
| Username enumeration by timing | A07 Identification & Auth Failures | `password_verify()` always runs, even for unknown users; username compared with `hash_equals()` | `login.php` |
| Session fixation | A07 Identification & Auth Failures | `session_regenerate_id(true)` on login + `session.use_strict_mode` | `login.php`, `security.php` |
| Session hijacking | A07 Identification & Auth Failures | `HttpOnly`, `SameSite=Strict`, `Secure` (HTTPS) cookies; 5 min idle and 30 min absolute timeout | `security.php` |
| Direct URL access to private pages | A01 Broken Access Control | One `require_auth()` guard used by every restricted page | `security.php` |
| Login / logout CSRF | A01 Broken Access Control | Random per-session token on every form; logout accepts **POST only** | `index.php`, `logout.php` |
| Clickjacking, MIME sniffing, caching | A05 Security Misconfiguration | CSP, `X-Frame-Options: DENY`, `nosniff`, `Cache-Control: no-store` | `security.php` |
| Reflected content in error messages | A03 Injection | Error messages come from a fixed allowlist; the URL parameter is never echoed | `index.php` |

## Design decisions

- **Rate limiting is not stored in the session.** An attacker can simply drop the cookie and get a fresh session on every attempt, so the counter lives on the server, keyed by a hash of the IP.
- **The session ID is never shown.** The dashboard displays only a truncated SHA-256 *fingerprint*: the real ID is a bearer secret.
- **Access control is centralized.** A single `require_auth()` function means a new page cannot "forget" a check that lives somewhere else.

## Defense tests

Run the app (see below) and try:

| Test | Expected result |
|---|---|
| Open `/dashboard.php` without logging in | Redirect to `index.php?error=unauthorized` |
| Compare the session cookie before and after login | New ID after login (fixation defense) |
| 5 wrong passwords in a row | Locked out for 15 minutes (`error=locked`), even after deleting cookies |
| Open `/logout.php` via GET (a link) | HTTP 405, session stays active |
| Stay idle for 5 minutes on the dashboard | Session destroyed, `error=expired` |

```bash
# Login without CSRF token must be refused (403)
curl -i -X POST http://localhost:8000/login.php -d "username=analista_sec&password=x"
```

## How to run

Requirements: PHP 8.0+.

```bash
git clone https://github.com/MattheusAlfonso/secure-auth-system.git
cd secure-auth-system
php -S localhost:8000
```

Open <http://localhost:8000> and use the test credentials shown on the login page:
`analista_sec` / `SenhaSegura@2026` (demo only).

## Production notes

This is an educational project. In a real system I would also add:

- [ ] A real database with prepared statements (PDO)
- [ ] Argon2id (`PASSWORD_ARGON2ID`) with `password_needs_rehash()` on login
- [ ] MFA (TOTP)
- [ ] Rate limiting in Redis or at the reverse proxy, plus security event logging
- [ ] HTTPS everywhere with HSTS

---
*Part of my [cybersecurity portfolio](https://mattheusalfonso.github.io).*
