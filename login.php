<?php
// © 2026 Aboubacar Sidick Meite (ApollonASM8977) — All Rights Reserved
session_start();
include "db_connect.php";

define("MAX_LOGIN_ATTEMPTS", 3); // Maximum number of allowed login attempts
define("SUSPENSION_TIME", 60 * 60); // Account suspension time (in seconds) for exceeding login attempts

if (isset($_POST['phone']) && isset($_POST['password'])) 
{
    function validate ($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $phone = validate($_POST['phone']);
    $pass = validate($_POST['password']);

    if (empty($phone))
    {
        header("Location: index.php?error=Phone number is required");
        exit();
    }
    else if (empty($pass))
    {
        header("Location: index.php?error=Password is required");
        exit();
    }

    // Check if the user has exceeded the maximum number of login attempts
    if (isset($_SESSION['login_attempts'][$phone]) && $_SESSION['login_attempts'][$phone] >= MAX_LOGIN_ATTEMPTS) {
        // Check if enough time has passed since the last login attempt
        if (time() - $_SESSION['login_attempts_time'][$phone] < SUSPENSION_TIME) {
            $remaining_time = SUSPENSION_TIME - (time() - $_SESSION['login_attempts_time'][$phone]);
            $message = "Your account has been suspended for $remaining_time seconds due to too many failed login attempts. Please try again later.";
            $_SESSION['message'] = $message;
            header("Location: index.php?error=$message");
            exit();
        } else {
            // Reset the login attempts
            $_SESSION['login_attempts'][$phone] = 0;
        }
    }

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM atlasin WHERE phone = ?");
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1)
    {
        $row = mysqli_fetch_assoc($result);
        // Verify password — supports bcrypt hashes (new) and plain-text (legacy migration)
        $storedPass  = $row['password'];
        $passwordOk  = false;

        if (password_verify($pass, $storedPass)) {
            // Modern bcrypt hash
            $passwordOk = true;
        } elseif ($storedPass === $pass) {
            // Legacy plain-text — auto-upgrade to bcrypt on next login
            $passwordOk = true;
            $newHash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $upStmt  = mysqli_prepare($conn, "UPDATE atlasin SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($upStmt, "si", $newHash, $row['id']);
            mysqli_stmt_execute($upStmt);
            mysqli_stmt_close($upStmt);
        }

        if ($passwordOk)
        {
            $_SESSION['phone'] = $row['phone'];
            $_SESSION['name']  = $row['name'];
            $_SESSION['id']    = $row['id'];

            // Reset the login attempts
            $_SESSION['login_attempts'][$phone] = 0;

            // Redirect based on admin flag (prefer a dedicated `is_admin` column over hardcoded check)
            if (!empty($row['is_admin']) && (int)$row['is_admin'] === 1)
            {
                header("Location: AdminPage.php");
            }
            else
            {
                header("Location: atlasmoney.php");
            }
            exit();
        }
        else 
        {
            // Increment the login attempts for the current user
            if (!isset($_SESSION['login_attempts'][$phone])) {
                $_SESSION['login_attempts'][$phone] = 1;
                $_SESSION['login_attempts_time'][$phone] = time();
            } else {
                $_SESSION['login_attempts'][$phone]++;
            }

            $_SESSION['message'] = "Incorrect Phone Number or Password."; 
            header("Location: index.php?error=Incorrect Phone Number or Password");
            echo '<h3>Invalid Phone Number or password</h3>';
            exit();
        }
    }
    else 
    {
        // Increment the login attempts for the current user
        if (!isset($_SESSION['login_attempts'][$phone])) {
            $_SESSION['login_attempts'][$phone] = 1;
            $_SESSION['login_attempts_time'][$phone] = time();
        } else {
            $_SESSION['login_attempts'][$phone]++;
        }
        
        // Check if the user has exceeded the maximum number of login attempts
        if (isset($_SESSION['login_attempts'][$phone]) && $_SESSION['login_attempts'][$phone] >= MAX_LOGIN_ATTEMPTS) {
            // Check if enough time has passed since the last login attempt
            if (time() - $_SESSION['login_attempts_time'][$phone] < SUSPENSION_TIME) {
                $remaining_time = SUSPENSION_TIME - (time() - $_SESSION['login_attempts_time'][$phone]);
                $message = "Your account has been suspended for $remaining_time seconds due to too many failed login attempts. Please try again later.";
                $_SESSION['message'] = $message;
                header("Location: index.php?error=$message");
                exit();
            } else {
                // Reset the login attempts
                $_SESSION['login_attempts'][$phone] = 0;
            }
        }
        
        $_SESSION['message'] = "Incorrect Phone Number or Password.";
        header("Location: index.php?error=Incorrect Phone Number or Password");
        echo '<h3>Invalid Phone Number or password</h3>';
        exit();
    }
}
