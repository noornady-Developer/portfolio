<?php
declare(strict_types=1);

$siteConfig = require __DIR__ . '/../config/config.php';
$portfolioData = require __DIR__ . '/../data/portfolio_data.php';
require_once __DIR__ . '/helpers.php';

$appName = $siteConfig['app']['name'] ?? 'Noor Nady ★ Full Stack | Portfolio';
$appDesc = $siteConfig['app']['description'] ?? 'Interactive Full-Stack & EdTech Portfolio';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="pop-lab">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFDF5">
    <meta name="description" content="<?= e($appDesc) ?>">
    <title>NOOR NADY ★ FULL STACK — <?= e($appName) ?></title>

    <!-- Google Fonts: Plus Jakarta Sans + Space Grotesk + Tajawal for sharp EN/AR display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Space+Grotesk:wght@700&family=Tajawal:wght@500;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('style.css')) ?>">
</head>

<body>
    <!-- Lively Pop-Sticker Paper Planes Convoy -->
    <div class="paper-planes-sky" aria-hidden="true">
        <?php
        $paperPlanes = [
            ['class' => 'plane-lane-a plane-a1', 'top' => '#FFFFFF', 'fold' => '#67E8F9', 'bottom' => '#E0F2FE'],
            ['class' => 'plane-lane-a plane-a2', 'top' => '#FDE047', 'fold' => '#FB7185', 'bottom' => '#FEF08A'],
            ['class' => 'plane-lane-a plane-a3', 'top' => '#FFFFFF', 'fold' => '#6EE7B7', 'bottom' => '#D1FAE5'],
            ['class' => 'plane-lane-b plane-b1', 'top' => '#DDD6FE', 'fold' => '#FDE047', 'bottom' => '#EDE9FE'],
            ['class' => 'plane-lane-b plane-b2', 'top' => '#FFFFFF', 'fold' => '#FB7185', 'bottom' => '#FFE4E6'],
            ['class' => 'plane-lane-c plane-c1', 'top' => '#67E8F9', 'fold' => '#FDE047', 'bottom' => '#CFFAFE'],
        ];
        foreach ($paperPlanes as $plane):
        ?>
        <div class="paper-plane <?= e($plane['class']) ?>">
            <svg viewBox="0 0 116 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="plane-trail" d="M 4 40 Q 22 46, 40 33" stroke="#111827" stroke-width="2.2" stroke-dasharray="5 5" stroke-linecap="round" />
                <polygon points="42,22 108,15 61,31" fill="<?= e($plane['top']) ?>" stroke="#111827" stroke-width="2.4" stroke-linejoin="round" />
                <polygon points="61,31 108,15 63,43" fill="<?= e($plane['fold']) ?>" stroke="#111827" stroke-width="2.4" stroke-linejoin="round" />
                <polygon points="63,43 108,15 76,48" fill="<?= e($plane['bottom']) ?>" stroke="#111827" stroke-width="2.4" stroke-linejoin="round" />
            </svg>
        </div>
        <?php endforeach; ?>
    </div>
