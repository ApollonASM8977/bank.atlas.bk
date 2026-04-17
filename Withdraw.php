<?php
// © 2026 Aboubacar Sidick Meite (ApollonIUGB77) — All Rights Reserved
session_start();
include "db_connect.php";

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$id       = (int) $_SESSION['id'];
$name     = "";
$error    = "";
$admin_id = 1;

// ── CSRF token ────────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ── Retrieve user name (prepared) ─────────────────────────────────────────────
$stmt = mysqli_prepare($conn, "SELECT name FROM atlasin WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ── Handle withdrawal ─────────────────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid request. Please try again.";
    } else {
        $agent_phone = trim($_POST['phone']  ?? '');
        $amount      = (float) ($_POST['amount'] ?? 0);

        if (empty($agent_phone)) {
            $error = "Agent phone number is required.";
        } elseif ($amount <= 0) {
            $error = "Amount must be greater than 0.";
        } elseif ($amount > 5_000_000) {
            $error = "Amount exceeds maximum withdrawal limit (5,000,000 FCFA).";
        } else {
            // Verify agent exists (prepared)
            $stmt = mysqli_prepare($conn, "SELECT id, role FROM atlasin WHERE phone = ?");
            mysqli_stmt_bind_param($stmt, "s", $agent_phone);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $agent_id, $agent_role);
            $found = mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (!$found) {
                $error = "Agent phone number not found.";
            } else {
                // Check user balance (prepared)
                $stmt = mysqli_prepare($conn, "SELECT balance FROM atlasin WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $balance);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                if ($balance < $amount) {
                    $error = "Insufficient balance.";
                } else {
                    $fees               = round($amount * 0.005, 2); // 0.5% withdrawal fee
                    $debit              = $amount;
                    $transaction_number = bin2hex(random_bytes(8));

                    mysqli_begin_transaction($conn);
                    try {
                        // Debit user
                        $s1 = mysqli_prepare($conn, "UPDATE atlasin SET balance = balance - ? WHERE id = ? AND balance >= ?");
                        mysqli_stmt_bind_param($s1, "dii", $debit, $id, $debit);
                        mysqli_stmt_execute($s1);
                        if (mysqli_stmt_affected_rows($s1) !== 1) throw new Exception("Debit failed.");
                        mysqli_stmt_close($s1);

                        // Credit fees to admin
                        $s2 = mysqli_prepare($conn, "UPDATE atlasin SET balance = balance + ? WHERE id = ?");
                        mysqli_stmt_bind_param($s2, "di", $fees, $admin_id);
                        mysqli_stmt_execute($s2);
                        mysqli_stmt_close($s2);

                        // Log withdrawal in transaction table
                        $s3 = mysqli_prepare($conn, "INSERT INTO transaction (sender, receiver, amount, fees, transaction_number) VALUES (?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($s3, "iidds", $id, $agent_id, $amount, $fees, $transaction_number);
                        mysqli_stmt_execute($s3);
                        mysqli_stmt_close($s3);

                        mysqli_commit($conn);

                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        $success = "Withdrawal of " . number_format($amount, 0, '.', ' ') . " FCFA successful! Ref: #" . htmlspecialchars($transaction_number);
                        header("Refresh: 3; url=atlasmoney.php");
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $error = "Withdrawal failed. Please try again.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Withdraw — Atlas Money</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        header { background: #1a237e; color: #fff; padding: 20px; text-align: center; margin-bottom: 30px; }
        header h2 { font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .card {
            background: #fff; padding: 30px; width: 420px; margin: 0 auto;
            border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        label { display: block; margin-top: 16px; font-weight: bold; font-size: 14px; color: #333; }
        input[type=text], input[type=number] {
            width: 100%; padding: 12px; margin-top: 6px;
            border: 2px solid #ddd; border-radius: 6px; font-size: 15px;
            transition: border-color 0.2s;
        }
        input:focus { border-color: #1a237e; outline: none; }
        .fees-note { color: #e53935; font-size: 12px; margin-top: 4px; }
        input[type=submit] {
            width: 100%; margin-top: 24px; padding: 14px;
            background: #1a237e; color: #fff; border: none;
            border-radius: 6px; font-size: 16px; font-weight: bold;
            cursor: pointer; transition: background 0.2s;
        }
        input[type=submit]:hover { background: #283593; }
        .btn-row { display: flex; gap: 10px; margin-top: 12px; }
        .btn-row a { flex: 1; }
        .btn-secondary {
            display: block; width: 100%; padding: 11px; text-align: center;
            background: #546e7a; color: #fff; border: none; border-radius: 6px;
            font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: none;
            transition: background 0.2s;
        }
        .btn-secondary:hover { background: #607d8b; }
        .error   { background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-top: 16px; border-left: 4px solid #e53935; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 6px; margin-top: 16px; border-left: 4px solid #43a047; }
        .user-info { text-align: center; color: #fff; font-size: 14px; margin-top: 4px; opacity: 0.8; }
    </style>
</head>
<body>
    <header>
        <h2>🏦 Withdraw</h2>
        <p class="user-info">Logged in as: <?= htmlspecialchars($name) ?></p>
    </header>
    <div class="card">
        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <label for="phone">Agent Phone Number</label>
            <input type="text" id="phone" name="phone" maxlength="15"
                   placeholder="e.g. 0701234567" required
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

            <label for="amount">Amount (FCFA)</label>
            <input type="number" id="amount" name="amount" min="1" step="1"
                   placeholder="e.g. 10000" required>
            <p class="fees-note">⚠ A 0.5% withdrawal fee applies.</p>

            <input type="submit" value="Withdraw">
            <div class="btn-row">
                <a href="atlasmoney.php" class="btn-secondary">🏠 Home</a>
                <a href="logout.php"     class="btn-secondary">🚪 Logout</a>
            </div>
        </form>
    </div>
</body>
</html>
