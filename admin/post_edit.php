<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\Str;
use App\Core\View;
use App\Repositories\PostRepository;
use App\Repositories\SettingRepository;

$postRepository = PostRepository::make();
$settings = SettingRepository::make()->all();

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$post = $id ? $postRepository->find($id) : null;

if ($id && !$post) {
    http_response_code(404);
    echo 'Beitrag nicht gefunden';
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: Str::slug($title);
    $excerpt = $_POST['excerpt'] ?? null;
    $content = $_POST['content'] ?? '';
    $publishedAt = $_POST['published_at'] ?? null;
    $publishedAtIso = null;

    if ($title === '' || $content === '') {
        $error = 'Titel und Inhalt sind erforderlich.';
    } else {
        if ($publishedAt) {
            try {
                $publishedAtIso = (new \DateTimeImmutable($publishedAt))->format(DATE_ATOM);
            } catch (\Exception) {
                $error = 'Ungültiges Veröffentlichungsdatum.';
            }
        }

        if ($error === null) {
        $payload = [
            'title' => $title,
            'slug' => Str::slug($slug),
            'excerpt' => $excerpt,
            'content' => $content,
            'published_at' => $publishedAtIso,
        ];

        if ($post) {
            $payload['id'] = $post['id'];
        }

        $postRepository->save($payload);
        header('Location: /admin/posts.php');
        exit;
        }
    }
}

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => false],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => true],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => false],
];

$postData = $post ?? ['title' => '', 'slug' => '', 'excerpt' => '', 'content' => '', 'published_at' => null];

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
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="title" value="<?= htmlspecialchars($postData['title']); ?>" required>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Slug</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="slug" value="<?= htmlspecialchars($postData['slug']); ?>">
        </label>
    </div>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Kurzbeschreibung</span>
        <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-32" name="excerpt"><?= htmlspecialchars($postData['excerpt']); ?></textarea>
    </label>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Inhalt (HTML)</span>
        <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-80" name="content" required><?= htmlspecialchars($postData['content']); ?></textarea>
    </label>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Veröffentlichungsdatum</span>
        <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="datetime-local" name="published_at" value="<?= $postData['published_at'] ? (new DateTimeImmutable($postData['published_at']))->format('Y-m-d\TH:i') : ''; ?>">
        <span class="text-xs text-slate-500">Leer lassen um als Entwurf zu speichern.</span>
    </label>
    <div class="flex justify-end gap-3">
        <a class="px-5 py-3 rounded-xl border border-slate-700 hover:border-slate-500" href="/admin/posts.php">Abbrechen</a>
        <button class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" type="submit">Speichern</button>
    </div>
</form>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => $post ? 'Beitrag bearbeiten' : 'Neuer Beitrag',
    'subtitle' => 'Teile Wissen und Neuigkeiten mit der Community.',
    'content' => $content,
]);
