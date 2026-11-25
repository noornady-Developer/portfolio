<?php
declare(strict_types=1);

$stats = $portfolioData['stats'] ?? [];
$reviews = $portfolioData['reviews'] ?? [];
$whatsappUrl = whatsapp_link(
    $siteConfig['contact']['whatsapp_intl'] ?? '201037864619',
    $siteConfig['contact']['whatsapp_default_message'] ?? ''
);

// Extract key stats for the Hero Sticker Boxes with safe fallbacks
$statProjects = $stats[0] ?? ['value' => '6', 'label' => 'مشاريع مكتملة', 'label_en' => 'Projects'];
$statExp      = $stats[1] ?? ['value' => '2', 'label' => 'سنتين خبرة', 'label_en' => 'Years Exp.'];
$statHappy    = $stats[2] ?? ['value' => '100%', 'label' => 'رضا العملاء', 'label_en' => 'Happy'];
?>
    <header class="lab-header" id="top">
        <!-- Sticky Top Sticker Navbar -->
        <nav class="lab-navbar" aria-label="Main navigation">
            <a href="#top" class="brand-sticker pop-box">
                <span class="brand-dot">★</span>
                <span>NOOR NADY • FULL STACK</span>
            </a>

            <div class="nav-links" aria-label="Page sections">
                <a href="#websites-section" class="nav-pill" data-en="Projects" data-ar="المشاريع">Projects</a>
                <a href="#process-section" class="nav-pill" data-en="Process" data-ar="خطوات العمل">Process</a>
                <a href="#contact-section" class="nav-pill" data-en="Contact" data-ar="تواصل">Contact</a>
            </div>

            <div class="nav-actions">
                <button type="button" id="sound-toggle" class="toy-btn pop-box" title="Toggle pop sound effects" aria-pressed="true">
                    <span class="sound-icon" id="sound-icon">🔊</span>
                    <span class="toy-btn-label" data-en="SFX" data-ar="صوت">SFX</span>
                </button>

                <button type="button" id="lang-toggle" class="lang-btn pop-box" aria-label="Switch language">
                    <span id="lang-badge">عربي / EN</span>
                </button>

                <button type="button" id="highfive-btn" class="highfive-btn pop-box">
                    <span class="hf-emoji">👋</span>
                    <span data-en="High-Five!" data-ar="كفك!">High-Five!</span>
                    <span class="hf-count" id="highfive-count">(148)</span>
                </button>
            </div>
        </nav>

        <!-- Hero Sticker Stage -->
        <section class="hero-stage">
            <div class="hero-main-card pop-box">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <div class="hero-eyebrow-row">
                            <span class="hero-sticker-tag" data-en="PLAYFUL UI • SERIOUS RESULTS" data-ar="تصميم تفاعلي • نتائج احترافية">
                                PLAYFUL UI • SERIOUS RESULTS
                            </span>
                            <span class="hero-status-pill">
                                <span class="live-dot"></span>
                                <span data-en="Available for Projects" data-ar="متاح لمشاريع جديدة">Available for Projects</span>
                            </span>
                        </div>

                        <h1 class="hero-headline"
                            data-en="Websites &amp; EdTech Platforms People Actually Remember."
                            data-ar="مواقع ومنصات تعليمية تفاعلية يعشقها الزوار ولا تُنسى.">
                            Websites &amp; EdTech Platforms People Actually Remember.
                        </h1>

                        <p class="hero-sub"
                           data-en="I'm Noor Nady — a Full-Stack Developer &amp; UI/UX Builder crafting colorful, ultra-fast educational platforms, SaaS dashboards, and web apps used by thousands."
                           data-ar="أنا نور نادي — مطوّر ومصمم واجهات Full-Stack أبني منصات تعليمية نابضة بالحياة، لوحات تحكم SaaS، وتطبيقات ويب سريعة يثق بها الآلاف.">
                            I'm Noor Nady — a Full-Stack Developer &amp; UI/UX Builder crafting colorful, ultra-fast educational platforms, SaaS dashboards, and web apps used by thousands.
                        </p>

                        <!-- Stat Sticker Boxes (matching uploaded design + 3rd experience badge) -->
                        <div class="hero-stats-row">
                            <div class="stat-sticker stat-mint pop-box">
                                <span class="stat-num"><?= e($statProjects['value']) ?></span>
                                <span class="stat-lbl" data-en="<?= e($statProjects['label_en'] ?? 'Projects') ?>" data-ar="<?= e($statProjects['label'] ?? 'مشروع مكتمل') ?>">
                                    <?= e($statProjects['label_en'] ?? 'Projects') ?>
                                </span>
                            </div>

                            <div class="stat-sticker stat-yellow pop-box">
                                <span class="stat-num"><?= e($statHappy['value']) ?></span>
                                <span class="stat-lbl" data-en="<?= e($statHappy['label_en'] ?? 'Happy') ?>" data-ar="<?= e($statHappy['label'] ?? 'رضا العملاء') ?>">
                                    <?= e($statHappy['label_en'] ?? 'Happy') ?>
                                </span>
                            </div>

                            <div class="stat-sticker stat-sky pop-box">
                                <span class="stat-num"><?= e($statExp['value']) ?></span>
                                <span class="stat-lbl" data-en="<?= e($statExp['label_en'] ?? 'Years Exp.') ?>" data-ar="<?= e($statExp['label'] ?? 'سنوات خبرة') ?>">
                                    <?= e($statExp['label_en'] ?? 'Years Exp.') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Action CTA + Kid-friendly Playground Toy Buttons -->
                        <div class="hero-cta-row">
                            <a href="#websites-section" class="cta-primary pop-box">
                                <span data-en="Explore Live Projects" data-ar="تصفح المشاريع الحية">Explore Live Projects</span>
                                <span class="cta-arrow">↓</span>
                            </a>

                            <a href="#contact-section" class="cta-secondary pop-box">
                                <span data-en="Start a Project" data-ar="ابدأ مشروعك">Start a Project</span>
                                <span>⚡</span>
                            </a>

                            <button type="button" id="confetti-btn" class="cta-toy pop-box">
                                <span>🎉</span>
                                <span data-en="Pop Confetti!" data-ar="أطلق الألوان!">Pop Confetti!</span>
                            </button>
                        </div>
                    </div>

                    <!-- Developer Polaroid Card with Cartoon Character & Full Stack Languages -->
                    <aside class="hero-profile-wrap">
                        <div class="profile-polaroid pop-box">
                            <div class="polaroid-tape">★ BUILDER ★</div>

                            <!-- Decorative Comic Sparkle Stars -->
                            <svg class="portrait-sparkle sparkle-tl" viewBox="0 0 36 36" aria-hidden="true">
                                <path d="M18 2 L22 14 L34 18 L22 22 L18 34 L14 22 L2 18 L14 14 Z" fill="#6EE7B7" stroke="#111827" stroke-width="2.5" stroke-linejoin="round" />
                            </svg>
                            <svg class="portrait-sparkle sparkle-br" viewBox="0 0 36 36" aria-hidden="true">
                                <path d="M18 3 L21.5 14.5 L33 18 L21.5 21.5 L18 33 L14.5 21.5 L3 18 L14.5 14.5 Z" fill="#67E8F9" stroke="#111827" stroke-width="2.5" stroke-linejoin="round" />
                            </svg>

                            <div class="polaroid-img-box">
                                <img src="<?= e(asset_url('imags/cartoon_mascot.jpg')) ?>" alt="Noor Nady — Full Stack Developer" loading="eager">
                            </div>

                            <div class="polaroid-caption">
                                <div class="polaroid-name">Noor Nady</div>
                                <span class="role-badge-pill" data-en="Full Stack" data-ar="Full Stack">Full Stack</span>
                            </div>

                            <?php
                            $knownLanguages = [
                                ['name' => 'HTML',    'cls' => 'chip-coral'],
                                ['name' => 'CSS',     'cls' => 'chip-sky'],
                                ['name' => 'JS',      'cls' => 'chip-yellow'],
                                ['name' => 'React',   'cls' => 'chip-cyan'],
                                ['name' => 'Node.js', 'cls' => 'chip-mint'],
                                ['name' => 'PHP',     'cls' => 'chip-violet'],
                                ['name' => 'Flutter', 'cls' => 'chip-sky'],
                                ['name' => 'Dart',    'cls' => 'chip-butter'],
                            ];
                            ?>
                            <div class="polaroid-stack" aria-label="Known programming languages and frameworks">
                                <?php foreach ($knownLanguages as $langItem): ?>
                                    <span class="stack-chip <?= e($langItem['cls']) ?>"><?= e($langItem['name']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- Dynamic Pop-Sticker Marquee synced with data/portfolio_data.php -->
            <div class="sticker-marquee pop-box" aria-label="Highlights and client feedback">
                <div class="marquee-track">
                    <?php for ($loop = 0; $loop < 2; $loop++): ?>
                        <?php foreach ($stats as $st): ?>
                            <div class="marquee-pill pill-stat">
                                <?= render_svg_icon($st['icon_type'] ?? 'rocket') ?>
                                <strong><?= e($st['value'] ?? '') ?></strong>
                                <span data-en="<?= e($st['label_en'] ?? $st['label'] ?? '') ?>" data-ar="<?= e($st['label'] ?? '') ?>">
                                    <?= e($st['label_en'] ?? $st['label'] ?? '') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div class="marquee-pill pill-review">
                                <?= render_stars((int)($rev['stars'] ?? 5)) ?>
                                <span>"<?= e($rev['text'] ?? '') ?>"</span>
                                <strong>— <?= e($rev['author'] ?? '') ?></strong>
                            </div>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
    </header>
