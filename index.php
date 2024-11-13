<?php
require_once 'lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('page_title'); ?></title>
    <link href="tailwind.min.css" rel="stylesheet">
    <script>
    function generatePassword() {
        const length = Math.floor(Math.random() * (16 - 8 + 1)) + 8; // Random length between 8 and 16
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789?!%@_-$*';
        let password = '';
        
        // Ensure at least one of each required type
        password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]; // Uppercase
        password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]; // Lowercase
        password += '0123456789'[Math.floor(Math.random() * 10)]; // Number
        password += '?!%@_-$*'[Math.floor(Math.random() * 8)]; // Special char
        
        // Fill the rest randomly
        for (let i = password.length; i < length; i++) {
            password += charset[Math.floor(Math.random() * charset.length)];
        }
        
        // Shuffle the password
        password = password.split('').sort(() => Math.random() - 0.5).join('');
        
        document.getElementById('password-input').value = password;
        document.getElementById('password-input').type = 'text';
        setTimeout(() => {
            document.getElementById('password-input').type = 'text';
        }, 2000);
    }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-right mb-4">
            <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'font-bold' : ''; ?>">Français</a> |
            <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'font-bold' : ''; ?>">English</a>
        </div>
        
        <h1 class="text-3xl font-bold text-center mb-8"><?php echo __('page_title'); ?></h1>
        
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <form action="create.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700"><?php echo __('password_to_share'); ?></label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="password" id="password-input" required 
                               class="flex-1 rounded-l-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" onclick="generatePassword()"
                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100">
                            <?php echo __('generate'); ?>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700"><?php echo __('expires_after'); ?></label>
                    <select name="expires" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php foreach (__('time_options') as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"><?php echo __('view_limit'); ?></label>
                    <select name="view_limit" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php foreach (__('view_options') as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <?php echo __('generate_link'); ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>