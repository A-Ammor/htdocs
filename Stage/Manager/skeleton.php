<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['department']) || !isset($_SESSION['department_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'Db.php';


$person_id = 2;
$department_selected = 0;
$list = array();


$stmt = $pdo->prepare('SELECT p.id, p.first_name, p.prefix, p.last_name FROM person_cv p
                                WHERE p.department_id = ? ORDER BY p.first_name ASC');
//$stmt->execute(array($department_id));
//$result_people = $stmt->fetchAll();


//
//if (isset($_SESSION['person_id'])) {
//    $person_id = $_SESSION['person_id'];
//} else {
//    $person_id = $result_people[0]['id'];
//    $_SESSION['person_id'] = $result_people[0]['id'];
//}


?>

<!DOCTYPE html>
<!--[if IE 9 ]>
<html class="ie9"><![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TalentPass - Manager</title>

    <!-- Vendor CSS -->
    <link href="vendors/bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
    <link href="vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
    <link href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css"
          rel="stylesheet">
    <link href="vendors/bower_components/google-material-color/dist/palette.css" rel="stylesheet">
    <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">

    <!-- CSS -->
    <link href="css/app.min.1.css" rel="stylesheet">
    <link href="css/app.min.2.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">


</head>
<body data-ma-header="teal">
<header id="header">
    <div class="pull-left h-logo">
        <a href="index.php" class="hidden-xs">
            TalentPass
            <small>Manager</small>
        </a>

        <div class="menu-collapse" data-ma-action="sidebar-open" data-ma-target="main-menu">
            <div class="mc-wrap">
                <div class="mcw-line top palette-White bg"></div>
                <div class="mcw-line center palette-White bg"></div>
                <div class="mcw-line bottom palette-White bg"></div>
            </div>
        </div>
    </div>

    <div class="media-body picker-style">
        <select class="selectpicker" id="people-picker" data-live-search="true">
            <?php
            foreach ($result_people as $row) {
                if (strlen($row['prefix']) == 0) {
                    $full_name = $row['first_name'] . " " . $row['last_name'];
                } else {
                    $full_name = $row['first_name'] . " " . $row['prefix'] . " " . $row['last_name'];
                }
                echo '<option id="' . $row['id'] . '">' . $full_name . '</option>';
            }
            ?>
        </select>
    </div>


</header>

<section id="main">

    <aside id="s-main-menu" class="sidebar">
        <div class="smm-header">
            <i class="zmdi zmdi-long-arrow-left" data-ma-action="sidebar-close"></i>
        </div>

        <ul class="smm-alerts">
            <li data-user-alert="sua-messages" data-ma-target="user-alerts">
                <i class="zmdi zmdi-email"></i>
            </li>
            <li data-user-alert="sua-notifications" data-ma-target="user-alerts">
                <i class="zmdi zmdi-notifications"></i>
            </li>
            <li data-user-alert="sua-tasks" data-ma-target="user-alerts">
                <i class="zmdi zmdi-view-list-alt"></i>
            </li>
        </ul>

        <ul class="main-menu">
            <li>
                <a href="index.php"><i class="zmdi zmdi-file-text"></i> Curriculum Vitae</a>
            </li>
            <li>
                <a href="assessment.php"><i class="zmdi zmdi-comment-alert"></i> Appreciation</a>
            </li>
            <li>
                <a href="reflection.php"><i class="zmdi zmdi-accounts"></i> Reflection, introspection</a>
            </li>
            <li>
                <a href="development.php"><i class="zmdi zmdi-chart"></i> Development</a>
            </li>
            <li>
                <a href="feedback-status.php"><i class="zmdi zmdi-comment-list"></i> Feedback status</a>
            </li>
            <li>
                <a href="change-role.php"><i class="zmdi zmdi-refresh"></i> Change role profile</a>
            </li>
            <li class="active">
                <a href="skeleton.php"><i class="zmdi zmdi-receipt"></i> Feedback report</a>
            </li>
            <li>
                <a href="index.php"><i class="zmdi zmdi-badge-check"></i> Close validated period</a>
            </li>
            <li>
                <a href="index.php"><i class="zmdi zmdi-search"></i> Role calculator</a>
            </li>
            <li>
                <a href="index.php"><i class="zmdi zmdi-accounts-list"></i> Competence card</a>
            </li>
            <li>
                <a href="index.php"><i class="zmdi zmdi-accounts-list"></i> Role score card</a>
            </li>
            <li>
                <a href="index.php"><i class="zmdi zmdi-star"></i> Leaderboard</a>
            </li>
            <li>
                <a href="logout.php"><i class="zmdi zmdi-power"></i> Logout</a>
            </li>
        </ul>
    </aside>

    <!------------------------------------  Hier begin ik.  --------------------------------------->
    <section id="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Filter on Role</h2> <br>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <div class="fg-line">
                                    <div class="select">
                                        <select class="form-control">
                                            <option>Select an Option</option>
                                            <option>Option 1</option>
                                            <option>Option 2</option>
                                            <option>Option 3</option>
                                            <option>Option 4</option>
                                            <option>Option 5</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="radio m-b-15 active">
                                <label>
                                    <input type="radio" name="feedback" value="" checked="">
                                    <i class="input-helper"></i>
                                    Receive feedback from
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="radio m-b-15">
                                <label>
                                    <input type="radio" name="feedback" value="">
                                    <i class="input-helper"></i>
                                    Evaluate (give feedback to)
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php
                    $stmt = $pdo->prepare('SELECT DISTINCT COUNT(DISTINCT rc.id_criteria) as criteria
                    FROM givefeedback gf INNER JOIN person_cv cv on cv.id = ?
                    INNER JOIN period o ON o.id = cv.id
                    INNER JOIN role_profile rp on rp.role_id = o.role_id
                    INNER JOIN role_competencecriteria rc on rc.id_role = o.role_id
                    WHERE period_date_till IS NULL');
                    $stmt->execute(array($person_id));
                    $result_max_behaviour = $stmt->fetch();

                    if ($department_selected == 0) {
                        $stmt = $pdo->prepare('SELECT DISTINCT cv.id, cv.first_name, cv.prefix, cv.last_name, r.name as role_name, d.department_name FROM person_cv cv
                    INNER JOIN period o ON o.id = cv.id
                    INNER JOIN role_profile r ON r.role_id = o.role_id
                    INNER JOIN department d ON d.department_id = cv.department_id
                    INNER JOIN givefeedback gf
                    WHERE cv.id != ? AND gf.id_user IS NOT NULL AND gf.id_user = ? AND gf.id_invitee = cv.id AND o.period_date_till IS NULL GROUP BY cv.id');
                        $stmt->execute(array($person_id, $person_id));
                        $result_invited_info = $stmt->fetchAll();

                        foreach ($result_invited_info as $row) {
                            $invitee = $row['id'];
                            $first_name = $row['first_name'];
                            $prefix = $row['prefix'];
                            $last_name = $row['last_name'];
                            $department_name = $row['department_name'];
                            $role_name = $row['role_name'];

                            $stmt = $pdo->prepare("SELECT DISTINCT sum(case when fr.level is not null then 1 else 0 end) as scored ,sum(case when fr.improvement_remark <> '' then 1 else 0 end) as improve,
                    sum(case when fr.appreciation_remark <> '' then 1 else 0 end) as appreciation  FROM role_competencecriteria rc
                    INNER JOIN feedback_rel fr on fr.id_criteria = rc.id_criteria
                    INNER JOIN period o on o.id = fr.id_invitee
                    WHERE rc.id_role = o.role_id AND fr.id_invitee = ? AND o.period_date_till IS NULL AND fr.period_id = o.period_id AND fr.id_person = ?");
                            $stmt->execute(array($person_id, $invitee));
                            $result_invited_invitee = $stmt->fetch();

                            $person = array(
                                'id' => $invitee,
                                'first_name' => $first_name,
                                'prefix' => $prefix,
                                'last_name' => $last_name,
                                'department_name' => $department_name,
                                'role_name' => $role_name,
                                'improve' => $result_invited_invitee['improve'],
                                'appreciation' => $result_invited_invitee['appreciation'],
                                'scored' => $result_invited_invitee['scored']);
                            array_push($list, $person);
                        }

                        $stmt = $pdo->prepare('SELECT p.first_name, p.last_name, p.prefix, d.department_name, r.* FROM person_cv p
                    INNER JOIN period o ON p.id = o.id
                    INNER JOIN role_profile r ON o.role_id = r.role_id
                    INNER JOIN department d ON d.department_id = p.department_id
                    WHERE p.id = ? AND o.period_date_till IS NULL');
                        $stmt->execute(array($person_id));
                        $result_person = $stmt->fetch();

                        $stmt = $pdo->prepare("SELECT DISTINCT sum(case when fr.level is not null then 1 else 0 end) as scored ,sum(case when fr.improvement_remark <> '' then 1 else 0 end) as improve,
                    sum(case when fr.appreciation_remark <> '' then 1 else 0 end) as appreciation  FROM role_competencecriteria rc
                    INNER JOIN feedback_mng_rel fr on fr.id_criteria = rc.id_criteria
                    INNER JOIN period o on o.id = fr.id_invitee
                    WHERE rc.id_role = o.role_id AND fr.id_invitee = ? AND o.period_date_till IS NULL AND fr.period_id = o.period_id");
                        $stmt->execute(array($person_id));
                        $result_invited_manager = $stmt->fetch();

                        if ($result_invited_manager['scored'] != 0) {
                            $person = array(
                                'id' => "mng",
                                'first_name' => "",
                                'prefix' => "",
                                'last_name' => "",
                                'department_name' => $result_person['department_name'],
                                'role_name' => "Manager",
                                'improve' => $result_invited_manager['improve'],
                                'appreciation' => $result_invited_manager['appreciation'],
                                'scored' => $result_invited_manager['scored']);

                            array_push($list, $person);
                        }
                    } else {
                        $stmt = $pdo->prepare('SELECT DISTINCT cv.id, cv.first_name, cv.prefix, cv.last_name, r.name as role_name, d.department_name,
                    sum(DISTINCT case when gf.id_user is not null and gf.id_user = ? and gf.id_invitee = cv.id then 1 else 0 end) as invited
                    FROM person_cv cv
                    INNER JOIN period o ON o.id = cv.id
                    INNER JOIN role_profile r ON r.role_id = o.role_id
                    INNER JOIN department d ON d.department_id = cv.department_id
                    INNER JOIN givefeedback gf
                    WHERE cv.id != ? AND cv.department_id = ?
                    AND o.period_date_till IS NULL GROUP BY cv.id');
                        $stmt->execute(array($person_id, $person_id, $department_selected));
                        $result_invited_info = $stmt->fetchAll();

                        foreach ($result_invited_info as $row) {
                            if ($row['invited'] == 0) {
                                $invitee = $row['id'];
                                $first_name = $row['first_name'];
                                $prefix = $row['prefix'];
                                $last_name = $row['last_name'];
                                $department_name = $row['department_name'];
                                $role_name = $row['role_name'];

                                $stmt = $pdo->prepare("SELECT DISTINCT sum(case when fr.level is not null then 1 else 0 end) as scored ,sum(case when fr.improvement_remark <> '' then 1 else 0 end) as improve,
                    sum(case when fr.appreciation_remark <> '' then 1 else 0 end) as appreciation  FROM role_competencecriteria rc
                    INNER JOIN feedback_rel fr on fr.id_criteria = rc.id_criteria
                    INNER JOIN period o on o.id = fr.id_invitee
                    WHERE rc.id_role = o.role_id AND fr.id_invitee = ? AND o.period_date_till IS NULL AND fr.period_id = o.period_id AND fr.id_person = ?");
                                $stmt->execute(array($person_id, $invitee));
                                $result_invited_invitee = $stmt->fetch();

                                $person = array(
                                    'id' => $invitee,
                                    'first_name' => $first_name,
                                    'prefix' => $prefix,
                                    'last_name' => $last_name,
                                    'department_name' => $department_name,
                                    'role_name' => $role_name,
                                    'improve' => $result_invited_invitee['improve'],
                                    'appreciation' => $result_invited_invitee['appreciation'],
                                    'scored' => $result_invited_invitee['scored']);

                                array_push($list, $person);
                            }

                            $stmt = $pdo->prepare('SELECT p.first_name, p.last_name, p.prefix, d.department_name, r.* FROM person_cv p
                    INNER JOIN period o ON p.id = o.id
                    INNER JOIN role_profile r ON o.role_id = r.role_id
                    INNER JOIN department d ON d.department_id = p.department_id
                    WHERE p.id = ? AND o.period_date_till IS NULL');
                            $stmt->execute(array($person_id));
                            $result_person = $stmt->fetch();
                        }
                    }
                    ?>
                    <div class="table-responsive">
                        <table id="data-table-received" class="table table-striped">
                            <thead>
                            <tr>
                                <th data-column-id="profile" data-formatter="pic" data-sortable="false">Profile</th>
                                <th data-column-id="percentage" data-order="desc">Percentage</th>
                                <th data-column-id="scored" data-type="numeric">About
                                </th>
                                <th data-column-id="scored" data-type="numeric">Scored</th>
                                <th data-column-id="above" data-type="numeric">Above</th>
                                <!--                                <th data-column-id="total" data-type="numeric">Total</th>-->
                                <th data-column-id="improve" data-type="numeric">Improve</th>
                                <th data-column-id="person">Name</th>
                                <th data-column-id="role">Role</th>
                                <th data-column-id="department">Department</th>
                                <th data-column-id="commands" data-formatter="commands" data-sortable="false">Commands
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $id = 0;

                            foreach ($list as $row) {

                                if (strlen($row['prefix']) == 0) {
                                    $full_name = $row['first_name'] . " " . $row['last_name'];
                                } else {
                                    $full_name = $row['first_name'] . " " . $row['prefix'] . " " . $row['last_name'];
                                } ?>
                                <tr>
                                    <!--                                    hidden bij class om het weg te laten-->
                                    <td class=""><?php
                                        if ($row['id'] == "mng") {
                                            echo 'manager.png';
                                        } else if ($row['id'] == "guest") {
                                            echo 'guest.png';
                                        } else {
//                                          height en width later naar css doen.
                                            echo "<img src='https://softwareguardian.eu/talentpass/avatars/{$row['id']}.jpeg' alt='avatars' height=\"100\" width=\"100\">";
                                        }
                                        ?></td>

                                    <td><?php echo round((($row['scored'] / $result_max_behaviour['criteria']) * 100), 2) ?>
                                        %
                                    </td>
                                    <td>

                                        Result of all assessments

                                        <a href="#">
                                            <i class="zmdi zmdi-plus" onclick="loadDoc()"></i>
                                        </a>



                                        <br><br>
                                        Self Assessment<br><br>

                                        <p id="1"></p>

                                    </td>

                                    <td><?php echo $row['scored'] ?></td>
                                    <td><?php echo $row['appreciation'] ?></td>
                                    <td><?php echo $row['improve'] ?></td>
                                    <!--                                    <td>-->
                                    <?php //echo $row['totaal'] ?><!--</td>-->
                                    <td><?php echo $full_name ?></td>
                                    <td><?php echo $row['role_name'] ?></td>
                                    <td><?php echo $row['department_name'] ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="footer">
        Copyright © 2018 TalentDeveloper
        <div id="google_translate_element"></div>
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    layout: google.translate.TranslateElement.InlineLayout.VERTICAL
                }, 'google_translate_element');
            }
        </script>
        <script type="text/javascript"
                src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    </footer>

</section>


<!-- Javascript Libraries -->
<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendors/bower_components/Waves/dist/waves.min.js"></script>
<script src="vendors/bootstrap-growl/bootstrap-growl.min.js"></script>

<script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
<!-- Placeholder for IE9 -->
<!--[if IE 9 ]>
<script src="vendors/bower_components/jquery-placeholder/jquery.placeholder.min.js"></script>

<![endif]-->
<script src="js/actions.js"></script>
<script src="js/functions.js"></script>


<script>
    function loadDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            document.getElementById("1").innerHTML = this.responseText;
        };
        xhttp.open("GET", "info.php", true);
        xhttp.send();
    }

    $(document).ready(function () {
        $('#<?php echo $person_id ?>').prop('selected', true);
        $('#people-picker').selectpicker('refresh');

        $('#people-picker').on('change', function () {
            var id = $('#people-picker option:selected').attr('id');
            $.ajax({
                type: "POST",
                url: "change-person.php",
                data: "id=" + id,
                success: function () {
                    location.reload();
                }
            });
        });
    });
</script>
</body>
</html>
