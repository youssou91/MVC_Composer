<?php
namespace App\Classes;

class Produit {
    private $nom;
    private $prix_unitaire;
    private $description;
    private $courte_description;
    private $quantite;
    private $id_categorie;
    private $couleurs_prod;
    private $model;
    
    // Constructeur
    public function __construct($nom, $prix_unitaire, $description, $courte_description, $quantite, $id_categorie, $couleurs_prod, $model) {
        $this->nom = $nom;
        $this->prix_unitaire = $prix_unitaire;
        $this->description = $description;
        $this->courte_description = $courte_description;
        $this->quantite = $quantite;
        $this->id_categorie = $id_categorie;
        $this->couleurs_prod = $couleurs_prod;
        $this->model = $model;
    }

    // Getters
    public function getNom() {
        return $this->nom;
    }

    public function getPrixUnitaire() {
        return $this->prix_unitaire;
    }
    

    public function getDescription() {
        return $this->description;
    }

    public function getCourteDescription() {
        return $this->courte_description;
    }

    public function getQuantite() {
        return $this->quantite;
    }

    public function getIdCategorie() {
        return $this->id_categorie;
    }

    public function getCouleursProd() {
        return $this->couleurs_prod;
    }

    public function getModel() {
        return $this->model;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setPrixUnitaire($prix_unitaire) {
        $this->prix_unitaire = $prix_unitaire;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCourteDescription($courte_description) {
        $this->courte_description = $courte_description;
    }

    public function setQuantite($quantite) {
        $this->quantite = $quantite;
    }

    public function setIdCategorie($id_categorie) {
        $this->id_categorie = $id_categorie;
    }

    public function setCouleursProd($couleurs_prod) {
        $this->couleurs_prod = $couleurs_prod;
    }
    
    public function setModel($model) {
        $this->model = $model;
    }
}
