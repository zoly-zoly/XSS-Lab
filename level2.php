<?php
require_once 'session_helper.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

// VULNERABLE: The developer strips angle brackets (< and >) to prevent <script> tags.
// However, the input is reflected inside an HTML attribute value enclosed by double quotes (").
// They forgot to escape quotes or use htmlspecialchars($query, ENT_QUOTES)!
$filtered_query = str_replace(['<', '>'], '', $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 2: Low | XSS Lab</title>
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
        .result-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 2: Low — HTML Attribute Breakout</h1>
        <p>The developer realized that raw reflections allow script tags and now strips all angle brackets (<code>&lt;</code> and <code>&gt;</code>). However, your input reflects directly inside an <code>input</code> attribute.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$filtered = str_replace(['&lt;', '&gt;'], '', $_GET['query']);
// VULNERABLE: Reflected inside double quotes without htmlspecialchars!
echo '&lt;input type="text" name="item" value="' . $filtered . '"&gt;';
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>You cannot use <code>&lt;script&gt;</code> because <code>&lt;</code> is stripped. Instead, **break out of the double-quote attribute context** and inject an HTML event handler: <br>
        <code>" onfocus="alert(document.cookie)" autofocus="</code> or <code>" onmouseover="alert(1)</code>.</p>

        <form action="level2.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="query">Search Item:</label><br>
            <input type="text" id="query" name="query" value="<?php echo htmlspecialchars($query); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" placeholder='e.g., " onfocus="alert(1)" autofocus="'><br>
            <input type="submit" value="Search" class="btn">
        </form>

        <?php if (!empty($query)): ?>
            <div class="result-box">
                <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Server Rendered Input Element:</p>
                <!-- VULNERABLE: Reflected into attribute without quote escaping -->
                <input type="text" name="item" value="<?php echo $filtered_query; ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #38bdf8; background: #1e293b; color: white;">
            </div>
        <?php endif; ?>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
