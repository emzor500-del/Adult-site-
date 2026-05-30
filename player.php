<?php
define('CONFIG_FILE', 'config.php');
include CONFIG_FILE;

$video_id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';
$title = isset($_GET['title']) ? htmlspecialchars(urldecode($_GET['title'])) : 'Premium Adult Stream';

if (empty($video_id)) {
    header('Location: index.php');
    exit;
}

// Generate the standard Pornhub embedded URL
$embed_url = "https://www.pornhub.com/embed/" . $video_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | Tube Portal Player</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?= $config['ad_header'] ?>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen flex flex-col font-sans">

    <!-- Header Frame -->
    <header class="bg-neutral-900 border-b border-neutral-800">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-black tracking-tighter text-white">ADULT<span class="bg-amber-500 text-neutral-950 px-2 py-0.5 rounded ml-1 font-bold tracking-normal">MATRIX</span></a>
            <a href="index.php" class="bg-neutral-800 hover:bg-neutral-700 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors">Return to Grid Matrix</a>
        </div>
    </header>

    <!-- Main Player Workspace Layout -->
    <main class="max-w-5xl w-full mx-auto px-4 py-8 flex-grow space-y-6">
        
        <!-- High-Tier Theater Mode Responsive Player Shell -->
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden shadow-2xl">
            <div class="relative w-full aspect-video bg-black">
                <iframe src="<?= $embed_url ?>" frameborder="0" width="100%" height="100%" scrolling="no" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
            </div>
            <div class="p-6">
                <h1 class="text-xl md:text-2xl font-black text-white leading-snug tracking-wide"><?= $title ?></h1>
                <div class="mt-4 pt-4 border-t border-neutral-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="text-xs text-neutral-400">
                        Stream Source Vector: <span class="text-amber-500 font-mono">PH_API_<?= $video_id ?></span>
                    </div>
                    <!-- Promo Contact Trigger Integration Area -->
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_number']) ?>?text=Hello,%20I%20am%20interested%20in%20your%20Premium%20Traffic%20Promotions." target="_blank" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-500 text-neutral-950 font-black text-xs uppercase tracking-widest px-6 py-3 rounded-xl transition-all shadow-md">
                        Advertise / Promote on WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Inline Static Advertising Space Block -->
        <div class="bg-neutral-900/40 border border-dashed border-neutral-800 rounded-2xl p-6 text-center text-xs text-neutral-500 uppercase tracking-widest">
            <!-- Inject your popunder or responsive banner ad elements inside this block -->
            Native Promotional Unit Space Holder
        </div>

    </main>

    <!-- Global Compliancy Footer Module -->
    <footer class="bg-neutral-900 border-t border-neutral-800 text-xs text-neutral-500 py-8 px-6 mt-20">
        <div class="max-w-5xl mx-auto text-center space-y-4">
            <p>&copy; <?= date('Y') ?> ADULTMATRIX Hub Engine. Content indexed dynamically under statutory global server liability rules.</p>
        </div>
    </footer>

    <?= $config['ad_footer'] ?>
</body>
</html>
