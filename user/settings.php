<?php
require_once '../config/bootstrap.php';
checkRole('user');

// Daftar pilihan yang tersedia
$themes = [
    'default' => 'Default (Light)',
    'dark' => 'Dark Mode',
    'ocean' => 'Ocean Blue',
    'forest' => 'Forest Green',
    'sunset' => 'Sunset Orange',
    'royal' => 'Royal Purple',
    'mono' => 'Monochrome',
    'pastel' => 'Pastel Dream',
    'crimson' => 'Crimson Red',
    'golden' => 'Golden Hour',
];

$currencies = [
    'IDR' => 'Indonesian Rupiah (Rp)',
    'USD' => 'US Dollar ($)',
    'EUR' => 'Euro (€)',
    'JPY' => 'Japanese Yen (¥)',
    'GBP' => 'British Pound (£)',
    'AUD' => 'Australian Dollar (A$)',
    'CAD' => 'Canadian Dollar (C$)',
    'CHF' => 'Swiss Franc (CHF)',
    'CNY' => 'Chinese Yuan (¥)',
    'INR' => 'Indian Rupee (₹)',
];

$languages = [
    'id' => 'Bahasa Indonesia',
    'en' => 'English',
    // Tambahkan 8 bahasa populer lainnya di sini jika file terjemahannya sudah dibuat
    // 'es' => 'Español',
    // 'zh' => '中文 (Mandarin)',
    // 'hi' => 'हिन्दी (Hindi)',
    // 'fr' => 'Français',
    // 'ar' => 'العربية (Arabic)',
    // 'bn' => 'বাংলা (Bengali)',
    // 'ru' => 'Русский (Russian)',
    // 'pt' => 'Português',
];

?>
<!DOCTYPE html>
<html lang="<?= $current_language ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('settings') ?> - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body data-theme="<?= htmlspecialchars($current_theme) ?>">

<?php
include __DIR__ . '/navbar.php';
?>
    <div class="container mt-4">
        <div class="d-flex align-items-center mb-3">
            <a href="../index.php" class="btn btn-outline-secondary me-2 btn-back-icon" title="Kembali ke Home">‹</a>
            <h3><?= __('settings') ?></h3>
        </div>
        <p class="text-muted"><?= __('settings_subtitle') ?></p>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="settings_update.php" method="POST">
                    
                    <!-- Pengaturan Tampilan -->
                    <h5 class="mb-3"><?= __('display_settings') ?></h5>
                    <div class="mb-4">
                        <label for="theme" class="form-label"><?= __('theme') ?></label>
                        <select name="theme" id="theme" class="form-select">
                            <?php foreach ($themes as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($user['theme'] ?? 'default') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr>

                    <!-- Pengaturan Regional -->
                    <h5 class="mb-3 mt-4"><?= __('regional_settings') ?></h5>
                    <div class="mb-3">
                        <label for="currency" class="form-label"><?= __('currency') ?></label>
                        <select name="currency" id="currency" class="form-select">
                             <?php foreach ($currencies as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($user['currency'] ?? 'IDR') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="language" class="form-label"><?= __('language') ?></label>
                        <select name="language" id="language" class="form-select">
                            <?php foreach ($languages as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($user['language'] ?? 'id') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary"><?= __('save_changes') ?></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>