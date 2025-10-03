<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Core\View;
use App\Repositories\PageRepository;
use App\Repositories\PostRepository;
use App\Repositories\SettingRepository;

$settingsRepo = SettingRepository::make();
$pageRepo = PageRepository::make();
$postRepo = PostRepository::make();

$settings = $settingsRepo->all();
$home = $pageRepo->findHomePage();
$posts = array_slice($postRepo->published(), 0, 3);

ob_start();
?>
<div class="space-y-8">
    <section class="p-8 rounded-3xl bg-gradient-to-r from-cyan-500/20 via-indigo-500/20 to-purple-500/20 border border-slate-800">
        <?= $settings['homepage_intro'] ?? '<p>Erkunde die faszinierende Vielfalt der Reptilienwelt mit datengestützten Einblicken.</p>'; ?>
    </section>
    <?php if ($home): ?>
        <section class="space-y-4 leading-relaxed text-slate-200">
            <?= $home['content']; ?>
        </section>
    <?php endif; ?>
    <?php if ($posts): ?>
        <section>
            <h2 class="text-xl font-semibold mb-4">Aktuelle News</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <?php foreach ($posts as $post): ?>
                    <article class="rounded-2xl border border-slate-800 p-5 bg-slate-900/40 backdrop-blur">
                        <h3 class="text-lg font-semibold text-cyan-200"><a href="/news.php?slug=<?= urlencode($post['slug']); ?>" class="hover:underline"><?= htmlspecialchars($post['title']); ?></a></h3>
                        <p class="text-sm text-slate-400 mt-2"><?= htmlspecialchars($post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 160) . '…'); ?></p>
                        <?php if ($post['published_at']): ?>
                            <p class="text-xs text-slate-500 mt-4">Veröffentlicht am <?= (new DateTimeImmutable($post['published_at']))->format('d.m.Y'); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

View::render('public_layout', compact('settings', 'content'));
