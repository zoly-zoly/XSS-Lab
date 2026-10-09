<?php
require_once 'session_helper.php';
$name = isset($_GET['name']) ? $_GET['name'] : 'Guest';

// Developer sanitizes single and double quotes, assuming that prevents JavaScript execution!
$sanitized_name = addslashes($name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 9: Critical | XSS Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 600px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #8b5cf6; }
        h1 { color: #8b5cf6; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #8b5cf6; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #7c3aed; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .status-box { background: #0f172a; padding: 15px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 9: Critical — Template Literal Injection</h1>
        <p>In modern web applications, developers frequently use ES6 template literals (backticks <code>`...`</code>). Here, the server escapes single and double quotes using <code>addslashes()</code>. The developer thought this completely prevented XSS.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;script&gt;
// Quotes are escaped via addslashes(), but reflected inside template literals!
const greeting = `Welcome back, &lt;?php echo addslashes($_GET['name']); ?&gt;!`;
document.getElementById('welcome-msg').innerText = greeting;
&lt;/script&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>You don't need single quotes or double quotes! Exploit ES6 template interpolation syntax (<code>${...}</code>) to execute arbitrary JavaScript within the backtick string literal. <br>
        Payload: <code>${alert(document.cookie)}</code></p>

        <form action="level9.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="name">Name Parameter (Template String):</label><br>
            <input type="text" id="name" name="name" value="${alert(document.cookie)}" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Render Greeting" class="btn">
        </form>

        <div class="status-box">
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Client Greeting:</p>
            <div id="welcome-msg" style="color: #38bdf8; font-weight: bold;">Rendering...</div>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>

    <script>
        // VULNERABLE: Direct reflection inside backticks
        const greeting = `Welcome back, <?php echo $sanitized_name; ?>!`;
        document.getElementById('welcome-msg').innerText = greeting;
    </script>
</body>
</html>
