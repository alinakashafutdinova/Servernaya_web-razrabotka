@echo off
setlocal
cd /d "%~dp0"
set "PHP_EXE=php"
where php >nul 2>&1
if errorlevel 1 (
  if exist "C:\xampp\php\php.exe" (
    set "PHP_EXE=C:\xampp\php\php.exe"
  ) else (
    echo PHP not found. Install PHP 8.0.2 or newer, or XAMPP.
    pause
    exit /b 1
  )
)

if not exist "vendor\autoload.php" (
  where composer >nul 2>&1
  if errorlevel 1 (
    echo Composer not found. Download the ready-to-run ZIP with vendor from GitHub Actions.
    pause
    exit /b 1
  )
  call composer install --no-interaction --prefer-dist --no-progress
  if errorlevel 1 (
    pause
    exit /b 1
  )
)

"%PHP_EXE%" scripts\setup-local.php
if errorlevel 1 (
  pause
  exit /b 1
)
echo.
echo Website: http://127.0.0.1:8000
echo Stop the server with Ctrl+C
"%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8000
pause
