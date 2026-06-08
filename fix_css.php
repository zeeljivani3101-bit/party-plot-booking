<?php
$file = 'c:/xampp/htdocs/partyplot/assets/css/style.css';
$content = file_get_contents($file);
$pos = strpos($content, '.btn-sm {');
if ($pos !== false) {
    $end_pos = strpos($content, '}', $pos);
    if ($end_pos !== false) {
        $clean = substr($content, 0, $end_pos + 1) . "\n";
        file_put_contents($file, $clean);
        echo "Fixed style.css";
    }
}
