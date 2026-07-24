<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$mobile = $_SESSION['user'];

// 1. Fetch user data directly using mobile primary key
$stmt = $conn->prepare("SELECT name, points FROM users WHERE mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Fetch all log actions for this specific profile
$history_stmt = $conn->prepare("SELECT action_type, points_changed, timestamp FROM transaction_history WHERE mobile = ? ORDER BY timestamp DESC");
$history_stmt->bind_param("s", $mobile);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WINBIN ECO SYSTEM - DASHBOARD</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #0d1624; color: #ffffff; height: 100vh; display: flex; flex-direction: column; }
        header { background-color: #0a111c; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1a2638; }
        .logo-area { font-weight: bold; font-size: 1.2rem; color: #00e699; letter-spacing: 1px; }
        .main-content { flex: 1; padding: 40px; display: flex; gap: 30px; }
        .panel { background-color: #152233; border-radius: 12px; padding: 30px; border: 1px solid #203147; height: 75vh; }
        .left-panel { flex: 1.3; display: flex; flex-direction: column; align-items: center; overflow-y: auto; gap: 20px; }
        .right-panel { flex: 0.7; display: flex; flex-direction: column; justify-content: space-between; align-items: center; }
        .brand-title { font-size: 1.8rem; color: #00e699; font-weight: 700; text-transform: uppercase; }
        .btn { background-color: #ff4d4d; color: #ffffff; padding: 15px 35px; font-weight: bold; border-radius: 8px; cursor: pointer; text-transform: uppercase; width: 100%; border: none; }
        .points-display { display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 20px 0; }
        .points-number { font-size: 6.5rem; font-weight: 800; color: #00e699; line-height: 1; }
        .points-label { color: #536982; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-top: 10px; }
        
        /* History Ledger Table CSS Layout */
        .ledger-box { width: 100%; background: #0a111c; border: 1px solid #1f2f45; border-radius: 8px; padding: 20px; }
        .ledger-title { font-size: 1rem; color: #ffffff; text-transform: uppercase; margin-bottom: 15px; border-bottom: 1px solid #1f2f45; padding-bottom: 8px; letter-spacing: 0.5px; }
        .ledger-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        .ledger-table th { color: #8fa0b5; padding: 10px; border-bottom: 1px solid #1f2f45; text-transform: uppercase; font-size: 0.75rem; }
        .ledger-table td { padding: 12px 10px; border-bottom: 1px solid #152233; color: #e1e7ef; }
        .status-add { color: #00e699; font-weight: bold; }
        .status-deduct { color: #ff4d4d; font-weight: bold; }
    </style>
</head>
<body>
    <header><div class="logo-area">♻️ WINBIN ECO SYSTEM</div></header>
    <div class="main-content">
        
        <!-- Left Panel: Clear Transaction Message + History Ledger List -->
        <div class="panel left-panel">
            <div style="text-align: center; background: #0a111c; border: 1px solid #1f2f45; padding: 20px; border-radius: 8px; width: 100%;">
                <h2 class="brand-title" style="font-size: 1.4rem;">🎉 Account Updated</h2>
            </div>
            
            <div class="ledger-box">
                <div class="ledger-title">Activity History Ledger</div>
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th>Action Type</th>
                            <th>Points</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($history_result->num_rows === 0): ?>
                            <tr><td colspan="3" style="text-align: center; color: #536982;">No movements registered yet.</td></tr>
                        <?php else: ?>
                            <?php while ($row = $history_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <span class="<?php echo $row['action_type'] === 'added' ? 'status-add' : 'status-deduct'; ?>">
                                            <?php echo strtoupper($row['action_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $row['action_type'] === 'added' ? '+' : '-'; ?><?php echo $row['points_changed']; ?> ₹</td>
                                    <td style="color: #8fa0b5; font-size: 0.8rem;"><?php echo $row['timestamp']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Panel: User Stats Summary -->
        <div class="panel right-panel">
            <div style="text-align: center;">
                <div style="font-size: 1.2rem; text-transform: uppercase; font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></div>
                <div style="font-size: 0.9rem; color: #536982; margin-top: 5px;">📞 <?php echo htmlspecialchars($mobile); ?></div>
            </div>
            <div class="points-display">
                <div class="points-number"><?php echo $user['points']; ?></div>
                <div class="points-label">Available Reward Balance</div>
            </div>
            <form method="post" style="width: 100%;"><button type="submit" name="logout" class="btn">Close Session</button></form>
        </div>
        
    </div>
</body>
</html>