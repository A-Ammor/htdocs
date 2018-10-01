<?php

require_once("../TCPDF-master/tcpdf_import.php");

/* database information */
$host = 'localhost';
$user = 'root';
$password = '1234567';
$db = 'software_talentpass';
$port = "3306";

/* Connect to a MySQL database using driver invocation */
$pdo = 'mysql:dbname=' . $db . ';host=' . $host . '';
$user = $user;
$password = $password;


try {
    $pdo = new PDO($pdo, $user, $password);
    /*  echo "damn son perfect";*/
} catch (PDOException $e) {/*    echo "damn son u failed" . 'Connection failed: ' . $e->getMessage(); */
}
/////////////////////////////////////////////////////////////////////////////////////////////// KLEUR \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$groen = 'green';
$rood = 'red';
$blauw = 'blue';
$color = $blauw;
/////////////////////////////////////////////////////////////////////////////////////////////// ID \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$id = 2;
$extended = 0;

$stmt = $pdo->prepare('SELECT period_id FROM period WHERE period_date_till IS NULL AND id = ?');
$stmt->execute(array($id));
$result_period = $stmt->fetch();

$period_id = $result_period['period_id'];

//$period_id = $result_period['period_id'];

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('TalentPass');
$pdf->SetTitle('CV');


// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setCellPaddings(0, 0, 0, 0);
// ---------------------------------------------------------
$blueWidth = 75;
$textCenter = $blueWidth / 2;

// set font
$pdf->SetFont('helvetica', '', 20);

// add a page
$pdf->AddPage();

//styles
$style2 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
$style3 = array('width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => array(25, 40, 100));

// Left side square
if ($color == $blauw) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(0, 135, 203));
}
if ($color == $groen) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(81, 173, 133));
}
if ($color == $rood) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(205, 71, 72));
}
// Text color
$pdf->SetTextColor(255, 255, 255);

$initials = 'initials';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 3.5;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }
        if (!empty($row[$initials])) {
            $row[$initials] = $row[$initials] . " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
    }
} else {
    $sth = $pdo->prepare("SELECT first_name, prefix, last_name FROM person_cv WHERE id = ?");
    $sth->execute(array($id, $period_id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row['prefix'])) {
            $space = "";
        } else {
            $space = " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row['first_name']) . " " . $row['prefix'] . $space . ucfirst($row['last_name']) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    }
}
$pdf->SetFont('helvetica', '', 10);

// Role profile
$sth = $pdo->prepare("SELECT name FROM role_profile INNER JOIN period ON role_profile.role_id = period.role_id WHERE id = ? ORDER BY period_date_till LIMIT 1");
$sth->execute(array($id));
$result = $sth->fetchAll();
foreach ($result as $row) {
    $pdf->MultiCell(75, 0, '' . ucfirst($row['name']) . "\n", 0, 'L', 0, 0, $x, 19, true, 0, false, true, 0, '', true);
}

// Image of yourself
$imageWidth = 75;
$imageHeight = 75;
$pdf->SetXY(110, 200);
$pdf->Image('https://softwareguardian.eu/talentpass/avatars/' . $id . '.jpeg', 0, 30, $imageWidth, $imageHeight, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

// Info of yourself in the left side - Change $y to change the icon and text at once
$x = 15;
$y = 170;
$iconNumber = 1;

// Ambition left side
$sth = $pdo->prepare("SELECT ambition FROM development_plan_ambition WHERE id = ? AND period_id = ? LIMIT 1");
$sth->execute(array($id, $period_id));
$txtAmbitie = $sth->fetchAll();
$tempy = 114;
$pdf->SetFont('helveticaB', '', 10);
$pdf->MultiCell(57, 0, 'Mijn Ambitie', 0, 'L', 0, 0, $x, $tempy, true, 0, false, false, 40, '', true);
$pdf->Image('images/image_0_' . $color . '.jpg', 2, 111.5, 10, 10, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', '', 10);
if (empty($txtAmbitie)) {
    $tempy += 5;
    $pdf->MultiCell(57, 0, "-", 0, 'L', 0, 0, $x, $tempy, true, 0, false, false, 40, '', true);
    $y = 130;
} else {
    foreach ($txtAmbitie as $row) {
        $tempy += 5;
        if (strlen($row['ambition']) > 0 && strlen($row['ambition']) < 20) {
            $y = 130;
        } else if (strlen($row['ambition']) > 20 && strlen($row['ambition']) < 170) {
            $y = 155;
        } else if (strlen($row['ambition']) >= 170) {
            $y = 170;
        } else {
            $y = 170;
        }
        $pdf->MultiCell(57, 0, $row['ambition'], 0, 'L', 0, 0, $x, $tempy, true, 0, false, false, 40, '', true);
    }
}
$yIcon = $y;
$sth = $pdo->prepare("SELECT telephone, email, street_no, date_of_birth, nationality FROM person_cv WHERE id = ?");
$sth->execute(array($id));
$result = $sth->fetchAll();
foreach ($result as $row) {
    $count = 5;
    for ($i = 0; $i < $count; $i++) {
        // Icons
        $pdf->Image('images/image_' . $iconNumber . "_" . $color . '.jpg', 2, $yIcon, 10, 10, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);
        $iconNumber++;
        $yIcon += 17;
    }
}
$seperator = ', ';

$sth = $pdo->prepare("SELECT telephone, email, street_no, zipcode, city, country, date_of_birth, place_of_birth, nationality FROM person_cv WHERE id = ?");
$sth->execute(array($id));
$result = $sth->fetchAll();
foreach ($result as $row) {
    if (empty($row['street_no'])) {
        $seperator = "";
    }
    if (empty($row['telephone'])) {
        $row['telephone'] = "-";
    }
    if (empty($row['email'])) {
        $row['email'] = "-";
    }
    if (empty($row['date_of_birth'])) {
        $row['date_of_birth'] = "-";
    }
    if (empty($row['place_of_birth'])) {
        $row['place_of_birth'] = "-";
    }
    $pdf->MultiCell(59, 3, '' . ucfirst($row['telephone']) . "\n", 0, 'J', 0, 0, $x, ($y + 1), true, 0, false, false, 8, 'M', true);
    $y += 17.8;
    $pdf->MultiCell(59, 3, '' . strtolower($row['email']) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
    $y += 16;
    if (empty($row['street_no'] && $row['zipcode'] && $row['city'])) {
        $pdf->MultiCell(59, 3, '-' . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
        $y += 17.6;
    } else {
        $pdf->MultiCell(59, 3, '' . ucfirst($row['street_no'] . $seperator . $row['zipcode'] . "\n" . $row['city'] . $seperator . $row['country'] . "\n"), 0, 'J', 0, 0, $x, $y, true, 0, false, false, 10, 'M', true);
        $y += 17.6;
    }
    $pdf->MultiCell(59, 3, '' . $row['date_of_birth'] . " \n" . $row['place_of_birth'], 0, 'L', 0, 0, $x, $y, true, 0, false, false, 10, 'M', true);
    $y += 18;
}
$name = 'name';
// drivers license
$sth = $pdo->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = ? and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 2;");
$sth->execute(array($id));
$result = $sth->fetchAll();
if (empty($result)) {
    $pdf->MultiCell(59, 3, '' . ucfirst('-') . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
} else {
    foreach ($result as $row) {
        $pdf->MultiCell(59, 3, '' . ucfirst($row[$name]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
        $y += 17.5;
    }
}
//------------------Opleding--------------------------------------//
$level = 'level';
$school = 'school';
$location = 'location';
$dateschoolfrom = "date_format(date_from,\"%Y\")";
$dateschooltill = "date_format(date_till,\"%Y\")";
$schoolname = "name";
$xs = 125;
$x1 = 125;
$x = 75;
$y = 30+ 5;
$pdf->SetTextColor(0, 0, 0);
$opleiding = $pdo->prepare("SELECT $schoolname,$school,$level,$location, $dateschoolfrom ,$dateschooltill , $schoolname FROM person_education where id=$id ORDER BY date_from DESC LIMIT 2");
$opleiding->execute();
$result = $opleiding->fetchAll();
$j = 0;
if($opleiding->rowCount() == 1) {
    if (empty($row[$level])) {
        $row[$level] = '-';
    }
    if (empty($row[$school])) {
        $row[$school] = '-';
    }
    if (empty($row[$location])) {
        $row[$location] = '-';
    }
    $pdf->SetFont('helvetica', '', 10);
    $y1 = 35+ 5;
    $y2 = 40+ 5;
    $y3 = 45+ 5;
    foreach ($result as $row) {
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$level]), 0, 'L', 0, 0, $xs, $y1, true, 0, false, true, 20, '', true);
        $y1 += 26;
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$school]), 0, 'L', 0, 0, $xs, $y2, true, 0, false, true, 20, '', true);
        $y2 += 26;
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$location]), 0, 'L', 0, 0, $xs, $y3, true, 0, false, true, 20, '', true);
        $y3 += 26;
        $pdf->SetFont('helvetica', 'b', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 1, '' . ucfirst($row[$dateschoolfrom]) . "-" . $row[$dateschooltill], 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '' . ucfirst(substr(($row[$schoolname]), 0, 40)), 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, '', true);
        $y += 26;
    }
    $xf = 75;
    $y1 = 35+ 5;
    $y2 = 40+ 5;
    $y3 = 45+ 5;
    for ($i = 0; $i < 1; $i++) {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40, 2, 'Niveau', 0, 'R', 0, 0, 75, $y1, true, 0, false, true, 20, '', true);
        $pdf->MultiCell(40, 2, 'School', 0, 'R', 0, 0, 75, $y2, true, 0, false, true, 20, '', true);
        $pdf->MultiCell(40, 2, 'Locatie', 0, 'R', 0, 0, 75, $y3, true, 0, false, true, 20, '', true);
        $y1 += 26;
        $y2 += 26;
        $y3 += 26;
    }
}else{
    if (empty($row[$level])) {
        $row[$level] = '-';
    }
    if (empty($row[$school])) {
        $row[$school] = '-';
    }
    if (empty($row[$location])) {
        $row[$location] = '-';
    }
    $pdf->SetFont('helvetica', '', 10);
    $y1 = 35+ 5;
    $y2 = 40+ 5;
    $y3 = 45+ 5;
    foreach ($result as $row) {
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$level]), 0, 'L', 0, 0, $xs, $y1, true, 0, false, true, 20, '', true);
        $y1 += 26;
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$school]), 0, 'L', 0, 0, $xs, $y2, true, 0, false, true, 20, '', true);
        $y2 += 26;
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$location]), 0, 'L', 0, 0, $xs, $y3, true, 0, false, true, 20, '', true);
        $y3 += 26;
        $pdf->SetFont('helvetica', 'b', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 1, '' . ucfirst($row[$dateschoolfrom]) . "-" . $row[$dateschooltill], 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '' . ucfirst(substr(($row[$schoolname]), 0, 40)), 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, '', true);
        $y += 26;

    }
    $xforloop = 75;
    $yfornivo = 35+ 5;
    $yforschool = 40+ 5;
    $yforlocatie = 45+ 5;
    for ($i = 0; $i < 2; $i++) {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(40, 2, 'Niveau', 0, 'R', 0, 0, $xforloop, $yfornivo, true, 0, false, true, 20, '', true);
        $pdf->MultiCell(40, 2, 'School', 0, 'R', 0, 0, $xforloop, $yforschool, true, 0, false, true, 20, '', true);
        $pdf->MultiCell(40, 2, 'Locatie', 0, 'R', 0, 0, $xforloop, $yforlocatie, true, 0, false, true, 20, '', true);
        $yfornivo += 26;
        $yforschool += 26;
        $yforlocatie += 26;
    }
}


//--------------Einde-Opleding--------------------------------------//
//------------------Certificaten---------------------------------------//
$pdf->SetTextColor(0, 0, 0);
$date_from = "date_from";
$date_till = 'date_till';
$name = 'name';
$cetificaten = $pdo->prepare("SELECT $date_from, $date_till, $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = $id ORDER BY date_till LIMIT 3");
$cetificaten->execute();
$result = $cetificaten->fetchAll();
$y = 83+ 5;
$ydate = 88+ 5;

if($opleiding->rowCount() == 1) {
    $y = 60+ 5;
    $ydate = 65+ 5;
    foreach ($result as $row) {
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $pdf->MultiCell(40, 1, '' . $row[$date_from] . "\n", 0, 'R', 0, 0, $x, $ydate, true, 0, false, true, 10, 'M', true);
        $x += 50;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(70, 1, '' . $row[$name] . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $ydate += 07;
        $y += 07;
    }
}else{
    $y = 86+ 5;
    $ydate = 91+ 5;
    foreach ($result as $row) {
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $pdf->MultiCell(40, 1, '' . $row[$date_from] . "\n", 0, 'R', 0, 0, $x, $ydate, true, 0, false, true, 10, 'M', true);
        $x += 50;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(70, 1, '' . $row[$name] . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $ydate += 07;
        $y += 07;
    }

}
//-----------------Einde-Certificaten---------------------------------------//
//
//if ($color == $blauw) {
//    $pdf->SetTextColor(0, 135, 203);
//}
//if ($color == $groen) {
//    $pdf->SetTextColor(81, 173, 133);
//}
//if ($color == $rood) {
//    $pdf->SetTextColor(205, 71, 72);
//}
//$pdf->SetFont('helvetica', '', 15);
//if ($cetificaten->rowCount() > 0) {//                                                                                   Wel Cetificaten
//    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 19, true, 0, false, true, 10, 'M', true);
//    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 20, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//} else {//                                                                                                              Geen Cetificaten
//    $pdf->MultiCell(80, 0, "Opleidingen", 0, 'L', 0, 0, $textx, 19, true, 0, false, true, 10, 'M', true);
//    $pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 20, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//}
//if ($color == $blauw) {
//    $pdf->SetTextColor(0, 135, 203);
//}
//if ($color == $groen) {
//    $pdf->SetTextColor(81, 173, 133);
//}
//if ($color == $rood) {
//    $pdf->SetTextColor(205, 71, 72);
//}
//$pdf->SetFont('helvetica', '', 15);
//if ($cetificaten->rowCount() == 0) {//                                                                                  0 cetificaten
//    $pdf->MultiCell(80, 0, "Opleidingen", 0, 'L', 0, 0, $textx, 45, true, 0, false, true, 10, 'M', true);
//    $pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 46, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//}elseif ($cetificaten->rowCount() == 1) {//                                                                             1 cetificaten
//    $pdf->MultiCell(80, 0, "Opleidingen", 0, 'L', 0, 0, $textx, 65, true, 0, false, true, 10, 'M', true);
//    $pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 66, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//}elseif ($cetificaten->rowCount() == 2) {//                                                                             2 cetificaten
//    $pdf->MultiCell(80, 0, "Opleidingen", 0, 'L', 0, 0, $textx, 85, true, 0, false, true, 10, 'M', true);
//    $pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 86, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//}

//-----------------Vaardigheden------------------------------------//

$idc = 'id_criteria';
$sth = $pdo->prepare("SELECT DISTINCT $idc FROM competences c INNER JOIN role_competencecriteria rc ON rc.id_competence = c.id_competence WHERE rc.id_role = 61 and competence_name = 'Development skills';");
$sth->execute();
$result = $sth->fetchAll();
$list = array();
foreach ($result as $row) {
    array_push($list, $row[$idc]);
}
$desktop = array();
$web = array();
$mobile = array();
$miscell = array();
$ratings = array();

//peer-assessment

$level = 'level';
for ($i = 0; $i < count($list); $i++) {
    $sth = $pdo->prepare("SELECT $level FROM feedback_rel c
INNER JOIN competence_criteria cc ON cc.id_criteria = c.id_criteria
INNER JOIN period o ON o.id = c.id_invitee AND o.period_id = c.period_id
WHERE c.id_criteria = $list[$i] AND c.id_invitee = $id AND o.period_id = $period_id");
    $sth->execute();
    $result = $sth->fetchall();
    foreach ($result as $row) {
        array_push($ratings, $row[$level]);
    }
}

if (empty($ratings[0])) {

} else {
    $desktop[] = $ratings[0];
    $web[] = $ratings[1];
    $mobile[] = $ratings[2];
    $miscell[] = $ratings[3];

    unset($ratings);
    $ratings = array();
}

//manager-assessment

for ($i = 0; $i < count($list); $i++) {
    $sth = $pdo->prepare("SELECT $level FROM feedback_mng_rel c
INNER JOIN competence_criteria cc ON cc.id_criteria = c.id_criteria
INNER JOIN period o ON o.id = c.id_invitee AND o.period_id = c.period_id
WHERE c.id_criteria = $list[$i] AND c.id_invitee = $id AND o.period_id = $period_id");
    $sth->execute();
    $result = $sth->fetchall();
    foreach ($result as $row) {
        array_push($ratings, $row[$level]);
    }
}
//if(!empty($ratings[0])) {
//    $desktop[] = $ratings[0];
//    $web[] = $ratings[1];
//    $mobile[] = $ratings[2];
//    $miscell[] = $ratings[3];
//
//    unset($ratings);
//    $ratings = array();
//}
if (empty($ratings[0])) {

} else {
    $desktop[] = $ratings[0];
    $web[] = $ratings[1];
    $mobile[] = $ratings[2];
    $miscell[] = $ratings[3];

    unset($ratings);
    $ratings = array();
}


//peer-assessment

for ($i = 0; $i < count($list); $i++) {
    $sth = $pdo->prepare("SELECT $level  FROM careerpath_evaluationcriteria c
INNER JOIN competence_criteria cc ON cc.id_criteria = c.id_criteria
INNER JOIN period o ON o.id = c.id_person AND o.period_id = c.period_id
WHERE c.id_criteria = $list[$i] AND c.id_person = $id AND o.period_id = $period_id");
    $sth->execute();
    $result = $sth->fetchall();
    foreach ($result as $row) {
        array_push($ratings, $row[$level]);
    }
}
if (empty($ratings[0])) {

} else {
    $desktop[] = $ratings[0];
    $web[] = $ratings[1];
    $mobile[] = $ratings[2];
    $miscell[] = $ratings[3];

//      zodra guest werk dit uitcommenten {
//    unset($ratings);
//    $ratings = array();
//
}


//guest-assessment////////////////////////////////////////////////////nog geen ratings hier vandaar uitgecomment
//for($i = 0; $i <$list; $i++){
//    $sth = $dsn->prepare("SELECT $level FROM feedback_guest_rel c
//                                    INNER JOIN competence_criteria cc ON cc.id_criteria = c.id_criteria
//                                    INNER JOIN period o ON o.id = c.id_invitee AND o.period_id = c.period_id
//                                    WHERE c.id_criteria = $list[$i] AND c.id_invitee = $id AND o.period_id = $periodid");
//    $sth->execute();
//    $result = $sth->fetchall();
//    foreach ($result as $row) {
//        array_push($ratings, $row[$level]);
//
//    }
//
//}
//if(empty($ratings[0])) {
//} else{
//    $desktop[] = $ratings[0];
//    $web[] = $ratings[1];
//    $mobile[] = $ratings[2];
//    $miscell[] = $ratings[3];
//

//}

if (empty($ratings[0])) {

} else {
    $desktop_score[] = array_sum($desktop) / count($desktop);
    $web_score[] = array_sum($web) / count($web);
    $mobile_score[] = array_sum($mobile) / count($mobile);
    $miscell_score[] = array_sum($miscell) / count($miscell);

    $desktopscore = floor($desktop_score[0]);
    $webscore = floor($web_score[0]);
    $mobilescore = floor($mobile_score[0]);
    $miscellscore = floor($miscell_score[0]);
}

$level = array();

$levelid = 'id_level';
$levelname = 'level_name';
$sth = $pdo->prepare("SELECT $levelid,$levelname  FROM software_talentpass.config_levels;");
$sth->execute();
$result = $sth->fetchall();
foreach ($result as $row) {
    array_push($level, $row[$levelname]);
}
if (empty($ratings[0])) {

} else {
//desktop
    $g = 0;
    for ($a = 0; $a < 9; $a++) {
        if ($desktopscore == $a) {
            $desktopscore = $level[$g];
        }
        $g++;
    }
//Web
    $g = 0;
    for ($a = 0; $a < 9; $a++) {
        if ($webscore == $a) {
            $webscore = $level[$g];
        }
        $g++;
    }
//Mobile
    $g = 0;
    for ($a = 0; $a < 9; $a++) {
        if ($mobilescore == $a) {
            $mobilescore = $level[$g];
        }
        $g++;
    }
//Miscell
    $g = 0;
    for ($a = 0; $a < 9; $a++) {
        if ($miscellscore == $a) {
            $miscellscore = $level[$g];
        }
        $g++;
    }
}

$x = 75;
$x1 = 125;
$rows1 = 63+ 5;
$newy = 127+ 5;
if ($opleiding->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = $rows1;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    ///Tekst
    $y = $rows1;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;

}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 87+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 87+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $vardigheid = true;

}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 95+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 95+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $vardigheid = true;

}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 &&  !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 102+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 102+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $vardigheid = true;
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 113+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 113+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 121+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 121+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {

    $y = 127+ 5;
    $pdf->SetFont('helvetica', 'B', 10);
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x1 = 125;
    $y = 127+ 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $vardigheid = true;

//}elseif (empty($desktopscore)) {
//
//    $y = $newy;
//    $pdf->SetFont('helvetica', 'B', 10);
//// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
//    $pdf->MultiCell(40, 0, "-", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//    $y += 5;
//    $pdf->MultiCell(40, 0, "-", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//    $y += 5;
//    $pdf->MultiCell(40, 0, "-", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//    $y += 5;
//    $pdf->MultiCell(40, 0, "-", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//    $x1 = 125;
//    $y = $newy;
//    $pdf->SetFont('helvetica', '', 10);
//    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
//    $y += 05;
//    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
//    $y += 05;
//    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
//    $y += 05;
//    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
//    $y += 05;
//    $vardigheid = true;

} elseif ($opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $rows2 = 90+ 5;
    $y = $rows2;
    //Scores
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(40, 0, $desktopscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $webscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $mobilescore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $y += 5;
    $pdf->MultiCell(40, 0, $miscellscore, 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    ///Tekst
    $y = $rows2;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
} else {

}

//-----------------Einde-Vaardigheden-------------------------------------//

$language = "language";
$speaking = "speaking";
$writing = "writing";
$reading = "reading";
$understanding = "understanding";
$seperator = '    ';
$streepje = '-';
$seperator1 = '----';
$x = 75;
$talen = $pdo->prepare("SELECT $language,$speaking,$writing,$reading,$understanding FROM software_talentpass.person_language where id=$id LIMIT 4");
$talen->execute();
$result = $talen->fetchAll();
$j = 0;
if ($opleiding->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore) && $cetificaten->rowCount() == 0) {                   // 1 opleiding zonder Certificaten met vaarddigheden
    $talenY = 103+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
} elseif ($opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore) && $cetificaten->rowCount() == 0) {                   // 2 opleiding zonder Certificaten met vaarddigheden
    $talenY = 130+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {

    $talenY = 127+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {
    $talenY = 136+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {
    $talenY = 144+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
//}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {
//    $talenY = 115;
//    $talenX = 125;
//    for ($count = 0; $count < 4; $count++) {
//        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//        $talenX += 10;
//    }
//    $y = $talenY;
//    foreach ($result as $row) {
//        $pdf->SetTextColor(0, 0, 0);
//        if ($row[$reading] && $row[$writing] == $streepje) {
//            $row[$reading] = $seperator1;
//            $row[$writing] = $seperator1;
//        }
//        //Font voor de naam
//        $pdf->SetFont('helvetica', 'b', 10);
//        $count = count($row) / 2;
//        $x = 75;
//        $y += 05;
//        if (strlen($row[$language]) < 20) {
//            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        } else {
//            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        }
//        $x = 125;
//        $pdf->SetFont('helvetica', '', 10);
//        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        $x += 20;
//
//    }
//}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {
//    $talenY = 162;
//    $talenX = 125;
//    for ($count = 0; $count < 4; $count++) {
//        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//        $talenX += 10;
//    }
//    $y = $talenY;
//    foreach ($result as $row) {
//        $pdf->SetTextColor(0, 0, 0);
//        if ($row[$reading] && $row[$writing] == $streepje) {
//            $row[$reading] = $seperator1;
//            $row[$writing] = $seperator1;
//        }
//        //Font voor de naam
//        $pdf->SetFont('helvetica', 'b', 10);
//        $count = count($row) / 2;
//        $x = 75;
//        $y += 05;
//        if (strlen($row[$language]) < 20) {
//            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        } else {
//            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        }
//        $x = 125;
//        $pdf->SetFont('helvetica', '', 10);
//        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        $x += 20;
//
//    }
//
//}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {
//
//    $talenY = 169;
//    $talenX = 125;
//    for ($count = 0; $count < 4; $count++) {
//        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//        $talenX += 10;
//    }
//    $y = $talenY;
//    foreach ($result as $row) {
//        $pdf->SetTextColor(0, 0, 0);
//        if ($row[$reading] && $row[$writing] == $streepje) {
//            $row[$reading] = $seperator1;
//            $row[$writing] = $seperator1;
//        }
//        //Font voor de naam
//        $pdf->SetFont('helvetica', 'b', 10);
//        $count = count($row) / 2;
//        $x = 75;
//        $y += 05;
//        if (strlen($row[$language]) < 20) {
//            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        } else {
//            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        }
//        $x = 125;
//        $pdf->SetFont('helvetica', '', 10);
//        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//        $x += 20;
//
//    }

} elseif($opleiding->rowCount() == 1 && $cetificaten->rowCount()==3 && empty($desktopscore)) {
    $talenY = 106+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_'.$count.'_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX+=10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==2 && empty($desktopscore)){
    $talenY = 97+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_'.$count.'_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX+=10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==1 && empty($desktopscore)){
    $talenY = 91+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_'.$count.'_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX+=10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }

} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==3 && empty($desktopscore)){
    $talenY = 130+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_'.$count.'_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX+=10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==1 && empty($desktopscore)){
    $talenY = 115+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_'.$count.'_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX+=10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==2 && empty($desktopscore)) {
    $talenY = 125+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()== 0 ) {
    $talenY = 68+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()== 0 ) {
    $talenY = 93+ 5;
    $talenX = 125;
    for ($count = 0; $count < 4; $count++) {
        $pdf->Image('talen/icon_' . $count . '_' . $color . '.jpg', $talenX, $talenY, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
        $talenX += 10;
    }
    $y = $talenY;
    foreach ($result as $row) {
        $pdf->SetTextColor(0, 0, 0);
        if ($row[$reading] && $row[$writing] == $streepje) {
            $row[$reading] = $seperator1;
            $row[$writing] = $seperator1;
        }
        //Font voor de naam
        $pdf->SetFont('helvetica', 'b', 10);
        $count = count($row) / 2;
        $x = 75;
        $y += 05;
        if (strlen($row[$language]) < 20) {
            $pdf->MultiCell(40, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        } else {
            $pdf->MultiCell(40, 0, substr(($row[$language]), 0, 17) . "...)", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        }
        $x = 125;
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $row[$speaking] . "     " . ($row[$writing]) . "      " . $row[$reading] . "      " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $x += 20;

    }
}


$pdf->SetTextColor(0, 0, 0);
$x = 125;
$hobby = "hobby";
$hobbies = $pdo->prepare("SELECT $hobby FROM software_talentpass.person_hobby where id= $id LIMIT 3");
$hobbies->execute();
$result = $hobbies->fetchAll();
$pdf->SetFont('helvetica', '', 10);
if ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 0 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//             opleiding = 1 & 0 certificaat met vaardigheden
//if ($opleiding->rowCount() == 2 && $vardigheid=false) {//      opleiding = 2 & 0 certificaat met vaardigheden
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 147+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 141+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 137+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 130+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 0 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//      opleiding = 2 & 0 certificaat met vaardigheden
//elseif ($opleiding->rowCount() == 2 && $vardigheid=false) {//      opleiding = 2 & 0 certificaat met vaardigheden

    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 173+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 166+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 162+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 158+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }

}
elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 169+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 164+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 158+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 154+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 177+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 173+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 168+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 164+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 186+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 180+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 177+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 173+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 196+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 191+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 186+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 181+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 203+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 198+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 194+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 190;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {

    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 210+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 206+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 201+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 196+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }

}elseif($opleiding->rowCount() == 1 && $cetificaten->rowCount()==3) {//                                                                 opl = 1 & ceti = 3
    $y = 149+ 5;
    foreach ($result as $row) {
        //Font voor de naam
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $y += 9;
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==2 && empty($desktopscore)){//                                                                 opl =1 & ceti = 2
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 140+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 135+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 130+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 125+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==1 && empty($desktopscore)){//                                                                 opl = 1 & ceti = 1
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 134+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 128+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 125+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 119+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==3 && empty($desktopscore)){//                                                                 opl = 2 & ceti = 3
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 171+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 167+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 164+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 159+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==1 && empty($desktopscore)){//                                                             opl = 2 & ceti = 1
    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 158+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 153+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 147+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 144+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==2 && empty($desktopscore)){//                                                            opl = 2 & ceti = 2

    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 168+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 163+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 158+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 154+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()== 0 ){//

    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 110+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 105+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 100+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 95+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()== 0 ){//

    if($talen->rowCount()==4) {//                                                                                       talen 4
        $y = 135+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==3) {//                                                                                   talen 3
        $y = 130+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==2) {//                                                                                   talen 2
        $y = 125+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
    elseif($talen->rowCount()==1) {//                                                                                   talen 1
        $y = 120+ 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
}
//--------------------------------------------------------------------------------------------------------------------text----------------------------------------------------------------------------

$pdf->Image('logo/vlinder_new_' . $color . '.jpg', 175, 03, 30, 15, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//$pdf->Image('check/check_goed_2.png', 170, 111, 4, 4, 'png', '', '', false, 300, '', false, false, 0, false, false);
//$pdf->Image('check/check_goed_2.png', 170, 118, 4, 4, 'png', '', '', false, 300, '', false, false, 0, false, false);

if ($color == $blauw) { /*Blauw*/ $pdf->SetTextColor(0, 135, 203);} elseif ($color == $groen)/*Groen*/  { $pdf->SetTextColor(81, 173, 133); } elseif ($color == $rood) {/*Rood*/ $pdf->SetTextColor(205, 71, 72); }
$whitex = 108;
$whiteh = 7;
$whitew = 7;
$textx = 125;
$pdf->SetFont('helvetica', '', 20);
$pdf->MultiCell(80, 0, "Curriculum Vitae", 0, 'L', 0, 0, 90, 10, true, 0, false, true, 10, 'M', true);
//--------------------Einde-CV-Tekst------------------------------------------//
$pdf->SetFont('helvetica', '', 15);
//Opleidingen
$pdf->MultiCell(80, 0, "Opleidingen", 0, 'L', 0, 0, $textx, 19 + 5, true, 0, false, true, 10, 'M', true);
$pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 20+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
////Certificaten
//$pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 80, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 80, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
if( $opleiding->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//                                     opl = 1 & vaardigheden
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 55+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 57+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 92+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 93+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 137+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 138+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 132+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 133+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 128+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 129+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 122+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 123+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

}elseif( $opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//                                  opl = 2  & vaardigheden
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 119+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 120+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                         talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 164+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 165+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 153+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 154+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 154+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 155+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 149+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 150+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

}elseif ( $opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)){
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 79+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 80+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 116+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 117+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 161+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 162+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 156+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 157+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 151+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 152+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 146+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 147+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 87+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 88+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 125+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 126+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 170+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 171+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 165+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 166+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 160+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 161+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 94+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 95+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 132+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 133+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 178+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 179+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 173+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 174+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 169+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 170+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 164+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 165+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 105+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 106+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 143+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 144+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 188+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 189+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 183+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 184+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 178+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 179+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 173+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 174+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 113+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 114+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 150+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 151+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 196+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 197+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 191+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 192+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 186+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 187+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 181+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 182+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 83+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 84+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 119+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 120+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 157+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 158+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 203+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 204+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 198+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 199+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 193+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 194+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 188+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 189+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

}elseif($opleiding->rowCount() == 1 && $cetificaten->rowCount()==3) {//                                                                                                                  opl = 1 & ceti = 3
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 94+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 95+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Hobbies
    $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 140+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 141+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==2) {//                                                                                                            opl = 1 & ceti = 2
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 87+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 88+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 132+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 133+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 127+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 128+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 123+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 124+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()==1) {//                                                                                                        opl = 1 & ceti = 1
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 80+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 81+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 125+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 126+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 120+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 121+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 110+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 111+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==3){//                                                                                                          opl = 2 & ceti = 3
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 120+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 121+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 163+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 164+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 160+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 161+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==1){//                                                                                                          opl = 2 & ceti = 1
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 105+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 106+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 144+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 145+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 140+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 141+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 135+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 136+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()==2) {//                                                                                                            opl = 2 & ceti = 2
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 113+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 114+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                           talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 159+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 160+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 115+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 116+ 5
            , $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount()== 0 ) {//
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 57+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 58+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                           talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 102+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex,103+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 97+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 98+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 92+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 93+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 87+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 88+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
    }
elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount()== 0 ) {//
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 82+ 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 83+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if($talen->rowCount()==4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 127+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 128+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 122+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 123+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }elseif($talen->rowCount()==1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 112+ 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 113+ 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}
////Talen
//$pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 113, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 114, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
////Hobbies
//$pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
// -----------------------Einde Code----------------------------------//
//Close and output PDF document
$pdf->Output('PDF-test-HarjitDarochLal', 'I');
//============================================================+
// END OF FILE
//============================================================+