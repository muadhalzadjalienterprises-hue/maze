<?php
// index.php – Home (zig-zag with animations, cookies, bg/image change, hamburger)
// Loader stays on, then all reveal/pop animations and typing start AFTER loader hides.
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction company Oman, engineering contractors Oman, infrastructure services Oman">

<meta name="description" content="Muadh Al Zadjali Engineering & Cont. Enterprises provides professional construction, engineering, and infrastructure services across Oman.">

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      height: 100%;
      scroll-behavior: smooth;
    }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      color: #222;
      background: #0b3c5d;
      overflow-x: hidden;
      position: relative;
    }

    /* GLOBAL ENTRANCE ANIMATIONS (disabled initially via .no-anim on body) */
    .no-anim .reveal-on-load,
    .no-anim .pop-on-load,
    .no-anim .logo-img {
      opacity: 0 !important;
      transform: none !important;
      animation: none !important;
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
    .reveal-delay-6 { animation-delay: 0.6s; }

    @keyframes revealUp {
      0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
      60%  { opacity: 1; transform: translateY(-4px) scale(1.01); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Slight pop for CTA / important elements */
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

    /* Logo bounce (like other pages) */
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
      100% { opacity: 1; transform: scale(1) rotate(0); }
    }

    /* GLOBAL CONTENT WRAPPER WITH SAFE PADDING (main only) */
    .page-shell {
      padding-left: 18px;
      padding-right: 18px;
    }
    main {
      padding-top: 14px;
    }

    /* Header */
    header.site-header {
      background: rgba(255,255,255,0.96);
      backdrop-filter: blur(6px);
      border-bottom: 1px solid #e0e0e0;
      padding-top: 24px;
      padding-bottom: 24px;
      width: 100%;
      position: relative;
      z-index: 10;
    }
    .header-inner {
      max-width: 1150px;
      margin: 0 auto;
      padding: 0 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    @media (max-width: 900px) {
      .page-shell {
        padding-left: max(18px, env(safe-area-inset-left, 0px));
        padding-right: max(18px, env(safe-area-inset-right, 0px));
      }
      .header-inner {
        padding-left: max(18px, env(safe-area-inset-left, 0px));
        padding-right: max(18px, env(safe-area-inset-right, 0px));
      }
      .footer-inner {
        padding-left: max(18px, env(safe-area-inset-left, 0px));
        padding-right: max(18px, env(safe-area-inset-right, 0px));
      }
    }

    /* Global background layer */
    .bg-layer {
      position: fixed;
      inset: 0;
      z-index: -3;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      transition: background-image 0.6s ease, background-color 0.6s ease;
    }

    /* Diagonal slanting lines */
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

    .header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .logo-title {
      font-weight: 700;
      font-size: 17px;
      color: #820404;
    }
    .logo-sub {
      font-size: 11px;
      color: #666;
    }

    /* Desktop nav */
    .header-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .main-nav {
      display: flex;
      gap: 16px;
      align-items: center;
    }
    .nav-link {
      position: relative;
      text-decoration: none;
      color: #333;
      font-size: 14px;
      padding: 4px 0;
    }
    .nav-link::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -2px;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #850303, #850808);
      transition: width 0.25s ease;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
      width: 100%;
    }
    .nav-link:hover {
      color: #d10606;
    }

    /* Menu arrow button (scroll/open overlay) */
    .menu-arrow {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 26px;
      height: 26px;
      border-radius: 999px;
      border: 1px solid rgba(0,0,0,0.3);
      background: rgba(255,255,255,0.8);
      cursor: pointer;
      font-size: 16px;
      color: #333;
      transition: background 0.2s ease, transform 0.2s ease;
    }
    .menu-arrow:hover {
      background: #ffd54f;
      transform: translateY(-1px);
    }

    /* Hamburger */
    .hamburger {
      display: flex;
      width: 26px;
      height: 20px;
      flex-direction: column;
      justify-content: space-between;
      cursor: pointer;
      margin-left: 6px;
    }
    .hamburger span {
      display: block;
      height: 3px;
      border-radius: 999px;
      background: #333;
      transition: transform 0.25s ease, opacity 0.25s ease;
    }
    .hamburger.active span:nth-child(1) {
      transform: translateY(8.5px) rotate(45deg);
    }
    .hamburger.active span:nth-child(2) {
      opacity: 0;
    }
    .hamburger.active span:nth-child(3) {
      transform: translateY(-8.5px) rotate(-45deg);
    }

    @media (max-width: 768px) {
      .main-nav {
        display: none;
      }
      .section {
        margin: 32px auto;
      }
      .hero-title {
        font-size: 24px;
      }
      .hero-sub {
        font-size: 14px;
      }
    }

    /* Hamburger overlay menu */
    .menu-overlay {
      position: fixed;
      inset: 0;
      background: rgba(11,60,93,0.96);
      color: #fff;
      transform: translateY(-100%);
      transition: transform 0.25s ease;
      z-index: 900;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 60px;
    }
    .menu-overlay.open {
      transform: translateY(0);
    }
    .menu-inner {
      max-width: 480px;
      width: 100%;
      margin: 0 auto;
      padding: 0 18px 16px;
      display: flex;
      flex-direction: column;
      gap: 14px;
    }
    .menu-list {
      list-style: none;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .menu-item a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 10px;
      border-radius: 6px;
      text-decoration: none;
      color: #fff;
      font-size: 14px;
      border: 1px solid rgba(255,255,255,0.18);
      background: rgba(255,255,255,0.05);
    }
    .menu-item a:hover {
      background: rgba(255,255,255,0.18);
    }
    .menu-item-icon {
      width: 18px;
      height: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .menu-item-icon svg {
      width: 18px;
      height: 18px;
      fill: currentColor;
    }

    /* Overlay footer social (same links as page footer) */
    .menu-footer {
      border-top: 1px solid rgba(255,255,255,0.2);
      padding-top: 10px;
      margin-top: 8px;
      font-size: 12px;
      color: #e0eef5;
    }
    .menu-social-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
      margin-top: 6px;
    }
    .menu-social-label {
      font-size: 12px;
    }
    .menu-social-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.22);
      text-decoration: none;
      transition: background 0.2s ease, transform 0.2s ease;
    }
    .menu-social-link svg {
      width: 16px;
      height: 16px;
      fill: #ffffff;
    }
    .menu-social-link:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-1px);
    }

    /* Sections */
    .section {
      min-height: 80vh;
      max-width: 1150px;
      margin: 50px auto;
      display: grid;
      gap: 24px;
      align-items: center;
      position: relative;
    }

    .section[data-side="left"] {
      grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.3fr);
    }
    .section[data-side="left"] .section-content {
      order: 1;
      text-align: left;
    }
    .section[data-side="left"] .section-label {
      order: 2;
      justify-content: flex-end;
      text-align: right;
    }

    .section[data-side="right"] {
      grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.3fr);
    }
    .section[data-side="right"] .section-content {
      order: 2;
      text-align: left;
    }
    .section[data-side="right"] .section-label {
      order: 1;
      justify-content: flex-start;
      text-align: left;
    }

    .section-content {
      color: #ffffff;
    }
    .section-label {
      color: #ffffff;
      display: flex;
      align-items: center;
    }

    .section.small {
      min-height: 60vh;
    }

    .section h2 {
      font-size: 26px;
      margin-bottom: 10px;
    }
    .section p {
      font-size: 14px;
      max-width: 520px;
      line-height: 1.6;
      color: #f1f1f1;
    }
    .section-tag {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #ffd54f;
      margin-bottom: 4px;
    }
    .section-link-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      font-size: 13px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,0.6);
      color: #ffffff;
      text-decoration: none;
      background: rgba(255,255,255,0.05);
      transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      white-space: nowrap;
    }
    .section-link-pill span.arrow {
      font-size: 14px;
    }
    .section-link-pill:hover {
      background: rgba(255,255,255,0.18);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }

    /* Hero with typing animation for company name */
    .hero-company {
      font-size: 18px !important;
      margin-bottom: 6px;
      letter-spacing: 0.03em;
    }

    .hero-title {
      font-size: 22px !important;
      margin-bottom: 10px;
      white-space: nowrap;
      overflow: hidden;
      border-right: 2px solid #ffd54f;
    }

    /* Arabic title: allow wrapping / line by line on mobile */
    #heroTitle {
      white-space: normal;
      line-height: 1.5;
    }
    @media (min-width: 769px) {
      #heroTitle {
        white-space: nowrap;
      }
    }

    .hero-sub {
      font-size: 13px !important;
      margin-bottom: 14px;
      max-width: 550px;
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
      z-index: 90;
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

    @media (max-width: 600px) {
      .scroll-arrow {
        right: 16px;
        bottom: 18px;
      }
    }

    /* Footer */
    footer {
      margin-top: 40px;
      width: 100%;
      background: rgba(0,0,0,0.9);
      color: #eee;
      border-top: 1px solid rgba(255,255,255,0.1);
      padding: 26px 0 32px;
    }
    .footer-inner {
      max-width: 1150px;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      font-size: 13px;
    }
    .footer-main {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }
    .footer-left {
      max-width: 420px;
      line-height: 1.5;
    }
    .footer-links a {
      color: #ffd54f;
      margin-left: 14px;
      text-decoration: none;
    }
    .footer-links a:hover {
      text-decoration: underline;
    }
    .footer-social-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .footer-social-label {
      font-size: 13px;
      color: #ddd;
      margin-right: 6px;
    }
    .social-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.22);
      text-decoration: none;
      transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    .social-link svg {
      width: 18px;
      height: 18px;
      fill: #ffffff;
    }
    .social-link:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }
    .footer-bottom-text {
      font-size: 12px;
      color: #ccc;
      line-height: 1.6;
    }

    @media (max-width: 900px) {
      .section {
        grid-template-columns: minmax(0, 1fr);
        text-align: left;
      }
      .section[data-side="left"] .section-content,
      .section[data-side="right"] .section-content {
        order: 1;
        text-align: left;
      }
      .section[data-side="left"] .section-label,
      .section[data-side="right"] .section-label {
        order: 2;
        justify-content: flex-start;
        text-align: left;
      }
      .footer-main {
        flex-direction: column;
        align-items: flex-start;
      }
      .footer-links {
        margin-top: 4px;
      }
    }

    /* Cookie banner */
    .cookie-banner {
      position: fixed;
      bottom: 16px;
      left: 50%;
      transform: translateX(-50%);
      max-width: 480px;
      width: calc(100% - 32px);
      background: #ffffff;
      color: #333;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.25);
      padding: 14px 16px;
      display: none;
      z-index: 200;
      font-size: 13px;
    }
    .cookie-banner p {
      margin-bottom: 8px;
    }
    .cookie-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      flex-wrap: wrap;
    }
    .cookie-btn {
      border-radius: 999px;
      border: none;
      padding: 6px 12px;
      cursor: pointer;
      font-size: 13px;
    }
    .cookie-accept {
      background: #0b3c5d;
      color: #fff;
    }
    .cookie-reject {
      background: #e0e0e0;
      color: #333;
    }

    /* SPLASH LOADER */
    .loader-overlay {
      position: fixed;
      inset: 0;
      background: #0b3c5d;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      transition: opacity 0.35s ease, visibility 0.35s ease;
    }
    .loader-overlay.hidden {
      opacity: 0;
      visibility: hidden;
    }
    .loader-logo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      overflow: hidden;
      margin-bottom: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.4);
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .loader-logo img {
      width: 80%;
      height: 80%;
      object-fit: contain;
    }
    .loader-text {
      color: #fff;
      font-size: 15px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      margin-bottom: 16px;
      text-align: center;
    }
    .loader-spinner {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.25);
      border-top-color: #ffd54f;
      animation: spinLoader 1s linear infinite;
    }
    @keyframes spinLoader {
      to { transform: rotate(360deg); }
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

    /* Fixed Ask Question popup (question mark) */
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
<body class="no-anim">

<!-- SPLASH LOADER -->
<div class="loader-overlay" id="loaderOverlay">
  <div class="loader-logo">
    <img src="maze.jpg" alt="Loading">
  </div>
  <div class="loader-text">MUADH AL ZADJALI</div>
  <div class="loader-spinner"></div>
</div>

<div class="bg-layer" id="bgLayer"></div>
<div class="slant-lines" id="slantLines"></div>

<header class="site-header reveal-on-load reveal-delay-1">
  <div class="header-inner">
    <div class="header-left">
      <img src="maze.jpg" alt="Company Logo" class="logo-img">
      <div class="logo">
        <span class="logo-title">MUADH AL ZADJALI</span>
        <span class="logo-sub">ENGINEERING &amp; CONT. ENTERPRISES</span>
      </div>
    </div>
    <div class="header-right">
      <nav class="main-nav reveal-on-load reveal-delay-2">
        <a href="#sec-hero" class="nav-link active">Home</a>
        <a href="#sec-services" class="nav-link">Services</a>
        <a href="#sec-contracts" class="nav-link">Contracts</a>
        <a href="#sec-about" class="nav-link">About Us</a>
      </nav>
      <button type="button" class="menu-arrow" id="menuArrow" aria-label="Open menu overlay">→</button>
      <div class="hamburger pop-on-load reveal-delay-3" id="hamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
</header>

<div class="menu-overlay" id="menuOverlay">
  <div class="menu-inner">
    <ul class="menu-list">
      <li class="menu-item">
        <a href="#sec-hero">
          <span class="menu-item-icon">
            <!-- Home icon -->
            <svg viewBox="0 0 24 24">
              <path d="M12 3l9 8h-3v9h-5v-6H11v6H6v-9H3z"/>
            </svg>
          </span>
          <span>Home</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-services">
          <span class="menu-item-icon">
            <!-- Services / tools icon -->
            <svg viewBox="0 0 24 24">
              <path d="M21 14.35l-2.17-.31a4.28 4.28 0 00-.76-1.83l1.29-1.79-2.12-2.12-1.79 1.29a4.28 4.28 0 00-1.83-.76L13.65 3h-3.3l-.31 2.17a4.28 4.28 0 00-1.83.76L6.42 4.64 4.3 6.76l1.29 1.79a4.28 4.28 0 00-.76 1.83L3 13.65v3.3l2.17.31a4.28 4.28 0 00.76 1.83l-1.29 1.79 2.12 2.12 1.79-1.29a4.28 4.28 0 001.83.76L13.65 21h3.3l.31-2.17a4.28 4.28 0 001.83-.76l1.79 1.29 2.12-2.12-1.29-1.79a4.28 4.28 0 00.76-1.83L21 13.65v.7zM12 16a4 4 0 114-4 4 4 0 01-4 4z"/>
            </svg>
          </span>
          <span>Services</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-contracts">
          <span class="menu-item-icon">
            <!-- Contracts / document icon -->
            <svg viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
              <path d="M14 2v6h6"/>
              <path d="M8 13h8M8 17h5" stroke="#fff" stroke-width="1.2" fill="none"/>
            </svg>
          </span>
          <span>Contracts</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-about">
          <span class="menu-item-icon">
            <!-- About / info icon -->
            <svg viewBox="0 0 24 24">
              <path d="M12 2a10 10 0 1010 10A10.011 10.011 0 0012 2zm0 4a1.5 1.5 0 11-1.5 1.5A1.5 1.5 0 0112 6zm2 12h-4v-1.5h1.25V11.5H10V10h4v6.5H14z"/>
            </svg>
          </span>
          <span>About Us</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-who">
          <span class="menu-item-icon">
            <!-- Insights / lightbulb -->
            <svg viewBox="0 0 24 24">
              <path d="M9 21h6v-1H9zM12 2a7 7 0 00-4 12.74V17h8v-2.26A7 7 0 0012 2z"/>
            </svg>
          </span>
          <span>Insights</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-insights">
          <span class="menu-item-icon">
            <!-- Chart / outlook -->
            <svg viewBox="0 0 24 24">
              <path d="M3 3v18h18v-2H5V3z"/>
              <path d="M9 17l3-5 2 3 4-7" stroke="#fff" stroke-width="1.4" fill="none"/>
            </svg>
          </span>
          <span>Industry Outlook</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-industry">
          <span class="menu-item-icon">
            <!-- Location / pin -->
            <svg viewBox="0 0 24 24">
              <path d="M12 2a7 7 0 00-7 7c0 4.25 7 13 7 13s7-8.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1114.5 9 2.5 2.5 0 0112 11.5z"/>
            </svg>
          </span>
          <span>Location</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-location">
          <span class="menu-item-icon">
            <!-- Phone / contact -->
            <svg viewBox="0 0 24 24">
              <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.36 11.36 0 003.56.57 1 1 0 011 1v3.54a1 1 0 01-.91 1A17.91 17.91 0 013 4.91 1 1 0 014 4h3.55a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.25 1.01z"/>
            </svg>
          </span>
          <span>Contact</span>
        </a>
      </li>
      <li class="menu-item">
        <a href="#sec-contact">
          <span class="menu-item-icon">
            <!-- Question mark -->
            <svg viewBox="0 0 24 24">
              <path d="M11.95 2a8 8 0 108 8 8 8 0 00-8-8zm.05 14a1.25 1.25 0 111.25-1.25A1.25 1.25 0 0112 16zm1.68-5.21l-.78.7A1.68 1.68 0 0012.5 13h-1.5v-.38a2.59 2.59 0 01.86-1.94l1.09-.98a1.4 1.4 0 00.47-1.06A1.64 1.64 0 0011.59 6a1.72 1.72 0 00-1.91 1.46L9.6 8.35H8a3.58 3.58 0 016.93-.8 3 3 0 01-.25 2.24z"/>
            </svg>
          </span>
          <span>Ask a Question</span>
        </a>
      </li>
    </ul>

    <!-- Footer-style social and links replicated in menu -->
    <div class="menu-footer">
      <div>Connect with us:</div>
      <div class="menu-social-row">
        <!-- WhatsApp -->
        <a class="menu-social-link" href="https://wa.me/96895789594" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
          <svg viewBox="0 0 32 32">
            <path d="M16 3C9.373 3 4 8.373 4 15c0 2.115.551 4.099 1.602 5.9L4 29l8.313-1.567C13.987 28.469 14.983 28.7 16 28.7 22.627 28.7 28 23.327 28 16.7 28 10.073 22.627 4.7 16 4.7zm0 22.4c-.87 0-1.722-.17-2.53-.506l-.18-.076-4.93.93.94-4.81-.12-.19C8.43 19.1 8 17.58 8 16.01 8 10.94 11.94 7 17.01 7 22.08 7 26 10.94 26 16.01 26 21.08 22.08 25.4 17.01 25.4z"/>
            <path d="M21.4 18.73c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.66.15-.2.29-.76.96-.93 1.16-.17.2-.34.22-.64.07-.3-.15-1.28-.47-2.44-1.5-.9-.8-1.5-1.8-1.68-2.1-.18-.3-.02-.46.13-.61.14-.14.3-.36.45-.54.15-.18.2-.29.3-.48.1-.19.05-.36-.02-.51-.07-.15-.66-1.6-.91-2.19-.24-.58-.49-.5-.66-.51l-.56-.01c-.19 0-.5.07-.76.35-.26.29-1 1-1 2.46 0 1.46 1.03 2.87 1.18 3.07.15.2 2.02 3.18 4.9 4.46.68.29 1.21.47 1.62.61.68.22 1.3.19 1.79.11.55-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.18-1.42-.07-.12-.27-.2-.57-.35z"/>
          </svg>
        </a>

        <!-- Instagram -->
        <a class="menu-social-link" href="https://www.instagram.com/muadhalzadjali?igsh=bWd2NHpod2c2bGY=" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
          <svg viewBox="0 0 32 32">
            <path d="M11 4C7.14 4 4 7.14 4 11v10c0 3.86 3.14 7 7 7h10c3.86 0 7-3.14 7-7V11c0-3.86-3.14-7-7-7H11zm0 2h10c2.77 0 5 2.23 5 5v10c0 2.77-2.23 5-5 5H11c-2.77 0-5-2.23-5-5V11c0-2.77 2.23-5 5-5z"/>
            <path d="M16 10a6 6 0 100 12 6 6 0 000-12zm0 2a4 4 0 110 8 4 4 0 010-8z"/>
            <circle cx="23" cy="9" r="1.2"/>
          </svg>
        </a>

        <!-- Facebook -->
        <a class="menu-social-link" href="https://www.facebook.com/profile.php?id=61587933443449" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
          <svg viewBox="0 0 32 32">
            <path d="M18.5 6H21V2.5h-2.5C14.91 2.5 13 4.41 13 7.75V10H10v3.5h3V29h4.25V13.5H21V10h-3.75V7.75c0-1.02.23-1.75 1.25-1.75z"/>
          </svg>
        </a>
      </div>
      <div style="margin-top:8px;">
        <a href="privacy-policy.php" style="color:#ffd54f; text-decoration:none; margin-right:10px;">Privacy Policy</a>
        <a href="cookie-policy.php" style="color:#ffd54f; text-decoration:none;">Cookie Policy</a>
      </div>
    </div>
  </div>
</div>

<!-- Scroll-to-top arrow -->
<div class="scroll-arrow" id="scrollArrow" aria-label="Scroll to top">
  <span>↑</span>
</div>

<!-- main content with side padding -->
<div class="page-shell">
<main>
  <!-- HERO / HOME -->
  <section class="section reveal-on-load reveal-delay-4" id="sec-hero" data-index="0" data-side="left">
    <div class="section-content">
      <div class="section-tag">Home</div>
      <div class="hero-company" id="heroCompany"></div>
      <h2 class="hero-title" id="heroTitle"></h2>
      <p class="hero-sub">Building excellence, one project at a time, across Oman and beyond, with a focus on safety, reliability, and engineering precision.</p>
      <p>Explore our services, learn who we are, and discover insights into the construction industry—all from this single page before visiting each detailed section.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-5" href="#sec-services">
        Services Overview
        <span class="arrow">↓</span>
      </a>
    </div>
  </section>

  <!-- SERVICES (left) -->
  <section class="section reveal-on-load reveal-delay-4" id="sec-services" data-index="1" data-side="left">
    <div class="section-content">
      <div class="section-tag">Services</div>
      <h2>Comprehensive engineering &amp; construction services.</h2>
      <p>From residential developments to large-scale commercial and industrial projects, our team delivers end‑to‑end solutions including design‑build, general contracting, and project management.</p>
      <p style="margin-top:6px;">View detailed descriptions for residential, commercial, industrial, and general contracting services on the dedicated Services page.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-5" href="services.php">
        Go to Services
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- CONTRACTS (right) -->
  <section class="section reveal-on-load reveal-delay-5" id="sec-contracts" data-index="2" data-side="right">
    <div class="section-content">
      <div class="section-tag">Contracts &amp; Agreements</div>
      <h2>Flexible contract models for different project needs.</h2>
      <p>Choose from fixed price, cost plus, time &amp; materials, or design‑build agreements to align with project complexity, risk profile, and budget control.</p>
      <p style="margin-top:6px;">Each contract type is clearly explained with key benefits to help you select the most suitable structure for your project.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-6" href="contracts.php">
        View Contract Types
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- ABOUT (left) -->
  <section class="section reveal-on-load reveal-delay-4" id="sec-about" data-index="3" data-side="left">
    <div class="section-content">
      <div class="section-tag">About Us</div>
      <h2>Decades of experience and trusted partnerships.</h2>
      <p>Learn about our history, milestones, and key statistics, including projects delivered, team size, and years of experience in the engineering and construction sector.</p>
      <p style="margin-top:6px;">The About page highlights our story, growth, and reputation for quality and reliability.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-5" href="about.php">
        Read Our Story
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- INSIGHTS (right) -->
  <section class="section reveal-on-load reveal-delay-5" id="sec-who" data-index="4" data-side="right">
    <div class="section-content">
      <div class="section-tag">Insights</div>
      <h2>Insights &amp; articles from our experts.</h2>
      <p>Read about safety best practices, sustainable materials, BIM technology, cost strategies, and workforce trends in our Insights section.</p>
      <p style="margin-top:6px;">You will also see what drives us: passion for building, commitment to safety, and environmental responsibility.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-6" href="insights.php">
        Explore Insights
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- INDUSTRY OUTLOOK (left, small) -->
  <section class="section small reveal-on-load reveal-delay-4" id="sec-insights" data-index="5" data-side="left">
    <div class="section-content">
      <div class="section-tag">Industry Outlook</div>
      <h2>Trends and forecasts in construction.</h2>
      <p>Follow market growth projections, technology adoption, sustainability, and labor market indicators in the Industry Outlook page.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-5" href="industry.php">
        View Industry Outlook
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- LOCATION (right, small) -->
  <section class="section small reveal-on-load reveal-delay-5" id="sec-industry" data-index="6" data-side="right">
    <div class="section-content">
      <div class="section-tag">Location</div>
      <h2>Find our main office and service areas.</h2>
      <p>See maps, address details, business hours, and primary service regions on the Location page, including the Ruwi, Muscat head office.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-6" href="location.php">
        View Location
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- CONTACT (left, small) -->
  <section class="section small reveal-on-load reveal-delay-4" id="sec-location" data-index="7" data-side="left">
    <div class="section-content">
      <div class="section-tag">Contact</div>
      <h2>Contact our team about your project.</h2>
      <p>Use the Contact page to send a message, ask for estimates, or schedule a meeting at our office.</p>

      <p style="margin-top:10px;">
        Phone:
        <a href="tel:+9685789594" style="color:#ffd54f; text-decoration:none;">+968 578 9594</a><br>
        Email:
        <a href="mailto:muadhalzadjalienterprises@gmail.com" style="color:#ffd54f; text-decoration:none;">muadhalzadjalienterprises@gmail.com</a>
      </p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-5" href="contact.php">
        Go to Contact Page
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

  <!-- ASK A QUESTION (right, small) -->
  <section class="section small reveal-on-load reveal-delay-5" id="sec-contact" data-index="8" data-side="right">
    <div class="section-content">
      <div class="section-tag">Ask a Question &amp; FAQ</div>
      <h2>Submit questions and read answers.</h2>
      <p>The Ask a Question page lets you submit queries about timelines, methods, or working with us, plus review common FAQs.</p>
    </div>
    <div class="section-label">
      <a class="section-link-pill pop-on-load reveal-delay-6" href="ask-question.php">
        Ask a Question
        <span class="arrow">↗</span>
      </a>
    </div>
  </section>

</main>
</div><!-- end .page-shell -->

<!-- FOOTER -->
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
        </div>
      </div>
    </div>
    <div class="footer-bottom-text">
      By continuing to use this site, you acknowledge our use of cookies and our commitment to quality, safety, and client satisfaction.
    </div>
  </div>
</footer>

<div class="cookie-banner" id="cookieBanner">
  <p>We use cookies to enhance your browsing experience and analyze site usage. You can accept or reject non‑essential cookies.</p>
  <div class="cookie-actions">
    <button class="cookie-btn cookie-reject" id="cookieReject">Reject</button>
    <button class="cookie-btn cookie-accept" id="cookieAccept">Accept</button>
  </div>
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

<!-- FIXED ASK QUESTION POPUP -->
<div class="question-popup-fixed" id="questionPopup" aria-label="Ask a Question">?</div>

<script>
  const sections = Array.from(document.querySelectorAll('.section'));
  const bgLayer = document.getElementById('bgLayer');
  const slantLines = document.getElementById('slantLines');
  const navLinks = Array.from(document.querySelectorAll('.nav-link'));

  // Background configurations
  const sectionBackgrounds = [
    { color: '#0b3c5d', image: '' },
    { color: '#163b73', image: '' },
    { color: '#3a2d6d', image: '' },
    { color: '#5b2834', image: '' },
    { color: '#274a36', image: '' },
    { color: '#24455a', image: '' },
    { color: '#4b3a2b', image: '' },
    { color: '#22324a', image: '' },
    { color: '#263238', image: '' },
    { color: '#1c2833', image: '' }
  ];
  function applyBackgroundByIndex(idx) {
    const conf = sectionBackgrounds[idx] || sectionBackgrounds[0];
    if (conf.image) {
      bgLayer.style.backgroundImage = 'url(' + conf.image + ')';
    } else {
      bgLayer.style.backgroundImage = 'none';
    }
    bgLayer.style.backgroundColor = conf.color;
  }

  let currentIndex = 0;
  function setActiveSection(idx) {
    currentIndex = idx;
    applyBackgroundByIndex(idx);

    navLinks.forEach(link => link.classList.remove('active'));
    if (idx === 0) {
      const home = navLinks.find(l => l.getAttribute('href') === '#sec-hero');
      if (home) home.classList.add('active');
    } else if (idx === 1) {
      const s = navLinks.find(l => l.getAttribute('href') === '#sec-services');
      if (s) s.classList.add('active');
    } else if (idx === 2) {
      const c = navLinks.find(l => l.getAttribute('href') === '#sec-contracts');
      if (c) c.classList.add('active');
    } else if (idx === 3) {
      const a = navLinks.find(l => l.getAttribute('href') === '#sec-about');
      if (a) a.classList.add('active');
    } else if (idx >= 4) {
      const a = navLinks.find(l => l.getAttribute('href') === '#sec-about');
      if (a) a.classList.add('active');
    }

    slantLines.setAttribute('data-shift', (idx % 2).toString());
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const idx = parseInt(entry.target.getAttribute('data-index'), 10);
        setActiveSection(idx);
      }
    });
  }, { threshold: 0.5 });

  sections.forEach(sec => observer.observe(sec));
  applyBackgroundByIndex(0);

  // Generate slanting lines
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

  // Cookie banner logic
  const cookieBanner = document.getElementById('cookieBanner');
  const cookieKey = 'muadh_cookie_choice';
  function checkCookieChoice() {
    const choice = localStorage.getItem(cookieKey);
    if (!choice) {
      cookieBanner.style.display = 'block';
    }
  }
  document.getElementById('cookieAccept').addEventListener('click', function () {
    localStorage.setItem(cookieKey, 'accept');
    cookieBanner.style.display = 'none';
  });
  document.getElementById('cookieReject').addEventListener('click', function () {
    localStorage.setItem(cookieKey, 'reject');
    cookieBanner.style.display = 'none';
  });
  checkCookieChoice();

  // Hamburger and overlay menu
  const hamburger = document.getElementById('hamburger');
  const menuOverlay = document.getElementById('menuOverlay');
  const menuList = menuOverlay.querySelector('.menu-list');
  const menuArrow = document.getElementById('menuArrow');

  function closeMenu() {
    hamburger.classList.remove('active');
    menuOverlay.classList.remove('open');
  }
  function openMenu() {
    hamburger.classList.add('active');
    menuOverlay.classList.add('open');
  }

  hamburger.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = menuOverlay.classList.contains('open');
    if (isOpen) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  // Arrow opens menu overlay and scrolls viewport slightly
  menuArrow.addEventListener('click', () => {
    openMenu();
  });

  // Close on any link click inside the menu
  menuOverlay.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      closeMenu();
    });
  });

  // Close when clicking anywhere outside the menu list and hamburger
  document.addEventListener('click', (event) => {
    const isMenuOpen = menuOverlay.classList.contains('open');
    if (!isMenuOpen) return;

    const clickedInsideMenu = menuList.contains(event.target);
    const clickedHamburger = hamburger.contains(event.target);

    if (!clickedInsideMenu && !clickedHamburger) {
      closeMenu();
    }
  });

  // Typing animation for company name + Arabic title
  const heroCompanyEl = document.getElementById('heroCompany');
  const heroTitleEl = document.getElementById('heroTitle');
  const companyText = 'Muadh Al Zadjali Engineering & Cont. Enterprises';
  const titleText = 'مؤسسة معاذ الزدجالي للهندسة والمقاولات';
  let companyIdx = 0;
  let titleIdx = 0;

  function typeCompany() {
    if (companyIdx <= companyText.length) {
      heroCompanyEl.textContent = companyText.slice(0, companyIdx);
      companyIdx++;
      setTimeout(typeCompany, 40);
    } else {
      typeTitle();
    }
  }
  function typeTitle() {
    if (titleIdx <= titleText.length) {
      heroTitleEl.textContent = titleText.slice(0, titleIdx);
      titleIdx++;
      setTimeout(typeTitle, 35);
    } else {
      heroTitleEl.style.borderRight = 'none';
    }
  }

  // Loader hide + THEN enable animations + start typing
  window.addEventListener('load', () => {
    const loader = document.getElementById('loaderOverlay');

    setTimeout(() => {
      loader.classList.add('hidden');

      // Enable all reveal / pop / logo animations AFTER loader is gone
      document.body.classList.remove('no-anim');

      // Reset and start typing animation
      heroCompanyEl.textContent = '';
      heroTitleEl.textContent = '';
      companyIdx = 0;
      titleIdx = 0;
      typeCompany();
    }, 1000);
  });

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

  // Question popup -> Ask a Question page
  const questionPopup = document.getElementById('questionPopup');
  questionPopup.addEventListener('click', () => {
    window.location.href = 'ask-question.php';
  });
</script>

</body>
</html>
