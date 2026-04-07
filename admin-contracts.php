<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php';

$message = '';
$errors  = [];

/*
  DB REMINDER:

  CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL,
    en_title VARCHAR(255) NOT NULL,
    en_description TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    media_json TEXT DEFAULT NULL,
    sort_order INT DEFAULT 0
  );
*/

// Handle DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];

    // Remove files (images/videos) from disk
    $stmt = $pdo->prepare("SELECT image_path, media_json FROM contracts WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        // legacy image_path
        if (!empty($row['image_path'])) {
            $paths = array_filter(array_map('trim', explode(',', $row['image_path'])));
            foreach ($paths as $p) {
                $full = __DIR__ . '/' . $p;
                if (is_file($full)) {
                    @unlink($full);
                }
            }
        }
        // media_json (image/video)
        if (!empty($row['media_json'])) {
            $media = json_decode($row['media_json'], true);
            if (is_array($media)) {
                foreach ($media as $m) {
                    if (empty($m['src'])) continue;
                    $p   = trim($m['src']);
                    $full = __DIR__ . '/' . $p;
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
            }
        }

        $del = $pdo->prepare("DELETE FROM contracts WHERE id = :id");
        $del->execute([':id' => $deleteId]);
        $message = 'Contract deleted successfully.';
    }
}

// Handle ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['delete_id'])) {

    $mode    = $_POST['mode'] ?? 'add'; // add | edit
    $editId  = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    $slug           = trim($_POST['slug'] ?? '');
    $en_title       = trim($_POST['en_title'] ?? '');
    $en_description = trim($_POST['en_description'] ?? '');
    $sort_order     = (int)($_POST['sort_order'] ?? 0);

    $existing_media_json = $_POST['existing_media_json'] ?? '[]';
    $existing_media      = json_decode($existing_media_json, true);
    if (!is_array($existing_media)) {
        $existing_media = [];
    }

    $delete_indexes = isset($_POST['delete_media']) && is_array($_POST['delete_media'])
        ? array_map('intval', $_POST['delete_media'])
        : [];

    if ($slug === '' || $en_title === '' || $en_description === '') {
        $errors[] = 'Please fill in slug, English title, and English description.';
    }

    $uploadDirRel = 'uploads/';
    $uploadDirAbs = __DIR__ . '/' . $uploadDirRel;
    if (!is_dir($uploadDirAbs)) {
        mkdir($uploadDirAbs, 0777, true);
    }

    $final_media = [];

    // Keep existing media except deleted ones
    foreach ($existing_media as $idx => $item) {
        if (in_array((int)$idx, $delete_indexes, true)) {
            if (!empty($item['src'])) {
                $full = __DIR__ . '/' . $item['src'];
                if (is_file($full)) {
                    @unlink($full);
                }
            }
            continue;
        }
        if (!empty($item['type']) && !empty($item['src'])) {
            $final_media[] = [
                'type' => $item['type'] === 'video' ? 'video' : 'image',
                'src'  => $item['src'],
            ];
        }
    }

    // New image uploads
    if (!empty($_FILES['image']['name']) && is_array($_FILES['image']['name'])) {
        foreach ($_FILES['image']['name'] as $idx => $name) {
            if (!$name) continue;

            if ($_FILES['image']['error'][$idx] === UPLOAD_ERR_OK) {
                $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $safeName = uniqid('ctr_img_', true) . '.' . $ext;
                $targetAbs = $uploadDirAbs . $safeName;

                if (move_uploaded_file($_FILES['image']['tmp_name'][$idx], $targetAbs)) {
                    $final_media[] = [
                        'type' => 'image',
                        'src'  => $uploadDirRel . $safeName,
                    ];
                } else {
                    $errors[] = 'Failed to move uploaded image: ' . htmlspecialchars($name);
                }
            } else {
                $errors[] = 'Error uploading image: ' . htmlspecialchars($name);
            }
        }
    }

    // New video uploads
    if (!empty($_FILES['video']['name']) && is_array($_FILES['video']['name'])) {
        foreach ($_FILES['video']['name'] as $idx => $name) {
            if (!$name) continue;

            if ($_FILES['video']['error'][$idx] === UPLOAD_ERR_OK) {
                $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $safeName = uniqid('ctr_vid_', true) . '.' . $ext;
                $targetAbs = $uploadDirAbs . $safeName;

                if (move_uploaded_file($_FILES['video']['tmp_name'][$idx], $targetAbs)) {
                    $final_media[] = [
                        'type' => 'video',
                        'src'  => $uploadDirRel . $safeName,
                    ];
                } else {
                    $errors[] = 'Failed to move uploaded video: ' . htmlspecialchars($name);
                }
            } else {
                $errors[] = 'Error uploading video: ' . htmlspecialchars($name);
            }
        }
    }

    $media_json = json_encode($final_media, JSON_UNESCAPED_SLASHES);

    if (empty($errors)) {
        if ($mode === 'edit' && $editId > 0) {
            $stmt = $pdo->prepare("
                UPDATE contracts
                SET slug = :slug,
                    en_title = :en_title,
                    en_description = :en_description,
                    media_json = :media_json,
                    sort_order = :sort_order
                WHERE id = :id
            ");
            $stmt->execute([
                ':slug'           => $slug,
                ':en_title'       => $en_title,
                ':en_description' => $en_description,
                ':media_json'     => $media_json,
                ':sort_order'     => $sort_order,
                ':id'             => $editId,
            ]);
            $message = 'Contract updated successfully.';
        } else {
            $stmt = $pdo->prepare("
              INSERT INTO contracts (
                slug,
                en_title,
                en_description,
                image_path,
                media_json,
                sort_order
              ) VALUES (
                :slug,
                :en_title,
                :en_description,
                NULL,
                :media_json,
                :sort_order
              )
            ");
            $stmt->execute([
                ':slug'           => $slug,
                ':en_title'       => $en_title,
                ':en_description' => $en_description,
                ':media_json'     => $media_json,
                ':sort_order'     => $sort_order,
            ]);
            $message = 'Contract added successfully.';
        }
    }
}

// Load existing contracts
$stmt = $pdo->query("SELECT * FROM contracts ORDER BY sort_order, id");
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for JS
$contractsForJs = [];
foreach ($contracts as $c) {
    $mediaItems = [];
    if (!empty($c['media_json'])) {
        $decoded = json_decode($c['media_json'], true);
        if (is_array($decoded)) {
            $mediaItems = $decoded;
        }
    } elseif (!empty($c['image_path'])) {
        $parts = array_filter(array_map('trim', explode(',', $c['image_path'])));
        foreach ($parts as $p) {
            $mediaItems[] = ['type' => 'image', 'src' => $p];
        }
    }

    $contractsForJs[(int)$c['id']] = [
        'id'             => (int)$c['id'],
        'slug'           => $c['slug'],
        'en_title'       => $c['en_title'],
        'en_description' => $c['en_description'],
        'sort_order'     => (int)$c['sort_order'],
        'media'          => $mediaItems,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Contracts | Muadh Al Zadjali</title>
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
      max-width: 1000px;
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
    input[type="number"],
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
      margin-bottom: 10px;
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
    button.btn-small {
      padding: 5px 10px;
      font-size: 12px;
      border-radius: 999px;
    }
    button.btn-delete {
      background: #ff7043;
      color: #fff;
    }
    .message {
      font-size: 13px;
      color: #c8e6c9;
      margin-bottom: 8px;
    }
    .errors {
      font-size: 13px;
      color: #ff8a80;
      margin-bottom: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
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
    img.thumb {
      max-width: 80px;
      max-height: 60px;
      border-radius: 4px;
      margin-right: 4px;
      margin-bottom: 3px;
      display: inline-block;
    }
    .thumb-video {
      display: inline-block;
      width: 80px;
      height: 60px;
      border-radius: 4px;
      margin-right: 4px;
      margin-bottom: 3px;
      background: rgba(0,0,0,0.75);
      color: #ffd54f;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .actions-cell {
      white-space: nowrap;
    }
    .media-existing-list {
      font-size: 12px;
      margin-bottom: 6px;
      background: rgba(255,255,255,0.06);
      border-radius: 6px;
      padding: 6px;
      max-height: 140px;
      overflow-y: auto;
    }
    .media-existing-item {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 4px;
    }
    .media-existing-item label {
      margin: 0;
      font-size: 12px;
      color: #eee;
    }
    .badge-type {
      font-size: 10px;
      padding: 2px 5px;
      border-radius: 999px;
      background: rgba(255,213,79,0.15);
      border: 1px solid rgba(255,213,79,0.7);
      color: #ffd54f;
    }
  </style>
</head>
<body>
<header>
  <div>Admin – Contracts</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage Contracts</h1>

  <div class="section-card">
    <h2 style="font-size:16px;margin-bottom:8px;" id="form-title">Add New Contract</h2>
    <p style="font-size:12px;margin-bottom:6px;" id="form-helper">
      Enter the contract details in English. You can upload multiple images and videos; the first media item will be shown on the Contracts page.
    </p>

    <?php if ($message): ?>
      <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="errors">
        <?php foreach ($errors as $e): ?>
          <div><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="contractForm">
      <input type="hidden" name="mode" id="mode" value="add">
      <input type="hidden" name="edit_id" id="edit_id" value="">
      <input type="hidden" name="existing_media_json" id="existing_media_json" value="[]">

      <label for="slug">Slug (unique key, e.g. fixed-price)</label>
      <input type="text" name="slug" id="slug" required>

      <label for="sort_order">Sort Order (lower appears first)</label>
      <input type="number" name="sort_order" id="sort_order" value="0">

      <label for="en_title">Title (English)</label>
      <input type="text" name="en_title" id="en_title" required>

      <label for="en_description">Description (English)</label>
      <textarea name="en_description" id="en_description" required></textarea>

      <div id="existingMediaWrapper" style="display:none;margin-top:4px;">
        <label>Existing Media (uncheck to remove specific items):</label>
        <div class="media-existing-list" id="existingMediaList"></div>
      </div>

      <label for="image">Add Images (optional, you can select multiple)</label>
      <input type="file" name="image[]" id="image" accept="image/*" multiple>

      <label for="video">Add Videos (optional, you can select multiple)</label>
      <input type="file" name="video[]" id="video" accept="video/*" multiple>

      <p style="font-size:11px;margin-bottom:8px;color:#eee;">
        When editing: existing media is listed above; uncheck items to remove them. Newly uploaded files will be appended.
      </p>

      <button type="submit" id="submitBtn">Add Contract</button>
      <button type="button" id="cancelEditBtn" style="display:none;margin-left:8px;">Cancel Edit</button>
    </form>
  </div>

  <div class="section-card">
    <h2 style="font-size:16px;margin-bottom:8px;">Existing Contracts</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Slug</th>
          <th>Title (EN)</th>
          <th>Media</th>
          <th>Order</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$contracts): ?>
        <tr><td colspan="6">No contracts added yet.</td></tr>
      <?php else: ?>
        <?php foreach ($contracts as $c):
          $mediaPreview = [];
          if (!empty($c['media_json'])) {
              $mj = json_decode($c['media_json'], true);
              if (is_array($mj)) $mediaPreview = $mj;
          } elseif (!empty($c['image_path'])) {
              $parts = array_filter(array_map('trim', explode(',', $c['image_path'])));
              foreach ($parts as $p) {
                  $mediaPreview[] = ['type' => 'image', 'src' => $p];
              }
          }
        ?>
          <tr>
            <td><?php echo (int)$c['id']; ?></td>
            <td><?php echo htmlspecialchars($c['slug']); ?></td>
            <td><?php echo htmlspecialchars($c['en_title']); ?></td>
            <td>
              <?php if (empty($mediaPreview)): ?>
                —
              <?php else: ?>
                <?php foreach ($mediaPreview as $m): ?>
                  <?php if (($m['type'] ?? 'image') === 'video'): ?>
                    <span class="thumb-video">VIDEO</span>
                  <?php else: ?>
                    <img src="<?php echo htmlspecialchars($m['src']); ?>" class="thumb" alt="">
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </td>
            <td><?php echo (int)$c['sort_order']; ?></td>
            <td class="actions-cell">
              <button
                type="button"
                class="btn-small"
                onclick="startEdit(<?php echo (int)$c['id']; ?>)"
              >Edit</button>

              <form method="post" style="display:inline;" onsubmit="return confirm('Delete this contract?');">
                <input type="hidden" name="delete_id" value="<?php echo (int)$c['id']; ?>">
                <button type="submit" class="btn-small btn-delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
  const contractsData = <?php echo json_encode($contractsForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

  const modeInput        = document.getElementById('mode');
  const editIdInput      = document.getElementById('edit_id');
  const slugInput        = document.getElementById('slug');
  const sortInput        = document.getElementById('sort_order');
  const titleInput       = document.getElementById('en_title');
  const descInput        = document.getElementById('en_description');
  const fileImageInput   = document.getElementById('image');
  const fileVideoInput   = document.getElementById('video');
  const formTitle        = document.getElementById('form-title');
  const formHelper       = document.getElementById('form-helper');
  const submitBtn        = document.getElementById('submitBtn');
  const cancelEditBtn    = document.getElementById('cancelEditBtn');
  const existingMediaJsonInput = document.getElementById('existing_media_json');
  const existingMediaWrapper   = document.getElementById('existingMediaWrapper');
  const existingMediaList      = document.getElementById('existingMediaList');

  function resetFormToAdd() {
    modeInput.value        = 'add';
    editIdInput.value      = '';
    slugInput.value        = '';
    sortInput.value        = '0';
    titleInput.value       = '';
    descInput.value        = '';
    fileImageInput.value   = '';
    fileVideoInput.value   = '';
    existingMediaJsonInput.value = '[]';
    existingMediaWrapper.style.display = 'none';
    existingMediaList.innerHTML = '';

    formTitle.textContent  = 'Add New Contract';
    formHelper.textContent = 'Enter the contract details in English. You can upload multiple images and videos; the first media item will be shown on the Contracts page.';
    submitBtn.textContent  = 'Add Contract';
    cancelEditBtn.style.display = 'none';
  }

  function renderExistingMediaList(media) {
    existingMediaList.innerHTML = '';
    if (!media || !media.length) {
      existingMediaWrapper.style.display = 'none';
      return;
    }
    existingMediaWrapper.style.display = 'block';

    media.forEach((item, idx) => {
      const row = document.createElement('div');
      row.className = 'media-existing-item';

      const checkbox = document.createElement('input');
      checkbox.type  = 'checkbox';
      checkbox.name  = 'keep_media[]';
      checkbox.value = idx;
      checkbox.checked = true;
      checkbox.dataset.index = idx;

      checkbox.addEventListener('change', () => {
        const name = 'delete_media[]';
        let hidden = row.querySelector('input[type="hidden"]');
        if (!checkbox.checked) {
          if (!hidden) {
            hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = name;
            hidden.value = idx;
            row.appendChild(hidden);
          }
        } else if (hidden) {
          hidden.remove();
        }
      });

      const label = document.createElement('label');
      const typeSpan = document.createElement('span');
      typeSpan.className = 'badge-type';
      typeSpan.textContent = (item.type === 'video' ? 'Video' : 'Image');

      const textSpan = document.createElement('span');
      textSpan.textContent = ' ' + (item.src || '');

      label.appendChild(typeSpan);
      label.appendChild(textSpan);

      row.appendChild(checkbox);
      row.appendChild(label);
      existingMediaList.appendChild(row);
    });
  }

  function startEdit(id) {
    const c = contractsData[id];
    if (!c) return;

    modeInput.value        = 'edit';
    editIdInput.value      = c.id;
    slugInput.value        = c.slug;
    sortInput.value        = String(c.sort_order);
    titleInput.value       = c.en_title;
    descInput.value        = c.en_description;
    fileImageInput.value   = '';
    fileVideoInput.value   = '';

    const media = c.media || [];
    existingMediaJsonInput.value = JSON.stringify(media);
    renderExistingMediaList(media);

    formTitle.textContent  = 'Edit Contract #' + c.id;
    formHelper.textContent = 'Update the fields below. Existing media is listed; uncheck items to remove them. New uploads will be added.';
    submitBtn.textContent  = 'Update Contract';
    cancelEditBtn.style.display = 'inline-block';

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  cancelEditBtn.addEventListener('click', function () {
    resetFormToAdd();
  });
</script>
</body>
</html>
