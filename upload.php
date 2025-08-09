<?php
$targetDir = __DIR__ . "/uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
$allowed = ['png','jpg','jpeg','gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $fileName = basename($_FILES['file']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            // Overwrite protection: filename_1.ext
            $targetFile = $targetDir . $fileName;
            $fileBase = pathinfo($fileName, PATHINFO_FILENAME);
            $counter = 1;
            while (file_exists($targetFile)) {
                $targetFile = $targetDir . $fileBase . '_' . $counter . '.' . $ext;
                $counter++;
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                http_response_code(201);
                echo "File uploaded successfully: " . htmlspecialchars(basename($targetFile));
            } else {
                http_response_code(500);
                echo "Error uploading the file.";
            }
        } else {
            http_response_code(400);
            echo "Only PNG, JPG, JPEG, and GIF files are allowed!";
        }
    } else {
        http_response_code(400);
        echo "No file sent!";
    }
} else {
    http_response_code(405);
    echo "Only POST requests allowed!";
}
?>
