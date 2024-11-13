<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Shared Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-center text-green-600 mb-4">Password Shared Successfully!</h2>
            
            <div class="bg-gray-50 p-4 rounded-md">
                <p class="text-sm text-gray-500 mb-2">Share this secure link:</p>
                <p class="font-mono bg-gray-100 p-2 rounded break-all"><?php echo htmlspecialchars($share_url); ?></p>
            </div>
            
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
