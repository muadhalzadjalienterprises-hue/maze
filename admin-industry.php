<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php';

$message = '';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

function get_section($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM industry_sections WHERE slug = :s AND is_active = 1");
    $stmt->execute([':s' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['id' => null, 'title' => '', 'content' => ''];
}

// Ensure required slugs exist
$slugs = [
    'hero','trend_1','trend_2','trend_3','trend_4',
    'forecast_2026','forecast_2027','forecast_2028',
    'metrics','regions_intro'
];
foreach ($slugs as $slug) {
    $q = $pdo->prepare("SELECT COUNT(*) FROM industry_sections WHERE slug = :s");
    $q->execute([':s' => $slug]);
    if (!$q->fetchColumn()) {
        $ins = $pdo->prepare("INSERT INTO industry_sections (slug,title,content) VALUES (:s,'','')");
        $ins->execute([':s' => $slug]);
    }
}

// Re-fetch with IDs guaranteed
function get_section_raw($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM industry_sections WHERE slug = :s");
    $stmt->execute([':s' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$heroRow     = get_section_raw($pdo, 'hero');
$trendRows   = [];
for ($i = 1; $i <= 4; $i++) {
    $trendRows[$i] = get_section_raw($pdo, "trend_{$i}");
}
$forecastRows = [];
foreach (['2026','2027','2028'] as $year) {
    $forecastRows[$year] = get_section_raw($pdo, "forecast_{$year}");
}
$metricsRow  = get_section_raw($pdo, 'metrics');
$regionsRow  = get_section_raw($pdo, 'regions_intro');

// Helper: get existing images for preview
function get_section_images($pdo, $section_id) {
    if (!$section_id) return [];
    $stmt = $pdo->prepare("SELECT * FROM industry_section_images WHERE section_id = :id ORDER BY sort_order, id");
    $stmt->execute([':id' => $section_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$heroImages    = get_section_images($pdo, $heroRow['id']);
$trendImages   = [];
for ($i = 1; $i <= 4; $i++) {
    $trendImages[$i] = get_section_images($pdo, $trendRows[$i]['id']);
}
$forecastImages = [];
foreach (['2026','2027','2028'] as $year) {
    $forecastImages[$year] = get_section_images($pdo, $forecastRows[$year]['id']);
}
$metricsImages = get_section_images($pdo, $metricsRow['id']);
$regionsImages = get_section_images($pdo, $regionsRow['id']);

// Upload handler
function handle_section_uploads($pdo, $section_id, $field_name) {
    if (!$section_id) return;
    if (empty($_FILES[$field_name]['name'][0])) {
        return;
    }

    $uploadDir = __DIR__ . '/uploads/industry/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    // current max sort_order
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM industry_section_images WHERE section_id = :id");
    $stmt->execute([':id' => $section_id]);
    $sort = (int)$stmt->fetchColumn();

    $allowedExt = ['jpg','jpeg','png','webp','gif'];

    foreach ($_FILES[$field_name]['name'] as $idx => $name) {
        if ($_FILES[$field_name]['error'][$idx] !== UPLOAD_ERR_OK) {
            continue;
        }

        $tmpName  = $_FILES[$field_name]['tmp_name'][$idx];
        $origName = basename($name);
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            continue;
        }

        // generate unique name
        $newName   = uniqid('sec'.$section_id.'_') . '.' . $ext;
        $targetAbs = $uploadDir . $newName;
        $targetRel = 'uploads/industry/' . $newName;

        // move_uploaded_file is required to safely move from temp upload dir [web:63][web:64]
        if (move_uploaded_file($tmpName, $targetAbs)) {
            $sort++;
            $ins = $pdo->prepare("INSERT INTO industry_section_images (section_id, image_path, sort_order) VALUES (:sid, :path, :sort)");
            $ins->execute([
                ':sid'  => $section_id,
                ':path' => $targetRel,
                ':sort' => $sort,
            ]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upd = $pdo->prepare("UPDATE industry_sections SET title = :t, content = :c WHERE slug = :s");

    $upd->execute([':t'=>trim($_POST['hero_title'] ?? ''), ':c'=>trim($_POST['hero_content'] ?? ''), ':s'=>'hero']);

    for ($i = 1; $i <= 4; $i++) {
        $upd->execute([
            ':t' => trim($_POST["trend{$i}_title"] ?? ''),
            ':c' => trim($_POST["trend{$i}_content"] ?? ''),
            ':s' => "trend_{$i}",
        ]);
    }

    foreach (['2026','2027','2028'] as $year) {
        $upd->execute([
            ':t' => trim($_POST["forecast_{$year}_title"] ?? ''),
            ':c' => trim($_POST["forecast_{$year}_content"] ?? ''),
            ':s' => "forecast_{$year}",
        ]);
    }

    $upd->execute([
        ':t' => trim($_POST['metrics_title'] ?? ''),
        ':c' => trim($_POST['metrics_content'] ?? ''),
        ':s' => 'metrics',
    ]);

    $upd->execute([
        ':t' => trim($_POST['regions_title'] ?? ''),
        ':c' => trim($_POST['regions_content'] ?? ''),
        ':s' => 'regions_intro',
    ]);

    // Handle uploads for each section
    handle_section_uploads($pdo, $heroRow['id'], 'hero_images');

    for ($i = 1; $i <= 4; $i++) {
        handle_section_uploads($pdo, $trendRows[$i]['id'], "trend{$i}_images");
    }

    foreach (['2026','2027','2028'] as $year) {
        handle_section_uploads($pdo, $forecastRows[$year]['id'], "forecast_{$year}_images");
    }

    handle_section_uploads($pdo, $metricsRow['id'], 'metrics_images');
    handle_section_uploads($pdo, $regionsRow['id'], 'regions_images');

    $message = 'Industry Outlook content updated.';
    // Reload images after upload
    $heroImages    = get_section_images($pdo, $heroRow['id']);
    for ($i = 1; $i <= 4; $i++) {
        $trendImages[$i] = get_section_images($pdo, $trendRows[$i]['id']);
    }
    foreach (['2026','2027','2028'] as $year) {
        $forecastImages[$year] = get_section_images($pdo, $forecastRows[$year]['id']);
    }
    $metricsImages = get_section_images($pdo, $metricsRow['id']);
    $regionsImages = get_section_images($pdo, $regionsRow['id']);
}

// Load sections (for text fields)
$hero    = get_section($pdo, 'hero');
$trend   = [];
for ($i = 1; $i <= 4; $i++) {
    $trend[$i] = get_section($pdo, "trend_{$i}");
}
$forecast = [];
foreach (['2026','2027','2028'] as $year) {
    $forecast[$year] = get_section($pdo, "forecast_{$year}");
}
$metrics = get_section($pdo, 'metrics');
$regions = get_section($pdo, 'regions_intro');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Industry Outlook | Muadh Al Zadjali</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { box-sizing:border-box;margin:0;padding:0; }
    body {
      font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;
      background:#0b3c5d;
      color:#fff;
      min-height:100vh;
    }
    header {
      background:rgba(255,255,255,0.96);
      color:#163b73;
      padding:14px 20px;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    header a {
      color:#163b73;
      text-decoration:none;
      font-size:13px;
      margin-left:10px;
    }
    main {
      max-width:1100px;
      margin:24px auto 32px;
      padding:0 18px;
    }
    h1 { font-size:22px;margin-bottom:10px; }
    .section-card {
      background:rgba(0,0,0,0.55);
      border-radius:10px;
      padding:16px;
      margin-bottom:18px;
      box-shadow:0 4px 12px rgba(0,0,0,0.4);
    }
    .section-card h2 {
      font-size:16px;
      margin-bottom:8px;
    }
    label {
      display:block;
      font-size:13px;
      margin-bottom:4px;
    }
    input[type="text"],
    textarea {
      width:100%;
      padding:7px 9px;
      border-radius:6px;
      border:1px solid #ccc;
      margin-bottom:8px;
      font-size:13px;
      color:#222;
    }
    textarea { min-height:70px;resize:vertical; }
    button {
      padding:8px 14px;
      border-radius:999px;
      border:none;
      cursor:pointer;
      background:#ffd54f;
      color:#163b73;
      font-size:13px;
      font-weight:600;
    }
    .message {
      font-size:13px;
      color:#c8e6c9;
      margin-bottom:6px;
    }
    .two-col {
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
      gap:12px;
    }
    .thumb-row {
      display:flex;
      flex-wrap:wrap;
      gap:6px;
      margin:4px 0 8px;
    }
    .thumb {
      width:70px;
      height:50px;
      border-radius:4px;
      overflow:hidden;
      border:1px solid rgba(255,255,255,0.35);
      background:#111;
    }
    .thumb img {
      width:100%;
      height:100%;
      object-fit:cover;
    }
    .hint {
      font-size:11px;
      opacity:0.8;
      margin-bottom:4px;
    }
  </style>
</head>
<body>
<header>
  <div>Admin – Industry Outlook</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage Industry Outlook Page</h1>

  <?php if ($message): ?>
    <div class="message"><?php echo esc($message); ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="section-card">
      <h2>Hero Section</h2>
      <label for="hero_title">Headline</label>
      <input type="text" id="hero_title" name="hero_title" value="<?php echo esc($hero['title']); ?>">
      <label for="hero_content">Intro</label>
      <textarea id="hero_content" name="hero_content"><?php echo esc($hero['content']); ?></textarea>

      <label class="hint">Hero slider images (multiple allowed, jpg/png/webp/gif)</label>
      <input type="file" name="hero_images[]" multiple accept="image/*">
      <?php if (!empty($heroImages)): ?>
        <div class="thumb-row">
          <?php foreach ($heroImages as $img): ?>
            <div class="thumb">
              <img src="<?php echo esc($img['image_path']); ?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="section-card">
      <h2>Current Trends (4 items)</h2>
      <div class="two-col">
        <?php for ($i=1;$i<=4;$i++): ?>
          <div>
            <label>Trend <?php echo $i; ?> title</label>
            <input type="text" name="trend<?php echo $i; ?>_title" value="<?php echo esc($trend[$i]['title']); ?>">
            <label>Trend <?php echo $i; ?> text</label>
            <textarea name="trend<?php echo $i; ?>_content"><?php echo esc($trend[$i]['content']); ?></textarea>

            <label class="hint">Trend <?php echo $i; ?> images</label>
            <input type="file" name="trend<?php echo $i; ?>_images[]" multiple accept="image/*">
            <?php if (!empty($trendImages[$i])): ?>
              <div class="thumb-row">
                <?php foreach ($trendImages[$i] as $img): ?>
                  <div class="thumb">
                    <img src="<?php echo esc($img['image_path']); ?>" alt="">
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endfor; ?>
      </div>
    </div>

    <div class="section-card">
      <h2>Forecast (2026–2028)</h2>
      <?php foreach (['2026','2027','2028'] as $year): ?>
        <label>Forecast <?php echo $year; ?> title</label>
        <input type="text" name="forecast_<?php echo $year; ?>_title"
               value="<?php echo esc($forecast[$year]['title']); ?>">
        <label>Forecast <?php echo $year; ?> text</label>
        <textarea name="forecast_<?php echo $year; ?>_content"><?php
          echo esc($forecast[$year]['content']);
        ?></textarea>

        <label class="hint">Forecast <?php echo $year; ?> images</label>
        <input type="file" name="forecast_<?php echo $year; ?>_images[]" multiple accept="image/*">
        <?php if (!empty($forecastImages[$year])): ?>
          <div class="thumb-row">
            <?php foreach ($forecastImages[$year] as $img): ?>
              <div class="thumb">
                <img src="<?php echo esc($img['image_path']); ?>" alt="">
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <hr style="border:none;border-top:1px solid rgba(255,255,255,0.2);margin:8px 0;">
      <?php endforeach; ?>
    </div>

    <div class="section-card">
      <h2>Key Indicators & Regions</h2>
      <label for="metrics_title">Metrics title</label>
      <input type="text" id="metrics_title" name="metrics_title" value="<?php echo esc($metrics['title']); ?>">
      <label for="metrics_content">Metrics content (free text, you can list values)</label>
      <textarea id="metrics_content" name="metrics_content"><?php echo esc($metrics['content']); ?></textarea>

      <label class="hint">Metrics images</label>
      <input type="file" name="metrics_images[]" multiple accept="image/*">
      <?php if (!empty($metricsImages)): ?>
        <div class="thumb-row">
          <?php foreach ($metricsImages as $img): ?>
            <div class="thumb">
              <img src="<?php echo esc($img['image_path']); ?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <label for="regions_title" style="margin-top:10px;">Regions title</label>
      <input type="text" id="regions_title" name="regions_title" value="<?php echo esc($regions['title']); ?>">
      <label for="regions_content">Regions intro + bullets (one per line)</label>
      <textarea id="regions_content" name="regions_content"><?php echo esc($regions['content']); ?></textarea>

      <label class="hint">Regions images</label>
      <input type="file" name="regions_images[]" multiple accept="image/*">
      <?php if (!empty($regionsImages)): ?>
        <div class="thumb-row">
          <?php foreach ($regionsImages as $img): ?>
            <div class="thumb">
              <img src="<?php echo esc($img['image_path']); ?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <button type="submit">Save Industry Outlook</button>
  </form>
</main>
</body>
</html>
