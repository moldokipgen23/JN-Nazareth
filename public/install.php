<?php
/**
 * Ehlom Blog CMS — Web Installer
 *
 * Upload the complete project (including vendor/) to your server,
 * visit /install.php in your browser, follow the steps, then DELETE this file.
 *
 * No terminal / SSH required.
 */

// Project root is one level above public/
define('BASE_PATH', __DIR__);
define('ENV_FILE',  BASE_PATH . '/.env');

session_start();

// ── Helpers ──────────────────────────────────────────────────────────────────

function req(string $key, string $default = ''): string {
    return trim($_POST[$key] ?? $_GET[$key] ?? $default);
}

function isInstalled(): bool {
    return file_exists(ENV_FILE) && str_contains(file_get_contents(ENV_FILE), 'INSTALLED=true');
}

function checkWritable(string $path): bool {
    return is_writable($path);
}

function generateKey(): string {
    return 'base64:' . base64_encode(random_bytes(32));
}

function testMysql(string $host, string $port, string $db, string $user, string $pass): ?string {
    try {
        new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        return null;
    } catch (\PDOException $e) {
        return $e->getMessage();
    }
}

function testSqlite(string $path): ?string {
    try {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        new PDO("sqlite:{$path}");
        return null;
    } catch (\PDOException $e) {
        return $e->getMessage();
    }
}

function writeEnv(array $cfg): void {
    $appKey = generateKey();
    $dbConfig = $cfg['db_connection'] === 'mysql'
        ? "DB_CONNECTION=mysql\nDB_HOST={$cfg['db_host']}\nDB_PORT={$cfg['db_port']}\nDB_DATABASE={$cfg['db_database']}\nDB_USERNAME={$cfg['db_username']}\nDB_PASSWORD={$cfg['db_password']}"
        : "DB_CONNECTION=sqlite\nDB_DATABASE=" . BASE_PATH . "/database/database.sqlite";

    $appName = addslashes($cfg['app_name']);

    $env = <<<ENV
APP_NAME="{$appName}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$cfg['app_url']}
APP_TIMEZONE=UTC

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

{$dbConfig}

BROADCAST_CONNECTION=log
CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="{$appName}"

FILESYSTEM_DISK=local

INSTALLED=true
ENV;

    file_put_contents(ENV_FILE, $env);
}

function runInstaller(array $cfg): array {
    $log = [];

    // 1. Ensure required directories exist and are writable
    $requiredDirs = [
        BASE_PATH . '/storage',
        BASE_PATH . '/storage/app',
        BASE_PATH . '/storage/app/public',
        BASE_PATH . '/storage/app/public/gallery',
        BASE_PATH . '/storage/app/public/programs',
        BASE_PATH . '/storage/app/public/slides',
        BASE_PATH . '/storage/app/public/logos',
        BASE_PATH . '/storage/framework',
        BASE_PATH . '/storage/framework/cache',
        BASE_PATH . '/storage/framework/cache/data',
        BASE_PATH . '/storage/framework/sessions',
        BASE_PATH . '/storage/framework/views',
        BASE_PATH . '/storage/logs',
        BASE_PATH . '/bootstrap/cache',
    ];
    foreach ($requiredDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!is_writable($dir)) {
            @chmod($dir, 0755);
        }
    }
    $log[] = ['ok', 'Storage directories verified'];

    // 2. Clear any stale bootstrap cache
    foreach (glob(BASE_PATH . '/bootstrap/cache/*.php') as $f) {
        @unlink($f);
    }
    $log[] = ['ok', 'Bootstrap cache cleared'];

    // 3. Write .env
    try {
        writeEnv($cfg);
        $log[] = ['ok', 'Configuration file (.env) written'];
    } catch (\Throwable $e) {
        return array_merge($log, [['error', 'Failed to write .env: ' . $e->getMessage()]]);
    }

    // 4. Create SQLite file if needed
    if ($cfg['db_connection'] === 'sqlite') {
        $sqlitePath = BASE_PATH . '/database/database.sqlite';
        if (!file_exists($sqlitePath)) {
            touch($sqlitePath);
        }
        $log[] = ['ok', 'SQLite database file ready'];
    }

    // 5. Bootstrap Laravel
    try {
        require BASE_PATH . '/vendor/autoload.php';
        $app    = require BASE_PATH . '/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $log[]  = ['ok', 'Application bootstrapped successfully'];
    } catch (\Throwable $e) {
        return array_merge($log, [['error', 'Laravel bootstrap failed: ' . $e->getMessage()]]);
    }

    // 6. Run migrations
    try {
        $kernel->call('migrate', ['--force' => true]);
        $log[] = ['ok', 'Database tables created (migrations ran)'];
    } catch (\Throwable $e) {
        return array_merge($log, [['error', 'Migration failed: ' . $e->getMessage()]]);
    }

    // 7. Run seeders
    try {
        $kernel->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
        $log[] = ['ok', 'Default data seeded (roles, settings, sample content)'];
    } catch (\Throwable $e) {
        $log[] = ['warn', 'Seeder issue (non-fatal): ' . $e->getMessage()];
    }

    // 8. Create storage symlink
    try {
        $target = BASE_PATH . '/storage/app/public';
        $link   = BASE_PATH . '/public/storage';
        if (is_link($link)) {
            unlink($link);
        }
        if (!file_exists($link)) {
            symlink($target, $link);
        }
        $log[] = ['ok', 'Storage symlink created (public/storage → storage/app/public)'];
    } catch (\Throwable $e) {
        $log[] = ['warn', 'Could not create storage symlink (try storage:link manually): ' . $e->getMessage()];
    }

    // 9. Create admin user with chosen credentials
    try {
        $adminEmail    = $cfg['admin_email'];
        $adminPassword = $cfg['admin_password'];
        $adminName     = $cfg['admin_name'];

        $db = $app->make('db');

        $db->table('users')->updateOrInsert(
            ['email' => $adminEmail],
            [
                'name'              => $adminName,
                'email'             => $adminEmail,
                'password'          => password_hash($adminPassword, PASSWORD_BCRYPT),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]
        );

        $user = $db->table('users')->where('email', $adminEmail)->first();
        if ($user) {
            $role = $db->table('roles')->where('name', 'admin')->first();
            if ($role) {
                $exists = $db->table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->where('model_id', $user->id)
                    ->where('model_type', 'App\\Models\\User')
                    ->exists();
                if (!$exists) {
                    $db->table('model_has_roles')->insert([
                        'role_id'    => $role->id,
                        'model_id'   => $user->id,
                        'model_type' => 'App\\Models\\User',
                    ]);
                }
            }
        }

        $log[] = ['ok', "Admin account created — {$adminEmail}"];
    } catch (\Throwable $e) {
        $log[] = ['warn', 'Admin account issue (check manually): ' . $e->getMessage()];
    }

    // 10. Cache config & routes for production speed
    try {
        $kernel->call('config:cache');
        $kernel->call('route:cache');
        $log[] = ['ok', 'Config and route cache built for production'];
    } catch (\Throwable $e) {
        $log[] = ['warn', 'Cache build skipped (non-fatal): ' . $e->getMessage()];
    }

    $log[] = ['ok', 'Installation complete!'];
    return $log;
}

// ── Routing ───────────────────────────────────────────────────────────────────

$step   = (int)($_GET['step'] ?? 1);
$errors = [];

if (isInstalled() && $step !== 4) {
    $step = 99;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $dbConn = req('db_connection', 'sqlite');
        if ($dbConn === 'mysql') {
            $err = testMysql(req('db_host','127.0.0.1'), req('db_port','3306'), req('db_database'), req('db_username'), req('db_password'));
            if ($err) $errors[] = "Database connection failed: {$err}";
        } else {
            $sqlitePath = BASE_PATH . '/database/database.sqlite';
            $err = testSqlite($sqlitePath);
            if ($err) $errors[] = "SQLite error: {$err}";
        }

        if (strlen(req('admin_password')) < 8) {
            $errors[] = 'Admin password must be at least 8 characters.';
        }

        if (empty($errors)) {
            $_SESSION['cfg'] = [
                'app_name'       => req('app_name', 'My Community Website'),
                'app_url'        => rtrim(req('app_url', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/'),
                'db_connection'  => $dbConn,
                'db_host'        => req('db_host', '127.0.0.1'),
                'db_port'        => req('db_port', '3306'),
                'db_database'    => req('db_database', 'ehlom_cms'),
                'db_username'    => req('db_username', 'root'),
                'db_password'    => req('db_password'),
                'admin_name'     => req('admin_name', 'Administrator'),
                'admin_email'    => req('admin_email', 'admin@example.com'),
                'admin_password' => req('admin_password'),
            ];
            header('Location: install.php?step=3');
            exit;
        }
    }

    if ($step === 3) {
        $cfg = $_SESSION['cfg'] ?? null;
        if (!$cfg) { header('Location: install.php?step=2'); exit; }
        $_SESSION['install_log'] = runInstaller($cfg);
        header('Location: install.php?step=4');
        exit;
    }
}

// ── Requirements ─────────────────────────────────────────────────────────────
$requirements = [
    ['PHP >= 8.2',           version_compare(PHP_VERSION, '8.2.0', '>=')],
    ['OpenSSL',              extension_loaded('openssl')],
    ['PDO',                  extension_loaded('pdo')],
    ['Mbstring',             extension_loaded('mbstring')],
    ['Tokenizer',            extension_loaded('tokenizer')],
    ['XML',                  extension_loaded('xml')],
    ['Ctype',                extension_loaded('ctype')],
    ['JSON',                 extension_loaded('json')],
    ['BCMath',               extension_loaded('bcmath')],
    ['Fileinfo',             extension_loaded('fileinfo')],
    ['GD or Imagick',        extension_loaded('gd') || extension_loaded('imagick')],
    ['PDO SQLite or MySQL',  extension_loaded('pdo_sqlite') || extension_loaded('pdo_mysql')],
    ['vendor/ folder',       is_dir(BASE_PATH . '/vendor')],
    ['storage/ writable',    checkWritable(BASE_PATH . '/storage')],
    ['bootstrap/cache/ writable', checkWritable(BASE_PATH . '/bootstrap/cache')],
    ['public/ writable',     checkWritable(BASE_PATH . '/public')],
    ['.env writable (or creatable)', !file_exists(ENV_FILE) || is_writable(ENV_FILE)],
];
$allPassed = !in_array(false, array_column($requirements, 1));

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ehlom Blog CMS — Web Installer</title>
<style>
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #14532d 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
.card { background: #fff; border-radius: 20px; width: 100%; max-width: 640px; box-shadow: 0 25px 60px rgba(0,0,0,.35); overflow: hidden; }
.header { background: linear-gradient(135deg, #14532d, #15803d); padding: 28px 32px; text-align: center; }
.header h1 { margin: 0 0 4px; font-size: 22px; color: #fff; font-weight: 700; }
.header p { margin: 0; font-size: 13px; color: rgba(255,255,255,.75); }
.steps { display: flex; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.step-tab { flex: 1; padding: 12px 8px; text-align: center; font-size: 11px; font-weight: 600; color: #94a3b8; border-bottom: 3px solid transparent; }
.step-tab.active { color: #15803d; border-color: #15803d; background: #fff; }
.step-tab.done { color: #22c55e; }
.body { padding: 28px 32px; }
h2 { font-size: 16px; color: #0f172a; margin: 0 0 6px; }
.sub { font-size: 13px; color: #64748b; margin: 0 0 24px; }
label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; }
input[type=text], input[type=url], input[type=email], input[type=password], input[type=number], select {
    width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 9px; font-size: 13px;
    outline: none; transition: border-color .15s; background: #fff; color: #0f172a; }
input:focus, select:focus { border-color: #15803d; box-shadow: 0 0 0 3px rgba(21,128,61,.1); }
.row { margin-bottom: 16px; }
.grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.grid3 { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; }
.btn { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #14532d, #15803d); color: #fff; border: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; justify-content: center; margin-top: 8px; text-decoration: none; }
.btn:hover { opacity: .92; }
.btn-outline { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.req-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
.req-item.pass { background: #f0fdf4; color: #166534; }
.req-item.fail { background: #fff1f2; color: #9f1239; }
.dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot.green { background: #22c55e; }
.dot.red   { background: #ef4444; }
.log-item { display: flex; align-items: flex-start; gap: 10px; padding: 8px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
.log-item.ok   { background: #f0fdf4; color: #166534; }
.log-item.warn { background: #fffbeb; color: #92400e; }
.log-item.error{ background: #fff1f2; color: #9f1239; }
.errors { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 12px 16px; margin-bottom:18px; font-size:13px; color:#9f1239; }
.errors ul { margin:0; padding-left:16px; }
.info-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 18px; margin:16px 0; font-size:13px; color:#15803d; }
.info-box strong { color:#0f172a; display:block; margin-bottom:4px; }
.divider { height:1px; background:#f1f5f9; margin:20px 0; }
.radio-group { display:flex; gap:10px; margin-bottom:16px; }
.radio-card { flex:1; border:2px solid #e2e8f0; border-radius:10px; padding:12px; cursor:pointer; transition:.15s; }
.radio-card:has(input:checked) { border-color:#15803d; background:#f0fdf4; }
.radio-card input { display:none; }
.radio-card .rc-title { font-size:13px; font-weight:700; color:#0f172a; margin-bottom:2px; }
.radio-card .rc-sub { font-size:11px; color:#64748b; }
#mysql-fields { display:none; }
.already { text-align:center; padding:20px 0; }
.already p { color:#64748b; font-size:13px; }
</style>
</head>
<body>

<?php if ($step === 99): ?>
<div class="card">
    <div class="header">
        <h1>Ehlom Blog CMS</h1>
        <p>Web Installer</p>
    </div>
    <div class="body">
        <div class="already">
            <div style="font-size:48px; margin-bottom:12px;">✅</div>
            <h2>Already Installed</h2>
            <p>This site is already installed. The installer is disabled for security.<br>
            Please <strong>delete install.php</strong> from your server immediately.</p>
            <br>
            <a href="/" class="btn">Go to Website</a>
            <br><br>
            <a href="/admin" class="btn btn-outline" style="margin-top:8px; display:flex;">Go to Admin Panel</a>
        </div>
    </div>
</div>

<?php elseif ($step === 4): ?>
<?php $log = $_SESSION['install_log'] ?? []; $hasError = in_array('error', array_column($log, 0)); $cfg = $_SESSION['cfg'] ?? []; ?>
<div class="card">
    <div class="header">
        <h1>Ehlom Blog CMS</h1>
        <p>Web Installer</p>
    </div>
    <div class="steps">
        <div class="step-tab done">① Requirements</div>
        <div class="step-tab done">② Configuration</div>
        <div class="step-tab done">③ Install</div>
        <div class="step-tab active">④ Complete</div>
    </div>
    <div class="body">
        <?php if ($hasError): ?>
            <h2 style="color:#e11d48;">Installation Failed</h2>
            <p class="sub">Some steps failed. See the log below.</p>
        <?php else: ?>
            <h2 style="color:#16a34a;">Installation Successful! 🎉</h2>
            <p class="sub">Your site is ready to use.</p>
        <?php endif; ?>

        <div style="margin-bottom:20px;">
            <?php foreach ($log as [$type, $msg]): ?>
            <div class="log-item <?= $type ?>">
                <span><?= $type === 'ok' ? '✓' : ($type === 'warn' ? '⚠' : '✗') ?></span>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$hasError): ?>
        <div class="info-box">
            <strong>Admin Login Credentials</strong>
            Admin URL: <strong><?= htmlspecialchars(($cfg['app_url'] ?? '') . '/admin') ?></strong><br>
            Email: <strong><?= htmlspecialchars($cfg['admin_email'] ?? '') ?></strong><br>
            Password: <strong><?= htmlspecialchars($cfg['admin_password'] ?? '') ?></strong>
        </div>
        <?php endif; ?>

        <div class="info-box" style="background:#fffbeb; border-color:#fde68a; color:#92400e;">
            <strong>Security: Delete install.php now</strong>
            After confirming the site works, delete <strong>install.php</strong> from your server via cPanel File Manager or FTP. Leaving it accessible is a serious security risk.
        </div>

        <a href="/" class="btn" style="margin-bottom:10px; display:flex;">Visit Website</a>
        <a href="/admin" class="btn btn-outline" style="display:flex; margin-top:8px;">Go to Admin Panel</a>
    </div>
</div>

<?php elseif ($step === 3): ?>
<?php $cfg = $_SESSION['cfg'] ?? null; if (!$cfg) { header('Location: install.php?step=2'); exit; } ?>
<div class="card">
    <div class="header">
        <h1>Ehlom Blog CMS</h1>
        <p>Web Installer</p>
    </div>
    <div class="steps">
        <div class="step-tab done">① Requirements</div>
        <div class="step-tab done">② Configuration</div>
        <div class="step-tab active">③ Install</div>
        <div class="step-tab">④ Complete</div>
    </div>
    <div class="body">
        <h2>Ready to Install</h2>
        <p class="sub">Review your settings, then click Install to begin. This may take a few seconds.</p>

        <div style="background:#f8fafc; border-radius:12px; padding:16px; margin-bottom:20px; font-size:13px;">
            <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e2e8f0;"><span style="color:#64748b;">Site Name</span><strong><?= htmlspecialchars($cfg['app_name']) ?></strong></div>
            <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e2e8f0;"><span style="color:#64748b;">Site URL</span><strong><?= htmlspecialchars($cfg['app_url']) ?></strong></div>
            <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e2e8f0;"><span style="color:#64748b;">Database</span><strong><?= $cfg['db_connection'] === 'mysql' ? "MySQL — {$cfg['db_database']}" : 'SQLite (file-based)' ?></strong></div>
            <div style="display:flex; justify-content:space-between; padding:6px 0;"><span style="color:#64748b;">Admin Email</span><strong><?= htmlspecialchars($cfg['admin_email']) ?></strong></div>
        </div>

        <form method="POST" action="install.php?step=3">
            <button type="submit" class="btn">Install Now</button>
        </form>
        <a href="install.php?step=2" style="display:block; text-align:center; margin-top:12px; font-size:12px; color:#94a3b8; text-decoration:none;">← Go back and change settings</a>
    </div>
</div>

<?php elseif ($step === 2): ?>
<?php
$detectedUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$detectedUrl = rtrim(preg_replace('#/install\.php.*#', '', $detectedUrl . $_SERVER['REQUEST_URI']), '/');
?>
<div class="card">
    <div class="header">
        <h1>Ehlom Blog CMS</h1>
        <p>Web Installer</p>
    </div>
    <div class="steps">
        <div class="step-tab done">① Requirements</div>
        <div class="step-tab active">② Configuration</div>
        <div class="step-tab">③ Install</div>
        <div class="step-tab">④ Complete</div>
    </div>
    <div class="body">
        <h2>Site & Database Configuration</h2>
        <p class="sub">Fill in your details. All fields marked * are required.</p>

        <?php if (!empty($errors)): ?>
        <div class="errors"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" action="install.php?step=2">

            <div class="row">
                <label>Site Name *</label>
                <input type="text" name="app_name" value="<?= htmlspecialchars(req('app_name','My Community Website')) ?>" required placeholder="My Community Website">
            </div>

            <div class="row">
                <label>Site URL * <span style="font-weight:400; color:#94a3b8;">(no trailing slash)</span></label>
                <input type="text" name="app_url" value="<?= htmlspecialchars(req('app_url', $detectedUrl)) ?>" required placeholder="https://yourdomain.com">
            </div>

            <div class="divider"></div>
            <label style="margin-bottom:10px;">Database Type *</label>
            <div class="radio-group">
                <label class="radio-card">
                    <input type="radio" name="db_connection" value="sqlite" <?= req('db_connection','sqlite')==='sqlite'?'checked':'' ?> onclick="document.getElementById('mysql-fields').style.display='none'">
                    <div class="rc-title">SQLite</div>
                    <div class="rc-sub">Recommended — no setup needed, file-based</div>
                </label>
                <label class="radio-card">
                    <input type="radio" name="db_connection" value="mysql" <?= req('db_connection')==='mysql'?'checked':'' ?> onclick="document.getElementById('mysql-fields').style.display='block'">
                    <div class="rc-title">MySQL / MariaDB</div>
                    <div class="rc-sub">cPanel hosting — requires DB credentials</div>
                </label>
            </div>

            <div id="mysql-fields">
                <div class="grid3 row">
                    <div>
                        <label>MySQL Host *</label>
                        <input type="text" name="db_host" value="<?= htmlspecialchars(req('db_host','localhost')) ?>" placeholder="localhost">
                    </div>
                    <div>
                        <label>Port</label>
                        <input type="number" name="db_port" value="<?= htmlspecialchars(req('db_port','3306')) ?>" placeholder="3306">
                    </div>
                </div>
                <div class="row">
                    <label>Database Name *</label>
                    <input type="text" name="db_database" value="<?= htmlspecialchars(req('db_database')) ?>" placeholder="ehlom_cms">
                </div>
                <div class="grid2 row">
                    <div>
                        <label>Database Username *</label>
                        <input type="text" name="db_username" value="<?= htmlspecialchars(req('db_username')) ?>" placeholder="db_user">
                    </div>
                    <div>
                        <label>Database Password</label>
                        <input type="password" name="db_password" value="<?= htmlspecialchars(req('db_password')) ?>" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="divider"></div>
            <div class="row">
                <label>Admin Full Name *</label>
                <input type="text" name="admin_name" value="<?= htmlspecialchars(req('admin_name','Administrator')) ?>" required>
            </div>
            <div class="row">
                <label>Admin Email *</label>
                <input type="email" name="admin_email" value="<?= htmlspecialchars(req('admin_email','admin@yourdomain.com')) ?>" required>
            </div>
            <div class="row">
                <label>Admin Password * <span style="font-weight:400; color:#94a3b8;">(min 8 characters)</span></label>
                <input type="password" name="admin_password" value="" required minlength="8" placeholder="Choose a strong password">
            </div>

            <button type="submit" class="btn">Next: Review & Install →</button>
        </form>
    </div>
</div>
<script>
if (document.querySelector('input[name=db_connection]:checked')?.value === 'mysql') {
    document.getElementById('mysql-fields').style.display = 'block';
}
</script>

<?php else: // Step 1 — Requirements ?>
<div class="card">
    <div class="header">
        <h1>Ehlom Blog CMS</h1>
        <p>Web Installer — Community CMS</p>
    </div>
    <div class="steps">
        <div class="step-tab active">① Requirements</div>
        <div class="step-tab">② Configuration</div>
        <div class="step-tab">③ Install</div>
        <div class="step-tab">④ Complete</div>
    </div>
    <div class="body">
        <h2>Server Requirements Check</h2>
        <p class="sub">Verifying your server meets all requirements before installation.</p>

        <?php foreach ($requirements as [$label, $pass]): ?>
        <div class="req-item <?= $pass ? 'pass' : 'fail' ?>">
            <span class="dot <?= $pass ? 'green' : 'red' ?>"></span>
            <span><?= htmlspecialchars($label) ?></span>
            <span style="margin-left:auto; font-size:11px; font-weight:700;"><?= $pass ? 'PASS' : 'FAIL' ?></span>
        </div>
        <?php endforeach; ?>

        <?php if (!$allPassed): ?>
        <div class="info-box" style="margin-top:16px; background:#fff1f2; border-color:#fecdd3; color:#9f1239;">
            <strong>Requirements not met</strong>
            Fix the failed items above before continuing. Contact your hosting provider to enable required PHP extensions or fix permissions.
        </div>
        <?php else: ?>
        <div class="info-box" style="margin-top:16px;">
            <strong>All requirements passed</strong>
            Your server is ready. Click Continue to configure your installation.
        </div>
        <?php endif; ?>

        <?php if (!is_dir(BASE_PATH . '/vendor')): ?>
        <div class="info-box" style="background:#fff1f2; border-color:#fecdd3; color:#9f1239; margin-top:12px;">
            <strong>vendor/ folder missing</strong>
            Upload the complete project including the <strong>vendor/</strong> folder. Use the pre-packaged release — not just the source code files.
        </div>
        <?php endif; ?>

        <a href="install.php?step=2" class="btn" style="margin-top:16px; display:flex; <?= !$allPassed ? 'opacity:.5; pointer-events:none;' : '' ?>">
            Continue →
        </a>
    </div>
</div>
<?php endif; ?>

</body>
</html>
