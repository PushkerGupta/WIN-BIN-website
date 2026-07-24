<?php
session_start();
require 'db.php';

if (!isset($_SESSION['bottle_accepted'])) {
    header("Location: index.php");
    exit();
}

$msg = "";

if (isset($_POST['action'])) {
    $mobile = $_POST['mobile'];
    $name = $_POST['name'] ?? '';

    // --- CASE 1: RETURNING USER LOGIN ---
    if ($_POST['action'] == 'login') {
        $stmt = $conn->prepare("SELECT * FROM users WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // 1. Increment total profile balance points
            $update = $conn->prepare("UPDATE users SET points = points + 1 WHERE mobile = ?");
            $update->bind_param("s", $mobile);
            $update->execute();

            // 2. Insert row into history ledger table
            $log = $conn->prepare("INSERT INTO transaction_history (mobile, action_type, points_changed) VALUES (?, 'added', 1)");
            $log->bind_param("s", $mobile);
            $log->execute();
            
            $_SESSION['user'] = $mobile;
            unset($_SESSION['bottle_accepted']);
            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "Profile reference mismatch. Please Register.";
        }
    }

    // --- CASE 2: NEW USER REGISTRATION ---
    if ($_POST['action'] == 'signup') {
        // 1. Insert brand new profile using mobile primary key validation template
        $stmt = $conn->prepare("INSERT INTO users (mobile, name, points) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $mobile, $name);
        
        if ($stmt->execute()) {
            // 2. Insert row into history ledger table
            $log = $conn->prepare("INSERT INTO transaction_history (mobile, action_type, points_changed) VALUES (?, 'added', 1)");
            $log->bind_param("s", $mobile);
            $log->execute();

            $_SESSION['user'] = $mobile;
            unset($_SESSION['bottle_accepted']);
            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "Registration conflict. Mobile entry already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WINBIN ECO SYSTEM - AUTHENTICATION</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #0d1624; color: #ffffff; height: 100vh; display: flex; flex-direction: column; }
        header { background-color: #0a111c; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a2638; }
        .logo-area { font-weight: bold; font-size: 1.2rem; color: #00e699; letter-spacing: 1px; }
        .main-content { flex: 1; padding: 40px; display: flex; gap: 30px; justify-content: center; align-items: center; }
        .auth-card { background-color: #152233; border-radius: 12px; padding: 40px; border: 1px solid #203147; width: 100%; max-width: 480px; box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
        .brand-title { font-size: 1.8rem; color: #00e699; font-weight: 700; text-transform: uppercase; text-align: center; margin-bottom: 10px; }
        .subtitle { color: #8fa0b5; font-size: 0.95rem; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #8fa0b5; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; margin-bottom: 8px; letter-spacing: 0.5px; }
        .form-group input { width: 100%; background-color: #0a111c; border: 1px solid #203147; padding: 14px 18px; border-radius: 8px; color: #ffffff; font-size: 1rem; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #00e699; }
        .btn { background-color: #00e699; color: #0d1624; padding: 15px; font-weight: bold; border-radius: 8px; cursor: pointer; text-transform: uppercase; width: 100%; border: none; font-size: 1rem; margin-top: 10px; transition: background 0.2s; }
        .btn:hover { background-color: #00cc88; }
        .error-msg { background-color: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 12px; border-radius: 6px; font-size: 0.9rem; text-align: center; margin-bottom: 20px; }
        .toggle-text { text-align: center; color: #8fa0b5; font-size: 0.9rem; margin-top: 25px; }
        .toggle-link { color: #00e699; text-decoration: none; font-weight: bold; cursor: pointer; }
        .toggle-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header><div class="logo-area">♻️ WINBIN ECO SYSTEM</div></header>
    
    <div class="main-content">
        <div class="auth-card">
            <h1 class="brand-title">Claim Reward</h1>
            <p class="subtitle" id="auth-subtitle">Enter your registered mobile number to claim points</p>
            
            <?php if (!empty($msg)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <form method="post" id="login-form">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="tel" name="mobile" placeholder="Enter 10-digit number" required maxlength="15">
                </div>
                <button type="submit" class="btn">Claim Point</button>
                <div class="toggle-text">
                    New to WINBIN? <span class="toggle-link" onclick="toggleMode('signup')">Register Profile</span>
                </div>
            </form>

            <form method="post" id="signup-form" style="display: none;">
                <input type="hidden" name="action" value="signup">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="tel" name="mobile" placeholder="Enter 10-digit number" required maxlength="15">
                </div>
                <button type="submit" class="btn">Create & Claim</button>
                <div class="toggle-text">
                    Already have a profile? <span class="toggle-link" onclick="toggleMode('login')">Sign In Instead</span>
                </div>