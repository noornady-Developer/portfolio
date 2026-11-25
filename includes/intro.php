<?php
declare(strict_types=1);
?>
    <!-- Snappy Pop-Sticker Intro Curtain (auto-skips after 0.9s or once per session) -->
    <div id="intro-container" class="pop-intro" aria-hidden="true">
        <div class="pop-intro-inner">
            <div class="intro-mascot-badge">
                <img src="<?= e(asset_url('imags/cartoon_mascot.jpg')) ?>" alt="Noor Nady Cartoon Mascot">
            </div>
            <div class="pop-intro-stickers">
                <span class="intro-chip intro-chip-cyan">INTERACTIVE UI</span>
                <span class="intro-chip intro-chip-mint">6 PROJECTS</span>
                <span class="intro-chip intro-chip-coral">BUILT FOR CLIENTS</span>
            </div>
            <div class="pop-intro-logo">
                <span class="intro-star">★</span>
                <span>NOOR NADY • FULL STACK</span>
            </div>
            <button type="button" class="intro-skip" id="intro-skip" data-en="Enter Portfolio ✕" data-ar="دخول سريع ✕">Enter Portfolio ✕</button>
        </div>
    </div>
