<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\View;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;

if (!$auth->checkRole('admin')) {
    http_response_code(403);
    echo 'Nur Administratoren dürfen Benutzer verwalten.';
    exit;
}

$userRepository = UserRepository::make();
$settings = SettingRepository::make()->all();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$user = $id ? $userRepository->find($id) : null;

if ($id && !$user) {
    http_response_code(404);
    echo 'Benutzer nicht gefunden';
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'editor';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '') {
        $error = 'Name und E-Mail sind erforderlich.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte eine gültige E-Mail-Adresse eingeben.';
    } else {
        $payload = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ];

        if ($password !== '') {
            $payload['password'] = $password;
        }

        if ($user) {
            $payload['id'] = $user['id'];
        }

        $userRepository->save($payload);
        header('Location: /admin/users.php');
        exit;
    }
}

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => false],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => true],
];

$userData = $user ?? ['name' => '', 'email' => '', 'role' => 'editor'];

ob_start();
?>
<?php if ($error): ?>
    <div class="rounded-xl border border-rose-500/60 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 mb-4">
        <?= htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<form method="post" class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Name</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="name" value="<?= htmlspecialchars($userData['name']); ?>" required>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">E-Mail</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="email" name="email" value="<?= htmlspecialchars($userData['email']); ?>" required>
        </label>
    </div>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Rolle</span>
        <select class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" name="role">
            <option value="admin" <?= $userData['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
            <option value="editor" <?= $userData['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
        </select>
    </label>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Passwort <?= $user ? '(leer lassen für keine Änderung)' : ''; ?></span>
        <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="password" name="password" <?= $user ? '' : 'required'; ?>>
    </label>
    <div class="flex justify-end gap-3">
        <a class="px-5 py-3 rounded-xl border border-slate-700 hover:border-slate-500" href="/admin/users.php">Abbrechen</a>
        <button class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" type="submit">Speichern</button>
    </div>
</form>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => $user ? 'Benutzer bearbeiten' : 'Neuer Benutzer',
    'subtitle' => 'Lege Rollen und Berechtigungen fest.',
    'content' => $content,
]);
