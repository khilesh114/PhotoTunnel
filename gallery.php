<?php
session_start();

// ==== CONFIG ====
define('AUTH_USER', 'codex');
define('AUTH_PASS', 'Codex@2003');
$log_file = __DIR__ . "/logs/access.log";
$upload_dir = __DIR__ . "/uploads/";

// ==== Brute Force Protection ====
if (!isset($_SESSION['failed'])) $_SESSION['failed'] = 0;
if ($_SESSION['failed'] > 5) {
    header("HTTP/1.1 429 Too Many Requests");
    die('Too many failed attempts. Try again later.');
}

// ==== LOG FUNCTION ====
function write_log($username, $ip, $action, $details = '') {
    global $log_file;
    $time = date('Y-m-d H:i:s');
    $line = "[$time] USERNAME: $username | IP: $ip | ACTION: $action";
    if ($details) $line .= " | DETAILS: $details";
    file_put_contents($log_file, $line.PHP_EOL, FILE_APPEND | LOCK_EX);
}

$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

// ==== LOGIN LOGIC ====
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            $_SESSION['failed']++;
            die('Invalid request.');
        }
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';
        if (hash_equals(AUTH_USER, $user) && hash_equals(AUTH_PASS, $pass)) {
            $_SESSION['auth'] = true;
            $_SESSION['failed'] = 0;
            unset($_SESSION['csrf']);
            write_log($user, $ip_address, 'LOGIN_SUCCESS');
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['failed']++;
            write_log($user ?: 'UNKNOWN', $ip_address, 'LOGIN_FAIL');
            $error = 'Invalid username or password.';
            sleep(2);
        }
    }
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
    ?>
    <!DOCTYPE html><html lang="en"><head>
    <meta charset="UTF-8"><title>Login</title>
    <style>
    body {font-family:sans-serif;background:#0d1636;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;}
    .box {background:#fff;color:#111;padding:20px 30px;border-radius:8px;width:300px;}
    input {width:100%;margin:6px 0;padding:8px;border:1px solid #ccc;border-radius:4px;}
    button {background:#2083fd;color:#fff;padding:8px;border:none;border-radius:4px;width:100%;}
    .error{color:red;margin-bottom:10px;}
    </style></head><body>
    <div class="box">
        <h2>Login</h2>
        <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="post" autocomplete="off">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
            <button type="submit">Login</button>
        </form>
        <?php if($_SESSION['failed']>0) echo "<small>Failed attempts: ".$_SESSION['failed']."</small>"; ?>
    </div>
    </body></html>
    <?php exit;
}

// ==== LOGOUT ====
if (isset($_GET['logout'])) {
    write_log(AUTH_USER, $ip_address, 'LOGOUT');
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ==== DOWNLOAD IMAGE ====
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $file_path = $upload_dir . $file;
    if (file_exists($file_path)) {
        write_log(AUTH_USER, $ip_address, 'DOWNLOAD_IMAGE', $file);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($file_path));
        flush();
        readfile($file_path);
        exit;
    } else {
        echo "File not found!";
        exit;
    }
}

// ==== DOWNLOAD LOG FILE ====
if (isset($_GET['view_logs'])) {
    if (file_exists($log_file)) {
        write_log(AUTH_USER, $ip_address, 'DOWNLOAD_LOG');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="access.log"');
        readfile($log_file);
        exit;
    } else {
        echo "No logs available.";
        exit;
    }
}

// ==== SECURITY HEADERS ====
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin");
header("X-XSS-Protection: 1; mode=block");

// ==== GALLERY LOG ====
write_log(AUTH_USER, $ip_address, 'VIEW_GALLERY');

// ==== GALLERY FILES ====
$files = array_merge(
    glob($upload_dir . "*.[pP][nN][gG]"),
    glob($upload_dir . "*.[jJ][pP][gG]"),
    glob($upload_dir . "*.[jJ][pP][eE][gG]"),
    glob($upload_dir . "*.[gG][iI][fF]")
);
$gallery = [];
foreach ($files as $f) {
    $gallery[] = [
        'filename' => basename($f),
        'url' => 'uploads/' . basename($f),
        'mtime' => filemtime($f),
        'size' => filesize($f)
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Secure Image Gallery 🔒</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family:Segoe UI, Arial;background:#f7f8fa;margin:0;}
.header {background:#0d1636;color:#fff;padding:10px;text-align:center;font-size:1.5em;position:relative;}
.btn {background:#2083fd;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;}
.controls {display:flex;justify-content:center;gap:12px;margin:15px;}
.gallery {display:flex;flex-wrap:wrap;gap:20px;justify-content:center;}
.img-container {background:#fff;padding:10px;border-radius:8px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.img-container img {max-width:180px;max-height:150px;object-fit:cover;border-radius:6px;}
.file-meta {color:#555;font-size:0.85em;}
a.download-link {display:inline-block;margin-top:5px;background:#2083fd;color:white;padding:3px 10px;border-radius:3px;text-decoration:none;}
</style>
<script>
function filterGallery(){
  let val=document.getElementById('searchInput').value.trim().toLowerCase();
  let order=document.getElementById('sortSelect').value;
  let imgs=[...document.querySelectorAll('.img-container')];
  imgs.forEach(d=>{d.style.display=d.dataset.filename.includes(val)?'':'none';});
  imgs.sort((a,b)=>{
    if(order==='newest') return b.dataset.ftime-a.dataset.ftime;
    if(order==='oldest') return a.dataset.ftime-b.dataset.ftime;
    if(order==='name') return a.dataset.filename.localeCompare(b.dataset.filename);
  });
  let g=document.getElementById('gallery'); imgs.forEach(d=>g.appendChild(d));
}
</script>
</head>
<body>

<div class="header">
  Secure Gallery
  <form method="get" style="position:absolute;top:10px;right:10px;display:flex;gap:5px;">
    <button class="btn" name="view_logs" value="1">Download Logs</button>
    <button class="btn" name="logout" value="1">Logout</button>
  </form>
</div>

<div class="controls">
  <input type="text" id="searchInput" placeholder="Search..." oninput="filterGallery()">
  <select id="sortSelect" onchange="filterGallery()">
    <option value="newest">Newest</option>
    <option value="oldest">Oldest</option>
    <option value="name">A-Z</option>
  </select>
</div>

<div class="gallery" id="gallery">
<?php
usort($gallery,fn($a,$b)=>$b['mtime'] - $a['mtime']);
foreach($gallery as $item): ?>
<div class="img-container" data-filename="<?php echo strtolower($item['filename']); ?>" data-ftime="<?php echo $item['mtime']; ?>">
  <img src="<?php echo htmlspecialchars($item['url']); ?>" alt="">
  <div class="file-info"><?php echo htmlspecialchars($item['filename']); ?></div>
  <div class="file-meta"><?php echo date("d M Y, H:i",$item['mtime']); ?> • <?php echo round($item['size']/1024); ?> KB</div>
  <a class="download-link" href="?download=<?php echo urlencode($item['filename']); ?>">Download</a>
</div>
<?php endforeach; ?>
</div>

</body>
</html>
