<?php
// contracts.php – Contracts & Agreements (same theme & behavior style as services.php)

require __DIR__ . '/db.php';

// Load contracts from DB
$stmt = $pdo->query("SELECT * FROM contracts ORDER BY sort_order, id");
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Contracts | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction contracts Oman, engineering projects Oman">

<meta name="description" content="View major construction and engineering contracts handled by Muadh Al Zadjali Engineering & Cont. Enterprises in Oman.">

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    html, body {
      height: 100%;
      scroll-behavior: smooth;
    }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      color: #fff;
      background: #0b3c5d;
      overflow-x: hidden;
      position: relative;
    }

    .bg-layer {
      position: fixed;
      inset: 0;
      z-index: -3;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-color: #163b73;
      transition: background-image 0.6s ease, background-color 0.6s ease;
    }

    .slant-lines {
      position: fixed;
      inset: -200px;
      z-index: -2;
      pointer-events: none;
      overflow: hidden;
    }
    .slant-line {
      position: absolute;
      width: 4px;
      height: 260px;
      opacity: 0.6;
      transform: rotate(45deg);
      animation: slantMove 12s linear infinite;
    }
    .slant-line.red { background: rgba(220, 20, 60, 0.7); }
    .slant-line.yellow { background: rgba(255, 215, 0, 0.7); }
    .slant-line.white { background: rgba(255, 255, 255, 0.7); }

    @keyframes slantMove {
      0% { transform: translateY(0) rotate(45deg); }
      100% { transform: translateY(400px) rotate(45deg); }
    }

    .slant-lines[data-shift="1"] .slant-line {
      animation-duration: 10s;
    }

    /* Global entrance animation base */
    .reveal-on-load {
      opacity: 0;
      transform: translateY(20px) scale(0.98);
      animation: revealUp 0.7s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
    }
    .reveal-delay-1 { animation-delay: 0.1s; }
    .reveal-delay-2 { animation-delay: 0.2s; }
    .reveal-delay-3 { animation-delay: 0.3s; }
    .reveal-delay-4 { animation-delay: 0.4s; }
    .reveal-delay-5 { animation-delay: 0.5s; }
    .reveal-delay-6 { animation-delay: 0.6s; }

    @keyframes revealUp {
      0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
      60%  { opacity: 1; transform: translateY(-4px) scale(1.01); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Slight pop for clickable/media items */
    .pop-on-load {
      opacity: 0;
      transform: scale(0.9);
      animation: popIn 0.55s cubic-bezier(0.17, 0.67, 0.43, 1.25) forwards;
    }
    @keyframes popIn {
      0%   { opacity: 0; transform: scale(0.9); }
      55%  { opacity: 1; transform: scale(1.04); }
      100% { opacity: 1; transform: scale(1); }
    }

    /* Header */
    .site-header {
      background: rgba(255,255,255,0.96);
      border-bottom: 1px solid #e0e0e0;
      padding: 24px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      position: relative;
      z-index: 10;
      flex-direction: row;
    }

    .logo {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 10px;
    }
    .logo-img {
      width: 62px;
      height: 62px;
      object-fit: contain;
      transform-origin: center;
      animation: logoBounce 0.8s ease-out 0.25s forwards;
      opacity: 0;
    }
    @keyframes logoBounce {
      0%   { opacity: 0; transform: scale(0.5) rotate(-6deg); }
      60%  { opacity: 1; transform: scale(1.05) rotate(2deg); }
      100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }

    .logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
      text-align: left;
    }
    .logo-title {
      font-weight: 700;
      font-size: 17px;
      color: #830606;
    }
    .logo-sub {
      font-size: 11px;
      color: #666;
    }

    .main-nav {
      display: flex;
      gap: 16px;
      align-items: center;
      flex-direction: row;
      position: relative;
    }
    .main-nav a {
      text-decoration: none;
      color: #333;
      font-size: 14px;
      position: relative;
      padding: 4px 0;
    }
    .main-nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -2px;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #810404, #810404);
      transition: width 0.25s ease;
    }
    .main-nav a:hover::after,
    .main-nav a.active::after {
      width: 100%;
    }
    .main-nav a:hover,
    .main-nav a.active {
      color: #860408;
    }

    .nav-item-with-sub {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .nav-sub-toggle {
      border: none;
      background: transparent;
      cursor: pointer;
      padding: 0;
      margin: 0;
      font-size: 66px;
      line-height: 1;
      color: #333;
    }
    .nav-sub-toggle span {
      display: inline-block;
      transition: transform 0.2s ease;
    }
    .nav-sub-toggle.open span {
      transform: rotate(180deg);
    }

    .nav-arrow-blink {
  animation: navArrowBlink 1s infinite ease-in-out;
}

/* blink + scale animation */
@keyframes navArrowBlink {
  0% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  40% {
    opacity: 0.2;
    transform: translateY(1px) scale(1.25);
  }
  80% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

    .sub-menu {
      position: absolute;
      top: 26px;
      right: 0;
      background: rgba(255,255,255,0.98);
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      padding: 6px 0;
      min-width: 190px;
      display: none;
      z-index: 30;
    }
    .sub-menu.open {
      display: block;
      animation: dropDown 0.18s ease-out forwards;
      transform-origin: top right;
    }
    @keyframes dropDown {
      0%   { opacity: 0; transform: scale(0.95) translateY(-4px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .sub-menu a {
      display: block;
      padding: 6px 10px;
      font-size: 13px;
      color: #333;
      text-decoration: none;
      white-space: nowrap;
    }
    .sub-menu a:hover {
      background: rgba(0,0,0,0.06);
    }

    main {
      padding-top: 14px;
    }

    /* Contracts Hero */
    .contracts-hero {
      max-width: 1150px;
      margin: 32px auto 16px;
      padding: 22px 20px;
    }
    .contracts-hero .tag-label {
      display: inline-block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #ffd54f;
      margin-bottom: 6px;
    }
    .contracts-hero h1 {
      font-size: 24px;
      margin-bottom: 8px;
    }
    .contracts-hero p {
      font-size: 14px;
      max-width: 650px;
      color: #e0e0e0;
    }

    /* Contract Sections (zig-zag like services) */
    .contract-section {
      min-height: 60vh;
      max-width: 1150px;
      margin: 40px auto;
      display: grid;
      grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.3fr);
      gap: 24px;
      align-items: center;
      position: relative;
    }
    .contract-section[data-side="right"] .contract-content {
      order: 2;
      text-align: right;
    }
    .contract-section[data-side="right"] .contract-media {
      order: 1;
    }

    .contract-content h2 {
      font-size: 22px;
      margin-bottom: 8px;
    }

    .contract-content-text {
      max-width: 520px;
      max-height: 220px;
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 4px;
    }
    .contract-content-text p {
      font-size: 14px;
      line-height: 1.6;
      color: #f1f1f1;
      white-space: pre-wrap;
    }

    .contract-media {
      position: relative;
      width: 100%;
      overflow: hidden;
      border-radius: 10px;
    }

    /* Slider container (same logic as services page) */
    .contract-slider {
      position: relative;
      width: 86%;
      height: 260px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.45);
      margin: 0 auto;
    }
    .contract-slider-track {
      position: absolute;
      inset: 0;
      display: flex;
      transition: transform 0.5s ease;
    }
    .contract-slide {
      min-width: 100%;
      height: 100%;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
      cursor: pointer;
    }
    .contract-slide img,
    .contract-slide video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 10px;
    }

    .contract-slider-dots {
      position: absolute;
      left: 50%;
      bottom: 8px;
      transform: translateX(-50%);
      display: flex;
      gap: 6px;
      z-index: 6;
    }
    .contract-slider-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: rgba(255,255,255,0.5);
      cursor: pointer;
      transition: transform 0.2s ease, background 0.2s ease;
    }
    .contract-slider-dot.active {
      transform: scale(1.3);
      background: #ffd54f;
    }

    .media-watermark-inline {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      color: rgba(255,255,255,0.8);
      background: rgba(0,0,0,0.45);
      padding: 4px 8px;
      border-radius: 999px;
      pointer-events: none;
      z-index: 5;
    }
    .media-watermark-inline img {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      object-fit: contain;
    }

    /* Modal */
    .media-modal {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }
    .media-modal.open {
      display: flex;
      animation: fadeInModal 0.25s ease-out forwards;
    }
    @keyframes fadeInModal {
      0%   { opacity: 0; }
      100% { opacity: 1; }
    }

    .media-modal-inner {
      position: relative;
      max-width: 900px;
      width: 92%;
      max-height: 90vh;
      background: #000;
      border-radius: 10px;
      padding: 10px 10px 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.7);
      transform: translateY(12px) scale(0.96);
      animation: modalUp 0.22s ease-out forwards;
    }
    @keyframes modalUp {
      0%   { opacity: 0; transform: translateY(18px) scale(0.95); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .media-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 6px;
      color: #fff;
      font-size: 13px;
    }
    .media-modal-title {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .media-modal-logo {
      width: 26px;
      height: 26px;
      border-radius: 4px;
      object-fit: contain;
      background: #111;
    }
    .media-modal-close {
      cursor: pointer;
      font-size: 18px;
      padding: 2px 6px;
      transition: transform 0.15s ease;
    }
    .media-modal-close:hover {
      transform: scale(1.15);
    }
    .media-modal-body {
      position: relative;
    }
    .media-modal-body img,
    .media-modal-body video {
      width: 100%;
      max-height: 75vh;
      object-fit: contain;
      border-radius: 8px;
    }
    .media-watermark {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      color: rgba(255,255,255,0.8);
      background: rgba(0,0,0,0.45);
      padding: 4px 8px;
      border-radius: 999px;
      pointer-events: none;
    }
    .media-watermark img {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      object-fit: contain;
    }

    footer {
      margin-top: 40px;
      padding: 18px 24px 24px;
      background: rgba(0,0,0,0.9);
      color: #eee;
      border-top: 1px solid rgba(255,255,255,0.15);
      font-size: 13px;
    }
    .footer-inner {
      max-width: 1150px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
      text-align: center;
    }
    .footer-links {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }
    .footer-links a {
      color: #ffd54f;
      text-decoration: none;
      font-size: 12px;
    }
    .footer-links a:hover {
      text-decoration: underline;
    }
    .footer-copy {
      font-size: 12px;
      color: #ccc;
    }

    /* Scroll-to-top arrow */
    .scroll-arrow {
      position: fixed;
      right: 22px;
      bottom: 26px;
      width: 42px;
      height: 42px;
      border-radius: 999px;
      background: rgba(0,0,0,0.55);
      box-shadow: 0 4px 12px rgba(0,0,0,0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 40;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s ease, transform 0.25s ease, background 0.2s ease;
    }
    .scroll-arrow.visible {
      opacity: 1;
      pointer-events: auto;
      transform: translateY(0);
    }
    .scroll-arrow:hover {
      background: rgba(0,0,0,0.75);
    }
    .scroll-arrow span {
      display: inline-block;
      color: #ffd54f;
      font-size: 22px;
      line-height: 1;
      transform: translateY(-1px);
    }

    @media (max-width: 900px) {
      .contract-section {
        grid-template-columns: minmax(0, 1fr);
        margin: 32px 14px;
      }
      .contract-section[data-side="right"] .contract-content {
        order: 1;
        text-align: left;
      }
      .contract-section[data-side="right"] .contract-media {
        order: 2;
      }
      .contracts-hero {
        margin: 22px 14px 12px;
        padding: 18px 14px;
      }
      .sub-menu {
        right: auto;
        left: 0;
      }
      .contract-content-text {
        max-height: 260px;
      }
    }

    @media (max-width: 768px) {
      .site-header {
        padding: 16px 14px;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      .main-nav {
        flex-wrap: wrap;
        justify-content: flex-start;
      }
    }

    @media (max-width: 600px) {
      .scroll-arrow {
        right: 16px;
        bottom: 18px;
      }
    }

    /* FIXED CONTACT POPUP */
    .contact-popup-fixed {
      position: fixed;
      right: 18px;
      bottom: 80px;
      z-index: 150;
      background: rgba(0,0,0,0.78);
      color: #fff;
      border-radius: 14px;
      padding: 8px 12px 8px 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.6);
      border: 1px solid rgba(255,255,255,0.18);
      cursor: pointer;
      max-width: 260px;
      font-size: 13px;
    }
    .contact-popup-fixed.hidden {
      display: none;
    }
    .contact-popup-icon {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      background: #ffd54f;
      color: #0b3c5d;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
    }
    .contact-popup-text {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .contact-popup-title {
      font-weight: 600;
      font-size: 13px;
    }
    .contact-popup-details {
      font-size: 12px;
      color: #e0eef5;
    }
    .contact-popup-close {
      border: none;
      background: transparent;
      color: #fff;
      font-size: 14px;
      cursor: pointer;
      padding: 0 0 0 4px;
      line-height: 1;
      align-self: flex-start;
    }
    

    .question-popup-fixed {
      position: fixed;
      right: 18px;
      bottom: 160px;
      z-index: 150;
      width: 40px;
      height: 40px;
      border-radius: 999px;
      background: #ffd54f;
      color: #0b3c5d;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 18px rgba(0,0,0,0.6);
      cursor: pointer;
      font-size: 22px;
      border: 1px solid rgba(0,0,0,0.25);
    }
    .question-popup-fixed:hover {
      transform: translateY(-1px);
    }

    @media (max-width: 600px) {
      .contact-popup-fixed {
        right: 12px;
        left: 12px;
        bottom: 74px;
        max-width: none;
      }
      .question-popup-fixed {
        right: 16px;
        bottom: 130px;
      }
    }

  </style>
</head>
<body class="reveal-ready">

<div class="bg-layer" id="bgLayer"></div>
<div class="slant-lines" id="slantLines"></div>

<header class="site-header reveal-on-load reveal-delay-1">
  <div class="logo">
    <img src="maze.jpg" alt="Company Logo" class="logo-img">
    <div class="logo-text">
      <span class="logo-title">MUADH AL ZADJALI</span>
      <span class="logo-sub">ENGINEERING &amp; CONT. ENTERPRISES</span>
    </div>
  </div>

  <nav class="main-nav reveal-on-load reveal-delay-2">
    <a href="index.php">Home</a>
    <a href="services.php">Services</a>
    <a href="contracts.php" class="active">Contracts</a>
    <a href="about.php">About Us</a>

    <div class="nav-item-with-sub">
      <a href="insights.php">Insights</a>
      <button type="button" class="nav-sub-toggle" id="whoSubToggle" aria-label="More pages">
        <span class="nav-arrow-blink">▾</span>
      </button>
      <div class="sub-menu" id="whoSubMenu">
        <a href="industry.php">Industry Outlook</a>
        <a href="location.php">Location</a>
        <a href="contact.php">Contact</a>
        <a href="ask-question.php">Ask a Question</a>
      </div>
    </div>
  </nav>
</header>

<main>
  <!-- Hero (section 0) -->
  <section class="contracts-hero section-scroll reveal-on-load reveal-delay-4" data-index="0" id="top">
    <span class="tag-label">Contracts &amp; Agreements</span>
    <h1>Flexible contract options for every project.</h1>
    <p>Choose from multiple contract structures to match your project scope, risk profile, and budget requirements, from fixed price to design‑build partnerships.</p>
  </section>

  <?php
  $idx = 1;
  foreach ($contracts as $contract):
      $side = ($idx % 2 === 1) ? 'left' : 'right';

      // Build media items from media_json; fallback to image_path
      $mediaItems = [];
      if (!empty($contract['media_json'])) {
          $decoded = json_decode($contract['media_json'], true);
          if (is_array($decoded)) {
              foreach ($decoded as $m) {
                  if (empty($m['type']) || empty($m['src'])) continue;
                  $type = $m['type'] === 'video' ? 'video' : 'image';
                  $mediaItems[] = ['type' => $type, 'src' => $m['src']];
              }
          }
      } else {
          if (!empty($contract['image_path'])) {
              $parts = explode(',', $contract['image_path']);
              foreach ($parts as $p) {
                  $trimmed = trim($p);
                  if ($trimmed !== '') {
                      $mediaItems[] = ['type' => 'image', 'src' => $trimmed];
                  }
              }
          }
      }
  ?>
    <section
      class="contract-section section-scroll reveal-on-load reveal-delay-<?php echo ($idx % 3) + 4; ?>"
      data-index="<?php echo $idx; ?>"
      data-side="<?php echo $side; ?>"
      id="<?php echo htmlspecialchars($contract['slug']); ?>"
    >
      <div class="contract-content">
        <h2><?php echo htmlspecialchars($contract['en_title']); ?></h2>
        <div class="contract-content-text">
          <p><?php echo htmlspecialchars($contract['en_description']); ?></p>
        </div>
      </div>
      <div class="contract-media">
        <?php if (!empty($mediaItems)): ?>
          <div class="contract-slider pop-on-load" data-slider>
            <div class="contract-slider-track" data-slider-track>
              <?php foreach ($mediaItems as $mi): ?>
                <div class="contract-slide" data-slide
                     data-type="<?php echo htmlspecialchars($mi['type']); ?>"
                     data-src="<?php echo htmlspecialchars($mi['src']); ?>">
                  <?php if ($mi['type'] === 'video'): ?>
                    <video src="<?php echo htmlspecialchars($mi['src']); ?>"
                           preload="metadata"
                           muted
                           playsinline></video>
                  <?php else: ?>
                    <img src="<?php echo htmlspecialchars($mi['src']); ?>" alt="">
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>

            <?php if (count($mediaItems) > 1): ?>
              <div class="contract-slider-dots" data-slider-dots>
                <?php foreach ($mediaItems as $i => $_): ?>
                  <div class="contract-slider-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-slider-dot data-index="<?php echo $i; ?>"></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="media-watermark-inline">
              <img src="maze.jpg" alt="Logo">
              <span>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises</span>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php
    $idx++;
  endforeach;
  ?>
</main>

<footer class="reveal-on-load reveal-delay-5">
  <div class="footer-inner">
    <div class="footer-links">
      <a href="privacy-policy.php">Privacy Policy</a>
      <a href="cookie-policy.php">Cookie Policy</a>
      <a href="terms.php">Terms of Use</a>
    </div>
    <div class="footer-copy">
      © 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises. All rights reserved.
    </div>
  </div>
</footer>

<!-- Shared modal -->
<div class="media-modal" id="mediaModal">
  <div class="media-modal-inner">
    <div class="media-modal-header">
      <div class="media-modal-title">
        <img src="maze.jpg" alt="Logo" class="media-modal-logo">
        <span id="mediaModalTitle">Contract</span>
      </div>
      <div class="media-modal-close" id="mediaModalClose">×</div>
    </div>
    <div class="media-modal-body" id="mediaModalBody">
      <div class="media-watermark">
        <img src="maze.jpg" alt="Logo">
        <span>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises</span>
      </div>
    </div>
  </div>
</div>

<!-- Scroll-to-top arrow -->
<div class="scroll-arrow" id="scrollArrow" aria-label="Scroll to top">
  <span>↑</span>
</div>

<!-- FIXED CONTACT POPUP -->
<div class="contact-popup-fixed" id="contactPopup">
  <div class="contact-popup-icon">☎</div>
  <div class="contact-popup-text">
    <div class="contact-popup-title">Need to talk?</div>
    <div class="contact-popup-details">
      Call +968 578 9594 or email us – tap to open the Contact page.
    </div>
  </div>
  <button class="contact-popup-close" id="contactPopupClose" aria-label="Close contact popup">×</button>
</div>


<div class="question-popup-fixed" id="questionPopup" aria-label="Ask a Question">?</div>

<script>
  const bgLayer = document.getElementById('bgLayer');
  const slantLines = document.getElementById('slantLines');
  const sections = Array.from(document.querySelectorAll('.section-scroll'));

  const sectionBackgrounds = [
    { color: '#163b73', image: '' },
    { color: '#3a2d6d', image: '' },
    { color: '#274a36', image: '' },
    { color: '#5b2834', image: '' },
    { color: '#24455a', image: '' },
    { color: '#4b3a2b', image: '' },
    { color: '#22324a', image: '' }
  ];

  function applyBackgroundByIndex(idx) {
    const conf = sectionBackgrounds[idx] || sectionBackgrounds[sectionBackgrounds.length - 1];
    bgLayer.style.backgroundImage = conf.image ? 'url(' + conf.image + ')' : 'none';
    bgLayer.style.backgroundColor = conf.color;
    slantLines.setAttribute('data-shift', (idx % 2).toString());
  }

  let currentIndex = 0;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const idx = parseInt(entry.target.getAttribute('data-index'), 10);
        currentIndex = idx;
        applyBackgroundByIndex(idx);
      }
    });
  }, { threshold: 0.5 });

  sections.forEach((sec, i) => {
    sec.setAttribute('data-index', i.toString());
    observer.observe(sec);
  });

  applyBackgroundByIndex(0);

  function createSlantLines() {
    const colors = ['red', 'yellow', 'white'];
    for (let i = 0; i < 18; i++) {
      const div = document.createElement('div');
      div.className = 'slant-line ' + colors[i % colors.length];
      div.style.left = (Math.random() * 120) + '%';
      div.style.top = (Math.random() * -200) + 'px';
      div.style.animationDelay = (Math.random() * -12) + 's';
      slantLines.appendChild(div);
    }
  }
  createSlantLines();

  // Submenu
  const whoToggle = document.getElementById('whoSubToggle');
  const whoMenu = document.getElementById('whoSubMenu');

  whoToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = whoMenu.classList.toggle('open');
    whoToggle.classList.toggle('open', isOpen);
  });

  document.addEventListener('click', (e) => {
    if (!whoMenu.contains(e.target) && !whoToggle.contains(e.target)) {
      whoMenu.classList.remove('open');
      whoToggle.classList.remove('open');
    }
  });

  // Modal
  const mediaModal = document.getElementById('mediaModal');
  const mediaModalBody = document.getElementById('mediaModalBody');
  const mediaModalTitle = document.getElementById('mediaModalTitle');
  const mediaModalClose = document.getElementById('mediaModalClose');

  function openMediaModal(sourceEl, title) {
    Array.from(mediaModalBody.children).forEach((child) => {
      if (!child.classList.contains('media-watermark')) {
        mediaModalBody.removeChild(child);
      }
    });

    let clone;
    if (sourceEl.tagName.toLowerCase() === 'video') {
      clone = document.createElement('video');
      clone.src = sourceEl.currentSrc || sourceEl.src;
      clone.controls = true;
      clone.autoplay = true;
      clone.playsInline = true;
    } else {
      clone = document.createElement('img');
      clone.src = sourceEl.src;
      clone.alt = '';
    }
    mediaModalBody.insertBefore(clone, mediaModalBody.firstChild);

    mediaModalTitle.textContent = title || 'Contract';
    mediaModal.classList.add('open');
  }

  function closeMediaModal() {
    mediaModal.classList.remove('open');
    const v = mediaModalBody.querySelector('video');
    if (v) v.pause();
  }

  mediaModalClose.addEventListener('click', closeMediaModal);
  mediaModal.addEventListener('click', (e) => {
    if (e.target === mediaModal) {
      closeMediaModal();
    }
  });

  // Slider logic (per contract section) – autoplay + dots + click opens modal
  const SLIDE_INTERVAL = 3500; // ms

  document.querySelectorAll('[data-slider]').forEach((sliderEl) => {
    const track = sliderEl.querySelector('[data-slider-track]');
    const slides = Array.from(sliderEl.querySelectorAll('[data-slide]'));
    const dotsContainer = sliderEl.querySelector('[data-slider-dots]');
    const dots = dotsContainer ? Array.from(dotsContainer.querySelectorAll('[data-slider-dot]')) : [];
    let current = 0;
    let timer = null;

    if (!slides.length) return;

    function goTo(index) {
      current = index;
      const offset = -index * 100;
      track.style.transform = 'translateX(' + offset + '%)';

      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });

      slides.forEach((slide, i) => {
        const v = slide.querySelector('video');
        if (v) {
          if (i === index) {
            v.play().catch(() => {});
          } else {
            v.pause();
            v.currentTime = 0;
          }
        }
      });
    }

    function next() {
      const nextIndex = (current + 1) % slides.length;
      goTo(nextIndex);
    }

    function startAuto() {
      if (timer) clearInterval(timer);
      timer = setInterval(next, SLIDE_INTERVAL);
    }

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        const idx = parseInt(dot.getAttribute('data-index'), 10) || 0;
        goTo(idx);
        startAuto();
      });
    });

    slides.forEach((slide) => {
      slide.addEventListener('click', () => {
        const section = slide.closest('.contract-section');
        const title = section ? section.querySelector('h2').textContent : 'Contract';
        const innerMedia = slide.querySelector('img, video');
        if (innerMedia) {
          openMediaModal(innerMedia, title);
        }
      });
    });

    goTo(0);
    if (slides.length > 1) {
      startAuto();
    }
  });

  // Hash navigation highlight
  if (window.location.hash) {
    const id = window.location.hash.substring(1);
    const el = document.getElementById(id);
    if (el) {
      el.style.boxShadow = "0 0 0 3px rgba(255,213,79,0.6)";
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => {
        el.style.boxShadow = "";
      }, 2000);
    }
  }

  // Scroll-to-top arrow logic
  const scrollArrow = document.getElementById('scrollArrow');

  function updateScrollArrow() {
    if (window.scrollY > 220) {
      scrollArrow.classList.add('visible');
    } else {
      scrollArrow.classList.remove('visible');
    }
  }

  window.addEventListener('scroll', updateScrollArrow);
  updateScrollArrow();

  scrollArrow.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });


 // CONTACT POPUP BEHAVIOUR
  const contactPopup = document.getElementById('contactPopup');
  const contactPopupClose = document.getElementById('contactPopupClose');

  // Click on body of popup (except close button) -> go to contact.php
  contactPopup.addEventListener('click', (e) => {
    if (e.target === contactPopupClose) return;
    window.location.href = 'contact.php';
  });

  // Close button just hides until next refresh
  contactPopupClose.addEventListener('click', (e) => {
    e.stopPropagation();
    contactPopup.classList.add('hidden');
  });


 const questionPopup = document.getElementById('questionPopup');
  questionPopup.addEventListener('click', () => {
    window.location.href = 'ask-question.php';
  });

</script>

</body>
</html>
