<?php

class Beheerder
{

    private $idBeheerder;
    private $login;
    private $password;

    function __construct() {
        #
    }

    function getFullName() {
        return $this->idBeheerder . " " . $this->login;
    }

}