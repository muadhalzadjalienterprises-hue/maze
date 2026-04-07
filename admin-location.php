<?php
// admin-location.php – Admin panel for location settings
require __DIR__ . '/db.php';

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$stmt = $pdo->query("SELECT * FROM location_settings ORDER BY id LIMIT 1");
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $office_address       = $_POST['office_address']       ?? '';
    $office_city          = $_POST['office_city']          ?? '';
    $office_postal        = $_POST['office_postal']        ?? '';
    $office_country       = $_POST['office_country']       ?? '';
    $phone                = $_POST['phone']                ?? '';
    $email                = $_POST['email']                ?? '';
    $business_hours       = $_POST['business_hours']       ?? '';
    $map_query            = $_POST['map_query']            ?? '';
    $service_areas        = $_POST['service_areas']        ?? '';
    $directions_text      = $_POST['directions_text']      ?? '';
    $need_directions_text = $_POST['need_directions_text'] ?? '';

    if ($current) {
        $stmt = $pdo->prepare("
            UPDATE location_settings SET
              office_address = :office_address,
              office_city = :office_city,
              office_postal = :office_postal,
              office_country = :office_country,
              phone = :phone,
              email = :email,
              business_hours = :business_hours,
              map_query = :map_query,
              service_areas = :service_areas,
              directions_text = :directions_text,
              need_directions_text = :need_directions_text
            WHERE id = :id
        ");
        $stmt->execute([
            ':office_address'       => $office_address,
            ':office_city'          => $office_city,
            ':office_postal'        => $office_postal,
            ':office_country'       => $office_country,
            ':phone'                => $phone,
            ':email'                => $email,
            ':business_hours'       => $business_hours,
            ':map_query'            => $map_query,
            ':service_areas'        => $service_areas,
            ':directions_text'      => $directions_text,
            ':need_directions_text' => $need_directions_text,
            ':id'                   => $current['id'],
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO location_settings (
              office_address, office_city, office_postal, office_country,
              phone, email, business_hours, map_query,
              service_areas, directions_text, need_directions_text
            ) VALUES (
              :office_address, :office_city, :office_postal, :office_country,
              :phone, :email, :business_hours, :map_query,
              :service_areas, :directions_text, :need_directions_text
            )
        ");
        $stmt->execute([
            ':office_address'       => $office_address,
            ':office_city'          => $office_city,
            ':office_postal'        => $office_postal,
            ':office_country'       => $office_country,
            ':phone'                => $phone,
            ':email'                => $email,
            ':business_hours'       => $business_hours,
            ':map_query'            => $map_query,
            ':service_areas'        => $service_areas,
            ':directions_text'      => $directions_text,
            ':need_directions_text' => $need_directions_text,
        ]);
    }

    header('Location: admin-location.php?saved=1');
    exit;
}

if (!$current) {
    $current = [
        'office_address'       => '',
        'office_city'          => '',
        'office_postal'        => '',
        'office_country'       => '',
        'phone'                => '',
        'email'                => '',
        'business_hours'       => '',
        'map_query'            => '',
        'service_areas'        => '',
        'directions_text'      => '',
        'need_directions_text' => '',
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin – Location Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{
      font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;
      background:#0b3c5d;color:#f5f5f5;padding:20px;
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

    .admin-wrap{
      max-width:900px;margin:0 auto;
      background:rgba(0,0,0,.35);
      border-radius:10px;
      padding:18px 18px 22px;
      box-shadow:0 6px 14px rgba(0,0,0,.45);
      border:1px solid rgba(255,255,255,.12);
    }
    h1{font-size:22px;margin-bottom:10px;}
    .note{font-size:13px;color:#cfd8e3;margin-bottom:16px;}
    label{display:block;font-size:13px;margin-top:12px;margin-bottom:4px;}
    input[type="text"],
    input[type="email"],
    textarea{
      width:100%;padding:7px 8px;font-size:14px;
      border-radius:6px;border:1px solid #ccc;
      outline:none;
    }
    textarea{min-height:70px;resize:vertical;}
    .help{
      font-size:11px;color:#cfd8e3;margin-top:2px;
    }
    .row{
      display:grid;grid-template-columns:1fr 1fr;gap:10px;
    }
    .btn-row{
      margin-top:18px;
      display:flex;gap:10px;align-items:center;
    }
    button{
      border:none;border-radius:999px;
      padding:8px 16px;font-size:14px;
      cursor:pointer;
    }
    .btn-primary{
      background:#ffd54f;color:#0b3c5d;
    }
    .status{
      font-size:13px;color:#a5d6a7;
    }
    a.front-link{
      color:#ffd54f;font-size:13px;text-decoration:none;
    }
    a.front-link:hover{text-decoration:underline;}
  </style>
</head>
<body>

<header>
  <div>Admin – Location</div>
  <div>
    <a href="admin-dashboard.php">Dashboard</a>
    <a href="admin-logout.php">Logout</a>
  </div>
</header>

<div class="admin-wrap">
  <h1>Location Settings</h1>
  <div class="note">
    Update address, contact details, service areas, and text used on the public Location page.
  </div>
  <?php if (!empty($_GET['saved'])): ?>
    <div class="status">Changes saved successfully.</div>
  <?php endif; ?>

  <form method="post" action="admin-location.php">
    <label for="office_address">Office Address (multi‑line)</label>
    <textarea id="office_address" name="office_address"><?php echo esc($current['office_address']); ?></textarea>

    <div class="row">
      <div>
        <label for="office_city">City</label>
        <input type="text" id="office_city" name="office_city" value="<?php echo esc($current['office_city']); ?>">
      </div>
      <div>
        <label for="office_postal">Postal / P.O. Box</label>
        <input type="text" id="office_postal" name="office_postal" value="<?php echo esc($current['office_postal']); ?>">
      </div>
    </div>

    <label for="office_country">Country</label>
    <input type="text" id="office_country" name="office_country" value="<?php echo esc($current['office_country']); ?>">

    <div class="row">
      <div>
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?php echo esc($current['phone']); ?>">
      </div>
      <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo esc($current['email']); ?>">
      </div>
    </div>

    <label for="business_hours">Business Hours (one line per row)</label>
    <textarea id="business_hours" name="business_hours"><?php echo esc($current['business_hours']); ?></textarea>

    <label for="map_query">Google Maps search query</label>
    <input type="text" id="map_query" name="map_query" value="<?php echo esc($current['map_query']); ?>">
    <div class="help">Used in: https://www.google.com/maps/search/?api=1&amp;query=&lt;this value&gt;</div>

    <label for="service_areas">Service Areas (one bullet per line)</label>
    <textarea id="service_areas" name="service_areas"><?php echo esc($current['service_areas']); ?></textarea>

    <label for="directions_text">Directions text (under map)</label>
    <textarea id="directions_text" name="directions_text"><?php echo esc($current['directions_text']); ?></textarea>

    <label for="need_directions_text">“Need Directions?” intro text</label>
    <textarea id="need_directions_text" name="need_directions_text"><?php echo esc($current['need_directions_text']); ?></textarea>

    <div class="btn-row">
      <button type="submit" class="btn-primary">Save Changes</button>
      <a class="front-link" href="location.php" target="_blank">View public Location page ↗</a>
    </div>
  </form>
</div>
</body>
</html>
