<?php
session_start();

//if(!isset($_SESSION['username']) || !isset($_SESSION['department']) || !isset($_SESSION['department_id'])) {
//    header("Location: ../login.php");
//    exit;
//}
$groen = 'Green';
$rood = 'Red';
$blauw = 'Blue';


$extended = 0;
$id = 2;
$color = $rood;

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
        $pdf->MultiCell(55, 0, '' . ucfirst($row['first_name']) . " " . $row['prefix'] . $space . ucfirst($row['last_name']) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, '', true);
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
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['learning_moment'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
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
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(80, 0, '-' . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
        $y += 14;
    } else {
        foreach ($txtAmbitie as $row) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(80, 0, $row['learning_moment'] . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 13, '', true);
            $y += 14;
        }
    }
}

$pdf->Image('images/vlinder' . "_" . $color . '.jpg', 180, 5, 25, 12.5, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);

$pdf->Image('images/vlinder' . "_" . $color . '.jpg', 180, 5, 25, 12.5, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);

// Clean any content of the output buffer
ob_end_clean();
// ---------------------------------------------------------

//Close and output PDF document
$sth = $pdo->prepare("SELECT first_name FROM person_cv WHERE id = ? LIMIT 1");
$sth->execute(array($id));
$textGegevens = $sth->fetchAll();
foreach ($textGegevens as $row) {
    $pdf->Output('E-Portfolio_Short_' . ucfirst($row['first_name']) . '_' . $color . '.pdf', 'I');
}

//============================================================+
// END OF FILE
//============================================================+