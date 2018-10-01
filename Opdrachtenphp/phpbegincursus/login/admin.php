<?php 
session_start();

if (isset($_SESSION["user"]["naam"])) {
	print_r($_SESSION);
}	

if ($_SESSION["user"] && $_SESSION["user"]["rol"] == "admin"){
	$message = "Welkom " . $_SESSION["user"]["naam"] . " op de admin pagina.";
} elseif ($_SESSION["user"] && $_SESSION["user"]["rol"] == "gebruiker") {
	$message = "U heeft onvoldoende rechten. klik <a href='login.php'><u>hier</u></a> om terug te naar de inlog pagina";
} else {
	header("location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>Admin</title>
</head>
<body>
	<h1><?php  echo $message; ?></h1>
	<p><?php if ($_SESSION["user"]["rol"] == "admin") {
		echo "<a href='login.php'>login</a>";
	}?></p>
</body>
</html>