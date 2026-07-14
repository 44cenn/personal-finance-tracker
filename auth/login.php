<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Personal Finance Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../welcome.php" style="color: var(--primary-color);">Finance Tracker</a>
        <div>
            <a href="../welcome.php" class="btn btn-outline-secondary me-2">Kembali ke Halaman Sambutan</a>
            <a href="register.php" class="btn btn-primary">Belum punya akun? Register</a>
        </div>
    </div>
</nav>

<div class="auth-wrapper">
    <div class="card auth-card border-0">
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h3 class="auth-title">Finance Tracker</h3>
                <p class="auth-subtitle mb-0">Masuk untuk mengelola keuangan pribadi Anda.</p>
            </div>

            <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])) : ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2">
                    Login
                </button>
            </form>

            <p class="text-center mt-4 mb-0">
                Belum punya akun?
                <a href="register.php" class="fw-bold">Register</a>
            </p>

        </div>
    </div>
</div>

</body>
</html>