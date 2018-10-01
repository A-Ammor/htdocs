<?php
session_start();
require_once("../classes/model/gegevensStudent.php");

/*
echo "<table>";


//Nadim 21-02 - Als er op de knop 'Haal gegevens' is gedrukt - Begin
if (isset($_SESSION['getGegevensStudentResult'])) {
	echo "<tr><th>studentcode</th><th>opleiding</th></tr>";
	$results = $_SESSION['getGegevensStudentResult'];
	foreach ($results as $result): echo "<tr><td>" . $result . "</td></tr>"; endforeach;
	unset($_SESSION['getGegevensStudentResult']);
}
// Nadim 21-02 Einde


echo "</table>";


*/

if (isset($_SESSION['getGegevensStudentResult'])) {
	$results = unserialize($_SESSION['getGegevensStudentResult']);

	echo "<table>";
	echo "<tr><th>studentcode</th><th>opleiding</th><th>persoonsnummer</th><th>voornaam</th></tr>";

	foreach ($results as $result) {
	 	echo "<tr><td>" . $result->getStudentCode() . "</td>";
	 	echo "<td>" . $result->getStudentOpleiding() .  "</td>";
	 	echo "<td>" . $result->getStudentPersoonsnummer() . "</td>";
	 	echo "<td>" . $result->getStudentVoornaam() . "</td></tr>";
	 	unset($_SESSION['getGegevensStudentResult']);	
	 }

	 echo "</table>";
}




?>

<link rel="stylesheet" type="text/css" href="../css/haalpersoonsnummer.css">

<form action="../controller/messageController.php" method="GET">
	<input type="submit" name="getGegevensStudent" value="Haal gegevens"></input>
</form>