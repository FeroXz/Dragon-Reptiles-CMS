<?php

declare(strict_types=1);

require __DIR__ . '/auth.php';

use App\Core\View;
use App\Repositories\SettingRepository;

$settingsRepo = SettingRepository::make();
$settings = $settingsRepo->all();
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = [
        'site_title' => trim($_POST['site_title'] ?? ''),
        'logo_text' => trim($_POST['logo_text'] ?? ''),
        'menu_structure' => $_POST['menu_structure'] ?? '[]',
        'sidebar_content' => $_POST['sidebar_content'] ?? '',
        'header_content' => $_POST['header_content'] ?? '',
        'footer_content' => $_POST['footer_content'] ?? '',
        'homepage_intro' => $_POST['homepage_intro'] ?? '',
    ];

    try {
        json_decode($updated['menu_structure'], true, 512, JSON_THROW_ON_ERROR);
        $settingsRepo->updateMany($updated);
        $settings = $settingsRepo->all();
        $message = 'Einstellungen wurden gespeichert.';
    } catch (\JsonException $exception) {
        $error = 'Menüstruktur ist kein gültiges JSON: ' . $exception->getMessage();
    }
}

$navigation = [
    ['label' => 'Übersicht', 'href' => '/admin/index.php', 'active' => false],
    ['label' => 'Einstellungen', 'href' => '/admin/settings.php', 'active' => true],
    ['label' => 'Seiten', 'href' => '/admin/pages.php', 'active' => false],
    ['label' => 'News', 'href' => '/admin/posts.php', 'active' => false],
    ['label' => 'User', 'href' => '/admin/users.php', 'active' => false],
];

ob_start();
?>
<?php if ($message): ?>
    <div class="rounded-xl border border-emerald-500/60 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
        <?= htmlspecialchars($message); ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="rounded-xl border border-rose-500/60 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
        <?= htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<form method="post" class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Seitentitel</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="site_title" value="<?= htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Logo Text</span>
            <input class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3" type="text" name="logo_text" value="<?= htmlspecialchars($settings['logo_text'] ?? ''); ?>">
        </label>
    </div>
    <label class="flex flex-col gap-2 text-sm">
        <span class="text-slate-300">Menüstruktur (JSON)</span>
        <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-48" name="menu_structure" required><?= htmlspecialchars($settings['menu_structure'] ?? '[]'); ?></textarea>
        <span class="text-xs text-slate-500">Beispiel: [{"label":"Startseite","url":"/"}]</span>
    </label>
    <div class="grid gap-6 md:grid-cols-2">
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Sidebar Inhalt (HTML)</span>
            <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-48" name="sidebar_content"><?= htmlspecialchars($settings['sidebar_content'] ?? ''); ?></textarea>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Startseiten Intro (HTML)</span>
            <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-48" name="homepage_intro"><?= htmlspecialchars($settings['homepage_intro'] ?? ''); ?></textarea>
        </label>
    </div>
    <div class="grid gap-6 md:grid-cols-2">
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Header Inhalt (HTML)</span>
            <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-36" name="header_content"><?= htmlspecialchars($settings['header_content'] ?? ''); ?></textarea>
        </label>
        <label class="flex flex-col gap-2 text-sm">
            <span class="text-slate-300">Footer Inhalt (HTML)</span>
            <textarea class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 h-36" name="footer_content"><?= htmlspecialchars($settings['footer_content'] ?? ''); ?></textarea>
        </label>
    </div>
    <div class="flex justify-end">
        <button class="rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-6 py-3 text-sm font-semibold text-slate-950 hover:from-cyan-400 hover:to-indigo-400" type="submit">Speichern</button>
    </div>
</form>
<?php
$content = ob_get_clean();

View::render('admin_layout', [
    'settings' => $settings,
    'navigation' => $navigation,
    'title' => 'Einstellungen',
    'subtitle' => 'Passe Branding und Layout an.',
    'content' => $content,
]);
