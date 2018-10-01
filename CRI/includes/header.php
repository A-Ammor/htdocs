<?php
  session_start();
  include '../classes/db/Connect.php';
  include '../classes/db/QueryManager.php';


  if (isset($_SESSION['persoonsnummer'])) {
    $persoonsnummer = $_SESSION['persoonsnummer'];
    $functienummer = $_SESSION['functienummer'];
  }

?>


<?php
if($_SESSION['functienummer'] == 1){
  	echo 

  '<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
    rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.css">
  </head>
  
  <body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand"><img height="50" alt="Brand" src="../images/logo.png"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="../view/home.php">Home</a>
            </li>
            <li>
              <a href="../view/bezoeken.php">Bezoeken</a>
            </li>
            <li>
              <a href="../view/gebruikers.php">Gebruikers Informatie</a>
            </li>
            <li>
              <a href="../view/maps.php">BPV-CO maps</a>
            </li>
            <li>
              <a href="#">Kalender</a>
            </li>
            <li>
              <a href="../view/loguit.php">Loguit</a>
            </li>
          </ul>
        </div>
      </div>
    </div>';
  }

  if($_SESSION['functienummer'] == 2){
  	echo '<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
    rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.css">
  </head>
  
  <body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand"><img height="50" alt="Brand" src="../images/logo.png"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="../view/home.php">Home</a>
            </li>
            <li>
              <a href="../view/bezoeken.php">Bezoeken</a>
            </li>
            <li>
              <a href="../view/gebruikers.php">Gebruikers Informatie</a>
            </li>
            <li>
              <a href="../view/maps.php">BPV-CO maps</a>
            </li>
            <li>
              <a href="#">Kalender</a>
            </li>
            <li>
              <a href="../view/loguit.php">Loguit</a>
            </li>
          </ul>
        </div>
      </div>
    </div>';
  }

  if($_SESSION['functienummer'] == 3){
  	echo 
  	'<html>
  	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
    rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.css">
  </head>
  
  <body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand"><img height="50" alt="Brand" src="../images/logo.png"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="../view/home.php">Home</a>
            </li>
            <li>
              <a href="../view/bezoeken.php">Bezoeken</a>
            </li>
            <li>
              <a href="../view/gebruikers.php">Gebruikers Informatie</a>
            </li>
            <li>
              <a href="../view/maps.php">BPV-CO maps</a>
            </li>
            <li>
              <a href="#">Kalender</a>
            </li>
            <li>
              <a href="../view/loguit.php">Loguit</a>
            </li>
          </ul>
        </div>
      </div>
    </div>';
  }

  if($_SESSION['functienummer'] == 4){
  	echo '<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
    rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.css">
  </head>
  
  <body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand"><img height="50" alt="Brand" src="../images/logo.png"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="../view/home.php">Home</a>
            </li>
            <li>
              <a href="../view/bezoeken.php">Bezoeken</a>
            </li>
            <li>
              <a href="../view/gebruikers.php">Gebruikers Informatie</a>
            </li>
            <li>
              <a href="../view/maps.php">BPV-CO maps</a>
            </li>
            <li>
              <a href="#">Kalender</a>
            </li>
            <li>
              <a href="../view/loguit.php">Loguit</a>
            </li>
          </ul>
        </div>
      </div>
    </div>';
  }

  if($_SESSION['functienummer'] == 5){
  	echo '<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
    rel="stylesheet" type="text/css"> 
    <link rel="stylesheet" type="text/css" href="../style/bootstrap.css">
  </head>
  
  <body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand"><img height="50" alt="Brand" src="../images/logo.png"></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="../view/home.php">Home</a>
            </li>
            <li>
              <a href="../view/bezoeken.php">Bezoeken</a>
            </li>
            <li>
              <a href="../view/gebruikers.php">Gebruikers Informatie</a>
            </li>
            <li>
              <a href="../view/maps.php">BPV-CO maps</a>
            </li>
            <li>
              <a href="#">Kalender</a>
            </li>
            <li>
              <a href="../view/loguit.php">Loguit</a>
            </li>
          </ul>
        </div>
      </div>
    </div>';
  }

?>