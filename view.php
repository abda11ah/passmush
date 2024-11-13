<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$current_time = time();
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ? AND expires_at > ?");
$stmt->execute([$id, $current_time]);
$password = $stmt->fetch();

if (!$password) {
    $error = "This link has expired or doesn't exist.";
} elseif ($password['view_limit'] > 0 && $password['view_count'] >= $password['view_limit']) {
    $error = "This password has reached its maximum view limit.";
} else {
    // Increment view count
    $stmt = $pdo->prepare("UPDATE passwords SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$id]);
    $password['view_count']++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Shared Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
    function copyToClipboard() {
        const passwordText = document.getElementById('password-text').textContent;
        navigator.clipboard.writeText(passwordText).then(() => {
            const copyBtn = document.getElementById('copy-btn');
            copyBtn.textContent = 'Copied!';
            setTimeout(() => {
                copyBtn.textContent = 'Copy to Clipboard';
            }, 2000);
        });
    }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <?php if (isset($error)): ?>
                <div class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php else: ?>
                <h2 class="text-2xl font-bold text-center mb-4">Shared Password</h2>
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-sm text-gray-500 mb-2">Password:</p>
                    <p id="password-text" class="font-mono bg-gray-100 p-2 rounded select-all"><?php echo $password['password']; ?></p>
                    <button id="copy-btn" onclick="copyToClipboard()" 
                            class="mt-2 w-full bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600">
                        Copy to Clipboard
                    </button>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    <p>Expires: <?php echo date('Y-m-d H:i:s', $password['expires_at']); ?></p>
                    <?php if ($password['view_limit'] > 0): ?>
                        <p class="mt-1">Views remaining: <?php echo $password['view_limit'] - $password['view_count']; ?> of <?php echo $password['view_limit']; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-6">
                <a href="index.php" 
                   class="block w-full text-center bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600">
                    Share Another Password
                </a>
            </div>
        </div>
    </div>
</body>
</html>
