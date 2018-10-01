<?php 

	//$apen = array("chimp", "makaak", "");

	//$aap = array(
	//	"naam" => "Harry",
	//	"leeftijd" => 14
	//);

	//$aap["naam"] = "Harry";
	//$aap["leeftijd"] = 12;

	//$nogeenaap["naam"] = "Joost";
	//$nogeenaap["leeftijd"] = 13;

	//$apen = array($aap, $nogeenaap);

	//foreach ($apen as $aap) {
	//	foreach ($aap as $gegevens) {
	//		echo $gegevens . "<br>";
	//	}
	//}	

	// is hetzelfde alleen hieronder is minder code.

	$apen = array(
		array("naam" => "Harry", "leeftijd" => 14),
		array("naam" => "Joost", "leeftijd" => 16)
	);

	foreach ($apen as $aap) {
		echo $aap["naam"] . "<br>";
		echo $aap["leeftijd"]. "<br>" . "<br>";
	}

	// handig om erbij te doen.

	$alle["leeftijd"] = 12;
    $alle["naam"] = "Jan";
    $alle["telefoon"] = "0612994354";


    foreach ($alle as $label => $waarde) {
        echo "de ".$label." is: ".$waarde."<br>";
    }
 ?>