Param(
    [ValidateSet("dev","dev-meili","prod","docker","docker-build","docker-stop")]
    [string]$Mode = "dev"
)

Write-Host ""
Write-Host "Running setup in mode: $Mode"
Write-Host "---------------------------------------"
Write-Host ""

function Copy-EnvFile($file) {
    if (Test-Path $file) {
        $existingKey = $null
        if (Test-Path ".env") {
            $envLines = Get-Content ".env"
            $keyLine = $envLines | Where-Object { $_ -match '^APP_KEY=.+' }
            if ($keyLine) {
                $existingKey = $keyLine
                Write-Host "[INFO] Preserving existing APP_KEY"
            }
        }
        
        Copy-Item $file ".env" -Force
        
        if ($existingKey) {
            $newEnvContent = Get-Content ".env"
            $newEnvContent = $newEnvContent | ForEach-Object {
                if ($_ -match '^APP_KEY=') { $existingKey } else { $_ }
            }
            $newEnvContent | Set-Content ".env"
        }
        
        Write-Host "[OK] Loaded environment: $file"
    } else {
        Write-Host "[WARNING] Environment file not found: $file"
    }
}

function Ensure-AppKey {
    $envPath = ".env"
    if (!(Test-Path $envPath)) {
        Write-Host "[WARNING] .env is missing - cannot verify APP_KEY"
        return
    }

    $envLines = Get-Content $envPath
    $appKeyLine = $envLines | Where-Object { $_ -match '^APP_KEY=' }
    
    if ($appKeyLine -and $appKeyLine -match '^APP_KEY=\s*$') {
        Write-Host "[KEY] APP_KEY is EMPTY - generating a new key..."
        php artisan key:generate
    }
    elseif ($appKeyLine) {
        Write-Host "[KEY] APP_KEY already exists - keeping it."
    }
    else {
        Write-Host "[WARNING] APP_KEY not found in .env"
        php artisan key:generate
    }
}

function Check-ServerRunning {
    $phpRunning = Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue
    $npmRunning = Get-NetTCPConnection -LocalPort 5173 -State Listen -ErrorAction SilentlyContinue
    
    if ($phpRunning -or $npmRunning) {
        Write-Host ""
        Write-Host "[WARNING] Development servers are already running!"
        if ($phpRunning) { Write-Host "  - PHP server on port 8000" }
        if ($npmRunning) { Write-Host "  - Vite dev server on port 5173" }
        Write-Host ""
        
        $response = Read-Host "Do you want to stop them and restart? (y/n)"
        if ($response -eq "y" -or $response -eq "Y") {
            Write-Host "[INFO] Stopping existing servers..."
            
            Get-Process -Name php -ErrorAction SilentlyContinue | Where-Object {
                $_.MainWindowTitle -match "artisan" -or 
                (Get-NetTCPConnection -OwningProcess $_.Id -ErrorAction SilentlyContinue | Where-Object LocalPort -eq 8000)
            } | Stop-Process -Force
            
            $viteProcess = Get-NetTCPConnection -LocalPort 5173 -ErrorAction SilentlyContinue
            if ($viteProcess) {
                Stop-Process -Id $viteProcess.OwningProcess -Force -ErrorAction SilentlyContinue
            }
            
            Write-Host "[OK] Stopped existing servers."
            Start-Sleep -Seconds 2
            return $true
        } else {
            Write-Host "[INFO] Keeping existing servers running. Exiting..."
            return $false
        }
    }
    return $true
}

Write-Host "DEBUG: About to enter switch with Mode = $Mode"

switch ($Mode) {
    "dev" {
        Write-Host "DEBUG: Entered dev case"
        Copy-EnvFile "./env/.env.dev"

        Ensure-AppKey

        if (Check-ServerRunning) {
            Write-Host "[OK] Starting local development environment (NO DOCKER)..."
            Start-Process "php" "artisan serve --host=127.0.0.1 --port=8000"
            Start-Process "cmd" "/c npm run dev"
        }
    }

    "dev-meili" {
        Write-Host "DEBUG: Entered dev-meili case"
        Copy-EnvFile "./env/.env.dev.meili"

        Write-Host "[OK] Starting Docker services (MySQL + Meilisearch + Mailpit)..."
        docker compose up -d

        Ensure-AppKey

        Write-Host "[OK] Running migrations..."
        php artisan migrate:fresh --force
        php artisan db:seed --force
        php artisan storage:link

        npm install

        if (Check-ServerRunning) {
            Write-Host "[OK] Starting PHP server & Vite..."
            Start-Process "php" "artisan serve --host=127.0.0.1 --port=8000"
            Start-Process "cmd" "/c npm run dev"
        }
    }

    "prod" {
        Write-Host "DEBUG: Entered prod case"
        Copy-EnvFile "./env/.env.prod"

        npm install
        npm run build

        Ensure-AppKey

        Write-Host "[OK] Optimizing Laravel for production..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan optimize

        Write-Host "[OK] Production build complete."
    }

    "docker" {
        Write-Host "DEBUG: Entered docker case"
        Copy-EnvFile "./env/.env.docker"

        Write-Host "[OK] Starting full docker environment..."
        docker compose -f docker-compose-full.yml up -d

        Write-Host "[OK] Full Docker environment is running."
        Write-Host "To run artisan inside docker: docker compose exec app php artisan COMMAND"
    }

    "docker-build" {
        Write-Host "DEBUG: Entered docker-build case"
        Write-Host "[OK] Building Docker images..."
        docker compose -f docker-compose-full.yml build
        Write-Host "[OK] Docker images built."
    }

    "docker-stop" {
        Write-Host "DEBUG: Entered docker-stop case"
        Write-Host "[OK] Stopping Docker containers..."
        docker compose -f docker-compose-full.yml down
        Write-Host "[OK] Docker stopped."
    }

    default {
        Write-Host "ERROR: No matching case found for Mode = $Mode"
    }
}

Write-Host ""
Write-Host "Done."
Write-Host "---------------------------------------"
Write-Host ""