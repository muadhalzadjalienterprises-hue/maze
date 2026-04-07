<?php
// about.php – DB-driven About Us + Who We Are

require __DIR__ . '/db.php'; // should set $pdo (PDO instance)

// Load sections
$secStmt = $pdo->query("SELECT * FROM about_sections WHERE is_active = 1");
$sections = [];
while ($row = $secStmt->fetch(PDO::FETCH_ASSOC)) {
    $sections[$row['slug']] = $row;
}

// Helper to get value safely
function about_val(array $sections, string $slug, string $field, string $fallback = ''): string {
    return isset($sections[$slug][$field]) && $sections[$slug][$field] !== ''
        ? $sections[$slug][$field]
        : $fallback;
}

// Load stats
$statsStmt = $pdo->query("SELECT * FROM about_stats WHERE is_active = 1 ORDER BY sort_order, id");
$stats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// Load diagonal images grouped by section_slug
$imgStmt = $pdo->query("
    SELECT *
    FROM about_images
    WHERE is_active = 1
    ORDER BY sort_order, id
");
$aboutImagesBySection = [];
while ($row = $imgStmt->fetch(PDO::FETCH_ASSOC)) {
  $section = explode('_', $row['slot_key'])[0];  // 'hero_1' → 'hero'
  $aboutImagesBySection[$section][] = $row;
}

// Owner popup config (single row)
$ownerPopup = null;
$ownerStmt = $pdo->query("SELECT * FROM about_owner_popup LIMIT 1");
$ownerPopup = $ownerStmt->fetch(PDO::FETCH_ASSOC) ?: null;

function section_images(array $aboutImagesBySection, string $slug): array {
    return $aboutImagesBySection[$slug] ?? [];
}

/**
 * Render a diagonal gallery for a given section slug
 */
function render_section_diagonal(array $aboutImagesBySection, string $slug): void {
    $group = section_images($aboutImagesBySection, $slug);
    if (!$group) return;
    ?>
    <section class="about-diagonal-wrapper reveal-on-load reveal-delay-4 about-diagonal-for-<?php echo htmlspecialchars($slug); ?>">
      <div class="about-diagonal">
        <?php foreach ($group as $img): ?>
          <?php if (empty($img['primary_image'])) continue; ?>
          <div class="about-diagonal-item">
            <div class="about-diagonal-item-inner">
              <img
                src="<?php echo htmlspecialchars($img['primary_image']); ?>"
                data-original="<?php echo htmlspecialchars($img['primary_image']); ?>"
                data-hover="<?php echo htmlspecialchars($img['hover_image'] ?? ''); ?>"
                alt="">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>About | Muadh Al Zadjali Engineering & Cont. Enterprises</title>

<meta name="keywords" content="about engineering company Oman, construction company Oman details">

<meta name="description" content="Learn about Muadh Al Zadjali Engineering & Cont. Enterprises, our mission, expertise, and commitment to quality construction services in Oman.">

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

    main {
      padding-top: 18px;
    }

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
      margin-bottom: 10px;
      color: #ffffff;
    }
    .page-hero p {
      font-size: 14px;
      max-width: 650px;
      color: #e0eef5;
      line-height: 1.7;
    }

    .stats-section {
      max-width: 1150px;
      margin: 18px auto 16px;
      padding: 0 20px;
    }
    .stats-heading {
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #ffd54f;
      margin-bottom: 6px;
    }
    .stats-subtext {
      font-size: 13px;
      color: #e4f0f7;
      margin-bottom: 14px;
      max-width: 520px;
      line-height: 1.6;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
    }
    .stat-card {
      position: relative;
      padding: 10px 8px 8px;
      border-radius: 10px;
      background: rgba(255,255,255,0.04);
      box-shadow: 0 6px 14px rgba(0,0,0,0.28);
      border: 1px solid rgba(255,255,255,0.06);
      backdrop-filter: blur(4px);
    }
    .stat-top {
      margin-bottom: 4px;
    }
    .stat-number {
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
    }
    .stat-label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.045em;
      color: #d0dde6;
      margin-bottom: 3px;
    }
    .stat-desc {
      font-size: 12px;
      color: #d5e4ee;
      line-height: 1.5;
    }

    .book-section {
      max-width: 1150px;
      margin: 10px auto 0;
      padding: 0 20px 10px;
      display: flex;
      justify-content: center;
    }
    .book-wrap {
      width: 100%;
      max-width: 960px;
      display: grid;
      grid-template-columns: minmax(0, 1.15fr) minmax(0, 1.05fr);
      gap: 32px;
    }
    .book-page h2 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffffff;
    }
    .book-page p {
      font-size: 14px;
      color: #dde9f1;
      margin-bottom: 10px;
      line-height: 1.7;
    }
    .book-side-heading {
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #ffd54f;
      margin-bottom: 8px;
    }
    .book-bullets {
      font-size: 13px;
      color: #dde9f1;
      list-style: none;
      padding-left: 0;
      line-height: 1.7;
    }
    .book-bullets li::before {
      content: "•";
      color: #ffd54f;
      margin-right: 6px;
    }

    .about-diagonal-wrapper {
      max-width: 1150px;
      margin: 24px auto 14px;
      padding: 0 20px;
      display: flex;
      justify-content: flex-end;
      clear: both;
    }
    .about-diagonal {
      width: 280px;
      max-width: 100%;
      transform: rotate(-8deg);
      transform-origin: center;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }
    .about-diagonal-item {
      transform-origin: center;
    }
    .about-diagonal-item:nth-child(odd) {
      transform: translateY(10px);
    }
    .about-diagonal-item:nth-child(even) {
      transform: translateY(-10px);
    }
    .about-diagonal-item-inner {
      position: relative;
      width: 100%;
      padding-top: 100%;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 12px 26px rgba(0,0,0,0.55);
      background: rgba(0,0,0,0.4);
    }
    .about-diagonal-item-inner img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.35s ease, opacity 0.25s ease;
    }
    .about-diagonal-item-inner:hover img {
      transform: translate(8px,-8px) scale(1.03);
    }

    .about-diagonal-for-who,
    .about-diagonal-for-mission,
    .about-diagonal-for-vision {
      margin-top: 10px;
      margin-bottom: 24px;
    }

    .who-hero {
      max-width: 1150px;
      margin: 36px auto 18px;
      padding: 0 20px;
    }
    .who-hero-inner {
      max-width: 760px;
    }
    .who-hero h2 {
      font-size: 22px;
      margin-bottom: 8px;
      color: #ffffff;
    }
    .who-hero p {
      font-size: 14px;
      max-width: 650px;
      color: #e0eef5;
      line-height: 1.7;
    }

    .who-wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 8px 20px 24px;
      display: grid;
      grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.3fr);
      gap: 24px;
      align-items: flex-start;
    }
    .who-main h2 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffffff;
    }
    .who-section {
      margin-bottom: 16px;
    }
    .who-section h3 {
      font-size: 15px;
      margin-bottom: 6px;
      color: #ffd54f;
    }
    .who-section p {
      font-size: 14px;
      color: #dde9f1;
      line-height: 1.6;
    }

    .core-values h2 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffffff;
    }
    .values-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
    }
    .value-item {
      padding: 10px 8px;
      border-radius: 8px;
      background: rgba(0,0,0,0.28);
      border: 1px solid rgba(255,255,255,0.07);
      box-shadow: 0 4px 10px rgba(0,0,0,0.35);
    }
    .value-title {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 4px;
      color: #ffd54f;
    }
    .value-text {
      font-size: 13px;
      color: #dde9f1;
    }

    .drives-wrap {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px 32px;
      display: grid;
      grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.5fr);
      gap: 24px;
    }
    .drives-main h2 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #ffffff;
    }
    .drives-main p {
      font-size: 14px;
      color: #dde9f1;
      margin-bottom: 10px;
      line-height: 1.6;
    }
    .drives-columns {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      margin-top: 10px;
    }
    .drive-item h3 {
      font-size: 14px;
      margin-bottom: 4px;
      color: #ffd54f;
    }
    .drive-item p {
      font-size: 13px;
      color: #dde9f1;
      margin-bottom: 0;
    }

    .commitment-box h2 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #ffffff;
    }
    .commitment-box p {
      font-size: 14px;
      color: #dde9f1;
      line-height: 1.6;
      margin-bottom: 8px;
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
      .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .book-wrap {
        grid-template-columns: minmax(0, 1fr);
      }
      .who-wrap,
      .drives-wrap {
        grid-template-columns: minmax(0, 1fr);
      }
      .about-diagonal-wrapper {
        justify-content: center;
      }
    }

    @media (max-width: 600px) {
      .stats-grid {
        grid-template-columns: minmax(0, 1fr);
      }
      .scroll-arrow {
        right: 16px;
        bottom: 18px;
      }
      .about-diagonal {
        width: 220px;
      }
    }

    /* === OWNER POPUP STYLES === */
    .owner-popup-launcher {
      position: fixed;
      left: 18px;
      bottom: 24px;
      z-index: 45;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px 8px 8px;
      border-radius: 999px;
      background: rgba(0,0,0,0.65);
      border: 1px solid rgba(255,255,255,0.12);
      box-shadow: 0 6px 18px rgba(0,0,0,0.55);
      cursor: pointer;
      backdrop-filter: blur(6px);
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, opacity 0.25s ease;
    }
    .owner-popup-launcher:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 24px rgba(0,0,0,0.7);
      background: rgba(0,0,0,0.8);
    }
    .owner-popup-avatar {
      width: 42px;
      height: 42px;
      border-radius: 999px;
      overflow: hidden;
      border: 2px solid #ffd54f;
      flex-shrink: 0;
    }
    .owner-popup-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .owner-popup-text {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .owner-popup-name {
      font-size: 13px;
      font-weight: 600;
      color: #ffffff;
    }
    .owner-popup-tagline {
      font-size: 11px;
      color: #e0eef5;
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    @media (max-width: 600px) {
      .owner-popup-launcher {
        left: 12px;
        bottom: 16px;
        padding-right: 8px;
      }
      .owner-popup-tagline {
        max-width: 160px;
      }
    }

    .owner-popup-overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at top, rgba(255,255,255,0.12), transparent 60%),
                  rgba(0,0,0,0.55);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 60;
    }
    .owner-popup-overlay.open {
      display: flex;
      animation: fadeOverlay 0.3s ease-out forwards;
    }
    @keyframes fadeOverlay {
      0% { opacity: 0; }
      100% { opacity: 1; }
    }

    .owner-popup-card {
      position: relative;
      width: min(420px, 92vw);
      padding: 16px 16px 14px;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 40px rgba(0,0,0,0.75);
      color: #fff;
      background: radial-gradient(circle at top left, #ff6f61, #ffca28 40%, #42a5f5 75%);
      animation: ownerCardEnter 0.4s ease-out forwards;
    }
    @keyframes ownerCardEnter {
      0%   { opacity: 0; transform: translateY(20px) scale(0.95) rotate(-1deg); }
      60%  { opacity: 1; transform: translateY(-4px) scale(1.02) rotate(1deg); }
      100% { opacity: 1; transform: translateY(0) scale(1) rotate(0); }
    }

    .owner-popup-card::before,
    .owner-popup-card::after {
      content: "";
      position: absolute;
      inset: -40%;
      background: conic-gradient(
        from 0deg,
        #ff6f61,
        #ffca28,
        #42a5f5,
        #ab47bc,
        #ff6f61
      );
      opacity: 0.18;
      animation: ownerBgSpin 14s linear infinite;
      mix-blend-mode: screen;
      pointer-events: none;
    }
    .owner-popup-card::after {
      animation-direction: reverse;
      opacity: 0.22;
    }
    @keyframes ownerBgSpin {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .owner-popup-inner {
      position: relative;
      z-index: 2;
      background: rgba(0,0,0,0.35);
      border-radius: 14px;
      padding: 10px 12px 10px;
      display: grid;
      grid-template-columns: 64px minmax(0, 1fr);
      gap: 10px;
      align-items: flex-start;
    }

    .owner-popup-photo-lg {
      width: 64px;
      height: 64px;
      border-radius: 999px;
      border: 2px solid rgba(255,255,255,0.9);
      overflow: hidden;
      flex-shrink: 0;
    }
    .owner-popup-photo-lg img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .owner-popup-heading {
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 4px;
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      gap: 4px;
    }
    .owner-popup-heading span.role {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #ffe082;
    }

    .owner-popup-message-title {
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 2px;
      color: #fffde7;
    }
    .owner-popup-message-text {
      font-size: 13px;
      line-height: 1.6;
      color: #fdfdfd;
      max-height: 210px;
      overflow-y: auto;
    }

    .owner-popup-close {
      position: absolute;
      top: 6px;
      right: 8px;
      border: none;
      background: rgba(0,0,0,0.55);
      color: #fff;
      border-radius: 999px;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 16px;
      line-height: 1;
      z-index: 3;
    }
    .owner-popup-close:hover {
      background: rgba(0,0,0,0.85);
    }

    @media (max-width: 480px) {
      .owner-popup-inner {
        grid-template-columns: minmax(0,1fr);
      }
      .owner-popup-photo-lg {
        margin: 0 auto 4px;
      }
      .owner-popup-heading {
        justify-content: center;
        text-align: center;
      }
      .owner-popup-message-text {
        max-height: 240px;
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
    <a href="about.php" class="active">About Us</a>

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
  <!-- Hero -->
  <section class="page-hero reveal-on-load reveal-delay-2" id="about-top">
    <div class="page-hero-inner">
      <span class="tag-label">About Us</span>
      <h1><?php echo htmlspecialchars(about_val($sections, 'hero', 'title', 'Decades of engineering and construction excellence.')); ?></h1>
      <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'hero', 'content',
        'Trusted partnerships, strong technical capability, and an unwavering commitment to quality define Muadh Al Zadjali Engineering & Contracting Enterprises.'
      ))); ?></p>
    </div>
  </section>

  <!-- Ladder -->
  <div class="section-ladder reveal-on-load reveal-delay-3">
    <span class="section-ladder-label">Sections</span>
    <a href="#about-top"><span class="arrow">➤</span><span>About Us</span></a>
    <a href="#who-we-are"><span class="arrow">➤</span><span>Who We Are</span></a>
    <a href="#mission-vision"><span class="arrow">➤</span><span>Mission &amp; Vision</span></a>
    <a href="#core-values"><span class="arrow">➤</span><span>Core Values</span></a>
    <a href="#what-drives-us"><span class="arrow">➤</span><span>What Drives Us</span></a>
  </div>

  <!-- Stats -->
  <section class="stats-section reveal-on-load reveal-delay-3">
    <div id="statsPaper">
      <div class="stats-heading">Key Project Milestones</div>
      <div class="stats-subtext">
        These highlights give a quick snapshot of our scale, capability, and client trust before you dive into our full story.
      </div>

      <div class="stats-grid">
        <?php if ($stats): ?>
          <?php foreach ($stats as $row): ?>
            <?php
              $val = $row['value_display'];
              if (preg_match('/^(\d+)(\D*)$/', $val, $m)) {
                  $target = (int)$m[1];
                  $suffix = $m[2];
              } else {
                  $target = 0;
                  $suffix = '';
              }
            ?>
            <div class="stat-card">
              <div class="stat-top">
                <?php if ($target > 0): ?>
                  <div class="stat-number" data-target="<?php echo (int)$target; ?>" data-suffix="<?php echo htmlspecialchars($suffix); ?>">0<?php echo htmlspecialchars($suffix); ?></div>
                <?php else: ?>
                  <div class="stat-number"><?php echo htmlspecialchars($val); ?></div>
                <?php endif; ?>
                <div class="stat-label"><?php echo htmlspecialchars($row['label']); ?></div>
              </div>
              <div class="stat-desc"><?php echo htmlspecialchars($row['description']); ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Story -->
  <section class="book-section reveal-on-load reveal-delay-4">
    <div class="book-wrap">
      <article class="book-page">
        <h2><?php echo htmlspecialchars(about_val($sections, 'story', 'title', 'Our Story')); ?></h2>
        <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'story', 'content',
          'Founded with a clear mission to deliver exceptional construction services built on integrity, quality, and client satisfaction, our company has grown from a focused contractor into a full‑service engineering and contracting enterprise.'
        ))); ?></p>
      </article>

      <aside class="book-page">
        <div class="book-side-heading">What this means for you</div>
        <ul class="book-bullets">
          <li>Clear project planning, regular updates, and transparent communication from start to handover.</li>
          <li>Site teams focused on safety, workmanship, and coordination with your stakeholders.</li>
          <li>Experience across different project types and complexities in Oman and the wider region.</li>
          <li>A partner committed to long‑term relationships, not just one‑time contracts.</li>
        </ul>
      </aside>
    </div>
  </section>

  <!-- Story images -->
  <?php render_section_diagonal($aboutImagesBySection, 'story'); ?>

  <!-- Who we are / mission / vision -->
  <section class="who-hero reveal-on-load reveal-delay-3" id="who-we-are">
    <div class="who-hero-inner">
      <span class="tag-label">Who We Are</span>
      <h2><?php echo htmlspecialchars(about_val($sections, 'who', 'title', 'Our mission, vision, and values guide every project.')); ?></h2>
      <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'who', 'content',
        'We approach each relationship with clear purpose, strong ethics, and a commitment to delivering lasting value through every construction project.'
      ))); ?></p>
    </div>
  </section>

  <!-- Who images -->
  <?php render_section_diagonal($aboutImagesBySection, 'who'); ?>

  <section class="who-wrap" id="mission-vision">
    <article class="who-main">
      <h2>Our Mission &amp; Vision</h2>

      <div class="who-section" id="mission">
        <h3><?php echo htmlspecialchars(about_val($sections, 'mission', 'title', 'Our Mission')); ?></h3>
        <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'mission', 'content',
          'To deliver exceptional construction and engineering services that transform visions into reality while maintaining the highest standards of quality, safety, and professionalism. We build more than structures – we build long‑term relationships and stronger communities.'
        ))); ?></p>
      </div>

      <!-- Mission images -->
      <?php render_section_diagonal($aboutImagesBySection, 'mission'); ?>

      <div class="who-section" id="vision">
        <h3><?php echo htmlspecialchars(about_val($sections, 'vision', 'title', 'Our Vision')); ?></h3>
        <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'vision', 'content',
          'To be one of the most trusted and innovative construction partners in our region, recognized for excellence in execution, sustainable practices, and unwavering commitment to client satisfaction. Each project aims to set new benchmarks for quality and craftsmanship.'
        ))); ?></p>
      </div>

      <!-- Vision images -->
      <?php render_section_diagonal($aboutImagesBySection, 'vision'); ?>
    </article>

    <aside class="core-values" id="core-values">
      <h2>Our Core Values</h2>
      <div class="values-grid">
        <div class="value-item">
          <div class="value-title">Quality Excellence</div>
          <div class="value-text">We never compromise on quality. Every project is executed to high standards with meticulous attention to detail.</div>
        </div>
        <div class="value-item">
          <div class="value-title">Integrity</div>
          <div class="value-text">Honest communication, transparent pricing, and ethical business practices form the foundation of our relationships.</div>
        </div>
        <div class="value-item">
          <div class="value-title">Innovation</div>
          <div class="value-text">We embrace new technologies and methods to deliver better results, faster timelines, and more value.</div>
        </div>
        <div class="value-item">
          <div class="value-title">Client Focus</div>
          <div class="value-text">Your vision drives our work. We listen, collaborate, and deliver solutions that exceed expectations.</div>
        </div>
      </div>
    </aside>
  </section>

  <!-- What drives us / commitment -->
  <section class="drives-wrap" id="what-drives-us">
    <article class="drives-main">
      <h2>What Drives Us</h2>
      <p>Construction is more than our business – it is our passion. We take pride in transforming thoughtfully designed plans into durable structures that support communities for generations.</p>

      <div class="drives-columns">
        <div class="drive-item">
          <h3>Passion for Building</h3>
          <p>We are motivated by the impact our projects have on people, businesses, and cities.</p>
        </div>
        <div class="drive-item">
          <h3>Commitment to Safety</h3>
          <p>Every team member returns home safely each day. Rigorous safety protocols protect workers, clients, and the public.</p>
        </div>
        <div class="drive-item">
          <h3>Environmental Responsibility</h3>
          <p>We use sustainable practices and materials to reduce environmental impact while maintaining high performance.</p>
        </div>
      </div>
    </article>

    <aside class="commitment-box">
      <h2><?php echo htmlspecialchars(about_val($sections, 'commitment', 'title', 'Our Commitment to You')); ?></h2>
      <p><?php echo nl2br(htmlspecialchars(about_val($sections, 'commitment', 'content',
        'When you choose Muadh Al Zadjali Engineering & Cont. Enterprises, you choose a partner dedicated to your success throughout the entire project lifecycle.'
      ))); ?></p>
      <p>From initial consultation to final handover, we combine decades of experience, strong project management, and modern tools to ensure your project is delivered on time, on budget, and to the quality you expect.</p>
    </aside>
  </section>

  <!-- Commitment images -->
  <?php render_section_diagonal($aboutImagesBySection, 'commitment'); ?>

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

<?php if ($ownerPopup && !empty($ownerPopup['is_active'])): ?>
  <!-- Owner popup launcher -->
  <button
    type="button"
    class="owner-popup-launcher"
    id="ownerLauncher"
    aria-label="Message from the Owner"
  >
    <div class="owner-popup-avatar">
      <img src="<?php echo htmlspecialchars($ownerPopup['photo_url']); ?>" alt="">
    </div>
    <div class="owner-popup-text">
      <span class="owner-popup-name">
        <?php echo htmlspecialchars($ownerPopup['owner_name'] ?: 'Owner'); ?>
      </span>
      <span class="owner-popup-tagline">
        <?php echo htmlspecialchars($ownerPopup['short_tagline'] ?: 'A personal note from our founder.'); ?>
      </span>
    </div>
  </button>

  <!-- Owner full popup -->
  <div class="owner-popup-overlay" id="ownerOverlay" aria-hidden="true">
    <div class="owner-popup-card" role="dialog" aria-modal="true" aria-labelledby="ownerDialogTitle">
      <button type="button" class="owner-popup-close" id="ownerClose" aria-label="Close">
        ×
      </button>
      <div class="owner-popup-inner">
        <div class="owner-popup-photo-lg">
          <img src="<?php echo htmlspecialchars($ownerPopup['photo_url']); ?>" alt="">
        </div>
        <div>
          <div class="owner-popup-heading">
            <span id="ownerDialogTitle">
              <?php echo htmlspecialchars($ownerPopup['owner_name'] ?: 'Owner'); ?>
            </span>
            <?php if (!empty($ownerPopup['owner_title'])): ?>
              <span class="role"><?php echo htmlspecialchars($ownerPopup['owner_title']); ?></span>
            <?php endif; ?>
          </div>
          <?php if (!empty($ownerPopup['short_tagline'])): ?>
            <div class="owner-popup-message-title">
              <?php echo htmlspecialchars($ownerPopup['short_tagline']); ?>
            </div>
          <?php endif; ?>
          <div class="owner-popup-message-text">
            <?php echo nl2br(htmlspecialchars($ownerPopup['full_message'])); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

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

  const statNumbers = document.querySelectorAll('.stat-number[data-target]');
  let statsStarted = false;

  function startStatsCount() {
    if (statsStarted) return;
    statsStarted = true;

    statNumbers.forEach(el => {
      const target = parseInt(el.getAttribute('data-target'), 10);
      const suffix = el.getAttribute('data-suffix') || '';
      if (!target || isNaN(target)) return;

      let current = 0;
      const duration = 1200;
      const stepTime = 30;
      const steps = Math.ceil(duration / stepTime);
      const increment = target / steps;

      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        el.textContent = Math.round(current) + suffix;
      }, stepTime);
    });
  }

  window.addEventListener('load', () => {
    setTimeout(() => {
      startStatsCount();
    }, 900);
  });

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

  document.querySelectorAll('.about-diagonal-item-inner img').forEach(img => {
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

  // Owner popup JS
  (function () {
    const launcher = document.getElementById('ownerLauncher');
    const overlay  = document.getElementById('ownerOverlay');
    const closeBtn = document.getElementById('ownerClose');

    if (!launcher || !overlay || !closeBtn) return;

    function openOwnerPopup() {
      overlay.classList.add('open');
      overlay.setAttribute('aria-hidden', 'false');
    }

    function closeOwnerPopup() {
      overlay.classList.remove('open');
      overlay.setAttribute('aria-hidden', 'true');
    }

    launcher.addEventListener('click', openOwnerPopup);
    closeBtn.addEventListener('click', closeOwnerPopup);

    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        closeOwnerPopup();
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeOwnerPopup();
      }
    });
  })();
</script>

</body>
</html>
