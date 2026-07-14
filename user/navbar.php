<?php
// This partial assumes that bootstrap.php has been included, providing
// functions like __() and variables like $current_theme.

// Define the base path for the application to create absolute URLs
$base_path = '/personal-finance-tracker';

// Get the current script's filename to determine the active page
$current_file = basename($_SERVER['PHP_SELF']);

// Define navigation links
$nav_links = [
    __('home') => ['url' => $base_path . '/index.php', 'file' => 'index.php'],
    __('income') => ['url' => $base_path . '/user/income.php', 'file' => 'income.php'],
    __('expense') => ['url' => $base_path . '/user/expense.php', 'file' => 'expense.php'],
    __('history') => ['url' => $base_path . '/user/transactions.php', 'file' => 'transactions.php'],
    __('profile') => ['url' => $base_path . '/user/profile.php', 'file' => 'profile.php'],
    __('settings') => ['url' => $base_path . '/user/settings.php', 'file' => 'settings.php'],
];

// Determine navbar color based on the current page for better UX
$navbar_color_var = 'var(--primary-color)'; // Default color

$income_pages = ['income.php', 'income_create.php', 'income_edit.php'];
$expense_pages = ['expense.php', 'expense_create.php', 'expense_edit.php'];

if (in_array($current_file, $income_pages)) {
    $navbar_color_var = 'var(--success-color)';
} elseif (in_array($current_file, $expense_pages)) {
    $navbar_color_var = 'var(--danger-color)';
}

?>
<nav class="navbar navbar-dark" style="background-color: <?= $navbar_color_var ?> !important;">
    <div class="container">
        <a class="navbar-brand" href="<?= $base_path ?>/welcome.php">Finance Tracker</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php foreach ($nav_links as $name => $link): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_file === $link['file']) ? 'active' : '' ?>" href="<?= $link['url'] ?>"><?= $name ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $base_path ?>/auth/logout.php"><?= __('logout') ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>