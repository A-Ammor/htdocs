<?php

include_once('lib/Db.php');
include_once('lib/Beheerder.php');

class Attractie
{

    private $idattractie;
    private $idbeheerder;
    private $naamattractie;
    private $soortattractie;


    function __construct()
    {

    }

    function getIdBeheerder()
    {
        return $this->idbeheerder;
    }

    function getBeheerderById()
    {
        $db = new Db();
        $conn = $db->getConnectie();
        $sth = $conn->prepare("SELECT * FROM gebruiker WHERE idbeheerder = " . $this->idbeheerder);
	    $sth->execute();
	    $sth->setFetchMode(PDO::FETCH_CLASS, "Beheerder");
	    return $sth->fetch();
    }

    function getIdAttractie()
    {
        return $this->idattractie;
    }

    function getAttractienaam()
    {
        return $this->naamattractie;
    }

    function getAttractiesoort()
    {
        return $this->soortattractie;
    }

}


?>