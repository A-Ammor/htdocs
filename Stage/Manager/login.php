<?php
session_start();
if(isset($_SESSION['username']) && isset($_SESSION['department']) && isset($_SESSION['department_id'])) {
    header("Location: index.php");
    exit;
}

// Remove from production
if(isset($_GET['database'])) {
    $_SESSION['database'] = $_GET['database'];
}

require_once 'Db.php';


$stmt = $pdo->prepare('SELECT department_id, department_name FROM department');
$stmt->execute();
$result = $stmt->fetchAll();
?>

<!DOCTYPE html>
    <!--[if IE 9 ]><html class="ie9"><![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TalentPass - Manager</title>

        <!-- Vendor CSS -->
        <link href="vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
        <link href="vendors/bower_components/google-material-color/dist/palette.css" rel="stylesheet">
        <link href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">
        <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">

        <!-- CSS -->
        <link href="css/app.min.1.css" rel="stylesheet">
        <link href="css/app.min.2.css" rel="stylesheet">
    </head>

    <body>
        <div class="login" data-lbg="teal">
            <!-- Login -->
            <div class="l-block toggled" id="l-login">
                <div class="lb-header palette-Teal bg">
                    Please Sign in
                </div>


                <div id="spinner" class="center-block" style="padding-top: 16px; padding-bottom: 16px;">
                    <div class="preloader pl-xxl pls-blue" style="display: block">
                        <svg class="pl-circular" viewBox="25 25 50 50">
                            <circle class="plc-path" cx="50" cy="50" r="20"></circle>
                        </svg>
                    </div>
                </div>

                <form>

                <div class="lb-body" id="login-body">
                    <?php
                    //Remove from production
                    if(isset($_GET['database'])) {
                        echo '<div class="alert alert-success" role="alert" id="alert">Database successfully set to '.$_GET['database'].'</div>';
                    }
                    ?>
                    <div class="alert alert-danger" role="alert" id="alert-danger">Incorrect username or password.</div>
                    <div class="form-group fg-float" id="login-input">
                        <div class="fg-line">
                            <input type="text" name="username" id="username" class="input-sm form-control fg-input">
                            <label class="fg-label">Username</label>
                        </div>
                    </div>

                    <div class="form-group fg-float" id="password-input">
                        <div class="fg-line">
                            <input type="password" name="password" id="password" class="input-sm form-control fg-input">
                            <label class="fg-label">Password</label>
                        </div>
                    </div>
                    <div class="form-group fg-float">
                        <select class="selectpicker" name="department" id="department">
                            <?php
                            foreach ($result as $row) {
                                echo '<option id="'.$row['department_id'].'">'.$row['department_name'].'</option>';
                            }
                            ?>
                        </select>
                      </div>
                    <button class="btn palette-Teal bg" id="btn-login">Sign in</button>
                </div>

                    <div class="p-l-10 p-b-5">
                        <small>TalentPass mng 0.0.2 © 2018 TalentDeveloper</small>
                    </div>
                </form>
            </div>
        </div>


        <!-- Javascript Libraries -->
        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/bower_components/Waves/dist/waves.min.js"></script>
        <script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>

        <!-- Placeholder for IE9 -->
        <!--[if IE 9 ]>
            <script src="vendors/bower_components/jquery-placeholder/jquery.placeholder.min.js"></script>
        <![endif]-->


        <script src="js/functions.js"></script>

        <script>
            var fade;
            $(document).ready(function(){
                $("#spinner").css('display', 'none', 'important');
                $("#alert").css('margin-bottom', '32px', 'important');
                $("#alert-danger").css('margin-bottom', '32px', 'important');
                $("#alert-danger").css('display', 'none', 'important');

                $("#btn-login").click(function(){
                    $("#alert-danger").css('display', 'none', 'important');
                    username=$("#username").val();
                    password=$("#password").val();
                    department=$("#department").val();
                    department_id=$('.selectpicker option:selected').attr('id');

                    if(username.length > 0 && password.length > 0) {
                        $.ajax({
                            type: "POST",
                            url: "login-request.php",
                            data: "username=" + username + "&password=" + password + "&department=" + department + "&department_id=" + department_id,
                            success: function (html) {

                                var obj = jQuery.parseJSON(html);

                                if (obj.success == true) {
                                    window.location = "index.php";
                                } else {
                                    $("#login-body").css('display', 'block', 'important');
                                    $("#alert-danger").css('display', 'block', 'important');
                                    $("#spinner").css('display', 'none', 'important');
                                    fadeAlert();
                                }
                            },
                            beforeSend: function () {
                                $("#login-body").css('display', 'none', 'important');
                                $("#spinner").css('display', 'block', 'important');
                                $("#alert").css('display', 'none', 'important');
                            }
                        });
                    } else {
                        if(username.length === 0) {
                        $("#login-input").addClass('has-error');
                        }
                        if(password.length === 0) {
                            $("#password-input").addClass('has-error');
                        }
                    }
                    return false;
                });
            });

            function fadeAlert() {
                clearTimeout(fade);
                fade = setTimeout(function() {
                    $("#alert-danger").fadeOut(700);
                }, 4000);
            }
        </script>
    </body>
</html>
