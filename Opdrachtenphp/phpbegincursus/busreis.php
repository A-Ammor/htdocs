<?php 

$leeftijd = 12;
$korting;
$kosten = 10;

if ($leeftijd > 65) {
	$korting = 50;
} elseif ($leeftijd <= 12 && $leeftijd >= 4) {
	$korting = 50;
} elseif ($leeftijd <= 3) {
	$korting = 0;
} else {
	$korting = 100;
}

$berekening = $kosten / 100 * $korting;
echo "Ouderen boven de 65 jaar krijgen 50% korting. <br>
Kinderen van 12 jaar en jonger krijgen eveneens 50% korting. <br>
Kinderen onder de 3 jaar mogen gratis mee.<br><br>
";
echo "<b>De reis kost : " . $berekening . " euro en je betaalt " . $korting . "% van het bedrag omdat je " . $leeftijd . " jaar hebt ingevuld</b>";

 ?>