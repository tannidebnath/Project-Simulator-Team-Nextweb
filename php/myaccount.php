<?php
// Run only if session started from parent
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.html");
    exit();
}

//DB CON
include('config.php');

$user_id = $_SESSION['user_id'];

$password_error = "";
$password_success = "";

/* ---------------- PHOTO UPLOAD ---------------- */
if (isset($_POST['upload_photo']) && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];

    if ($file['error'] === 0) {
        $imageData = file_get_contents($file['tmp_name']);

        $sql = "UPDATE users SET photo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("bi", $null, $user_id);
        $stmt->send_long_data(0, $imageData);
        $stmt->execute();
        $stmt->close();

        $_SESSION['photo_uploaded'] = true;
        $_SESSION['photo_data'] = base64_encode($imageData);
    }
}

/* ---------------- PASSWORD CHANGE ---------------- */
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch user’s current hashed password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!$hashedPassword) {
        $password_error = "❌ User not found.";
    } elseif (!password_verify($current_password, $hashedPassword)) {
        $password_error = "❌ Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "❌ New passwords do not match.";
    } else {
        // Hash and update new password
        $newHashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newHashedPassword, $user_id);
        if ($stmt->execute()) {
            $password_success = "✅ Password updated successfully.";
        } else {
            $password_error = "❌ Failed to update password.";
        }
        $stmt->close();
    }
}

/* ---------------- FETCH PROFILE PHOTO ---------------- */
$sql = "SELECT photo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($photoData);
$stmt->fetch();
$stmt->close();
$conn->close();

// Decide which image to display
if (!empty($_SESSION['photo_uploaded'])) {
    $photoSrc = "data:image/jpeg;base64," . $_SESSION['photo_data'];
    unset($_SESSION['photo_uploaded']);
} elseif ($photoData) {
    $photoSrc = "data:image/jpeg;base64," . base64_encode($photoData);
} else {
    $photoSrc = "../images/upload.png";
}
