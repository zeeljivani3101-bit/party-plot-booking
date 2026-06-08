<?php
$index_file = 'c:/xampp/htdocs/partyplot/index.php';
$style_file = 'c:/xampp/htdocs/partyplot/assets/css/style.css';

$index_content = file_get_contents($index_file);

// Extract the <style> block from index.php
if (preg_match('/<style>\s*\/\* Ultra-Premium Editorial Hero CSS \*\/(.*?)<\/style>/s', $index_content, $matches)) {
    $css = "/* --- Ultra-Premium Cinematic Styles --- */\n" . $matches[1];
    
    // Append to style.css
    file_put_contents($style_file, "\n" . $css, FILE_APPEND);
    
    // Remove from index.php
    $new_index = preg_replace('/<style>\s*\/\* Ultra-Premium Editorial Hero CSS \*\/.*?<\/style>/s', '', $index_content);
    file_put_contents($index_file, $new_index);
    echo "Successfully moved CSS.\n";
} else {
    echo "Could not find CSS block in index.php.\n";
}
?>
