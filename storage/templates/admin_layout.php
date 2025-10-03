<?php
use App\Services\VersionManager;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#020617">
    <title>Admin · <?= htmlspecialchars($settings['site_title'] ?? 'Dragon Reptiles CMS'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --safe-area-top: env(safe-area-inset-top);
            --safe-area-right: env(safe-area-inset-right);
            --safe-area-bottom: env(safe-area-inset-bottom);
            --safe-area-left: env(safe-area-inset-left);
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #020617;
            color: #e2e8f0;
            margin: 0;
            min-height: 100vh;
            padding: var(--safe-area-top) var(--safe-area-right) var(--safe-area-bottom) var(--safe-area-left);
        }
    </style>
</head>
<body class="min-h-screen flex bg-slate-950/90">
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
        <header class="border-b border-slate-800 bg-slate-900/70 backdrop-blur px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button type="button" class="md:hidden inline-flex items-center gap-2 rounded-xl border border-slate-700 px-3 py-2 text-sm text-slate-200 hover:border-cyan-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-400" data-mobile-nav-toggle>
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Menü
                </button>
                <div>
                    <h2 class="text-lg font-semibold"><?= htmlspecialchars($title ?? 'Übersicht'); ?></h2>
                    <p class="text-sm text-slate-400"><?= htmlspecialchars($subtitle ?? 'Verwalte Inhalte und Einstellungen.'); ?></p>
                </div>
            </div>
            <div class="flex gap-3 text-sm">
                <form method="post" action="/admin/logout.php">
                    <button class="px-4 py-2 rounded-lg border border-slate-700 hover:border-rose-500 hover:text-rose-300" type="submit">Logout</button>
                </form>
            </div>
        </header>
        <nav class="md:hidden border-b border-slate-800 bg-slate-900/80 backdrop-blur" data-mobile-nav hidden>
            <div class="px-4 py-4 space-y-2 text-sm">
                <?php foreach ($navigation as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']); ?>" class="block rounded-lg px-4 py-3 <?= $item['active'] ? 'bg-cyan-500/20 text-cyan-200' : 'hover:bg-slate-800/70'; ?>">
                        <?= htmlspecialchars($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
        <main class="flex-1 bg-slate-950/60 px-4 py-6 sm:px-6">
            <div class="max-w-5xl mx-auto space-y-6">
                <?= $content ?? ''; ?>
            </div>
        </main>
    </div>
    <script>
        const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
        const mobileNav = document.querySelector('[data-mobile-nav]');
        if (mobileToggle && mobileNav) {
            mobileToggle.addEventListener('click', () => {
                const isHidden = mobileNav.hasAttribute('hidden');
                if (isHidden) {
                    mobileNav.removeAttribute('hidden');
                    mobileToggle.setAttribute('aria-expanded', 'true');
                } else {
                    mobileNav.setAttribute('hidden', 'hidden');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    </script>
</body>
</html>
