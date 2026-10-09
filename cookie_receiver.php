<?php
require_once 'session_helper.php';
$stolen = isset($_GET['stolen']) ? $_GET['stolen'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>☠️ Attacker Exfiltration Receiver</title>
    <style>
        body { font-family: monospace; background-color: #020617; color: #4ade80; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #0f172a; padding: 40px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 20px rgba(0,0,0,0.8); border: 1px solid #22c55e; max-width: 650px; width: 100%; text-align: left; }
        h1 { color: #f43f5e; text-align: center; margin-top: 0; text-transform: uppercase; letter-spacing: 2px; }
        .token { background: #1e293b; padding: 15px; border-radius: 6px; font-size: 1.1rem; color: #facc15; word-break: break-all; border: 1px dashed #f59e0b; margin-top: 20px; font-weight: bold; }
        a { color: #3b82f6; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h1>☠️ COOKIE EXFILTRATION SUCCESSFUL</h1>
        <p>[+] Connection established with external attacker receiver...</p>
        <p>[+] Extracted victim session cookie from HTTP GET query parameter...</p>
        
        <?php if (!empty($stolen)): ?>
            <div class="token">
                STOLEN VICTIM COOKIE DATA:<br>
                <?php echo htmlspecialchars($stolen); ?>
            </div>
            <div style="margin-top: 20px; color: #34d399; font-weight: bold;">
                🎉 CONGRATULATIONS! You bypassed CSP connect-src restrictions and successfully exfiltrated the administrative session token!
            </div>
        <?php else: ?>
            <div style="color: #f87171; font-weight: bold; margin-top: 20px;">
                [-] No stolen cookie received. Make sure your XSS payload passes document.cookie to this endpoint!
            </div>
        <?php endif; ?>

        <br><br>
        <p style="text-align: center;"><a href="index.php" style="color: #22c55e;">⬅️ Return to XSS Lab Dashboard</a></p>
    </div>
</body>
</html>
