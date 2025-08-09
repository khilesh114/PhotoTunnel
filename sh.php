<?php
$uploadDir = __DIR__ . '/';
$allowedExt = ['sh'];

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {

        $fileName = basename($_FILES['file']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExt)) {
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
            $targetFile = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $msg = "✅ File uploaded successfully: " . htmlspecialchars($safeName);
            } else {
                $msg = "❌ Error uploading the file.";
            }
        } else {
            $msg = "❌ Invalid file type. Only .sh files are allowed.";
        }
    } else {
        $msg = "❌ No file selected or upload error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>.sh File Upload</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body { font-family: Arial, sans-serif; background: #0d1636; color: #fff; margin:0; padding:0; display:flex; justify-content:center; align-items:center; height:100vh;}
    .container { background:#fff; color:#222; padding:25px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.3); width:320px; text-align:center; }
    h2 { margin-top:0; }
    input[type=file] { margin:15px 0; padding:5px; }
    button { background:#2083fd; color:#fff; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:1em;}
    button:hover { background:#0a5ad7; }
    .msg { margin-top:15px; font-weight:bold; color: green; }
</style>
</head>
<body>

<div class="container">
    <h2>Upload .sh File</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".sh" required><br>
        <button type="submit">Upload</button>
    </form>
    <?php if (!empty($msg)): ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
