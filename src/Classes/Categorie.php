<?php
namespace App\Classes;

    class Categorie {
        private $id_categorie;
        private $nom_categorie;

        public function __construct($id_categorie, $nom_categorie) {
            $this->id_categorie = $id_categorie;
            $this->nom_categorie = $nom_categorie;
        }

        public function getId_categorie() {
            return $this->id_categorie;
        }

        public function getNom_categorie() {
            return $this->nom_categorie;
        }
    }
?>
