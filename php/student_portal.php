<?php
session_start();

// If user not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "user_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT fullname, photo FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $photoData);
$stmt->fetch();
$stmt->close();
$conn->close();

// Handle photo (if null, show default)
if ($photoData) {
    $photoSrc = "data:image/jpeg;base64," . base64_encode($photoData);
} else {
    $photoSrc = "../images/default.png"; // fallback image
}
?>
