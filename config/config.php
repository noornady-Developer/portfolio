<?php
declare(strict_types=1);

/**
 * Site Configuration & Global Settings
 * =====================================
 * Centralized settings for the portfolio application.
 * Designed with senior engineering architecture principles.
 */

return [
    'app' => [
        'name'        => 'Portfolio | Full-Stack Web Development',
        'short_name'  => 'Portfolio',
        'author'      => 'Noor Nady',
        'description' => 'بورتفوليو احترافي لعرض المشاريع البرمجية وتطوير المواقع والمنصات وتطبيقات الويب بأعلى معايير الجودة والأداء.',
        'keywords'    => 'Portfolio, Web Development, PHP, Frontend, JavaScript, UI/UX, منصات, مواقع',
        'lang'        => 'ar',
        'dir'         => 'rtl',
        'theme_color' => '#5A0A18',
        'base_url'    => '', // Relative or absolute base URL
    ],

    'contact' => [
        'whatsapp_number'  => '01037864619',
        'whatsapp_intl'    => '201037864619',
        'whatsapp_default_message' => 'مرحباً، أود الاستفسار بخصوص مشروع جديد وطلب خدمة تطوير ويب.',
        'display_phone'    => '01037864619',
    ],

    'features' => [
        'enable_cinematic_intro' => true,
        'intro_auto_dismiss_ms'  => 3200,
        'enable_glass_ticker'    => true,
        'enable_rainbow_arc'     => true,
    ],

    'socials' => [
        'whatsapp' => 'https://wa.me/201037864619',
        'github'   => 'https://github.com/noornady00-blip',
    ],
];
