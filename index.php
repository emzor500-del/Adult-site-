<?php
define('CONFIG_FILE', 'config.php');
include CONFIG_FILE;

// Routing and Query Building
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$search = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$category = isset($_GET['cat']) ? urlencode($_GET['cat']) : '';

$api_url = "https://www.pornhub.com/webmasters/search?thumbsize=medium_hd";

if (!empty($search)) {
    $api_url .= "&search=" . $search;
} elseif (!empty($category)) {
    $api_url .= "&category=" . $category;
} else {
    // If homepage, alternate via default config sort rule
    $api_url .= "&ordering=" . $config['api_sort'];
}
$api_url .= "&page=" . $page;

// Server-side cache/fetch architecture
$response = file_get_contents($api_url);
$data = json_decode($response, true);
$videos = $data['videos'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Streaming Matrix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .cookie-gate { position: fixed; inset: 0; background: rgba(10,10,10,0.98); z-index: 9999; display: flex; align-items: center; justify-content: center; }
    </style>
    <?= $config['ad_header'] ?>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen flex flex-col font-sans">

    <!-- Age Verification Interstitial Modal Gate -->
    <div id="ageGate" class="cookie-gate hidden px-4">
        <div class="bg-neutral-900 border border-neutral-800 max-w-lg w-full p-8 rounded-2xl text-center shadow-2xl">
            <h2 class="text-3xl font-black text-amber-500 tracking-wider mb-4">RESTRICTED PORTAL</h2>
            <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                This platform contains sexually explicit adult material. You must be at least 18 years of age or the age of majority in your jurisdiction to access this content.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="acceptAge()" class="bg-amber-500 hover:bg-amber-600 text-neutral-950 font-bold px-8 py-3 rounded-xl transition-all tracking-wide uppercase text-sm">I am 18 or older</button>
                <button onclick="window.location.href='https://google.com'" class="bg-neutral-800 hover:bg-neutral-700 text-neutral-400 px-8 py-3 rounded-xl transition-all text-sm">Leave Platform</button>
            </div>
        </div>
    </div>

    <!-- Layout Container (hidden until verified) -->
    <div id="appContainer" class="opacity-0 transition-opacity duration-300 flex-grow flex flex-col">
        
        <!-- Premium Navigation Matrix Header -->
        <header class="bg-neutral-900 border-b border-neutral-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="index.php" class="text-2xl font-black tracking-tighter text-white">ADULT<span class="bg-amber-500 text-neutral-950 px-2 py-0.5 rounded ml-1 font-bold tracking-normal">MATRIX</span></a>
                
                <!-- Advanced Query Pipeline Form -->
                <form method="GET" action="index.php" class="w-full md:w-96 flex">
                    <input type="text" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" placeholder="Search stream matrix..." class="w-full bg-neutral-950 border border-neutral-800 rounded-l-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-neutral-950 px-5 font-bold rounded-r-xl text-sm transition-colors">Search</button>
                </form>
            </div>
            
            <!-- Dynamic Niche Navbar -->
            <div class="bg-neutral-900/50 border-t border-neutral-800/60 overflow-x-auto whitespace-nowrap scrollbar-none px-4 py-2.5 text-center">
                <?php foreach ($config['categories'] as $cat): ?>
                    <a href="index.php?cat=<?= urlencode($cat) ?>" class="inline-block mx-2 text-xs uppercase tracking-wider font-semibold text-neutral-400 hover:text-amber-500 transition-colors"><?= htmlspecialchars($cat) ?></a>
                <?php endforeach; ?>
            </div>
        </header>

        <!-- Main Workspace Frame Layout -->
        <main class="max-w-7xl w-full mx-auto px-4 py-8 flex-grow">
            <h2 class="text-lg font-bold text-neutral-400 uppercase tracking-widest mb-6">
                <?= !empty($search) ? 'Search Results For: ' . htmlspecialchars(urldecode($search)) : (!empty($category) ? htmlspecialchars(urldecode($category)) . ' Matrix' : 'Featured Content Streams') ?>
            </h2>

            <?php if (empty($videos)): ?>
                <div class="text-center py-20 text-neutral-500">No stream channels responded. Try another keyword combination.</div>
            <?php else: ?>
                <!-- Video Stream Responsive Grid Array -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($videos as $video): ?>
                        <div class="bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden group hover:border-neutral-700 transition-all shadow-md">
                            <a href="player.php?id=<?= $video['video_id'] ?>&title=<?= urlencode($video['title']) ?>" class="block relative aspect-video bg-neutral-950">
                                <img src="<?= $video['default_thumb'] ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                                <span class="absolute bottom-2 right-2 bg-neutral-950/80 text-[10px] px-2 py-0.5 rounded font-mono text-neutral-200"><?= $video['duration'] ?></span>
                            </a>
                            <div class="p-4">
                                <h3 class="text-sm font-bold line-clamp-2 mb-2 text-neutral-200 group-hover:text-amber-400 transition-colors">
                                    <a href="player.php?id=<?= $video['video_id'] ?>&title=<?= urlencode($video['title']) ?>"><?= htmlspecialchars($video['title']) ?></a>
                                </h3>
                                <div class="flex items-center justify-between text-[11px] text-neutral-500 font-medium">
                                    <span><?= htmlspecialchars($video['views'] ?? '0') ?> views</span>
                                    <span class="text-amber-600/80">★ <?= htmlspecialchars($video['ratings'] ?? '100') ?>%</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Structural Flat-File Pagination Architecture -->
                <div class="mt-12 flex justify-center items-center gap-4">
                    <?php if ($page > 1): ?>
                        <a href="index.php?p=<?= $page - 1 ?><?= !empty($search) ? '&q='.$search : '' ?><?= !empty($category) ? '&cat='.$category : '' ?>" class="bg-neutral-900 border border-neutral-800 hover:border-neutral-700 px-5 py-2.5 rounded-xl text-sm font-bold transition-all">Previous Page</a>
                    <?php endif; ?>
                    <span class="text-sm text-neutral-400 font-mono">Stream Vector Array: <?= $page ?></span>
                    <a href="index.php?p=<?= $page + 1 ?><?= !empty($search) ? '&q='.$search : '' ?><?= !empty($category) ? '&cat='.$category : '' ?>" class="bg-neutral-900 border border-neutral-800 hover:border-neutral-700 px-5 py-2.5 rounded-xl text-sm font-bold transition-all">Next Page</a>
                </div>
            <?php endif; ?>
        </main>

        <!-- Global Protective Compliancy Footer -->
        <footer class="bg-neutral-900 border-t border-neutral-800 mt-20 text-xs text-neutral-500 py-12 px-6">
            <div class="max-w-5xl mx-auto space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">
                    <div>
                        <h4 class="text-neutral-300 font-bold uppercase tracking-wider mb-2">Legal Navigation</h4>
                        <ul class="space-y-1">
                            <li><a href="#" class="hover:underline">Terms of Service</a></li>
                            <li><a href="#" class="hover:underline">Privacy Policy</a></li>
                            <li><a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_number']) ?>" target="_blank" class="text-emerald-500 hover:underline font-bold">WhatsApp Promotion Channel</a></li>
                        </ul>
                    </div>
                    <div class="md:col-span-2">
                        <h4 class="text-neutral-300 font-bold uppercase tracking-wider mb-2">18 U.S.C. § 2257 Record-Keeping Compliance Statement</h4>
                        <p class="leading-relaxed text-justify">
                            This platform operates explicitly as a structural indexing directory utilizing remote, cloud-routed REST API protocols. All media streams, metadata, imagery, and iframe integrations are executed natively from external 3rd-party content host syndicates. The host server infrastructure of this index does not produce, record, or retain structural file content locally. Content compliance documentation obligations remain localized with the originating platforms under global regulatory compliance frameworks.
                        </p>
                    </div>
                </div>

                <hr class="border-neutral-800">

                <div class="space-y-3">
                    <h4 class="text-neutral-300 font-bold uppercase tracking-wider text-center md:text-left">DMCA Copyright Infringement & Content Takedown Protocols</h4>
                    <p class="leading-relaxed text-justify">
                        Since all content is embedded dynamically directly from public streaming platforms via APIs, we cannot remove content directly from its host server. To systematically block an indexed payload vector from this node directory, submit a valid verification notice detailing the structural embed link parameters to our technical compliance intake handling queue. Please address validation requests via our active promotional and support interface channels.
                    </p>
                </div>

                <div class="text-center pt-4 text-[11px] text-neutral-600 font-mono">
                    &copy; <?= date('Y') ?> ADULTMATRIX Core Integration Layer. Powered entirely via Flat-File NoDB Architecture.
                </div>
            </div>
        </footer>

    </div>

    <!-- Age Verification Dynamic JavaScript Logic -->
    <script>
        function checkAgeCookie() {
            var consent = localStorage.getItem('adult_matrix_consent');
            if (consent === 'granted') {
                document.getElementById('appContainer').classList.remove('opacity-0');
            } else {
                document.getElementById('ageGate').classList.remove('hidden');
            }
        }
        function acceptAge() {
            localStorage.setItem('adult_matrix_consent', 'granted');
            document.getElementById('ageGate').classList.add('hidden');
            document.getElementById('appContainer').classList.remove('opacity-0');
        }
        window.onload = checkAgeCookie;
    </script>
    <?= $config['ad_footer'] ?>
</body>
</html>
