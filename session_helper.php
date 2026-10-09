<?php
// MOCK SESSION & STORE HELPER FOR LOCAL XSS LAB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up mock secret session cookie for token exfiltration exercises
if (!isset($_COOKIE['session_token'])) {
    setcookie('session_token', 'SEC-KEY-99a8b1-FLAG-ADMIN-COOKIE', time() + 3600, '/', '', false, false);
    $_COOKIE['session_token'] = 'SEC-KEY-99a8b1-FLAG-ADMIN-COOKIE';
}

// Initial stored database for persistent/stored XSS (Level 9 & 10)
if (!isset($_SESSION['xss_stored_posts'])) {
    $_SESSION['xss_stored_posts'] = [
        [
            'id' => 1,
            'author' => 'System Admin',
            'comment' => 'Welcome to the public community bulletin board. Please keep discussions professional.'
        ],
        [
            'id' => 2,
            'author' => 'Alice',
            'comment' => 'Testing the forum feed. Everything looks great so far!'
        ]
    ];
}

// Reset helper
if (isset($_GET['reset_session'])) {
    unset($_SESSION['xss_stored_posts']);
    header("Location: index.php");
    exit();
}

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$lab_root = $base_url . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>