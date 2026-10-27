[ 🇺🇸 English ](README.md) | [ 🇪🇸 Español ](README.es.md) | [ 🇧🇷 Português ](README.pt.md)

---

# 🔒 Sistema de Autenticación y Gestión Segura de Sesiones (PHP/HTML5)

Un sistema de autenticación defensivo desarrollado en PHP y HTML5, diseñado para demostrar la gestión segura de sesiones, el almacenamiento hash de contraseñas y la mitigación de fallos de **Control de Acceso Inseguro** (OWASP A01:2021) y **Autenticación Defectuosa** (OWASP A07:2021).

![Dashboard Preview](preview.png)

## 🎯 Propósito y Alcance
Este proyecto implementa un flujo seguro de acceso a un panel interno. Garantiza la protección de las credenciales de usuario mediante hashing y combate activamente los ataques de fijación de sesión y el acceso directo no autorizado mediante URL.

## 🛡️ Funcionalidades de Seguridad Implementadas
* **Hash Seguro de Contraseñas:** Uso de `password_hash()` con algoritmo bcrypt/Argon2id y verificación mediante `password_verify()`.
* **Defensa contra Fijación de Sesión:** Ejecución de `session_regenerate_id(true)` tras una autenticación exitosa para renovar el identificador de sesión.
* **Control de Acceso Estricto:** Bloqueo de accesos no autorizados a `dashboard.php` mediante la validación de `$_SESSION['authenticated']`.
* **Expiración por Inactividad:** Cierre automático de sesión tras 5 minutos (300s) de inactividad del usuario.
* **Destrucción Completa de Sesión (`logout.php`):** Limpieza de variables `$_SESSION`, destrucción del archivo de sesión en el servidor y expiración de cookies del navegador.

---
*Desarrollado como parte del Portafolio de Ciberseguridad para roles internacionales de análisis de software y amenazas.*