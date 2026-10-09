<?php
require_once 'session_helper.php';
// The PHP backend is completely clean and does not echo any input!
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 6: High | XSS Lab</title>
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
        .dom-box { background: #0f172a; padding: 20px; border-radius: 6px; border: 1px dashed #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 6: High — DOM-Based XSS (innerHTML)</h1>
        <p>The PHP server is completely safe. The flaw exists 100% in the client-side JavaScript code. The application reads user input from the URL fragment (<code>window.location.hash</code>) and passes it directly into the dangerous <code>innerHTML</code> sink.</p>
        
        <h3>Source Code Snippet (Client-Side JS):</h3>
        <pre><code>// VULNERABLE: Direct assignment of unvalidated hash into innerHTML
const hash = window.location.hash.substring(1);
if (hash) {
    document.getElementById('display').innerHTML = decodeURIComponent(hash);
}</code></pre>

        <h3>Your Goal:</h3>
        <p>Note: HTML5 specifies that <code>&lt;script&gt;</code> tags inserted via <code>innerHTML</code> will NOT execute. You must use an HTML element with an inline error/load event handler: <br>
        <code>#&lt;img src=x onerror=alert(document.cookie)&gt;</code> or <code>#&lt;svg onload=alert(1)&gt;</code>.</p>

        <form onsubmit="testDOMHash(event)" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="hash_input">Test Fragment Payload (Hash):</label><br>
            <input type="text" id="hash_input" value="<img src=x onerror=alert(document.cookie)>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Update Hash & Trigger DOM" class="btn">
        </form>

        <div class="dom-box">
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">DOM Sink Output Container:</p>
            <div id="display" style="color: #38bdf8; font-weight: bold;">(Waiting for URL fragment...)</div>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>

    <script>
        function renderHash() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                // VULNERABLE: innerHTML sink
                document.getElementById('display').innerHTML = decodeURIComponent(hash);
            }
        }

        function testDOMHash(e) {
            e.preventDefault();
            const val = document.getElementById('hash_input').value;
            window.location.hash = val;
            renderHash();
        }

        window.addEventListener('DOMContentLoaded', renderHash);
        window.addEventListener('hashchange', renderHash);
    </script>
</body>
</html>
