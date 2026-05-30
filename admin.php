<?php
session_start();
define('CONFIG_FILE', 'config.php');
include CONFIG_FILE;

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$errors = [];
$success = false;

if (isset($_POST['login'])) {
    $password = $_POST['password'] ?? '';
    if ($password === $config['admin_password']) {
        $_SESSION['loggedin'] = true;
    } else {
        $errors[] = 'Invalid admin password.';
    }
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_POST['save_config'])) {
    $new_password = trim($_POST['admin_password']);
    $new_whatsapp = trim($_POST['whatsapp_number']);
    $new_sort = $_POST['api_sort'];
    $categories_raw = $_POST['categories'];
    
    $categories_array = array_filter(array_map('trim', explode(',', $categories_raw)));
    
    if (empty($new_password)) {
        $errors[] = 'Password cannot be empty.';
    } else {
        $config['admin_password'] = $new_password;
        $config['whatsapp_number'] = $new_whatsapp;
        $config['api_sort'] = $new_sort;
        $config['categories'] = array_values($categories_array);
        $config['ad_header'] = $_POST['ad_header'];
        $config['ad_footer'] = $_POST['ad_footer'];
        
        $file_content = "<?php\nif (basename(\$_SERVER['SCRIPT_FILENAME']) === 'config.php') {\n    die('Direct access forbidden.');\n}\n\n\$config = " . var_export($config, true) . ";\n";
        if (file_put_contents(CONFIG_FILE, $file_content)) {
            $success = true;
        } else {
            $errors[] = 'Failed to write to config.php. Check file permissions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Tube Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen font-sans">
    
    <?php if (!isset($_SESSION['loggedin'])): ?>
    <!-- Login Form -->
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-neutral-900 border border-neutral-800 p-8 rounded-xl w-full max-w-md shadow-2xl">
            <h2 class="text-2xl font-bold text-center text-amber-500 mb-6 tracking-wide">TUBE PORTAL ADMIN</h2>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-900/30 border border-red-500 text-red-200 p-3 rounded mb-4 text-sm"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-6">
                    <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Admin Password</label>
                    <input type="password" name="password" required class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white focus:outline-none focus:border-amber-500 transition-colors">
                </div>
                <button type="submit" name="login" class="w-full bg-amber-500 hover:bg-amber-600 text-neutral-950 font-bold py-3 px-4 rounded tracking-wide transition-colors">Access Panel</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    
    <!-- Admin Dashboard Header -->
    <header class="bg-neutral-900 border-b border-neutral-800 px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-black text-amber-500 tracking-wider">PORTAL ENGINE <span class="text-xs text-neutral-400 font-normal">v1.0 (Flat-File)</span></h1>
        <a href="admin.php?action=logout" class="bg-neutral-800 hover:bg-neutral-700 px-4 py-2 rounded text-sm transition-colors">Logout</a>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">
        <?php if ($success): ?>
            <div class="bg-emerald-950/40 border border-emerald-500 text-emerald-200 p-4 rounded-lg mb-6">Configuration updated successfully!</div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="bg-red-900/30 border border-red-500 text-red-200 p-4 rounded-lg mb-6"><?= implode('<br>', $errors) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <!-- Platform Security & Essentials -->
            <div class="bg-neutral-900 border border-neutral-800 p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-bold text-white mb-4 pb-2 border-b border-neutral-800">System & Contact Configurations</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Admin Password</label>
                        <input type="text" name="admin_password" value="<?= htmlspecialchars($config['admin_password']) ?>" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Promo WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" value="<?= htmlspecialchars($config['whatsapp_number']) ?>" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <!-- API Configuration -->
            <div class="bg-neutral-900 border border-neutral-800 p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-bold text-white mb-4 pb-2 border-b border-neutral-800">API Stream Options</h3>
                <div class="mb-4">
                    <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Video Sorting Rule</label>
                    <select name="api_sort" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white focus:outline-none focus:border-amber-500">
                        <option value="mostviewed" <?= $config['api_sort'] === 'mostviewed' ? 'selected' : '' ?>>Most Viewed</option>
                        <option value="rating" <?= $config['api_sort'] === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                        <option value="newest" <?= $config['api_sort'] === 'newest' ? 'selected' : '' ?>>Newest Videos</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Navigation Niche Keywords (Comma Separated)</label>
                    <textarea name="categories" rows="3" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white font-mono text-sm focus:outline-none focus:border-amber-500"><?= htmlspecialchars(implode(', ', $config['categories'])) ?></textarea>
                </div>
            </div>

            <!-- Ad Networks Injector -->
            <div class="bg-neutral-900 border border-neutral-800 p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-bold text-white mb-4 pb-2 border-b border-neutral-800">Ad Automation Code Injection</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Header Scripts (Verification Metas, Banner Script Wrappers)</label>
                        <textarea name="ad_header" rows="4" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white font-mono text-xs focus:outline-none focus:border-amber-500"><?= htmlspecialchars($config['ad_header']) ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-neutral-400 mb-2 font-bold">Footer Scripts (Popunder Native Injection, Tracking Pixels)</label>
                        <textarea name="ad_footer" rows="4" class="w-full bg-neutral-950 border border-neutral-800 rounded p-3 text-white font-mono text-xs focus:outline-none focus:border-amber-500"><?= htmlspecialchars($config['ad_footer']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" name="save_config" class="w-full bg-amber-500 hover:bg-amber-600 text-neutral-950 font-black tracking-widest py-4 rounded-xl uppercase transition-colors shadow-lg">Commit Changes to Engine</button>
            </div>
        </form>
    </main>
    <?php endif; ?>
</body>
</html>
