# Crons — Nexova Desk (nicagsm)

Tres formatos equivalentes. Usar el que corresponda según el hosting.

---

## FORMATO 1 — Shell scripts (.sh)
> Para hosting compartido (cPanel, hPanel, Plesk) que ejecuta archivos `.sh`

**Ruta de los scripts:** `/ruta-al-proyecto/`

### cron-scheduler.sh
```bash
#!/bin/bash
# Ejecuta TODOS los comandos programados en routes/console.php
# Cron: cada minuto  →  * * * * * /bin/bash /ruta/cron-scheduler.sh

PHP="/ruta/bin/php"
BASE="/ruta-al-proyecto"

cd "$BASE" || exit 1
$PHP artisan schedule:run >> /dev/null 2>&1
```

### worker-monitor.sh
```bash
#!/bin/bash
# Mantiene el queue worker vivo (ProcessBotReply, emails)
# Cron: cada minuto  →  * * * * * /bin/bash /ruta/worker-monitor.sh

PIDFILE="/tmp/nicagsm_worker.pid"
BASE="/ruta-al-proyecto"
PHP="/ruta/bin/php"
LOG="$BASE/storage/logs/worker.log"

if [ -f "$PIDFILE" ]; then
    PID=$(cat "$PIDFILE")
    if kill -0 "$PID" 2>/dev/null; then
        exit 0
    fi
fi

echo "[$(date '+%Y-%m-%d %H:%M:%S')] worker:respawn" >> "$LOG"
nohup $PHP "$BASE/artisan" queue:work --sleep=3 --tries=1 --max-time=3600 --memory=128 >> "$LOG" 2>&1 &
echo $! > "$PIDFILE"
```

### Entrada en crontab (hPanel / cPanel)
```
* * * * * /bin/bash /home/usuario/public_html/nicagsm/cron-scheduler.sh
* * * * * /bin/bash /home/usuario/public_html/nicagsm/worker-monitor.sh
```

---

## FORMATO 2 — Linux crontab (artisan directo)
> Para VPS/servidor propio con acceso a `/etc/cron.d/` o `crontab -e`

### /etc/cron.d/nicagsm (archivo completo)
```
PHP=/www/server/php/82/bin/php
BASE=/www/wwwroot/nexovadesk.com/nicagsm

# Todos los comandos programados (schedule:run los maneja todos)
* * * * * root /bin/bash /www/wwwroot/nexovadesk.com/nicagsm/cron-scheduler.sh >> /www/wwwlogs/cron_nicagsm.log 2>&1

# Queue worker — mantiene vivo el proceso queue:work
* * * * * root /bin/bash /www/wwwroot/nexovadesk.com/nicagsm/worker-monitor.sh >> /www/wwwlogs/cron_nicagsm_worker.log 2>&1
```

### Comandos individuales (alternativa a schedule:run)
```
# tickets:process-inbound — procesar emails IMAP entrantes (cada minuto)
* * * * * root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan tickets:process-inbound >> /dev/null 2>&1

# chat:expire-agent-calls — revertir llamados sin respuesta (cada 2 min)
*/2 * * * * root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan chat:expire-agent-calls >> /dev/null 2>&1

# chat:purge-visitors — limpiar visitantes sin heartbeat (cada minuto)
* * * * * root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan chat:purge-visitors >> /dev/null 2>&1

# tickets:auto-close — cerrar conversaciones bot inactivas 24h (hourly)
0 * * * * root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan tickets:auto-close >> /dev/null 2>&1

# partner:check-license — verificar licencia (daily 03:00)
0 3 * * * root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan partner:check-license >> /dev/null 2>&1

# nexova:recrawl-urls — re-indexar URLs de KB (weekly, domingo 02:00)
0 2 * * 0 root /www/server/php/82/bin/php /www/wwwroot/nexovadesk.com/nicagsm/artisan nexova:recrawl-urls >> /dev/null 2>&1
```

---

## FORMATO 3 — curl (HTTP endpoints)
> Para cron-job.org, EasyCron, hPanel "URL cron", o cualquier servicio externo.
> No requiere acceso SSH. Los endpoints están protegidos con throttle:120/min.

### Dominio base
```
https://nicagsm.nexovadesk.com
```

| Tarea | URL | Frecuencia |
|---|---|---|
| Todos los scheduled commands | `GET /api/cron/worker` | Cada minuto |
| Solo IMAP (emails entrantes) | `GET /api/cron/imap` | Cada minuto |
| Solo verificar licencia | `GET /api/cron/license` | Diario 03:00 |
| Diagnóstico IMAP (no procesa) | `GET /api/cron/imap-status` | Manual |

### Comandos curl para crontab
```bash
# Worker general (todos los scheduled commands) — cada minuto
* * * * * root curl -sk https://nicagsm.nexovadesk.com/api/cron/worker > /dev/null 2>&1

# IMAP — cada minuto
* * * * * root curl -sk https://nicagsm.nexovadesk.com/api/cron/imap > /dev/null 2>&1

# Licencia — diario 03:00
0 3 * * * root curl -sk https://nicagsm.nexovadesk.com/api/cron/license > /dev/null 2>&1
```

### Para cron-job.org / EasyCron
```
URL:    https://nicagsm.nexovadesk.com/api/cron/worker
Método: GET
Intervalo: Cada 1 minuto
```

---

## ESTADO ACTUAL EN VPS (2026-04-25)

| Script | Ubicación VPS | En crontab |
|---|---|---|
| `cron-scheduler.sh` | `/www/wwwroot/nexovadesk.com/nicagsm/cron-scheduler.sh` | ✅ cada minuto |
| `worker-monitor.sh` | `/www/wwwroot/nexovadesk.com/nicagsm/worker-monitor.sh` | ✅ cada minuto |
| `worker.sh` | `/www/wwwroot/nexovadesk.com/nicagsm/worker.sh` | llamado por monitor |

**Comandos cubiertos por schedule:run:**
- `tickets:process-inbound` — cada minuto
- `chat:expire-agent-calls` — cada 2 minutos
- `chat:purge-visitors` — cada minuto
- `tickets:auto-close` — hourly
- `partner:check-license` — daily 03:00
- `nexova:recrawl-urls` — weekly

**QUEUE_CONNECTION=sync** — el worker no es crítico ahora pero está corriendo como respaldo.
