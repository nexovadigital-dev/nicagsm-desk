import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect('82.25.113.102', port=65002, username='u164741808', password='Pm16567733**',
          timeout=30, allow_agent=False, look_for_keys=False)

base = '/home/u164741808/domains/nexovadesk.com/public_html/nicagsm'

def run(cmd):
    stdin, stdout, stderr = c.exec_command(cmd)
    out = stdout.read().decode(errors='ignore').strip()
    err = stderr.read().decode(errors='ignore').strip()
    if out: print(out)
    if err: print('[ERR]', err)

print('==> git pull...')
run(f'cd {base} && git pull origin main 2>&1')

print('==> migrate...')
run(f'cd {base} && php artisan migrate --force 2>&1')

print('==> optimize:clear...')
run(f'cd {base} && php artisan config:clear 2>&1')
run(f'cd {base} && php artisan route:clear 2>&1')
run(f'cd {base} && php artisan view:clear 2>&1')
run(f'cd {base} && php artisan cache:clear 2>&1')
run(f'cd {base} && php artisan config:cache 2>&1')
run(f'cd {base} && php artisan route:cache 2>&1')

c.close()
print('DEPLOY DONE')
