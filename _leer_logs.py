import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect('82.25.113.102', port=65002, username='u164741808', password='Pm16567733**',
          timeout=30, allow_agent=False, look_for_keys=False)

base = '/home/u164741808/domains/nexovadesk.com/public_html/nicagsm'

def run(cmd):
    stdin, stdout, stderr = c.exec_command(cmd)
    out = stdout.read().decode(errors='ignore')
    err = stderr.read().decode(errors='ignore')
    return (out + err).strip()

print('=== PHP VERSION ===')
print(run('php -v | head -1'))

print('\n=== LARAVEL LOG - ULTIMAS 300 LINEAS ===')
print(run(f'tail -n 300 {base}/storage/logs/laravel.log'))

print('\n=== GIT LOG ===')
print(run(f'cd {base} && git log --oneline -8'))

c.close()
print('\n=== DONE ===')
