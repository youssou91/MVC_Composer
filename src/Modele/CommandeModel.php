<?php
namespace App\Modele;
use PDO;
use App\Classes\Commande;  
use PDOException;

class CommandeModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCommandes() {
        try {
            $stmt = $this->pdo->prepare("SELECT 
                commande.id_commande, 
                commande.id_utilisateur, 
                commande.date_commande, 
                commande.prix_total, 
                utilisateur.nom_utilisateur, 
                utilisateur.prenom,
                commande.statut
            FROM 
                commande
            INNER JOIN utilisateur ON commande.id_utilisateur = utilisateur.id_utilisateur");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la récupération des commandes : " . $e->getMessage());
        }
    }
    
    public function updateCommande($id_commande, $statut) {
        try {
            $stmt = $this->pdo->prepare("UPDATE commande SET statut = :statut WHERE id_commande = :id_commande");
            $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
            $stmt->bindParam(':statut', $statut);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la mise à jour de la commande avec ID $id_commande : " . $e->getMessage());
        }
    }
    
    public function getCommandeById($id_commande) {
        try {
            // Récupérer les informations de base de la commande
            $stmt = $this->pdo->prepare(
                "SELECT c.*, u.nom_utilisateur, u.prenom, u.telephone, u.couriel as email,
                        a.rue, a.ville, a.code_postal, a.pays, a.numero 
                 FROM commande c 
                 INNER JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
                 LEFT JOIN utilisateur_adresse ua ON u.id_utilisateur = ua.id_utilisateur
                 LEFT JOIN adresse a ON ua.id_adresse = a.id_adresse
                 WHERE c.id_commande = :id_commande"
            );
            $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                return null;
            }
            
            // Récupérer les articles de la commande
            $stmt = $this->pdo->prepare(
                "SELECT pc.*, p.nom as nom_produit, p.prix_unitaire as prix, p.chemin_image as image 
                 FROM produit_commande pc 
                 INNER JOIN produits p ON pc.id_produit = p.id_produit 
                 WHERE pc.id_commande = :id_commande"
            );
            $stmt->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculer le sous-total
            $sousTotal = array_reduce($articles, function($carry, $item) {
                return $carry + ($item['prix'] * $item['quantite']);
            }, 0);
            
            // Ajouter les articles et le sous-total à la commande
            $commande['articles'] = $articles;
            $commande['sous_total'] = $sousTotal;
            $commande['frais_livraison'] = $commande['frais_livraison'] ?? 0;
            $commande['total'] = $sousTotal + $commande['frais_livraison'];
            
            return $commande;
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la récupération de la commande avec ID $id_commande : " . $e->getMessage());
        }
    }
    
    public function addCommande($commande) {
        if (!isset($commande['id_utilisateur']) || !isset($commande['prix_total'])) {
            throw new PDOException("Les informations requises pour ajouter une commande sont manquantes.");
        }
    
        $id_utilisateur = $commande['id_utilisateur'];
        $prix_total = $commande['prix_total'];
        $statut = 'En attente';
    
        try {
            // Début de la transaction
            $this->pdo->beginTransaction();
    
            // Insertion dans la table commande
            $sql = "INSERT INTO commande (id_utilisateur, statut, date_commande, prix_total) VALUES (:id_utilisateur, :statut, NOW(), :prix_total)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':statut' => $statut,
                ':prix_total' => $prix_total
            ]);
            // Récupération de l'ID de la commande créée
            $id_commande = $this->pdo->lastInsertId();
    
            // Insertion des produits de la commande
            foreach ($commande['produits'] as $produit) {
                if (!$this->addProduitCommande($id_commande, $produit)) {
                    throw new PDOException("Erreur lors de l'ajout d'un produit à la commande.");
                }
            }
    
            // Validation de la transaction
            $this->pdo->commit();
            return $id_commande;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new PDOException("Erreur lors de l'ajout de la commande : " . $e->getMessage());
        }
    }
    
    function addProduitCommande($id_commande, $produit) {
        $id_produit = $produit['id_produit'];
        $quantite = $produit['quantite'];
    
        try {
            // Vérifiez si le produit existe dans la table produits
            $stmtCheckProduit = $this->pdo->prepare("SELECT id_produit FROM produits WHERE id_produit = :id_produit");
            $stmtCheckProduit->execute([':id_produit' => $id_produit]);
    
            if ($stmtCheckProduit->rowCount() > 0) {
                // Vérifier si le produit est déjà ajouté à cette commande
                $stmtCheckProduitCommande = $this->pdo->prepare("SELECT * FROM produit_commande WHERE id_commande = :id_commande AND id_produit = :id_produit");
                $stmtCheckProduitCommande->execute([
                    ':id_commande' => $id_commande,
                    ':id_produit' => $id_produit
                ]);
    
                if ($stmtCheckProduitCommande->rowCount() > 0) {
                    // Si le produit est déjà présent, mettez à jour la quantité
                    $stmtUpdateQuantite = $this->pdo->prepare(
                        "UPDATE produit_commande SET quantite = quantite + :quantite WHERE id_commande = :id_commande AND id_produit = :id_produit"
                    );
                    $stmtUpdateQuantite->execute([
                        ':quantite' => $quantite,
                        ':id_commande' => $id_commande,
                        ':id_produit' => $id_produit
                    ]);
                } else {
                    // Insertion dans produit_commande si ce n'est pas déjà ajouté
                    $stmtProduitCommande = $this->pdo->prepare(
                        "INSERT INTO produit_commande (id_commande, id_produit, quantite) VALUES (:id_commande, :id_produit, :quantite)"
                    );
                    $stmtProduitCommande->execute([
                        ':id_commande' => $id_commande,
                        ':id_produit' => $id_produit,
                        ':quantite' => $quantite
                    ]);
                }
    
                // Mise à jour de la quantité du produit
                if (!$this->miseAJourQuantiteProduit($id_produit, $quantite)) {
                    throw new \PDOException("Erreur lors de la mise à jour de la quantité du produit.");
                }
            } else {
                throw new \PDOException("Le produit avec l'ID $id_produit n'existe pas.");
            }
    
            return true;
        } catch (\PDOException $e) {
            throw new \PDOException("Erreur dans addProduitCommande : " . $e->getMessage());
        }
    }    
    
    
    function miseAJourQuantiteProduit($id_produit, $quantite) {
        try {
            $sql = "UPDATE produits SET quantite = quantite - :quantite WHERE id_produit = :id_produit";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':quantite' => $quantite,
                ':id_produit' => $id_produit
            ]);
            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur dans miseAJourQuantiteProduit : " . $e->getMessage());
        }
    }
    
    public function getOrderTotal($order_id) {
        try {
            // Préparer la requête SQL
            $stmt = $this->pdo->prepare("
                SELECT SUM(p.prix_unitaire * pc.quantite) AS total 
                FROM commande c
                INNER JOIN produit_commande pc ON c.id_commande = pc.id_commande
                INNER JOIN produits p ON pc.id_produit = p.id_produit
                WHERE c.id_commande = :order_id
            
            ");
    
            // Lier l'ID de la commande avec un paramètre sécurisé
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    
            // Exécuter la requête
            $stmt->execute();
    
            // Récupérer le résultat
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Retourner le total ou 0 par défaut
            return $row['total'] ?? 0;
    
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du total de la commande $order_id : " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupère toutes les commandes d'un utilisateur avec leur statut
     * 
     * @param int $id_utilisateur L'ID de l'utilisateur
     * @return array Les commandes de l'utilisateur
     */
    public function getCommandesByUser($id_utilisateur) {
        try {
            error_log("Début de getCommandesByUser pour l'utilisateur ID: $id_utilisateur");
            
            // Vérifier la connexion PDO
            if (!$this->pdo) {
                error_log("Erreur: Pas de connexion PDO");
                throw new PDOException("Pas de connexion à la base de données");
            }
            
            // Préparer et exécuter la requête
            $query = "
                SELECT 
                    id_commande,
                    id_utilisateur,
                    date_commande,
                    prix_total,
                    statut
                FROM 
                    commande
                WHERE 
                    id_utilisateur = :id_utilisateur
                ORDER BY 
                    date_commande DESC
            ";
            
            error_log("Requête SQL: " . $query);
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id_utilisateur', (int)$id_utilisateur, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                throw new PDOException("Erreur d'exécution de la requête: " . ($errorInfo[2] ?? 'Inconnue'));
            }
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Nombre de commandes trouvées: " . count($result));
            
            // Convertir les prix en nombres flottants
            foreach ($result as &$row) {
                $row['prix_total'] = (float)$row['prix_total'];
            }
            
            return $result;
            
        } catch (PDOException $e) {
            $errorMsg = "Erreur PDO dans getCommandesByUser: " . $e->getMessage();
            error_log($errorMsg);
            throw $e; // Renvoyer l'exception pour une meilleure gestion en amont
        } catch (Exception $e) {
            $errorMsg = "Erreur inattendue dans getCommandesByUser: " . $e->getMessage();
            error_log($errorMsg);
            throw $e; // Renvoyer l'exception pour une meilleure gestion en amont
        }
    }
}