/**
 * NOOR NADY ★ LAB — Interactive Pop-Sticker Portfolio Controller
 * Handles snappy intro, EN/AR bilingual toggle, Web Audio pop SFX,
 * High-Five counter + confetti bursts, category filtering,
 * project inspection modal, and 1-tap WhatsApp brief builder.
 */

document.addEventListener('DOMContentLoaded', () => {
    const STORAGE_KEYS = {
        introSeen: 'noor_lab_intro_seen',
        lang: 'noor_lab_lang',
        highFives: 'noor_lab_highfives',
        sfxMuted: 'noor_lab_sfx_muted',
    };

    // =========================================================================
    // 1. Web Audio Synthesized Tactile Pop SFX (Zero external audio deps)
    // =========================================================================
    let audioCtx = null;
    let isMuted = localStorage.getItem(STORAGE_KEYS.sfxMuted) === '1';

    const soundToggleBtn = document.getElementById('sound-toggle');
    const soundIconEl = document.getElementById('sound-icon');

    function syncSoundBtn() {
        if (!soundToggleBtn || !soundIconEl) return;
        soundToggleBtn.classList.toggle('is-muted', isMuted);
        soundToggleBtn.setAttribute('aria-pressed', String(!isMuted));
        soundIconEl.textContent = isMuted ? '🔇' : '🔊';
    }

    function playPopSound(freqStart = 420, freqEnd = 680, duration = 0.08) {
        if (isMuted) return;
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;

        try {
            if (!audioCtx) {
                audioCtx = new AudioContextClass();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(freqStart, now);
            osc.frequency.exponentialRampToValueAtTime(freqEnd, now + duration);

            gain.gain.setValueAtTime(0.11, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + duration);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start(now);
            osc.stop(now + duration);
        } catch (err) {
            // Silently ignore browser autoplay restrictions
        }
    }

    if (soundToggleBtn) {
        syncSoundBtn();
        soundToggleBtn.addEventListener('click', () => {
            isMuted = !isMuted;
            localStorage.setItem(STORAGE_KEYS.sfxMuted, isMuted ? '1' : '0');
            syncSoundBtn();
            if (!isMuted) {
                playPopSound(480, 760, 0.09);
            }
        });
    }

    // =========================================================================
    // 2. Snappy Pop-Sticker Intro Curtain
    // =========================================================================
    const introContainer = document.getElementById('intro-container');
    const introSkipBtn = document.getElementById('intro-skip');

    if (introContainer) {
        const alreadySeen = sessionStorage.getItem(STORAGE_KEYS.introSeen) === '1';

        function dismissIntro(immediate = false) {
            sessionStorage.setItem(STORAGE_KEYS.introSeen, '1');
            if (immediate) {
                introContainer.remove();
                return;
            }
            if (introContainer.classList.contains('intro-done')) return;
            introContainer.classList.add('intro-done');
            setTimeout(() => {
                introContainer.remove();
            }, 600);
        }

        if (alreadySeen) {
            dismissIntro(true);
        } else {
            const introTimer = setTimeout(() => dismissIntro(false), 950);
            if (introSkipBtn) {
                introSkipBtn.addEventListener('click', () => {
                    clearTimeout(introTimer);
                    playPopSound(520, 820, 0.08);
                    dismissIntro(false);
                });
            }
        }
    }

    // =========================================================================
    // 3. Confetti Burst Engine (for High-Five & Toy Playground Button)
    // =========================================================================
    const confettiLayer = document.getElementById('confetti-layer');
    const CONFETTI_COLORS = ['#FDE047', '#67E8F9', '#6EE7B7', '#FB7185', '#DDD6FE', '#2563EB'];

    function launchConfetti(originX, originY, count = 26) {
        if (!confettiLayer) return;
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reducedMotion) return;

        for (let i = 0; i < count; i++) {
            const piece = document.createElement('span');
            piece.className = 'confetti-piece';
            piece.style.left = `${originX}px`;
            piece.style.top = `${originY}px`;
            piece.style.backgroundColor = CONFETTI_COLORS[i % CONFETTI_COLORS.length];
            piece.style.borderRadius = i % 3 === 0 ? '50%' : '3px';
            confettiLayer.appendChild(piece);

            const angle = (Math.PI * 2 * i) / count + (Math.random() - 0.5) * 0.4;
            const velocity = 65 + Math.random() * 140;
            const dx = Math.cos(angle) * velocity;
            const dy = Math.sin(angle) * velocity - 45;
            const rot = (Math.random() - 0.5) * 520;
            const duration = 600 + Math.random() * 350;

            const anim = piece.animate(
                [
                    { transform: 'translate3d(0, 0, 0) rotate(0deg) scale(1)', opacity: 1 },
                    { transform: `translate3d(${dx}px, ${dy + 95}px, 0) rotate(${rot}deg) scale(0.4)`, opacity: 0 },
                ],
                {
                    duration,
                    easing: 'cubic-bezier(0.2, 0.8, 0.3, 1)',
                    fill: 'forwards',
                }
            );

            anim.onfinish = () => piece.remove();
        }
    }

    // =========================================================================
    // 4. High-Five Counter & Toy Confetti Button
    // =========================================================================
    const highFiveBtn = document.getElementById('highfive-btn');
    const highFiveCountEl = document.getElementById('highfive-count');
    const confettiBtn = document.getElementById('confetti-btn');

    let highFives = parseInt(localStorage.getItem(STORAGE_KEYS.highFives) || '148', 10);
    if (Number.isNaN(highFives) || highFives < 148) {
        highFives = 148;
    }

    function renderHighFives() {
        if (highFiveCountEl) {
            highFiveCountEl.textContent = `(${highFives})`;
        }
    }
    renderHighFives();

    if (highFiveBtn) {
        highFiveBtn.addEventListener('click', (e) => {
            highFives += 1;
            localStorage.setItem(STORAGE_KEYS.highFives, String(highFives));
            renderHighFives();

            highFiveBtn.classList.remove('is-bumping');
            void highFiveBtn.offsetWidth;
            highFiveBtn.classList.add('is-bumping');

            const rect = highFiveBtn.getBoundingClientRect();
            const cx = e.clientX || rect.left + rect.width / 2;
            const cy = e.clientY || rect.top + rect.height / 2;
            launchConfetti(cx, cy, 22);
            playPopSound(500, 920, 0.1);
        });
    }

    if (confettiBtn) {
        confettiBtn.addEventListener('click', (e) => {
            const rect = confettiBtn.getBoundingClientRect();
            const cx = e.clientX || rect.left + rect.width / 2;
            const cy = e.clientY || rect.top + rect.height / 2;
            launchConfetti(cx, cy, 34);
            playPopSound(440, 880, 0.11);
        });
    }

    // Interactive Cartoon Mascot Buddy
    const mascotBtn = document.getElementById('mascot-buddy-btn');
    const mascotBubble = document.getElementById('mascot-speech-bubble');
    const mascotLinesEn = [
        "Got an idea? Pick a topic & let's fly! ✈️",
        "Full Stack power: HTML to Flutter! ⚡",
        "Fast websites & happy clients guaranteed! 🚀",
        "Tap WhatsApp & let's build yours today! 💬"
    ];
    const mascotLinesAr = [
        "عندك فكرة؟ اختر طلبك ويلا نطير بمشروعك! ✈️",
        "قوة الـ Full Stack: من HTML لحد Flutter! ⚡",
        "مواقع سريعة وتصميم يبهر كل عميل! 🚀",
        "اضغط على واتساب ونبدأ مشروعك النهارده! 💬"
    ];
    let mascotLineIdx = 0;

    if (mascotBtn) {
        mascotBtn.addEventListener('click', (e) => {
            const rect = mascotBtn.getBoundingClientRect();
            const cx = e.clientX || rect.left + rect.width / 2;
            const cy = e.clientY || rect.top + rect.height / 2;
            launchConfetti(cx, cy, 24);
            playPopSound(520, 860, 0.1);

            mascotLineIdx = (mascotLineIdx + 1) % mascotLinesEn.length;
            if (mascotBubble) {
                mascotBubble.setAttribute('data-en', mascotLinesEn[mascotLineIdx]);
                mascotBubble.setAttribute('data-ar', mascotLinesAr[mascotLineIdx]);
                mascotBubble.textContent = currentLang === 'ar'
                    ? mascotLinesAr[mascotLineIdx]
                    : mascotLinesEn[mascotLineIdx];
            }
        });
    }

    // =========================================================================
    // 5. Bilingual EN / عربي Live Switcher
    // =========================================================================
    let currentLang = localStorage.getItem(STORAGE_KEYS.lang) === 'ar' ? 'ar' : 'en';
    const langToggleBtn = document.getElementById('lang-toggle');
    const langBadgeEl = document.getElementById('lang-badge');
    let activeModalCard = null;

    function applyLanguage(lang) {
        currentLang = lang;
        localStorage.setItem(STORAGE_KEYS.lang, lang);

        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';

        if (langBadgeEl) {
            langBadgeEl.textContent = lang === 'ar' ? 'English' : 'عربي';
        }

        document.querySelectorAll('[data-en][data-ar]').forEach((el) => {
            const nextText = el.getAttribute(`data-${lang}`);
            if (nextText !== null) {
                el.textContent = nextText;
            }
        });

        if (activeModalCard) {
            populateModalFromCard(activeModalCard);
        }
    }

    if (langToggleBtn) {
        langToggleBtn.addEventListener('click', () => {
            playPopSound(390, 590, 0.08);
            applyLanguage(currentLang === 'en' ? 'ar' : 'en');
        });
    }

    // =========================================================================
    // 6. Category Filter Pills
    // =========================================================================
    const filterButtons = document.querySelectorAll('.filter-pill[data-filter]');
    const projectCards = document.querySelectorAll('.sticker-project-card');

    filterButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const chosen = btn.getAttribute('data-filter') || 'all';
            playPopSound(460, 660, 0.07);

            filterButtons.forEach((pill) => pill.classList.remove('is-active'));
            btn.classList.add('is-active');

            projectCards.forEach((card) => {
                const cat = card.getAttribute('data-category') || 'web';
                const show = chosen === 'all' || cat === chosen;
                card.classList.toggle('is-hidden', !show);
            });
        });
    });

    // =========================================================================
    // 7. Pop-Sticker Project Inspection Modal
    // =========================================================================
    const lightbox = document.getElementById('project-lightbox');
    const lightboxBackdrop = document.getElementById('project-lightbox-backdrop');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const modalEmoji = document.getElementById('modal-project-emoji');
    const modalBadge = document.getElementById('modal-project-badge');
    const modalTitle = document.getElementById('modal-project-title');
    const modalDesc = document.getElementById('modal-project-desc');
    const modalImage = document.getElementById('modal-project-image');
    const modalTags = document.getElementById('modal-project-tags');
    const modalLiveLink = document.getElementById('modal-live-link');
    const modalSimilarBtn = document.getElementById('modal-similar-btn');

    function populateModalFromCard(card) {
        const isAr = currentLang === 'ar';
        const title = card.getAttribute(isAr ? 'data-title-ar' : 'data-title-en') || '';
        const desc = card.getAttribute(isAr ? 'data-desc-ar' : 'data-desc-en') || '';
        const cat = card.getAttribute(isAr ? 'data-cat-ar' : 'data-cat-en') || '';
        const emoji = card.getAttribute('data-emoji') || '🚀';
        const image = card.getAttribute('data-image') || '';
        const url = card.getAttribute('data-url') || '#';
        const rawTags = card.getAttribute('data-tags') || '';

        if (modalEmoji) modalEmoji.textContent = emoji;
        if (modalBadge) modalBadge.textContent = cat;
        if (modalTitle) modalTitle.textContent = title;
        if (modalDesc) modalDesc.textContent = desc;
        if (modalImage) {
            modalImage.src = image;
            modalImage.alt = title;
        }
        if (modalLiveLink) modalLiveLink.href = url;

        if (modalTags) {
            modalTags.innerHTML = '';
            rawTags
                .split(',')
                .map((t) => t.trim())
                .filter(Boolean)
                .forEach((tag) => {
                    const span = document.createElement('span');
                    span.className = 'card-tag-chip';
                    span.textContent = tag;
                    modalTags.appendChild(span);
                });
        }
    }

    function openProjectModal(card) {
        if (!lightbox || !card) return;
        activeModalCard = card;
        populateModalFromCard(card);
        lightbox.classList.add('is-active');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        playPopSound(430, 740, 0.09);
    }

    function closeProjectModal() {
        if (!lightbox || !lightbox.classList.contains('is-active')) return;
        lightbox.classList.remove('is-active');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        activeModalCard = null;
    }

    document.querySelectorAll('.project-open-trigger').forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const card = trigger.closest('.sticker-project-card');
            openProjectModal(card);
        });
    });

    if (lightboxBackdrop) lightboxBackdrop.addEventListener('click', closeProjectModal);
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeProjectModal);

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeProjectModal();
        }
    });

    // =========================================================================
    // 8. Smart 1-Tap WhatsApp Brief Builder
    // =========================================================================
    const inquiryChips = document.querySelectorAll('.inquiry-chip[data-message]');
    const messageInput = document.getElementById('whatsapp-message-input');
    const whatsappLaunchBtn = document.getElementById('whatsapp-launch-btn');

    function updateWhatsAppHref(text) {
        if (!whatsappLaunchBtn) return;
        const phone = whatsappLaunchBtn.getAttribute('data-phone') || '201037864619';
        const trimmed = (text || '').trim();
        const url = trimmed
            ? `https://wa.me/${phone}?text=${encodeURIComponent(trimmed)}`
            : `https://wa.me/${phone}`;
        whatsappLaunchBtn.href = url;
    }

    inquiryChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            playPopSound(510, 710, 0.07);
            inquiryChips.forEach((c) => c.classList.remove('is-selected'));
            chip.classList.add('is-selected');

            const presetMsg = chip.getAttribute('data-message') || '';
            if (messageInput) {
                messageInput.value = presetMsg;
            }
            updateWhatsAppHref(presetMsg);
        });
    });

    if (messageInput) {
        messageInput.addEventListener('input', () => {
            updateWhatsAppHref(messageInput.value);
        });
    }

    if (modalSimilarBtn) {
        modalSimilarBtn.addEventListener('click', () => {
            if (activeModalCard && messageInput) {
                const titleAr = activeModalCard.getAttribute('data-title-ar') || activeModalCard.getAttribute('data-title-en') || 'المشروع';
                const customMsg = `مرحباً نور، أعجبني مشروع "${titleAr}" وأرغب في تصميم وبرمجة مشروع مشابه.`;
                messageInput.value = customMsg;
                updateWhatsAppHref(customMsg);
                inquiryChips.forEach((c) => c.classList.remove('is-selected'));
            }
            closeProjectModal();
        });
    }

    // Apply initial persisted language
    applyLanguage(currentLang);
});
