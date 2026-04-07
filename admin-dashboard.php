<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Muadh Al Zadjali</title>
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
    header .title {
      font-weight: 700;
      font-size: 18px;
    }
    header .user {
      font-size: 13px;
      margin-right: 8px;
    }
    header a {
      color: #163b73;
      text-decoration: none;
      font-size: 13px;
      margin-left: 10px;
    }
    main {
      max-width: 900px;
      margin: 30px auto;
      padding: 0 18px 30px;
    }
    h2 {
      font-size: 20px;
      margin-bottom: 12px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 14px;
    }
    .card {
      background: rgba(0,0,0,0.55);
      padding: 16px;
      border-radius: 10px;
    }
    .card h3 {
      font-size: 16px;
      margin-bottom: 4px;
    }
    .card p {
      font-size: 13px;
      margin-bottom: 6px;
    }
    .card a {
      display: inline-block;
      margin-top: 6px;
      padding: 7px 12px;
      border-radius: 999px;
      background: #ffd54f;
      color: #163b73;
      font-size: 13px;
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>
<body>
<header>
  <div class="title">Admin Dashboard</div>
  <div>
    <span class="user">
      Logged in as: <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?>
    </span>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<main>
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></h2>

  <div class="cards">
    <div class="card">
      <h3>Services Management</h3>
      <p>View and add services that appear on the public Services page.</p>
      <a href="admin-services.php">Go to Services Admin</a>
    </div>

    <div class="card">
      <h3>Contracts Management</h3>
      <p>Manage contract types, descriptions, images, and videos that appear on the public Contracts page.</p>
      <a href="admin-contracts.php">Go to Contracts Admin</a>
    </div>

    <div class="card">
      <h3>About Page Management</h3>
      <p>Edit About Us text sections, stats, and diagonal gallery images that appear on the public About page.</p>
      <a href="admin-about.php">Go to About Admin</a>
    </div>

    <div class="card">
      <h3>Insights Management</h3>
      <p>Manage Insights hero text, featured article, recent articles, and diagonal gallery images that appear on the public Insights page.</p>
      <a href="admin-insights.php">Go to Insights Admin</a>
    </div>

    <div class="card">
      <h3>Contact Page Management</h3>
      <p>Edit Contact hero text, form intro, note, contact details, and review messages submitted from the public Contact page.</p>
      <a href="admin-contact.php">Go to Contact Admin</a>
    </div>

    <div class="card">
      <h3>Industry Outlook Management</h3>
      <p>Manage Industry Outlook hero text, trends, forecasts, key indicators, and regional content shown on the Industry Outlook page.</p>
      <a href="admin-industry.php">Go to Industry Outlook Admin</a>
    </div>

    <div class="card">
      <h3>Ask Question Management</h3>
      <p>Manage Ask Question hero text, form intro, and FAQs that appear on the public Ask Question page.</p>
      <a href="admin-ask-question.php">Go to Ask Question Admin</a>
    </div>

    <div class="card">
      <h3>Location Management</h3>
      <p>Manage location details, contact info, and map settings that appear on the public Location page.</p>
      <a href="admin-location.php">Go to Location Admin</a>
    </div>
  </div>
</main>
</body>
</html>
