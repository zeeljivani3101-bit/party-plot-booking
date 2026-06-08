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
    // Find  on select/input tags and remove the background part
    $c = preg_replace('/style="[^"]*background:\s*rgba\([^)]+\);?[^"]*"/', '', $c);
    file_put_contents($f, $c);
    echo "Cleaned inline styles in $f\n";
}
?>
