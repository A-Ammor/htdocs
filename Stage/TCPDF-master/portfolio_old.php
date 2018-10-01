<?php
//============================================================+
// File name   : example_002.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 002 for TCPDF class
//               Removing Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Removing Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');
require_once('Db.php');
ob_start();

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$servername = "localhost";
$username = "root";
$password = "";
$db = "software_talentpass";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Anwar Ammor');
$pdf->SetTitle('Portfolio');
$pdf->SetSubject('TCPDF with PHP and MYSQL');
$pdf->SetKeywords('TCPDF, PDF, example, portfolio');

function getConnectie()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db = "pretpark";
    try {
        $this->connectie = new PDO('mysql:host=' . $servername . ';dbname=' . $db, $username, $password);
    } catch (PDOException $e) {
        print "Error! Er is een foutje: " . $e->getMessage() . "<br/>";
        die();
    }
    return $this->connectie;
}

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

// Blue square
$pdf->Rect(0, 0, $blueWidth, 300, 'DF', $style2, array(0, 135, 203));

// Text color
$pdf->SetTextColor(255, 255, 255);
$pdf->writeHTML($tbl, true, false, false, false, '');

// Your info
$id = 18;

$initials = 'initials';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$x = 10;
$y = 10;

$sth = $conn->prepare("SELECT $initials, $prefix, $lastName FROM person_cv WHERE id=\"$id\"");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    if (empty($row[$prefix])) {
        $space = "";
    } else {
        $space = " ";
    }
    $count = count($row) / 2;
    $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
}

// role profile (ga wss in een inner join doen)
$sth = $conn->prepare("SELECT name FROM role_profile INNER JOIN period ON role_profile.role_id = period.role_id WHERE id = $id ORDER BY period_date_till LIMIT 1;");
$sth->execute();
$pdf->SetFont('helvetica', '', 10);

/* Fetch all of the remaining rows in the result set */

$result = $sth->fetchAll();
foreach ($result as $row) {
    $pdf->MultiCell(75, 10, '' . ucfirst($row['name']) . "\n", 0, 'J', 0, 0, 10, 15, true, 0, false, true, 20, 'M', true);
}

// Image of yourself
$imageWidth = 75;
$imageHeight = 70;
$pdf->SetXY(110, 200);
$pdf->Image('https://softwareguardian.eu/talentpass/avatars/' . $id . '.jpeg', 0, 30, $imageWidth, $imageHeight, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Info of yourself in the blue side - Change $y to change the icon and text at once
$x = 15;
$y = 170;
$j = 0;
$iconNumber = 1;
$yIcon = $y;

$sth = $conn->prepare("SELECT telephone, email, street_no, date_of_birth, nationality FROM person_cv WHERE id=\"$id\"");
$sth->execute();
/* Fetch all of the remaining rows in the result set */
$result = $sth->fetchAll();
foreach ($result as $row) {
    $count = count($row) / 2;
    for ($i = 0; $i < $count; $i++) {
        // Icons
        $pdf->Image('images/image_' . $iconNumber . '.jpg', 2, $yIcon, 10, 10, 'jpg', '', '', false, 300, '', false, false, 0, false, false, false);
        $iconNumber++;
        $yIcon += 17;
    }
}

$seperator = ', ';
$sth = $conn->prepare("SELECT telephone, email, street_no, zipcode, city, country, date_of_birth, place_of_birth, nationality FROM person_cv WHERE id=\"$id\"");
$sth->execute();
/* Fetch all of the remaining rows in the result set */
$result = $sth->fetchAll();
foreach ($result as $row) {
    if (empty($row['street_no'])) {
        $seperator = "";
    }
    $pdf->MultiCell(59, 3, '' . ucfirst($row['telephone']) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
    $y += 17.5;
    $pdf->MultiCell(59, 3, '' . strtolower($row['email']) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
    $y += 17.5;
    $pdf->MultiCell(59, 3, '' . ucfirst($row['street_no'] . $seperator . $row['zipcode'] . "\n" . $row['city'] . ", " . $row['country'] . "\n"), 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
    $y += 17.5;
    $pdf->MultiCell(59, 3, '' . $row['date_of_birth'] . " \n" . $row['place_of_birth'], 0, 'L', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
    $y += 17.5;
}

$name = 'name';
// drivers license (is een fout met dit want het kan zijn dat er geen driver lincese komt. maar een ander certificaat
$sth = $conn->prepare("SELECT $name FROM certificate INNER JOIN person_certificate ON certificate.certificate_id = person_certificate.certificate_id WHERE id = $id and person_certificate.certificate_id = '8' ORDER BY date_from LIMIT 1;");
$sth->execute();
$result = $sth->fetchAll();
if (empty($result)) {
    $pdf->MultiCell(59, 3, '' . ucfirst('-') . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
} else {
    foreach ($result as $row) {
        $pdf->MultiCell(59, 3, '' . ucfirst($row[$name]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, false, 8, 'M', true);
        $y += 17.5;
    }
}

// Ambition blue side
$sth = $conn->prepare("SELECT remark FROM person_remark WHERE id=\"$id\" LIMIT 1");
$sth->execute();
$txtAmbitie = $sth->fetchAll();
foreach ($txtAmbitie as $row) {
    $pdf->MultiCell(57, 0, 'Mijn Ambitie' . "\n \n" . $row[0] . "\n", 0, 'L', 0, 0, $x, 117, true, 0, false, false, 40, '', true);
    $pdf->Image('images/image_0.jpg', 2, 111.5, 10, 10, '', '', '', false, 300, '', false, false, 0, false, false, false);
}

// --------------------------------------------------

// Font
$pdf->SetTextColor(0, 135, 203);
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
$sth = $conn->prepare("SELECT $name, $sector, $responsibleFor, $function, $locationWork, $reference, $referenceNumber, $dateFrom, $dateTill, $telephone FROM person_work INNER JOIN person_cv ON person_work.id = person_cv.id WHERE person_work.id='$id' ORDER BY date_till IS NULL DESC, date_from DESC LIMIT 6");
$sth->execute();
$textGegevens = $sth->fetchAll();
$x = 10;
$y = 30;
$yBlue = 160;
$j = 0;
$xgegevens = 80;

foreach ($textGegevens as $row) {
    if (empty($row[$reference])) {
        $row[$reference] = "-";
    }
    if (empty($row[$responsibleFor])) {
        $row[$responsibleFor] = "-";
    }
    if (empty($row[$dateTill])) {
        $row[$dateTill] = 'Heden';
    }

    $yChange = 4;
    $pdf->SetFont('helvetica', '', 30);
    $pdf->SetTextColor(0, 147, 214);
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$name]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 7, 'M', true);
    $pdf->MultiCell(44, 3, '' . ucfirst($row[$dateFrom]) . " - " . ucfirst($row[$dateTill]) . "\n", 0, 'L', 0, 0, 80, $y, true, 0, false, false, 7, 'M', true);
    $y += $yChange;
    $y += $yChange;
// dit is als ze de lijn in het midden willen
//    $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 147, 214));
//    $pdf->Line($xgegevens, $yLine, 200, $yLine, $style);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(45, 0, 'Plaats' . "\n" . 'Organisatie-type' . "\n" . "Verantwoordelijkheden" . "\n" . "Functie" . "\n" . "Referentie" . "\n", 0, 'J', 0, 0, 80, $y, true, 0, false, true, 20, 'M', true);
    $pdf->SetTextColor(0, 147, 214);
    $pdf->MultiCell(80, 3, '' . ucfirst($row['location']), 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += $yChange;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$sector]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += $yChange;
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$responsibleFor]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += $yChange;
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$function]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += $yChange;
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$reference]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += $yChange;
    $pdf->MultiCell(80, 3, '' . ucfirst($row[$referenceNumber]) . "\n", 0, 'L', 0, 0, 125, $y, true, 0, false, false, 4, 'M', true);
    $y += 13;
    if (empty($row[$referenceNumber])) {
        $yLine = $y - 10;
    } else {
        $yLine = $y - 7;
    }
    $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 147, 214));
    $pdf->Line($xgegevens, $yLine, 209, $yLine, $style);
}


// Clean any content of the output buffer
ob_end_clean();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_002.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+