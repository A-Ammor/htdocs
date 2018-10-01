<?php
session_start();

//if(!isset($_SESSION['username']) || !isset($_SESSION['department']) || !isset($_SESSION['department_id'])) {
//    header("Location: ../login.php");
//    exit;
//}
$groen = 'green';
$rood = 'red';
$blauw = 'blue';

$referenceID = array(19, 1, 20, 18);

$roleID = 70;

$competenceID = array(6, 22, 31, 16, 18);

$extended = 0;
$id = 18;
$color = $groen;

$aantalCompetence = count($competenceID);
$aantal_referenties = count($referenceID);

$X_ass_vantabbel = 142;
$X_ass_vantabbeltext = $X_ass_vantabbel + 1;

//$TabelCompetence[0] = "Coaching individually";
//$TabelCompetence[1] = "Encouraging";
//$TabelCompetence[2] = "initiative";
//$TabelCompetence[3] = "Perseverance";
//$TabelCompetence[4] = "Power to convince";


//$TabelCompetence[0] = "Co-operating";
//$TabelCompetence[1] = "Flexibility";
//$TabelCompetence[2] = "Initiative";
//$TabelCompetence[3] = "Motivation";
//$TabelCompetence[4] = "Self-development by Refl...";

// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');
require_once('db.php');
ob_start();


$stmt = $pdo->prepare('SELECT period_id FROM period WHERE period_date_till IS NULL AND id = ?');
$stmt->execute(array($id));
$result_period = $stmt->fetch();

$period_id = $result_period['period_id'];


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('TalentPass');
$pdf->SetTitle('e-Portfolio');

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
$firstName = 'first_name';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 3.5;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $firstName, $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }

        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$firstName]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
    }
} else {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row['prefix'])) {
            $space = "";
        } else {
            $space = " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
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
$sth = $pdo->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = ? and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 1;");
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
$y = 30 + 5;
$pdf->SetTextColor(0, 0, 0);
$opleiding = $pdo->prepare("SELECT $schoolname,$school,$level,$location, $dateschoolfrom ,$dateschooltill , $schoolname FROM person_education where id=$id ORDER BY date_from DESC LIMIT 2");
$opleiding->execute();
$result = $opleiding->fetchAll();
$j = 0;

if ($opleiding->rowCount() == 1) {
    $pdf->SetFont('helvetica', '', 10);
    $y1 = 35 + 5;
    $y2 = 40 + 5;
    $y3 = 45 + 5;
    foreach ($result as $row) {
        if (empty($row[$schoolname])) {
            $row[$schoolname] = '-';
        }
        if (empty($row[$level])) {
            $row[$level] = '-';
        }
        if (empty($row[$school])) {
            $row[$school] = '-';
        }
        if (empty($row[$location])) {
            $row[$location] = '-';
        }
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
    $y1 = 35 + 5;
    $y2 = 40 + 5;
    $y3 = 45 + 5;
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
} else {
    $pdf->SetFont('helvetica', '', 10);
    $y1 = 35 + 5;
    $y2 = 40 + 5;
    $y3 = 45 + 5;
    foreach ($result as $row) {
        if (empty($row[$schoolname])) {
            $row[$schoolname] = '-';
        }
        if (empty($row[$level])) {
            $row[$level] = '-';
        }
        if (empty($row[$school])) {
            $row[$school] = '-';
        }
        if (empty($row[$location])) {
            $row[$location] = '-';
        }
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
    $yfornivo = 35 + 5;
    $yforschool = 40 + 5;
    $yforlocatie = 45 + 5;
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
$y = 83 + 5;
$ydate = 88 + 5;

if ($opleiding->rowCount() == 1) {
    $y = 60 + 5;
    $ydate = 65 + 5;
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
} else {
    $y = 86 + 5;
    $ydate = 91 + 5;
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
$rows1 = 63 + 5;
$newy = 127 + 5;
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

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 87 + 5;
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
    $y = 87 + 5;
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

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 95 + 5;
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
    $y = 95 + 5;
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

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 102 + 5;
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
    $y = 102 + 5;
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
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 113 + 5;
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
    $y = 113 + 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    $y = 121 + 5;
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
    $y = 121 + 5;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(51, 0, 'Desktop development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Web development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Mobile development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
    $pdf->MultiCell(51, 0, 'Miscellaneous development', 0, 'L', 0, 0, $x1, $y, true, 0, false, true, 10, 'M', true);
    $y += 05;
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {

    $y = 127 + 5;
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
    $y = 127 + 5;
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
    $rows2 = 90 + 5;
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
    $talenY = 103 + 5;
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
    $talenY = 130 + 5;
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
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {

    $talenY = 127 + 5;
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
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {
    $talenY = 136 + 5;
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
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {
    $talenY = 144 + 5;
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

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {
    $talenY = 106 + 5;
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
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {
    $talenY = 97 + 5;
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

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {
    $talenY = 91 + 5;
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

} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {
    $talenY = 130 + 5;
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
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {
    $talenY = 115 + 5;
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
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {
    $talenY = 125 + 5;
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
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 0) {
    $talenY = 68 + 5;
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
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 0) {
    $talenY = 93 + 5;
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
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 147 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 141 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 137 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 130 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 0 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//      opleiding = 2 & 0 certificaat met vaardigheden
//elseif ($opleiding->rowCount() == 2 && $vardigheid=false) {//      opleiding = 2 & 0 certificaat met vaardigheden

    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 173 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 166 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 162 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 158 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 169 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 164 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 158 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 154 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 177 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 173 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 168 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 164 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 186 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 180 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 177 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 173 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 196 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 191 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 186 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 181 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 203 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 198 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 194 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 190;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {

    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 210 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 206 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 201 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 196 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3) {//                                                                 opl = 1 & ceti = 3
    $y = 149 + 5;
    foreach ($result as $row) {
        //Font voor de naam
        $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
        $y += 9;
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {//                                                                 opl =1 & ceti = 2
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 140 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 135 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 130 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 125 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {//                                                                 opl = 1 & ceti = 1
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 134 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 128 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 125 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 119 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && empty($desktopscore)) {//                                                                 opl = 2 & ceti = 3
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 171 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 167 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 164 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 159 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {//                                                             opl = 2 & ceti = 1
    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 158 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 153 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 147 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 144 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && empty($desktopscore)) {//                                                            opl = 2 & ceti = 2

    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 168 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 163 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 158 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 154 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 0) {//

    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 110 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 105 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 100 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 95 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 0) {//

    if ($talen->rowCount() == 4) {//                                                                                       talen 4
        $y = 135 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 3) {//                                                                                   talen 3
        $y = 130 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 2) {//                                                                                   talen 2
        $y = 125 + 5;
        foreach ($result as $row) {
            //Font voor de naam
            $pdf->MultiCell(80, 0, '' . ucfirst($row[$hobby]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
            $y += 9;
        }
    } elseif ($talen->rowCount() == 1) {//                                                                                   talen 1
        $y = 120 + 5;
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

if ($color == $blauw) { /*Blauw*/
    $pdf->SetTextColor(0, 135, 203);
} elseif ($color == $groen)/*Groen*/ {
    $pdf->SetTextColor(81, 173, 133);
} elseif ($color == $rood) {/*Rood*/
    $pdf->SetTextColor(205, 71, 72);
}
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
$pdf->Image('icon/icon_2_' . $color . '.jpg', $whitex, 20 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
////Certificaten
//$pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 80, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 80, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
if ($opleiding->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//                                     opl = 1 & vaardigheden
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 55 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 57 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 92 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 93 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 137 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 138 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 132 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 133 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 128 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 129 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 122 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 123 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

} elseif ($opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {//                                  opl = 2  & vaardigheden
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 119 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 120 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                         talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 164 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 165 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 153 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 154 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 154 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 155 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 149 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 150 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

} elseif ($opleiding->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1 && empty($desktopscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 79 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 80 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 116 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 117 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 161 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 162 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 156 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 157 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 151 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 152 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 146 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 147 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 87 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 88 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 125 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 126 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 170 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 171 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 165 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 166 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 160 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 161 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 94 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 95 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 132 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 133 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 178 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 179 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 173 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 174 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 169 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 170 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 164 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 165 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 105 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 106 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 143 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 144 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 188 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 189 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 183 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 184 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 178 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 179 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 173 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 174 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 113 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 114 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 150 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 151 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 196 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 197 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 191 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 192 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 186 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 187 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 181 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 182 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3 && !empty($desktopscore) && !empty($webscore) && !empty($mobilescore) && !empty($miscellscore)) {
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 83 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 84 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Vaardigheden
    $pdf->MultiCell(80, 0, "Vaardigheden", 0, 'L', 0, 0, $textx, 119 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_3_' . $color . '.jpg', $whitex, 120 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 157 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 158 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 203 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 204 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 198 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 199 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 193 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 194 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 188 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 189 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 3) {//                                                                                                                  opl = 1 & ceti = 3
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 94 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 95 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Hobbies
    $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 140 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 141 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 2) {//                                                                                                            opl = 1 & ceti = 2
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 87 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 88 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 132 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 133 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 127 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 128 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 123 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 124 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }

} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 1) {//                                                                                                        opl = 1 & ceti = 1
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 80 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 81 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 125 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 126 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 120 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 121 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 110 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 111 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 3) {//                                                                                                          opl = 2 & ceti = 3
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 120 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 121 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 163 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 164 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 160 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 161 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 1) {//                                                                                                          opl = 2 & ceti = 1
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 105 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 106 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 144 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 145 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 140 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 141 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 135 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 136 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 2) {//                                                                                                            opl = 2 & ceti = 2
    //Certificaten
    $pdf->MultiCell(80, 0, "Certificaten ", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_1_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 113 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 114 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                           talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 159 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 160 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 150 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 151 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 115 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 116 + 5
            , $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 1 && $cetificaten->rowCount() == 0) {//
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 57 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 58 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                           talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 102 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 103 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 97 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 98 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 92 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 93 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 87 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 88 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
} elseif ($opleiding->rowCount() == 2 && $cetificaten->rowCount() == 0) {//
    //Talen
    $pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 82 + 5, true, 0, false, true, 10, 'M', true);
    $pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 83 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    if ($talen->rowCount() == 4) {//                                                                                                                                          talen 4
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 127 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 128 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 3) {//                                                                                                                                    talen 3
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 122 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 123 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 2) {//                                                                                                                                    talen 2
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 117 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 118 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    } elseif ($talen->rowCount() == 1) {//                                                                                                                                    talen 1
        //Hobbies
        $pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 112 + 5, true, 0, false, true, 10, 'M', true);
        $pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 113 + 5, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    }
}
////Talen
//$pdf->MultiCell(80, 0, "Talen", 0, 'L', 0, 0, $textx, 113, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_4_' . $color . '.jpg', $whitex, 114, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
////Hobbies
//$pdf->MultiCell(80, 0, "Hobbies", 0, 'L', 0, 0, $textx, 155, true, 0, false, true, 10, 'M', true);
//$pdf->Image('icon/icon_5_' . $color . '.jpg', $whitex, 156, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
// -----------------------Einde Code----------------------------------//
$pdf->SetFont('helvetica', '', 20);
// add a page
$pdf->AddPage();
$pdf->Image('images/vlinder' . "_" . $color . '.jpg', 180, 5, 25, 12.5, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);


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
$firstName = 'first_name';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 3.5;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $firstName, $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }

        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$firstName]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
    }
} else {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row['prefix'])) {
            $space = "";
        } else {
            $space = " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
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
$sth = $pdo->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = ? and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 1;");
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
// ------------------------------------------------------------------------- EINDE LINKER KANT --------------------------------------------------------

// Font
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}
$pdf->SetFont('helvetica', '', 20);

// Portfolio right side info
$pdf->MultiCell(35, 0, 'Portfolio' . "\n", 0, 'R', 0, 0, 80, 10, true, 0, false, true, 10, '', true);

//$pdf->Text(80, 7, "Mijn Portfolio");
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);

$name = 'name';
$sector = 'sector';
$responsibleFor = 'responsible_for';
$function = 'function';
$locationWork = 'software_talentpass.person_work.location';
$locationCV = 'software_talentpass.person_cv.location';
$reference = 'reference';
$referenceNumber = 'reference_number';
$dateFrom = 'date_format(date_from,\'%Y\')';
$dateTill = 'date_format(date_till,\'%Y\')';
$telephone = 'telephone';

// you can add LIMIT 4 at the end to show only 4 max.
$sth = $pdo->prepare("SELECT $name, $sector, $responsibleFor, $function, $locationWork, $reference, $referenceNumber, $dateFrom, $dateTill, $telephone FROM person_work INNER JOIN person_cv ON person_work.id = person_cv.id WHERE person_work.id = ? ORDER BY date_till IS NULL DESC, date_from DESC LIMIT 6");
$sth->execute(array($id));
$textGegevens = $sth->fetchAll();
$x = 10;
$y = 30;
$yBlue = 160;
$xgegevens = 80;
$boxWidth = 80;
$boxHeight = 6;
$height = 25;

foreach ($textGegevens as $row) {
    if (empty($row[$reference])) {
        $row[$reference] = "-";
    }
    if (empty($row[$responsibleFor])) {
        $row[$responsibleFor] = "-";
    }
    if (empty($row[$sector])) {
        $row[$sector] = "-";
    }
    if (empty($row[$function])) {
        $row[$function] = "-";
    }
    if (empty($row[$dateTill])) {
        $row[$dateTill] = 'Heden';
    }
    $yChange = 4.1;
    $pdf->SetFont('helvetica', '', 30);
    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$name]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 7, 'M', true);
    $pdf->MultiCell(35, 3, '' . ucfirst($row[$dateFrom]) . " - " . ucfirst($row[$dateTill]) . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, false, 7, 'M', true);
    $y += $yChange;
    $y += $yChange;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $stringResponsibleFor = $row[$responsibleFor];
    if (strlen($stringResponsibleFor) > 87) $stringResponsibleFor = substr($stringResponsibleFor, 0, 87) . "...";

    $stringSector = $row[$sector];
    if (strlen($stringSector) > 87) $stringSector = substr($stringSector, 0, 87) . "...";

    if (strlen($row[$sector]) > 60 && strlen($row[$responsibleFor]) > 60) {
        $space = "\n \n";
        $height = 29.8;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . $space . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else if (strlen($row[$responsibleFor]) > 60) {
        $space = "\n \n";
        $height = 25;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else if (strlen($row[$sector]) > 60) {
        $space = "\n \n";
        $height = 25;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . $space . "Verantwoordelijkheden" . "\n" . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else {
        $space = "\n";
        $height = 21;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    }

    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row['location']), 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->SetTextColor(0, 0, 0);
    if (strlen($row[$sector]) < 10) {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
    } else if (strlen($row[$sector]) > 60) {
        $boxHeight = 10;
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $boxHeight = 6;
        $y += 8.5;
    } else {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
        $boxHeight = 6;
    }
    if (strlen($row[$responsibleFor]) < 10) {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
    } else if (strlen($row[$responsibleFor]) > 60) {
        $boxHeight = 10;
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $boxHeight = 6;
        $y += 8.5;
    } else {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
        $boxHeight = 6;
    }
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$function]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$reference]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$referenceNumber]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += 13;
    if (empty($row[$referenceNumber])) {
        $yLine = $y - 10;
    } else {
        $yLine = $y - 7;
    }
    if ($color == $blauw) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 135, 203));
    }
    if ($color == $groen) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(81, 173, 133));
    }
    if ($color == $rood) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(205, 71, 72));
    }
    $pdf->Line($xgegevens, ($yLine + 2), 205, ($yLine + 2), $style);
}

$blueWidth = 75;
$textCenter = $blueWidth / 2;

// set font
$pdf->SetFont('helvetica', '', 20);

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
$firstName = 'first_name';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 3.5;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $firstName, $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }

        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$firstName]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
    }
} else {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row['prefix'])) {
            $space = "";
        } else {
            $space = " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
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
$sth = $pdo->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = ? and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 1;");
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
// --------------------------------------------------

// Font
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}
$pdf->SetFont('helvetica', '', 20);

// Portfolio right side info
$pdf->MultiCell(35, 0, 'Portfolio' . "\n", 0, 'R', 0, 0, 80, 10, true, 0, false, true, 10, '', true);

//$pdf->Text(80, 10, "Portfolio");
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);

$name = 'name';
$sector = 'sector';
$responsibleFor = 'responsible_for';
$function = 'function';
$locationWork = 'software_talentpass.person_work.location';
$locationCV = 'software_talentpass.person_cv.location';
$reference = 'reference';
$referenceNumber = 'reference_number';
$dateFrom = 'date_format(date_from,\'%Y\')';
$dateTill = 'date_format(date_till,\'%Y\')';
$telephone = 'telephone';

// you can add LIMIT 4 at the end to show only 4 max.
$sth = $pdo->prepare("SELECT $name, $sector, $responsibleFor, $function, $locationWork, $reference, $referenceNumber, $dateFrom, $dateTill, $telephone FROM person_work INNER JOIN person_cv ON person_work.id = person_cv.id WHERE person_work.id = ? ORDER BY date_till IS NULL DESC, date_from DESC LIMIT 1");
$sth->execute(array($id));
$textGegevens = $sth->fetchAll();
$x = 10;
$y = 30;
$yBlue = 160;
$xgegevens = 80;
$boxWidth = 80;
$boxHeight = 6;
$height = 25;

foreach ($textGegevens as $row) {
    if (empty($row[$reference])) {
        $row[$reference] = "-";
    }
    if (empty($row[$responsibleFor])) {
        $row[$responsibleFor] = "-";
    }
    if (empty($row[$sector])) {
        $row[$sector] = "-";
    }
    if (empty($row[$function])) {
        $row[$function] = "-";
    }
    if (empty($row[$dateTill])) {
        $row[$dateTill] = 'Heden';
    }
    $yChange = 4.1;
    $pdf->SetFont('helvetica', '', 30);
    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$name]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 7, 'M', true);
    $pdf->MultiCell(35, 3, '' . ucfirst($row[$dateFrom]) . " - " . ucfirst($row[$dateTill]) . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, false, 7, 'M', true);
    $y += $yChange;
    $y += $yChange;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);

    $stringResponsibleFor = $row[$responsibleFor];
    if (strlen($stringResponsibleFor) > 87) $stringResponsibleFor = substr($stringResponsibleFor, 0, 87) . "...";

    $stringSector = $row[$sector];
    if (strlen($stringSector) > 87) $stringSector = substr($stringSector, 0, 87) . "...";

    if (strlen($row[$sector]) > 60 && strlen($row[$responsibleFor]) > 60) {
        $space = "\n \n";
        $height = 29.8;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . $space . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else if (strlen($row[$responsibleFor]) > 60) {
        $space = "\n \n";
        $height = 25;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else if (strlen($row[$sector]) > 60) {
        $space = "\n \n";
        $height = 25;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . $space . "Verantwoordelijkheden" . "\n" . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    } else {
        $space = "\n";
        $height = 21;
        $pdf->MultiCell(40, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie", 0, 'R', 0, 0, 75, $y, true, 0, false, true, $height, '', true);
    }

    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row['location']), 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->SetTextColor(0, 0, 0);
    if (strlen($row[$sector]) < 10) {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
    } else if (strlen($row[$sector]) > 60) {
        $boxHeight = 10;
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $boxHeight = 6;
        $y += 8.5;
    } else {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringSector) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
        $boxHeight = 6;
    }
    if (strlen($row[$responsibleFor]) < 10) {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
    } else if (strlen($row[$responsibleFor]) > 60) {
        $boxHeight = 10;
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $boxHeight = 6;
        $y += 8.5;
    } else {
        $pdf->MultiCell($boxWidth, 0, '' . ucfirst($stringResponsibleFor) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
        $y += $yChange;
        $boxHeight = 6;
    }
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$function]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$reference]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 0, '' . ucfirst($row[$referenceNumber]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, '', true);
    $y += 10;

    $sth = $pdo->prepare("SELECT period_date_from, period_date_till FROM period WHERE id = ? AND period_id = ? ORDER BY period_date_till IS NULL limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Periode' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
    } else {
        foreach ($txtAmbitie as $row) {
            if ($row['period_date_till'] == NULL) {
                $pdf->MultiCell(40, 0, $row['period_date_from'] . " / " . 'Heden' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 5, '', true);
            } else if (empty($row)) {
                $pdf->MultiCell(40, 0, 'Er is geen datum ingevoerd.' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            } else {
                $pdf->MultiCell(40, 0, $row['period_date_from'] . " / " . $row['period_date_till'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            }
        }
    }
    $y += 9;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);

    $sth = $pdo->prepare("SELECT name FROM role_profile INNER JOIN period ON role_profile.role_id = period.role_id WHERE id = ? AND period_id = ? ORDER BY period_date_till");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Rolprofiel' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['name'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 10;
        }
    }

    $pdf->SetFont('helvetica', '', 10);
    $sth = $pdo->prepare("SELECT distinct name, context FROM person_complexity INNER JOIN complexity ON person_complexity.complexity_id = complexity.complexity_id WHERE id=? AND period_id = ? LIMIT 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Context' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', 'B', 10);
            $stringContext = $row['context'];
            if (strlen($stringContext) > 140) $stringContext = substr($stringContext, 0, 140) . "...";
            $pdf->MultiCell(80, 0, $row['name'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 5, '', true);
            $y += 5;
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $stringContext . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }
    $pdf->SetFont('helvetica', '', 10);

    $sth = $pdo->prepare("SELECT target FROM development_plan_target WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Doelstelling' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['target'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT plan_of_action FROM development_plan_plan_of_action WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Actieplan' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['plan_of_action'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT result FROM development_plan_result WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Resultaten' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 18;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', 'B', 10);

            $pdf->MultiCell(80, 0, $row['result'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT learning_moment FROM development_plan_learning_moment WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Leermomenten' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        if ($color == $blauw) {
            $pdf->SetTextColor(0, 135, 203);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

        }
        if ($color == $groen) {
            $pdf->SetTextColor(81, 173, 133);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

        }
        if ($color == $rood) {
            $pdf->SetTextColor(205, 71, 72);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

        }
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', 'B', 10);
            if ($color == $blauw) {
                $pdf->SetTextColor(0, 135, 203);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

            }
            if ($color == $groen) {
                $pdf->SetTextColor(81, 173, 133);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

            }
            if ($color == $rood) {
                $pdf->SetTextColor(205, 71, 72);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);

            }
            $y += 14;
        }
    }
    $pdf->SetFont('helvetica', '', 10);
    $y += 15;
    if (empty($row[$referenceNumber])) {
        $yLine = $y - 10;
    } else {
        $yLine = $y - 7;
    }

    // de lijn voor een nieuwe regel
    if ($color == $blauw) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 135, 203));
    }
    if ($color == $groen) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(81, 173, 133));
    }
    if ($color == $rood) {
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(205, 71, 72));
    }
}

if ($period_id >= 2) {
    $pdf->Line($xgegevens, $yLine, 209, $yLine, $style);
    $period_id = ($period_id - 1);
    $sth = $pdo->prepare("SELECT period_date_from, period_date_till FROM period WHERE id = ? AND period_id = ? ORDER BY period_date_till IS NULL limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Periode' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
    } else {
        foreach ($txtAmbitie as $row) {
            if ($row['period_date_till'] == NULL) {
                $pdf->MultiCell(40, 0, $row['period_date_from'] . " / " . 'Heden' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 5, '', true);
            } else {
                $pdf->MultiCell(40, 0, $row['period_date_from'] . " / " . $row['period_date_till'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            }
            $y += 9;
        }
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 10);

    $sth = $pdo->prepare("SELECT name FROM role_profile INNER JOIN period ON role_profile.role_id = period.role_id WHERE id = ? AND period_id = ? ORDER BY period_date_till");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Rolprofiel' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['name'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 10;
        }
    }


    $pdf->SetFont('helvetica', '', 10);
    $sth = $pdo->prepare("SELECT distinct name, context FROM person_complexity INNER JOIN complexity ON person_complexity.complexity_id = complexity.complexity_id WHERE id = ? AND period_id = ? LIMIT 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Context' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', 'B', 10);
            $stringContext = $row['context'];
            if (strlen($stringContext) > 140) $stringContext = substr($stringContext, 0, 140) . "...";
            $pdf->MultiCell(80, 0, $row['name'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 5, '', true);
            $y += 5;
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $stringContext . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }
    $pdf->SetFont('helvetica', '', 10);

    $sth = $pdo->prepare("SELECT target FROM development_plan_target WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Doelstelling' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['target'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT plan_of_action FROM development_plan_plan_of_action WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Actieplan' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['plan_of_action'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT result FROM development_plan_result WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Resultaten' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 18;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['result'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }

    $sth = $pdo->prepare("SELECT learning_moment FROM development_plan_learning_moment WHERE id = ? AND period_id = ? limit 1");
    $sth->execute(array($id, $period_id));
    $txtAmbitie = $sth->fetchAll();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(35, 0, 'Leermomenten' . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, 13, '', true);
    if (empty($txtAmbitie)) {
        $pdf->SetFont('helvetica', 'B', 10);
        if ($color == $blauw) {
            $pdf->SetTextColor(0, 135, 203);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        }
        if ($color == $groen) {
            $pdf->SetTextColor(81, 173, 133);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        }
        if ($color == $rood) {
            $pdf->SetTextColor(205, 71, 72);
            $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        }
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', 'B', 10);
            if ($color == $blauw) {
                $pdf->SetTextColor(0, 135, 203);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            }
            if ($color == $groen) {
                $pdf->SetTextColor(81, 173, 133);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            }
            if ($color == $rood) {
                $pdf->SetTextColor(205, 71, 72);
                $pdf->MultiCell(80, 0, 'Zie TalentPass' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            }
            $y += 14;
        }
    }
}


$pdf->SetFont('helvetica', '', 20);

$pdf->Image('images/vlinder' . "_" . $color . '.jpg', 180, 5, 25, 12.5, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);

$pdf->AddPage();

//styles
$style2 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
$style3 = array('width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => array(25, 40, 100));

$y = 95;
// Left side square
if ($color == $blauw) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(0, 135, 203));
    $pdf->Rect(0, 250, $blueWidth, 0.4, 'DF', $style2, array(255, 255, 255));
    $pdf->Rect($blueWidth, 250, 300, 0.4, 'DF', $style2, array(0, 135, 203));

    if ($aantal_referenties == 4) {
        $y = 78;
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(0, 135, 203));
            $y += 57;
        }
    } else {
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(0, 135, 203));
            $y += 75;
        }
    }
}

if ($color == $groen) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(81, 173, 133));
    $pdf->Rect(0, 250, $blueWidth, 0.4, 'DF', $style2, array(255, 255, 255));
    $pdf->Rect($blueWidth, 250, 300, 0.4, 'DF', $style2, array(81, 173, 133));

    if ($aantal_referenties == 4) {
        $y = 78;
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(81, 173, 133));
            $y += 57;
        }
    } else {
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(81, 173, 133));
            $y += 75;
        }
    }
}
if ($color == $rood) {
    $pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(205, 71, 72));
    $pdf->Rect(0, 250, $blueWidth, 0.4, 'DF', $style2, array(255, 255, 255));
    $pdf->Rect($blueWidth, 250, 300, 0.4, 'DF', $style2, array(205, 71, 72));

    if ($aantal_referenties == 4) {
        $y = 78;
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(205, 71, 72));
            $y += 57;
        }
    } else {
        for ($i = 1; $i < $aantal_referenties; $i++) {
            $pdf->Rect($blueWidth, $y, 300, 0.4, 'DF', $style2, array(205, 71, 72));
            $y += 75;
        }
    }
}


// Text color
$pdf->SetTextColor(255, 255, 255);
//$pdf->writeHTML($tbl, true, false, false, false, '');


$initials = 'initials';
$firstName = 'first_name';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 3.5;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $firstName, $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }

        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$firstName]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
    }
} else {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    foreach ($result as $row) {
        if (empty($row[$prefix])) {
            $space = "";
        } else {
            $space = " ";
        }
        $count = count($row) / 2;
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
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

//Info of yourself in the left side - Change $y to change the icon and text at once
$x = 15;
$y = 170;
$j = 0;
$iconNumber = 1;

// Ambition left side
$sth = $pdo->prepare("SELECT ambition FROM development_plan_ambition WHERE id = $id AND $period_id = $period_id LIMIT 1");
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


$sth = $pdo->prepare("SELECT telephone, email, street_no, date_of_birth, nationality FROM person_cv WHERE id = $id");
$sth->execute(array($id));
//Fetch all of the remaining rows in the result set /
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
$sth = $pdo->prepare("SELECT telephone, email, street_no, zipcode, city, country, date_of_birth, place_of_birth, nationality FROM person_cv WHERE id = $id");
$sth->execute(array($id));
//Fetch all of the remaining rows in the result set /
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
// drivers license (is een fout met dit want het kan zijn dat er geen driver lincese komt. maar een ander certificaat
$sth = $pdo->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = $id and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 1;");
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

//---------------------Einde Linkerkant ------------------------------------------//

// Dit print de tekst Referentie
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}
$pdf->SetFont('helvetica', '', 20);
$pdf->Text(85, 10, "Referenties");

$pdf->SetFont('helvetica', '', 10);
$pdf->Text(137, 13, "met hun assessments");

///// dit print het Talentdeveloper logo////
$pdf->Image('images/vlinder' . "_" . $color . '.jpg', 180, 5, 25, 12.5, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);


if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}
$pdf->SetFont('helvetica', '', 10);
$period_id = $period_id + 1;

$initials = 'initials';
$last_name = 'last_name';
$name = 'name';
$telephone = 'telephone';
$email = 'email';
$prefix = 'prefix';


/* This loops the first reference information */
$x = 85;
$y = 54;
$y_referentienaam = 16;
$sth = $pdo->prepare("SELECT DISTINCT initials, last_name, prefix, name, telephone, email FROM role_profile 
INNER JOIN period ON role_profile.role_id = period.role_id 
INNER JOIN person_cv ON person_cv.id = period.id
WHERE person_cv.id = $referenceID[0] ORDER BY period_date_till LIMIT 1;");
$sth->execute();

//--------------- hier word de foto van ref1 geplaatst


$pdf->Image("https://softwareguardian.eu/talentpass/avatars/$referenceID[0].jpeg", 85, 30, 30, 30, '', '', 'T', false, 300, '', false, false, 0, false, false, false);


/*Fetch all of the remaining rows in the result set */
$result = $sth->fetchAll();
$j = 0;
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
    for ($i = 0; $i < 1; $i++) {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(100, 1, '' . $row[$initials] . $row[$prefix] . $space . $row[$lastName] . "\n", 0, 'J', 0, 0, $x, $y_referentienaam, true, 0, false, true, 20, 'M', true);
        $pdf->MultiCell(100, 1, '' . $row[$name] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $y += 5;
        if ($color == $blauw) {
            $pdf->SetTextColor(0, 135, 203);
        }
        if ($color == $groen) {
            $pdf->SetTextColor(81, 173, 133);
        }
        if ($color == $rood) {
            $pdf->SetTextColor(205, 71, 72);
        }
        $pdf->MultiCell(100, 1, '' . $row[$telephone] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $y += 5;
        $pdf->MultiCell(100, 1, '' . $row[$email] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $j++;
        $y += 5;
    }
}

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 10);

//-------- Here ends the first reference--------------------------------------------------------------------------//
if ($aantal_referenties > 1) {
    /* This loops the first reference information */
    if ($aantal_referenties == 4) {
        $x = 85;
        $y = 112;
        $y_referentienaam = 73;
        $y_foto2 = 87;
    } else {
        $x = 85;
        $y = 129;
        $y_foto2 = 105;
        $y_referentienaam = 92;

    }
    $sth = $pdo->prepare("SELECT DISTINCT initials, last_name, name, telephone, email FROM role_profile 
INNER JOIN period ON role_profile.role_id = period.role_id 
INNER JOIN person_cv ON person_cv.id = period.id
WHERE person_cv.id = $referenceID[1] ORDER BY period_date_till LIMIT 1;");
    $sth->execute();

//--------------- hier word de foto van ref1 geplaatst
    $pdf->Image("https://softwareguardian.eu/talentpass/avatars/$referenceID[1].jpeg", $x, $y_foto2, 30, 30, '', '', '', false, 300, '', false, false, 0, false, false);


    /*Fetch all of the remaining rows in the result set */
    $result = $sth->fetchAll();
    $j = 0;
    foreach ($result as $row) {
        $count = count($row) / 2;
        for ($i = 0; $i < 1; $i++) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(100, 1, '' . $row[$initials] . $row[$lastName] . "\n", 0, 'J', 0, 0, $x, $y_referentienaam, true, 0, false, true, 20, 'M', true);
            $pdf->MultiCell(100, 1, '' . $row[$name] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            if ($color == $blauw) {
                $pdf->SetTextColor(0, 135, 203);
            }
            if ($color == $groen) {
                $pdf->SetTextColor(81, 173, 133);
            }
            if ($color == $rood) {
                $pdf->SetTextColor(205, 71, 72);
            }
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$telephone] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$email] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);

            $j++;
            $y += 5;
        }
    }
}


///----------------------------------- here ends referentie 2--------------------------------------///

/* This loops the first reference information */
if ($aantal_referenties == 4) {
    $x = 85;
    $y = 168;
    $y_referentienaam = 130;
    $y_foto3 = 144;
} else {
    $x = 85;
    $y = 204;
    $y_foto3 = 180;
    $y_referentienaam = 167;

}
if ($aantal_referenties > 2) {

    $pdf->Image("https://softwareguardian.eu/talentpass/avatars/$referenceID[2].jpeg", 85, $y_foto3, 30, 30, '', '', '', false, 300, '', false, false, 0, false, false);

    $sth = $pdo->prepare("SELECT DISTINCT initials, last_name, name, telephone, email FROM role_profile 
INNER JOIN period ON role_profile.role_id = period.role_id 
INNER JOIN person_cv ON person_cv.id = period.id
WHERE person_cv.id = $referenceID[2] ORDER BY period_date_till LIMIT 1;");
    $sth->execute();


    /*Fetch all of the remaining rows in the result set */
    $result = $sth->fetchAll();
    $j = 0;
    foreach ($result as $row) {
        $count = count($row) / 2;
        for ($i = 0; $i < 1; $i++) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(100, 1, '' . $row[$initials] . $row[$lastName] . "\n", 0, 'J', 0, 0, $x, $y_referentienaam, true, 0, false, true, 20, 'M', true);
            $pdf->MultiCell(100, 1, '' . $row[$name] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            if ($color == $blauw) {
                $pdf->SetTextColor(0, 135, 203);
            }
            if ($color == $groen) {
                $pdf->SetTextColor(81, 173, 133);
            }
            if ($color == $rood) {
                $pdf->SetTextColor(205, 71, 72);
            }
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$telephone] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$email] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);

            $j++;
            $y += 5;
        }
    }
}

///----------------------------------- here ends referentie 3--------------------------------------///

/* This loops the first reference information */
if ($aantal_referenties == 4) {
    $x = 85;
    $y = 225;
    $y_referentienaam = 187;
    $y_foto4 = 201;


    $pdf->Image("https://softwareguardian.eu/talentpass/avatars/$referenceID[3].jpeg", 85, $y_foto4, 30, 30, '', '', '', false, 300, '', false, false, 0, false, false);

    $sth = $pdo->prepare("SELECT DISTINCT initials, last_name, name, telephone, email FROM role_profile 
INNER JOIN period ON role_profile.role_id = period.role_id 
INNER JOIN person_cv ON person_cv.id = period.id
WHERE person_cv.id = $referenceID[3] ORDER BY period_date_till LIMIT 1;");
    $sth->execute();


    /*Fetch all of the remaining rows in the result set */
    $result = $sth->fetchAll();
    $j = 0;
    foreach ($result as $row) {
        $count = count($row) / 2;
        for ($i = 0; $i < 1; $i++) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(100, 1, '' . $row[$initials] . $row[$lastName] . "\n", 0, 'J', 0, 0, $x, $y_referentienaam, true, 0, false, true, 20, 'M', true);
            $pdf->MultiCell(100, 1, '' . $row[$name] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            if ($color == $blauw) {
                $pdf->SetTextColor(0, 135, 203);
            }
            if ($color == $groen) {
                $pdf->SetTextColor(81, 173, 133);
            }
            if ($color == $rood) {
                $pdf->SetTextColor(205, 71, 72);
            }
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$telephone] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
            $y += 5;
            $pdf->MultiCell(100, 1, '' . $row[$email] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);

            $j++;
            $y += 5;
        }
    }
}
///----------------------------------- here ends referentie 4--------------------------------------///


// this sets the color for the phonenumber and the E-mail
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', '', 10);


$stmt = $pdo->prepare("SELECT c.competence_name, c.id_competence, rc.required_level, cr.id_criteria, f.level FROM competences c
LEFT JOIN competence_criteria_rel cr ON cr.id_competence = c.id_competence
LEFT JOIN feedback_rel f ON cr.id_criteria = f.id_criteria AND id_invitee = ? AND id_person = ? AND period_id = ?
LEFT JOIN role_competencecriteria rc ON cr.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence AND rc.id_role = ?
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY c.competence_name");
$stmt->execute(array($id, $referenceID[0], $period_id, $roleID));
$result_competence = $stmt->fetchAll();
$comp_name = "";
$scores = array();
$average_score = array();
$required_average_score = array();
$required_scores = array();
$comp_names = array();
$count = 0;
$on_level = 0;
$above_level = 0;
$below_level = 0;
$not_scored_count = 0;
$levelsRef1 = array();
foreach ($result_competence as $row) {
    $count++;
    if ($comp_name != $row['competence_name'] && $comp_name != '') {
        $levelsRef1[] = $not_scored_count;
        $levelsRef1[] = $below_level;
        $levelsRef1[] = $on_level;
        $levelsRef1[] = $above_level;
        $on_level = 0;
        $above_level = 0;
        $below_level = 0;
        $not_scored_count = 0;

        $required_scores = array_filter($required_scores);
        $required_average_score[] = array_sum($required_scores) / count($required_scores);
        $scores = array_filter($scores);
        $average_score[] = array_sum($scores) / count($scores);
        $scores = array();
        $required_scores = array();
    }
    $comp_name = $row['competence_name'];
    $comp_names[] = $row['competence_name'];
    if ($row['level'] != null) {
        if ($row['level'] == $row['required_level']) {
            $on_level++;
        } else if ($row['level'] > $row['required_level']) {
            $above_level++;
        } else if ($row['level'] < $row['required_level']) {
            $below_level++;
        }
    } else {
        $not_scored_count++;
    }
    $scores[] = $row['level'];
    $required_scores[] = $row['required_level'];
    if ($count == count($result_competence)) {
        $levelsRef1[] = $not_scored_count;
        $levelsRef1[] = $below_level;
        $levelsRef1[] = $on_level;
        $levelsRef1[] = $above_level;
        $on_level = 0;
        $above_level = 0;
        $below_level = 0;
        $not_scored_count = 0;
        $required_scores = array_filter($required_scores);
        $required_average_score[] = array_sum($required_scores) / count($required_scores);
        $scores = array_filter($scores);
        $average_score[] = array_sum($scores) / count($scores);
    }
}


// referenie 3 table
$y = 30;

for ($i = 0; $i < $aantalCompetence; $i++) {
//        $pdf->Rect(130, $y, 70, 5, 'DF', $style2, array(0, 135, 203));
    $comp_names = array_unique($comp_names);
    $comp_names_format = array_values($comp_names);
    for ($i = 0; $i < count($average_score); $i++) {
        $with1 = (35 / $required_average_score[$i] * $average_score[$i]) + 10;
        $Referentie1_avarage_score[$i] = $average_score[$i];
        $Referentie1_required_score[$i] = $required_average_score[$i];
//        if ($with1 > 75){
//            $with1 = 75;
//        }
        //Tabel 1
        if ($color == $blauw) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
            $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(0, 135, 203));
        }
        if ($color == $groen) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
            $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(81, 173, 133));
        }
        if ($color == $rood) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
            $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(205, 71, 72));
        }
        $y += 6.2;
    }
}
$y = 30;

$sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
$sth->execute(array($id, $period_id));
$tabelCompentences = $sth->fetchAll();
foreach ($tabelCompentences as $tabelCompentence) {
    if (strlen($tabelCompentence['competence_name']) < 20) {
        $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
    } else {
        $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 24) . "...", 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
    }
    $y += 6.4;
}


$y = 30;
$x = 117;
$number = 0;
for ($i = 0; $i < $aantalCompetence; $i++) {
//    $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
    $pdf->MultiCell(5, 0, '' . $levelsRef1[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
    $pdf->MultiCell(5, 0, '' . $levelsRef1[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
    $pdf->MultiCell(5, 0, '' . $levelsRef1[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
    $pdf->MultiCell(5, 0, '' . $levelsRef1[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x = 117;
    $y += 6.2;
    $number++;
}


/////////////////// einde tabel 1 ////////////////////////////////////////////

if ($aantal_referenties > 1) {
    $stmt = $pdo->prepare("SELECT c.competence_name, c.id_competence, rc.required_level, cr.id_criteria, f.level FROM competences c
LEFT JOIN competence_criteria_rel cr ON cr.id_competence = c.id_competence
LEFT JOIN feedback_rel f ON cr.id_criteria = f.id_criteria AND id_invitee = ? AND id_person = ? AND period_id = ?
LEFT JOIN role_competencecriteria rc ON cr.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence AND rc.id_role = ?
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY c.competence_name");
    $stmt->execute(array($id, $referenceID[1], $period_id, $roleID));
    $result_competence = $stmt->fetchAll();
    $comp_name = "";
    $scores = array();
    $average_score = array();
    $required_average_score = array();
    $required_scores = array();
    $comp_names = array();
    $count = 0;
    $on_level = 0;
    $above_level = 0;
    $below_level = 0;
    $not_scored_count = 0;
    $levelsRef2 = array();
    foreach ($result_competence as $row) {
        $count++;
        if ($comp_name != $row['competence_name'] && $comp_name != '') {
            $levelsRef2[] = $not_scored_count;
            $levelsRef2[] = $below_level;
            $levelsRef2[] = $on_level;
            $levelsRef2[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;

            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
            $scores = array();
            $required_scores = array();
        }
        $comp_name = $row['competence_name'];
        $comp_names[] = $row['competence_name'];
        if ($row['level'] != null) {
            if ($row['level'] == $row['required_level']) {
                $on_level++;
            } else if ($row['level'] > $row['required_level']) {
                $above_level++;
            } else if ($row['level'] < $row['required_level']) {
                $below_level++;
            }
        } else {
            $not_scored_count++;
        }
        $scores[] = $row['level'];
        $required_scores[] = $row['required_level'];
        if ($count == count($result_competence)) {
            $levelsRef2[] = $not_scored_count;
            $levelsRef2[] = $below_level;
            $levelsRef2[] = $on_level;
            $levelsRef2[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;
            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
        }
    }

// referenie 2 table
    if ($aantal_referenties == 4) {
        $y = 87;
    } else {
        $y = 105;
    }
    for ($i = 0; $i < $aantalCompetence; $i++) {
        $comp_names = array_unique($comp_names);
        $comp_names_format = array_values($comp_names);
        for ($i = 0; $i < count($average_score); $i++) {
            $with1 = (35 / $required_average_score[$i] * $average_score[$i]) + 10;
            $Referentie2_avarage_score[$i] = $average_score[$i];
            $Referentie2_required_score[$i] = $required_average_score[$i];
//        if ($with1 > 75){
//            $with1 = 75;
//        }
            // tabel 2
            if ($color == $blauw) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(0, 135, 203));
            }
            if ($color == $groen) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(81, 173, 133));
            }
            if ($color == $rood) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(205, 71, 72));
            }
            $y += 6.2;
        }
    }
    if ($aantal_referenties == 4) {
        $y = 87;
    } else {
        $y = 105;
    }
    $sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
    $sth->execute(array($id, $period_id));
    $tabelCompentences = $sth->fetchAll();
    foreach ($tabelCompentences as $tabelCompentence) {
        if (strlen($tabelCompentence['competence_name']) < 20) {
            $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        } else {
            $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 24) . "...", 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        }
        $y += 6.4;
    }

    if ($aantal_referenties == 4) {
        $y = 144;
    } else {
        $y = 180;
    }

    for ($i = 0; $i < $aantalCompetence; $i++) {
        $comp_names = array_unique($comp_names);
        $comp_names_format = array_values($comp_names);
        for ($i = 0; $i < count($average_score); $i++) {
            $with1 = (35 / $required_average_score[$i] * $average_score[$i]) + 10;
            $Referentie2_avarage_score[$i] = $average_score[$i];
            $Referentie2_required_score[$i] = $required_average_score[$i];
//        if ($with1 > 75){
//            $with1 = 75;
//        }
            // tabel 2
            if ($aantal_referenties > 2) {
                if ($color == $blauw) {
                    $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
                    $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(0, 135, 203));
                }
                if ($color == $groen) {
                    $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
                    $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(81, 173, 133));
                }
                if ($color == $rood) {
                    $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
                    $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(205, 71, 72));
                }
                $y += 6.2;
            }
        }
    }


    if ($aantal_referenties == 4) {
        $y = 87;
    } else {
        $y = 105;

    }
    if ($aantal_referenties == 4) {
        $y = 87;
    } else {
        $y = 105;
    }
    $x = 117;
    $number = 0;
    for ($i = 0; $i < $aantalCompetence; $i++) {
        //        $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
        $pdf->MultiCell(5, 0, '' . $levelsRef2[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
        $pdf->MultiCell(5, 0, '' . $levelsRef2[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');

        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
        $pdf->MultiCell(5, 0, '' . $levelsRef2[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');

        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
        $pdf->MultiCell(5, 0, '' . $levelsRef2[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');

        $x = 117;
        $y += 6.2;
        $number++;
    }
}
//////////////////// einde tabel 2//////////////////////////////////////

if ($aantal_referenties > 3) {
    $stmt = $pdo->prepare("SELECT c.competence_name, c.id_competence, rc.required_level, cr.id_criteria, f.level FROM competences c
LEFT JOIN competence_criteria_rel cr ON cr.id_competence = c.id_competence
LEFT JOIN feedback_rel f ON cr.id_criteria = f.id_criteria AND id_invitee = ? AND id_person = ? AND period_id = ?
LEFT JOIN role_competencecriteria rc ON cr.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence AND rc.id_role = ?
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY c.competence_name");
    $stmt->execute(array($id, $referenceID[3], $period_id, $roleID));
    $result_competence = $stmt->fetchAll();
    $comp_name = "";
    $scores = array();
    $average_score = array();
    $required_average_score = array();
    $required_scores = array();
    $comp_names = array();
    $count = 0;
    $on_level = 0;
    $above_level = 0;
    $below_level = 0;
    $not_scored_count = 0;
    $levelsRef4 = array();
    foreach ($result_competence as $row) {
        $count++;
        if ($comp_name != $row['competence_name'] && $comp_name != '') {
            $levelsRef4[] = $not_scored_count;
            $levelsRef4[] = $below_level;
            $levelsRef4[] = $on_level;
            $levelsRef4[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;

            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
            $scores = array();
            $required_scores = array();
        }
        $comp_name = $row['competence_name'];
        $comp_names[] = $row['competence_name'];
        if ($row['level'] != null) {
            if ($row['level'] == $row['required_level']) {
                $on_level++;
            } else if ($row['level'] > $row['required_level']) {
                $above_level++;
            } else if ($row['level'] < $row['required_level']) {
                $below_level++;
            }
        } else {
            $not_scored_count++;
        }
        $scores[] = $row['level'];
        $required_scores[] = $row['required_level'];
        if ($count == count($result_competence)) {
            $levelsRef4[] = $not_scored_count;
            $levelsRef4[] = $below_level;
            $levelsRef4[] = $on_level;
            $levelsRef4[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;
            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
        }
    }

// referenie 3 table


    if ($aantal_referenties == 4) {
        $y = 144;
    } else {
        $y = 180;
    }
    $sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
    $sth->execute(array($id, $period_id));
    $tabelCompentences = $sth->fetchAll();
    foreach ($tabelCompentences as $tabelCompentence) {
        if (strlen($tabelCompentence['competence_name']) < 20) {
            $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        } else {
            $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 24) . "...", 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        }
        $y += 6.4;
    }


    if ($aantal_referenties == 4) {
        $y = 144;
    } else {
        $y = 180;
    }


}


///////////////////////////////////////////////////////Einde tabel 3 ////////////////////////////

if ($aantal_referenties > 2) {
    $stmt = $pdo->prepare("SELECT c.competence_name, c.id_competence, rc.required_level, cr.id_criteria, f.level FROM competences c
LEFT JOIN competence_criteria_rel cr ON cr.id_competence = c.id_competence
LEFT JOIN feedback_rel f ON cr.id_criteria = f.id_criteria AND id_invitee = ? AND id_person = ? AND period_id = ?
LEFT JOIN role_competencecriteria rc ON cr.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence AND rc.id_role = ?
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY c.competence_name");
    $stmt->execute(array($id, $referenceID[2], $period_id, $roleID));
    $result_competence = $stmt->fetchAll();
    $comp_name = "";
    $scores = array();
    $average_score = array();
    $required_average_score = array();
    $required_scores = array();
    $comp_names = array();
    $count = 0;
    $on_level = 0;
    $above_level = 0;
    $below_level = 0;
    $not_scored_count = 0;
    $levelsRef3 = array();
    foreach ($result_competence as $row) {
        $count++;
        if ($comp_name != $row['competence_name'] && $comp_name != '') {
            $levelsRef3[] = $not_scored_count;
            $levelsRef3[] = $below_level;
            $levelsRef3[] = $on_level;
            $levelsRef3[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;

            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
            $scores = array();
            $required_scores = array();
        }
        $comp_name = $row['competence_name'];
        $comp_names[] = $row['competence_name'];
        if ($row['level'] != null) {
            if ($row['level'] == $row['required_level']) {
                $on_level++;
            } else if ($row['level'] > $row['required_level']) {
                $above_level++;
            } else if ($row['level'] < $row['required_level']) {
                $below_level++;
            }
        } else {
            $not_scored_count++;
        }
        $scores[] = $row['level'];
        $required_scores[] = $row['required_level'];
        if ($count == count($result_competence)) {
            $levelsRef3[] = $not_scored_count;
            $levelsRef3[] = $below_level;
            $levelsRef3[] = $on_level;
            $levelsRef3[] = $above_level;
            $on_level = 0;
            $above_level = 0;
            $below_level = 0;
            $not_scored_count = 0;
            $required_scores = array_filter($required_scores);
            $required_average_score[] = array_sum($required_scores) / count($required_scores);
            $scores = array_filter($scores);
            $average_score[] = array_sum($scores) / count($scores);
        }
    }

// referenie 4 table
    if ($aantal_referenties == 4) {
        $y = 201;
    } else {
        $y = 180;
    }
    for ($i = 0; $i < $aantalCompetence; $i++) {
//        $pdf->Rect(130, $y, 70, 5, 'DF', $style2, array(0, 135, 203));
        $comp_names = array_unique($comp_names);
        $comp_names_format = array_values($comp_names);
        for ($i = 0; $i < count($average_score); $i++) {
            $with1 = (35 / $required_average_score[$i] * $average_score[$i]) + 10;
            $Referentie4_avarage_score[$i] = $average_score[$i];
            $Referentie4_required_score[$i] = $required_average_score[$i];
//        if ($with1 > 75){
//            $with1 = 75;
//        }
            if ($color == $blauw) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(0, 135, 203));

            }
            if ($color == $groen) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(81, 173, 133));
            }
            if ($color == $rood) {
                $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
                $pdf->Rect($X_ass_vantabbel, $y, $with1, 5, 'DF', $style2, array(205, 71, 72));
            }
            $y += 6.2;
        }
    }
    if ($aantal_referenties == 4) {
        $y = 201;
    } else {
        $y = 180;
    }
    $sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
    $sth->execute(array($id, $period_id));
    $tabelCompentences = $sth->fetchAll();
    foreach ($tabelCompentences as $tabelCompentence) {
        if (strlen($tabelCompentence['competence_name']) < 20) {
            $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        } else {
            $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 24) . "...", 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
        }
        $y += 6.4;
    }


    if ($aantal_referenties == 4) {
        $y = 201;
    } else {
        $y = 180;
    }

    if ($aantal_referenties == 4) {
        $y = 201;
    } else {
        $y = 180;
    }
    $x = 117;
    $number = 0;
    for ($i = 0; $i < $aantalCompetence; $i++) {
        //        $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
        $pdf->MultiCell(5, 0, '' . $levelsRef4[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
        $pdf->MultiCell(5, 0, '' . $levelsRef4[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
        $pdf->MultiCell(5, 0, '' . $levelsRef4[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
        $pdf->MultiCell(5, 0, '' . $levelsRef4[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x = 117;
        $y += 6.2;
        $number++;
    }
}

if ($aantal_referenties == 4) {
    $y = 144;
} else {
    $y = 180;
}
$number = 0;
$x = 117;
//            $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);
if ($aantal_referenties > 2) {
    for ($i = 0; $i < $aantalCompetence; $i++) {
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
        $pdf->MultiCell(5, 0, '' . $levelsRef3[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
        $pdf->MultiCell(5, 0, '' . $levelsRef3[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
        $pdf->MultiCell(5, 0, '' . $levelsRef3[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x += 6;
        $number++;
        $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
        $pdf->MultiCell(5, 0, '' . $levelsRef3[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
        $x = 117;
        $y += 6.2;
        $number++;
    }
}

///////////////////////////////////////////////////////Einde tabel 4 ////////////////////////////


$pdf->Image("images/vinkje_$color.png", 85, 260, 30, 30, 'png', '', '', false, 300, '', false, false, 0, false, false, false);

$gemiddelde = 0;
$alle_Referenties_bijelkaar = 0;
// referenie 3 table
$y = 260;
for ($i = 0; $i < $aantalCompetence; $i++) {
//        $pdf->Rect(130, $y, 70, 5, 'DF', $style2, array(0, 135, 203));
    $comp_names = array_unique($comp_names);
    $comp_names_format = array_values($comp_names);
    for ($i = 0; $i < count($average_score); $i++) {

        $alle_Referenties_bijelkaar = ($Referentie1_avarage_score[$i] + $Referentie2_avarage_score[$i] + $Referentie3_avarage_score[$i]) / $aantal_referenties;

        $required_score_bijelkaar = ($Referentie1_required_score[$i] + $Referentie2_required_score[$i] + $Referentie3_required_score[$i]) / $aantal_referenties;


        $width = (45 / $required_score_bijelkaar * $alle_Referenties_bijelkaar);
//        if ($with1 > 75){
//            $with1 = 75;
//        }

        if ($color == $blauw) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
            $pdf->Rect($X_ass_vantabbel, $y, $width, 5, 'DF', $style2, array(0, 135, 203));
        }
        if ($color == $groen) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
            $pdf->Rect($X_ass_vantabbel, $y, $width, 5, 'DF', $style2, array(81, 173, 133));
        }
        if ($color == $rood) {
            $pdf->Rect($X_ass_vantabbel, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
            $pdf->Rect($X_ass_vantabbel, $y, $width, 5, 'DF', $style2, array(205, 71, 72));
        }
        $y += 6.2;
    }
}
$y = 260;
$sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
$sth->execute(array($id, $period_id));
$tabelCompentences = $sth->fetchAll();
foreach ($tabelCompentences as $tabelCompentence) {
    if (strlen($tabelCompentence['competence_name']) < 20) {
        $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
    } else {
        $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 24) . "...", 0, 'L', 0, 0, $X_ass_vantabbeltext, $y, true, 0, false, true, 10, '', true);
    }
    $y += 6.4;
}

$y = 260;
for ($i = 0; $i < $aantalCompetence; $i++) {
    $pdf->Text(143, $y, $TabelCompetence[$i]);
    $y += 6.2;
}


//This places the text above the last referentie table
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}
$pdf->SetFont('helvetica', '', 10);
$pdf->Text(136, 255, 'Geconsolideerd');

////////////// this makes the 0 and the 100 % ///////////////
$pdf->SetFont('helvetica', '', 10);
$y = 255;
$y_normlijn = 260;
$pdf->Text(179, $y, 'norm');
$pdf->Rect(187, $y_normlijn, 0.4, 30, 'DF', $style2, array(255, 255, 255));


if ($aantal_referenties == 1) {
    for ($i = 0; $i < count($levelsRef1); $i++) {

        $Geconsolideerd_levels[] = $levelsRef1[$i];
    }
}
if ($aantal_referenties == 2) {
    for ($i = 0; $i < count($levelsRef1); $i++) {
        $Geconsolideerd_levels[] = ($levelsRef1[$i] + $levelsRef2[$i]);
    }
}
if ($aantal_referenties == 3) {
    for ($i = 0; $i < count($levelsRef1); $i++) {
        $Geconsolideerd_levels[] = ($levelsRef1[$i] + $levelsRef2[$i] + $levelsRef3[$i]);
    }
}
if ($aantal_referenties == 4) {
    for ($i = 0; $i < count($levelsRef1); $i++) {
        $Geconsolideerd_levels[] = ($levelsRef1[$i] + $levelsRef2[$i] + $levelsRef3[$i] + $levelsRef4[$i]);
    }
}
//    $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);


$pdf->SetTextColor(255, 255, 255);
$y = 260;
$x = 117;
$xtest = 260;
$ytest = 117;
$number = 0;

for ($i = 0; $i < $aantalCompetence; $i++) {
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
    $pdf->MultiCell(5, 0, '' . $Geconsolideerd_levels[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
    $pdf->MultiCell(5, 0, '' . $Geconsolideerd_levels[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
    $pdf->MultiCell(5, 0, '' . $Geconsolideerd_levels[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
    $pdf->MultiCell(5, 0, '' . $Geconsolideerd_levels[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x = 117;
    $y += 6.2;
    $number++;
}

///////////////////einde Geconsolideerd///////////////////////

$pdf->SetFont('helvetica', '', 10);
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}

$pdf->SetFont('helvetica', '', 10);

if ($aantal_referenties == 4) {
    $y = 25;
    $y_normlijn = 30;
    for ($i = 0; $i < $aantal_referenties; $i++) {
        $pdf->Text(179, $y, 'norm');
        $pdf->Rect(187, $y_normlijn, 0.4, 30, 'DF', $style2, array(255, 255, 255));
        $y += 57;
        $y_normlijn += 57;
    }
} else {
    $y = 25;
    $y_normlijn = 30;
    for ($i = 0; $i < $aantal_referenties; $i++) {
        $pdf->Text(179, $y, 'norm');
        $pdf->Rect(187, $y_normlijn, 0.4, 30, 'DF', $style2, array(255, 255, 255));
        $y += 75;
        $y_normlijn += 75;
    }
}

///////////////////////////////////////////// self refectie table//////////////////////////////////////
$stmt = $pdo->prepare('SELECT c.competence_name, c.id_competence, rc.required_level, f.id_criteria, f.level FROM careerpath_evaluationcriteria f
INNER JOIN competence_criteria_rel cr ON cr.id_criteria = f.id_criteria
INNER JOIN competences c ON cr.id_competence = c.id_competence
INNER JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE id_person = ? AND period_id = ? AND rc.id_role = ?');
$stmt->execute(array(18, $period_id, 62));
$result_competence = $stmt->fetchAll();
$comp_name = "";
$scores = array();
$average_score = array();
$required_average_score = array();
$required_scores = array();
$comp_names = array();
$count = 0;
$on_level = 0;
$above_level = 0;
$below_level = 0;
$not_scored_count = 0;
$levels_zelf_assessment = array();
foreach ($result_competence as $row) {
    $count++;
    if ($comp_name != $row['competence_name'] && $comp_name != '') {
        $levels_zelf_assessment[] = $not_scored_count;
        $levels_zelf_assessment[] = $below_level;
        $levels_zelf_assessment[] = $on_level;
        $levels_zelf_assessment[] = $above_level;
        $on_level = 0;
        $above_level = 0;
        $below_level = 0;
        $not_scored_count = 0;

        $required_scores = array_filter($required_scores);
        $required_average_score[] = array_sum($required_scores) / count($required_scores);
        $scores = array_filter($scores);
        $average_score[] = array_sum($scores) / count($scores);
        $scores = array();
        $required_scores = array();
    }
    $comp_name = $row['competence_name'];
    $comp_names[] = $row['competence_name'];
    $scores[] = $row['level'];
    $required_scores[] = $row['required_level'];
    if ($row['level'] != null) {
        if ($row['level'] == $row['required_level']) {
            $on_level++;
        } else if ($row['level'] > $row['required_level']) {
            $above_level++;
        } else if ($row['level'] < $row['required_level']) {
            $below_level++;
        }
    } else {
        $not_scored_count++;
    }

    if ($count == count($result_competence)) {
        $levels_zelf_assessment[] = $below_level;
        $levels_zelf_assessment[] = $on_level;
        $levels_zelf_assessment[] = $above_level;
        $levels_zelf_assessment[] = $not_scored_count;
        $on_level = 0;
        $above_level = 0;
        $below_level = 0;
        $not_scored_count = 0;

        $required_scores = array_filter($required_scores);
        $required_average_score[] = array_sum($required_scores) / count($required_scores);
        $scores = array_filter($scores);
        $average_score[] = array_sum($scores) / count($scores);
    }
}


//Self refelectie tabel
$y = 260;
$yTabelWhite = 28;
//hierzo
$comp_names = array_unique($comp_names);
$comp_names_format = array_values($comp_names);
for ($i = 0; $i < $aantalCompetence; $i++) {
    $with1 = (35 / $required_average_score[$i] * $average_score[$i]);
    if ($with1 > 50) {
        $with1 = 50;
    }
    if ($color == $blauw) {
        $pdf->Rect($yTabelWhite, $y, 45, 5, 'DF', $style2, array(194, 222, 252));
        $pdf->Rect($yTabelWhite, $y, $with1, 5, 'DF', $style2, array(255, 255, 255));
    }
    if ($color == $groen) {
        $pdf->Rect($yTabelWhite, $y, 45, 5, 'DF', $style2, array(219, 255, 224));
        $pdf->Rect($yTabelWhite, $y, $with1, 5, 'DF', $style2, array(255, 255, 255));
    }
    if ($color == $rood) {
        $pdf->Rect($yTabelWhite, $y, 45, 5, 'DF', $style2, array(247, 185, 185));
        $pdf->Rect($yTabelWhite, $y, $with1, 5, 'DF', $style2, array(255, 255, 255));
    }
    $y += 6.2;
}


$y = 260;


$pdf->SetTextColor(255, 255, 255);
$y = 260;
$x = 3;
$number = 0;
for ($i = 0; $i < $aantalCompetence; $i++) {
    //    $pdf->Text($x+1, $y+0.5, $levelsRef1[$number]);
    //    $pdf->Text($x+1, $y+0.5,$levels_zelf_assessment[$number]);
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(160, 160, 160));
    $pdf->MultiCell(5, 0, '' . $levels_zelf_assessment[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(146, 39, 39));
    $pdf->MultiCell(5, 0, '' . $levels_zelf_assessment[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(34, 155, 102));
    $pdf->MultiCell(5, 0, '' . $levels_zelf_assessment[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x += 6;
    $number++;
    $pdf->Rect($x, $y, 5, 5, 'DF', $style2, array(15, 107, 181));
    $pdf->MultiCell(5, 0, '' . $levels_zelf_assessment[$number], 0, 'C', 0, 0, $x, $y, true, 0, false, true, 5, 'M');
    $x = 3;
    $y += 6.2;
    $number++;
}


////////// einde self refectie tabel////////


//This places the text above the last referentie table
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', '', 10);
$pdf->Text(22, 255, 'Zelf-assessment');

$pdf->SetFont('helvetica', '', 10);
$y = 255;
$y_normlijn = 260;
$pdf->Text(59, $y, 'norm');
if ($color == $blauw) {
    $pdf->Rect(67, $y_normlijn, 0.4, 40, 'DF', $style2, array(0, 135, 203));
}
if ($color == $groen) {
    $pdf->Rect(67, $y_normlijn, 0.4, 40, 'DF', $style2, array(81, 173, 133));
}
if ($color == $rood) {
    $pdf->Rect(67, $y_normlijn, 0.4, 40, 'DF', $style2, array(205, 71, 72));
}

$x = 28;
$y = 260;
if ($color == $blauw) {
    $pdf->SetTextColor(0, 135, 203);
}
if ($color == $groen) {
    $pdf->SetTextColor(81, 173, 133);
}
if ($color == $rood) {
    $pdf->SetTextColor(205, 71, 72);
}

$sth = $pdo->prepare("SELECT Distinct c.competence_name FROM competence_criteria_rel cr
LEFT JOIN careerpath_evaluationcriteria f ON cr.id_criteria = f.id_criteria AND id_person = ? AND period_id = ?
LEFT JOIN competences c ON cr.id_competence = c.id_competence
LEFT JOIN role_competencecriteria rc ON f.id_criteria = rc.id_criteria AND c.id_competence = rc.id_competence
WHERE c.id_competence IN (" . implode(',', $competenceID) . ") ORDER BY competence_name LIMIT 5");
$sth->execute(array($id, $period_id));
$tabelCompentences = $sth->fetchAll();
foreach ($tabelCompentences as $tabelCompentence) {
    if (strlen($tabelCompentence['competence_name']) < 20) {
        $pdf->MultiCell(55, 0, '' . ucfirst($tabelCompentence['competence_name']), 0, 'L', 0, 0, $yTabelWhite + 1, $y, true, 0, false, true, 7, '', true);
    } else {
        $pdf->MultiCell(55, 0, '' . substr(($tabelCompentence['competence_name']), 0, 20) . "...", 0, 'L', 0, 0, $yTabelWhite + 1, $y, true, 0, false, true, 10, '', true);
    }
    $y += 6.4;
}

$pdf->SetTextColor(0, 0, 0);
// Clean any content of the output buffer
ob_end_clean();
// ---------------------------------------------------------

//Close and output PDF document
$sth = $pdo->prepare("SELECT first_name FROM person_cv WHERE id = ? LIMIT 1");
$sth->execute(array($id));
$textGegevens = $sth->fetchAll();
foreach ($textGegevens as $row) {
    $pdf->Output('E-CV-Portfolio-Reference_' . ucfirst($row['first_name']) . '_' . $color . '.pdf', 'I');
}

//============================================================+
// END OF FILE
//============================================================+



