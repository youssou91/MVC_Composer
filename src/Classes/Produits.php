<?php
namespace App\Classes;
class Produits {
    private $nom;
    private $prix_unitaite;
    private $quantite;
    private $idCategorie;
    private $model;
    private $courteDescription;
    private $longueDescription;
    private $couleurs;
    private $cheminImage;

    public function __construct($nom, $prix_unitaite, $quantite, $idCategorie, $model, $courteDescription, $longueDescription = null, $couleurs = null, $cheminImage = null) {
        $this->nom = $nom;
        $this->prix_unitaite = $prix_unitaite;
        $this->quantite = $quantite;
        $this->idCategorie = $idCategorie;
        $this->model = $model;
        $this->courteDescription = $courteDescription;
        $this->longueDescription = $longueDescription;
        $this->couleurs = $couleurs;
        $this->cheminImage = $cheminImage;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrixUnitaire() {
        return $this->prix_unitaite;
    }

    public function getQuantite() {
        return $this->quantite;
    }

    public function getIdCategorie() {
        return $this->idCategorie;
    }

    public function getModel() {
        return $this->model;
    }

    public function getCourteDescription() {
        return $this->courteDescription;
    }

    public function getDescription() {
        return $this->longueDescription;
    }

    public function getCouleursProd() {
        return $this->couleurs;
    }

    public function getCheminImage() {
        return $this->cheminImage;
    }
}
