<?php
$dirs = ['admin', 'staff mengement', 'documents model', 'whatsapp model'];
$files = glob("*.php");
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files = array_merge($files, glob("$dir/*.php"));
    }
}

foreach ($files as $f) {
    $c = file_get_contents($f);
    if (strpos($c, 'family=Inter:wght') !== false) {
        $c = str_replace(
            '<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">',
            '<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">',
            $c
        );
        file_put_contents($f, $c);
        echo "Updated $f\n";
    }
}
?>
