# Serbizyu - Setup & Commands Reference

## 🚀 Quick Start

### Setup Script Usage

The `setup.ps1` script provides multiple environment configurations:

```powershell
# Local development (SQLite, no Docker)
./setup.ps1 -Mode dev

# Local development with Meilisearch (Docker: MySQL + Meilisearch + Mailpit)
./setup.ps1 -Mode dev-meili

# Production build
./setup.ps1 -Mode prod

# Full Docker environment
./setup.ps1 -Mode docker

# Build Docker images
./setup.ps1 -Mode docker-build

# Stop Docker containers
./setup.ps1 -Mode docker-stop
```

## 📁 Environment Files

All environment files are stored in the `./env/` directory:

- `./env/.env.dev` - Local development (SQLite)
- `./env/.env.dev.meili` - Local with Meilisearch (MySQL + Docker services)
- `./env/.env.prod` - Production configuration
- `./env/.env.docker` - Full Docker environment

**Important:** The script automatically preserves your `APP_KEY` when switching between environments!

## 🔧 What the Setup Script Does

### `dev` Mode
1. Copies `./env/.env.dev` to `.env`
2. Preserves existing APP_KEY or generates a new one
3. Checks for running servers (ports 8000 & 5173)
4. Starts PHP artisan server on `http://127.0.0.1:8000`
5. Starts Vite dev server

### `dev-meili` Mode
1. Copies `./env/.env.dev.meili` to `.env`
2. Starts Docker services (MySQL, Meilisearch, Mailpit)
3. Preserves existing APP_KEY or generates a new one
4. Runs fresh migrations and seeders
5. Creates storage symlink
6. Installs npm packages
7. Starts PHP and Vite servers

### `prod` Mode
1. Copies `./env/.env.prod` to `.env`
2. Installs npm packages
3. Builds production assets
4. Optimizes Laravel (config, routes, views)

### `docker` Mode
1. Copies `./env/.env.docker` to `.env`
2. Starts full Docker environment using `docker-compose-full.yml`

## 🐳 Docker Commands

### When Using `dev-meili` Mode (Partial Docker)

```powershell
# View running containers
docker compose ps

# View logs
docker compose logs -f

# Stop services
docker compose down

# Restart services
docker compose restart

# Access MySQL
docker compose exec mysql mysql -u root -ppassword serbizyu

# Access Meilisearch
# Browser: http://localhost:7700

# Access Mailpit (Email testing)
# Browser: http://localhost:8025
```

### When Using `docker` Mode (Full Docker)

**First time setup:**
```powershell
# Build Docker images
./setup.ps1 -Mode docker-build

# Start Docker environment
./setup.ps1 -Mode docker

# Run migrations inside container
docker compose -f docker-compose-full.yml exec app php artisan migrate --force
docker compose -f docker-compose-full.yml exec app php artisan db:seed --force
docker compose -f docker-compose-full.yml exec app php artisan storage:link
```

**Daily commands:**
```powershell
# View running containers
docker compose -f docker-compose-full.yml ps

# View logs
docker compose -f docker-compose-full.yml logs -f

# View specific service logs
docker compose -f docker-compose-full.yml logs -f app
docker compose -f docker-compose-full.yml logs -f mysql

# Stop all services
docker compose -f docker-compose-full.yml down

# Restart services
docker compose -f docker-compose-full.yml restart

# Run artisan commands inside Docker
docker compose -f docker-compose-full.yml exec app php artisan migrate
docker compose -f docker-compose-full.yml exec app php artisan db:seed
docker compose -f docker-compose-full.yml exec app php artisan cache:clear
docker compose -f docker-compose-full.yml exec app php artisan route:list
docker compose -f docker-compose-full.yml exec app php artisan config:cache

# Access container shell
docker compose -f docker-compose-full.yml exec app bash

# Access MySQL in Docker
docker compose -f docker-compose-full.yml exec mysql mysql -u root -ppassword serbizyu

# Rebuild after code changes
docker compose -f docker-compose-full.yml up -d --build
```

## 📋 Common Laravel Commands

### Development

```powershell
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Fresh migrations with seed
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name

# Create model with migration and controller
php artisan make:model ModelName -mc

# Create controller
php artisan make:controller ControllerName

# View routes
php artisan route:list

# Create storage symlink
php artisan storage:link
```

### Database

```powershell
# Run seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Fresh database
php artisan migrate:fresh
```

### Queue & Jobs

```powershell
# Run queue worker
php artisan queue:work

# Run queue with restart on code changes
php artisan queue:listen

# Create job
php artisan make:job JobName
```

### Cache Management

```powershell
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear all caches
php artisan optimize:clear
```

## 🔍 Meilisearch Commands

```powershell
# Import all models to Meilisearch
php artisan scout:import "App\Models\ModelName"

# Flush Meilisearch index
php artisan scout:flush "App\Models\ModelName"

# Check Meilisearch status
curl http://localhost:7700/health
```

## 📦 NPM Commands

```powershell
# Install dependencies
npm install

# Development server (with HMR)
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## 🛠️ Troubleshooting

### Port Already in Use

If you get port conflicts, the script will detect and offer to stop existing servers. Or manually:

```powershell
# Find process on port 8000 (PHP)
Get-NetTCPConnection -LocalPort 8000 | Select-Object OwningProcess
Stop-Process -Id <PROCESS_ID> -Force

# Find process on port 5173 (Vite)
Get-NetTCPConnection -LocalPort 5173 | Select-Object OwningProcess
Stop-Process -Id <PROCESS_ID> -Force
```

### APP_KEY Issues

```powershell
# Generate new key
php artisan key:generate

# The script automatically handles this, but if needed manually:
# 1. Open .env
# 2. Set APP_KEY=
# 3. Run: php artisan key:generate
```

### Docker Issues

```powershell
# Rebuild containers
docker compose down
docker compose up -d --build

# Or for full Docker mode:
docker compose -f docker-compose-full.yml down
docker compose -f docker-compose-full.yml build --no-cache
docker compose -f docker-compose-full.yml up -d

# Remove all containers and volumes (CAUTION: deletes data)
docker compose down -v
docker compose -f docker-compose-full.yml down -v

# View Docker disk usage
docker system df

# Clean up unused images
docker image prune -a

# If build fails, try:
docker compose -f docker-compose-full.yml down
docker system prune -a
./setup.ps1 -Mode docker-build
```

### Permission Issues (Linux/Mac)

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🌐 Access URLs

| Service | URL | Notes |
|---------|-----|-------|
| Laravel App | http://127.0.0.1:8000 | Main application |
| Vite Dev Server | http://localhost:5173 | Asset development |
| Meilisearch | http://localhost:7700 | Search engine |
| Mailpit | http://localhost:8025 | Email testing |
| MySQL | localhost:3306 | Database |

## 📝 Development Workflow

### Starting Fresh Day

```powershell
# 1. Pull latest changes
git pull

# 2. Start environment
./setup.ps1 -Mode dev

# 3. Update dependencies (if composer.lock or package-lock.json changed)
composer install
npm install

# 4. Run migrations (if new migrations exist)
php artisan migrate
```

### Switching Between Environments

```powershell
# From dev to dev-meili (adds Docker services)
./setup.ps1 -Mode dev-meili

# From dev-meili back to dev (stops Docker)
docker compose down
./setup.ps1 -Mode dev
```

### Before Committing

```powershell
# Run tests (if configured)
php artisan test

# Check code style (if using Pint/PHP-CS-Fixer)
./vendor/bin/pint

# Build assets for testing
npm run build
```

## 🔐 Environment Variables Reference

### Essential Variables

```env
APP_NAME=Serbizyu
APP_ENV=local|production
APP_KEY=base64:...          # Auto-generated
APP_DEBUG=true|false
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite|mysql
DB_HOST=127.0.0.1           # mysql only
DB_PORT=3306                # mysql only
DB_DATABASE=serbizyu        # mysql only
DB_USERNAME=root            # mysql only
DB_PASSWORD=password        # mysql only

SCOUT_DRIVER=null|meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=masterKey

MAIL_MAILER=log|smtp
MAIL_HOST=localhost
MAIL_PORT=1025

FILESYSTEM_DISK=local|s3
SESSION_DRIVER=file|database|redis
QUEUE_CONNECTION=sync|database|redis
```

## 🎯 Quick Reference Cheatsheet

```powershell
# Start development
./setup.ps1 -Mode dev

# Add Meilisearch
./setup.ps1 -Mode dev-meili

# Fresh database
php artisan migrate:fresh --seed

# Clear everything
php artisan optimize:clear

# View logs
tail -f storage/logs/laravel.log

# Stop Docker services
docker compose down
```

---

**Pro Tip:** The setup script preserves your APP_KEY across environment switches, so you don't lose session data when switching between `dev` and `dev-meili` modes! 🎉