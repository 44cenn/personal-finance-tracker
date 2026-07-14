<?php
require_once '../config/bootstrap.php';

checkRole('user');

$query = "SELECT * FROM categories WHERE type = 'income' AND (user_id IS NULL OR user_id = ?) ORDER BY name ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('add_income') ?> - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body data-theme="<?= htmlspecialchars($current_theme) ?>">
<?php include __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex align-items-center mb-3">
        <a href="income.php" class="btn btn-outline-secondary me-2 btn-back-icon" title="<?= __('back_to_income') ?>">‹</a>
        <h3><?= __('add_income') ?></h3>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">

            <?php if (isset($_GET['error_key'])) : ?>
                <div class="alert alert-danger">
                    <?= __($_GET['error_key']); ?>
                </div>
            <?php endif; ?>

            <form action="income_store.php" method="POST" class="needs-validation-custom" data-error-required="<?= __('error_all_fields_required') ?>" data-error-amount="<?= __('error_amount_must_be_positive') ?>">

                <div class="mb-3">
                    <label class="form-label"><?= __('category') ?></label>
                    <select name="category_id" class="form-select" required data-required="true">
                        <option value=""><?= __('choose_option') ?></option>

                        <?php while ($category = mysqli_fetch_assoc($result)) : ?>
                            <option value="<?= $category['id']; ?>">
                                <?= htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('income_amount') ?></label>
                    <input type="text" name="amount" class="form-control" required data-required="true" data-amount="true" data-format="numeric" placeholder="<?= __('amount_placeholder_income') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('date') ?></label>
                    <input type="date" name="transaction_date" class="form-control" required data-required="true">
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= __('description') ?></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="<?= __('income_description_placeholder') ?>"></textarea>
                </div>

                <button type="submit" class="btn btn-success"><?= __('save') ?></button>

            </form>

        </div>
    </div>
</div>

<!-- Custom Validation JavaScript -->
<script src="../assets/js/validation.js"></script>
<script src="../assets/js/input-formatter.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>