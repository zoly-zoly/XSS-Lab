# XSS Deep-Dive: Contextual Parsing & Defensive Architecture

Welcome to the official Wiki for the **XSS Masterclass Lab Suite**. This wiki is designed to be an advanced architectural reference guide, shifting the focus from "finding an alert box" to **how browser tokenizers and parsing engines process untrusted data**, context-aware encoding rules, and enterprise Content Security Policy (CSP) engineering.

---

## 🔬 Part 1: How Browsers Parse Code (The Context Principle)

Cross-Site Scripting (XSS) occurs when an application injects untrusted data into an execution context without proper encoding for that **specific context**. The browser parses HTML sequentially through multiple distinct tokenizers:

```
[ Inbound Raw HTTP Response ]
            │
            ├─► 1. HTML Tokenizer (Parses Tags: <div>, <input>, <script>)
            │
            ├─► 2. HTML Attribute Parser (Parses: value="...", href="...")
            │
            ├─► 3. JavaScript Parser (Inside <script> blocks or Event Handlers)
            │
            └─► 4. CSS / URL Tokenizer (Parses: url(...), javascript:...)
```

### Contextual Vulnerability Matrix

| Insertion Context | Vulnerable Example | Why Standard Encoding Fails | Required Defense |
| :--- | :--- | :--- | :--- |
| **HTML Body** | `<div>USER_INPUT</div>` | Raw `<` and `>` create new DOM elements. | HTML Entity Encode (`&lt;`, `&gt;`, `&amp;`) |
| **HTML Attribute** | `<input value="USER_INPUT">` | Quotes (`"` or `'`) close the attribute, allowing event handlers (`onfocus`). | Attribute-safe Quotes & Entity Encoding |
| **JavaScript String** | `<script>var x = 'USER_INPUT';</script>` | HTML encoding doesn't stop JS string breakout (`'; alert(1); //`). | JavaScript Unicode Hex Encoding (`\x27`, `\x22`) |
| **URL Parameter** | `<a href="USER_INPUT">` | Even if encoded, `javascript:` pseudo-protocol executes script on click. | Strict Protocol Whitelisting (`http://`, `https://`) |
| **DOM Sink** | `element.innerHTML = USER_INPUT` | Executes purely in client-side JS; server-side sanitizers cannot detect it. | Use `element.textContent` or DOMPurify |

---

## 🎯 Part 2: Advanced Attack Archetypes

### 1. Template Literal Expression Evaluation (Level 9)
In modern JavaScript (ES6+), strings enclosed in backticks (`` `...` ``) support expression evaluation through `${expression}`.
*   **The Flaw:** Developers often believe escaping single (`'`) and double (`"`) quotes prevents script breakout inside JavaScript blocks.
*   **The Bypass:** If user input is reflected inside a backtick template literal, an attacker does not need to break out of string quotes! Injecting `${alert(document.cookie)}` causes the JavaScript engine to evaluate the expression directly within the string.

### 2. Content Security Policy (CSP) Evasion (Level 10)
CSP is a browser defense designed to restrict resource loading and script execution.
*   **Policy Directive:** `connect-src 'self'` prevents JavaScript from transmitting data via background requests (like `fetch()` or `XMLHttpRequest`) to external attacker endpoints.
*   **The Evasion:** While background API calls are blocked, top-level window navigation directives (like `window.location.href`) are governed by the separate `navigate-to` directive (which is rarely implemented). Attackers can exfiltrate sensitive data by redirecting the user's browser directly to the attacker receiver with the stolen session tokens in the query string.

---

## 🛡️ Part 3: Defensive Architecture (The Industry Standard)

To eliminate XSS across an enterprise application, implement a **Defense-in-Depth Pipeline**:

```
                  ┌────────────────────────────────────────┐
                  │          Inbound Untrusted Input       │
                  └───────────────────┬────────────────────┘
                                      │
                                      ▼
                        [ Context-Aware Encoding ]
                        (HTML, Attribute, JavaScript, URL)
                                      │
                                      ▼
                       [ Modern Framework Auto-Escaping ]
                        (React, Angular, Blade, Twig)
                                      │
                                      ▼
                        [ Client-Side DOMPurify (if HTML needed) ]
                                      │
                                      ▼
                        [ Strict Content Security Policy (CSP) ]
                        (script-src 'nonce-...'; object-src 'none')
                                      │
                                      ▼
                         [ Safe DOM Output Rendered ]
```

### 1. Context-Aware Output Encoding
Never use a generic function (like `htmlspecialchars()`) everywhere. Use the encoder designed for the active context:
*   **HTML Body:** `htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`
*   **JavaScript Variables:** Use `json_encode($str, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)` to safely serialize data into script blocks.
*   **URLs:** Validate that the protocol begins with `http://` or `https://` before rendering into `href` or `src` attributes.

### 2. Modern Content Security Policy (CSP)
Implement a modern, nonce-based CSP header:
```http
Content-Security-Policy: default-src 'self'; script-src 'nonce-rAnd0m123' 'strict-dynamic'; object-src 'none'; base-uri 'none';
```

---
*Documentation curated by **Zoly** for the advancement of secure application architecture.*
