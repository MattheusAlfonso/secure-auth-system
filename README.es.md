[ 🇺🇸 English ](README.md) | [ 🇪🇸 Español ](README.es.md) | [ 🇧🇷 Português ](README.pt.md)

---

# Autenticación y Gestión de Sesiones Segura (PHP)

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![OWASP](https://img.shields.io/badge/OWASP-A01%20%7C%20A07-red)
![Enfoque](https://img.shields.io/badge/enfoque-AppSec-0284c7)

Un flujo login → panel restringido → logout creado para demostrar los controles detrás de una autenticación segura: hash de contraseñas, endurecimiento de sesiones, control de acceso centralizado, protección contra fuerza bruta y defensa contra CSRF.

![Vista previa del panel](preview.png)

## Modelo de amenazas

| Amenaza | OWASP Top 10 (2021) | Mitigación | Dónde |
|---|---|---|---|
| Filtración de contraseñas almacenadas | A02 Cryptographic Failures | Solo se guarda el **hash bcrypt (cost 12)**; verificación con `password_verify()` | `config.php`, `login.php` |
| Fuerza bruta / credential stuffing | A07 Identification & Auth Failures | 5 intentos fallidos por IP → bloqueo de 15 min, guardado **en el servidor** | `security.php` |
| Enumeración de usuarios por tiempo de respuesta | A07 Identification & Auth Failures | `password_verify()` se ejecuta siempre, incluso con usuarios inexistentes; usuario comparado con `hash_equals()` | `login.php` |
| Session fixation | A07 Identification & Auth Failures | `session_regenerate_id(true)` al iniciar sesión + `session.use_strict_mode` | `login.php`, `security.php` |
| Secuestro de sesión | A07 Identification & Auth Failures | Cookies `HttpOnly`, `SameSite=Strict`, `Secure` (HTTPS); expiración de 5 min por inactividad y 30 min en total | `security.php` |
| Acceso directo por URL a páginas privadas | A01 Broken Access Control | Una sola función `require_auth()` en toda página restringida | `security.php` |
| CSRF en login / logout | A01 Broken Access Control | Token aleatorio por sesión en cada formulario; logout **solo por POST** | `index.php`, `logout.php` |
| Clickjacking, MIME sniffing, caché | A05 Security Misconfiguration | CSP, `X-Frame-Options: DENY`, `nosniff`, `Cache-Control: no-store` | `security.php` |
| Contenido reflejado en mensajes de error | A03 Injection | Los mensajes vienen de una allowlist fija; el parámetro de la URL nunca se muestra | `index.php` |

## Decisiones de diseño

- **El rate limiting no vive en la sesión.** El atacante puede descartar la cookie y obtener una sesión nueva en cada intento, así que el contador está en el servidor, indexado por un hash de la IP.
- **El Session ID nunca se muestra.** El panel muestra solo una *huella* SHA-256 truncada: el ID real es un secreto que da acceso a la cuenta.
- **El control de acceso está centralizado.** Con una sola función `require_auth()`, una página nueva no puede "olvidar" una verificación que vive en otro lugar.

## Pruebas de defensa

Ejecuta la aplicación (ver abajo) y prueba:

| Prueba | Resultado esperado |
|---|---|
| Abrir `/dashboard.php` sin iniciar sesión | Redirección a `index.php?error=unauthorized` |
| Comparar la cookie de sesión antes y después del login | ID nuevo tras el login (defensa contra fixation) |
| 5 contraseñas incorrectas seguidas | Bloqueo de 15 minutos, incluso borrando las cookies |
| Abrir `/logout.php` por GET (un enlace) | HTTP 405, la sesión sigue activa |
| 5 minutos sin actividad en el panel | Sesión destruida, `error=expired` |

```bash
# Login sin token CSRF debe ser rechazado (403)
curl -i -X POST http://localhost:8000/login.php -d "username=analista_sec&password=x"
```

## Cómo ejecutar

Requisito: PHP 8.0+.

```bash
git clone https://github.com/MattheusAlfonso/secure-auth-system.git
cd secure-auth-system
php -S localhost:8000
```

Abre <http://localhost:8000> y usa las credenciales de prueba que aparecen en la pantalla de login:
`analista_sec` / `SenhaSegura@2026` (solo demostración).

## Para producción

Este es un proyecto educativo. En un sistema real también agregaría:

- [ ] Base de datos real con prepared statements (PDO)
- [ ] Argon2id (`PASSWORD_ARGON2ID`) con `password_needs_rehash()` al iniciar sesión
- [ ] MFA (TOTP)
- [ ] Rate limiting en Redis o en el proxy inverso, además de registro de eventos de seguridad
- [ ] HTTPS en todo con HSTS

---
*Parte de mi [portafolio de ciberseguridad](https://mattheusalfonso.github.io).*
