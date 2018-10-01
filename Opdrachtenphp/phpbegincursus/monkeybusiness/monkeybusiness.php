

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/style.css"> 
	<link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet"> 
	<title>Monkey Business</title>
</head>
<body>
	<center>
		<img src="img/monkeyhead.jpg"><br>
		<h1>Select your monkey!</h1><br>
		
		<img src="img/monkeyswing.jpg"><br>
		<?php

$apen = array(
	"Baviaan", "Guereza", "Langoer", "Neusaap", "Tamarin", "Brulaap", "Halfaap", "Mandril", 
	"Oeakari", "Faunaap", "Hoelman", "Meerkat", "Oormaki", "Gorilla", "Kuifaap", "Mensaap",
	"Spinaap",
	);

sort($apen);

foreach ($apen as $aap) {
	echo  "<a class='aap' href='http://www.google.com/search?q=$aap&tbm=isch'>$aap</a>" . "<br>";
}

?>
	</center>

</body>
</html>