<?php
// services.php – Our Services (zig-zag sections, animated background, images + videos)

require __DIR__ . '/db.php';

// Load services from DB
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order, id");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Services | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction services Oman, engineering services Oman, contracting services Oman">

<meta name="description" content="Explore construction, engineering, and contracting services offered by Muadh Al Zadjali Enterprises in Oman.">

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

    /* Entrance animations */
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

    /* Slight pop for media blocks */
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

    .services-hero {
      max-width: 1150px;
      margin: 32px auto 16px;
      padding: 22px 20px;
    }
    .services-hero .tag-label {
      display: inline-block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #ffd54f;
      margin-bottom: 6px;
    }
    .services-hero h1 {
      font-size: 24px;
      margin-bottom: 8px;
    }
    .services-hero p {
      font-size: 14px;
      max-width: 650px;
      color: #e0e0e0;
    }

    .service-section {
      min-height: 60vh;
      max-width: 1150px;
      margin: 40px auto;
      display: grid;
      grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.3fr);
      gap: 24px;
      align-items: center;
      position: relative;
    }
    .service-section[data-side="right"] .service-content {
      order: 2;
      text-align: right;
    }
    .service-section[data-side="right"] .service-media {
      order: 1;
    }

    .service-content h2 {
      font-size: 22px;
      margin-bottom: 8px;
    }

    .service-content-text {
      max-width: 520px;
      max-height: 220px;
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 4px;
    }
    .service-content-text p {
      font-size: 14px;
      line-height: 1.6;
      color: #f1f1f1;
      white-space: pre-wrap;
    }

    .service-media {
      position: relative;
      width: 100%;
      overflow: hidden;
      border-radius: 10px;
    }
    .service-carousel {
      position: relative;
      width: 100%;
      height: 260px;
    }
    .service-carousel-item {
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 0.4s ease, transform 0.4s ease;
      display: flex;
      justify-content: center;
      align-items: center;
      pointer-events: none;
    }
    .service-carousel-item img,
    .service-carousel-item video {
      width: 86%;
      height: 100%;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.45);
      cursor: pointer;
    }

    .service-carousel-item.peek-left,
    .service-carousel-item.peek-right {
      opacity: 0.2;
      pointer-events: none;
    }
    .service-carousel-item.peek-left {
      transform: translateX(-26%);
    }
    .service-carousel-item.peek-right {
      transform: translateX(26%);
    }
    .service-carousel-item.active {
      opacity: 1;
      transform: translateX(0);
      pointer-events: auto;
      z-index: 1;
    }

    .service-carousel-item .media-watermark-inline {
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
    .service-carousel-item .media-watermark-inline img {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      object-fit: contain;
    }

    .carousel-nav {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      pointer-events: none;
      z-index: 2;
    }
    .carousel-arrow {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      background: rgba(0,0,0,0.55);
      border: 1px solid rgba(255,255,255,0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      pointer-events: auto;
      margin: 0 6px;
      transition: background 0.2s ease, transform 0.2s ease;
      z-index: 3;
    }
    .carousel-arrow span {
      color: #fff;
      font-size: 16px;
      line-height: 1;
    }
    .carousel-arrow:hover {
      background: rgba(255,213,79,0.85);
      transform: translateY(-1px);
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

    /* Scroll-to-top arrow (same style as industry/about) */
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

    /* EASY CONTACT ROW PER SERVICE */
    .service-contact-row {
      margin-top: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      font-size: 12px;
    }
    .service-contact-label {
      font-weight: 600;
      color: #ffd54f;
      margin-right: 4px;
    }
    .service-contact-row a,
    .service-contact-row span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 9px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,0.4);
      text-decoration: none;
      color: #fff;
      background: rgba(0,0,0,0.25);
      cursor: pointer;
      transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    .service-contact-row a:hover,
    .service-contact-row span:hover {
      background: rgba(255,255,255,0.18);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.4);
    }
    .service-contact-row a svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
    }
    .service-contact-row .phone-pill,
    .service-contact-row .email-pill {
      border-style: dashed;
    }

    /* FIXED CONTACT POPUP (same family as index) */
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

    @media (max-width: 900px) {
      .service-section {
        grid-template-columns: minmax(0, 1fr);
        margin: 32px 14px;
      }
      .service-section[data-side="right"] .service-content {
        order: 1;
        text-align: left;
      }
      .service-section[data-side="right"] .service-media {
        order: 2;
      }
      .services-hero {
        margin: 22px 14px 12px;
        padding: 18px 14px;
      }
      .sub-menu {
        right: auto;
        left: 0;
      }
      .service-content-text {
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
      .contact-popup-fixed {
        right: 12px;
        left: 12px;
        bottom: 74px;
        max-width: none;
      }
      .service-contact-row {
        gap: 6px;
      }
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
    <a href="services.php" class="active">Services</a>
    <a href="contracts.php">Contracts</a>
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
  <section class="services-hero section-scroll reveal-on-load reveal-delay-4" data-index="0" id="top">
    <span class="tag-label">Our Services</span>
    <h1>Comprehensive construction solutions tailored to your needs.</h1>
    <p>From residential developments to large-scale commercial and industrial projects, we deliver end‑to‑end engineering and contracting services with quality and reliability.</p>
  </section>

  <?php
  $idx = 1;
  foreach ($services as $service):
      $side = ($idx % 2 === 1) ? 'left' : 'right';

      // Build media items from media_json; fallback to image_path
      $mediaItems = [];
      if (!empty($service['media_json'])) {
          $decoded = json_decode($service['media_json'], true);
          if (is_array($decoded)) {
              foreach ($decoded as $m) {
                  if (empty($m['type']) || empty($m['src'])) continue;
                  $type = $m['type'] === 'video' ? 'video' : 'image';
                  $mediaItems[] = ['type' => $type, 'src' => $m['src']];
              }
          }
      } else {
          if (!empty($service['image_path'])) {
              $parts = explode(',', $service['image_path']);
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
      class="service-section section-scroll reveal-on-load reveal-delay-<?php echo ($idx % 3) + 4; ?>"
      data-index="<?php echo $idx; ?>"
      data-side="<?php echo $side; ?>"
      id="<?php echo htmlspecialchars($service['slug']); ?>"
      data-service-title="<?php echo htmlspecialchars($service['en_title'], ENT_QUOTES); ?>"
      data-service-desc="<?php echo htmlspecialchars($service['en_description'], ENT_QUOTES); ?>"
    >
      <div class="service-content">
        <h2><?php echo htmlspecialchars($service['en_title']); ?></h2>
        <div class="service-content-text">
          <p><?php echo htmlspecialchars($service['en_description']); ?></p>
        </div>

        <!-- EASY CONTACT ROW -->
        <div class="service-contact-row"
             data-service-title="<?php echo htmlspecialchars($service['en_title'], ENT_QUOTES); ?>"
             data-service-desc="<?php echo htmlspecialchars($service['en_description'], ENT_QUOTES); ?>">
          <span class="service-contact-label">Easy contact:</span>

          <!-- Phone -->
          <a href="tel:+9685789594" class="phone-pill">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.36 11.36 0 003.56.57 1 1 0 011 1v3.54a1 1 0 01-.91 1A17.91 17.91 0 013 4.91 1 1 0 014 4h3.55a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.25 1.01z"/>
            </svg>
            <span>Call</span>
          </a>

          <!-- Email -->
          <a href="mailto:muadhalzadjalienterprises@gmail.com" class="email-pill">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 2v.01L12 11 4 6.01V6h16zM4 18V8l8 5 8-5v10H4z"/>
            </svg>
            <span>Email</span>
          </a>

          <!-- WhatsApp -->
          <a href="#"
             class="service-wa-link"
             target="_blank"
             rel="noopener noreferrer">
            <svg viewBox="0 0 32 32" aria-hidden="true">
              <path fill="#25D366" d="M16 3C9.373 3 4 8.373 4 15c0 2.115.551 4.099 1.602 5.9L4 29l8.313-1.567C13.987 28.469 14.983 28.7 16 28.7 22.627 28.7 28 23.327 28 16.7 28 10.073 22.627 4.7 16 4.7z"/>
              <path fill="#FFF" d="M21.4 18.73c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.66.15-.2.29-.76.96-.93 1.16-.17.2-.34.22-.64.07-.3-.15-1.28-.47-2.44-1.5-.9-.8-1.5-1.8-1.68-2.1-.18-.3-.02-.46.13-.61.14-.14.3-.36.45-.54.15-.18.2-.29.3-.48.1-.19.05-.36-.02-.51-.07-.15-.66-1.6-.91-2.19-.24-.58-.49-.5-.66-.51l-.56-.01c-.19 0-.5.07-.76.35-.26.29-1 1-1 2.46 0 1.46 1.03 2.87 1.18 3.07.15.2 2.02 3.18 4.9 4.46.68.29 1.21.47 1.62.61.68.22 1.3.19 1.79.11.55-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.18-1.42-.07-.12-.27-.2-.57-.35z"/>
            </svg>
            <span>WhatsApp</span>
          </a>

          <!-- Instagram DM -->
          <a href="https://ig.me/m/muadhalzadjali"
             class="service-ig-link"
             target="_blank"
             rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <radialGradient id="igGrad" cx="1.5" cy="0" r="1.5">
                <stop offset="0%" stop-color="#fdf497"/>
                <stop offset="45%" stop-color="#fd5949"/>
                <stop offset="60%" stop-color="#d6249f"/>
                <stop offset="90%" stop-color="#285AEB"/>
              </radialGradient>
              <path fill="url(#igGrad)" d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5z"/>
              <path fill="#fff" d="M12 7a5 5 0 105 5 5 5 0 00-5-5zm0 8.2A3.2 3.2 0 1115.2 12 3.21 3.21 0 0112 15.2zm5.25-8.73a1.17 1.17 0 11-1.17-1.17 1.17 1.17 0 011.17 1.17z"/>
            </svg>
            <span>Instagram</span>
          </a>

          <!-- Facebook Messenger -->
          <a href="https://m.me/muadhalzadjali"
             class="service-fb-link"
             target="_blank"
             rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <linearGradient id="fbmGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#00B2FF"/>
                <stop offset="100%" stop-color="#006AFF"/>
              </linearGradient>
              <path fill="url(#fbmGrad)" d="M12 2C6.48 2 2 6.06 2 11.12c0 2.75 1.22 5.21 3.22 6.86V22l2.95-1.61A11.4 11.4 0 0012 20.24c5.52 0 10-4.06 10-9.12S17.52 2 12 2z"/>
              <path fill="#fff" d="M18.14 10.28l-3.06 3.26a1.07 1.07 0 01-1.45.07l-2.15-1.8-2.9 2.47a.6.6 0 01-.96-.42l-.3-4.37a.86.86 0 011.37-.7l2.93 2.1 2.64-2.8a1.07 1.07 0 011.58.09l1.25 1.58a.79.79 0 01-.05 1.02z"/>
            </svg>
            <span>Messenger</span>
          </a>
        </div>
      </div>

      <div class="service-media">
        <?php if (!empty($mediaItems)): ?>
          <div class="service-carousel pop-on-load"
               data-count="<?php echo count($mediaItems); ?>"
               data-service-title="<?php echo htmlspecialchars($service['en_title'], ENT_QUOTES); ?>">
            <?php foreach ($mediaItems as $i => $item): ?>
              <div class="service-carousel-item<?php echo $i === 0 ? ' active' : ''; ?>"
                   data-type="<?php echo htmlspecialchars($item['type']); ?>">
                <?php if ($item['type'] === 'video'): ?>
                  <video src="<?php echo htmlspecialchars($item['src']); ?>"
                         preload="metadata"
                         muted
                         playsinline></video>
                  <div class="media-watermark-inline">
                    <img src="maze.jpg" alt="Logo">
                    <span>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises</span>
                  </div>
                <?php else: ?>
                  <img src="<?php echo htmlspecialchars($item['src']); ?>" alt="">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>

            <div class="carousel-nav">
              <button type="button" class="carousel-arrow carousel-prev"><span>‹</span></button>
              <button type="button" class="carousel-arrow carousel-next"><span>›</span></button>
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
        <span id="mediaModalTitle">Service</span>
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
    { color: '#22324a', image: '' },
    { color: '#263238', image: '' },
    { color: '#1c2833', image: '' }
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
  const mediaModal      = document.getElementById('mediaModal');
  const mediaModalBody  = document.getElementById('mediaModalBody');
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

    mediaModalTitle.textContent = title || 'Service';
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

  // Carousels: auto-slide + longer on video + 2s pause window after arrow clicks
  const carousels = document.querySelectorAll('.service-carousel');

  carousels.forEach((carousel) => {
    const items = Array.from(carousel.querySelectorAll('.service-carousel-item'));
    if (!items.length) return;

    let active = 0;
    const baseIntervalMs = 2000;
    const videoExtraMs   = 4000;
    let autoTimer = null;
    let userPauseTimeout = null;
    let autoEnabled = true;

    const prevBtn = carousel.querySelector('.carousel-prev');
    const nextBtn = carousel.querySelector('.carousel-next');

    function updateClasses() {
      items.forEach((item) => {
        item.classList.remove('active', 'peek-left', 'peek-right');
      });

      const leftIndex  = (active - 1 + items.length) % items.length;
      const rightIndex = (active + 1) % items.length;

      items[active].classList.add('active');
      if (items.length > 1) {
        items[leftIndex].classList.add('peek-left');
        items[rightIndex].classList.add('peek-right');
      }
    }

    function resetVideoHandlersAndPlayback() {
      items.forEach(item => {
        const v = item.querySelector('video');
        if (v) {
          v.onended = null;
          v.pause();
          v.currentTime = 0;
        }
      });
    }

    function isActiveVideo() {
      const currentItem = items[active];
      return currentItem.dataset.type === 'video';
    }

    function setupVideoForActive() {
      const currentItem = items[active];
      const videoEl = currentItem.querySelector('video');
      if (!videoEl) return;

      videoEl.muted = true;
      videoEl.playsInline = true;
      videoEl.play().catch(() => {});
      videoEl.onended = () => {
        if (!autoEnabled) return;
        nextSlide();
      };
    }

    function showSlide(index) {
      active = (index + items.length) % items.length;
      resetVideoHandlersAndPlayback();
      updateClasses();
      if (isActiveVideo()) {
        setupVideoForActive();
      }
    }

    function nextSlide() {
      showSlide(active + 1);
    }

    function prevSlide() {
      showSlide(active - 1);
    }

    function clearAuto() {
      if (autoTimer) {
        clearTimeout(autoTimer);
        autoTimer = null;
      }
    }

    function scheduleNextAuto() {
      clearAuto();
      if (!autoEnabled) return;

      let delay = baseIntervalMs;
      if (isActiveVideo()) {
        delay += videoExtraMs;
      }

      autoTimer = setTimeout(() => {
        if (isActiveVideo()) {
          const currentItem = items[active];
          const v = currentItem.querySelector('video');
          if (v && !v.paused && !v.ended) {
            scheduleNextAuto();
            return;
          }
        }
        nextSlide();
        scheduleNextAuto();
      }, delay);
    }

    function restartAutoAfterUserPause() {
      autoEnabled = false;
      clearAuto();

      if (userPauseTimeout) {
        clearTimeout(userPauseTimeout);
      }
      userPauseTimeout = setTimeout(() => {
        autoEnabled = true;
        scheduleNextAuto();
      }, 2000);
    }

    // init
    updateClasses();
    if (isActiveVideo()) {
      setupVideoForActive();
    }
    scheduleNextAuto();

    prevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      prevSlide();
      restartAutoAfterUserPause();
    });

    nextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      nextSlide();
      restartAutoAfterUserPause();
    });

    items.forEach((item) => {
      const media = item.querySelector('img, video');
      if (!media) return;
      media.addEventListener('click', () => {
        openMediaModal(
          media,
          carousel.getAttribute('data-service-title') || ''
        );
      });
    });
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

  // FIXED CONTACT POPUP BEHAVIOUR
  const contactPopup = document.getElementById('contactPopup');
  const contactPopupClose = document.getElementById('contactPopupClose');

  contactPopup.addEventListener('click', (e) => {
    if (e.target === contactPopupClose) return;
    window.location.href = 'contact.php';
  });

  contactPopupClose.addEventListener('click', (e) => {
    e.stopPropagation();
    contactPopup.classList.add('hidden');
  });

  // PER-SERVICE EASY CONTACT: build WhatsApp message per service
  const waBaseNumber = '96895789594'; // international format without +
  const waLinks = document.querySelectorAll('.service-contact-row .service-wa-link');

  waLinks.forEach(link => {
    const row = link.closest('.service-contact-row');
    if (!row) return;
    const title = row.getAttribute('data-service-title') || '';
    const desc  = row.getAttribute('data-service-desc') || '';

    const msg = `Hello, I am interested in your service: ${title}. ${desc ? 'Details: ' + desc : ''}`;
    const encoded = encodeURIComponent(msg);
    // Format wa.me link with text parameter.[web:524][web:528]
    const href = `https://wa.me/${waBaseNumber}?text=${encoded}`;
    link.setAttribute('href', href);
  });

  const questionPopup = document.getElementById('questionPopup');
  questionPopup.addEventListener('click', () => {
    window.location.href = 'ask-question.php';
  });

</script>

</body>
</html>
