<?php
require_once '../config/bootstrap.php';

checkRole('user');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data foto profil saat ini untuk menghapus file lama jika perlu
$queryUser = "SELECT profile_picture FROM users WHERE id = ?";
$stmtUser = mysqli_prepare($conn, $queryUser);
mysqli_stmt_bind_param($stmtUser, "i", $user_id);
mysqli_stmt_execute($stmtUser);
$resultUser = mysqli_stmt_get_result($stmtUser);
$currentUser = mysqli_fetch_assoc($resultUser);
$oldPicture = $currentUser['profile_picture'];

function updateUserPicture($conn, $path, $user_id, $oldPicture) {
    $updateQuery = "UPDATE users SET profile_picture = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "si", $path, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Jika gambar lama adalah custom upload, hapus file-nya
        if ($oldPicture && strpos($oldPicture, 'uploads/profile_pictures/') === 0 && file_exists('../' . $oldPicture)) {
            unlink('../' . $oldPicture);
        }
        header("Location: profile.php?success=Foto profil berhasil diperbarui.");
    } else {
        header("Location: profile.php?error=Gagal memperbarui foto profil di database.");
    }
    exit;
}

// Logika 1: Jika pengguna memilih avatar default
if (isset($_POST['default_avatar']) && !empty($_POST['default_avatar'])) {
    $newPicturePath = $_POST['default_avatar'];
    
    // Validasi sederhana untuk memastikan path avatar default valid
    $allowed_defaults = [
        'assets/img/avatars/Cherry.jpg',
        'assets/img/avatars/Jeruk.jpg',
        'assets/img/avatars/MDL.jpg'
    ];
    if (!in_array($newPicturePath, $allowed_defaults, true)) {
        header("Location: profile.php?error=Avatar yang dipilih tidak valid.");
        exit;
    }
    
    updateUserPicture($conn, $newPicturePath, $user_id, $oldPicture);
}

// Logika 2: Jika pengguna mengupload file baru
if (isset($_FILES['custom_picture']) && $_FILES['custom_picture']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['custom_picture'];

    // Validasi ukuran file (maks 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        header("Location: profile.php?error=Ukuran file terlalu besar. Maksimal 2MB.");
        exit;
    }

    // Validasi tipe file
    $fileInfo = getimagesize($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!$fileInfo || !in_array($fileInfo['mime'], $allowedTypes)) {
        header("Location: profile.php?error=Tipe file tidak diizinkan. Hanya JPG, PNG, GIF.");
        exit;
    }

    // Proses upload
    $uploadDir = '../uploads/profile_pictures/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('user' . $user_id . '-', true) . '.' . $extension;
    $newPicturePath = 'uploads/profile_pictures/' . $newFileName;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        updateUserPicture($conn, $newPicturePath, $user_id, $oldPicture);
    } else {
        header("Location: profile.php?error=Gagal memindahkan file yang diupload.");
        exit;
    }
}

// Jika tidak ada input yang valid
header("Location: profile.php?error=Tidak ada foto yang dipilih atau diupload.");
exit;

?>