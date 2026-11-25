<?php
declare(strict_types=1);

$projects = $portfolioData['projects'] ?? [];
$reviews  = $portfolioData['reviews'] ?? [];

$apps = array_values(array_filter($projects, function ($p) {
    return ($p['type'] ?? 'website') === 'app';
}));
$hasApps = !empty($apps);

// Sticker pastel palette rotating across cards cleanly
$cardThemes = [
    'card-violet',
    'card-sky',
    'card-yellow',
    'card-rose',
    'card-mint',
    'card-peach',
];
?>
        <!-- ================================================================= -->
        <!-- STICKER SHOWCASE SECTION: INTERACTIVE FILTERABLE PROJECTS         -->
        <!-- ================================================================= -->
        <section class="projects-section" id="websites-section">
            <div class="section-heading-bar">
                <div>
                    <span class="section-sticker-tag" data-en="★ LIVE PLATFORMS &amp; SYSTEMS" data-ar="★ منصات وأنظمة حقيقية">
                        ★ LIVE PLATFORMS &amp; SYSTEMS
                    </span>
                    <h2 class="lab-section-title" data-en="Crafted Projects That Spark Joy &amp; Results" data-ar="مشاريع تفاعلية تجمع المتعة بالنتائج">
                        Crafted Projects That Spark Joy &amp; Results
                    </h2>
                </div>

                <!-- Interactive Filter Chips -->
                <div class="filter-bar" role="tablist" aria-label="Filter projects by category">
                    <button type="button" class="filter-pill is-active pop-box" data-filter="all" data-en="All (<?= count($projects) ?>)" data-ar="الكل (<?= count($projects) ?>)">
                        All (<?= count($projects) ?>)
                    </button>
                    <button type="button" class="filter-pill pop-box" data-filter="edtech" data-en="🎓 EdTech" data-ar="🎓 تعليمية">
                        🎓 EdTech
                    </button>
                    <button type="button" class="filter-pill pop-box" data-filter="saas" data-en="🚀 SaaS" data-ar="🚀 أنظمة SaaS">
                        🚀 SaaS
                    </button>
                    <button type="button" class="filter-pill pop-box" data-filter="fintech" data-en="💳 FinTech" data-ar="💳 مالية">
                        💳 FinTech
                    </button>
                    <?php if ($hasApps): ?>
                    <button type="button" class="filter-pill pop-box" data-filter="app" data-en="📱 Apps (<?= count($apps) ?>)" data-ar="📱 تطبيقات (<?= count($apps) ?>)">
                        📱 Apps (<?= count($apps) ?>)
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($projects)): ?>
            <div class="projects-sticker-grid" id="projects-grid">
                <?php foreach ($projects as $idx => $project):
                    $themeClass = $cardThemes[$idx % count($cardThemes)];
                    $filterSlug = infer_project_filter($project);
                    $emoji      = infer_project_emoji($project);
                    $titleEn    = !empty($project['title_en']) ? $project['title_en'] : ($project['title'] ?? '');
                    $titleAr    = !empty($project['title']) ? $project['title'] : $titleEn;
                    $descEn     = !empty($project['description_en']) ? $project['description_en'] : ($project['description'] ?? '');
                    $descAr     = !empty($project['description']) ? $project['description'] : $descEn;
                    $catEn      = !empty($project['category']) ? $project['category'] : ($project['category_ar'] ?? 'Web App');
                    $catAr      = !empty($project['category_ar']) ? $project['category_ar'] : $catEn;
                    $tags       = $project['tags'] ?? [];
                    $url        = $project['url'] ?? '#';
                    $image      = $project['image'] ?? 'imags/5803405861454548990.jpg';
                ?>
                <article
                    class="sticker-project-card <?= e($themeClass) ?> pop-box"
                    data-category="<?= e($filterSlug) ?>"
                    data-project-id="<?= (int)($project['id'] ?? ($idx + 1)) ?>"
                    data-title-en="<?= e($titleEn) ?>"
                    data-title-ar="<?= e($titleAr) ?>"
                    data-desc-en="<?= e($descEn) ?>"
                    data-desc-ar="<?= e($descAr) ?>"
                    data-cat-en="<?= e($catEn) ?>"
                    data-cat-ar="<?= e($catAr) ?>"
                    data-emoji="<?= e($emoji) ?>"
                    data-image="<?= e($image) ?>"
                    data-url="<?= e($url) ?>"
                    data-tags="<?= e(implode(', ', $tags)) ?>"
                >
                    <!-- Card Top Row: Emoji Sticker + Crisp White Category Pill -->
                    <div class="card-top-row">
                        <span class="card-emoji-sticker" aria-hidden="true"><?= $emoji ?></span>
                        <span class="card-cat-badge" data-en="<?= e($catEn) ?>" data-ar="<?= e($catAr) ?>">
                            <?= e($catEn) ?>
                        </span>
                    </div>

                    <!-- Card Content + Miniature Phone Device Frame -->
                    <div class="card-body-split">
                        <div class="card-text-col">
                            <h3 class="card-title" data-en="<?= e($titleEn) ?>" data-ar="<?= e($titleAr) ?>">
                                <?= e($titleEn) ?>
                            </h3>
                            <p class="card-desc" data-en="<?= e($descEn) ?>" data-ar="<?= e($descAr) ?>">
                                <?= e($descEn) ?>
                            </p>

                            <?php if (!empty($tags)): ?>
                            <div class="card-tags">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="card-tag-chip"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-device-col">
                            <button type="button" class="mini-phone-frame project-open-trigger" aria-label="Preview <?= e($titleEn) ?>">
                                <span class="mini-phone-notch"></span>
                                <img src="<?= e($image) ?>" alt="<?= e($titleEn) ?>" loading="lazy">
                                <span class="mini-phone-zoom" data-en="Zoom 🔍" data-ar="تكبير 🔍">Zoom 🔍</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card Bottom Action Buttons -->
                    <div class="card-actions-row">
                        <button type="button" class="card-btn-inspect project-open-trigger">
                            <span>🔍</span>
                            <span data-en="Inspect" data-ar="التفاصيل">Inspect</span>
                        </button>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer" class="card-btn-live">
                            <span data-en="Live Preview" data-ar="معاينة حية">Live Preview</span>
                            <?= render_svg_icon('arrow-up-right') ?>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- ================================================================= -->
        <!-- PROCESS & CLIENT LOVE STICKERS                                    -->
        <!-- ================================================================= -->
        <section class="process-section" id="process-section">
            <div class="section-heading-bar">
                <div>
                    <span class="section-sticker-tag tag-yellow" data-en="⚡ HOW WE BUILD" data-ar="⚡ كيف نعمل معاً">
                        ⚡ HOW WE BUILD
                    </span>
                    <h2 class="lab-section-title" data-en="From First Sketch to Happy Users" data-ar="من الفكرة الأولى إلى إطلاق منصة يعشقها الجميع">
                        From First Sketch to Happy Users
                    </h2>
                </div>
            </div>

            <div class="process-grid">
                <div class="process-card process-sky pop-box">
                    <span class="step-badge">01</span>
                    <h3 class="step-title" data-en="Idea &amp; UX Blueprint" data-ar="الفكرة وتخطيط التجربة">Idea &amp; UX Blueprint</h3>
                    <p class="step-desc" data-en="We map your goals, student or customer flow, and a visual style that stands out immediately." data-ar="نرتب أهداف المشروع وتجربة الطالب أو العميل بتصميم واضح ومميز من أول ثانية.">
                        We map your goals, student or customer flow, and a visual style that stands out immediately.
                    </p>
                </div>

                <div class="process-card process-yellow pop-box">
                    <span class="step-badge">02</span>
                    <h3 class="step-title" data-en="Playful UI &amp; Clean Code" data-ar="تصميم مبهج وبرمجة سريعة">Playful UI &amp; Clean Code</h3>
                    <p class="step-desc" data-en="Responsive interfaces, smooth animations, secure dashboards, and fast loading on every phone." data-ar="واجهات متجاوبة، حركات سلسة، لوحات تحكم آمنة، وسرعة عالية على جميع الهواتف.">
                        Responsive interfaces, smooth animations, secure dashboards, and fast loading on every phone.
                    </p>
                </div>

                <div class="process-card process-mint pop-box">
                    <span class="step-badge">03</span>
                    <h3 class="step-title" data-en="Launch &amp; Real Support" data-ar="الإطلاق والدعم المستمر">Launch &amp; Real Support</h3>
                    <p class="step-desc" data-en="Full deployment, testing, and ongoing technical support after delivery so you grow stress-free." data-ar="إطلاق كامل واختبار دقيق مع دعم فني حقيقي بعد التسليم لتنمو منصتك بثقة.">
                        Full deployment, testing, and ongoing technical support after delivery so you grow stress-free.
                    </p>
                </div>
            </div>

            <?php if (!empty($reviews)): ?>
            <div class="reviews-sticker-grid">
                <?php
                $revClasses = ['rev-tilt-left', 'rev-tilt-flat', 'rev-tilt-right'];
                foreach ($reviews as $rIdx => $rev):
                    $rClass = $revClasses[$rIdx % count($revClasses)];
                ?>
                <div class="review-sticker-card <?= e($rClass) ?> pop-box">
                    <div class="review-top">
                        <?= render_stars((int)($rev['stars'] ?? 5)) ?>
                        <span class="review-verified" data-en="Verified Client" data-ar="عميل موثوق">Verified Client</span>
                    </div>
                    <p class="review-quote" dir="rtl">"<?= e($rev['text'] ?? '') ?>"</p>
                    <div class="review-author" dir="rtl">
                        <strong><?= e($rev['author'] ?? '') ?></strong>
                        <span>• <?= e($rev['role'] ?? '') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
