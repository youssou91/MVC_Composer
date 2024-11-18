<?php
namespace App\Classes;

class Utilisateur {
    private $id_utilisateur;
    private $nom_utilisateur;
    private $prenom;
    private $date_naissance;
    private $couriel;
    private $mot_de_pass;
    private $telephone;
    private $statut;

    // Constructor to initialize the user object with data
    public function __construct($id_utilisateur, $nom_utilisateur, $prenom, $date_naissance, $couriel, $mot_de_pass, 
                                $telephone, $statut) {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->couriel = $couriel;
        $this->mot_de_pass = $mot_de_pass;
        $this->telephone = $telephone;
        $this->statut = $statut;
        
    }

    // Getter methods for the properties
    public function getIdUtilisateur() {
        return $this->id_utilisateur;
    }

    public function getNomUtilisateur() {
        return $this->nom_utilisateur;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getDateNaissance() {
        return $this->date_naissance;
    }

    public function getCouriel() {
        return $this->couriel;
    }

    public function getMotDePass() {
        return $this->mot_de_pass;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function getStatut() {
        return $this->statut;
    }

    // Setter methods for the properties
    public function setIdUtilisateur($id_utilisateur) {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function setNomUtilisateur($nom_utilisateur) {
        $this->nom_utilisateur = $nom_utilisateur;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    public function setDateNaissance($date_naissance) {
        $this->date_naissance = $date_naissance;
    }

    public function setCouriel($couriel) {
        $this->couriel = $couriel;
    }

    public function setMotDePass($mot_de_pass) {
        $this->mot_de_pass = $mot_de_pass;
    }

    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

}
?>
