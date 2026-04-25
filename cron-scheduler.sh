#!/bin/bash
# cron-scheduler.sh — Nexova Desk (nicagsm)
# Cron: * * * * * /bin/bash /ruta/cron-scheduler.sh >> /ruta/cron.log 2>&1
#
# Runs schedule:run at :00, then IMAP again at :30 for sub-minute email delivery.

PHP="${PHP:-/www/server/php/82/bin/php}"
BASE="${BASE:-/www/wwwroot/nexovadesk.com/nicagsm}"

cd "$BASE" || exit 1

# :00 — all scheduled tasks (IMAP, expire-agent-calls, purge-visitors, etc.)
"$PHP" "$BASE/artisan" schedule:run 2>&1 | grep -v Warning | grep -v JIT

# :30 — IMAP again for ~30s email delivery
sleep 30
"$PHP" "$BASE/artisan" tickets:process-inbound 2>&1 | grep -v Warning | grep -v JIT
