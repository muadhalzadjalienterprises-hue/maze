<?php
// insights.php – DB-driven Insights & Articles

require __DIR__ . '/db.php';

// hero
$heroRow = $pdo->query("SELECT * FROM insights_sections WHERE slug='hero' AND is_active=1")->fetch(PDO::FETCH_ASSOC);

// featured
$featured = $pdo->query("SELECT * FROM insights_featured WHERE is_active=1 ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// articles
$articlesStmt = $pdo->query("SELECT * FROM insights_articles WHERE is_active=1 ORDER BY sort_order, id");
$articles = $articlesStmt->fetchAll(PDO::FETCH_ASSOC);

// images
$imgStmt = $pdo->query("SELECT * FROM insights_images WHERE is_active=1 ORDER BY sort_order, id");
$insightsImages = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Insights | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction insights Oman, engineering trends Oman, infrastructure insights Oman">

<meta name="description" content="Read insights and industry trends from Muadh Al Zadjali Engineering & Cont. Enterprises on construction and engineering in Oman.">

<meta name="viewport" content="width=device-width, initial-scale=1">



<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; scroll-behavior: smooth; }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      background: #0b3c5d;
      color: #f5f5f5;
      overflow-x: hidden;
      position: relative;
    }

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

    @keyframes revealUp {
      0% { opacity: 0; transform: translateY(20px) scale(0.98); }
      60% { opacity: 1; transform: translateY(-4px) scale(1.01); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .logo-img {
      width: 62px; height: 62px; object-fit: contain;
      transform-origin: center;
      animation: logoBounce 0.8s ease-out 0.25s forwards;
      opacity: 0;
    }
    @keyframes logoBounce {
      0% { opacity: 0; transform: scale(0.5) rotate(-6deg); }
      60% { opacity: 1; transform: scale(1.05) rotate(2deg); }
      100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }

    .bg-layer {
      position: fixed; inset: 0; z-index: -3;
      background-color: #163b73;
      background-size: cover; background-position: center; background-repeat: no-repeat;
      pointer-events: none;
    }
    .slant-lines {
      position: fixed; inset: -200px; z-index: -2;
      pointer-events: none;
      overflow: hidden;
    }
    .slant-line {
      position: absolute; width: 4px; height: 260px;
      opacity: 0.6; transform: rotate(45deg);
      animation: slantMove 12s linear infinite;
    }
    .slant-line.red    { background: rgba(220, 20, 60, 0.7); }
    .slant-line.yellow { background: rgba(255, 215, 0, 0.7); }
    .slant-line.white  { background: rgba(255, 255, 255, 0.7); }
    @keyframes slantMove {
      0% { transform: translateY(0) rotate(45deg); }
      100% { transform: translateY(400px) rotate(45deg); }
    }

    .site-header {
      background: rgba(255,255,255,0.96);
      border-bottom: 1px solid #e0e0e0;
      padding: 24px 18px;
      display: flex; align-items: center; justify-content: space-between;
      width: 100%; position: relative; z-index: 10; flex-direction: row;
    }
    .logo { display: flex; flex-direction: row; align-items: center; gap: 10px; }
    .logo-text { display: flex; flex-direction: column; line-height: 1.1; text-align: left; }
    .logo-title {
      font-weight: 700; font-size: 17px; color: #830606;
      text-transform: uppercase; letter-spacing: 0.03em;
    }
    .logo-sub {
      font-size: 11px; color: #666;
      text-transform: uppercase; letter-spacing: 0.08em;
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

    main { padding-top: 18px; }

    .page-hero {
      max-width: 1150px; margin: 40px auto 18px; padding: 0 20px;
    }
    .page-hero-inner { max-width: 760px; }
    .tag-label {
      display: inline-block; font-size: 11px; text-transform: uppercase;
      letter-spacing: 0.08em; color: #ffd54f; margin-bottom: 6px;
    }
    .page-hero h1 { font-size: 26px; margin-bottom: 10px; color: #ffffff; }
    .page-hero p {
      font-size: 14px; max-width: 680px;
      color: #e0eef5; line-height: 1.7;
    }

    /* Full-width articles layout */
    .insights-shell {
      max-width: 1150px;
      margin: 0 auto;
      padding: 18px 20px 32px;
    }

    .card {
      background: rgba(0,0,0,0.32);
      border-radius: 10px;
      box-shadow: 0 6px 14px rgba(0,0,0,0.4);
      border: 1px solid rgba(255,255,255,0.08);
      padding: 20px 20px 22px;
      backdrop-filter: blur(4px);
      margin-bottom: 18px;
    }

    .featured-article {
      width: 100%;
      margin-bottom: 22px;
    }
    .featured-article h2 {
      font-size: 22px;
      margin-bottom: 6px;
      color: #fff;
    }
    .meta {
      font-size: 12px;
      color: #c4d6e5;
      margin-bottom: 10px;
    }
    .featured-article p {
      font-size: 14px;
      color: #dde9f1;
      line-height: 1.7;
    }

    .recent-articles {
      width: 100%;
    }
    .recent-articles h2 {
      font-size: 18px;
      margin-bottom: 12px;
      color: #fff;
    }

    .article-item {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding-top: 10px;
      margin-top: 10px;
    }
    .article-item:first-of-type {
      border-top: none;
      padding-top: 0;
      margin-top: 0;
    }
    .article-item h3 {
      font-size: 16px;
      margin-bottom: 4px;
      color: #fff;
    }
    .article-item .meta {
      margin-bottom: 4px;
    }
    .article-item p {
      font-size: 14px;
      color: #dde9f1;
      line-height: 1.6;
    }

    .categories {
      margin-top: 16px;
      display: flex; flex-wrap: wrap; gap: 6px;
    }
    .cat-pill {
      font-size: 12px; padding: 4px 9px; border-radius: 999px;
      background: rgba(0,0,0,0.35); color: #f5f5f5; cursor: pointer;
      border: 1px solid rgba(255,255,255,0.08);
      transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .cat-pill.active {
      background: #ffd54f; color: #1b2838;
      border-color: rgba(0,0,0,0.5);
      box-shadow: 0 3px 10px rgba(0,0,0,0.6);
    }

    /* Diagonal image gallery (kept, right-aligned but independent) */
    .insights-diagonal-wrapper {
      max-width: 1150px;
      margin: 10px auto 0;
      padding: 0 20px 12px;
      display: flex;
      justify-content: flex-end;
    }
    .insights-diagonal {
      width: 260px;
      max-width: 100%;
      transform: rotate(-8deg);
      transform-origin: center;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }
    .insights-diagonal-item {
      transform-origin: center;
    }
    .insights-diagonal-item:nth-child(odd) { transform: translateY(10px); }
    .insights-diagonal-item:nth-child(even){ transform: translateY(-10px); }
    .insights-diagonal-inner {
      position: relative;
      width: 100%; padding-top: 100%;
      border-radius: 16px; overflow: hidden;
      box-shadow: 0 12px 26px rgba(0,0,0,0.55);
      background: rgba(0,0,0,0.4);
    }
    .insights-diagonal-inner img {
      position: absolute; inset: 0;
      width: 100%; height: 100%;
      object-fit: cover;
      transition: transform 0.35s ease, opacity 0.25s ease;
    }
    .insights-diagonal-inner:hover img {
      transform: translate(8px,-8px) scale(1.03);
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
      display: flex; flex-direction: column; gap: 10px;
      text-align: center;
    }
    .footer-links {
      display: flex; justify-content: center;
      gap: 16px; flex-wrap: wrap;
    }
    .footer-links a {
      color: #ffd54f;
      text-decoration: none;
      font-size: 12px;
    }
    .footer-links a:hover { text-decoration: underline; }
    .footer-copy { font-size: 12px; color: #ccc; }

    .scroll-arrow {
      position: fixed; right: 22px; bottom: 26px;
      width: 42px; height: 42px; border-radius: 999px;
      background: rgba(0,0,0,0.55);
      box-shadow: 0 4px 12px rgba(0,0,0,0.45);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; z-index: 40;
      opacity: 0; pointer-events: none;
      transition: opacity 0.25s ease, transform 0.25s ease, background 0.2s ease;
    }
    .scroll-arrow.visible {
      opacity: 1; pointer-events: auto; transform: translateY(0);
    }
    .scroll-arrow:hover { background: rgba(0,0,0,0.75); }
    .scroll-arrow span {
      display: inline-block; color: #ffd54f;
      font-size: 22px; line-height: 1; transform: translateY(-1px);
    }

    /* CONTACT POPUP */
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

    /* Ask Question popup */
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

    @media (max-width: 900px) {
      .site-header {
        padding: 16px 14px;
        flex-direction: column; align-items: flex-start; gap: 8px;
      }
      .insights-diagonal-wrapper {
        justify-content: center;
      }
    }
    @media (max-width: 600px) {
      .scroll-arrow { right: 16px; bottom: 18px; }
      .insights-diagonal { width: 220px; }
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
    <a href="services.php">Services</a>
    <a href="contracts.php">Contracts</a>
    <a href="about.php">About Us</a>

    <div class="nav-item-with-sub">
      <a href="insights.php" class="active">Insights</a>
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
  <section class="page-hero reveal-on-load reveal-delay-2">
    <div class="page-hero-inner">
      <span class="tag-label">Insights &amp; Articles</span>
      <h1><?php echo esc($heroRow['title'] ?? 'Expert perspectives on construction and engineering.'); ?></h1>
      <p><?php echo nl2br(esc($heroRow['content'] ?? 'Read articles, thought leadership, and analysis from our team on safety, technology, sustainability, and market trends in construction.')); ?></p>
    </div>
  </section>

  <?php if ($insightsImages): ?>
  <section class="insights-diagonal-wrapper reveal-on-load reveal-delay-3">
    <div class="insights-diagonal">
      <?php foreach ($insightsImages as $img): ?>
        <?php if (empty($img['primary_image'])) continue; ?>
        <div class="insights-diagonal-item">
          <div class="insights-diagonal-inner">
            <img
              src="<?php echo esc($img['primary_image']); ?>"
              data-original="<?php echo esc($img['primary_image']); ?>"
              data-hover="<?php echo esc($img['hover_image'] ?? ''); ?>"
              alt="">
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- Full-width stacked article sections -->
  <section class="insights-shell reveal-on-load reveal-delay-3">

    <article class="card featured-article">
      <h2><?php echo esc($featured['title'] ?? ''); ?></h2>
      <?php
        $cat  = $featured['category']     ?? '';
        $date = $featured['published_at'] ?? '';
        $min  = $featured['read_minutes'] ?? '';
        $metaParts = [];
        if ($cat)  $metaParts[] = $cat;
        if ($date) $metaParts[] = date('F j, Y', strtotime($date));
        if ($min)  $metaParts[] = $min . ' min read';
      ?>
      <?php if ($metaParts): ?>
        <div class="meta"><?php echo esc(implode(' • ', $metaParts)); ?></div>
      <?php endif; ?>
      <p><?php echo nl2br(esc($featured['summary'] ?? '')); ?></p>
    </article>

    <aside class="card recent-articles">
      <h2>Recent Articles</h2>

      <?php foreach ($articles as $a): ?>
        <?php
          $metaParts = [];
          if (!empty($a['category_label'])) $metaParts[] = $a['category_label'];
          if (!empty($a['published_at']))   $metaParts[] = date('F j, Y', strtotime($a['published_at']));
          if (!empty($a['read_minutes']))   $metaParts[] = $a['read_minutes'] . ' min read';
        ?>
        <div class="article-item" data-cat="<?php echo esc($a['category_slug']); ?>">
          <h3><?php echo esc($a['title']); ?></h3>
          <?php if ($metaParts): ?>
            <div class="meta"><?php echo esc(implode(' • ', $metaParts)); ?></div>
          <?php endif; ?>
          <p><?php echo nl2br(esc($a['excerpt'])); ?></p>
        </div>
      <?php endforeach; ?>

      <div class="categories">
        <span class="cat-pill active" data-filter="all">All</span>
        <span class="cat-pill" data-filter="safety">Safety</span>
        <span class="cat-pill" data-filter="sustainability">Sustainability</span>
        <span class="cat-pill" data-filter="technology">Technology</span>
        <span class="cat-pill" data-filter="business">Business</span>
        <span class="cat-pill" data-filter="design">Design</span>
        <span class="cat-pill" data-filter="workforce">Workforce</span>
      </div>
    </aside>

  </section>
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

<!-- Fixed Ask Question popup -->
<div class="question-popup-fixed" id="questionPopup" aria-label="Ask a Question">?</div>

<script>
  const bgLayer = document.getElementById('bgLayer');
  const slantLines = document.getElementById('slantLines');

  bgLayer.style.backgroundColor = '#163b73';

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

  // Dropdown arrow
  const insightsToggle = document.getElementById('whoSubToggle');
  const insightsMenu   = document.getElementById('whoSubMenu');

  insightsToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = insightsMenu.classList.toggle('open');
    insightsToggle.classList.toggle('open', isOpen);
  });

  document.addEventListener('click', (e) => {
    if (!insightsMenu.contains(e.target) && !insightsToggle.contains(e.target)) {
      insightsMenu.classList.remove('open');
      insightsToggle.classList.remove('open');
    }
  });

  // Category filter
  document.addEventListener('DOMContentLoaded', function () {
    const pills = document.querySelectorAll('.cat-pill');
    const items = document.querySelectorAll('.article-item');

    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        const filter = pill.getAttribute('data-filter');
        pills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');

        items.forEach(function (item) {
          if (filter === 'all') {
            item.style.display = '';
          } else {
            const cat = item.getAttribute('data-cat');
            item.style.display = (cat === filter) ? '' : 'none';
          }
        });
      });
    });
  });

  // Scroll-to-top arrow
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

  // Diagonal image hover swap
  document.querySelectorAll('.insights-diagonal-inner img').forEach(img => {
    const hover = img.dataset.hover;
    const original = img.dataset.original;
    if (!hover) return;

    img.addEventListener('mouseenter', () => {
      img.style.opacity = '0';
      setTimeout(() => {
        img.src = hover;
        img.style.opacity = '1';
      }, 120);
    });

    img.addEventListener('mouseleave', () => {
      img.style.opacity = '0';
      setTimeout(() => {
        img.src = original;
        img.style.opacity = '1';
      }, 120);
    });
  });

  // CONTACT POPUP
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

  // Ask Question popup
  const questionPopup = document.getElementById('questionPopup');
  questionPopup.addEventListener('click', () => {
    window.location.href = 'ask-question.php';
  });
</script>

</body>
</html>
