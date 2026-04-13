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

print('=== PHP VERSION ===')
run('php -v')

print('\n=== LAST 150 LINES LARAVEL LOG ===')
run(f'tail -n 150 {base}/storage/logs/laravel.log')

print('\n=== GIT LOG (last 5) ===')
run(f'cd {base} && git log --oneline -5')

c.close()
print('\nDONE')
