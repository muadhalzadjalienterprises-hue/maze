\<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php';

$message = '';
$errors  = [];

// Helper
function get_section($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM contact_sections WHERE slug = :slug AND is_active = 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['title' => '', 'content' => ''];
}

// Ensure rows exist for known slugs (only those still used)
$knownSlugs = ['hero','visit_note'];
foreach ($knownSlugs as $slug) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_sections WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    if (!$stmt->fetchColumn()) {
        $ins = $pdo->prepare("INSERT INTO contact_sections (slug, title, content, is_active) VALUES (:slug,'','',1)");
        $ins->execute([':slug' => $slug]);
    }
}

// Ensure rows exist for contact_details
$detailLabels = ['phone','email','office'];
foreach ($detailLabels as $label) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_details WHERE label = :l");
    $stmt->execute([':l' => $label]);
    if (!$stmt->fetchColumn()) {
        $ins = $pdo->prepare("INSERT INTO contact_details (label, value, extra, is_active) VALUES (:l,'','',1)");
        $ins->execute([':l' => $label]);
    }
}

// Ensure rows exist for social links
$socialLabels = ['whatsapp','facebook','instagram'];
foreach ($socialLabels as $label) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_social_links WHERE label = :l");
    $stmt->execute([':l' => $label]);
    if (!$stmt->fetchColumn()) {
        $ins = $pdo->prepare("INSERT INTO contact_social_links (label, url, is_active) VALUES (:l,'',1)");
        $ins->execute([':l' => $label]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sections (only hero + visit note now)
    $hero_title   = trim($_POST['hero_title'] ?? '');
    $hero_content = trim($_POST['hero_content'] ?? '');
    $visit_note   = trim($_POST['visit_note'] ?? '');

    $upd = $pdo->prepare("UPDATE contact_sections SET title=:t, content=:c WHERE slug=:s");

    $upd->execute([':t'=>$hero_title, ':c'=>$hero_content, ':s'=>'hero']);
    $upd->execute([':t'=>'',         ':c'=>$visit_note,    ':s'=>'visit_note']);

    // Contact details
    $phone_value  = trim($_POST['phone_value'] ?? '');
    $phone_extra  = trim($_POST['phone_extra'] ?? '');
    $email_value  = trim($_POST['email_value'] ?? '');
    $office_value = trim($_POST['office_value'] ?? '');

    $updDetail = $pdo->prepare("
        UPDATE contact_details SET value = :v, extra = :e WHERE label = :l
    ");

    $updDetail->execute([':v'=>$phone_value,  ':e'=>$phone_extra, ':l'=>'phone']);
    $updDetail->execute([':v'=>$email_value,  ':e'=>null,         ':l'=>'email']);
    $updDetail->execute([':v'=>$office_value, ':e'=>null,         ':l'=>'office']);

    // Social links
    $whatsapp_url  = trim($_POST['whatsapp_url'] ?? '');
    $facebook_url  = trim($_POST['facebook_url'] ?? '');
    $instagram_url = trim($_POST['instagram_url'] ?? '');

    $updSocial = $pdo->prepare("
        UPDATE contact_social_links
        SET url = :u, is_active = :a
        WHERE label = :l
    ");

    $updSocial->execute([
        ':u' => $whatsapp_url,
        ':a' => $whatsapp_url !== '' ? 1 : 0,
        ':l' => 'whatsapp'
    ]);
    $updSocial->execute([
        ':u' => $facebook_url,
        ':a' => $facebook_url !== '' ? 1 : 0,
        ':l' => 'facebook'
    ]);
    $updSocial->execute([
        ':u' => $instagram_url,
        ':a' => $instagram_url !== '' ? 1 : 0,
        ':l' => 'instagram'
    ]);

    $message = 'Contact page updated successfully.';
}

// Load sections
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
$socialStmt = $pdo->query("SELECT label, url, is_active FROM contact_social_links");
$socialRaw  = $socialStmt->fetchAll(PDO::FETCH_ASSOC);
$social = [];
foreach ($socialRaw as $row) {
    $social[$row['label']] = $row;
}

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Contact | Muadh Al Zadjali</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
      background: #0b3c5d;
      color: #fff;
      min-height: 100vh;
    }
    header {
      background: rgba(255,255,255,0.96);
      color: #163b73;
      padding: 14px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header a {
      color: #163b73;
      text-decoration: none;
      font-size: 13px;
      margin-left: 10px;
    }
    main {
      max-width: 1100px;
      margin: 24px auto 32px;
      padding: 0 18px;
    }
    h1 { font-size: 22px; margin-bottom: 10px; }
    .section-card {
      background: rgba(0,0,0,0.55);
      border-radius: 10px;
      padding: 16px;
      margin-bottom: 18px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }
    .section-card h2 {
      font-size: 16px;
      margin-bottom: 8px;
    }
    label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
    }
    input[type="text"],
    textarea {
      width: 100%;
      padding: 7px 9px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 8px;
      font-size: 13px;
      color: #222;
    }
    textarea {
      min-height: 70px;
      resize: vertical;
    }
    button {
      padding: 8px 14px;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      background: #ffd54f;
      color: #163b73;
      font-size: 13px;
      font-weight: 600;
    }
    .message {
      font-size: 13px;
      color: #c8e6c9;
      margin-bottom: 6px;
    }
    .hint {
      font-size: 11px;
      color: #ddd;
      margin-bottom: 6px;
    }
  </style>
</head>
<body>
<header>
  <div>Admin – Contact Page</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage Contact Page</h1>

  <?php if ($message): ?>
    <div class="message"><?php echo esc($message); ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="section-card">
      <h2>Hero Section</h2>
      <label for="hero_title">Headline</label>
      <input type="text" id="hero_title" name="hero_title" value="<?php echo esc($hero['title']); ?>">

      <label for="hero_content">Intro Text</label>
      <textarea id="hero_content" name="hero_content"><?php echo esc($hero['content']); ?></textarea>
    </div>

    <div class="section-card">
      <h2>Contact Details</h2>
      <label for="phone_value">Phone</label>
      <input type="text" id="phone_value" name="phone_value"
             value="<?php echo esc($details['phone']['value'] ?? ''); ?>">

      <label for="phone_extra">Phone extra (e.g. timing)</label>
      <input type="text" id="phone_extra" name="phone_extra"
             value="<?php echo esc($details['phone']['extra'] ?? ''); ?>">

      <label for="email_value">Email</label>
      <input type="text" id="email_value" name="email_value"
             value="<?php echo esc($details['email']['value'] ?? ''); ?>">

      <label for="office_value">Office address</label>
      <textarea id="office_value" name="office_value"><?php
        echo esc($details['office']['value'] ?? '');
      ?></textarea>

      <label for="visit_note">Visit note</label>
      <textarea id="visit_note" name="visit_note"><?php echo esc($visitNote['content']); ?></textarea>
    </div>

    <div class="section-card">
      <h2>Social Links</h2>
      <p class="hint">
        Leave a field blank to hide that icon on the public Contact page.
        For WhatsApp you can enter either a full <code>https://wa.me/...</code> URL
        or just the phone number (country code + number).
      </p>

      <label for="whatsapp_url">WhatsApp</label>
      <input type="text" id="whatsapp_url" name="whatsapp_url"
             value="<?php echo esc($social['whatsapp']['url'] ?? ''); ?>">

      <label for="facebook_url">Facebook URL</label>
      <input type="text" id="facebook_url" name="facebook_url"
             value="<?php echo esc($social['facebook']['url'] ?? ''); ?>">

      <label for="instagram_url">Instagram URL</label>
      <input type="text" id="instagram_url" name="instagram_url"
             value="<?php echo esc($social['instagram']['url'] ?? ''); ?>">
    </div>

    <button type="submit">Save Contact Page</button>
  </form>
</main>
</body>
</html>
