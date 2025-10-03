<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\View;
use App\Repositories\PageRepository;
use App\Repositories\SettingRepository;

$pageRepository = PageRepository::make();
$settings = SettingRepository::make()->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $pageRepository->delete((int) $_POST['delete_id']);
    header('Location: /admin/pages.php');
    exit;
}

$pages = $pageRepository->all();

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => true],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => false],
];

ob_start();
?>
<div class="flex justify-end mb-4">
    <a class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" href="/admin/page_edit.php">Neue Seite</a>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-800">
    <table class="min-w-full divide-y divide-slate-800 text-sm">
        <thead class="bg-slate-900/60 text-slate-400 uppercase text-xs tracking-widest">
            <tr>
                <th class="px-4 py-3 text-left">Titel</th>
                <th class="px-4 py-3 text-left">Slug</th>
                <th class="px-4 py-3 text-left">Aktualisiert</th>
                <th class="px-4 py-3 text-left">Startseite</th>
                <th class="px-4 py-3 text-right">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            <?php foreach ($pages as $page): ?>
                <tr class="hover:bg-slate-900/40">
                    <td class="px-4 py-3 font-medium text-slate-200"><?= htmlspecialchars($page['title']); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($page['slug']); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= (new DateTimeImmutable($page['updated_at']))->format('d.m.Y H:i'); ?></td>
                    <td class="px-4 py-3 text-slate-400"><?= (int) $page['is_home'] === 1 ? 'Ja' : 'Nein'; ?></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a class="px-3 py-1 rounded-lg border border-slate-700 hover:border-cyan-400" href="/admin/page_edit.php?id=<?= $page['id']; ?>">Bearbeiten</a>
                            <form method="post" onsubmit="return confirm('Seite wirklich löschen?');">
                                <input type="hidden" name="delete_id" value="<?= $page['id']; ?>">
                                <button class="px-3 py-1 rounded-lg border border-rose-600 text-rose-300 hover:bg-rose-600/20" type="submit">Löschen</button>
                            </form>
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
    'title' => 'Seitenverwaltung',
    'subtitle' => 'Erstelle und pflege Seiteninhalte.',
    'content' => $content,
]);
