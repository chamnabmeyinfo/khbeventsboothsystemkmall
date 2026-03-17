<?php
/**
 * ONE-TIME SCRIPT: Seed cPanel DB credentials into settings table.
 * DELETE this file after running it!
 * Access via: http://localhost/KHB/khbevents/boothsystemv1/public/seed-cpanel-settings.php
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$settings = [
    ['cpanel_remote_db_host',     'localhost',                 'string',  'Remote cPanel DB host'],
    ['cpanel_remote_db_port',     '3306',                     'string',  'Remote cPanel DB port'],
    ['cpanel_remote_db_database', 'khbevents_aebooths',       'string',  'Remote cPanel DB name'],
    ['cpanel_remote_db_username', 'khbevents_admaebooths',    'string',  'Remote cPanel DB username'],
    ['cpanel_remote_db_password', 'VC2eY12CqARXmc4dDg',      'string',  'Remote cPanel DB password'],
    ['cpanel_remote_ssh_host',    'system.khbevents.com',     'string',  'Remote SSH host'],
    ['cpanel_remote_ssh_user',    'khbevents',                'string',  'Remote SSH user'],
    ['cpanel_use_ssh',            '1',                        'boolean', 'Use SSH for DB pull'],
];

$results = [];
foreach ($settings as [$key, $value, $type, $desc]) {
    try {
        Setting::setValue($key, $value, $type, $desc);
        $results[] = ['key' => $key, 'status' => '✅ Saved', 'value' => $key === 'cpanel_remote_db_password' ? '***hidden***' : $value];
    } catch (Exception $e) {
        $results[] = ['key' => $key, 'status' => '❌ Error: ' . $e->getMessage(), 'value' => ''];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>cPanel Settings Seeder</title>
    <style>
        body { font-family: monospace; background: #0f1117; color: #e2e8f0; padding: 2rem; }
        h2 { color: #a78bfa; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { padding: .6rem 1rem; border: 1px solid #2d3748; text-align: left; }
        th { background: #1e2733; color: #93c5fd; }
        .ok { color: #6ee7b7; }
        .err { color: #fca5a5; }
        .warn { background: #7c3a00; color: #fed7aa; padding: 1rem; border-radius: 8px; margin-top: 1.5rem; font-size: .9rem; }
    </style>
</head>
<body>
<h2>⚙️ cPanel Database Settings — Seeder</h2>
<table>
    <thead><tr><th>Key</th><th>Status</th><th>Value</th></tr></thead>
    <tbody>
    <?php foreach ($results as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['key']) ?></td>
        <td class="<?= str_contains($r['status'], '✅') ? 'ok' : 'err' ?>"><?= htmlspecialchars($r['status']) ?></td>
        <td><?= htmlspecialchars($r['value']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="warn">
    ⚠️ <strong>IMPORTANT:</strong> Delete this file immediately after running it!<br>
    <code>DELETE: public/seed-cpanel-settings.php</code>
</div>
</body>
</html>
