<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\Str;
use App\Core\View;
use App\Repositories\PageRepository;
use App\Repositories\SettingRepository;

$pageRepository = PageRepository::make();
$settings = SettingRepository::make()->all();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$page = $id ? $pageRepository->find($id) : null;

if ($id && !$page) {
    http_response_code(404);
    echo 'Seite nicht gefunden';
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: Str::slug($title);
    $content = $_POST['content'] ?? '';
    $isHome = isset($_POST['is_home']) ? 1 : 0;

    if ($title === '' || $content === '') {
        $error = 'Titel und Inhalt sind erforderlich.';
    } else {
        $payload = [
            'title' => $title,
            'slug' => Str::slug($slug),
            'content' => $content,
            'is_home' => $isHome,
        ];

        if ($page) {
            $payload['id'] = $page['id'];
        }

        $pageRepository->save($payload);
        header('Location: /admin/pages.php');
        exit;
    }
}

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => true],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => false],
];

$pageData = $page ?? ['title' => '', 'slug' => '', 'content' => '', 'is_home' => 0];

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
            <span class="text-slate-300">Titel</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="title" value="<?= htmlspecialchars($pageData['title']); ?>" required>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Slug</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="slug" value="<?= htmlspecialchars($pageData['slug']); ?>">
        </label>
    </div>
    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
        <input type="checkbox" name="is_home" value="1" <?= (int) $pageData['is_home'] === 1 ? 'checked' : ''; ?>>
        Als Startseite verwenden
    </label>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Inhalt (HTML)</span>
        <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-80" name="content" required><?= htmlspecialchars($pageData['content']); ?></textarea>
    </label>
    <div class="flex justify-end gap-3">
        <a class="px-5 py-3 rounded-xl border border-slate-700 hover:border-slate-500" href="/admin/pages.php">Abbrechen</a>
        <button class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" type="submit">Speichern</button>
    </div>
</form>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => $page ? 'Seite bearbeiten' : 'Neue Seite',
    'subtitle' => 'Strukturiere Inhalte für das Reptilien-Wissensarchiv.',
    'content' => $content,
]);
