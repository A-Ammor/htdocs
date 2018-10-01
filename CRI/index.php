<?php
  session_start();
  include 'classes/db/Connect.php';
  include 'classes/db/QueryManager.php';

$q  = new QueryManager();

  if(isset($_POST['submit'])){
    $voornaam = $_POST['voornaam'];
    $wachtwoord = $_POST['wachtwoord'];

    $q->Login($voornaam, $wachtwoord);
  }

  if (isset($_SESSION['persoonsnummer'])) {
    $persoonsnummer = $_SESSION['persoonsnummer'];
    $functienummer = $_SESSION['functienummer'];
    echo $persoonsnummer;
    echo "<br>" . $functienummer;

  }

?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>BPV-CO Login</title>
    <link rel="stylesheet prefetch" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900">
    <link rel="stylesheet prefetch" href="http://fonts.googleapis.com/css?family=Montserrat:400,700">
    <link rel="stylesheet prefetch" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style/login_style.css">
  </head>
  
  <body>
    <div class="container">
      <div class="info">
        </span>
      </div>
    </div>
    <div class="form">
      <div class="thumbnail">
        <img src="images/logo.png">
      </div>
      <form method="post" action="index.php">
        <input type="text" name="voornaam" placeholder="gebruikersnaam">
        <input type="wachtwoord" name="wachtwoord" placeholder="wachtwoord">
        <input type="submit" name="submit" value="Login">
      </form>
    </div>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/index.js"></script>
  </body>

</html>