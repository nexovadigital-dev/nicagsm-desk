import paramiko, secrets

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect('82.25.113.102', port=65002, username='u164741808', password='Pm16567733**',
          timeout=30, allow_agent=False, look_for_keys=False)

base = '/home/u164741808/domains/nexovadesk.com/public_html/nicagsm'

def run(cmd, timeout=30):
    s, o, e = c.exec_command(cmd, timeout=timeout)
    out = o.read().decode(errors='ignore').strip()
    err = e.read().decode(errors='ignore').strip()
    print('OUT:', out[:3000] if out else '(vacío)')
    if err: print('ERR:', err[:1000])
    return out

# Leer .env actual
print('==> Leer CRON_SECRET actual...')
current = run(f"grep 'CRON_SECRET' {base}/.env 2>/dev/null || echo 'NOEXISTE'")
print('Valor actual:', current)

# Generar token robusto
token = secrets.token_hex(24)
print(f'\n==> Token nuevo: {token}')

# Reemplazar o agregar en .env
if 'NOEXISTE' in current or 'CRON_SECRET=' not in current:
    run(f"echo 'CRON_SECRET={token}' >> {base}/.env")
    print('[OK] CRON_SECRET agregado al .env')
else:
    # Reemplazar la línea existente
    run(f"sed -i 's/^CRON_SECRET=.*/CRON_SECRET={token}/' {base}/.env")
    print('[OK] CRON_SECRET reemplazado en .env')

# Verificar
new_val = run(f"grep 'CRON_SECRET' {base}/.env")
print('Valor en .env:', new_val)

# Limpiar caché de config
run(f'cd {base} && php artisan config:clear && php artisan config:cache 2>&1')

# Probar endpoint
print(f'\n==> Probar: https://nicagsm.nexovadesk.com/api/cron/imap?token={token}')
result = run(f"curl -s 'https://nicagsm.nexovadesk.com/api/cron/imap?token={token}'", timeout=60)
print('Resultado:', result)

c.close()
print(f'\n=== CRON_SECRET FINAL: {token} ===')
