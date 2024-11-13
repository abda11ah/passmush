<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Password Share</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Secure Password Share</h1>
        
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <form action="create.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password to share</label>
                    <input type="password" name="password" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expires after</label>
                    <select name="expires" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="6">6 hours</option>
                        <option value="24">24 hours</option>
                        <option value="72">3 days</option>
                        <option value="168">1 week</option>
                        <option value="720">1 month</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">View/Copy limit</label>
                    <select name="view_limit" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">1 time</option>
                        <option value="3">3 times</option>
                        <option value="5">5 times</option>
                        <option value="10">10 times</option>
                        <option value="0">Unlimited</option>
                    </select>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Generate Secure Link
                </button>
            </form>
        </div>
    </div>
</body>
</html>
