<?php
use App\Services\VersionManager;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin · <?= htmlspecialchars($settings['site_title'] ?? 'Dragon Reptiles CMS'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; background: #020617; color: #e2e8f0; }
    </style>
</head>
<body class="min-h-screen flex">
    <aside class="w-72 bg-slate-900/80 backdrop-blur border-r border-slate-800 hidden md:flex flex-col">
        <div class="px-6 py-8 border-b border-slate-800">
            <p class="text-xs uppercase tracking-widest text-slate-400">Dragon Reptiles CMS</p>
            <h1 class="text-2xl font-semibold text-cyan-300">Adminbereich</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
            <?php foreach ($navigation as $item): ?>
                <a href="<?= htmlspecialchars($item['href']); ?>" class="block px-4 py-3 rounded-xl <?= $item['active'] ? 'bg-cyan-500/20 text-cyan-200' : 'hover:bg-slate-800/80'; ?>">
                    <?= htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="px-6 py-6 border-t border-slate-800 text-xs text-slate-500">
            Version <?= VersionManager::latest(); ?>
        </div>
    </aside>
    <div class="flex-1 flex flex-col">
        <header class="border-b border-slate-800 bg-slate-900/70 backdrop-blur px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold"><?= htmlspecialchars($title ?? 'Übersicht'); ?></h2>
                <p class="text-sm text-slate-400"><?= htmlspecialchars($subtitle ?? 'Verwalte Inhalte und Einstellungen.'); ?></p>
            </div>
            <div class="flex gap-3 text-sm">
                <form method="post" action="/admin/logout.php">
                    <button class="px-4 py-2 rounded-lg border border-slate-700 hover:border-rose-500 hover:text-rose-300" type="submit">Logout</button>
                </form>
            </div>
        </header>
        <main class="flex-1 p-6 bg-slate-950/60">
            <div class="max-w-5xl mx-auto space-y-6">
                <?= $content ?? ''; ?>
            </div>
        </main>
    </div>
</body>
</html>
