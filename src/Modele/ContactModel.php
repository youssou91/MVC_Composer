<?php
namespace App\Modele;
use PDO;
class ContactModel {
    private $conn;
    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }
    
}

?>