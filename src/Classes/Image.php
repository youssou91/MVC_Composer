<?php
namespace App\Classes;

    class Image{
        private $id_image;
        private $id_produit;
        private $chemin_image;
        private $nom_image;
        
        // Constructeur
        public function __construct($id_image, $id_produit, $chemin_image, $nom_image) {
            $this->id_image = $id_image;
            $this->id_produit = $id_produit;
            $this->chemin_image = $chemin_image;
            $this->nom_image = $nom_image;
        }
        
        // Getters
        public function getId_image() {
            return $this->id_image;
        }
        
        public function getId_produit() {
            return $this->id_produit;
        }
        
        public function getChemin_image() {
            return $this->chemin_image;
        }
        
        public function getNom_image() {
            return $this->nom_image;
        }
        
        // Setters
        public function setId_image($id_image) {
            $this->id_image = $id_image;
        }
        
        public function setId_produit($id_produit) {
            $this->id_produit = $id_produit;
        }
        
        public function setChemin_image($chemin_image) {
            $this->chemin_image = $chemin_image;
        }
        
        public function setNom_image($nom_image) {
            $this->nom_image = $nom_image;
        }
        
    }
?>