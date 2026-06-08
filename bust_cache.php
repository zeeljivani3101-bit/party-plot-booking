<?php
$files = glob("c:/xampp/htdocs/partyplot/admin/*.php");
foreach ($files as $f) {
    $c = file_get_contents($f);
    $c = str_replace('<link rel="stylesheet" href="../assets/css/style.css">', '<link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">', $c);
    file_put_contents($f, $c);
}
echo "Cache busted for admin files.";
?>
