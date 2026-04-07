<?php
// privacy-policy.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Privacy Policy | Muadh Al Zadjali Engineering &amp; Cont. Enterprises</title>
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

    /* Mobile back button (replaces menu) */
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
      <a href="privacy-policy.php" class="active">Privacy Policy</a>
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
    <h1 class="policy-title">Privacy Policy</h1>
    <p class="policy-subtitle">
      This Privacy Policy explains how Muadh Al Zadjali Engineering &amp; Cont. Enterprises collects, uses, and protects personal data in accordance with the Personal Data Protection Law of the Sultanate of Oman (Royal Decree 6/2022). [web:335][web:337]
    </p>

    <h2 class="policy-section-title">1. Data controller details</h2>
    <p class="policy-text">
      Muadh Al Zadjali Engineering &amp; Cont. Enterprises is an Oman government‑approved engineering and construction company and acts as the data controller for personal data collected through this website and our related communication channels. [web:335][web:338]
    </p>
    <p class="policy-text">
      Registered location: Ruwi, Muscat, Sultanate of Oman (full address and CR/registration numbers may be added here).<br>
      Contact email: info@example.com<br>
      Telephone: +968‑0000‑0000
    </p>

    <h2 class="policy-section-title">2. Personal data we collect</h2>
    <p class="policy-text">Depending on how you interact with us, we may collect:</p>
    <ul class="policy-list">
      <li>Contact details such as name, email address, phone number, and company name when you use our Contact or Ask a Question forms.</li>
      <li>Project‑related information you voluntarily provide when requesting proposals, estimates, or meetings.</li>
      <li>Technical data such as IP address, browser type, device information, and basic usage data collected via cookies and similar technologies.</li>
    </ul>

    <h2 class="policy-section-title">3. Purposes and legal basis for processing</h2>
    <p class="policy-text">
      We process personal data only for specific, lawful, and declared purposes, and primarily on the basis of your explicit consent, performance of a contract, or to comply with applicable Omani laws. [web:333][web:335]
    </p>
    <ul class="policy-list">
      <li>Responding to enquiries, quote requests, and project discussions.</li>
      <li>Preparing, performing, and managing engineering and construction contracts.</li>
      <li>Improving our website, services, and user experience.</li>
      <li>Fulfilling legal or regulatory obligations in the Sultanate of Oman.</li>
    </ul>

    <h2 class="policy-section-title">4. Cookies and similar technologies</h2>
    <p class="policy-text">
      Our website uses essential and, where allowed, non‑essential cookies to operate the site, remember your preferences, and understand how visitors use our pages. Details are provided in our separate Cookie Policy, and consent for non‑essential cookies is obtained through the cookie banner. [web:333][web:339]
    </p>

    <h2 class="policy-section-title">5. Data sharing and international transfers</h2>
    <p class="policy-text">
      We do not sell your personal data. We may share limited personal data with trusted service providers (for example, hosting, email, or analytics providers) strictly for operating this website and delivering our services, under appropriate confidentiality and data‑protection commitments. [web:338][web:335]
    </p>
    <p class="policy-text">
      Where data is transferred outside the Sultanate of Oman, we take reasonable steps to ensure that the recipient provides a level of protection consistent with Omani Personal Data Protection Law and other applicable regulations. [web:335]
    </p>

    <h2 class="policy-section-title">6. Data retention</h2>
    <p class="policy-text">
      We retain personal data only for as long as necessary to fulfil the purposes outlined in this Privacy Policy, to meet contractual and legal obligations, or as required by applicable regulations in Oman. [web:335][web:338]
    </p>

    <h2 class="policy-section-title">7. Your rights under Omani law</h2>
    <p class="policy-text">
      In line with Omani Personal Data Protection Law, you may have the right to access, correct, update, or request deletion of your personal data, and to withdraw consent where processing is based on consent. [web:337][web:340]
    </p>
    <p class="policy-text">
      To exercise these rights or raise questions about how your data is handled, you can contact us using the contact details above. Requests will be reviewed and responded to in accordance with applicable Omani regulations. [web:337][web:338]
    </p>

    <h2 class="policy-section-title">8. Security measures</h2>
    <p class="policy-text">
      We implement reasonable technical and organisational measures to protect personal data against unauthorised access, loss, misuse, or alteration, taking into account the nature of the data and the risks associated with processing. [web:335][web:338]
    </p>

    <h2 class="policy-section-title">9. Updates to this Privacy Policy</h2>
    <p class="policy-text">
      We may update this Privacy Policy from time to time to reflect changes in legal requirements or our services. Any material changes will be indicated by updating the effective date at the top of this page. Continued use of the website after such changes constitutes acceptance of the updated policy. [web:338]
    </p>
  </div>
</div>

<footer>
  <div class="footer-inner">
    <div class="footer-main">
      <div>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises.</div>
      <div class="footer-links">
        <a href="cookie-policy.php">Cookie Policy</a>
      </div>
    </div>
    <div>By continuing to use this site, you acknowledge our use of cookies and our commitment to quality, safety, and client satisfaction.</div>
  </div>
</footer>

</body>
</html>
