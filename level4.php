<?php
require_once 'session_helper.php';

$comment = isset($_GET['comment']) ? $_GET['comment'] : '';

// VULNERABLE: Naive WAF blacklist filter.
// The developer strips <script> and </script> tags using a single pass of str_ireplace().
// They completely ignore alternative tags (<img>, <svg>, <body>, <iframe>) and recursive nesting!
$filtered_comment = str_ireplace(['<script>', '</script>'], '', $comment);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 4: Medium | XSS Lab</title>
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
        <h1>Level 4: Medium — WAF Tag Blacklist Evasion</h1>
        <p>The developer deployed a custom regex WAF filter that detects and strips <code>&lt;script&gt;</code> tags. However, blacklists are fundamentally flawed.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Blacklist only checks for script tags
$filtered = str_ireplace(['&lt;script&gt;', '&lt;/script&gt;'], '', $_GET['comment']);
echo "&lt;div&gt;" . $filtered . "&lt;/div&gt;";
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Bypass the blacklist filter using either:</p>
        <ol>
            <li><strong>Alternative HTML5 vector:</strong> <code>&lt;img src=x onerror=alert(document.cookie)&gt;</code> or <code>&lt;svg onload=alert(1)&gt;</code>.</li>
            <li><strong>Recursive tag nesting:</strong> <code>&lt;scr&lt;script&gt;ipt&gt;alert(1)&lt;/scr&lt;/script&gt;ipt&gt;</code>. When the inner tag is stripped, the outer fragments collapse to form a valid tag!</li>
        </ol>

        <form action="level4.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="comment">Preview Feedback:</label><br>
            <input type="text" id="comment" name="comment" value="<?php echo htmlspecialchars($comment); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" placeholder="e.g., <img src=x onerror=alert(1)>"><br>
            <input type="submit" value="Preview Comment" class="btn">
        </form>

        <?php if (!empty($comment)): ?>
            <div class="result-box">
                <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Comment Output:</p>
                <!-- VULNERABLE: Output rendered after naive blacklist -->
                <div><?php echo $filtered_comment; ?></div>
            </div>
        <?php endif; ?>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
