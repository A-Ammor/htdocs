<?php
session_start();
include "Car.php";
$merken = array("Audi", "Ferrari", "Mercedes", "Opel", "Volkswagen");
$carList = array(
  new Car("Audi", 23200, "audi1"),
  new Car("Audi", 8250, "audi2"),
  new Car("Ferrari", 9500, "ferrari1"),
  new Car("Ferrari", 122300, "ferrari2"),
  new Car("Ferrari", 154500, "ferrari3"),
  new Car("Mercedes", 8200, "mercedes1"),
  new Car("Mercedes", 132700, "mercedes2"),
  new Car("Mercedes", 87500, "mercedes3"),
  new Car("Mercedes", 22250, "mercedes4"),
  new Car("Opel", 18500, "opel1"),
  new Car("Opel", 6500, "opel2"),
  new Car("Opel", 29500, "opel3"),
  new Car("Volkswagen", 19340, "volkswagen1"),
  new Car("Volkswagen", 13340, "volkswagen2"),
  new Car("Volkswagen", 256070, "volkswagen3")
  );

if(isset($_POST['brandSelect'])){
  $merk = $_POST['brandSelect'];
}else{
  $merk = "Alles";
}

$highestValueCar = 0;

foreach ($carList as $car) {
  if ($highestValueCar < $car->getPrice()) {
    $highestValueCar = $car->getPrice();
  }
}

if (isset($_POST["brand"])) {
  $brandSelected = $_POST["brand"];
  $brandList = array($brandSelected);
}else{
  $brandList = array();
}

foreach ($carList as $car) {
  if (!in_array($car->getBrand(), $brandList)) {
    $brandList[] = $car->getBrand();
  }  
}

if (isset($brandSelected)) {
  $brandList = array_diff($brandList, array($brandSelected));
}


if(isset($_POST["brand"])){
  if ($_POST["brand"] != "AlleMerken"){
    foreach ($carList as $car) {
      if ($car->getBrand() != $_POST["brand"]) {
        unset($carList[array_search($car, $carList)]);
      } 
    } 
  }
}

if(isset($_POST["minPrice"])){
  foreach($carList as $car){
    if($car->getPrice() < $_POST["minPrice"]){
      unset($carList[array_search($car, $carList)]);
    }
  }
}

if(isset($_POST["maxPrice"])){
  if ($_POST["maxPrice"] == "") {
    $_POST["maxPrice"] = $highestValueCar;
  }else{
    foreach($carList as $car){
      if($car->getPrice() > $_POST["maxPrice"]){
        unset($carList[array_search($car, $carList)]);
      }
    }
  }
}
?>



<!DOCTYPE html>
<html lang="en">
<style>
</style>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Mr Wheely!</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
  crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <header>
    <div class="container wheely">

    </div>
  </header>

  <div class="container">
    <div class="content">
      <form action="index.php" method="POST">
        <div class="form-group">
          <label for="brandSelect">Merk</label>
          <select id="brandSelect" class="form-control" name="brand">
            <?php if($_POST["brand"] != "AlleMerken" && $_POST["brand"] != ""){ ?> 
            <option value="<?php echo $_POST["brand"];?>" default><?php echo $_POST["brand"] ?></option>
            <?php }?> 
            <option value="AlleMerken" default>Alle Merken</option>

            <?php foreach ($brandList as $brand) { ?>

            <option value="<?php echo $brand;?>"><?php echo $brand; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group">
          <label for="minPriceSelect">Minimum prijs</label>
          <input type="text" id="minPriceSelect" name="minPrice" class="form-control"/>
        </div>
        <div class="form-group">
          <label for="maxPriceSelect">Maximum prijs</label>
          <input type="text" id="maxPriceSelect" name="maxPrice" class="form-control"/>
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-success" value="Submit"/>
        </div>
      </form>

      <hr>

      <div class="row">
        <?php foreach ($carList as $car) {  ?>

        <div class="thumbnail" style="float:left; margin:1%; width:17%;">
          <div style="height:15vh; width:100%;">
            <img src="img/<?php echo $car->getImageName(); ?>.jpg" alt="plaatje" style=" max-width:100%; max-height:100%; vertical-align: central"/>
          </div>
          <div class="caption">
            <?php echo($car->getBrand() . "<br>"); ?>
            <?php echo("€ " . $car->getPrice() . ",-"); ?>
          </div>
        </div>
        <?php } ?>
      </div>
      <br>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" 
  integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" 
  crossorigin="anonymous"></script>
</body>
</html>