import paramiko, sys
sys.stdout.reconfigure(encoding='utf-8')

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect('82.25.113.102', port=65002, username='u164741808', password='Pm16567733**',
          timeout=60, allow_agent=False, look_for_keys=False)

base = '/home/u164741808/domains/nexovadesk.com/public_html/nicagsm'
php  = '/opt/alt/php82/usr/bin/php'

def run(cmd):
    stdin, stdout, stderr = c.exec_command(cmd, timeout=45)
    out = stdout.read().decode('utf-8', errors='replace')
    err = stderr.read().decode('utf-8', errors='replace')
    return (out + err).strip()

# Inyectar la clave directamente via SQL (bypass Livewire)
print('=== GUARDANDO CLAVE IMAP VIA ARTISAN ===')
# Usamos tinker para actualizar directamente
r = run(f"""cd {base} && {php} artisan tinker --execute="App\\\\Models\\\\SmtpSetting::where('organization_id',1)->update(['imap_password' => 'Pm16567733**']);" 2>&1""")
print(r)

# Verificar que se guardo
print('\n=== VERIFICANDO CLAVE GUARDADA ===')
r2 = run(f"""cd {base} && {php} artisan tinker --execute="\\$s=App\\\\Models\\\\SmtpSetting::where('organization_id',1)->first(); echo strlen(\\$s->imap_password ?? '').\" chars guardados\";" 2>&1""")
print(r2)

# Ahora probar la conexion directamente
test_conn = r"""<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
$app = require '/home/u164741808/domains/nexovadesk.com/public_html/nicagsm/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$s = \App\Models\SmtpSetting::where('organization_id', 1)->first();
echo "host:{$s->imap_host} user:{$s->imap_username} folder:{$s->imap_folder} pass_len:".strlen($s->imap_password ?? '')."\n";
$dsn = "{{$s->imap_host}:{$s->imap_port}/imap/ssl}{$s->imap_folder}";
echo "DSN: $dsn\n";
$conn = @imap_open($dsn, $s->imap_username, $s->imap_password, 0, 1);
if ($conn) {
    $n = imap_num_msg($conn);
    echo "OK! Mensajes en {$s->imap_folder}: $n\n";
    // Ver INBOX tambien
    imap_close($conn);
    $conn2 = @imap_open("{{$s->imap_host}:{$s->imap_port}/imap/ssl}INBOX", $s->imap_username, $s->imap_password, 0, 1);
    if ($conn2) {
        $unseen = imap_search($conn2, 'UNSEEN', SE_UID);
        echo "INBOX no leidos: ".($unseen ? count($unseen) : 0)."\n";
        if ($unseen) {
            foreach(array_slice($unseen, 0, 3) as $uid) {
                $h = imap_rfc822_parse_headers(imap_fetchheader($conn2, $uid, FT_UID));
                $subj = isset($h->subject) ? imap_utf8($h->subject) : '';
                echo "  UID:$uid Asunto:$subj\n";
            }
        }
        imap_close($conn2);
    }
} else {
    echo "ERROR: " . imap_last_error() . "\n";
}
"""
sftp = c.open_sftp()
sftp.open('/tmp/test_conn.php', 'w').write(test_conn)
sftp.close()
print('\n=== PRUEBA CONEXION CON CLAVE ACTUALIZADA ===')
print(run(f'{php} /tmp/test_conn.php 2>&1'))

# Ejecutar el comando IMAP directamente
print('\n=== EJECUTAR tickets:process-inbound AHORA ===')
print(run(f'cd {base} && {php} -d display_errors=1 -d log_errors=0 artisan tickets:process-inbound 2>&1'))

c.close()
print('\n=== DONE ===')
