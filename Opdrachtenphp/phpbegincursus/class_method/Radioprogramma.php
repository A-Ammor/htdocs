<?php 

class Radioprogramma {

	private $programmanaam = "";
	private $omschrijving = "";
	private $liedjes = array();	


	public function voegLiedjeToe($liedje) {
		$this->liedjes[] = $liedje;
	}

	public function getLiedjes() {
		return $this->liedjes;
	}

	/*
	Geeft programma info terug
	@return array
	*/
	public function getProgramma() {	
		return array("naam" => $this->programmanaam,
			"omschrijving" => $this->omschrijving
			);
	}

	/*
	Geeft het programma een naam
	@param de naam als string
	*/
	public function setnaam($naam) {
		if (strlen($naam) >= 2) {
			$this->programmanaam = $naam;
		}
		
	}

	/*
	Geeft het programma een omschrijving
	@param de omschrijving als string
	*/
	public function setOmschrijving($omschrijving) {
		$this->omschrijving = $omschrijving;
	}

	/*
	retouneert naam van het programma
	@param de naam als string
	*/
	public function getNaam() {
		return $this->programmanaam;
	}

	/*
	retouneert omschrijving van het programma
	@param de omschrijving als string
	*/
	public function getOmschrijving() {
		return $this->omschrijving;
	}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>OOP met Methods</title>
</head>
<body>

</body>
</html>