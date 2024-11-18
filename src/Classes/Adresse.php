<?php
namespace App\Classes;

class Adresse {
    private $id_adresse;
    private $rue;
    private $ville;
    private $code_postal;
    private $pays;
    private $numero;
    private $province;

    // Constructor to initialize the user object with data
    public function __construct($id_adresse, $rue, $ville, $code_postal, $pays, $numero, $province) {
        $this->id_adresse = $id_adresse;
        $this->rue = $rue;
        $this->ville = $ville;
        $this->code_postal = $code_postal;
        $this->pays = $pays;
        $this->numero = $numero;
        $this->province = $province;
    }

    // Getter methods for the properties
    public function getId_adresse() {
        return $this->id_adresse;
    }

    public function getRue() {
        return $this->rue;
    }

    public function getVille() {
        return $this->ville;
    }

    public function getCodePostal() {
        return $this->code_postal;
    }

    public function getPays() {
        return $this->pays;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getProvince() {
        return $this->province;
    }

    // Setter methods for the properties
    public function setId_adresse($id_adresse) {
        $this->id_adresse = $id_adresse;
    }
    public function setRue($rue) {
        $this->rue = $rue;
    }

    public function setVille($ville) {
        $this->ville = $ville;
    }

    public function setCodePostal($code_postal) {
        $this->code_postal = $code_postal;
    }

    public function setPays($pays) {
        $this->pays = $pays;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function setProvince($province) {
        $this->province = $province;
    }
    
}
?>
