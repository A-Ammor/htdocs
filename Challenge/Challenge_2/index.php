<!--
* naam: Anwar
* groep: 6c
* datum: 28-9-2018
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">


    <title>Rita Hayworth</title>


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
        $image = "img/sign-in.png";
        $width = "";
        $height = "";

        echo '<a href="http://localhost/challenge/challenge_2/registration/login.php"><img src="' . $image . '" id="loginKnop" alt="login icon" style=width:"' . $width . 'px;height:' . $height . 'px;"></a>';
        ?>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="docs/imageswap.php">Picturepuzzle</a></li>
                <li><a href="docs/getallenrij.php">Getallenrij</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="picture2">
            <img class="picture" alt="" src="http://localhost/challenge/challenge_2/img/ritamobileversion_1.jpg"
                 height="100%" width="100%" id="imgClickAndChange" onclick="changeImage()"/>
        </div>

        <div class="picture">
            <img class="tabletpicture" src="img/ritamobileversion_2.jpg" width="100%" height="100%">
        </div>

        <script language="javascript">
            function changeImage() {
                if (document.getElementById("imgClickAndChange").src == "http://localhost/challenge/challenge_2/img/ritamobileversion_1.jpg") {
                    document.getElementById("imgClickAndChange").src = "http://localhost/challenge/challenge_2/img/ritamobileversion_4.jpg";
                } else if (document.getElementById("imgClickAndChange").src == "http://localhost/challenge/challenge_2/img/ritamobileversion_3.jpg") {
                    document.getElementById("imgClickAndChange").src = "http://localhost/challenge/challenge_2/img/ritamobileversion_1.jpg";
                } else if (document.getElementById("imgClickAndChange").src == "http://localhost/challenge/challenge_2/img/ritamobileversion_4.jpg") {
                    document.getElementById("imgClickAndChange").src = "http://localhost/challenge/challenge_2/img/ritamobileversion_5.jpg";
                } else if (document.getElementById("imgClickAndChange").src == "http://localhost/challenge/challenge_2/img/ritamobileversion_5.jpg") {
                    document.getElementById("imgClickAndChange").src = "http://localhost/challenge/challenge_2/img/ritamobileversion_3.jpg";
                } else {
                    document.getElementById("imgClickAndChange").src = "http://localhost/challenge/challenge_2/img/ritamobileversion_4.jpg";
                }
            }
        </script>
        <section id="nieuws">
            <h3>Nieuws</h3>

            <p>Hayworth werd geboren als de dochter van flamencodanser Eduardo Cansino en Volga Hayworth. Ze zat in een
                beroemde familie met Spaanse dansers. Ze spendeerde bijna heel haar jeugd met dansen. "From the time I
                was
                three and a half, as soon as I could stand on my own feet, I was given dance lessons." en "I didn't like
                it
                very much, but I didn't have the courage to tell my father, so I began taking the lessons. Rehearse,
                rehearse, rehearse, that was my girlhood." vertelde ze hierover.
            <p></p>
        </section>


        <div class="videoWrapper">
            <iframe width="" height="" src="https://www.youtube.com/embed/YnBmbsDan5s" frameborder="0"
                    allowfullscreen></iframe>
        </div>



    </div>

</div>


</div>
<table class="toccoloursvatop" style="margin: 0.5em 1em 0.5em 0; font-size: 85%;">
    <tbody>
    <tr>
        <td colspan="4" style="background-color: #db847d; text-align: center;"><b>Filmografie</b> als acteur
        </td>
    </tr>
    <tr style="background-color: lavender; text-align: left;">
        <th style="width: 85px; padding: 1px 4px;"></th>
        <th style="margin-right: 85px; padding: 1px 4px;"></th>
        <th style="margin-right: 85px; padding: 1px 4px;"></th>
        <th style="margin-right: 85px; padding: 1px 4px;">
        </th>
    </tr>
    <tr align="center">
        <td>1926</td>
        <td align="left"><i><a href="/w/index.php?title=Anna_Case_in_La_Fiesta&amp;action=edit&amp;redlink=1"
                               class="new" title="Anna Case in La Fiesta (de pagina bestaat niet)">Anna Case in
                    La Fiesta</a></i></td>
        <td align="left">A Dancing Cansino</td>
        <td>
        </td>
    </tr>
    <tr align="center">
        <td>1935</td>
        <td align="left"><i><a href="/wiki/In_Caliente" title="In Caliente">In Caliente</a></i></td>
        <td align="left">-</td>
        <td>
        </td>
    </tr>
    <tr align="center">
        <td>1935</td>
        <td align="left"><i><a href="/w/index.php?title=Under_the_Pampas_Moon&amp;action=edit&amp;redlink=1"
                               class="new" title="Under the Pampas Moon (de pagina bestaat niet)">Under the
                    Pampas Moon</a></i></td>
        <td align="left">Carmen</td>
        <td>
        </td>
    </tr>

    </td></tr></tbody>
</table>
</body>
<footer class="w3-theme-l2">
    <p>Rita Hayworth</p>
    <p>Gemaakt door Anwar Ammor &copy;</p>
</footer>
</html>