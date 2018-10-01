<?php


// Nadim 22-02 - Er wordt een klasse aangemaakt die gegevens van de student opslaat om deze later op te halen in de view - Begin
class gegevensStudent
{

	private $studentcode;
    private $opleiding; 
	
	function __construct($dbRow)
	{
		$this->studentcode = $dbRow['studentcode'];
		$this->opleiding = $dbRow['opleiding'];
		$this->persoonsnummer = $dbRow['persoonsnummer'];
		$this->voornaam = $dbRow['voornaam'];
	}

	public function getStudentCode()
	{
		return $this->studentcode;

	}

	public function getStudentOpleiding()
	{
		return $this->opleiding;

	}

	public function getStudentPersoonsnummer()
	{
		return $this->persoonsnummer;

	}

	public function getStudentVoornaam()
	{
		return $this->voornaam;

	}


}

// Nadim 22-02 Einde

?>