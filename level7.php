<?php
require_once 'session_helper.php';
$theme = isset($_GET['theme']) ? $_GET['theme'] : 'dark';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 7: High | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 600px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #ef4444; }
        h1 { color: #ef4444; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #ef4444; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #dc2626; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .status-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 7: High — DOM XSS in Dynamic Execution (eval)</h1>
        <p>This level features a client-side routing script that dynamically sets the UI theme by passing input directly into the dangerous JavaScript execution sink <code>eval()</code>.</p>
        
        <h3>Source Code Snippet (Client-Side JS):</h3>
        <pre><code>// VULNERABLE: Direct string interpolation into eval() execution sink
const params = new URLSearchParams(window.location.search);
const theme = params.get('theme') || 'dark';

eval("applyTheme('" + theme + "')");</code></pre>

        <h3>Your Goal:</h3>
        <p>Break out of the single-quoted string literal within the <code>eval()</code> statement. Use a payload such as: <br>
        <code>';alert(document.cookie);//</code> or <code>'-alert(document.cookie)-'</code>.</p>

        <form action="level7.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="theme">Theme Parameter (eval sink):</label><br>
            <input type="text" id="theme" name="theme" value="<?php echo htmlspecialchars($theme); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Apply Theme via eval()" class="btn">
        </form>

        <div class="status-box">
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Active Theme Status:</p>
            <div id="theme-status" style="color: #38bdf8; font-weight: bold;">Applying theme...</div>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>

    <script>
        function applyTheme(name) {
            document.getElementById('theme-status').innerText = 'Theme successfully applied: ' + name;
        }

        // Execution sink
        const urlParams = new URLSearchParams(window.location.search);
        const themeVal = urlParams.get('theme');
        if (themeVal) {
            try {
                // VULNERABLE: eval() execution sink
                eval("applyTheme('" + themeVal + "')");
            } catch (e) {
                document.getElementById('theme-status').innerText = 'Evaluation Error: ' + e.message;
            }
        } else {
            applyTheme('dark');
        }
    </script>
</body>
</html>
