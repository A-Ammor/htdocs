<?php
/**
 * Created by PhpStorm.
 * User: aammo
 * Date: 6-10-2017
 * Time: 09:09
 */

// functie die als een waarde in celsius accepteert en de temp in Fahrenheit weergeeft.
// T(°C) = (T(°F) - 32) × 5/9
$celsius = 1;
$berekening = 9 / 5 + 32;

function fahrenheit($celsius, $berekening)
{
	$fahrenheit = $celsius * $berekening;
	return $fahrenheit;
}
echo "$celsius Celsius is: " . fahrenheit($celsius, $berekening) . " Fahrenheit <br><br>";



// functie die een getal kan delen door 3 en met een retour waarde - boolean

$boolean = false;
$invoer = 6; 
$delenDoor = 3;

function delen($invoer, $delenDoor) 
{

	if ($invoer %$delenDoor == 0) {
		$boolean = true;
	} else {
		$boolean = false;
	}
	$totaal = $invoer;

	if ($boolean == true) {
		echo "Het getal " . $totaal . " kan wel gedeelt worden door " . $delenDoor . "<br><br>";

	} 

	if ($boolean == false) {
		echo "Het getal " . $totaal . " kan niet gedeelt worden door " . $delenDoor . "<br><br>";
	}
	return $boolean;
}
delen($invoer, $delenDoor);



//functie die string accepteert als argument en return een string met letters omgekeerd.


function reverse($reverted) 
{
	$reverted = implode(array_reverse(str_split("Anwar")));
	$c = $reverted;
	return $c;
}

echo reverse("Anwar");


// bovenste werkt niet zo goed door die implode functie maar dit is gwn de opdracht.


function telOp($a, $b) {
	$c = $a + $b;
	return $c;
}

echo  "<br><br>" . "a + b = " . telOp(1, 2);

?>