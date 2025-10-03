<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\View;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;

$userRepository = UserRepository::make();
$settings = SettingRepository::make()->all();

if (!$auth->checkRole('admin')) {
    http_response_code(403);
    echo 'Nur Administratoren dürfen Benutzer verwalten.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if ((int) $_POST['delete_id'] === (int) ($currentUser['id'] ?? 0)) {
        $error = 'Du kannst dich nicht selbst löschen.';
    } else {
        $userRepository->delete((int) $_POST['delete_id']);
        header('Location: /admin/users.php');
        exit;
    }
}

$users = $userRepository->all();
$error = $error ?? null;

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => false],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => true],
];

ob_start();
?>
<?php if ($error): ?>
    <div class="rounded-xl border border-rose-500/60 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 mb-4">
        <?= htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<div class="flex justify-end mb-4">
    <a class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" href="/admin/user_edit.php">Neuer Benutzer</a>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-800">
    <table class="min-w-full divide-y divide-slate-800 text-sm">
        <thead class="bg-slate-900/60 text-slate-400 uppercase text-xs tracking-widest">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">E-Mail</th>
                <th class="px-4 py-3 text-left">Rolle</th>
                <th class="px-4 py-3 text-left">Aktualisiert</th>
                <th class="px-4 py-3 text-right">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-slate-900/40">
                    <td class="px-4 py-3 font-medium text-slate-200"><?= htmlspecialchars($user['name']); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($user['email']); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($user['role']); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= (new DateTimeImmutable($user['updated_at']))->format('d.m.Y H:i'); ?></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a class="px-3 py-1 rounded-lg border border-slate-700 hover:border-cyan-400" href="/admin/user_edit.php?id=<?= $user['id']; ?>">Bearbeiten</a>
                            <?php if ((int) $user['id'] !== (int) ($currentUser['id'] ?? 0)): ?>
                                <form method="post" onsubmit="return confirm('Benutzer wirklich löschen?');">
                                    <input type="hidden" name="delete_id" value="<?= $user['id']; ?>">
                                    <button class="px-3 py-1 rounded-lg border border-rose-600 text-rose-300 hover:bg-rose-600/20" type="submit">Löschen</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => 'Benutzerverwaltung',
    'subtitle' => 'Steuere den Zugriff auf dein Reptilien-Wissensarchiv.',
    'content' => $content,
]);
