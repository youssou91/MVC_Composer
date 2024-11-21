<?php
namespace App\Classes;

    class Commande {
        private $id_commande;	
        private $id_utilisateur;
        private $date_commande;	
        private $prix_total;	
        private $statut;
        // Constructeur
        public function __construct($id_commande, $id_utilisateur, $date_commande, $prix_total, $statut) {
            $this->id_commande = $id_commande;
            $this->id_utilisateur = $id_utilisateur;
            $this->date_commande = $date_commande;
            $this->prix_total = $prix_total;
            $this->statut = $statut;
        }
        // Getters and setters
        public function getId_commande() {
            return $this->id_commande;
        }
        public function getId_utilisateur() {
            return $this->id_utilisateur;
        }
        public function getDate_commande() {
            return $this->date_commande;
        }
        public function getPrix_total() {
            return $this->prix_total;
        }
        public function getStatut() {
            return $this->statut;
        }
        public function setId_commande($id_commande) {
            $this->id_commande = $id_commande;
        }
        public function setId_utilisateur($id_utilisateur) {
            $this->id_utilisateur = $id_utilisateur;
        }
        public function setDate_commande($date_commande) {
            $this->date_commande = $date_commande;
        }
        public function setPrix_total($prix_total) {
            $this->prix_total = $prix_total;
        }
        public function setStatut($statut) {
            $this->statut = $statut;
        }
    
    }
?>
