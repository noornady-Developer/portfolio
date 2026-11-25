<?php
declare(strict_types=1);

/**
 * Portfolio Helper Functions
 * ==========================
 * Production-ready utility functions with strict typing and XSS security.
 */

if (!function_exists('e')) {
    /**
     * Escape HTML output safely to prevent XSS attacks.
     *
     * @param string|null $value
     * @return string
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generate an asset URL with automated filemtime-based cache busting.
     *
     * @param string $path Relative path from project root (e.g. 'style.css')
     * @return string
     */
    function asset_url(string $path): string
    {
        $cleanPath = ltrim($path, '/\\');
        $fullPath = __DIR__ . '/../' . $cleanPath;

        if (file_exists($fullPath)) {
            $version = filemtime($fullPath);
            return $cleanPath . '?v=' . $version;
        }

        return $cleanPath;
    }
}

if (!function_exists('whatsapp_link')) {
    /**
     * Generate a sanitized, URL-encoded WhatsApp direct link.
     *
     * @param string $phone
     * @param string $message
     * @return string
     */
    function whatsapp_link(string $phone, string $message = ''): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $url = 'https://wa.me/' . $cleanPhone;

        if (!empty($message)) {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }
}

if (!function_exists('render_stars')) {
    /**
     * Render golden rating stars.
     *
     * @param int $count
     * @return string
     */
    function render_stars(int $count = 5): string
    {
        $html = '<span class="badge-stars-svg" aria-label="' . e((string)$count) . ' out of 5 stars">';
        $starSvg = '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';

        for ($i = 0; $i < $count; $i++) {
            $html .= $starSvg;
        }

        $html .= '</span>';
        return $html;
    }
}

if (!function_exists('render_svg_icon')) {
    /**
     * Render standardized SVG icons across the design.
     *
     * @param string $name
     * @param string $extraClass
     * @return string
     */
    function render_svg_icon(string $name, string $extraClass = ''): string
    {
        $classAttr = !empty($extraClass) ? ' class="' . e($extraClass) . '"' : '';

        switch ($name) {
            case 'rocket':
                return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path></svg>';

            case 'medal':
                return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>';

            case 'diamond':
                return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><path d="M6 3h12l4 6-10 12L2 9z"></path><path d="M11 3 8 9l4 12 4-12-3-6"></path><path d="M2 9h20"></path></svg>';

            case 'whatsapp':
                return '<svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"' . $classAttr . '><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.101.005.232-.038.361.275.145.347.491 1.2.535 1.286.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.939l-1.564 5.707 5.864-1.538c1.691.921 3.63 1.453 5.7 1.453 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12zm0 22c-1.874 0-3.627-.535-5.115-1.457l-.367-.226-3.483.913.93-3.395-.248-.395c-1.026-1.637-1.573-3.538-1.573-5.48 0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/></svg>';

            case 'arrow-up-right':
                return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>';

            case 'copy':
                return '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>';

            case 'check':
                return '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><polyline points="20 6 9 17 4 12"></polyline></svg>';

            case 'shield':
                return '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>';

            case 'sparkles':
                return '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"' . $classAttr . '><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>';

            default:
                return '';
        }
    }
}

if (!function_exists('infer_project_filter')) {
    /**
     * Determine filter slug ('edtech', 'saas', 'fintech', 'app', or 'web') for a project.
     *
     * @param array<string, mixed> $project
     * @return string
     */
    function infer_project_filter(array $project): string
    {
        if (($project['type'] ?? 'website') === 'app') {
            return 'app';
        }

        $haystack = strtolower(
            ($project['category'] ?? '') . ' ' .
            ($project['title_en'] ?? '') . ' ' .
            implode(' ', $project['tags'] ?? [])
        );

        if (strpos($haystack, 'fintech') !== false || strpos($haystack, 'wallet') !== false || strpos($haystack, 'balance') !== false) {
            return 'fintech';
        }
        if (strpos($haystack, 'saas') !== false || strpos($haystack, 'dashboard') !== false || strpos($haystack, 'admin') !== false) {
            return 'saas';
        }
        if (strpos($haystack, 'edtech') !== false || strpos($haystack, 'learning') !== false || strpos($haystack, 'education') !== false || strpos($haystack, 'course') !== false || strpos($haystack, 'faculty') !== false) {
            return 'edtech';
        }

        return 'web';
    }
}

if (!function_exists('infer_project_emoji')) {
    /**
     * Pick an expressive emoji badge for a project based on its category/tags.
     *
     * @param array<string, mixed> $project
     * @return string
     */
    function infer_project_emoji(array $project): string
    {
        if (($project['type'] ?? 'website') === 'app') {
            return '📱';
        }

        $haystack = strtolower(
            ($project['category'] ?? '') . ' ' .
            ($project['title_en'] ?? '') . ' ' .
            implode(' ', $project['tags'] ?? [])
        );

        if (strpos($haystack, 'wallet') !== false || strpos($haystack, 'fintech') !== false) {
            return '💳';
        }
        if (strpos($haystack, 'saas') !== false || strpos($haystack, 'hub') !== false || strpos($haystack, 'dashboard') !== false) {
            return '🚀';
        }
        if (strpos($haystack, 'faculty') !== false || strpos($haystack, 'math') !== false) {
            return '📐';
        }
        if (strpos($haystack, 'khashaba') !== false || strpos($haystack, 'personal') !== false) {
            return '⚡';
        }
        if (strpos($haystack, 'pwa') !== false || strpos($haystack, 'next') !== false) {
            return '✨';
        }

        return '🎓';
    }
}
