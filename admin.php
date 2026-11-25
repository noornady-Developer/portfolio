<?php
declare(strict_types=1);

/**
 * Portfolio Projects Admin Control Panel
 * =======================================
 * Dedicated dashboard for managing portfolio projects:
 * - Add new projects (Websites for Upper section or Applications for Lower section)
 * - Edit existing projects (titles, descriptions, categories, links, images, type)
 * - Delete projects with instant confirmation
 * - Automatic synchronization of portfolio_data.php and index.html
 */

session_start();

const ADMIN_PASSWORD = '123456';
const DATA_FILE = __DIR__ . '/data/portfolio_data.php';
const INDEX_HTML = __DIR__ . '/index.html';
const UPLOAD_DIR = __DIR__ . '/imags/';

// Ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

// Helper: load portfolio data
function loadData(): array {
    if (file_exists(DATA_FILE)) {
        $loaded = require DATA_FILE;
        if (is_array($loaded)) {
            return $loaded;
        }
    }
    return [
        'projects' => [],
        'stats' => [],
        'reviews' => [],
        'quick_inquiries' => [],
    ];
}

// Helper: sync index.html from index.php
function syncHtml(): void {
    try {
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate(DATA_FILE, true);
        }
        ob_start();
        require __DIR__ . '/index.php';
        $html = ob_get_clean();
        if ($html && strlen($html) > 500) {
            @file_put_contents(INDEX_HTML, $html, LOCK_EX);
        }
    } catch (\Throwable $t) {
        // Fallback: render silently
    }
}

// Helper: save portfolio data to data/portfolio_data.php
function saveData(array $data): bool {
    unset($data['bio'], $data['about']);
    $exported = var_export($data, true);
    $content = "<?php\ndeclare(strict_types=1);\n\n/**\n * Portfolio Data Models & Datasets\n * Generated via Admin Dashboard\n * Last Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . $exported . ";\n";
    $success = @file_put_contents(DATA_FILE, $content, LOCK_EX) !== false;
    if ($success) {
        syncHtml();
    }
    return $success;
}

// Helper: handle safe file upload to imags/
function handleUpload(string $inputName, string $prefix = 'proj_'): ?string {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$inputName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $filename = $prefix . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $target = UPLOAD_DIR . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'imags/' . $filename;
    }
    return null;
}

// Logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['portfolio_admin_logged']);
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Login action
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $pass = $_POST['password'] ?? '';
    if ($pass === ADMIN_PASSWORD) {
        $_SESSION['portfolio_admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = 'كلمة المرور غير صحيحة، يرجى المحاولة مرة أخرى.';
    }
}

$isLoggedIn = !empty($_SESSION['portfolio_admin_logged']);

// Notification messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Process authenticated project actions (CRUD)
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $data = loadData();

    // 1. ADD PROJECT
    if ($action === 'add_project') {
        $type = trim($_POST['type'] ?? 'website');
        if (!in_array($type, ['website', 'app'], true)) {
            $type = 'website';
        }

        $title = trim($_POST['title'] ?? '');
        $title_en = trim($_POST['title_en'] ?? '');
        $category = trim($_POST['category'] ?? ($type === 'app' ? 'Mobile App' : 'Full-Stack Web App'));
        $category_ar = trim($_POST['category_ar'] ?? ($type === 'app' ? 'تطبيق هاتف وويب' : 'منصة ويب متكاملة'));
        $description = trim($_POST['description'] ?? '');
        $description_en = trim($_POST['description_en'] ?? '');
        $url = trim($_POST['url'] ?? '#');
        $badge = trim($_POST['badge'] ?? ($type === 'app' ? 'Mobile App' : 'Live Platform'));
        $tagsInput = trim($_POST['tags'] ?? '');
        $tags = array_values(array_filter(array_map('trim', explode(',', $tagsInput))));

        // Image upload or fallback
        $imagePath = handleUpload('project_image', 'proj_');
        if (!$imagePath && !empty($_POST['existing_image'])) {
            $imagePath = trim($_POST['existing_image']);
        }
        if (!$imagePath) {
            $imagePath = 'imags/5803405861454548990.jpg';
        }

        // Generate next ID
        $maxId = 0;
        foreach ($data['projects'] as $p) {
            if (isset($p['id']) && (int)$p['id'] > $maxId) {
                $maxId = (int)$p['id'];
            }
        }
        $newId = $maxId + 1;

        $newProject = [
            'id'             => $newId,
            'type'           => $type,
            'title'          => $title ?: ($type === 'app' ? 'تطبيق جديد ' . $newId : 'مشروع ويب جديد ' . $newId),
            'title_en'       => $title_en ?: ($type === 'app' ? 'New App ' . $newId : 'New Web Project ' . $newId),
            'category'       => $category,
            'category_ar'    => $category_ar,
            'description'    => $description,
            'description_en' => $description_en,
            'image'          => $imagePath,
            'url'            => $url ?: '#',
            'badge'          => $badge,
            'tags'           => $tags ?: ($type === 'app' ? ['Mobile', 'App'] : ['Web App']),
            'is_live'        => true,
        ];

        $data['projects'][] = $newProject;

        if (saveData($data)) {
            $_SESSION['flash_success'] = 'تم إضافة المشروع بنجاح إلى قسم (' . ($type === 'app' ? 'التطبيقات' : 'مواقع الويب') . ')! 🚀';
        } else {
            $_SESSION['flash_error'] = 'حدث خطأ أثناء حفظ المشروع.';
        }

        $filterParam = ($type === 'app') ? '?filter=apps' : '?filter=websites';
        header('Location: admin.php' . $filterParam);
        exit;
    }

    // 2. EDIT PROJECT
    if ($action === 'edit_project') {
        $id = (int)($_POST['id'] ?? 0);
        $found = false;
        $updatedType = 'website';

        foreach ($data['projects'] as &$p) {
            if ((int)$p['id'] === $id) {
                $found = true;
                $p['type']           = in_array($_POST['type'] ?? '', ['website', 'app'], true) ? $_POST['type'] : ($p['type'] ?? 'website');
                $updatedType         = $p['type'];
                $p['title']          = trim($_POST['title'] ?? $p['title']);
                $p['title_en']       = trim($_POST['title_en'] ?? ($p['title_en'] ?? $p['title']));
                $p['category']       = trim($_POST['category'] ?? $p['category']);
                $p['category_ar']    = trim($_POST['category_ar'] ?? ($p['category_ar'] ?? $p['category']));
                $p['description']    = trim($_POST['description'] ?? ($p['description'] ?? ''));
                $p['description_en'] = trim($_POST['description_en'] ?? ($p['description_en'] ?? ''));
                $p['url']            = trim($_POST['url'] ?? $p['url']);
                $p['badge']          = trim($_POST['badge'] ?? ($p['badge'] ?? 'Web App'));

                $tagsInput = trim($_POST['tags'] ?? '');
                if ($tagsInput !== '') {
                    $p['tags'] = array_values(array_filter(array_map('trim', explode(',', $tagsInput))));
                }

                $uploaded = handleUpload('project_image', 'proj_');
                if ($uploaded) {
                    $p['image'] = $uploaded;
                } elseif (!empty($_POST['existing_image'])) {
                    $p['image'] = trim($_POST['existing_image']);
                }
                break;
            }
        }
        unset($p);

        if ($found && saveData($data)) {
            $_SESSION['flash_success'] = 'تم حفظ تعديلات المشروع بنجاح! ✏️';
        } else {
            $_SESSION['flash_error'] = 'تعذر العثور على المشروع أو حفظ التعديل.';
        }

        $filterParam = ($updatedType === 'app') ? '?filter=apps' : '?filter=websites';
        header('Location: admin.php' . $filterParam);
        exit;
    }

    // 3. DELETE PROJECT
    if ($action === 'delete_project') {
        $id = (int)($_POST['id'] ?? 0);
        $origCount = count($data['projects']);
        $data['projects'] = array_values(array_filter($data['projects'], function($p) use ($id) {
            return (int)($p['id'] ?? 0) !== $id;
        }));

        if (count($data['projects']) < $origCount && saveData($data)) {
            $_SESSION['flash_success'] = 'تم حذف المشروع نهائياً وتحديث الموقع! 🗑️';
        } else {
            $_SESSION['flash_error'] = 'لم يتم العثور على المشروع المراد حذفه.';
        }
        header('Location: admin.php');
        exit;
    }
}

// Current data
$currentData = loadData();
$allProjects = $currentData['projects'] ?? [];

$filter = $_GET['filter'] ?? 'all';
if ($filter === 'websites') {
    $projects = array_values(array_filter($allProjects, fn($p) => ($p['type'] ?? 'website') === 'website'));
} elseif ($filter === 'apps') {
    $projects = array_values(array_filter($allProjects, fn($p) => ($p['type'] ?? 'website') === 'app'));
} else {
    $projects = $allProjects;
}

// Counts
$websitesCount = count(array_filter($allProjects, fn($p) => ($p['type'] ?? 'website') === 'website'));
$appsCount = count(array_filter($allProjects, fn($p) => ($p['type'] ?? 'website') === 'app'));
$totalCount = count($allProjects);

// Available images in imags/
$availableImages = [];
if (is_dir(UPLOAD_DIR)) {
    $files = scandir(UPLOAD_DIR);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && !is_dir(UPLOAD_DIR . $f)) {
            $availableImages[] = 'imags/' . $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة إدارة المشاريع | Portfolio Admin</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-base: #0a0406;
            --bg-surface: #14080e;
            --bg-card: rgba(28, 12, 19, 0.82);
            --bg-input: rgba(14, 6, 10, 0.9);
            --border-glass: rgba(255, 235, 59, 0.18);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --crimson-prime: #5A0A18;
            --crimson-bright: #91112b;
            --gold-prime: #ffeb3b;
            --gold-warm: #e6c875;
            --emerald-prime: #22c55e;
            --cyan-prime: #38bdf8;
            --text-main: #f5f0f2;
            --text-muted: #b8a6ad;
            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
            --shadow-card: 0 16px 40px rgba(0, 0, 0, 0.55), 0 0 25px rgba(90, 10, 24, 0.25);
            --transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tajawal', 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 0%, #26050d 0%, var(--bg-base) 65%);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .admin-wrap {
            max-width: 1240px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }

        /* Top Navigation Header */
        .admin-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 16px 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-card);
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-badge {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--crimson-bright), #30040c);
            border: 1.5px solid var(--gold-prime);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.35rem;
            color: var(--gold-prime);
            box-shadow: 0 0 15px rgba(255, 235, 59, 0.35);
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 40%, var(--gold-warm) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .nav-btn-view {
            background: rgba(255, 235, 59, 0.1);
            color: var(--gold-prime);
            border-color: rgba(255, 235, 59, 0.3);
        }

        .nav-btn-view:hover {
            background: var(--gold-prime);
            color: #1a0509;
            box-shadow: 0 0 20px rgba(255, 235, 59, 0.4);
            transform: translateY(-2px);
        }

        .nav-btn-logout {
            background: rgba(230, 25, 75, 0.12);
            color: #ff5c7c;
            border-color: rgba(230, 25, 75, 0.3);
        }

        .nav-btn-logout:hover {
            background: var(--crimson-bright);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Notifications Alert */
        .alert-box {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
            border: 1px solid rgba(74, 222, 128, 0.35);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.35);
        }

        /* Stats Bar */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--gold-prime);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-icon-total {
            background: rgba(255, 235, 59, 0.12);
            border: 1px solid rgba(255, 235, 59, 0.35);
            color: var(--gold-prime);
        }

        .stat-icon-websites {
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: var(--cyan-prime);
        }

        .stat-icon-apps {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.35);
            color: var(--emerald-prime);
        }

        .stat-info-val {
            font-size: 1.8rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
        }

        .stat-info-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Main Section Card */
        .admin-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 30px;
            box-shadow: var(--shadow-card);
        }

        /* Toolbar: Filter Tabs + Add Button */
        .toolbar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 26px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .filter-tabs {
            display: inline-flex;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 4px;
            gap: 4px;
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .filter-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }

        .filter-btn.active {
            background: var(--crimson-prime);
            color: var(--gold-prime);
            border: 1px solid var(--gold-prime);
            box-shadow: 0 0 12px rgba(255, 235, 59, 0.25);
        }

        .btn-add-project {
            background: linear-gradient(135deg, #107c41, #0b532b);
            color: #fff;
            border: 1px solid #4ade80;
            padding: 11px 22px;
            border-radius: var(--radius-md);
            font-weight: 800;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 6px 18px rgba(16, 124, 65, 0.35);
        }

        .btn-add-project:hover {
            background: #16a34a;
            box-shadow: 0 0 20px rgba(74, 222, 128, 0.5);
            transform: translateY(-2px);
        }

        /* Projects Grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 22px;
        }

        .project-item-card {
            background: rgba(18, 7, 12, 0.88);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            position: relative;
        }

        .project-item-card:hover {
            border-color: var(--border-glass);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
            transform: translateY(-4px);
        }

        .project-item-thumb {
            height: 180px;
            width: 100%;
            position: relative;
            background: #000;
            overflow: hidden;
        }

        .project-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .project-item-card:hover .project-item-thumb img {
            transform: scale(1.04);
        }

        /* Type Tag (Upper Website vs Lower App) */
        .project-type-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            backdrop-filter: blur(8px);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .tag-type-website {
            background: rgba(90, 10, 24, 0.92);
            color: var(--gold-prime);
            border: 1px solid var(--gold-prime);
        }

        .tag-type-app {
            background: rgba(16, 75, 40, 0.92);
            color: #4ade80;
            border: 1px solid #4ade80;
        }

        .project-badge-tag {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.75);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            backdrop-filter: blur(6px);
        }

        .project-item-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .project-item-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 3px;
        }

        .project-item-title-en {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 6px;
        }

        .project-item-category {
            font-size: 0.82rem;
            color: var(--gold-warm);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .project-item-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
            line-height: 1.55;
        }

        .project-item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid var(--border-subtle);
            padding-top: 14px;
        }

        .btn-edit {
            flex: 1;
            background: rgba(255, 235, 59, 0.1);
            color: var(--gold-prime);
            border: 1px solid rgba(255, 235, 59, 0.3);
            padding: 8px 14px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-edit:hover {
            background: var(--gold-prime);
            color: #1a0509;
            box-shadow: 0 0 15px rgba(255, 235, 59, 0.35);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(248, 113, 113, 0.3);
            padding: 8px 14px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
        }

        .btn-preview-link {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: var(--radius-md);
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .btn-preview-link:hover {
            color: #fff;
            border-color: #fff;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.82);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: #170710;
            border: 1px solid var(--gold-prime);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7), 0 0 30px rgba(255, 235, 59, 0.2);
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-box {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 12px;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--gold-prime);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.6rem;
            cursor: pointer;
            padding: 0 6px;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: #fff;
        }

        /* Segmented Radio Controls for Type */
        .type-selector-box {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 12px;
            margin-bottom: 18px;
        }

        .type-selector-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--gold-prime);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .type-radio-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .type-radio-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: var(--transition);
        }

        .type-radio-label:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--gold-prime);
        }

        .type-radio-label input[type="radio"] {
            accent-color: var(--gold-prime);
            width: 18px;
            height: 18px;
        }

        .type-radio-text-title {
            font-weight: 800;
            font-size: 0.92rem;
            color: #fff;
        }

        .type-radio-text-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Forms & Inputs */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-label span.req {
            color: #ff4757;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid rgba(255, 235, 59, 0.2);
            border-radius: var(--radius-md);
            padding: 11px 14px;
            color: #fff;
            font-family: inherit;
            font-size: 0.92rem;
            transition: var(--transition);
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--gold-prime);
            box-shadow: 0 0 15px rgba(255, 235, 59, 0.25);
            background: rgba(20, 8, 14, 0.95);
        }

        .form-textarea {
            resize: vertical;
            min-height: 85px;
            line-height: 1.55;
        }

        .file-drop-area {
            border: 1.5px dashed rgba(255, 235, 59, 0.35);
            border-radius: var(--radius-md);
            padding: 16px;
            text-align: center;
            background: rgba(0, 0, 0, 0.3);
            position: relative;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-drop-area:hover {
            border-color: var(--gold-prime);
            background: rgba(255, 235, 59, 0.05);
        }

        .file-drop-area input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--crimson-bright), var(--crimson-prime));
            border: 1px solid var(--gold-prime);
            color: var(--gold-prime);
            font-weight: 800;
            font-size: 1rem;
            padding: 12px 28px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 8px 20px rgba(90, 10, 24, 0.4);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, var(--gold-prime), var(--gold-warm));
            color: #1a0509;
            box-shadow: 0 10px 25px rgba(255, 235, 59, 0.35);
        }

        /* Login Screen */
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl);
            padding: 44px 36px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--shadow-card);
            text-align: center;
        }

        .login-logo {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--crimson-bright), #30040c);
            border: 2px solid var(--gold-prime);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--gold-prime);
            margin-bottom: 18px;
            box-shadow: 0 0 25px rgba(255, 235, 59, 0.35);
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .password-input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .password-toggle-btn {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .admin-nav {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .admin-brand {
                flex-direction: column;
            }
            .admin-card {
                padding: 20px 16px;
            }
            .toolbar-row {
                flex-direction: column;
                align-items: stretch;
            }
            .type-radio-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
    <!-- ==================== LOGIN SCREEN ==================== -->
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-logo">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 class="login-title">لوحة إدارة المشاريع</h1>
            <p class="login-subtitle">أدخل كلمة المرور لإدارة مشاريع الويب والتطبيقات</p>

            <?php if (!empty($loginError)): ?>
                <div class="alert-box alert-error" style="margin-bottom: 18px;">
                    <?= htmlspecialchars($loginError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin.php">
                <input type="hidden" name="action" value="login">
                <div class="form-group password-input-group">
                    <input type="password" name="password" id="admin_password" class="form-input" placeholder="كلمة المرور..." required autofocus style="text-align: center; font-size: 1.15rem; letter-spacing: 2px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePass('admin_password')" title="إظهار/إخفاء">👁️</button>
                </div>
                <button type="submit" class="btn-submit" style="width: 100%;">
                    <span>دخول للوحة التحكم</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- ==================== AUTHENTICATED DASHBOARD ==================== -->
    <div class="admin-wrap">
        
        <!-- Top Navigation -->
        <header class="admin-nav">
            <div class="admin-brand">
                <div class="brand-badge">P</div>
                <div>
                    <h1 class="brand-title">لوحة إدارة المشاريع</h1>
                    <p class="brand-subtitle">إضافة وتعديل وحذف مشاريع الويب والتطبيقات</p>
                </div>
            </div>

            <div class="admin-nav-actions">
                <a href="index.php" target="_blank" class="nav-btn nav-btn-view" title="فتح الموقع في نافذة جديدة">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>معاينة الموقع الرئيسي</span>
                </a>

                <a href="admin.php?action=logout" class="nav-btn nav-btn-logout">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>خروج</span>
                </a>
            </div>
        </header>

        <!-- Flash Alerts -->
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert-box alert-success">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= htmlspecialchars($flashSuccess) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert-box alert-error">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= htmlspecialchars($flashError) ?></span>
            </div>
        <?php endif; ?>

        <!-- Stats Overview Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-total">💼</div>
                <div>
                    <div class="stat-info-val"><?= $totalCount ?></div>
                    <div class="stat-info-label">إجمالي المشاريع في الموقع</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-websites">🌐</div>
                <div>
                    <div class="stat-info-val"><?= $websitesCount ?></div>
                    <div class="stat-info-label">مواقع الويب (القسم العلوي)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-apps">📱</div>
                <div>
                    <div class="stat-info-val"><?= $appsCount ?></div>
                    <div class="stat-info-label">التطبيقات (القسم السفلي)</div>
                </div>
            </div>
        </div>

        <!-- Main Projects Management Card -->
        <section class="admin-card">
            
            <!-- Toolbar -->
            <div class="toolbar-row">
                <div class="filter-tabs">
                    <a href="admin.php?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
                        <span>🌟</span> الكل (<?= $totalCount ?>)
                    </a>
                    <a href="admin.php?filter=websites" class="filter-btn <?= $filter === 'websites' ? 'active' : '' ?>">
                        <span>🌐</span> مواقع الويب (<?= $websitesCount ?>)
                    </a>
                    <a href="admin.php?filter=apps" class="filter-btn <?= $filter === 'apps' ? 'active' : '' ?>">
                        <span>📱</span> التطبيقات (<?= $appsCount ?>)
                    </a>
                </div>

                <button type="button" class="btn-add-project" onclick="openAddModal('<?= $filter === 'apps' ? 'app' : 'website' ?>')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>إضافة مشروع جديد</span>
                </button>
            </div>

            <!-- Projects Grid -->
            <?php if (empty($projects)): ?>
                <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 12px;">📂</div>
                    <h3 style="color: #fff; margin-bottom: 8px;">لا توجد مشاريع في هذا القسم حالياً</h3>
                    <p style="margin-bottom: 20px;">اضغط على زر "إضافة مشروع جديد" لإضافة مشاريع تظهر فوراً على الموقع.</p>
                    <button type="button" class="btn-add-project" onclick="openAddModal('<?= $filter === 'apps' ? 'app' : 'website' ?>')">
                        إضافة مشروع جديد الآن
                    </button>
                </div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $project): 
                        $isApp = ($project['type'] ?? 'website') === 'app';
                    ?>
                        <div class="project-item-card">
                            <div class="project-item-thumb">
                                <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                                
                                <span class="project-type-tag <?= $isApp ? 'tag-type-app' : 'tag-type-website' ?>">
                                    <?= $isApp ? '📱 تطبيق (سفلي)' : '🌐 موقع ويب (علوي)' ?>
                                </span>

                                <?php if (!empty($project['badge'])): ?>
                                    <span class="project-badge-tag"><?= htmlspecialchars($project['badge']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="project-item-body">
                                <h3 class="project-item-title"><?= htmlspecialchars($project['title']) ?></h3>
                                <?php if (!empty($project['title_en'])): ?>
                                    <div class="project-item-title-en"><?= htmlspecialchars($project['title_en']) ?></div>
                                <?php endif; ?>

                                <div class="project-item-category">
                                    <?= htmlspecialchars($project['category_ar'] ?? $project['category']) ?>
                                </div>

                                <p class="project-item-desc">
                                    <?= htmlspecialchars(!empty($project['description']) ? $project['description'] : ($project['description_en'] ?? '')) ?>
                                </p>
                                
                                <div class="project-item-actions">
                                    <button type="button" class="btn-edit" onclick='openEditModal(<?= json_encode($project, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)'>
                                        ✏️ تعديل
                                    </button>

                                    <a href="<?= htmlspecialchars($project['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-preview-link" title="معاينة الرابط">
                                        ↗️
                                    </a>

                                    <form method="POST" action="admin.php" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع نهائياً؟');" style="margin: 0;">
                                        <input type="hidden" name="action" value="delete_project">
                                        <input type="hidden" name="id" value="<?= (int)$project['id'] ?>">
                                        <button type="submit" class="btn-delete" title="حذف المشروع">🗑️ حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

    </div>

    <!-- ==================== ADD / EDIT PROJECT MODAL ==================== -->
    <div class="modal-overlay" id="projectModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title" id="modalHeading">إضافة مشروع جديد</h3>
                <button type="button" class="modal-close" onclick="closeModal()">✕</button>
            </div>

            <form method="POST" action="admin.php" enctype="multipart/form-data" id="projectForm">
                <input type="hidden" name="action" id="modalAction" value="add_project">
                <input type="hidden" name="id" id="modalProjectId" value="">

                <!-- 1. Section & Type Selection -->
                <div class="type-selector-box">
                    <div class="type-selector-title">
                        <span>🎯</span> قسم المشروع المعروض فيه على الموقع:
                    </div>
                    <div class="type-radio-group">
                        <label class="type-radio-label">
                            <input type="radio" name="type" id="type_website" value="website" checked>
                            <div>
                                <div class="type-radio-text-title">🌐 موقع ويب (Websites)</div>
                                <div class="type-radio-text-desc">يُعرض في القسم العلوي بالإنجليزية</div>
                            </div>
                        </label>

                        <label class="type-radio-label">
                            <input type="radio" name="type" id="type_app" value="app">
                            <div>
                                <div class="type-radio-text-title">📱 تطبيق (Applications)</div>
                                <div class="type-radio-text-desc">يُعرض في القسم السفلي للتطبيقات</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Titles -->
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">عنوان المشروع بالعربية <span class="req">*</span></label>
                        <input type="text" name="title" id="p_title" class="form-input" required placeholder="مثال: تطبيق المحفظة الذكية">
                    </div>

                    <div class="form-group" dir="ltr">
                        <label class="form-label">Project Title (English) <span class="req">*</span></label>
                        <input type="text" name="title_en" id="p_title_en" class="form-input" required placeholder="e.g. Smart Wallet Platform">
                    </div>
                </div>

                <!-- 3. Categories -->
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">التصنيف بالعربية</label>
                        <input type="text" name="category_ar" id="p_category_ar" class="form-input" placeholder="مثال: تطبيق ويب متكامل / تطبيق هاتف">
                    </div>

                    <div class="form-group" dir="ltr">
                        <label class="form-label">Category (English)</label>
                        <input type="text" name="category" id="p_category" class="form-input" placeholder="e.g. Full-Stack Web App / Mobile App">
                    </div>
                </div>

                <!-- 4. Descriptions -->
                <div class="form-group">
                    <label class="form-label">وصف المشروع بالعربية</label>
                    <textarea name="description" id="p_description" class="form-textarea" placeholder="شرح مبسط لمزايا وخصائص المشروع يظهر عند قلب البطاقة..."></textarea>
                </div>

                <div class="form-group" dir="ltr">
                    <label class="form-label">Project Description (English - Shown on Websites)</label>
                    <textarea name="description_en" id="p_description_en" class="form-textarea" placeholder="Brief project overview shown in the websites section..."></textarea>
                </div>

                <!-- 5. URL & Badge -->
                <div class="form-grid">
                    <div class="form-group" dir="ltr">
                        <label class="form-label">Project URL (Link) <span class="req">*</span></label>
                        <input type="url" name="url" id="p_url" class="form-input" required placeholder="https://example.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">الشارة (Badge)</label>
                        <input type="text" name="badge" id="p_badge" class="form-input" placeholder="Live Platform أو Mobile App أو UI/UX">
                    </div>
                </div>

                <!-- 6. Tags -->
                <div class="form-group">
                    <label class="form-label">الوسوم والتقنيات (Tags - مفصولة بفواصل)</label>
                    <input type="text" name="tags" id="p_tags" class="form-input" placeholder="React, Node.js, PHP, Flutter">
                </div>

                <!-- 7. Image Upload / Select -->
                <div class="form-group">
                    <label class="form-label">صورة المشروع (رفع صورة جديدة أو اختيار صورة موجودة)</label>
                    <div class="file-drop-area" style="margin-bottom: 10px;">
                        <input type="file" name="project_image" accept="image/*">
                        <div style="font-size: 1.8rem; margin-bottom: 4px;">📷</div>
                        <div style="font-weight: 700; font-size: 0.92rem;">اضغط هنا لرفع صورة جديدة من جهازك</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">يدعم صيغ JPG, PNG, WEBP</div>
                    </div>

                    <?php if (!empty($availableImages)): ?>
                    <label class="form-label" style="font-size: 0.85rem; color: var(--text-muted);">أو اختر صورة من المجلد الحالي:</label>
                    <select name="existing_image" id="p_existing_image" class="form-select">
                        <option value="">-- احتفظ بالصورة الحالية أو اختر صورة من القائمة --</option>
                        <?php foreach ($availableImages as $img): ?>
                            <option value="<?= htmlspecialchars($img) ?>"><?= htmlspecialchars($img) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn-submit" style="flex: 1;" id="modalSubmitBtn">
                        <span>حفظ المشروع</span>
                    </button>
                    <button type="button" class="btn-delete" style="padding: 12px 24px; font-size: 1rem;" onclick="closeModal()">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePass(id) {
            const el = document.getElementById(id);
            if (el.type === 'password') {
                el.type = 'text';
            } else {
                el.type = 'password';
            }
        }

        const modal = document.getElementById('projectModal');

        function openAddModal(defaultType = 'website') {
            document.getElementById('modalHeading').innerText = 'إضافة مشروع جديد';
            document.getElementById('modalAction').value = 'add_project';
            document.getElementById('modalProjectId').value = '';
            document.getElementById('modalSubmitBtn').innerHTML = '<span>إضافة المشروع 🚀</span>';

            if (defaultType === 'app') {
                document.getElementById('type_app').checked = true;
                document.getElementById('p_category').value = 'Mobile App';
                document.getElementById('p_category_ar').value = 'تطبيق هاتف وويب';
                document.getElementById('p_badge').value = 'Mobile App';
                document.getElementById('p_tags').value = 'Flutter, Mobile, API';
            } else {
                document.getElementById('type_website').checked = true;
                document.getElementById('p_category').value = 'Full-Stack Web App';
                document.getElementById('p_category_ar').value = 'تطبيق ويب متكامل';
                document.getElementById('p_badge').value = 'Live Platform';
                document.getElementById('p_tags').value = 'Web App, Full-Stack';
            }

            document.getElementById('p_title').value = '';
            document.getElementById('p_title_en').value = '';
            document.getElementById('p_description').value = '';
            document.getElementById('p_description_en').value = '';
            document.getElementById('p_url').value = '';

            const sel = document.getElementById('p_existing_image');
            if (sel) sel.value = '';

            modal.classList.add('active');
        }

        function openEditModal(project) {
            document.getElementById('modalHeading').innerText = 'تعديل مشروع: ' + (project.title || '');
            document.getElementById('modalAction').value = 'edit_project';
            document.getElementById('modalProjectId').value = project.id || '';
            document.getElementById('modalSubmitBtn').innerHTML = '<span>حفظ التعديلات ✏️</span>';

            if (project.type === 'app') {
                document.getElementById('type_app').checked = true;
            } else {
                document.getElementById('type_website').checked = true;
            }

            document.getElementById('p_title').value = project.title || '';
            document.getElementById('p_title_en').value = project.title_en || project.title || '';
            document.getElementById('p_category').value = project.category || '';
            document.getElementById('p_category_ar').value = project.category_ar || '';
            document.getElementById('p_description').value = project.description || '';
            document.getElementById('p_description_en').value = project.description_en || '';
            document.getElementById('p_url').value = project.url || '';
            document.getElementById('p_badge').value = project.badge || '';
            document.getElementById('p_tags').value = Array.isArray(project.tags) ? project.tags.join(', ') : '';

            const sel = document.getElementById('p_existing_image');
            if (sel) {
                sel.value = project.image || '';
            }

            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        window.onclick = function(e) {
            if (e.target === modal) {
                closeModal();
            }
        };
    </script>
<?php endif; ?>

</body>
</html>
