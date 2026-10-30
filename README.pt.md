[ 🇺🇸 English ](README.md) | [ 🇪🇸 Español ](README.es.md) | [ 🇧🇷 Português ](README.pt.md)

---

# Autenticação & Gerenciamento de Sessão Seguros (PHP)

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![OWASP](https://img.shields.io/badge/OWASP-A01%20%7C%20A07-red)
![Foco](https://img.shields.io/badge/foco-AppSec-0284c7)

Um fluxo login → painel restrito → logout criado para demonstrar os controles por trás de uma autenticação segura: hash de senha, endurecimento de sessão, controle de acesso centralizado, proteção contra força bruta e defesa contra CSRF.

![Preview do Painel](preview.png)

## Modelo de ameaças

| Ameaça | OWASP Top 10 (2021) | Mitigação | Onde |
|---|---|---|---|
| Vazamento de senha armazenada | A02 Cryptographic Failures | Só o **hash bcrypt (cost 12)** é guardado; verificação com `password_verify()` | `config.php`, `login.php` |
| Força bruta / credential stuffing | A07 Identification & Auth Failures | 5 tentativas falhas por IP → bloqueio de 15 min, guardado **no servidor** | `security.php` |
| Enumeração de usuários por tempo de resposta | A07 Identification & Auth Failures | `password_verify()` roda sempre, mesmo com usuário inexistente; usuário comparado com `hash_equals()` | `login.php` |
| Session fixation | A07 Identification & Auth Failures | `session_regenerate_id(true)` no login + `session.use_strict_mode` | `login.php`, `security.php` |
| Sequestro de sessão | A07 Identification & Auth Failures | Cookies `HttpOnly`, `SameSite=Strict`, `Secure` (HTTPS); timeout de 5 min parado e 30 min no total | `security.php` |
| Acesso direto pela URL a páginas privadas | A01 Broken Access Control | Uma única função `require_auth()` usada por toda página restrita | `security.php` |
| CSRF no login / logout | A01 Broken Access Control | Token aleatório por sessão em todo formulário; logout **só via POST** | `index.php`, `logout.php` |
| Clickjacking, MIME sniffing, cache | A05 Security Misconfiguration | CSP, `X-Frame-Options: DENY`, `nosniff`, `Cache-Control: no-store` | `security.php` |
| Conteúdo refletido nas mensagens de erro | A03 Injection | Mensagens vêm de uma allowlist fixa; o parâmetro da URL nunca é exibido | `index.php` |

## Decisões de design

- **O rate limiting não fica na sessão.** O atacante pode descartar o cookie e ganhar uma sessão nova a cada tentativa, então o contador fica no servidor, indexado por um hash do IP.
- **O Session ID nunca é exibido.** O painel mostra só uma *impressão digital* SHA-256 truncada: o ID real é um segredo que dá acesso à conta.
- **O controle de acesso é centralizado.** Com uma única função `require_auth()`, uma página nova não tem como "esquecer" uma checagem que mora em outro lugar.

## Testes de defesa

Rode a aplicação (veja abaixo) e teste:

| Teste | Resultado esperado |
|---|---|
| Abrir `/dashboard.php` sem login | Redireciona para `index.php?error=unauthorized` |
| Comparar o cookie de sessão antes e depois do login | ID novo após o login (defesa contra fixation) |
| 5 senhas erradas seguidas | `Muitas tentativas falhas` por 15 minutos, mesmo apagando os cookies |
| Abrir `/logout.php` via GET (um link) | HTTP 405, sessão continua ativa |
| Ficar 5 minutos parado no painel | Sessão destruída, `error=expired` |

```bash
# Login sem token CSRF deve ser recusado (403)
curl -i -X POST http://localhost:8000/login.php -d "username=analista_sec&password=x"
```

## Como executar

Requisito: PHP 8.0+.

```bash
git clone https://github.com/MattheusAlfonso/secure-auth-system.git
cd secure-auth-system
php -S localhost:8000
```

Acesse <http://localhost:8000> e use as credenciais de teste exibidas na tela de login:
`analista_sec` / `SenhaSegura@2026` (apenas demonstração).

## Para produção

Este é um projeto educacional. Num sistema real eu também adicionaria:

- [ ] Banco de dados real com prepared statements (PDO)
- [ ] Argon2id (`PASSWORD_ARGON2ID`) com `password_needs_rehash()` no login
- [ ] MFA (TOTP)
- [ ] Rate limiting no Redis ou no proxy reverso, além de log de eventos de segurança
- [ ] HTTPS em tudo com HSTS

---
*Parte do meu [portfólio de cibersegurança](https://mattheusalfonso.github.io).*
