#!/bin/bash
set -e
cd "$(dirname "$0")"

if command -v php >/dev/null 2>&1; then
  PHP_BIN="$(command -v php)"
elif [ -x "/Applications/XAMPP/xamppfiles/bin/php" ]; then
  PHP_BIN="/Applications/XAMPP/xamppfiles/bin/php"
else
  echo "Не найден PHP. Установите PHP 8.0.2+ или XAMPP и повторите запуск."
  exit 1
fi

if [ ! -f "vendor/autoload.php" ]; then
  echo "Устанавливаю зависимости Laravel..."
  if command -v composer >/dev/null 2>&1; then
    composer install --no-interaction --prefer-dist --no-progress
  else
    echo "Отсутствует Composer. Скачайте готовый локальный ZIP с vendor из GitHub Actions или установите Composer."
    exit 1
  fi
fi

"$PHP_BIN" scripts/setup-local.php
echo ""
echo "Откройте http://127.0.0.1:8000"
echo "Для остановки сервера нажмите Ctrl+C."
"$PHP_BIN" artisan serve --host=127.0.0.1 --port=8000
