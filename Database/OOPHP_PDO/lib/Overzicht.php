<?php 
// moet includen anders kan je selectattracties niet gebruiken. want je haalt het uit de database (db)
include_once("lib/Db.php");
include_once("lib/Attractie.php");

class Overzicht {
	private $attracties;

	function __construct() {
		$this->attracties = array();
	}

	function getAttracties() {
		return $this->attracties;
	}

	function addAttracties($attractie) {
		$this->attracties($attractie);
	}

	// haalt alle attracties uit de database
	function selectAttracties() {
		$db = new Db();
		$conn = $db->getConnectie();
		$sth = $conn->prepare("SELECT * FROM Attractie");
		$sth->execute();

		$this->attracties = $sth->fetchAll(PDO::FETCH_CLASS, "Attractie");
	}


}
