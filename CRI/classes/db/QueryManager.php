<?php
require_once("Connect.php");

class QueryManager {

	private $dbconn;
    
    public function QueryManager() {
      // OOP: instantieer een MySQLConnection-object en geef deze als resultaat 
      $this->dbconn = new Connect();     
    }
	
  //Nadim 21-02 - Haalt de gegevens studentcode, persoonsnummer, voornaam, achternaam en opleiding uit de database met een INNER JOIN en zet deze gegevens in een array PERSOONSNUMMER MOET AANWEZIG ZIJN SESSION- Begin
  public function getGegevensStudent($persoonsnummer) {
      $result = $this->dbconn->query("SELECT bezoekstudent.studentcode, gebruiker.persoonsnummer, gebruiker.voornaam, gebruiker.achternaam, student.opleiding FROM gebruiker INNER JOIN student ON gebruiker.persoonsnummer=student.persoonsnummer INNER JOIN bezoekstudent ON student.studentcode=bezoekstudent.studentcode WHERE bezoekstudent.persoonsnummer='$persoonsnummer'");
      while($row = mysqli_fetch_array($result)) {
          $rows[] = new gegevensStudent($row);
      }
      return $rows;
    }
  //Nadim 21-02 Einde


    public function Login($voornaam, $wachtwoord){
      if(!empty($voornaam) && !empty($wachtwoord)){
        $result = $this->dbconn->query("SELECT persoonsnummer, functienummer FROM gebruiker WHERE voornaam= '$voornaam' AND wachtwoord = '$wachtwoord'");
        $row = mysqli_num_rows($result);
        if($row == 1){
          header('location: view/home.php');

          while (list($persoonsnummer, $functienummer)=mysqli_fetch_row($result)) {
            $_SESSION['persoonsnummer'] = $persoonsnummer;      
            $_SESSION['functienummer'] = $functienummer;   
          }  
        }else{
        echo "fout";
        }

        return $row;
      }
    }






  }

?>