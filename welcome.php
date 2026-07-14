<?php
session_start();

// Sertakan fungsi i18n
require_once 'user/i18n.php';

// Tentukan bahasa. Gunakan bahasa dari sesi jika ada, jika tidak, default ke 'id'.
$current_language = $_SESSION['language'] ?? 'id';
load_language($current_language);

// Cek apakah pengguna sudah login untuk menyesuaikan tombol di navbar
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('welcome_title') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #18398d 0%, #1e40af 45%, #111827 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
        .hero .btn {
            font-size: 1.1rem;
            padding: 12px 30px;
        }
        .features {
            padding: 80px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="welcome.php" style="color: var(--primary-color);">Finance Tracker</a>
            <div>
                <?php if ($is_logged_in): ?>
                    <a href="index.php" class="btn btn-primary">Lanjutkan ke Home</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-outline-primary"><?= __('login') ?></a>
                    <a href="auth/register.php" class="btn btn-primary"><?= __('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['success'])): ?>
    <div class="container mt-4">
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
    </div>
    <?php endif; ?>

    <div class="hero">
        <div class="container">
            <h1><?= __('hero_title') ?></h1>
            <p><?= __('hero_subtitle') ?></p>
            <?php if (!$is_logged_in): ?>
                <a href="auth/register.php" class="btn btn-light btn-lg"><?= __('get_started_free') ?></a>
            <?php else: ?>
                 <a href="index.php" class="btn btn-light btn-lg"><?= __('continue_to_home') ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2><?= __('featured_features') ?></h2>
                <p class="lead text-muted"><?= __('features_subtitle') ?></p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">📈</div>
                    <h3><?= __('intuitive_dashboard') ?></h3>
                    <p><?= __('intuitive_dashboard_desc') ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">🗂️</div>
                    <h3><?= __('transaction_management') ?></h3>
                    <p><?= __('transaction_management_desc') ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">📊</div>
                    <h3><?= __('reports_and_charts') ?></h3>
                    <p><?= __('reports_and_charts_desc') ?></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 bg-light">
        <div class="container">
            <p class="mb-0 text-muted">&copy; <?= date('Y'); ?> <?= __('copyright'); ?></p>
        </div>
    </footer>

</body>
</html>