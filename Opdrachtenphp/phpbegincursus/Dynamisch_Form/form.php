<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form</title>
</head>
<body>
	<!-- haalt de wachtwoord in de url weg. -->
	<script>    
	    if(typeof window.history.pushState == 'function') {
	        window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF'];?>');
	    }
	</script>
	<?php  
		// if ($_GET["naam"] == "joey") {
		// 	echo "wat een fgt naam is " . $_GET["naam"] . "<br>";
		// } else if ($_GET["naam"] == "harjit") {
		// 	echo "Hey $_GET["naam"] ;) xd";
		// } else {
		// 	echo "Hey " . $_GET["naam"] . "<br>";
		// }
		print_r("Naam: " . $_GET["naam"] . "<br> Email: " . $_GET["email"] . "<br> Wachtwoord: " . $_GET["wachtwoord"]);
		die;
	?>


</body>
</html>