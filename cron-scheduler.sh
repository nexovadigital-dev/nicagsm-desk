#!/bin/bash
# NexovaDesk — Laravel scheduler wrapper
# Optimized for Hostinger shared hosting

APP_DIR="/home/u164741808/domains/nexovadesk.com/public_html/nexova-app"
PHP_BIN="/usr/bin/php"
LOCK_FILE="/tmp/nexovadesk_scheduler.lock"

# Avoid overlapping runs
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if kill -0 "$PID" 2>/dev/null; then
        exit 0
    fi
fi

echo $$ > "$LOCK_FILE"
trap "rm -f $LOCK_FILE" EXIT

cd "$APP_DIR" || exit 1
$PHP_BIN artisan schedule:run --no-interaction >> /dev/null 2>&1

