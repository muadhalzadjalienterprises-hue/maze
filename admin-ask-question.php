<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

require __DIR__ . '/db.php';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$message = '';

/**
 * Handle delete single question
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_question_id'])) {
    $delId = (int)$_POST['delete_question_id'];
    if ($delId > 0) {
        // Delete any FAQs that were sourced from this question (optional but safer)
        $delFaq = $pdo->prepare("DELETE FROM ask_faqs WHERE source_question_id = :sid");
        $delFaq->execute([':sid' => $delId]);

        // Delete the question itself
        $delQ = $pdo->prepare("DELETE FROM ask_questions WHERE id = :id");
        $delQ->execute([':id' => $delId]);

        $message = 'Selected question deleted.';
    }
}

/**
 * Handle delete all questions
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_all_questions'])) {
    // Optionally also clear links from FAQs
    $pdo->exec("UPDATE ask_faqs SET source_question_id = NULL");
    $pdo->exec("DELETE FROM ask_questions");
    $message = 'All questions deleted.';
}

// Ensure section rows
$slugs = ['hero','form_intro'];
foreach ($slugs as $slug) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ask_sections WHERE slug = :s");
    $stmt->execute([':s' => $slug]);
    if (!$stmt->fetchColumn()) {
        $ins = $pdo->prepare("INSERT INTO ask_sections (slug, title, content, is_active) VALUES (:s,'','',1)");
        $ins->execute([':s' => $slug]);
    }
}

// Helper to load section
function getSection($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT * FROM ask_sections WHERE slug = :s AND is_active = 1");
    $stmt->execute([':s' => $slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['title' => '', 'content' => ''];
}

// Handle main POST (content + FAQs + promote)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['delete_question_id']) && empty($_POST['delete_all_questions'])) {
    // Update hero/form intro
    $hero_title   = trim($_POST['hero_title'] ?? '');
    $hero_content = trim($_POST['hero_content'] ?? '');
    $form_title   = trim($_POST['form_title'] ?? '');
    $form_content = trim($_POST['form_content'] ?? '');

    $upd = $pdo->prepare("UPDATE ask_sections SET title = :t, content = :c WHERE slug = :s");
    $upd->execute([':t'=>$hero_title, ':c'=>$hero_content, ':s'=>'hero']);
    $upd->execute([':t'=>$form_title, ':c'=>$form_content, ':s'=>'form_intro']);

    // Update existing FAQs
    if (!empty($_POST['faq_id']) && is_array($_POST['faq_id'])) {
        $updFaq = $pdo->prepare("
            UPDATE ask_faqs
            SET question_text = :q,
                answer_text   = :a,
                is_active     = :ac,
                display_order = :o
            WHERE id = :id
        ");
        foreach ($_POST['faq_id'] as $idx => $id) {
            $id = (int)$id;
            $q  = trim($_POST['faq_question'][$idx] ?? '');
            $a  = trim($_POST['faq_answer'][$idx] ?? '');
            $o  = (int)($_POST['faq_order'][$idx] ?? 0);
            $ac = isset($_POST['faq_active'][$idx]) ? 1 : 0;
            $updFaq->execute([
                ':q'=>$q, ':a'=>$a, ':ac'=>$ac, ':o'=>$o, ':id'=>$id
            ]);
        }
    }

    // Add new FAQ (manual)
    $new_q = trim($_POST['new_faq_question'] ?? '');
    $new_a = trim($_POST['new_faq_answer'] ?? '');
    if ($new_q !== '' && $new_a !== '') {
        $insFaq = $pdo->prepare("
            INSERT INTO ask_faqs (question_text, answer_text, is_active, display_order)
            VALUES (:q, :a, 1, 0)
        ");
        $insFaq->execute([':q'=>$new_q, ':a'=>$new_a]);
    }

    // Promote a question to FAQ
    if (!empty($_POST['promote_question_id'])) {
        $qid   = (int)$_POST['promote_question_id'];
        $faq_q = trim($_POST['promote_faq_question'] ?? '');
        $faq_a = trim($_POST['promote_faq_answer'] ?? '');
        if ($faq_q !== '' && $faq_a !== '') {
            $insFaq = $pdo->prepare("
                INSERT INTO ask_faqs (question_text, answer_text, source_question_id, is_active, display_order)
                VALUES (:q, :a, :sid, 1, 0)
            ");
            $insFaq->execute([
                ':q'=>$faq_q,
                ':a'=>$faq_a,
                ':sid'=>$qid
            ]);
            $updQ = $pdo->prepare("UPDATE ask_questions SET is_published_faq = 1 WHERE id = :id");
            $updQ->execute([':id'=>$qid]);
        }
    }

    if ($message === '') {
        $message = 'Ask Question page updated.';
    }
}

// Load sections
$hero      = getSection($pdo, 'hero');
$formIntro = getSection($pdo, 'form_intro');

// Load recent questions
$qStmt = $pdo->query("
    SELECT * FROM ask_questions
    ORDER BY created_at DESC
    LIMIT 30
");
$questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

// Load FAQs
$fStmt = $pdo->query("
    SELECT * FROM ask_faqs
    ORDER BY display_order ASC, created_at DESC
");
$faqs = $fStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Ask Question | Muadh Al Zadjali</title>
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
      max-width: 1150px;
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
    textarea,
    select {
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
    .btn-danger {
      background: #e53935;
      color: #fff;
    }
    .btn-small {
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 999px;
    }
    .message {
      font-size: 13px;
      color: #c8e6c9;
      margin-bottom: 6px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 12px;
      background: rgba(0,0,0,0.35);
    }
    th, td {
      border: 1px solid rgba(255,255,255,0.2);
      padding: 6px 8px;
      text-align: left;
      vertical-align: top;
    }
    th {
      background: rgba(0,0,0,0.6);
    }
    .small-note {
      font-size: 11px;
      color: #ddd;
      margin-bottom: 6px;
    }
    .inline {
      display: inline-block;
      margin-right: 6px;
    }
    .actions-cell {
      white-space: nowrap;
    }
  </style>
</head>
<body>
<header>
  <div>Admin – Ask a Question</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h1>Manage Ask a Question Page</h1>

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
      <h2>Form Intro</h2>
      <label for="form_title">Form title</label>
      <input type="text" id="form_title" name="form_title" value="<?php echo esc($formIntro['title']); ?>">

      <label for="form_content">Form intro text</label>
      <textarea id="form_content" name="form_content"><?php echo esc($formIntro['content']); ?></textarea>
    </div>

    <div class="section-card">
      <h2>Existing FAQs</h2>
      <p class="small-note">You can edit the question, answer, active status, and display order.</p>
      <?php if ($faqs): ?>
        <?php foreach ($faqs as $idx => $f): ?>
          <div style="border:1px solid rgba(255,255,255,0.3); padding:8px; border-radius:6px; margin-bottom:8px;">
            <input type="hidden" name="faq_id[]" value="<?php echo (int)$f['id']; ?>">

            <label>Question</label>
            <textarea name="faq_question[]"><?php echo esc($f['question_text']); ?></textarea>

            <label>Answer</label>
            <textarea name="faq_answer[]"><?php echo esc($f['answer_text']); ?></textarea>

            <div>
              <span class="inline">
                <label>Display order</label>
                <input type="text" name="faq_order[]" style="width:70px;"
                       value="<?php echo (int)$f['display_order']; ?>">
              </span>
              <span class="inline">
                <label>
                  <input type="checkbox" name="faq_active[<?php echo $idx; ?>]" <?php echo $f['is_active'] ? 'checked' : ''; ?>>
                  Active
                </label>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="font-size:13px;">No FAQs yet.</p>
      <?php endif; ?>
    </div>

    <div class="section-card">
      <h2>Add New FAQ (Manual)</h2>
      <label for="new_faq_question">Question</label>
      <textarea id="new_faq_question" name="new_faq_question"></textarea>

      <label for="new_faq_answer">Answer</label>
      <textarea id="new_faq_answer" name="new_faq_answer"></textarea>
    </div>

    <div class="section-card">
      <h2>Promote a Question to FAQ</h2>
      <p class="small-note">Select a question and write the FAQ version of the question and answer.</p>
      <label for="promote_question_id">Choose question</label>
      <select id="promote_question_id" name="promote_question_id">
        <option value="">-- Select question --</option>
        <?php foreach ($questions as $q): ?>
          <option value="<?php echo (int)$q['id']; ?>">
            [<?php echo esc($q['created_at']); ?>] <?php echo esc(mb_strimwidth($q['question'], 0, 60, '...')); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="promote_faq_question">FAQ Question text</label>
      <textarea id="promote_faq_question" name="promote_faq_question"></textarea>

      <label for="promote_faq_answer">FAQ Answer text</label>
      <textarea id="promote_faq_answer" name="promote_faq_answer"></textarea>
    </div>

    <button type="submit">Save Ask Question Page</button>
  </form>

  <div class="section-card" style="margin-top:18px;">
    <h2>Recent Submitted Questions</h2>
    <?php if ($questions): ?>
      <p class="small-note">
        Use the delete button on a row to remove a single question.
        To wipe all recent questions, use the "Delete all questions" button.
      </p>
      <form method="post" onsubmit="return confirmDeleteAll(this);">
        <button type="submit" name="delete_all_questions" value="1" class="btn-danger btn-small">
          Delete all questions
        </button>
      </form>

      <table>
        <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Question</th>
          <th>In FAQ?</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($questions as $q): ?>
          <tr>
            <td><?php echo esc($q['created_at']); ?></td>
            <td><?php echo esc($q['name']); ?></td>
            <td><?php echo nl2br(esc($q['question'])); ?></td>
            <td><?php echo !empty($q['is_published_faq']) ? 'Yes' : 'No'; ?></td>
            <td class="actions-cell">
              <form method="post" style="display:inline;" onsubmit="return confirmDeleteOne();">
                <input type="hidden" name="delete_question_id" value="<?php echo (int)$q['id']; ?>">
                <button type="submit" class="btn-danger btn-small">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="font-size:13px;">No questions submitted yet.</p>
    <?php endif; ?>
  </div>
</main>

<script>
  function confirmDeleteOne() {
    return confirm('Delete this question? This cannot be undone.');
  }
  function confirmDeleteAll(form) {
    if (!confirm('Delete ALL questions? This cannot be undone.')) {
      return false;
    }
    return true;
  }
</script>
</body>
</html>
