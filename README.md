# Trataplan – Site Institucional

Site institucional bilíngue (PT/EN) com painel administrativo, blog, parceiros, formulário de contato e SEO completo.

**Stack:** Laravel 13 · Vue 3 · Inertia.js · Tailwind CSS v4 · PostgreSQL

---

## Sumário

- [Requisitos](#requisitos)
- [Setup Local](#setup-local)
- [Painel Administrativo](#painel-administrativo)
- [Deploy em Produção](#deploy-em-produção)
- [Variáveis de Ambiente](#variáveis-de-ambiente)
- [Comandos Úteis](#comandos-úteis)

---

## Requisitos

| Ferramenta | Versão mínima |
|---|---|
| PHP | 8.2+ |
| PostgreSQL | 14+ |
| Node.js | 20+ |
| Composer | 2.x |
| Extensões PHP | `pdo_pgsql`, `mbstring`, `xml`, `curl`, `gd` |

---

## Setup Local

### 1. Instalar dependências

```bash
cd laravel-app
composer install
npm install
```

### 2. Configurar ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=trataplan
DB_USERNAME=postgres
DB_PASSWORD=sua_senha

MAIL_MAILER=log   # emails vão para storage/logs em desenvolvimento
```

### 3. Criar banco e migrar

```bash
# Criar o banco (ajuste usuário/senha conforme necessário)
psql -U postgres -c "CREATE DATABASE trataplan;"

# Migrar e popular com dados iniciais
php artisan migrate --seed

# Criar link de storage para uploads
php artisan storage:link
```

O seeder cria:
- **Admin:** `admin@trataplan.com.br` / `Trataplan@2024!`
- Configurações padrão (WhatsApp, endereço, textos)
- Páginas iniciais (Sobre e Produtos)

### 4. Iniciar

```bash
# Terminal 1 – Vite (hot reload)
npm run dev

# Terminal 2 – Laravel
php artisan serve
```

- Site: `http://localhost:8000`
- Admin: `http://localhost:8000/admin/login`

---

## Painel Administrativo

Acesso em `/admin/login`

| Seção | URL | O que gerencia |
|---|---|---|
| Dashboard | `/admin` | Estatísticas e mensagens recentes |
| Postagens | `/admin/posts` | Blog: criar, editar, agendar, SEO por post |
| Páginas | `/admin/paginas` | Conteúdo de Sobre e Produtos (HTML + SEO) |
| Parceiros | `/admin/parceiros` | Logo, link, descrição, ordem |
| Mensagens | `/admin/mensagens` | Formulário de contato recebido |
| Configurações | `/admin/configuracoes` | WhatsApp, Telegram, email, redes sociais |

### Criar novo admin pelo terminal

```bash
php artisan tinker

User::create([
    'name'     => 'Nome',
    'email'    => 'email@dominio.com',
    'password' => bcrypt('senha_segura'),
    'is_admin' => true,
]);
```

---

## Deploy em Produção

### Servidor recomendado

- **OS:** Ubuntu 22.04 LTS ou Debian 12
- **Web server:** Nginx
- **PHP:** 8.2+ via php-fpm
- **Banco:** PostgreSQL 14+
- **Fila de emails:** Supervisor + `queue:work`

---

### 1. Instalar dependências no servidor

```bash
sudo apt update
sudo apt install -y php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-gd php8.2-zip php8.2-bcmath \
  nginx postgresql supervisor git unzip

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

### 2. Criar banco PostgreSQL

```bash
sudo -u postgres psql

CREATE USER trataplan WITH PASSWORD 'senha_forte_aqui';
CREATE DATABASE trataplan OWNER trataplan;
GRANT ALL PRIVILEGES ON DATABASE trataplan TO trataplan;
\q
```

---

### 3. Fazer upload do código

**Via Git (recomendado):**

```bash
cd /var/www
git clone <repo-url> trataplan
cd trataplan/laravel-app
```

**Via rsync (do local para o servidor):**

```bash
rsync -avz --exclude='.git' --exclude='node_modules' --exclude='vendor' \
  ./laravel-app/ usuario@servidor:/var/www/trataplan/
```

---

### 4. Instalar e compilar

```bash
cd /var/www/trataplan

composer install --no-dev --optimize-autoloader

npm install
npm run build
rm -rf node_modules   # Remove node_modules após build para economizar espaço
```

---

### 5. Configurar `.env` de produção

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

```env
APP_NAME="Trataplan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://trataplan.com.br

APP_LOCALE=pt
APP_FALLBACK_LOCALE=pt

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=trataplan
DB_USERNAME=trataplan
DB_PASSWORD=senha_forte_aqui

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public

# SMTP (veja opções abaixo)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=seu@gmail.com
MAIL_PASSWORD=app_password_16_chars
MAIL_FROM_ADDRESS=trataplan@trataplan.com.br
MAIL_FROM_NAME="Trataplan"

CONTACT_EMAIL=trataplan@trataplan.com.br
WHATSAPP_NUMBER=554999600329
TELEGRAM_LINK=https://t.me/trataplan
```

> **Email em produção:** Use [Gmail App Password](https://support.google.com/accounts/answer/185833), [Brevo](https://brevo.com) (gratuito até 300 emails/dia) ou [Mailgun](https://mailgun.com). Evite usar o email do domínio diretamente sem SMTP configurado.

---

### 6. Preparar a aplicação

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize   # config + route + view cache

sudo chown -R www-data:www-data /var/www/trataplan
sudo chmod -R 755 /var/www/trataplan
sudo chmod -R 775 /var/www/trataplan/storage
sudo chmod -R 775 /var/www/trataplan/bootstrap/cache
```

---

### 7. Configurar Nginx

```bash
sudo nano /etc/nginx/sites-available/trataplan
```

```nginx
server {
    listen 80;
    server_name trataplan.com.br www.trataplan.com.br;
    root /var/www/trataplan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache longo para assets compilados pelo Vite (hash no nome)
    location ~* \.(js|css|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~* \.(png|jpg|jpeg|gif|ico|svg|webp)$ {
        expires 30d;
        add_header Cache-Control "public";
    }

    client_max_body_size 10M;
}
```

```bash
sudo ln -s /etc/nginx/sites-available/trataplan /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

### 8. SSL com Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d trataplan.com.br -d www.trataplan.com.br
# Renovação automática já configurada pelo certbot
```

---

### 9. Fila de emails com Supervisor

Garante que os emails do formulário sejam enviados de forma assíncrona, sem travar a resposta HTTP.

```bash
sudo nano /etc/supervisor/conf.d/trataplan-worker.conf
```

```ini
[program:trataplan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/trataplan/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/trataplan/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start trataplan-worker:*
```

---

### 10. DNS

No painel do registrador de domínio:

| Tipo | Nome | Valor |
|---|---|---|
| `A` | `@` | `IP_DO_SERVIDOR` |
| `A` | `www` | `IP_DO_SERVIDOR` |

---

## Deploy de Atualizações

Crie o arquivo `deploy.sh` na raiz do projeto:

```bash
#!/bin/bash
set -e

echo ">>> Iniciando deploy..."
cd /var/www/trataplan

git pull

echo ">>> Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader

echo ">>> Compilando assets..."
npm ci && npm run build && rm -rf node_modules

echo ">>> Atualizando banco..."
php artisan migrate --force

echo ">>> Otimizando..."
php artisan optimize

echo ">>> Reiniciando workers..."
sudo supervisorctl restart trataplan-worker:*

echo ">>> Deploy concluído!"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## Variáveis de Ambiente

| Variável | Descrição | Exemplo |
|---|---|---|
| `APP_URL` | URL pública do site | `https://trataplan.com.br` |
| `APP_DEBUG` | Debug (sempre `false` em produção) | `false` |
| `DB_*` | Credenciais PostgreSQL | — |
| `MAIL_*` | Configuração SMTP para emails | — |
| `CONTACT_EMAIL` | Email que recebe os formulários de contato | `trataplan@trataplan.com.br` |
| `WHATSAPP_NUMBER` | Número sem `+` ou espaços | `554999600329` |
| `TELEGRAM_LINK` | Link completo do Telegram | `https://t.me/trataplan` |
| `QUEUE_CONNECTION` | Driver de filas (database para produção) | `database` |
| `FILESYSTEM_DISK` | Disco de uploads | `public` |

---

## Comandos Úteis

```bash
# Limpar todos os caches
php artisan optimize:clear

# Ativar modo manutenção
php artisan down --message="Atualização em andamento..."
php artisan up

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Reprocessar emails com falha na fila
php artisan queue:retry all

# Ver status dos workers
sudo supervisorctl status

# Gerar sitemap (acesse via browser)
# https://trataplan.com.br/sitemap.xml
```

---

## SEO – Notas

- **Sitemap XML** gerado automaticamente em `/sitemap.xml` — inclui todas as páginas e posts em PT e EN
- **Robots.txt** em `/public/robots.txt` — bloqueia rastreamento do `/admin`
- **Meta tags** — cada página e post tem `meta_title` e `meta_description` editáveis no painel
- **Open Graph** — gerado automaticamente via `SeoHead.vue` (título, descrição, imagem)
- **Canonical URLs** — definidas por controller com base na URL atual

Para adicionar hreflang (opcional, melhora SEO internacional), edite [resources/js/Components/Shared/SeoHead.vue](resources/js/Components/Shared/SeoHead.vue):

```html
<link rel="alternate" hreflang="pt-BR" :href="ptUrl" />
<link rel="alternate" hreflang="en" :href="enUrl" />
<link rel="alternate" hreflang="x-default" :href="ptUrl" />
```

---

## Estrutura de Rotas

| Rota PT | Rota EN | Página |
|---|---|---|
| `/` | `/en` | Home |
| `/sobre` | `/en/about` | Sobre (CMS) |
| `/produtos` | `/en/products` | Produtos (CMS) |
| `/blog` | `/en/blog` | Lista de posts |
| `/blog/{slug}` | `/en/blog/{slug}` | Post individual |
| `/parceiros` | `/en/partners` | Parceiros |
| `/contato` | `/en/contact` | Formulário de contato |
| `/sitemap.xml` | — | Sitemap automático |
| `/admin/login` | — | Login do painel |
| `/admin` | — | Dashboard admin |
