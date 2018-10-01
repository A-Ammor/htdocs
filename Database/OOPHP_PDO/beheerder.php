<?php
include_once('layout/header.php');


?>
<?php

if (isset($_POST)) {
    print_r($_POST);
}

?>

<div class="container">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-10">
            <h1>Inloggen</h1>
        </div>

    </div>

    <form class="form-horizontal" action="#" method="post">
        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">username:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="username" placeholder="username" name="voornaam">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">Password:</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password"
                       name="password">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">login</button>
            </div>
        </div>
    </form>
</div> <!-- container-->
<?php include_once('layout/footer.php'); ?>
