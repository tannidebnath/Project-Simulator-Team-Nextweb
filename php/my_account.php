<?php
// php/my_account.php — handles teacher fixed data and session start
session_start();

$current = basename($_SERVER['SCRIPT_NAME']);

// ---- FIXED TEACHER INFO (read-only) ----
$fixed = [
  'name'          => 'Mr Rayhan',
  'email'         => 'rayhan@1122.com',
  'institution'   => 'ABC University',
  'qualification' => 'MSc in Computer Science',
  'interests'     => 'Machine Learning, Deep Learning, Image Processing',
  'avatar'        => '../images/teachers/rayhan.jpg', // adjust path to your project
];

// For the right profile card header
$displayName = $fixed['name'];
?>
