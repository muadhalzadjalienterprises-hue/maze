<?php
// location.php – Our Location (themed like industry.php, DB driven)
require __DIR__ . '/db.php';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$stmt = $pdo->query("SELECT * FROM location_settings ORDER BY id LIMIT 1");
$loc  = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$officeAddress = $loc['office_address'] ?? 'Behind Grand Muttrah, Ruwi';
$officeCity    = $loc['office_city']    ?? 'Muscat';
$officePostal  = $loc['office_postal']  ?? '112';
$officeCountry = $loc['office_country'] ?? 'Sultanate of Oman';
$phone         = $loc['phone']          ?? '+968-0000 0000';
$email         = $loc['email']          ?? 'info@muadhalzadjali.com';

$businessHours = preg_split('/\r\n|\r|\n/', $loc['business_hours'] ?? '');
$businessHours = array_filter(array_map('trim', $businessHours));

$mapQuery      = $loc['map_query'] ?? 'Muadh+Al+Zadjali+Engineering+Ruwi+Muscat';

$serviceAreasLines = preg_split('/\r\n|\r|\n/', $loc['service_areas'] ?? '');
$serviceAreasLines = array_filter(array_map('trim', $serviceAreasLines));

$directionsText = $loc['directions_text'] ?? 'Use your preferred maps application and search for “Muadh Al Zadjali Engineering & Cont. Enterprises, Ruwi, Muscat” for turn‑by‑turn directions.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Location | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction company Oman location, engineering company Oman address">

<meta name="description" content="Find the location of Muadh Al Zadjali Engineering & Cont. Enterprises and connect with our team in Oman.">

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; scroll-behavior: smooth; }
    body {
      font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;
      background: #0b3c5d;
      color: #f5f5f5;
      overflow-x: hidden;
      position: relative;
    }

    .reveal-on-load {
      opacity: 0;
      transform: translateY(20px) scale(0.98);
      animation: revealUp 0.7s cubic-bezier(0.22,0.61,0.36,1) forwards;
    }
    .reveal-delay-1 { animation-delay: .1s; }
    .reveal-delay-2 { animation-delay: .2s; }
    .reveal-delay-3 { animation-delay: .3s; }
    .reveal-delay-4 { animation-delay: .4s; }
    .reveal-delay-5 { animation-delay: .5s; }
    @keyframes revealUp {
      0% { opacity:0; transform:translateY(20px) scale(0.98); }
      60%{ opacity:1; transform:translateY(-4px) scale(1.01); }
      100%{opacity:1; transform:translateY(0) scale(1);}
    }

    .logo-img{
      width:62px;height:62px;object-fit:contain;transform-origin:center;
      animation:logoBounce .8s ease-out .25s forwards;opacity:0;
    }
    @keyframes logoBounce{
      0%{opacity:0;transform:scale(.5) rotate(-6deg);}
      60%{opacity:1;transform:scale(1.05) rotate(2deg);}
      100%{opacity:1;transform:scale(1) rotate(0);}
    }

    .bg-layer{
      position:fixed;inset:0;z-index:-3;
      background-color:#22324a;background-size:cover;
      background-position:center;background-repeat:no-repeat;
    }
    .slant-lines{
      position:fixed;inset:-200px;z-index:-2;pointer-events:none;overflow:hidden;
    }
    .slant-line{
      position:absolute;width:4px;height:260px;opacity:.6;transform:rotate(45deg);
      animation:slantMove 12s linear infinite;
    }
    .slant-line.red{background:rgba(220,20,60,.7);}
    .slant-line.yellow{background:rgba(255,215,0,.7);}
    .slant-line.white{background:rgba(255,255,255,.7);}
    @keyframes slantMove{
      0%{transform:translateY(0) rotate(45deg);}
      100%{transform:translateY(400px) rotate(45deg);}
    }

    .site-header{
      background:rgba(255,255,255,.96);
      border-bottom:1px solid #e0e0e0;
      padding:24px 18px;
      display:flex;align-items:center;justify-content:space-between;
      width:100%;position:relative;z-index:10;flex-direction:row;
    }
    .logo{display:flex;flex-direction:row;align-items:center;gap:10px;}
    .logo-text{display:flex;flex-direction:column;line-height:1.1;text-align:left;}
    .logo-title{font-weight:700;font-size:17px;color:#830606;}
    .logo-sub{font-size:11px;color:#666;}

    .main-nav{
      display:flex;gap:16px;align-items:center;flex-direction:row;position:relative;
    }
    .main-nav a{
      text-decoration:none;color:#333;font-size:14px;position:relative;padding:4px 0;
    }
    .main-nav a::after{
      content:"";position:absolute;left:0;bottom:-2px;width:0;height:2px;
      background:linear-gradient(90deg,#810404,#810404);transition:width .25s ease;
    }
    .main-nav a:hover::after,.main-nav a.active::after{width:100%;}
    .main-nav a:hover,.main-nav a.active{color:#860408;}

    .nav-item-with-sub{
      position:relative;display:inline-flex;align-items:center;gap:4px;
    }
    .nav-sub-toggle{
      border:none;background:transparent;cursor:pointer;padding:0;margin:0;
      font-size:66px;line-height:1;color:#333;
    }
    .nav-sub-toggle span{display:inline-block;transition:transform .2s ease;}
    .nav-sub-toggle.open span{transform:rotate(180deg);}

    .nav-arrow-blink {
      animation: navArrowBlink 1s infinite ease-in-out;
    }
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

    .sub-menu{
      position:absolute;top:26px;right:0;background:rgba(255,255,255,.98);
      border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.25);
      padding:6px 0;min-width:190px;display:none;z-index:30;
    }
    .sub-menu.open{
      display:block;animation:dropDown .18s ease-out forwards;transform-origin:top right;
    }
    @keyframes dropDown{
      0%{opacity:0;transform:scale(.95) translateY(-4px);}
      100%{opacity:1;transform:scale(1) translateY(0);}
    }
    .sub-menu a{
      display:block;padding:6px 10px;font-size:13px;color:#333;
      text-decoration:none;white-space:nowrap;
    }
    .sub-menu a:hover{background:rgba(0,0,0,.06);}

    main{padding-top:18px;}

    .page-hero{
      max-width:1150px;margin:40px auto 10px;padding:0 20px;
    }
    .page-hero-inner{max-width:760px;}
    .tag-label{
      display:inline-block;font-size:11px;text-transform:uppercase;
      letter-spacing:.08em;color:#ffd54f;margin-bottom:6px;
    }
    .page-hero h1{font-size:26px;margin-bottom:8px;color:#fff;}
    .page-hero p{
      font-size:14px;max-width:650px;color:#e0eef5;line-height:1.7;
    }

    /* Section ladder */
    .section-ladder {
      max-width: 1150px;
      margin: 6px auto 0;
      padding: 0 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      font-size: 12px;
      color: #f5f5f5;
      align-items: center;
    }
    .section-ladder-label {
      text-transform: uppercase;
      letter-spacing: 0.12em;
      font-weight: 600;
      color: #ffd54f;
    }
    .section-ladder a {
      text-decoration: none;
      color: #f5f5f5;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      border-radius: 999px;
      background: rgba(0,0,0,0.25);
      border: 1px solid rgba(255,255,255,0.06);
      transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    .section-ladder a span.arrow {
      font-size: 13px;
      transform: translateY(-1px);
    }
    .section-ladder a:hover {
      background: rgba(0,0,0,0.45);
      box-shadow: 0 3px 10px rgba(0,0,0,0.4);
      transform: translateY(-1px);
    }

    .location-wrap{
      max-width:1100px;margin:0 auto;
      padding:24px 20px 32px;
      display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1.6fr);
      gap:24px;
    }
    .office-card,.map-card,.areas-card{
      background:rgba(0,0,0,.32);padding:18px 18px 20px;border-radius:10px;
      box-shadow:0 6px 14px rgba(0,0,0,.35);
      border:1px solid rgba(255,255,255,.09);backdrop-filter:blur(4px);
      font-size:14px;color:#dde9f1;
    }
    .office-card h2,.map-card h2,.areas-card h2{
      font-size:18px;margin-bottom:8px;color:#fff;
    }
    .office-card p,.map-card p,.areas-card p{
      margin-bottom:6px;line-height:1.6;
    }
    .office-card strong{color:#ffd54f;}
    .hours-list{margin-top:6px;}

    .map-placeholder {
  background:rgba(0,0,0,.35);
  border-radius:6px;
  height:260px;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  margin-bottom:10px;
  border:1px dashed rgba(255,255,255,.35);
  cursor: pointer;
  position: relative;
}

.map-click-text {
  margin-top: 28px;
  font-size: 13px;
  color: #ffd54f;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  animation: mapTextBlink 1s infinite;
}

/* simple blink for the text */
@keyframes mapTextBlink {
  0%, 50% {
    opacity: 1;
  }
  51%, 100% {
    opacity: 0.2;
  }
}


    /* Blinking location icon */
    .map-location-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: radial-gradient(circle at 30% 30%, #ffffff 0, #ffd54f 45%, #ffb300 80%);
      display:flex;
      align-items:center;
      justify-content:center;
      position: relative;
      animation: mapPulse 1.3s infinite ease-out;
      box-shadow: 0 0 0 0 rgba(255,213,79,0.9);
    }
    .map-location-icon::before {
      content:"📍";
      font-size:34px;
      transform: translateY(-2px);
    }
    @keyframes mapPulse {
      0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(255,213,79,0.9);
        opacity: 1;
      }
      60% {
        transform: scale(1.15);
        box-shadow: 0 0 0 18px rgba(255,213,79,0);
        opacity: 1;
      }
      100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(255,213,79,0);
        opacity: 1;
      }
    }

    /* Service areas full width (no right aside) */
    .service-areas{
      max-width:1100px;margin:0 auto;
      padding:0 20px 40px;
      display:block;
      font-size:14px;color:#dde9f1;
    }
    .areas-card ul{padding-left:18px;margin-top:4px;}
    .areas-card li{margin-bottom:4px;}

    footer{
      margin-top:40px;width:100%;background:rgba(0,0,0,.9);color:#eee;
      border-top:1px solid rgba(255,255,255,.1);padding:26px 0 32px;
    }
    .footer-inner{
      max-width:1150px;margin:0 auto;padding:0 24px;
      display:flex;flex-direction:column;gap:14px;font-size:13px;
    }
    .footer-main{
      display:flex;justify-content:space-between;flex-wrap:wrap;
      gap:12px;align-items:center;
    }
    .footer-left{max-width:420px;line-height:1.5;}
    .footer-links a{
      color:#ffd54f;margin-left:14px;text-decoration:none;
    }
    .footer-links a:hover{text-decoration:underline;}
    .footer-social-row{
      display:flex;flex-wrap:wrap;gap:10px;align-items:center;
    }
    .footer-social-label{font-size:13px;color:#ddd;margin-right:6px;}
    .social-link{
      display:inline-flex;align-items:center;justify-content:center;
      width:34px;height:34px;border-radius:50%;
      background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.22);text-decoration:none;
      transition:background .2s ease,transform .2s ease,box-shadow .2s ease;
    }
    .social-link svg{width:18px;height:18px;fill:#fff;}
    .social-link:hover{
      background:rgba(255,255,255,.2);transform:translateY(-1px);
      box-shadow:0 4px 10px rgba(0,0,0,.4);
    }
    .footer-bottom-text{
      font-size:12px;color:#ccc;line-height:1.6;
    }

    .scroll-arrow{
      position:fixed;right:22px;bottom:26px;width:42px;height:42px;
      border-radius:999px;background:rgba(0,0,0,.55);
      box-shadow:0 4px 12px rgba(0,0,0,.45);
      display:flex;align-items:center;justify-content:center;
      cursor:pointer;z-index:90;opacity:0;pointer-events:none;
      transition:opacity .25s ease,transform .25s ease,background .2s ease;
    }
    .scroll-arrow.visible{opacity:1;pointer-events:auto;transform:translateY(0);}
    .scroll-arrow:hover{background:rgba(0,0,0,.75);}
    .scroll-arrow span{
      display:inline-block;color:#ffd54f;font-size:22px;line-height:1;
      transform:translateY(-1px);
    }

    .question-popup-fixed{
      position:fixed;right:18px;bottom:90px;z-index:150;
      width:40px;height:40px;border-radius:999px;background:#ffd54f;
      color:#0b3c5d;display:flex;align-items:center;justify-content:center;
      box-shadow:0 6px 18px rgba(0,0,0,.6);cursor:pointer;
      font-size:22px;border:1px solid rgba(0,0,0,.25);
    }
    .question-popup-fixed:hover{transform:translateY(-1px);}

    @media (max-width:900px){
      .site-header{
        padding:16px 14px;flex-direction:column;align-items:flex-start;gap:8px;
      }
      .location-wrap{
        grid-template-columns:minmax(0,1fr);
      }
      .footer-main{flex-direction:column;align-items:flex-start;}
      .footer-links{margin-top:4px;}
    }
    @media (max-width:600px){
      .scroll-arrow{right:16px;bottom:18px;}
      .question-popup-fixed{right:16px;bottom:90px;}
    }
  </style>
</head>
<body>
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
    <a href="industry.php">Industry Outlook</a>
    <a href="location.php" class="active">Location</a>
    <a href="contact.php">Contact</a>
    <div class="nav-item-with-sub">
      <a href="ask-question.php">Ask a question</a>
      <button type="button" class="nav-sub-toggle" id="whoSubToggle" aria-label="More pages">
        <span class="nav-arrow-blink">▾</span>
      </button>
      <div class="sub-menu" id="whoSubMenu">
        <a href="services.php">Services</a>
        <a href="contracts.php">Contracts</a>
        <a href="about.php">About Us</a>
        <a href="insights.php">Insights</a>
      </div>
    </div>
  </nav>
</header>

<main>
  <section class="page-hero reveal-on-load reveal-delay-2" id="top">
    <div class="page-hero-inner">
      <span class="tag-label">Location</span>
      <h1>Visit our office or project sites.</h1>
      <p>Find our main office in Ruwi, Muscat, and understand the regions we serve so you can plan your visit or site meeting with confidence.</p>
    </div>
  </section>

  <!-- Ladder nav -->
  <div class="section-ladder reveal-on-load reveal-delay-3">
    <span class="section-ladder-label">Sections</span>
    <a href="#main-office"><span class="arrow">➤</span><span>Main Office</span></a>
    <a href="#map-directions"><span class="arrow">➤</span><span>Map &amp; Directions</span></a>
    <a href="#service-areas"><span class="arrow">➤</span><span>Service Areas</span></a>
  </div>

  <section class="location-wrap">
    <article class="office-card reveal-on-load reveal-delay-3" id="main-office">
      <h2>Main Office</h2>
      <p><strong>Address</strong><br>
        <?php echo nl2br(esc($officeAddress)); ?><br>
        <?php echo esc($officeCity); ?>, <?php echo esc($officePostal); ?><br>
        <?php echo esc($officeCountry); ?>
      </p>
      <p><strong>Phone</strong><br><?php echo esc($phone); ?></p>
      <p><strong>Email</strong><br><?php echo esc($email); ?></p>

      <?php if ($businessHours): ?>
        <div class="hours-list">
          <p><strong>Business Hours</strong></p>
          <?php foreach ($businessHours as $line): ?>
            <p><?php echo esc($line); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>

    <aside class="map-card reveal-on-load reveal-delay-4" id="map-directions">
      <h2>Map &amp; Directions</h2>
      <div class="map-placeholder" id="mapPlaceholder">
        <div class="map-location-icon"></div>
        <div class="map-click-text">Click to see map</div>
      </div>
      <p><?php echo esc($directionsText); ?></p>
    </aside>
  </section>

  <section class="service-areas">
    <article class="areas-card reveal-on-load reveal-delay-4" id="service-areas">
      <h2>Service Areas</h2>
      <p>We support projects across key regions with a focus on quality, safety, and on‑time delivery.</p>
      <?php if ($serviceAreasLines): ?>
        <ul>
          <?php foreach ($serviceAreasLines as $line): ?>
            <li><?php echo esc($line); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </article>
  </section>
</main>

<footer class="reveal-on-load reveal-delay-5">
  <div class="footer-inner">
    <div class="footer-main">
      <div class="footer-left">
        <div>© 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises. All rights reserved.</div>
        <div style="margin-top:4px;">
          Committed to quality, safety, timely delivery, and long‑term client partnerships across Oman and the wider region.
        </div>
      </div>
      <div>
        <div class="footer-social-row">
          <span class="footer-social-label">Connect with us:</span>

          <!-- WhatsApp -->
          <a class="social-link" href="https://wa.me/96895789594" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
            <svg viewBox="0 0 32 32">
              <path d="M16 3C9.373 3 4 8.373 4 15c0 2.115.551 4.099 1.602 5.9L4 29l8.313-1.567C13.987 28.469 14.983 28.7 16 28.7 22.627 28.7 28 23.327 28 16.7 28 10.073 22.627 4.7 16 4.7zm0 22.4c-.87 0-1.722-.17-2.53-.506l-.18-.076-4.93.93.94-4.81-.12-.19C8.43 19.1 8 17.58 8 16.01 8 10.94 11.94 7 17.01 7 22.08 7 26 10.94 26 16.01 26 21.08 22.08 25.4 17.01 25.4z"/>
              <path d="M21.4 18.73c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.66.15-.2.29-.76.96-.93 1.16-.17.2-.34.22-.64.07-.3-.15-1.28-.47-2.44-1.5-.9-.8-1.5-1.8-1.68-2.1-.18-.3-.02-.46.13-.61.14-.14.3-.36.45-.54.15-.18.2-.29.3-.48.1-.19.05-.36-.02-.51-.07-.15-.66-1.6-.91-2.19-.24-.58-.49-.5-.66-.51l-.56-.01c-.19 0-.5.07-.76.35-.26.29-1 1-1 2.46 0 1.46 1.03 2.87 1.18 3.07.15.2 2.02 3.18 4.9 4.46.68.29 1.21.47 1.62.61.68.22 1.3.19 1.79.11.55-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.18-1.42-.07-.12-.27-.2-.57-.35z"/>
            </svg>
          </a>

          <!-- Instagram -->
          <a class="social-link" href="https://www.instagram.com/muadhalzadjali?igsh=bWd2NHpod2c2bGY=" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
            <svg viewBox="0 0 32 32">
              <path d="M11 4C7.14 4 4 7.14 4 11v10c0 3.86 3.14 7 7 7h10c3.86 0 7-3.14 7-7V11c0-3.86-3.14-7-7-7H11zm0 2h10c2.77 0 5 2.23 5 5v10c0 2.77-2.23 5-5 5H11c-2.77 0-5-2.23-5-5V11c0-2.77 2.23-5 5-5z"/>
              <path d="M16 10a6 6 0 100 12 6 6 0 000-12zm0 2a4 4 0 110 8 4 4 0 010-8z"/>
              <circle cx="23" cy="9" r="1.2"/>
            </svg>
          </a>

          <!-- Facebook -->
          <a class="social-link" href="https://www.facebook.com/profile.php?id=61587933443449" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
            <svg viewBox="0 0 32 32">
              <path d="M18.5 6H21V2.5h-2.5C14.91 2.5 13 4.41 13 7.75V10H10v3.5h3V29h4.25V13.5H21V10h-3.75V7.75c0-1.02.23-1.75 1.25-1.75z"/>
            </svg>
          </a>
        </div>
        <div class="footer-links" style="margin-top:8px;">
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="cookie-policy.php">Cookie Policy</a>
          <a href="terms.php">Terms of Use</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom-text">
      By continuing to use this site, you acknowledge our use of cookies and our commitment to quality, safety, and client satisfaction.
    </div>
  </div>
</footer>

<div class="scroll-arrow" id="scrollArrow" aria-label="Scroll to top">
  <span>↑</span>
</div>

<div class="question-popup-fixed" id="questionPopup" aria-label="Ask a Question">?</div>

<script>
  const bgLayer    = document.getElementById('bgLayer');
  const slantLines = document.getElementById('slantLines');
  bgLayer.style.backgroundColor = '#22324a';

  function createSlantLines() {
    const colors = ['red','yellow','white'];
    for (let i = 0; i < 18; i++) {
      const div = document.createElement('div');
      div.className = 'slant-line ' + colors[i % colors.length];
      div.style.left = (Math.random() * 120) + '%';
      div.style.top  = (Math.random() * -200) + 'px';
      div.style.animationDelay = (Math.random() * -12) + 's';
      slantLines.appendChild(div);
    }
  }
  createSlantLines();

  const whoToggle = document.getElementById('whoSubToggle');
  const whoMenu   = document.getElementById('whoSubMenu');
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

  const scrollArrow = document.getElementById('scrollArrow');
  function updateScrollArrow() {
    if (window.scrollY > 220) scrollArrow.classList.add('visible');
    else scrollArrow.classList.remove('visible');
  }
  window.addEventListener('scroll', updateScrollArrow);
  updateScrollArrow();
  scrollArrow.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  const questionPopup = document.getElementById('questionPopup');
  questionPopup.addEventListener('click', () => {
    window.location.href = 'ask-question.php';
  });

  document.addEventListener('DOMContentLoaded', function () {
    const map = document.getElementById('mapPlaceholder');
    if (!map) return;
    map.addEventListener('click', function () {
      window.open(
        'https://www.google.com/maps/search/?api=1&query=<?php echo rawurlencode($mapQuery); ?>',
        '_blank'
      );
    });
  });
</script>
</body>
</html>
