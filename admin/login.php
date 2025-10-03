<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Repositories\SettingRepository;
use App\Services\AuthService;

$settings = SettingRepository::make()->all();
$auth = AuthService::make();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($auth->attempt($email, $password)) {
        header('Location: /admin/index.php');
        exit;
    }

    $error = 'Login fehlgeschlagen. Bitte Zugangsdaten prüfen.';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · <?= htmlspecialchars($settings['site_title'] ?? 'Dragon Reptiles CMS'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background: radial-gradient(circle at top, rgba(14,165,233,0.12), #020617 55%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 rounded-3xl bg-slate-900/80 backdrop-blur border border-slate-800">
        <h1 class="text-2xl font-semibold text-cyan-300 mb-2">Dragon Reptiles CMS</h1>
        <p class="text-sm text-slate-400 mb-6">Admin Login</p>
        <?php if ($error): ?>
            <div class="mb-4 rounded-lg border border-rose-500/60 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-300 mb-1" for="email">E-Mail</label>
                <input class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm focus:border-cyan-400 focus:outline-none" type="email" name="email" id="email" required>
            </div>
            <div>
                <label class="block text-sm text-slate-300 mb-1" for="password">Passwort</label>
                <input class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm focus:border-cyan-400 focus:outline-none" type="password" name="password" id="password" required>
            </div>
            <button class="w-full rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 py-3 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" type="submit">Anmelden</button>
        </form>
        <p class="text-xs text-slate-500 mt-6">Standardzugang: admin@example.com · ChangeMe123!</p>
    </div>
</body>
</html>
