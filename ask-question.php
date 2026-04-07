<?php
// ask-question.php – Ask a Question (DB-driven, dark theme)

require __DIR__ . '/db.php';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

// Load sections
function get_section($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM ask_sections WHERE slug = :slug AND is_active = 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['title' => '', 'content' => ''];
}

$hero      = get_section($pdo, 'hero');
$formIntro = get_section($pdo, 'form_intro');

$successMsg = '';
$errorMsg   = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $question = trim($_POST['question'] ?? '');

    if ($question === '') {
        $errorMsg = 'Please enter your question.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO ask_questions (name, question)
            VALUES (:n, :q)
        ");
        $stmt->execute([
            ':n' => $name !== '' ? $name : null,
            ':q' => $question,
        ]);
        $successMsg = 'Thank you. Your question has been received.';
    }
}

// Load FAQs
$faqStmt = $pdo->query("
    SELECT * FROM ask_faqs
    WHERE is_active = 1
    ORDER BY display_order ASC, created_at DESC
");
$faqs = $faqStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Ask a Question | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="construction questions Oman, engineering consultation Oman">

<meta name="description" content="Ask questions about construction and engineering services offered by Muadh Al Zadjali Engineering & Cont. Enterprises.">

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

    .reveal-on-load {
      opacity: 0;
      transform: translateY(20px) scale(0.98);
      animation: revealUp 0.7s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
    }
    .reveal-delay-1 { animation-delay: 0.1s; }
    .reveal-delay-2 { animation-delay: 0.2s; }
    .reveal-delay-3 { animation-delay: 0.3s; }

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
      100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }

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

    .ask-wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 24px 20px 40px;
      display: grid;
      grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.4fr);
      gap: 24px;
    }

    .ask-form-card,
    .faq-card {
      background: rgba(0,0,0,0.32);
      padding: 18px 18px 20px;
      border-radius: 10px;
      box-shadow: 0 6px 14px rgba(0,0,0,0.35);
      border: 1px solid rgba(255,255,255,0.09);
      backdrop-filter: blur(4px);
      color: #dde9f1;
      font-size: 14px;
    }
    .ask-form-card h2,
    .faq-card h2 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #ffffff;
    }

    .field { margin-bottom: 10px; }
    label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
      color: #e3edf5;
    }
    input[type="text"],
    textarea {
      width: 100%;
      padding: 8px 9px;
      font-size: 14px;
      border-radius: 4px;
      border: 1px solid rgba(255,255,255,0.4);
      outline: none;
      background: rgba(0,0,0,0.35);
      color: #f5f5f5;
    }
    textarea { min-height: 100px; resize: vertical; }
    input:focus,
    textarea:focus {
      border-color: #ffd54f;
    }
    .btn-primary {
      display: inline-block;
      padding: 10px 18px;
      border-radius: 999px;
      border: none;
      background: #ffd54f;
      color: #163b73;
      font-size: 14px;
      cursor: pointer;
      font-weight: 600;
    }
    .btn-primary:hover { background: #ffe173; }

    .status-message {
      font-size: 13px;
      margin-bottom: 8px;
    }
    .status-success { color: #c8e6c9; }
    .status-error   { color: #ff9e9e; }

    .faq-item { margin-bottom: 10px; }
    .faq-q {
      font-weight: 600;
      margin-bottom: 2px;
      cursor: pointer;
      color: #ffd54f;
    }
    .faq-a {
      font-size: 13px;
      color: #dde9f1;
      display: none;
      line-height: 1.6;
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
    .footer-copy {
      font-size: 12px;
      color: #ccc;
    }

    @media (max-width: 900px) {
      .site-header {
        padding: 16px 14px;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      .ask-wrap {
        grid-template-columns: minmax(0, 1fr);
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
    <a href="contact.php">Contact</a>
    <div class="nav-item-with-sub">
      <a href="ask-question.php" class="active">Ask a question</a>
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
  <section class="page-hero reveal-on-load reveal-delay-2">
    <div class="page-hero-inner">
      <span class="tag-label">Ask a Question</span>
      <h1><?php echo esc($hero['title'] ?: 'Have a question about our services or a project?'); ?></h1>
      <p><?php echo esc($hero['content'] ?: 'Our team is ready to help. Submit your question and we will review it as soon as possible.'); ?></p>
    </div>
  </section>

  <section class="ask-wrap reveal-on-load reveal-delay-3">
    <article class="ask-form-card">
      <h2><?php echo esc($formIntro['title'] ?: 'Submit Your Question'); ?></h2>
      <p><?php echo esc($formIntro['content'] ?: 'Use this form to ask about construction methods, timelines, pricing models, or any aspect of working with us.'); ?></p>

      <?php if ($successMsg): ?>
        <div class="status-message status-success"><?php echo esc($successMsg); ?></div>
      <?php elseif ($errorMsg): ?>
        <div class="status-message status-error"><?php echo esc($errorMsg); ?></div>
      <?php endif; ?>

      <form method="post" action="ask-question.php">
        <div class="field">
          <label for="aq-name">Name</label>
          <input
            type="text"
            id="aq-name"
            name="name"
            value="<?php echo esc($_POST['name'] ?? ''); ?>"
            placeholder="Your name (optional)"
          >
        </div>
        <div class="field">
          <label for="aq-question">Your Question *</label>
          <textarea
            id="aq-question"
            name="question"
            required
            placeholder="What would you like to know about our construction services?"
          ><?php echo esc($_POST['question'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn-primary">Submit Question</button>
      </form>
    </article>

    <aside class="faq-card">
      <h2>Frequently Asked Questions</h2>

      <?php if ($faqs): ?>
        <?php foreach ($faqs as $f): ?>
          <div class="faq-item">
            <div class="faq-q"><?php echo esc($f['question_text']); ?></div>
            <div class="faq-a"><?php echo nl2br(esc($f['answer_text'])); ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="font-size:13px;">No FAQs have been published yet.</p>
      <?php endif; ?>
    </aside>
  </section>
</main>

<footer>
  <div class="footer-inner">
    <div class="footer-copy">
      © 2026 Muadh Al Zadjali Engineering &amp; Cont. Enterprises. All rights reserved.
    </div>
  </div>
</footer>

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

  document.querySelectorAll('.faq-q').forEach(function (q) {
    q.addEventListener('click', function () {
      const a = q.nextElementSibling;
      if (!a) return;
      a.style.display = (a.style.display === 'block') ? 'none' : 'block';
    });
  });
</script>
</body>
</html>
