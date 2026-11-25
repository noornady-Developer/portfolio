<?php
declare(strict_types=1);

$githubUrl = $siteConfig['socials']['github'] ?? 'https://github.com/noornady00-blip';
?>
    <footer class="lab-footer">
        <div class="footer-inner pop-box">
            <div class="footer-brand">
                <span class="brand-dot">★</span>
                <span>NOOR NADY • FULL STACK</span>
            </div>
            <p class="footer-note" data-en="Crafted with interactive precision for learners &amp; ambitious clients." data-ar="صُمّم بدقة إبداعية ليُبهر كل زائر وعميل طموح.">
                Crafted with interactive precision for learners &amp; ambitious clients.
            </p>
            <div class="footer-links">
                <a href="<?= e($githubUrl) ?>" target="_blank" rel="noopener noreferrer" class="footer-link-pill">GitHub ↗</a>
                <a href="#top" class="footer-link-pill" data-en="Back to Top ↑" data-ar="العودة للأعلى ↑">Back to Top ↑</a>
            </div>
        </div>
    </footer>

    <!-- Pop-Sticker Project Inspection Lightbox Modal -->
    <div class="project-lightbox-overlay" id="project-lightbox" aria-hidden="true">
        <div class="project-lightbox-backdrop" id="project-lightbox-backdrop"></div>
        <div class="project-modal-card pop-box" id="project-modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-project-title">
            <div class="modal-top-bar">
                <div class="modal-top-left">
                    <span class="modal-emoji" id="modal-project-emoji">🚀</span>
                    <span class="modal-badge" id="modal-project-badge">EdTech</span>
                </div>
                <button type="button" class="modal-close-btn pop-box" id="modal-close-btn" aria-label="Close modal">✕</button>
            </div>

            <div class="modal-body-grid">
                <div class="modal-preview-col">
                    <div class="modal-phone-frame pop-box">
                        <span class="mini-phone-notch"></span>
                        <img id="modal-project-image" src="imags/IMG_3885.PNG" alt="Project preview">
                    </div>
                </div>

                <div class="modal-info-col">
                    <h3 class="modal-title" id="modal-project-title">Project Title</h3>
                    <p class="modal-desc" id="modal-project-desc">Project description</p>
                    <div class="modal-tags" id="modal-project-tags"></div>

                    <div class="modal-actions">
                        <a href="#" id="modal-live-link" target="_blank" rel="noopener noreferrer" class="modal-btn-live pop-box">
                            <span data-en="Open Live Website" data-ar="فتح الموقع مباشرة">Open Live Website</span>
                            <?= render_svg_icon('arrow-up-right') ?>
                        </a>
                        <a href="#contact-section" id="modal-similar-btn" class="modal-btn-similar pop-box">
                            <span data-en="Build Something Like This ⚡" data-ar="اطلب مشروعاً مشابهاً ⚡">Build Something Like This ⚡</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confetti Burst Canvas Layer -->
    <div id="confetti-layer" class="confetti-layer" aria-hidden="true"></div>

    <script src="<?= e(asset_url('script.js')) ?>"></script>
</body>

</html>
