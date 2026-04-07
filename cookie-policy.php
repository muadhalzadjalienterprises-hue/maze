<?php
// cookie-policy.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cookie Policy | Muadh Al Zadjali Engineering &amp; Cont. Enterprises</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

 <!-- Favicon -->
  <link rel="icon" type="image/png" href="favicon.png">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      height: 100%;
      scroll-behavior: smooth;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      background: #0b3c5d;
      color: #222;
    }
    body { overflow-x: hidden; }

    .bg-layer {
      position: fixed;
      inset: 0;
      z-index: -1;
      background: #0b3c5d;
    }

    header.site-header {
      background: rgba(255,255,255,0.96);
      border-bottom: 1px solid #e0e0e0;
      padding: 20px 0;
      width: 100%;
    }
    .header-inner {
      max-width: 1150px;
      margin: 0 auto;
      padding: 0 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo-img {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    .logo {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .logo-title {
      font-weight: 700;
      font-size: 16px;
      color: #0b3c5d;
    }
    .logo-sub {
      font-size: 11px;
      color: #666;
    }

    /* Desktop nav */
    .main-nav {
      display: flex;
      gap: 14px;
      font-size: 13px;
      align-items: center;
    }
    .main-nav a {
      text-decoration: none;
      color: #333;
      position: relative;
      padding: 3px 0;
    }
    .main-nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -2px;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #f9a825, #ff5252);
      transition: width 0.25s ease;
    }
    .main-nav a:hover::after,
    .main-nav a.active::after {
      width: 100%;
    }

    /* Mobile back button */
    .mobile-back {
      display: none;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      color: #0b3c5d;
      font-size: 13px;
      text-decoration: none;
    }
    .mobile-back-icon {
      width: 22px;
      height: 22px;
      border-radius: 999px;
      border: 1px solid #0b3c5d;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
    }
    .mobile-back-icon span {
      display: block;
      font-size: 14px;
      line-height: 1;
    }
    .mobile-back-text {
      font-weight: 500;
    }

    @media (max-width: 768px) {
      .main-nav { display: none; }
      .mobile-back { display: inline-flex; }
    }

    .page-wrap {
      max-width: 1150px;
      margin: 32px auto 40px;
      padding: 0 18px;
    }
    @media (max-width: 900px) {
      .page-wrap {
        padding-left: max(18px, env(safe-area-inset-left, 0px));
        padding-right: max(18px, env(safe-area-inset-right, 0px));
      }
    }

    .policy-card {
      background: rgba(255,255,255,0.98);
      border-radius: 10px;
      padding: 22px 20px 26px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.25);
      color: #222;
    }
    .policy-badge {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: #f9a825;
      margin-bottom: 4px;
    }
    .policy-title {
      font-size: 22px;
      margin-bottom: 6px;
      color: #0b3c5d;
    }
    .policy-subtitle {
      font-size: 13px;
      color: #555;
      margin-bottom: 14px;
    }
    .policy-section-title {
      font-size: 15px;
      font-weight: 600;
      margin-top: 14px;
      margin-bottom: 4px;
      color: #0b3c5d;
    }
    .policy-text {
      font-size: 13px;
      line-height: 1.7;
      color: #333;
      margin-bottom: 6px;
    }
    .policy-list {
      font-size: 13px;
      line-height: 1.7;
      color: #333;
      margin-left: 18px;
      margin-bottom: 4px;
    }

    footer {
      margin-top: 24px;
      width: 100%;
      background: rgba(0,0,0,0.9);
      color: #eee;
      border-top: 1px solid rgba(255,255,255,0.1);
      padding: 22px 0 28px;
    }
    .footer-inner {
      max-width: 1150px;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      font-size: 12px;
    }
    .footer-main {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .footer-links a {
      color: #ffd54f;
      margin-left: 10px;
      text-decoration: none;
    }
    .footer-links a:hover { text-decoration: underline; }
    @media (max-width: 900px) {
      .footer-inner {
        padding-left: max(18px, env(safe-area-inset-left, 0px));
        padding-right: max(18px, env(safe-area-inset-right, 0px));
      }
      .footer-main {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

<div class="bg-layer"></div>

<header class="site-header">
  <div class="header-inner">
    <div class="header-left">
      <img src="maze.jpg" alt="Company Logo" class="logo-img">
      <div class="logo">
        <span class="logo-title">MUADH AL ZADJALI</span>
        <span class="logo-sub">ENGINEERING &amp; CONT. ENTERPRISES</span>
      </div>
    </div>

    <!-- Desktop nav -->
    <nav class="main-nav">
      <a href="index.php#sec-hero">Home</a>
      <a href="cookie-policy.php" class="active">Cookie Policy</a>
    </nav>

    <!-- Mobile back button -->
    <a href="index.php#sec-hero" class="mobile-back">
      <div class="mobile-back-icon"><span>←</span></div>
      <span class="mobile-back-text">Back</span>
    </a>
  </div>
</header>

<div class="page-wrap">
  <div class="policy-card">
    <div class="policy-badge">Legal</div>
    <h1 class="policy-title">Cookie Policy</h1>
    <p class="policy-subtitle">
      This Cookie Policy explains how Muadh Al Zadjali Engineering &amp; Cont. Enterprises uses cookies and similar technologies on this website, in line with applicable laws in the Sultanate of Oman. [web:333][web:339]
    </p>

    <h2 class="policy-section-title">1. What are cookies?</h2>
    <p class="policy-text">
      Cookies are small text files that are stored on your device when you visit a website. They help the site remember your actions and preferences over a period of time, and can also provide anonymous statistics about how the site is used. [web:339]
    </p>

    <h2 class="policy-section-title">2. Types of cookies we use</h2>
    <p class="policy-text">We may use the following categories of cookies on this website:</p>
    <ul class="policy-list">
      <li><strong>Strictly necessary cookies</strong> – required for the website to function properly, such as basic navigation and security features. These cannot be switched off in our systems.</li>
      <li><strong>Preference / functional cookies</strong> – used to remember your choices (for example, cookie banner decisions or language options) and provide a more personalised experience.</li>
      <li><strong>Performance / analytics cookies</strong> – used, when enabled, to help us understand how visitors interact with our site so that we can improve design, content, and performance. Data is typically aggregated and does not directly identify you. [web:339][web:336]</li>
    </ul>

    <h2 class="policy-section-title">3. Cookies used by government‑approved Oman company websites</h2>
    <p class="policy-text">
      As an Oman government‑approved engineering and construction company, we aim to keep our use of cookies proportionate and compliant with Omani Personal Data Protection Law, particularly when cookies involve processing personal data such as online identifiers or contact information. [web:335][web:338]
    </p>
    <p class="policy-text">
      Where a cookie allows us to identify you, we treat the associated data as personal data and apply the safeguards and rights described in our Privacy Policy. [web:333][web:335]
    </p>

    <h2 class="policy-section-title">4. Cookie consent and control</h2>
    <p class="policy-text">
      When you first visit our website, a cookie banner allows you to accept or reject non‑essential cookies. Essential cookies are always active because they are required to deliver the website and its core functions. [web:333][web:339]
    </p>
    <p class="policy-text">
      You can also manage or delete cookies through your browser settings. Most browsers allow you to block cookies entirely, delete existing cookies, or configure notifications before cookies are placed on your device. Please note that blocking some cookies may impact site functionality. [web:336][web:339]
    </p>

    <h2 class="policy-section-title">5. Third‑party cookies</h2>
    <p class="policy-text">
      Some cookies may be placed by third‑party services integrated into our site, such as analytics or embedded content providers. These third parties have their own privacy and cookie policies governing how they use information. [web:339]
    </p>
    <p class="policy-text">
      Where required, we will only enable such cookies after you have provided consent through the cookie banner or relevant settings.
    </p>

    <h2 class="policy-section-title">6. Relationship with our Privacy Policy</h2>
    <p class="policy-text">
      For more information on how we process personal data, your rights, and our responsibilities under Omani Personal Data Protection Law, please refer to our Privacy Policy. [web:333][web:338]
    </p>

    <h2 class="policy-section-title">7. Changes to this Cookie Policy</h2>
    <p class="policy-text">
      We may update this Cookie Policy from time to time to reflect changes in technology, legal requirements, or our use of cookies. Any updates will be published on this page, with an updated effective date. Continued use of the website after such updates constitutes your acceptance of the revised policy. [web:338]
    </p>
  </div>
</div>

<footer>
  <div class="footer-inner">
    <div class="footer-main">
      <div>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises.</div>
      <div class="footer-links">
        <a href="privacy-policy.php">Privacy Policy</a>
      </div>
    </div>
    <div>By continuing to use this site, you acknowledge our use of cookies and our commitment to quality, safety, and client satisfaction.</div>
  </div>
</footer>

</body>
</html>
