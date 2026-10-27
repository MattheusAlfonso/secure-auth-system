[ 🇺🇸 English ](README.md) | [ 🇪🇸 Español ](README.es.md) | [ 🇧🇷 Português ](README.pt.md)

---

# 🔒 Sistema de Autenticação & Gestão Segura de Sessões (PHP/HTML5)

Sistema de autenticação defensivo desenvolvido em PHP e HTML5 para demonstrar o controle seguro de sessões, hash de senhas e mitigação contra **Broken Access Control** (OWASP A01:2021) e **Broken Authentication** (OWASP A07:2021).

![Dashboard Preview](preview.png)

## 🎯 Propósito e Escopo
Este projeto implementa um portal de acesso restrito seguro. Ele garante a proteção de credenciais na memória/servidor e combate ataques de *Session Fixation*, sequestro de sessão e navegação direta não autorizada via URL.

## 🛡️ Camadas de Defesa Implementadas
* **Hash Seguro de Senhas:** Uso de `password_hash()` com algoritmo bcrypt/Argon2id e validação por `password_verify()`.
* **Proteção contra Session Fixation:** Execução de `session_regenerate_id(true)` no login bem-sucedido para renovar o ID da sessão.
* **Controle de Acesso Restrito:** Bloqueio direto de requisições ao `dashboard.php` sem sessão autenticada ativa.
* **Timeout por Inatividade:** Expiração automática da sessão após 5 minutos de inatividade.
* **Encerramento Seguro (`logout.php`):** Limpeza total das variáveis de sessão, destruição no servidor e expiração do cookie `PHPSESSID`.

---
*Desenvolvido como parte do Portfólio de Cibersegurança para vagas internacionais.*