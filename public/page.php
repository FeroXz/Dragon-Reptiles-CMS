<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Core\View;
use App\Repositories\PageRepository;
use App\Repositories\SettingRepository;

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: /');
    exit;
}

$page = PageRepository::make()->findBySlug($slug);

if (!$page) {
    http_response_code(404);
    echo 'Seite nicht gefunden';
    exit;
}

$settings = SettingRepository::make()->all();

ob_start();
?>
<article class="space-y-6">
    <header>
        <h1 class="text-3xl font-bold text-cyan-200"><?= htmlspecialchars($page['title']); ?></h1>
    </header>
    <section class="space-y-4 leading-relaxed text-slate-200">
        <?= $page['content']; ?>
    </section>
</article>
<?php
$content = ob_get_clean();

View::render('public_layout', compact('settings', 'content'));
