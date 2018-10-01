<?php 

$users = array(
	"piet@worldonline.nl" => "doetje123" , 
	"klaas@carpets.nl" => "snoepje777" , 
	"truushendriks@wegweg.nl" => "arkiearkie201",
	);

function gebruikers($users) {
	foreach ($users as $naam => $wachtwoord) {
		echo "<td>" . $naam . "<td>" . $wachtwoord . "<tr>";
	}

}

if (isset($_POST['knop']) 
	&& isset($users[$_POST["login"]]) 
	&& $users[$_POST["login"]] == $_POST['ww'])  {
	$message = "Welkom!!!";
} else {
	$message = "Inloggen";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href=""> 
	<title>Untitled</title>
</head>
<body>
	<h1><?php echo $message; ?></h1>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label for="login">Login: </label>
		<input type="text" name="login" value=""><br>

		<label for="ww">Wachtwoord: </label>
		<input type="password" name="ww" value=""><br>
		<input type="submit" name="knop" value="verzend">
	</form>
	<br><br>

	<table>
		<tr>
			<th>Gebruikers namen</th>
			<th>Wachtwoorden</th>
		</tr>
		<tr>
			<?php echo gebruikers($users) ?>
		</tr>
	</table>
	<style>
	table, th, td {
		border: 1px solid black;
	}
	</style>
</body>
</html>

