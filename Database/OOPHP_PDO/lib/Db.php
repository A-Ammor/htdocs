<?php 


class Db {

	private $connectie;

	function getConnectie() {
		$servername = "localhost";
		$username = "root";
		$password = "";
		$db = "pretpark";

		try {
			$this->connectie = new PDO('mysql:host='.$servername.';dbname='.$db, $username, $password);
		} catch (PDOException $e) {
			print "Error! Er is een foutje: " . $e->getMessage() . "<br/>";
			die();
		}
		
		return $this->connectie;

	}


}