<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Core\View;
use App\Repositories\PostRepository;
use App\Repositories\SettingRepository;

$settings = SettingRepository::make()->all();
$postRepository = PostRepository::make();

$slug = $_GET['slug'] ?? null;

if ($slug) {
    $post = $postRepository->findBySlug($slug);
    if (!$post) {
        http_response_code(404);
        echo 'Beitrag nicht gefunden';
        exit;
    }

    ob_start();
    ?>
    <article class="space-y-6">
        <header>
            <h1 class="text-3xl font-bold text-cyan-200"><?= htmlspecialchars($post['title']); ?></h1>
            <?php if ($post['published_at']): ?>
                <p class="text-sm text-slate-400 mt-2">Veröffentlicht am <?= (new DateTimeImmutable($post['published_at']))->format('d.m.Y H:i'); ?></p>
            <?php endif; ?>
        </header>
        <section class="space-y-4 leading-relaxed text-slate-200">
            <?= $post['content']; ?>
        </section>
    </article>
    <?php
    $content = ob_get_clean();
} else {
    $posts = $postRepository->published();

    ob_start();
    ?>
    <section class="space-y-6">
        <header>
            <h1 class="text-3xl font-bold text-cyan-200">News</h1>
            <p class="text-sm text-slate-400">Aktuelle Entwicklungen aus Haltung, Zucht und Forschung.</p>
        </header>
        <div class="grid gap-4">
            <?php foreach ($posts as $post): ?>
                <article class="rounded-2xl border border-slate-800 p-6 bg-slate-900/50">
                    <h2 class="text-xl font-semibold text-cyan-200"><a href="?slug=<?= urlencode($post['slug']); ?>" class="hover:underline"><?= htmlspecialchars($post['title']); ?></a></h2>
                    <p class="text-sm text-slate-400 mt-2"><?= htmlspecialchars($post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 200) . '…'); ?></p>
                    <?php if ($post['published_at']): ?>
                        <p class="text-xs text-slate-500 mt-4">Veröffentlicht am <?= (new DateTimeImmutable($post['published_at']))->format('d.m.Y'); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    $content = ob_get_clean();
}

View::render('public_layout', compact('settings', 'content'));
