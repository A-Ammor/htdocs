<html>
<head>
	<title>zwemclubs</title>
</head>
<body>
<!--		<table style="width:300px">-->
<!--			<tr>-->
<!--				<td>De Spartelkuikens</td>-->
<!--				<td>25</td>-->
<!--			</tr>-->
<!--			<tr> -->
<!--				<td>De waterbuffels</td>-->
<!--				<td>32</td>-->
<!--			</tr>-->
<!--			<tr>-->
<!--				<td>Plonsmderin</td>-->
<!--				<td>11</td>-->
<!--			</tr>-->
<!--			<tr>-->
<!--				<td>Bommetje</td>-->
<!--				<td>23</td>-->
<!--			</tr>-->
<!--		</table>-->
		<?php
        $aantal["De spartelkuikens"] = 25;
        $aantal["De waterbuffels"] = 32;
        $aantal["Plonsmderin"] = 11;
        $aantal["Bommetje"] = 23;


        foreach ($aantal as $totaal => $waarde) {
            $uitkomst = $waarde / 5;
            for($i = 1; $i <= $uitkomst; $i++) {
                echo '<img src="img/zwem.jpg" alt="zwemlogo">';
            }
            echo "$totaal is: $waarde <br>";
        }

//		$spartel = 25;
//		$waterbuffels = 32;
//		$plonsmderin = 11;
//		$bommetje = 23;
//		$delendoor = 5;
//
//
//		for ($i=1; $i <= $spartel; $i++) {
//			if ($i % $delendoor == 0) {
//				echo '<img src="img/zwem.jpg" alt="zwemlogo">';
//			}
//		}
//		echo "spartel";
//		echo "<br/>";
//		for ($i=1; $i <= $waterbuffels; $i++) {
//			if ($i % $delendoor == 0) {
//				echo '<img src="img/zwem.jpg" alt="zwemlogo">';
//			}
//		}
//		echo "waterbuffels";
//		echo "<br/>";
//
//		for ($i=1; $i <= $plonsmderin; $i++) {
//			if ($i % $delendoor == 0) {
//				echo '<img src="img/zwem.jpg" alt="zwemlogo">';
//			}
//		}
//		echo "plonsmderin";
//		echo "<br/>";
//
//		for ($i=1; $i <= $bommetje; $i++) {
//			if ($i % $delendoor == 0) {
//				echo '<img src="img/zwem.jpg" alt="zwemlogo">';
//			}
//		}
//		echo "bommetje";
//		echo "<br/>";
		?>
</body>
</html>