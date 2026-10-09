<?php
require_once 'session_helper.php';

$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

// VULNERABLE: The developer uses htmlspecialchars() with default settings (ENT_COMPAT).
// ENT_COMPAT encodes double quotes (") and HTML tags (< and >), but leaves single quotes (') UNTOUCHED!
// The input is reflected inside a JavaScript string bounded by single quotes.
$safe_html = htmlspecialchars($tag, ENT_COMPAT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 3: Medium | XSS Lab</title>
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
        .result-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 3: Medium — JavaScript String Context</h1>
        <p>The developer uses PHP's classic <code>htmlspecialchars()</code> function. However, they passed default flags (<code>ENT_COMPAT</code>), which leaves single quotes (<code>'</code>) completely unescaped.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$tag = htmlspecialchars($_GET['tag'], ENT_COMPAT); // Only encodes " and &lt; &gt;
?&gt;
&lt;script&gt;
    var userCategory = '&lt;?php echo $tag; ?&gt;';
&lt;/script&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Break out of the single-quoted JavaScript string literal: <br>
        <code>'; alert(document.cookie); //</code> or <code>'-alert(1)-'</code>.</p>

        <form action="level3.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="tag">Category Tag:</label><br>
            <input type="text" id="tag" name="tag" value="<?php echo htmlspecialchars($tag); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" placeholder="e.g., '; alert(1); //"><br>
            <input type="submit" value="Set Category" class="btn">
        </form>

        <?php if (!empty($tag)): ?>
            <div class="result-box">
                <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Generated Client Script Block:</p>
                <code style="color: #38bdf8;">var userCategory = '<?php echo $safe_html; ?>';</code>
            </div>
            <!-- VULNERABLE: Execution inside script tag -->
            <script>
                var userCategory = '<?php echo $safe_html; ?>';
            </script>
        <?php endif; ?>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
