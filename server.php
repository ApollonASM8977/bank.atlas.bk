<?php
// © 2026 Aboubacar Sidick Meite (ApollonIUGB77) — All Rights Reserved
session_start();

$con = mysqli_connect('localhost', 'root', '', 'atlasmoney');
mysqli_select_db($con, 'atlasmoney');

// Sanitize inputs
$name         = trim(htmlspecialchars($_POST['name'],         ENT_QUOTES, 'UTF-8'));
$email        = trim(htmlspecialchars($_POST['email'],        ENT_QUOTES, 'UTF-8'));
$phone        = trim(htmlspecialchars($_POST['phone'],        ENT_QUOTES, 'UTF-8'));
$Pass         = $_POST['password'];
$confirm_Pass = $_POST['repeatPassword'];

// Validate password length
if (strlen($Pass) < 4) {
    header("Location: register.php?error=" . urlencode("Password must be at least 4 characters"));
    exit();
}

// Verify password confirmation
if ($Pass !== $confirm_Pass) {
    header("Location: register.php?error=" . urlencode("Passwords do not match"));
    exit();
}

// Check duplicate email or phone — prepared statement
$stmt = mysqli_prepare($con, "SELECT email, phone FROM atlasin WHERE email = ? OR phone = ?");
mysqli_stmt_bind_param($stmt, "ss", $email, $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $error = ($email === $row['email']) ? "Email already exists" : "Phone number already registered";
    header("Location: register.php?error=" . urlencode($error));
    exit();
}

// Insert new user — prepared statement
$stmt_insert = mysqli_prepare($con,
    "INSERT INTO atlasin (name, email, phone, password) VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param($stmt_insert, "ssss", $name, $email, $phone, $Pass);
mysqli_stmt_execute($stmt_insert);

// Fetch inserted user for session
$stmt_auth = mysqli_prepare($con, "SELECT * FROM atlasin WHERE phone = ?");
mysqli_stmt_bind_param($stmt_auth, "s", $phone);
mysqli_stmt_execute($stmt_auth);
$result_auth = mysqli_stmt_get_result($stmt_auth);

if (mysqli_num_rows($result_auth) === 1) {
    $row = mysqli_fetch_assoc($result_auth);
    $_SESSION['name']  = $row['name'];
    $_SESSION['phone'] = $row['phone'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['id']    = $row['id'];
    header("Location: atlasmoney.php");
    exit();
} else {
    header("Location: login.php?error=" . urlencode("Registration failed. Please try again."));
    exit();
}
?>
