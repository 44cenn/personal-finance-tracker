<?php
require_once '../config/bootstrap.php';
 
checkRole('user');

// Ambil data user saat ini
// $user is already available from bootstrap.php
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_language) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('user_profile') ?> - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
include __DIR__ . '/navbar.php';
?>

    <div class="container mt-4">
        <div class="d-flex align-items-center mb-3">
            <a href="../index.php" class="btn btn-outline-secondary me-2 btn-back-icon" title="<?= __('back') ?>">‹</a>
            <h3><?= __('user_profile') ?></h3>
        </div>
        <p class="text-muted"><?= __('profile_subtitle') ?></p>

        <?php if (isset($_GET['success_key'])): ?>
            <div class="alert alert-success"><?= __($_GET['success_key']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error_key'])): ?>
            <div class="alert alert-danger"><?= __($_GET['error_key']); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Update Informasi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><?= __('update_information') ?></div>
                    <div class="card-body">
                        <form action="profile_update_info.php" method="POST" class="needs-validation-custom">
                            <div class="mb-3">
                                <label for="name" class="form-label"><?= __('full_name') ?></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><?= __('email_address') ?></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><?= __('save_changes') ?></button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Form Ganti Password -->
            <div class="col-md-6 mt-4 mt-md-0">
                <div class="card">
                    <div class="card-header"><?= __('change_password') ?></div>
                    <div class="card-body">
                        <form action="profile_update_password.php" method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label"><?= __('current_password') ?></label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label"><?= __('new_password') ?></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label"><?= __('confirm_new_password') ?></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning"><?= __('change_password') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Ganti Foto Profil -->
        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header"><?= __('change_profile_picture') ?></div>
                <div class="card-body">
                    <form action="profile_update_picture.php" method="POST" enctype="multipart/form-data">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="../<?= htmlspecialchars($user['profile_picture'] ?? 'assets/images/avatars/default.png'); ?>" class="img-fluid rounded-circle" alt="<?= __('profile') ?>" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label for="custom_picture" class="form-label"><?= __('upload_new_photo') ?></label>
                                    <input class="form-control" type="file" id="custom_picture" name="custom_picture" accept="image/png, image/jpeg, image/gif">
                                    <div class="form-text"><?= __('upload_from_device') ?></div>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label"><?= __('or_choose_default_avatar') ?></label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <!-- Avatar Cherry -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="default_avatar" id="avatar_cherry" value="assets/img/avatars/Cherry.jpg">
                                            <label class="form-check-label" for="avatar_cherry"><img src="../assets/img/avatars/Cherry.jpg" width="50" class="rounded-circle" alt="Cherry"></label>
                                        </div>
                                        <!-- Avatar Jeruk -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="default_avatar" id="avatar_jeruk" value="assets/img/avatars/Jeruk.jpg">
                                            <label class="form-check-label" for="avatar_jeruk"><img src="../assets/img/avatars/Jeruk.jpg" width="50" class="rounded-circle" alt="Jeruk"></label>
                                        </div>
                                        <!-- Avatar MDL -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="default_avatar" id="avatar_mdl" value="assets/img/avatars/MDL.jpg">
                                            <label class="form-check-label" for="avatar_mdl"><img src="../assets/img/avatars/MDL.jpg" width="50" class="rounded-circle" alt="MDL"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-info text-white"><?= __('update_profile_picture') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
