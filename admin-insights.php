<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php'; // must define $pdo

$message = '';
$errors  = [];

// HANDLE POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Hero section
    $hero_title   = trim($_POST['hero_title'] ?? '');
    $hero_content = trim($_POST['hero_content'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE insights_sections
        SET title = :t, content = :c
        WHERE slug = 'hero'
    ");
    $stmt->execute([
        ':t' => $hero_title,
        ':c' => $hero_content,
    ]);

    // Featured article
    $feat_title   = trim($_POST['feat_title'] ?? '');
    $feat_cat     = trim($_POST['feat_category'] ?? '');
    $feat_date    = trim($_POST['feat_date'] ?? '');
    $feat_minutes = (int)($_POST['feat_minutes'] ?? 0);
    $feat_summary = trim($_POST['feat_summary'] ?? '');

    // ensure one row exists
    $count = (int)$pdo->query("SELECT COUNT(*) FROM insights_featured")->fetchColumn();
    if ($count === 0) {
        $ins = $pdo->prepare("INSERT INTO insights_featured (title, category, published_at, read_minutes, summary) VALUES (:t,:c,:d,:m,:s)");
        $ins->execute([
            ':t'=>$feat_title, ':c'=>$feat_cat, ':d'=>$feat_date ?: null, ':m'=>$feat_minutes ?: null, ':s'=>$feat_summary
        ]);
    } else {
        $upd = $pdo->prepare("UPDATE insights_featured SET title=:t, category=:c, published_at=:d, read_minutes=:m, summary=:s WHERE id = (SELECT id FROM (SELECT id FROM insights_featured ORDER BY id LIMIT 1) AS x)");
        $upd->execute([
            ':t'=>$feat_title, ':c'=>$feat_cat, ':d'=>$feat_date ?: null, ':m'=>$feat_minutes ?: null, ':s'=>$feat_summary
        ]);
    }

    // Articles inline update
    if (!empty($_POST['art_id']) && is_array($_POST['art_id'])) {
        foreach ($_POST['art_id'] as $idx => $id) {
            $id = (int)$id;
            $title   = trim($_POST['art_title'][$idx] ?? '');
            $catSlug = trim($_POST['art_cat_slug'][$idx] ?? '');
            $catLbl  = trim($_POST['art_cat_label'][$idx] ?? '');
            $date    = trim($_POST['art_date'][$idx] ?? '');
            $mins    = (int)($_POST['art_minutes'][$idx] ?? 0);
            $excerpt = trim($_POST['art_excerpt'][$idx] ?? '');
            $sort    = (int)($_POST['art_sort'][$idx] ?? 0);

            if ($title === '') continue;

            $stmt = $pdo->prepare("
                UPDATE insights_articles
                SET title = :t,
                    category_slug = :cs,
                    category_label = :cl,
                    published_at = :d,
                    read_minutes = :m,
                    excerpt = :e,
                    sort_order = :s
                WHERE id = :id
            ");
            $stmt->execute([
                ':t'=>$title,
                ':cs'=>$catSlug,
                ':cl'=>$catLbl,
                ':d'=>$date ?: null,
                ':m'=>$mins ?: null,
                ':e'=>$excerpt,
                ':s'=>$sort,
                ':id'=>$id,
            ]);
        }
    }

    // OPTIONAL: new article add (simple)
    if (!empty($_POST['new_title'])) {
        $nt  = trim($_POST['new_title']);
        $ncs = trim($_POST['new_cat_slug']);
        $ncl = trim($_POST['new_cat_label']);
        $nd  = trim($_POST['new_date']);
        $nm  = (int)($_POST['new_minutes'] ?? 0);
        $ne  = trim($_POST['new_excerpt']);
        $ns  = (int)($_POST['new_sort'] ?? 0);

        if ($nt !== '' && $ncs !== '' && $ncl !== '') {
            $ins = $pdo->prepare("
                INSERT INTO insights_articles
                (title, category_slug, category_label, published_at, read_minutes, excerpt, sort_order)
                VALUES (:t,:cs,:cl,:d,:m,:e,:s)
            ");
            $ins->execute([
                ':t'=>$nt,
                ':cs'=>$ncs,
                ':cl'=>$ncl,
                ':d'=>$nd ?: null,
                ':m'=>$nm ?: null,
                ':e'=>$ne,
                ':s'=>$ns,
            ]);
        } else {
            $errors[] = 'To add a new article please fill title, category slug, and category label.';
        }
    }

    // Diagonal images (like about)
    $uploadDirRel = 'uploads/insights/';
    $uploadDirAbs = __DIR__ . '/' . $uploadDirRel;
    if (!is_dir($uploadDirAbs)) {
        mkdir($uploadDirAbs, 0775, true);
    }

    if (!empty($_POST['img_id']) && is_array($_POST['img_id'])) {
        foreach ($_POST['img_id'] as $idx => $imgId) {
            $imgId = (int)$imgId;

            $stmt = $pdo->prepare("SELECT primary_image, hover_image FROM insights_images WHERE id = :id");
            $stmt->execute([':id' => $imgId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) continue;

            $primary = $row['primary_image'];
            $hover   = $row['hover_image'];

            // primary upload
            if (!empty($_FILES['primary_image']['name'][$idx])) {
                if ($_FILES['primary_image']['error'][$idx] === UPLOAD_ERR_OK) {
                    $name = $_FILES['primary_image']['name'][$idx];
                    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                        $newName = 'ins_p_' . $imgId . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['primary_image']['tmp_name'][$idx], $uploadDirAbs . $newName)) {
                            $primary = $uploadDirRel . $newName;
                        } else {
                            $errors[] = 'Failed to upload primary image for slot ' . $imgId;
                        }
                    }
                }
            }

            // hover upload
            if (!empty($_FILES['hover_image']['name'][$idx])) {
                if ($_FILES['hover_image']['error'][$idx] === UPLOAD_ERR_OK) {
                    $name = $_FILES['hover_image']['name'][$idx];
                    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                        $newName = 'ins_h_' . $imgId . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['hover_image']['tmp_name'][$idx], $uploadDirAbs . $newName)) {
                            $hover = $uploadDirRel . $newName;
                        } else {
                            $errors[] = 'Failed to upload hover image for slot ' . $imgId;
                        }
                    }
                }
            }

            $up = $pdo->prepare("
                UPDATE insights_images
                SET primary_image = :p, hover_image = :h
                WHERE id = :id
            ");
            $up->execute([
                ':p'=>$primary,
                ':h'=>$hover,
                ':id'=>$imgId,
            ]);
        }
    }

    if (!$errors) {
        $message = 'Insights page updated successfully.';
    }
}

// LOAD DATA
$heroRow = $pdo->query("SELECT * FROM insights_sections WHERE slug = 'hero'")->fetch(PDO::FETCH_ASSOC);

$featured = $pdo->query("SELECT * FROM insights_featured ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$articlesStmt = $pdo->query("SELECT * FROM insights_articles WHERE is_active = 1 ORDER BY sort_order, id");
$articles = $articlesStmt->fetchAll(PDO::FETCH_ASSOC);

$imgStmt = $pdo->query("SELECT * FROM insights_images WHERE is_active = 1 ORDER BY sort_order, id");
$insightsImages = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Insights | Muadh Al Zadjali</title>
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
    label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
    }
    input[type="text"],
    input[type="number"],
    input[type="date"],
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
    input[type="file"] {
      margin-bottom: 6px;
      font-size: 13px;
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
    .message { font-size: 13px; color: #c8e6c9; margin-bottom: 6px; }
    .errors { font-size: 13px; color: #ff8a80; margin-bottom: 6px; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 13px;
      background: rgba(0,0,0,0.35);
    }
    th, td {
      border: 1px solid rgba(255,255,255,0.2);
      padding: 6px 8px;
      text-align: left;
      vertical-align: top;
    }
    th { background: rgba(0,0,0,0.6); }
    .img-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin-top: 8px;
    }
    .img-slot {
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 8px;
      font-size: 12px;
    }
    .img-preview {
      display: flex;
      gap: 8px;
      margin-bottom: 6px;
    }
    .img-preview img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #ccc;
      background:#fff;
    }
  </style>
</head>
<body>
<header>
  <div>Admin – Insights</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage Insights Page</h1>

  <?php if ($message): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="errors">
      <?php foreach ($errors as $e): ?>
        <div><?php echo htmlspecialchars($e); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">

    <div class="section-card">
      <h2 style="font-size:16px;margin-bottom:6px;">Hero Section</h2>
      <label for="hero_title">Headline</label>
      <input type="text" id="hero_title" name="hero_title"
             value="<?php echo htmlspecialchars($heroRow['title'] ?? ''); ?>">
      <label for="hero_content">Intro Text</label>
      <textarea id="hero_content" name="hero_content"><?php
        echo htmlspecialchars($heroRow['content'] ?? '');
      ?></textarea>
    </div>

    <div class="section-card">
      <h2 style="font-size:16px;margin-bottom:6px;">Featured Article</h2>
      <label for="feat_title">Title</label>
      <input type="text" id="feat_title" name="feat_title"
             value="<?php echo htmlspecialchars($featured['title'] ?? ''); ?>">

      <label for="feat_category">Category label (e.g. Safety)</label>
      <input type="text" id="feat_category" name="feat_category"
             value="<?php echo htmlspecialchars($featured['category'] ?? ''); ?>">

      <label for="feat_date">Published date</label>
      <input type="date" id="feat_date" name="feat_date"
             value="<?php echo htmlspecialchars($featured['published_at'] ?? ''); ?>">

      <label for="feat_minutes">Read minutes</label>
      <input type="number" id="feat_minutes" name="feat_minutes"
             value="<?php echo htmlspecialchars($featured['read_minutes'] ?? ''); ?>">

      <label for="feat_summary">Summary</label>
      <textarea id="feat_summary" name="feat_summary"><?php
        echo htmlspecialchars($featured['summary'] ?? '');
      ?></textarea>
    </div>

    <div class="section-card">
      <h2 style="font-size:16px;margin-bottom:6px;">Recent Articles (existing)</h2>
      <table>
        <thead>
        <tr>
          <th>Title</th>
          <th>Category Slug</th>
          <th>Category Label</th>
          <th>Date</th>
          <th>Minutes</th>
          <th>Excerpt</th>
          <th>Order</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
          <tr>
            <td>
              <input type="hidden" name="art_id[]" value="<?php echo (int)$a['id']; ?>">
              <input type="text" name="art_title[]" value="<?php echo htmlspecialchars($a['title']); ?>">
            </td>
            <td>
              <input type="text" name="art_cat_slug[]" value="<?php echo htmlspecialchars($a['category_slug']); ?>">
            </td>
            <td>
              <input type="text" name="art_cat_label[]" value="<?php echo htmlspecialchars($a['category_label']); ?>">
            </td>
            <td>
              <input type="date" name="art_date[]" value="<?php echo htmlspecialchars($a['published_at']); ?>">
            </td>
            <td>
              <input type="number" name="art_minutes[]" value="<?php echo htmlspecialchars($a['read_minutes']); ?>">
            </td>
            <td>
              <textarea name="art_excerpt[]" style="min-height:60px;"><?php
                echo htmlspecialchars($a['excerpt']);
              ?></textarea>
            </td>
            <td>
              <input type="number" name="art_sort[]" value="<?php echo (int)$a['sort_order']; ?>" style="width:60px;">
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <h3 style="font-size:14px;margin-top:10px;">Add New Article</h3>
      <table>
        <tr>
          <td style="width:20%;">
            <label>Title</label>
            <input type="text" name="new_title">
          </td>
          <td style="width:15%;">
            <label>Category Slug</label>
            <input type="text" name="new_cat_slug" placeholder="safety">
          </td>
          <td style="width:15%;">
            <label>Category Label</label>
            <input type="text" name="new_cat_label" placeholder="Safety">
          </td>
          <td style="width:15%;">
            <label>Date</label>
            <input type="date" name="new_date">
          </td>
          <td style="width:10%;">
            <label>Minutes</label>
            <input type="number" name="new_minutes" style="width:70px;">
          </td>
          <td>
            <label>Excerpt</label>
            <textarea name="new_excerpt" style="min-height:60px;"></textarea>
          </td>
          <td style="width:10%;">
            <label>Order</label>
            <input type="number" name="new_sort" style="width:60px;" value="10">
          </td>
        </tr>
      </table>
    </div>

    <div class="section-card">
      <h2 style="font-size:16px;margin-bottom:6px;">Diagonal Images (optional)</h2>
      <p style="font-size:12px;margin-bottom:6px;">
        Upload primary and hover images for the diagonal gallery on the Insights page.
      </p>
      <div class="img-grid">
        <?php foreach ($insightsImages as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div style="width:70px;height:70px;border:1px dashed #ccc;border-radius:4px;font-size:11px;display:flex;align-items:center;justify-content:center;color:#999;background:#fff;">
                    No image
                  </div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div style="width:70px;height:70px;border:1px dashed #ccc;border-radius:4px;font-size:11px;display:flex;align-items:center;justify-content:center;color:#999;background:#fff;">
                    No image
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <label>Upload Primary Image</label>
            <input type="file" name="primary_image[]">

            <label>Upload Hover Image</label>
            <input type="file" name="hover_image[]">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <button type="submit">Save Insights Page</button>
  </form>
</main>
</body>
</html>
