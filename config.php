<?php
if (basename($_SERVER['SCRIPT_FILENAME']) === 'config.php') {
    die('Direct access forbidden.');
}

$config = array (
  'admin_password' => 'emzor500',
  'api_sort' => 'mostviewed',
  'whatsapp_number' => '+2348131466173',
  'categories' => 
  array (
    0 => 'Amateur',
    1 => 'Anal',
    2 => 'Asian',
    3 => 'BBW',
    4 => 'Big Tits',
    5 => 'Ebony',
    6 => 'Latina',
    7 => 'Milf',
    8 => 'Mature',
    9 => 'POV',
    10 => 'College',
    11 => 'Threesome',
  ),
  'ad_header' => '<!-- PASTE YOUR HEADER ADS OR META TAGS HERE DIRECTLY IN ACODE -->',
  'ad_footer' => '<!-- PASTE YOUR POPUNDER OR FOOTER SCRIPTS HERE DIRECTLY IN ACODE -->',
);
