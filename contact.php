<?php
// contact.php – Contact Us (details only, no form)

require __DIR__ . '/db.php';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Load sections (hero and visit note are still useful)
function get_section($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM contact_sections WHERE slug = :slug AND is_active = 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['title' => '', 'content' => ''];
}

$hero      = get_section($pdo, 'hero');
$visitNote = get_section($pdo, 'visit_note');

// Load details
$detailsStmt = $pdo->query("SELECT * FROM contact_details WHERE is_active = 1");
$detailsRaw  = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
$details = [];
foreach ($detailsRaw as $d) {
    $details[$d['label']] = $d;
}

// Load social links
$socialStmt = $pdo->query("SELECT label, url FROM contact_social_links WHERE is_active = 1");
$socialRaw  = $socialStmt->fetchAll(PDO::FETCH_ASSOC);
$social = [];
foreach ($socialRaw as $row) {
    $social[$row['label']] = $row['url'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Contact | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="contact construction company Oman, engineering company Oman contact">

<meta name="description" content="Contact Muadh Al Zadjali Engineering & Cont. Enterprises for construction and engineering services in Oman.">

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
      background: #0b3c5d;
      color: #f5f5f5;
      overflow-x: hidden;
      position: relative;
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

    @keyframes revealUp {
      0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
      60%  { opacity: 1; transform: translateY(-4px) scale(1.01); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
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
      100% { opacity: 1; transform: scale(1) rotate(0); }
    }

    /* Static animated slant background */
    .bg-layer {
      position: fixed;
      inset: 0;
      z-index: -3;
      background-color: #163b73;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
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
    .slant-line.red    { background: rgba(220, 20, 60, 0.7); }
    .slant-line.yellow { background: rgba(255, 215, 0, 0.7); }
    .slant-line.white  { background: rgba(255, 255, 255, 0.7); }

    @keyframes slantMove {
      0%   { transform: translateY(0) rotate(45deg); }
      100% { transform: translateY(400px) rotate(45deg); }
    }

    /* Header – same style as Industry/Insights */
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

    main { padding-top: 18px; }

    /* Hero */
    .page-hero {
      max-width: 1150px;
      margin: 40px auto 18px;
      padding: 0 20px;
    }
    .page-hero-inner {
      max-width: 760px;
    }
    .tag-label {
      display: inline-block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #ffd54f;
      margin-bottom: 6px;
    }
    .page-hero h1 {
      font-size: 26px;
      margin-bottom: 8px;
      color: #ffffff;
    }
    .page-hero p {
      font-size: 14px;
      max-width: 650px;
      color: #e0eef5;
      line-height: 1.7;
    }

    /* Contact layout – single card centered on desktop */
    .contact-wrap {
      max-width: 700px;
      margin: 0 auto;
      padding: 24px 20px 40px;
    }

    .contact-info {
      background: rgba(0,0,0,0.32);
      padding: 18px 18px 20px;
      border-radius: 10px;
      box-shadow: 0 6px 14px rgba(0,0,0,0.35);
      border: 1px solid rgba(255,255,255,0.09);
      backdrop-filter: blur(4px);
      color: #dde9f1;
    }
    .contact-info h2 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffffff;
    }
    .contact-info p {
      margin-bottom: 8px;
      font-size: 14px;
      color: #dde9f1;
      line-height: 1.6;
    }
    .contact-info strong {
      color: #ffd54f;
    }

    /* Social section */
    .social-links {
      margin-top: 14px;
      padding-top: 10px;
      border-top: 1px solid rgba(255,255,255,0.15);
    }
    .social-links-title {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #ffffff;
    }
    .social-icons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .social-icon {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #163b73;
      text-decoration: none;
      font-size: 16px;
      font-weight: 700;
      background: #ffd54f;
      box-shadow: 0 3px 8px rgba(0,0,0,0.4);
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .social-icon:hover {
      transform: translateY(-1px);
      box-shadow: 0 5px 12px rgba(0,0,0,0.6);
      background: #ffe173;
    }
    .social-icon.whatsapp { background: #25d366; color:#fff; }
    .social-icon.whatsapp:hover { background:#32e070; }
    .social-icon.facebook { background: #1877f2; color:#fff; }
    .social-icon.facebook:hover { background:#2b88ff; }
    .social-icon.instagram {
      background: radial-gradient(circle at 30% 30%, #fdf497 0, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
      color:#fff;
    }

    /* Footer */
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

    /* Scroll side arrow */
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
      .site-header {
        padding: 16px 14px;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
    }
    @media (max-width: 600px) {
      .scroll-arrow {
        right: 16px;
        bottom: 18px;
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
    <a href="industry.php">Industry Outlook</a>
    <a href="location.php">Location</a>
    <a href="contact.php" class="active">Contact</a>
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
      <span class="tag-label">Contact</span>
      <h1><?php echo esc($hero['title'] ?: 'Get in touch with our team.'); ?></h1>
      <p><?php echo esc($hero['content'] ?: 'You can reach us using the contact information below.'); ?></p>
    </div>
  </section>

  <section class="contact-wrap reveal-on-load reveal-delay-3">
    <aside class="contact-info">
      <h2>Contact Details</h2>
      <p>
        <strong>Phone</strong><br>
        <?php
          $phoneValue = $details['phone']['value'] ?? '+968-0000 0000';
          $phoneHref  = 'tel:' . preg_replace('/\s+/', '', $phoneValue);
        ?>
        <a href="<?php echo esc($phoneHref); ?>" style="color:#ffd54f;text-decoration:none;">
          <?php echo esc($phoneValue); ?>
        </a><br>
        <?php echo esc($details['phone']['extra'] ?? 'Monday – Friday, 8:00 AM – 6:00 PM'); ?>
      </p>
      <p>
        <strong>Email</strong><br>
        <?php
          $emailValue = $details['email']['value'] ?? 'info@muadhalzadjali.com';
          $emailHref  = 'mailto:' . $emailValue;
        ?>
        <a href="<?php echo esc($emailHref); ?>" style="color:#ffd54f;text-decoration:none;">
          <?php echo esc($emailValue); ?>
        </a>
      </p>
      <p>
        <strong>Office Location</strong><br>
        <?php echo nl2br(esc($details['office']['value'] ?? "Behind Grand Muttrah, Ruwi\nMuscat, 112, Sultanate of Oman")); ?>
      </p>
      <p>
        <?php echo esc($visitNote['content'] ?: 'Visits by appointment to ensure access and proper site coordination.'); ?>
      </p>

      <?php if (!empty($social)): ?>
        <div class="social-links">
          <div class="social-links-title">Connect with us</div>
          <div class="social-icons">
            <?php if (!empty($social['whatsapp'])): ?>
              <?php
                $wa = $social['whatsapp'];
                if (strpos($wa, 'http') !== 0) {
                    $waClean = preg_replace('/\D+/', '', $wa);
                    $waHref  = 'https://wa.me/' . $waClean;
                } else {
                    $waHref = $wa;
                }
              ?>
              <a href="<?php echo esc($waHref); ?>" target="_blank" rel="noopener" class="social-icon whatsapp" aria-label="WhatsApp">W</a>
            <?php endif; ?>

            <?php if (!empty($social['facebook'])): ?>
              <a href="<?php echo esc($social['facebook']); ?>" target="_blank" rel="noopener" class="social-icon facebook" aria-label="Facebook">f</a>
            <?php endif; ?>

            <?php if (!empty($social['instagram'])): ?>
              <a href="<?php echo esc($social['instagram']); ?>" target="_blank" rel="noopener" class="social-icon instagram" aria-label="Instagram">I</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
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

<script>
  const bgLayer    = document.getElementById('bgLayer');
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

  // Submenu
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

  // Scroll arrow
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
</script>

</body>
</html>
