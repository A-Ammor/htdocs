<?php 

$artiesten = array(
	'Sjoerd' => 'Lied de kiet',
	'Pip' => 'Diet de fiet',
	'Willem' => 'Miet de piet',
	'Henk' => 'Siet de hiet'
	);

function artiesten($artiesten) {

	foreach ($artiesten as $artiest => $lied) {
		
		$namen = $artiest;
		$songs = $lied;

		echo "<td>" . $namen . "<td>" . $songs . "<tr>";
	}
}

?>
<table>
  <tr>
    <th>Artiest Naam</th>
    <th>Muziek Naam</th>
  </tr>
  <tr>
    <?php echo artiesten($artiesten); ?>
  </tr>
</table>