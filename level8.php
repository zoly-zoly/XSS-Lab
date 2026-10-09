<?php
require_once 'session_helper.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $raw_comment = $_POST['comment'];
    $author = !empty($_POST['author']) ? $_POST['author'] : 'Anonymous';

    // VULNERABLE Blacklist Filter: Strips <script> tags and blocks 'onerror' / 'onload'
    $sanitized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $raw_comment);
    $sanitized = preg_replace('/(onerror|onload)/i', 'blocked', $sanitized);

    // Store persistently in the session forum database
    $_SESSION['xss_stored_posts'][] = [
        'id' => count($_SESSION['xss_stored_posts']) + 1,
        'author' => htmlspecialchars($author),
        'comment' => $sanitized // Stored unsafely!
    ];
    $msg = "Success: Comment posted to the persistent forum board!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 8: High | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #ef4444; }
        h1 { color: #ef4444; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #ef4444; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #dc2626; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .comment-card { background: #0f172a; border-radius: 8px; padding: 15px; border: 1px dashed #334155; margin-top: 15px; }
        .author-tag { color: #38bdf8; font-weight: bold; font-size: 0.9rem; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 8: High — Stored / Persistent XSS</h1>
        <p>This is a persistent comment bulletin board. Comments are saved on the server and rendered for every subsequent visitor. The backend employs a filter blacklist that strips <code>&lt;script&gt;</code> tags and replaces <code>onerror</code> and <code>onload</code> with "blocked".</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$sanitized = preg_replace('/&lt;script\b[^>]*&gt;(.*?)&lt;\/script&gt;/is', '', $_POST['comment']);
// Weak blacklist: only blocks 'onerror' and 'onload'
$sanitized = preg_replace('/(onerror|onload)/i', 'blocked', $sanitized);

// Stores permanently in the database and renders raw:
echo $post['comment'];
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Bypass the blacklist filter using modern HTML5 auto-executing event handlers. <br>
        Examples: <br>
        <code>&lt;input autofocus onfocus=alert(document.cookie)&gt;</code> <br>
        <code>&lt;details ontoggle=alert(document.cookie) open&gt;</code> <br>
        <code>&lt;select autofocus onfocus=alert(document.cookie)&gt;&lt;/select&gt;</code></p>

        <form action="level8.php" method="POST" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="author">Author Name:</label><br>
            <input type="text" id="author" name="author" value="Hacker" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br><br>
            
            <label for="comment">Post Comment:</label><br>
            <textarea id="comment" name="comment" rows="3" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required>&lt;details ontoggle=alert(document.cookie) open&gt;</textarea><br>
            
            <input type="submit" value="Submit Persistent Post" class="btn">
        </form>

        <h3 style="margin-top: 30px; border-top: 1px solid #334155; padding-top: 20px;">Public Bulletin Feed:</h3>
        <?php foreach ($_SESSION['xss_stored_posts'] as $post): ?>
            <div class="comment-card">
                <div class="author-tag">👤 <?php echo $post['author']; ?></div>
                <div style="color: #cbd5e1;"><?php echo $post['comment']; // Raw HTML execution ?></div>
            </div>
        <?php endforeach; ?>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
