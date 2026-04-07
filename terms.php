<?php
// terms.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Terms of Use | Muadh Al Zadjali Engineering &amp; Cont. Enterprises</title>
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
      <a href="privacy-policy.php">Privacy Policy</a>
      <a href="terms.php" class="active">Terms of Use</a>
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
    <h1 class="policy-title">Terms of Use</h1>
    <p class="policy-subtitle">
      These Terms of Use govern your access to and use of the website of Muadh Al Zadjali Engineering &amp; Cont. Enterprises and any content, features, or services made available through it. By using this site, you accept these Terms of Use. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">1. Acceptance of terms</h2>
    <p class="policy-text">
      By accessing or using this website, you confirm that you are at least 18 years old, or that you are using the site under the supervision of a parent or legal guardian, and that you agree to be bound by these Terms of Use and any additional terms and policies referenced here, including our Privacy Policy and Cookie Policy. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">2. Website content and information only</h2>
    <p class="policy-text">
      The content on this website is provided for general information and illustrative purposes related to our engineering and construction services in Oman. It does not constitute professional engineering, legal, financial, or other advice and should not be relied upon as a substitute for appropriate technical or professional consultation. [web:278][web:280]
    </p>
    <p class="policy-text">
      While we aim to keep information accurate and up to date, we do not guarantee that the content, materials, or information on this site are complete, error‑free, or suitable for any particular purpose, and they may be updated or removed at any time without prior notice. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">3. Use of the website</h2>
    <p class="policy-text">
      You agree to use this website only for lawful purposes and in a manner that does not infringe the rights of, restrict, or inhibit the use and enjoyment of the site by any other person. Prohibited behaviour includes transmitting or distributing any harmful, unlawful, defamatory, or otherwise objectionable material. [web:278]
    </p>
    <p class="policy-text">
      You are responsible for ensuring that any information you submit through our Contact or Ask a Question forms is accurate, does not violate any third‑party rights, and complies with all applicable laws in the Sultanate of Oman. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">4. Intellectual property</h2>
    <p class="policy-text">
      Unless otherwise stated, all content on this website, including text, images, graphics, layout, and design, is owned by or licensed to Muadh Al Zadjali Engineering &amp; Cont. Enterprises and is protected by applicable intellectual property laws. All rights are reserved. [web:278][web:280]
    </p>
    <p class="policy-text">
      You may view, print, or download content from this site for your personal, non‑commercial use only. You may not reproduce, distribute, modify, create derivative works of, publicly display, or otherwise use any part of the site or its content for commercial purposes without our prior written consent. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">5. User submissions</h2>
    <p class="policy-text">
      When you submit enquiries, questions, or other information through this website, you grant us a non‑exclusive, royalty‑free license to use, store, and process that information for the purposes of responding to you, improving our services, and fulfilling our legal and contractual obligations. [web:280]
    </p>
    <p class="policy-text">
      You remain responsible for the legality, accuracy, and appropriateness of any content you submit and confirm that you have the right to provide such information and that it does not violate any third‑party rights. [web:280]
    </p>

    <h2 class="policy-section-title">6. No warranties</h2>
    <p class="policy-text">
      This website and its content are provided on an “as is” and “as available” basis, without any warranties or representations of any kind, whether express or implied, including but not limited to warranties of accuracy, fitness for a particular purpose, or non‑infringement, to the fullest extent permitted by applicable law. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">7. Limitation of liability</h2>
    <p class="policy-text">
      To the fullest extent permitted by law, Muadh Al Zadjali Engineering &amp; Cont. Enterprises and its owners, employees, and representatives shall not be liable for any direct, indirect, incidental, consequential, or special losses or damages arising out of or in connection with your access to or use of this website, including reliance on any content, interruption of service, or data loss. [web:278][web:280]
    </p>
    <p class="policy-text">
      Your use of any information or materials on this site is entirely at your own risk, and it is your responsibility to ensure that any services, information, or materials meet your specific requirements and comply with relevant regulations and technical standards. [web:278]
    </p>

    <h2 class="policy-section-title">8. Links to third‑party sites</h2>
    <p class="policy-text">
      This website may contain links to third‑party websites or resources. These links are provided for convenience only and do not signify endorsement of the content, products, or services on those sites. We do not control and are not responsible for any third‑party content, security practices, or privacy policies. [web:278]
    </p>

    <h2 class="policy-section-title">9. Compliance with Omani law</h2>
    <p class="policy-text">
      This website is operated from the Sultanate of Oman and is primarily intended for users located in Oman. Your use of the site and these Terms of Use are governed by the laws of the Sultanate of Oman, without regard to conflict of law principles. [web:275][web:279]
    </p>
    <p class="policy-text">
      You agree that any disputes arising out of or in connection with your use of this website shall be subject to the exclusive jurisdiction of the competent courts in the Sultanate of Oman, unless another forum is required by mandatory law. [web:279]
    </p>

    <h2 class="policy-section-title">10. Privacy and cookies</h2>
    <p class="policy-text">
      Our handling of personal data collected through this website is described in our Privacy Policy, which should be read together with these Terms of Use. Information about how we use cookies and similar technologies is provided in our Cookie Policy and any cookie consent banner displayed on the site. [web:277][web:281]
    </p>

    <h2 class="policy-section-title">11. Changes to these Terms</h2>
    <p class="policy-text">
      We may update or revise these Terms of Use from time to time, for example to reflect changes in applicable law, our services, or our internal policies. Any changes will be posted on this page and will apply from the date of publication. Your continued use of the website after such changes are posted constitutes your acceptance of the updated Terms. [web:278][web:280]
    </p>

    <h2 class="policy-section-title">12. Contact us</h2>
    <p class="policy-text">
      If you have any questions about these Terms of Use or about using this website, you can contact us using the details provided on our Contact page. We will review and respond to queries in line with our internal procedures and applicable Omani regulations. [web:279][web:281]
    </p>
  </div>
</div>

<footer>
  <div class="footer-inner">
    <div class="footer-main">
      <div>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises.</div>
      <div class="footer-links">
        <a href="privacy-policy.php">Privacy Policy</a>
        <a href="cookie-policy.php">Cookie Policy</a>
      </div>
    </div>
    <div>
      By continuing to use this site, you acknowledge these Terms of Use and our commitment to quality, safety, and client satisfaction.
    </div>
  </div>
</footer>

</body>
</html>
