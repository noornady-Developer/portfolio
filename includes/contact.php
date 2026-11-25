<?php
declare(strict_types=1);

$quickInquiries = $portfolioData['quick_inquiries'] ?? [];
$whatsappIntl   = $siteConfig['contact']['whatsapp_intl'] ?? '201037864619';
$displayPhone   = $siteConfig['contact']['display_phone'] ?? '01037864619';
$defaultMessage = $quickInquiries[0]['message'] ?? ($siteConfig['contact']['whatsapp_default_message'] ?? 'مرحباً، أود الاستفسار بخصوص مشروع جديد.');

$quickEnLabels = [
    'New Website / Platform',
    'New Mobile / Web App',
    'Upgrade Existing Project',
    'Quick Tech Consultation',
];
?>
        <!-- ================================================================= -->
        <!-- SMART 1-TAP WHATSAPP BRIEF BUILDER                                -->
        <!-- ================================================================= -->
        <section class="contact-section" id="contact-section">
            <div class="contact-lab-card pop-box">
                <div class="contact-left">
                    <div class="contact-mascot-row">
                        <button type="button" id="mascot-buddy-btn" class="mascot-sticker-btn pop-box" title="Tap me!">
                            <img src="<?= e(asset_url('imags/cartoon_mascot.jpg')) ?>" alt="Noor Nady Cartoon Character" loading="lazy">
                        </button>
                        <div class="mascot-speech-col">
                            <span class="contact-badge-pill">
                                <span class="live-dot"></span>
                                <span data-en="SMART 1-TAP BRIEF" data-ar="تواصل ذكي بضغطة واحدة">SMART 1-TAP BRIEF</span>
                            </span>
                            <div class="mascot-speech-bubble pop-box" id="mascot-speech-bubble"
                                 data-en="Got an idea? Pick a topic &amp; let's fly! ✈️"
                                 data-ar="عندك فكرة؟ اختر طلبك ويلا نطير بمشروعك! ✈️">
                                Got an idea? Pick a topic &amp; let's fly! ✈️
                            </div>
                        </div>
                    </div>
                    <h2 class="contact-title"
                        data-en="Ready to Build Something Unforgettable?"
                        data-ar="جاهز لبناء مشروع يبهر عملاءك؟">
                        Ready to Build Something Unforgettable?
                    </h2>
                    <p class="contact-desc"
                       data-en="Pick what you need below and send your ready-to-go brief straight to WhatsApp."
                       data-ar="اختر نوع مشروعك من البطاقات التالية لتجهيز رسالتك وإرسالها مباشرة على واتساب.">
                        Pick what you need below and send your ready-to-go brief straight to WhatsApp.
                    </p>

                    <?php if (!empty($quickInquiries)): ?>
                    <div class="inquiry-chips" role="radiogroup" aria-label="Project inquiry presets">
                        <?php foreach ($quickInquiries as $qIdx => $inq):
                            $arLabel = $inq['label'] ?? 'استفسار';
                            $enLabel = $quickEnLabels[$qIdx] ?? $arLabel;
                            $msg     = $inq['message'] ?? $defaultMessage;
                        ?>
                        <button type="button"
                                class="inquiry-chip pop-box <?= $qIdx === 0 ? 'is-selected' : '' ?>"
                                data-message="<?= e($msg) ?>"
                                data-en="<?= e($enLabel) ?>"
                                data-ar="<?= e($arLabel) ?>">
                            <?= e($enLabel) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="contact-right">
                    <label for="whatsapp-message-input" class="brief-preview-label" data-en="Your WhatsApp Message (editable):" data-ar="رسالتك الجاهزة (يمكنك تعديلها):">
                        Your WhatsApp Message (editable):
                    </label>
                    <textarea id="whatsapp-message-input" class="brief-textarea pop-box" rows="3" dir="rtl"><?= e($defaultMessage) ?></textarea>

                    <a id="whatsapp-launch-btn"
                       href="<?= e(whatsapp_link($whatsappIntl, $defaultMessage)) ?>"
                       data-phone="<?= e(preg_replace('/[^0-9]/', '', $whatsappIntl)) ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="whatsapp-lab-btn pop-box">
                        <div class="whatsapp-icon-box">
                            <?= render_svg_icon('whatsapp') ?>
                        </div>
                        <div class="whatsapp-info">
                            <span class="whatsapp-label" data-en="Send Brief on WhatsApp" data-ar="إرسال عبر واتساب الآن">Send Brief on WhatsApp</span>
                            <span class="whatsapp-number"><?= e($displayPhone) ?></span>
                        </div>
                        <div class="whatsapp-arrow">
                            <?= render_svg_icon('arrow-up-right') ?>
                        </div>
                    </a>
                </div>
            </div>
        </section>
