<?php 
session_start();

if (isset($_SESSION["user"]["naam"])) {
	print_r($_SESSION);
}

if (isset($_SESSION["user"])) {
	echo "<h1>Welkom " . $_SESSION["user"]["naam"] . " op de website </h1>";
} else {
	header('location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>Website</title>
</head>
<body>
	<p><a href="login.php">Login</a></p>
</body>
</html>