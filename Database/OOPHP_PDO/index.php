<?php

include_once("layout/header.php");
include_once("lib/Overzicht.php");

$Overzicht = new Overzicht();
$Overzicht->selectAttracties();


?>

    <div class="container">
        <div class="page-header">
            <h1>Attractieoverzicht</h1>
        </div>

        <?php foreach ($Overzicht->getAttracties() as $attractie) { ?>

            <div class="row">
                <div class="col-md-3"><?php echo $attractie->getAttractienaam(); ?></div>
                <div class="col-md-2"><?php echo $attractie->getAttractiesoort(); ?></div>
                <div class="col-md-7">Verwijder button</div>
            </div>

        <?php } ?>


    </div> <!-- /container -->


<?php include_once("layout/header.php"); ?>