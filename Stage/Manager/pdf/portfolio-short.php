<?php
session_start();

if(!isset($_SESSION['username']) || !isset($_SESSION['department']) || !isset($_SESSION['department_id'])) {
    header("Location: ../login.php");
    exit;
}

$extended = strip_tags($_POST['radio']);
$color_id = strip_tags($_POST['color']);
$id = $_SESSION['person_id'];

$groen = 'green';
$rood = 'red';
$blauw = 'blue';

if($color_id == 0) {
    $color = $rood;
} else if ($color_id == 1) {
    $color = $groen;
} else {
    $color = $blauw;
}

require_once('tcpdf.php');
require_once('../config.php');
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
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

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
$pdf->writeHTML($tbl, true, false, false, false, '');

$initials = 'initials';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 10;
$y = 10;

if ($extended == 0) {
    $sth = $pdo->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
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
        $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    }
} else {
    $sth = $pdo->prepare("SELECT first_name, prefix, last_name FROM person_cv WHERE id = ?");
    $sth->execute(array($id));
    $result = $sth->fetchAll();
    $j = 0;
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
// role profile (ga wss in een inner join doen)
$sth = $pdo->prepare("SELECT name FROM role_profile INNER JOIN period ON role_profile.role_id = period.role_id WHERE id = ? ORDER BY period_date_till LIMIT 1;");
$sth->execute(array($id));
$pdf->SetFont('helvetica', '', 10);

/* Fetch all of the remaining rows in the result set */

$result = $sth->fetchAll();
foreach ($result as $row) {
    $pdf->MultiCell(75, 10, '' . ucfirst($row['name']) . "\n", 0, 'J', 0, 0, 10, 12, true, 0, false, true, 20, 'M', true);
}

// Image of yourself
$imageWidth = 75;
$imageHeight = 75;
$pdf->SetXY(110, 200);
$pdf->Image('https://softwareguardian.eu/talentpass/avatars/' . $id . '.jpeg', 0, 30, $imageWidth, $imageHeight, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Info of yourself in the blue side - Change $y to change the icon and text at once
$x = 15;
$y = 170;
$j = 0;
$iconNumber = 1;

// Ambition blue side
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
/* Fetch all of the remaining rows in the result set */
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
/* Fetch all of the remaining rows in the result set */
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

$pdf->SetFont('helvetica', '', 30);

// Portfolio right side info
$pdf->Text(80, 7, "Mijn Portfolio");
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
$y = 29;
$yBlue = 160;
$j = 0;
$xgegevens = 80;
$boxWidth = 80;
$boxHeight = 4;
$height = 20;

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

    $yChange = 4;
    $pdf->SetFont('helvetica', '', 30);
    //    $pdf->SetTextColor(0, 147, 214); lichte kleur
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
    $pdf->MultiCell(33.3, 3, '' . ucfirst($row[$dateFrom]) . " - " . ucfirst($row[$dateTill]) . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, false, 7, 'M', true);
    $y += $yChange;
    $y += $yChange;
// dit is als ze de lijn in het midden willen
//    $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 147, 214));
//    $pdf->Line($xgegevens, $yLine, 200, $yLine, $style);
    if(strlen($row[$responsibleFor]) > 60){
        $space = "\n \n";
        $height = 24.5;
    } else {
        $space = "\n";
        $height = 20;
    }
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(33, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . $space . "Functie" . "\n" . "Referentie" . "\n", 0, 'R', 0, 0, 80, $y, true, 0, false, true, $height, 'M', true);
    $pdf->SetFont('helvetica', '', 10);

//    $pdf->SetTextColor(0, 147, 214); Lichte kleur
    if ($color == $blauw) {
        $pdf->SetTextColor(0, 135, 203);
    }
    if ($color == $groen) {
        $pdf->SetTextColor(81, 173, 133);
    }
    if ($color == $rood) {
        $pdf->SetTextColor(205, 71, 72);
    }

    $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row['location']), 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
    $y += $yChange;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$sector]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
    $y += $yChange;
    if (strlen($row[$responsibleFor]) < 10) {
        $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$responsibleFor]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
        $y += $yChange;
    } else if (strlen($row[$responsibleFor]) > 60) {
        $boxHeight = 8;
        $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$responsibleFor]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
        $boxHeight = 4;
        $y += 8;
    } else {
        $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$responsibleFor]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
        $y += $yChange;
        $boxHeight = 4;
    }
    $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$function]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$reference]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
    $y += $yChange;
    $pdf->MultiCell($boxWidth, 3, '' . ucfirst($row[$referenceNumber]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, $boxHeight, 'M', true);
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


// Clean any content of the output buffer
ob_end_clean();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('e-portfolio.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+