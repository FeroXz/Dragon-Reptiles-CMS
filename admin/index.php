<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\View;
use App\Repositories\PageRepository;
use App\Repositories\PostRepository;
use App\Repositories\SettingRepository;

$settings = SettingRepository::make()->all();
$pages = PageRepository::make()->all();
$posts = PostRepository::make()->all();

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => true],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => false],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => false],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => false],
];

ob_start();
?>
<section class="grid gap-6 md:grid-cols-3">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
        <p class="text-sm text-slate-400">Seiten</p>
        <p class="text-3xl font-semibold text-cyan-300"><?= count($pages); ?></p>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
        <p class="text-sm text-slate-400">News Beiträge</p>
        <p class="text-3xl font-semibold text-cyan-300"><?= count($posts); ?></p>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
        <p class="text-sm text-slate-400">Aktive Version</p>
        <p class="text-3xl font-semibold text-cyan-300"><?= \App\Services\VersionManager::latest(); ?></p>
    </div>
</section>
<section class="grid gap-6 md:grid-cols-2">
    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
        <h3 class="text-lg font-semibold text-slate-200 mb-4">Letzte Seiten</h3>
        <ul class="space-y-2 text-sm text-slate-300">
            <?php foreach (array_slice($pages, 0, 5) as $page): ?>
                <li class="flex justify-between"><span><?= htmlspecialchars($page['title']); ?></span><span class="text-slate-500"><?= (new DateTimeImmutable($page['updated_at']))->format('d.m.Y'); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
        <h3 class="text-lg font-semibold text-slate-200 mb-4">Letzte News</h3>
        <ul class="space-y-2 text-sm text-slate-300">
            <?php foreach (array_slice($posts, 0, 5) as $post): ?>
                <li class="flex justify-between"><span><?= htmlspecialchars($post['title']); ?></span><span class="text-slate-500"><?= (new DateTimeImmutable($post['updated_at']))->format('d.m.Y'); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => 'Übersicht',
    'subtitle' => 'Willkommen zurück, ' . htmlspecialchars($currentUser['name'] ?? 'Admin'),
    'content' => $content,
]);
