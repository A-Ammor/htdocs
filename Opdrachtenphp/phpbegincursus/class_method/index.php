<?php 
include_once('Radioprogramma.php');
include_once('Liedje.php');


$ditprogramma = new Radioprogramma();
$ditprogramma->setnaam("Mijn eerste programma");
$ditprogramma->setOmschrijving("Even testen");

foreach($ditprogramma->getProgramma() as $p) {
	echo $p . "<br>";
}

$nieuwliedje = new Liedje("<br>Dit is de titel", "Rolling stones");

$ditprogramma->voegLiedjeToe($nieuwliedje);
foreach ($ditprogramma->getLiedjes() as $liedje) {
	echo $liedje->getTitel() . " - " . $liedje->getArtiest() . "<br>";
}

?>