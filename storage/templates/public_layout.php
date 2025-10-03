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
    <meta name="theme-color" content="#050505">
    <title><?= htmlspecialchars($settings['site_title'] ?? 'Dragon Reptiles CMS'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            color-scheme: dark;
            --safe-area-top: env(safe-area-inset-top);
            --safe-area-right: env(safe-area-inset-right);
            --safe-area-bottom: env(safe-area-inset-bottom);
            --safe-area-left: env(safe-area-inset-left);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #050505;
            color: #f5f5f5;
            margin: 0;
            min-height: 100vh;
            padding: var(--safe-area-top) var(--safe-area-right) var(--safe-area-bottom) var(--safe-area-left);
        }
        .gradient-border { position: relative; }
        .gradient-border::before { content: ""; position: absolute; inset: -2px; background: linear-gradient(120deg, #22d3ee, #6366f1, #ec4899); z-index: -1; border-radius: inherit; filter: blur(6px); opacity: 0.6; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
<header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <span class="text-xs uppercase tracking-widest text-slate-400"><?= htmlspecialchars($settings['logo_text'] ?? 'Dragon Reptiles'); ?></span>
            <h1 class="text-3xl font-bold text-cyan-300 drop-shadow"><?= htmlspecialchars($settings['header_content'] ?? 'Dragon Reptiles CMS'); ?></h1>
        </div>
        <div class="flex flex-col w-full md:w-auto">
            <button type="button" class="md:hidden inline-flex items-center justify-center gap-2 self-end rounded-full border border-slate-800 px-4 py-2 text-sm font-medium text-slate-200 hover:border-cyan-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-400" data-public-nav-toggle>
                Menü öffnen
            </button>
            <nav class="hidden md:flex gap-3 text-sm mt-3 md:mt-0" data-public-nav>
                <?php
                try {
                    $menu = json_decode($settings['menu_structure'] ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    $menu = [];
                }
                ?>
                <?php foreach ($menu as $item): ?>
                    <a class="px-4 py-2 rounded-full border border-slate-800 hover:border-cyan-400 transition" href="<?= htmlspecialchars($item['url']); ?>"><?= htmlspecialchars($item['label']); ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="md:hidden" data-public-nav-drawer hidden>
                <nav class="mt-3 space-y-2 text-sm">
                    <?php foreach ($menu as $item): ?>
                        <a class="block rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-3 text-slate-200 hover:border-cyan-400" href="<?= htmlspecialchars($item['url']); ?>"><?= htmlspecialchars($item['label']); ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
    </div>
</header>
<main class="flex-1">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid gap-10 md:grid-cols-[2fr,1fr]">
        <article class="space-y-6">
            <?= $content ?? ''; ?>
        </article>
        <aside class="gradient-border rounded-3xl p-[1px]">
            <div class="rounded-3xl bg-slate-900/60 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-200">Insights</h2>
                <div class="space-y-3 text-sm leading-relaxed text-slate-300">
                    <?= $settings['sidebar_content'] ?? '<p>Modernste Informationen rund um Reptilienhaltung und -genetik.</p>'; ?>
                </div>
            </div>
        </aside>
    </section>
</main>
<footer class="border-t border-slate-800 bg-slate-950/90 backdrop-blur">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between text-sm text-slate-400">
        <div><?= $settings['footer_content'] ?? '&copy; ' . date('Y') . ' Dragon Reptiles'; ?></div>
        <div>Version <?= VersionManager::latest(); ?></div>
    </div>
</footer>
<script>
    const publicToggle = document.querySelector('[data-public-nav-toggle]');
    const publicNav = document.querySelector('[data-public-nav]');
    const publicDrawer = document.querySelector('[data-public-nav-drawer]');
    if (publicToggle && publicNav && publicDrawer) {
        publicToggle.addEventListener('click', () => {
            const isHidden = publicDrawer.hasAttribute('hidden');
            if (isHidden) {
                publicDrawer.removeAttribute('hidden');
                publicToggle.textContent = 'Menü schließen';
                publicToggle.setAttribute('aria-expanded', 'true');
            } else {
                publicDrawer.setAttribute('hidden', 'hidden');
                publicToggle.textContent = 'Menü öffnen';
                publicToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
</script>
</body>
</html>
