# Deploy Notes — nicagsm.nexovadesk.com

## 1. Cloudflare DNS
Agregar en nexovadesk.com zone:
- Type: CNAME | Name: nicagsm | Target: nexovadesk.com | Proxy: ON

## 2. Hostinger hPanel
Ir a Subdominios → Crear:
- Subdominio: nicagsm
- Dominio: nexovadesk.com
- Document root: /home/u164741808/domains/nexovadesk.com/public_html/nicagsm/public

## 3. En servidor via SSH
```bash
cd /home/u164741808/domains/nexovadesk.com/public_html/
git clone https://github.com/nexovadigital-dev/nicagsm-desk.git nicagsm
cd nicagsm
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --omit=dev && npm run build
cp .env.example .env
php artisan key:generate
# Editar .env con DB, PARTNER_TOKEN, APP_URL=https://nicagsm.nexovadesk.com
php artisan migrate --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
chmod -R 755 storage bootstrap/cache
```

## 4. Variables .env críticas a cambiar
```
APP_URL=https://nicagsm.nexovadesk.com
APP_NAME="Nexova Desk"
DB_DATABASE=u164741808_nicagsm   # base de datos separada
DB_USERNAME=u164741808_nicagsm
DB_PASSWORD=...

PARTNER_TOKEN=<token copiado del HQ de nexovadesk.com>
PARTNER_NAME="NicaGSM"
NEXOVA_LICENSE_URL=https://nexovadesk.com
```

## 5. Cuando el cliente tenga su propio dominio (ej: desk.nicagsm.com)
Solo cambiar:
1. `.env` → `APP_URL=https://desk.nicagsm.com`
2. Plugin WP → `apiUrl: 'https://desk.nicagsm.com'`
3. DNS del cliente → CNAME a tu servidor
4. `php artisan config:cache` en el servidor

## 6. Generar PARTNER_TOKEN para nicagsm
En nexovadesk.com HQ → Organizaciones → Buscar nicagsm → Asignar Plan Partner
El token aparece como botón al lado del badge "PARTNER ✓" (click para copiar)
