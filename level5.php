<?php
require_once 'session_helper.php';

$site = isset($_GET['site']) ? $_GET['site'] : 'https://example.com';

// The developer rigorously encodes all HTML special characters with ENT_QUOTES.
// No quotes or brackets can escape the attribute.
$encoded_site = htmlspecialchars($site, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 5: Medium | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 600px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #f59e0b; }
        h1 { color: #f59e0b; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #f59e0b; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #d97706; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .result-box { background: #0f172a; padding: 20px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; text-align: center; }
        .user-link { display: inline-block; background-color: #38bdf8; color: #0f172a; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .user-link:hover { background-color: #0ea5e9; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 5: Medium — Anchor Href Pseudo-Protocol</h1>
        <p>The developer believes this page is 100% secure because they used <code>htmlspecialchars(..., ENT_QUOTES)</code>. You cannot break out of the attribute quotes. However, your input lands inside an anchor <code>href</code>.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$safe = htmlspecialchars($_GET['site'], ENT_QUOTES, 'UTF-8');
// VULNERABLE: Direct assignment to href allows pseudo-protocols!
echo '&lt;a href="' . $safe . '"&gt;Visit Author Website&lt;/a&gt;';
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Trigger JavaScript without breaking out of the attribute. Exploit the browser's <code>javascript:</code> pseudo-protocol handler: <br>
        <code>javascript:alert(document.cookie)</code>.</p>

        <form action="level5.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="site">Website URL:</label><br>
            <input type="text" id="site" name="site" value="<?php echo htmlspecialchars($site); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" placeholder="javascript:alert(document.cookie)"><br>
            <input type="submit" value="Update Profile Link" class="btn">
        </form>

        <div class="result-box">
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Generated Profile Anchor Tag:</p>
            <!-- VULNERABLE: href pseudo-protocol execution -->
            <a href="<?php echo $encoded_site; ?>" class="user-link">🔗 Visit Author Website</a>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 10px;">(Click the blue button above to test the payload execution)</p>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
