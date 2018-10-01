<?php
session_start();

require_once("../classes/db/QueryManager.php");
require_once("../classes/model/gegevensStudent.php");

$q = new Querymanager();

if (isset($_GET['getGegevensStudent'])) {
	if (isset($_SESSION['persoonsnummer'])) {
	$persoonsnummer = $_SESSION['persoonsnummer'];
	$results = $q->getGegevensStudent($persoonsnummer);
	$_SESSION['getGegevensStudentResult'] = serialize($results);
	header('Location: ../view/haalpersoonsnummer.php');
	}else{
		header('Error.php');
	}
}

?>