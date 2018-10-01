<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<?php

$kappersagenda["9.15"] = "Mevr.Pietersen";
$kappersagenda["9.30"] = "Mevr.Willems";
$kappersagenda["9.45"] = "";
$kappersagenda["10.00"] = "Paul van den Broek";
$kappersagenda["10.15"] = "Karel de Meeuw";
$kappersagenda["10.30"] = "";

echo "De volgende momenten zijn nog beschikbaar: <ul>";

foreach ($kappersagenda as $tijd => $afspraak) {
    if ($afspraak == "") {
        echo "<li>  $tijd  </li><br>";
    }
}

echo "</ul>";

?>
</body>
</html>