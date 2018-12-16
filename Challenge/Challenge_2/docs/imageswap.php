<?php session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
}
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rita Hayworth Imageswap</title>
    <link rel="stylesheet" type="text/css" href="../imageswap.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">


    <!-- Start WOWSlider.com HEAD section -->
    <link rel="stylesheet" type="text/css" href="engine1/style.css"/>
    <script type="text/javascript" src="engine1/jquery.js"></script>
</head>
<body class="w3-theme-l5">


<nav class="w3-theme-l4 navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header pull-left">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <?php
        $image = "../img/sign-in.png";
        $width = "";
        $height = "";

        echo '<a href="http://localhost/challenge/challenge_2/registration/login.php"><img src="' . $image . '" id="loginKnop" alt="login icon" style=width:"' . $width . 'px;height:' . $height . 'px;"></a>';
        ?>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav" class="w3-theme-d5">
                <li><a href="../index.php">Home</a></li>
                <li><a href="imageswap.php">Picturepuzzle</a></li>
                <li><a href="getallenrij.php">Getallenrij</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">


                <?php  if (isset($_SESSION['username'])) : ?>
                    <li><p id="loginInfo">Welcome <strong><?php echo $_SESSION['username']; ?></strong> <a href="index.php?logout='1'" style="color: red;">logout</a></p></li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>


<div id="content"></div>
<div id="punten"></div>
<script src="../imageswap.js"></script>

</body>
<footer class="w3-theme-l2">
    <p>Rita Hayworth</p>
    <p>Gemaakt door Anwar Ammor &copy;</p>
</footer>

</html>