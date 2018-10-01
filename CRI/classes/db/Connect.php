<?php

//Nadim 21-02 - Connectie met de database 'bpvco' - Begin
class Connect{

    private $dbhost = 'localhost';      //naam van de server                                  
    private $dbuser = 'root';           //naam van de gebruiker 
    private $dbpassword = '';           //wachtwoord van de gebruiker 
    private $dbname = 'bpvco';      //naam van de database

    private $connection; // bewaar de db link    
    
    public function Connect() {
            $this->openConnection();           
    }

    public function openConnection() {
        //* connect to database
        $this->connection = 
                mysqli_connect($this->dbhost, 
                        $this->dbuser, 
                        $this->dbpassword, 
                        $this->dbname);
        if (!$this->connection) {
            die("Database connectie mislukte: " . mysqli_error($this->connection));
        	} 
        return $this->connection;      
    }        
    // query
        public function query($sql) {
        $this->last_query = $sql; // bewaar de query
        $result = mysqli_query($this->connection, $sql);
        return $result;
    }     

} 
//Nadim 21-02 Einde

?>