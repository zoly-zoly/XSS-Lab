<?php
require_once 'session_helper.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 1: Low | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 600px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #10b981; }
        h1 { color: #10b981; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #059669; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .result-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; word-break: break-all; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 1: Low — HTML Context Reflected XSS</h1>
        <p>This search endpoint reflects user input directly into the HTML response body with absolutely zero output encoding or sanitization.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$search = $_GET['search'];
// VULNERABLE: Direct echo without htmlspecialchars()
echo "&lt;h2&gt;Results for: " . $search . "&lt;/h2&gt;";
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Trigger an alert box displaying your session cookie (<code>document.cookie</code>). Test basic script tags: <code>&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code> or image tags: <code>&lt;img src=x onerror=alert(1)&gt;</code>.</p>

        <form action="level1.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="search">Search Query:</label><br>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" placeholder="e.g., hacker"><br>
            <input type="submit" value="Search Database" class="btn">
        </form>

        <?php if (!empty($search)): ?>
            <div class="result-box">
                <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Server Rendered Output:</p>
                <!-- VULNERABLE: Direct reflection into HTML context -->
                <h2>Search Results for: <?php echo $search; ?></h2>
            </div>
        <?php endif; ?>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
