<?php
// Fehler anzeigen – zum Debuggen
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Upload-Verarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['zip_file'])) {
        echo "❌ Keine Datei empfangen.";
        exit;
    }

    $file = $_FILES['zip_file'];

    echo "<h2>📦 Upload-Ergebnis</h2>";
    echo "<pre>";
    print_r([
        'original_name' => $file['name'],
        'type_from_browser' => $file['type'],
        'tmp_name' => $file['tmp_name'],
        'error' => $file['error'],
        'size_bytes' => $file['size'],
        'guessed_mime' => mime_content_type($file['tmp_name']),
    ]);
    echo "</pre>";

    // Optional: Datei speichern
    $target = __DIR__ . '/uploads/' . basename($file['name']);
    if (!is_dir(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads');
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        echo "<p style='color:green;'>✅ Datei gespeichert unter: uploads/" . htmlspecialchars($file['name']) . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Fehler beim Speichern.</p>";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>ZIP Upload Test</title>
</head>
<body>
    <h1>🔧 ZIP Upload Test</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="zip_file">Wähle eine ZIP-Datei:</label><br>
        <input type="file" name="zip_file" id="zip_file" accept=".zip" required><br><br>
        <button type="submit">Hochladen</button>
    </form>
</body>
</html>

