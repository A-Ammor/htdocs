<?php
/**
 * Created by PhpStorm.
 * User: Vinci
 * Date: 16-2-2018
 * Time: 17:01
 */
$host = 'localhost';
$user = 'root';
$password = '1234567';
$db = 'school';
$port = "3306";

/* Connect to a MySQL database using driver invocation */
$pdo = 'mysql:dbname=' . $db . ';host=' . $host . '';
$user = $user;
$password = $password;

try {
    $pdo = new PDO($pdo, $user, $password); /*   echo "damn son perfect";*/
} catch (PDOException $e) {/*    echo "damn son u failed" . 'Connection failed: ' . $e->getMessage();*/
}

// onderste is beter require_once ("../TCPDF-master/tcpdf.php");
// Include the main TCPDF library (search for installation path).
require_once("../TCPDF-master/tcpdf_import.php");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Harjit Daroch Lal');
$pdf->SetTitle('Harjit Cv Test');
$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// disable header and footer
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

// ---------------------------------------------------------
// Cell(width, height, text, border,en line, [align])
// add a page
$pdf->AddPage();
//styles
$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));

//Text Color for the Blue part
$pdf->SetTextColor(255, 255, 255);

// Rect

$pdf->Rect(0, 0, 75, 300, 'DF', $style, array(0, 135, 203));


//CV text
$pdf->SetTextColor(0, 135, 203);
$pdf->SetFont('helvetica', 'b', 25);
$pdf->Text(100, 6, "Curriculum Vitae");
$pdf->SetTextColor(255, 255, 255);

//Font voor de Rol
$pdf->SetFont('helvetica', '', 15);

//image
$ID = 19;
//    $pdf->Rect(0, 40, 75, 70, 'DF', $style, array(155, 155, 155));
$pdf->Image("https://softwareguardian.eu/talentpass/avatars/$ID.jpeg", 0, 30, 75, 75, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
// Mijn ambitie
// Title
$pdf->Text(20, 110, "Mijn Ambitie");
// Texts size
$pdf->SetFont('helvetica', '', 12);
// Ambitie
$pdf->MultiCell(50, 50, 'I want to deliver something to this world that did not exist before. Dream it, Code it. Take it to the next level.', 1, 'l', 0, 0, 20, 107, true, 0, false, true, 10, 'M', true);

//IMAGE TEST
//$icons = array("icon1.jpg", "icon2.jpg", "icon3.jpg" ,"icon4.jpg", "icon5.jpg", "icon6.jpg");
//
//foreach ($icons as $icon) {
//    $pdf->Image('icons/new/icon/'.$icon . $x1, $y1, $w1, $h1, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//    $y1 += 20;
//}

//ICONS + TEXT VOOR HET WITTE GEDEELTE............................................................................................................................................
$whitex = 98;
$whiteh = 10;
$whitew = 10;
$textx = 114;

$pdf->SetTextColor(0, 135, 203);

//WERKERVARING
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 18, "Werkervaring(en)");
$pdf->Image('icons/new/icon/blue/icon7.jpg', $whitex, 17, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//Cetificaten
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 105, "Certificaten");
$pdf->Image('icons/new/icon/blue/icon2.jpg', $whitex, 105, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//Opleidingen
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 150, "Opleidingen");
$pdf->Image('icons/new/icon/blue/icon3.jpg', $whitex, 150, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//Vaardigheden
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 195, "Vaardigheden");
$pdf->Image('icons/new/icon/blue/icon4.jpg', $whitex, 195, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//Talen
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 240, "Talen");
$pdf->Image('icons/new/icon/blue/icon5.jpg', $whitex, 240, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);

//Hobbies TEXT
$pdf->SetFont('helvetica', '', 20);
$pdf->Text($textx, 275, "Hobbies");
$pdf->Image('icons/new/icon/blue/icon8.jpg', $whitex, 274, $whitew, $whiteh, 'jpg', '', '', false, 300, '', false, false, 0, false, false);


// ICONEN LOOP VOOR HET Blauwe GEDEELTE///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$x1 = 5;
$y1 = 175;
$w1 = 10;
$h1 = 10;
$pdf->Image('icons/new/icon/blue/icon1.jpg', $x1, 109, $w1, $h1, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
$icon = 2;
for ($o = 0; $o < 5; $o++) {
    $pdf->Image('icons/new/icon/blue/icon' . $icon . '.jpg', $x1, $y1, $w1, $h1, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
    $y1 += 21;
    $icon++;

}

//Kleur text voor de blauwe gedeelte.....................................................................................................................................................

$pdf->SetTextColor(255, 255, 255);

$initials = 'initials';
$lastName = 'last_name';
$prefix = 'prefix';
$space = "";
$rol = "name";


// NAAM LINKS BOVEN/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$x = 10;
$y = 7;
$sth = $pdo->prepare("SELECT $initials, $prefix, $lastName  FROM talentdeveloper.person_cv WHERE id = $ID");
$sth->execute();

$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    if (empty($row[$prefix])) {
        $space = "";
    } else {
        $space = " ";
    }
    //Font voor de naam
    $pdf->SetFont('helvetica', '', 25);
    $count = count($row) / 2;
    $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);

}
//ROL ONDER NAAM LINKS BOVEN/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$count = count($row) / 2;
$sth = $pdo->prepare("SELECT name FROM talentdeveloper.role_profile WHERE role_id=\"54\"");
$sth->execute();
$pdf->SetFont('helvetica', '', 10);
$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    $count = count($row) / 2;
    for ($i = 0; $i < $count; $i++) {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(75, 10, '' . ucfirst($row[$j]) . "\n", 0, 'J', 0, 0, 10, 12, true, 0, false, true, 20, 'M', true);
        $j++;
    }
}
// BLAUWE GEDEELTE INFORMATIE (NUMMER, EMAIL, ADRES, GEBOORTEDATUM)///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$x = 20;
$y = 170;
$sth = $pdo->prepare("SELECT telephone, email, street_no, date_of_birth FROM talentdeveloper.person_cv WHERE id=$ID");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    $count = count($row) / 2;
    for ($i = 0; $i < $count; $i++) {
        $pdf->MultiCell(55, 1, '' . $row[$j] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $j++;
        $y += 20;
    }
}
// BLAUWE GEDEELTE INFORMATIE (postcode en stad)/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$zipcode = "zipcode";
$city = "city";
$x = 20;
$y = 215;
$sth = $pdo->prepare("SELECT $zipcode, $city FROM talentdeveloper.person_cv WHERE id=$ID");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    $count = count($row) / 2;
    $pdf->MultiCell(55, 1, '' . $row[$zipcode] . ", " . $row[$city], 0, 'L', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
    $j++;
    $x += 20;

}
// BLAUWE GEDEELTE INFORMATIE (GEBOORTEPLAATS)//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$x = 20;
$y = 235;
$sth = $pdo->prepare("SELECT place_of_birth FROM talentdeveloper.person_cv where id = $ID");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;
foreach ($result as $row) {
    $count = count($row) / 2;
    for ($i = 0; $i < $count; $i++) {
        $pdf->MultiCell(55, 1, '' . $row[$j] . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
        $j++;
        $x += 20;
    }
}
//// NAAM LINKS BOVEN INITIALEN EN ACHTERNAAM
//$x = 10;
//$y = 7;
//$sth = $dsn->prepare("SELECT $initials, $prefix, $lastName  FROM talentdeveloper.person_cv WHERE id=\"19\"");
//$sth->execute();
//$result = $sth->fetchAll();
//$j = 0;
//foreach ($result as $row) {
//    if (empty($row[$prefix])) {
//        $space = "";
//    } else {
//        $space = " ";
//    }
//    //Font voor de naam
//    $pdf->SetFont('helvetica', 'b', 25);
//    $count = count($row) / 2;
//    $pdf->MultiCell(55, 0, '' . ucfirst($row[$initials]) . " " . $row[$prefix] . $space . ucfirst($row[$lastName]) . "\n", 0, 'J', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//}
//Kleur text voor de witte gedeelte............................................................................................................................................................
$pdf->SetTextColor(0, 0, 0);

// Werkervaring TEXT
$xforloop = 115;
$y1 = 35;
$y2 = 43;
$y3 = 47;
for ($h = 0; $h < 3; $h++) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Text($xforloop, $y1, 'Taken:');
    $pdf->Text($xforloop, $y2, 'Functie:');
    $pdf->Text($xforloop, $y3, 'Referentie:');
    $y1 += 25;
    $y2 += 25;
    $y3 += 25;
}

// Werkervaring data taken, functie, referentie //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$pdf->SetTextColor(0, 0, 0);
$workname = "name";
$responsible = 'responsible_for';
$function = 'function';
$reference = 'reference';

$x = 135;
$y = 35;
$sth = $pdo->prepare("SELECT  $responsible, $function,$reference FROM talentdeveloper.person_work where id= $ID ORDER BY date_from DESC LIMIT 3");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
    //Font voor de naam
    $pdf->SetFont('helvetica', 'b', 20);
    $count = count($row) / 2;
    $pdf->MultiCell(60, 0, '' . ucfirst($row[$responsible]) . "\n" . $row[$function] . "\n" . ucfirst($row[$reference]) . "\n", 0, 'L', 0, 0, $x, $y, true, 0, false, true, 16, 'M', true);
    $y += 25;

}

// Werknaam en datums van de database /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$workname = 'name';
$dateFrom = 'date_format(date_from,"%Y")';
$dateTill = 'date_format(date_till,"%Y")';
$x = 80;
$y = 26;
$ywork = 20;
$sth = $pdo->prepare("SELECT  $workname, $dateFrom, $dateTill FROM talentdeveloper.person_work where id=$ID ORDER BY date_from DESC LIMIT 3");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
    //Font voor de naam
    if ($row[$dateTill] == NULL) {
        $row[$dateTill] = 'Heden';
    }
    $pdf->SetFont('helvetica', 'b', 12);
    $count = count($row) / 2;
    $x = 80;
    $pdf->MultiCell(28, 1, '' . $row[$dateFrom] . ' - ' . $row[$dateTill] . "\n", 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x += 35;
    $pdf->MultiCell(70, 1, '' . $row[$workname] . "\n", 0, 'L', 0, 0, $x, $ywork, true, 0, false, true, 20, 'M', true);
    $ywork += 26;
    $y += 25;

}


//NAAM VAN DE OPLEIDING /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$dateschoolfrom = "date_format(date_from,\"%Y\")";
$dateschooltill = "date_format(date_till,\"%Y\")";
$schoolname = "name";

$x = 80;
$y = 160;
$sth = $pdo->prepare("SELECT $dateschoolfrom ,$dateschooltill , $schoolname FROM talentdeveloper.person_education where id=$ID ORDER BY date_from DESC LIMIT 2");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
    //Font voor de naam
    $pdf->SetFont('helvetica', 'b', 11);
    $count = count($row) / 2;
    $pdf->MultiCell(28, 1, '' . ucfirst($row[$dateschoolfrom]) . "-" . $row[$dateschoolfrom], 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x2 = 115;
    $pdf->MultiCell(50, 0, '' . ucfirst($row[$schoolname]), 0, 'L', 0, 0, $x2, $y, true, 0, false, true, 10, 'M', true);
    $y += 20;
}

//SCHOOL TEXT ONDER OPLEIDING EN OPLEDING NAAM///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$yforschool = 168;
for ($i = 0; $i < 2; $i++) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Text(115, $yforschool, "School");
    $yforschool += 20;
}

//LOCATIE SCHOOL van de data base ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$x = 130;
$y = 160;
$sth = $pdo->prepare("SELECT  location FROM talentdeveloper.person_education where id=$ID  ORDER BY date_from DESC LIMIT 2");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
    //Font voor de naam
    $pdf->SetFont('helvetica', '', 10);
    $count = count($row) / 2;
    $pdf->MultiCell(100, 20, '' . ucfirst($row[$j]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 20, 'M', true);
    $y += 20;
}
//TALEN icons////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$talenhori = 250;
$pdf->Image('icons/new/Talen/blue/icon1.jpg', 115, $talenhori, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
$pdf->Image('icons/new/Talen/blue/icon2.jpg', 125, $talenhori, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
$pdf->Image('icons/new/Talen/blue/icon3.jpg', 135, $talenhori, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
$pdf->Image('icons/new/Talen/blue/icon4.jpg', 145, $talenhori, 5, 5, 'jpg', '', '', false, 300, '', false, false, 0, false, false);
//TALEN ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$language = "language";
$speaking = "speaking";
$writing = "writing";
$reading = "reading";
$understanding = "understanding";

$y = 250;
$sth = $pdo->prepare("SELECT $language,$speaking,$writing,$reading,$understanding FROM talentdeveloper.person_language where id=$ID LIMIT 3");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
//    if ($row[$reading] && $row[$writing]  == NULL ) {
//        $row[$reading] = 'hallo';
//        $row[$writing] = 'hallo';
//    }
    //Font voor de naam
    $pdf->SetFont('helvetica', 'b', 11);
    $count = count($row) / 2;
    $x = 83;
    $y += 05;
    $pdf->MultiCell(25, 0, ($row[$language]), 0, 'R', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x = 115;
    $pdf->MultiCell(100, 0, $row[$speaking] . "    " . ($row[$writing]) . "     " . $row[$reading] . "    " . ($row[$understanding]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
    $x += 20;
}


//HOBBIES VAN DE DATABASE ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$x = 88;
$y = 285;
$sth = $pdo->prepare("SELECT hobby FROM talentdeveloper.person_hobby where id= $ID");
$sth->execute();
$result = $sth->fetchAll();
$j = 0;

foreach ($result as $row) {
    //Font voor de naam
    $pdf->SetFont('helvetica', '', 10);
    $count = count($row) / 2;
    $pdf->MultiCell(35, 0, '' . ucfirst($row[$j]), 0, 'L', 0, 0, $x, $y, true, 0, false, true, 10, 'M', true);
//    $pdf->Text($x,$y,ucfirst($row[$j]) );
    $x += 30;
}
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('PDF-test-HarjitDarochLal', 'I');


//============================================================+
// END OF FILE
//============================================================+