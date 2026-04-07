<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php'; // uses $pdo

$message = '';
$errors  = [];

// load / ensure about_owner_popup row
$ownerStmt = $pdo->query("SELECT * FROM about_owner_popup LIMIT 1");
$ownerPopup = $ownerStmt->fetch(PDO::FETCH_ASSOC);

if (!$ownerPopup) {
    $pdo->exec("
        INSERT INTO about_owner_popup
        (is_active, owner_name, owner_title, photo_url, short_tagline, full_message)
        VALUES (0, '', '', '', '', '')
    ");
    $ownerStmt = $pdo->query("SELECT * FROM about_owner_popup LIMIT 1");
    $ownerPopup = $ownerStmt->fetch(PDO::FETCH_ASSOC);
}

$ownerId = (int)$ownerPopup['id'];

// handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // update text sections
    $sectionSlugs = ['hero','story','who','mission','vision','commitment'];
    foreach ($sectionSlugs as $slug) {
        $titleKey   = $slug . '_title';
        $contentKey = $slug . '_content';

        $title   = trim($_POST[$titleKey] ?? '');
        $content = trim($_POST[$contentKey] ?? '');

        $stmt = $pdo->prepare("
            UPDATE about_sections
            SET title = :title, content = :content
            WHERE slug = :slug
        ");
        $stmt->execute([
            ':title'   => $title,
            ':content' => $content,
            ':slug'    => $slug,
        ]);
    }

    // update stats
    if (!empty($_POST['stat_id']) && is_array($_POST['stat_id'])) {
        foreach ($_POST['stat_id'] as $idx => $id) {
            $id    = (int)$id;
            $label = trim($_POST['stat_label'][$idx] ?? '');
            $value = trim($_POST['stat_value'][$idx] ?? '');
            $desc  = trim($_POST['stat_desc'][$idx] ?? '');

            if ($label === '' && $value === '' && $desc === '') {
                continue;
            }

            $stmt = $pdo->prepare("
                UPDATE about_stats
                SET label = :l, value_display = :v, description = :d
                WHERE id = :id
            ");
            $stmt->execute([
                ':l'  => $label,
                ':v'  => $value,
                ':d'  => $desc,
                ':id' => $id,
            ]);
        }
    }

    // diagonal images upload, grouped per section
    $uploadDirRel = 'uploads/about/';
    $uploadDirAbs = __DIR__ . '/' . $uploadDirRel;
    if (!is_dir($uploadDirAbs)) {
        mkdir($uploadDirAbs, 0775, true);
    }

    // update existing images (upload new primary/hover)
    if (!empty($_POST['img_id']) && is_array($_POST['img_id'])) {
        foreach ($_POST['img_id'] as $idx => $imgId) {
            $imgId = (int)$imgId;

            $stmt = $pdo->prepare("
                SELECT primary_image, hover_image
                FROM about_images
                WHERE id = :id
            ");
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
                        $newName = 'about_p_' . $imgId . '_' . time() . '.' . $ext;
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
                        $newName = 'about_h_' . $imgId . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($_FILES['hover_image']['tmp_name'][$idx], $uploadDirAbs . $newName)) {
                            $hover = $uploadDirRel . $newName;
                        } else {
                            $errors[] = 'Failed to upload hover image for slot ' . $imgId;
                        }
                    }
                }
            }

            $up = $pdo->prepare("
                UPDATE about_images
                SET primary_image = :p, hover_image = :h
                WHERE id = :id
            ");
            $up->execute([
                ':p' => $primary,
                ':h' => $hover,
                ':id'=> $imgId,
            ]);
        }
    }

    // clear selected images (keep slot, set columns empty and unlink files)
    if (!empty($_POST['delete_img_ids']) && is_array($_POST['delete_img_ids'])) {
        foreach ($_POST['delete_img_ids'] as $deleteId) {
            $deleteId = (int)$deleteId;
            if (!$deleteId) continue;

            $stmt = $pdo->prepare("
                SELECT primary_image, hover_image
                FROM about_images
                WHERE id = :id
            ");
            $stmt->execute([':id' => $deleteId]);
            $imgRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imgRow) {
                // unlink existing files
                foreach (['primary_image','hover_image'] as $col) {
                    if (!empty($imgRow[$col])) {
                        $absPath = __DIR__ . '/' . $imgRow[$col];
                        if (is_file($absPath)) {
                            @unlink($absPath);
                        }
                    }
                }

                // keep row, just clear paths
                $clr = $pdo->prepare("
                    UPDATE about_images
                    SET primary_image = '', hover_image = ''
                    WHERE id = :id
                ");
                $clr->execute([':id' => $deleteId]);
            }
        }
    }

    // === Owner popup update ===
    $owner_active = !empty($_POST['owner_is_active']) ? 1 : 0;
    $owner_name   = trim($_POST['owner_name'] ?? '');
    $owner_title  = trim($_POST['owner_title'] ?? '');
    $owner_tag    = trim($_POST['short_tagline'] ?? '');
    $owner_full   = trim($_POST['full_message'] ?? '');

    $ownerPhoto = $ownerPopup['photo_url'] ?? '';

    $uploadDirRelOwner = 'uploads/owner/';
    $uploadDirAbsOwner = __DIR__ . '/' . $uploadDirRelOwner;
    if (!is_dir($uploadDirAbsOwner)) {
        mkdir($uploadDirAbsOwner, 0775, true);
    }

    if (!empty($_POST['owner_clear_photo']) && $ownerPhoto) {
        $abs = __DIR__ . '/' . $ownerPhoto;
        if (is_file($abs)) {
            @unlink($abs);
        }
        $ownerPhoto = '';
    }

    if (!empty($_FILES['owner_photo']['name'])) {
        if ($_FILES['owner_photo']['error'] === UPLOAD_ERR_OK) {
            $name = $_FILES['owner_photo']['name'];
            $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $newName = 'owner_' . $ownerId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['owner_photo']['tmp_name'], $uploadDirAbsOwner . $newName)) {
                    if (!empty($ownerPhoto)) {
                        $old = __DIR__ . '/' . $ownerPhoto;
                        if (is_file($old)) {
                            @unlink($old);
                        }
                    }
                    $ownerPhoto = $uploadDirRelOwner . $newName;
                } else {
                    $errors[] = 'Failed to upload owner photo.';
                }
            }
        }
    }

    $upOwner = $pdo->prepare("
        UPDATE about_owner_popup
        SET is_active = :active,
            owner_name = :name,
            owner_title = :title,
            photo_url = :photo,
            short_tagline = :tagline,
            full_message = :full
        WHERE id = :id
    ");
    $upOwner->execute([
        ':active'  => $owner_active,
        ':name'    => $owner_name,
        ':title'   => $owner_title,
        ':photo'   => $ownerPhoto,
        ':tagline' => $owner_tag,
        ':full'    => $owner_full,
        ':id'      => $ownerId,
    ]);

    $ownerPopup['is_active']     = $owner_active;
    $ownerPopup['owner_name']    = $owner_name;
    $ownerPopup['owner_title']   = $owner_title;
    $ownerPopup['photo_url']     = $ownerPhoto;
    $ownerPopup['short_tagline'] = $owner_tag;
    $ownerPopup['full_message']  = $owner_full;

    if (empty($errors)) {
        $message = 'About page updated successfully.';
    }
}

// load data
$secStmt = $pdo->query("SELECT * FROM about_sections");
$sections = [];
while ($r = $secStmt->fetch(PDO::FETCH_ASSOC)) {
    $sections[$r['slug']] = $r;
}

$statsStmt = $pdo->query("SELECT * FROM about_stats WHERE is_active = 1 ORDER BY sort_order, id");
$stats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// load images grouped by section_slug
$imgStmt = $pdo->query("
    SELECT *
    FROM about_images
    WHERE is_active = 1
    ORDER BY sort_order, id
");
$aboutImagesBySection = [];
while ($row = $imgStmt->fetch(PDO::FETCH_ASSOC)) {
    // slot_key pattern: hero_1, story_2, who_3, mission_1, etc.
    $slotKey = $row['slot_key'] ?? '';
    $parts   = explode('_', $slotKey, 2);
    $section = $parts[0] ?? '';
    if ($section === '') {
        continue; // ignore bad/missing slot_key
    }
    $aboutImagesBySection[$section][] = $row;
}

function section_images(array $aboutImagesBySection, string $slug): array {
    return $aboutImagesBySection[$slug] ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin About | Muadh Al Zadjali</title>
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
      max-width: 1050px;
      margin: 24px auto 32px;
      padding: 0 18px;
    }
    h1 { font-size: 22px; margin-bottom: 10px; }
    .section-card {
      background: rgba(0,0,0,0.55);
      border-radius: 10px;
      padding: 16px;
      margin-bottom: 18px;
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
    }
    th { background: rgba(0,0,0,0.6); }

    .img-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      margin-top: 8px;
      margin-bottom: 12px;
    }
    .img-slot {
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 8px;
      font-size: 12px;
      background: rgba(0,0,0,0.35);
    }
    .img-slot strong {
      display: block;
      margin-bottom: 4px;
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
    .img-preview .placeholder {
      width: 70px;
      height: 70px;
      border: 1px dashed #ccc;
      border-radius: 4px;
      font-size: 11px;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#999;
      background:#fff;
      text-align:center;
      padding:2px;
    }
    .section-label {
      margin-top: 14px;
      font-size: 14px;
      font-weight: 600;
      border-top: 1px solid rgba(255,255,255,0.25);
      padding-top: 10px;
    }
    .delete-flag {
      margin-top: 4px;
      font-size: 12px;
      color: #ffccbc;
    }
    .delete-flag input {
      margin-right: 4px;
    }

    .owner-box {
      margin-top: 16px;
      padding-top: 12px;
      border-top: 1px solid rgba(255,255,255,0.35);
    }
    .owner-row {
      display: grid;
      grid-template-columns: minmax(0,1.7fr) minmax(0,1.3fr);
      gap: 16px;
      margin-top: 8px;
    }
    .owner-photo-preview {
      margin-bottom: 8px;
    }
    .owner-photo-preview img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ffd54f;
      background:#fff;
      display:block;
    }
    .small-note {
      font-size: 11px;
      color: #ffe082;
      margin-top: 2px;
    }
    @media (max-width: 800px) {
      .owner-row {
        grid-template-columns: minmax(0,1fr);
      }
    }
  </style>
</head>
<body>
<header>
  <div>Admin – About</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage About Page</h1>

  <div class="section-card">
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
      <!-- Hero -->
      <h2 style="font-size:16px;margin-bottom:4px;">Hero Section</h2>
      <label for="hero_title">Headline</label>
      <input type="text" id="hero_title" name="hero_title"
             value="<?php echo htmlspecialchars($sections['hero']['title'] ?? ''); ?>">
      <label for="hero_content">Sub text</label>
      <textarea id="hero_content" name="hero_content"><?php
        echo htmlspecialchars($sections['hero']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Hero – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'hero') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Story -->
      <h2 style="font-size:16px;margin:16px 0 4px;">Our Story</h2>
      <label for="story_title">Title</label>
      <input type="text" id="story_title" name="story_title"
             value="<?php echo htmlspecialchars($sections['story']['title'] ?? ''); ?>">
      <label for="story_content">Content</label>
      <textarea id="story_content" name="story_content"><?php
        echo htmlspecialchars($sections['story']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Story – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'story') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Who / Mission / Vision / Commitment -->
      <h2 style="font-size:16px;margin:16px 0 4px;">Who We Are / Mission / Vision / Commitment</h2>

      <label for="who_title">Who We Are – Title</label>
      <input type="text" id="who_title" name="who_title"
             value="<?php echo htmlspecialchars($sections['who']['title'] ?? ''); ?>">
      <label for="who_content">Who We Are – Content</label>
      <textarea id="who_content" name="who_content"><?php
        echo htmlspecialchars($sections['who']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Who We Are – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'who') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <label for="mission_title">Mission – Title</label>
      <input type="text" id="mission_title" name="mission_title"
             value="<?php echo htmlspecialchars($sections['mission']['title'] ?? ''); ?>">
      <label for="mission_content">Mission – Content</label>
      <textarea id="mission_content" name="mission_content"><?php
        echo htmlspecialchars($sections['mission']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Mission – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'mission') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <label for="vision_title">Vision – Title</label>
      <input type="text" id="vision_title" name="vision_title"
             value="<?php echo htmlspecialchars($sections['vision']['title'] ?? ''); ?>">
      <label for="vision_content">Vision – Content</label>
      <textarea id="vision_content" name="vision_content"><?php
        echo htmlspecialchars($sections['vision']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Vision – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'vision') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <label for="commitment_title">Commitment – Title</label>
      <input type="text" id="commitment_title" name="commitment_title"
             value="<?php echo htmlspecialchars($sections['commitment']['title'] ?? ''); ?>">
      <label for="commitment_content">Commitment – Content</label>
      <textarea id="commitment_content" name="commitment_content"><?php
        echo htmlspecialchars($sections['commitment']['content'] ?? '');
      ?></textarea>

      <div class="section-label">Commitment – Diagonal Images</div>
      <div class="img-grid">
        <?php foreach (section_images($aboutImagesBySection, 'commitment') as $img): ?>
          <div class="img-slot">
            <input type="hidden" name="img_id[]" value="<?php echo (int)$img['id']; ?>">
            <strong>Slot <?php echo (int)$img['sort_order']; ?> (<?php echo htmlspecialchars($img['slot_key']); ?>)</strong>
            <div class="img-preview">
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Primary</div>
                <?php if ($img['primary_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['primary_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
              <div>
                <div style="font-size:11px;margin-bottom:2px;">Hover</div>
                <?php if ($img['hover_image']): ?>
                  <img src="<?php echo htmlspecialchars($img['hover_image']); ?>" alt="">
                <?php else: ?>
                  <div class="placeholder">No image</div>
                <?php endif; ?>
              </div>
            </div>
            <label>Upload Primary</label>
            <input type="file" name="primary_image[]" accept="image/*">
            <label>Upload Hover</label>
            <input type="file" name="hover_image[]" accept="image/*">
            <div class="delete-flag">
              <label>
                <input type="checkbox" name="delete_img_ids[]" value="<?php echo (int)$img['id']; ?>">
                Clear images for this slot
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Stats -->
      <h2 style="font-size:16px;margin:16px 0 4px;">Key Project Milestones</h2>
      <table>
        <thead>
        <tr>
          <th>Label</th>
          <th>Value display</th>
          <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($stats as $row): ?>
          <tr>
            <td>
              <input type="hidden" name="stat_id[]" value="<?php echo (int)$row['id']; ?>">
              <input type="text" name="stat_label[]" value="<?php echo htmlspecialchars($row['label']); ?>">
            </td>
            <td>
              <input type="text" name="stat_value[]" value="<?php echo htmlspecialchars($row['value_display']); ?>">
            </td>
            <td>
              <input type="text" name="stat_desc[]" value="<?php echo htmlspecialchars($row['description']); ?>">
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Owner Popup -->
      <div class="owner-box">
        <h2 style="font-size:16px;margin-bottom:4px;">Owner Popup (About Page)</h2>
        <label>
          <input type="checkbox" name="owner_is_active" value="1"
            <?php echo !empty($ownerPopup['is_active']) ? 'checked' : ''; ?>>
          Activate owner popup on About page
        </label>
        <div class="small-note">
          When enabled, a small bubble with owner photo and tagline appears at the bottom-left of About page. Clicking it opens the animated message popup.
        </div>

        <div class="owner-row">
          <div>
            <label for="owner_name">Owner name</label>
            <input type="text" id="owner_name" name="owner_name"
                   value="<?php echo htmlspecialchars($ownerPopup['owner_name'] ?? ''); ?>">

            <label for="owner_title">Owner title / designation</label>
            <input type="text" id="owner_title" name="owner_title"
                   value="<?php echo htmlspecialchars($ownerPopup['owner_title'] ?? ''); ?>">

            <label for="short_tagline">Short tagline (small bubble)</label>
            <input type="text" id="short_tagline" name="short_tagline"
                   value="<?php echo htmlspecialchars($ownerPopup['short_tagline'] ?? ''); ?>">

            <label for="full_message">Full message (shown in animated popup)</label>
            <textarea id="full_message" name="full_message" rows="6"><?php
              echo htmlspecialchars($ownerPopup['full_message'] ?? '');
            ?></textarea>
          </div>

          <div>
            <label>Owner photo</label>
            <div class="owner-photo-preview">
              <?php if (!empty($ownerPopup['photo_url'])): ?>
                <img src="<?php echo htmlspecialchars($ownerPopup['photo_url']); ?>" alt="">
              <?php else: ?>
                <div class="img-preview">
                  <div class="placeholder" style="width:90px;height:90px;border-radius:50%;">No photo</div>
                </div>
              <?php endif; ?>
            </div>
            <input type="file" name="owner_photo" accept="image/*">
            <?php if (!empty($ownerPopup['photo_url'])): ?>
              <div class="delete-flag">
                <label>
                  <input type="checkbox" name="owner_clear_photo" value="1">
                  Remove current photo
                </label>
              </div>
            <?php endif; ?>
            <div class="small-note">
              Recommended: square image (at least 300×300), JPG/PNG/WebP.
            </div>
          </div>
        </div>
      </div>

      <div style="margin-top:12px;">
        <button type="submit">Save About Page</button>
      </div>
    </form>
  </div>
</main>
</body>
</html>
