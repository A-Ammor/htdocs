<?php 

// $hond = "hond";
// $kat = "kat";
// $aap = "aap";
// $beer = "beer";

// $hondHoofdletter = "Hond";
// $katHoofdletter = "Kat";
// $aapHoofdletter = "Aap";
// $beerHoofdletter = "Beer";


// if ($_GET["naam"] == $hond || $_GET["naam"] == $hondHoofdletter) {
// 	echo '<img src="img/hond.jpg">';
// } elseif ($_GET["naam"] == $kat || $_GET["naam"] == $katHoofdletter) {
// 	echo '<img src="img/kat.jpg">';
// } elseif ($_GET["naam"] == $aap || $_GET["naam"] == $aapHoofdletter) {
// 	echo '<img src="img/apen.jpg">';
// } elseif ($_GET["naam"] == $beer || $_GET["naam"] == $beerHoofdletter) {
// 	echo '<img src="img/bear.jpg">';
// } else {
// 	echo "Sorry, <b>" . $_GET["naam"] . "</b> staat niet in de lijst. Probeer het opnieuw";
// }

// 	echo '<img src="img/hond.jpg">';
// } elseif ($_GET["naam"] == $kat || $_GET["naam"] == $katHoofdletter) {
// 	echo '<img src="img/kat.jpg">';
// } elseif ($_GET["naam"] == $aap || $_GET["naam"] == $aapHoofdletter) {
// 	echo '<img src="img/apen.jpg">';
// } elseif ($_GET["naam"] == $beer || $_GET["naam"] == $beerHoofdletter) {
// 	echo '<img src="img/bear.jpg">';
// } else {
// 	echo "Sorry, <b>" . $_GET["naam"] . "</b> staat niet in de lijst. Probeer het opnieuw";
// }


if (isset($_GET["hond"])) {
	echo '<img src="img/hond.jpg">';
} 
if (isset($_GET["kat"])) {
	echo '<img src="img/kat.jpg">';
} 
if (isset($_GET["aap"])) {
	echo '<img src="img/apen.jpg">';
} 
if (isset($_GET["beer"])) {
	echo '<img src="img/bear.jpg">';
} 
if (!isset($_GET["hond"]) && !isset($_GET["kat"]) && !isset($_GET["beer"]) && !isset($_GET["beer"])) {
		echo "Je hebt geen dier uitgekozen :( klik <a href='beasties.php'> hier </a> om terug te gaan naar de vorige pagina";
} else {
echo "<br>Leuk! klik <a href='beasties.php'><u>hier</u> </a> om terug te gaan naar de vorige pagina";
}



 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>Uitgekozen Dier(en)</title>
</head>
<body>

</body>
</html>