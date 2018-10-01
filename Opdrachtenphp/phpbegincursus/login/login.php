<?php 
session_start();

$users = array(
	"anwar" => array("ww" => "1", "rol" => "admin"), 
	"nadim" => array("ww" => "2", "rol" => "admin"), 
	"harjit" => array("ww" => "3", "rol" => "gebruiker"), 
	"joey" => array("ww" => "4", "rol" => "admin"),
	);

if (isset($_POST['knop'])
	&& isset($users[$_POST["login"]])
	&& $users[$_POST["login"]]["ww"] == $_POST["ww"]) {
	$_SESSION["user"] = array("naam" => $_POST["login"],
		"ww" => $users[$_POST["login"]]["ww"],
		"rol" => $users[$_POST["login"]]["rol"]);
$message = "Welkom " . $_SESSION["user"]["naam"] . " met de rol " . $_SESSION["user"]["rol"];
} else {
	$message = "Login";
}



if (isset($_POST['knop']) && ($_POST["login"]) != "anwar" && ($_POST["login"]) != "harjit" && ($_POST["login"]) != "nadim" && ($_POST["login"]) != "joey") {
	$foutmelding = "Gebruiker niet gevonden.";
} else {
	if (isset($_POST['knop']) 
		&& $users[$_POST["login"]]["ww"] != $_POST["ww"]) {
		$foutmelding = "Sorry, onjuiste wachtwoord.";
} else {
	$foutmelding = "";
}
}

if (isset($_GET["loguit"])) {
	$_SESSION = array();
	session_destroy();
	header("Location: login.php");
}
if (isset($_SESSION["user"]["naam"])) {
	print_r($_SESSION);
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>Login Pagina</title>
</head>
<body>
	<h1><?php echo "$message"; ?></h1>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label for="login">Login:</label>
		<input type="text" name="login" value=""><br>

		<label for="login">Wachtwoord:</label>
		<input type="password" name="ww" value=""><br>
		<input type="submit" name="knop" value="Verzend">
	</form>
	<p><?php echo $foutmelding ?></p>
	<p><a href="website.php">Website</a></p>
	<p><a href="admin.php">Admin</a></p>
	<p><a href="login.php?loguit">Uitloggen</a></p>
</body>
</html>