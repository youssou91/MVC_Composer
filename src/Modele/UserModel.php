<?php
namespace App\Modele;
use PDO;
use Exception;
use DateTime;
class UserModel {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // Fonction pour ajouter un utilisateur à la base de données
    public function addUserDB($user) {
        // Démarrer une transaction
        $this->conn->beginTransaction();

        try {
            // Insérer l'utilisateur, l'adresse, et les associer
            $id_utilisateur = $this->insertUser($user);
            $id_adresse = $this->insertAddress($user);
            $this->associateUserAddress($id_utilisateur, $id_adresse);
            $this->assignUserRole($id_utilisateur, 'client');

            // Commit de la transaction
            $this->conn->commit();
            return "L'utilisateur a été ajouté avec succès.";
        } catch (Exception $e) {
            // Rollback si une erreur se produit
            $this->conn->rollBack();
            return "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
        }
    }

    // Insérer l'utilisateur dans la table des utilisateurs
    private function insertUser($user) {
        $sql = "INSERT INTO utilisateur (nom_utilisateur, prenom, date_naissance, couriel, mot_de_pass, telephone, statut) 
                VALUES (:nom_utilisateur, :prenom, :datNaiss, :couriel, :password, :telephone, :statut)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Hash du mot de passe
        $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);

        try {
            $stmt->execute([
                ':nom_utilisateur' => $user['nom_utilisateur'],
                ':prenom' => $user['prenom'],
                ':datNaiss' => $user['datNaiss'],
                ':couriel' => $user['couriel'],
                ':password' => $passwordHash,
                ':telephone' => $user['telephone'],
                ':statut' => 'actif'
            ]);
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage());
        }
    }

    // Insérer l'adresse dans la table des adresses
    private function insertAddress($user) {
        $sql = "INSERT INTO adresse (rue, ville, code_postal, pays, numero, province) 
                VALUES (:rue, :ville, :code_postal, :pays, :numero, :province)";
        
        $stmt = $this->conn->prepare($sql);
        
        try {
            $stmt->execute([
                ':rue' => $user['rue'],
                ':ville' => $user['ville'],
                ':code_postal' => $user['code_postal'],
                ':pays' => $user['pays'],
                ':numero' => $user['numero'],
                ':province' => $user['province']
            ]);
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'insertion de l'adresse : " . $e->getMessage());
        }
    }

    // Associer l'utilisateur à l'adresse dans la table d'association
    private function associateUserAddress($id_utilisateur, $id_adresse) {
        $sql = "INSERT INTO utilisateur_adresse (id_utilisateur, id_adresse) 
                VALUES (:id_utilisateur, :id_adresse)";
        
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':id_adresse' => $id_adresse
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'association utilisateur-adresse : " . $e->getMessage());
        }
    }

    // Assigner un rôle à l'utilisateur
    private function assignUserRole($id_utilisateur, $role_description) {
        try {
            $role = $this->getRoleByDescription($role_description);

            $sql = "INSERT INTO role_utilisateur (id_role, id_utilisateur) 
                    VALUES (:id_role, :id_utilisateur)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_role' => $role['id_role'],
                ':id_utilisateur' => $id_utilisateur
            ]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'assignation du rôle à l'utilisateur : " . $e->getMessage());
        }
    }

    // Récupérer un rôle par description
    private function getRoleByDescription($role_description) {
        $sql = "SELECT * FROM role WHERE description = :description";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':description' => $role_description]);

        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$role) {
            throw new Exception("Rôle non trouvé : " . $role_description);
        }

        return $role;
    }

    // Valider les données de l'utilisateur (Email, Mot de passe, etc.)
    public function validateUserData($email, $password, $cpassword, $birthDate) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Le format de l'email est invalide.");
        }

        if ($this->getElementByEmailForAddUser($email)) {
            throw new Exception("L'email existe déjà dans la base de données.");
        }

        // Validation du mot de passe
        if (strlen($password) < 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[@$!%*?&]/', $password)) {
            throw new Exception("Le mot de passe doit contenir au moins 6 caractères, une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial.");
        }

        if ($password !== $cpassword) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        if ($this->calculateAge($birthDate) < 16) {
            throw new Exception("L'utilisateur doit avoir au moins 16 ans.");
        }
    }

    // Vérifier si l'email existe déjà dans la base de données
    private function getElementByEmailForAddUser($email) {
        $sql = "SELECT * FROM utilisateur WHERE couriel = :couriel";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':couriel' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Calculer l'âge à partir de la date de naissance
    private function calculateAge($birthDate) {
        $birthDate = new DateTime($birthDate);
        $today = new DateTime();
        $age = $today->diff($birthDate);
        
        if ($age->y < 16) {
            throw new Exception("L'utilisateur doit avoir au moins 16 ans.");
        }

        return $age->y;
    }
    
    // Récupérer tous les utilisateurs avec leurs rôles
    public function getAllUsers() {
        try {
            $sql = "SELECT u.*, r.description as role 
                    FROM utilisateur u 
                    JOIN role_utilisateur ru ON u.id_utilisateur = ru.id_utilisateur 
                    JOIN role r ON ru.id_role = r.id_role";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        }
    }

    // Get user information by ID
    public function getUserInfo($id_utilisateur) {
        $sql = "SELECT u.*, a.rue, a.numero, a.ville, a.code_postal, a.province, a.pays
                FROM utilisateur u
                LEFT JOIN utilisateur_adresse ua ON u.id_utilisateur = ua.id_utilisateur
                LEFT JOIN adresse a ON ua.id_adresse = a.id_adresse
                WHERE u.id_utilisateur = :id_utilisateur";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par email pour la connexion
    public function getElementByEmailForLogin($email) {
        $query = 'SELECT * FROM utilisateur WHERE couriel = :email LIMIT 1'; // vérifier que le nom de table et colonne sont corrects
        $stmt = $this->conn->prepare($query); // Remplacez `$this->db` par `$this->conn`
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourner les données de l'utilisateur ou null
    }

    // Vérifier les informations d'identification de l'utilisateur
    public function checkUser($email, $password) {
        $user = $this->getElementByEmailForLogin($email); // Récupérer l'utilisateur par email

        if ($user && password_verify($password, $user['mot_de_pass'])) {
            return $user; // L'utilisateur existe et le mot de passe est correct
        } else {
            return null; // L'utilisateur ou le mot de passe est incorrect
        }
    }

    // Fonction pour récupérer les commandes d'un utilisateur avec leurs statuts
    public function getUserCommandWithStatus($userId) {
        // Préparer la requête SQL
        $sql = "SELECT * FROM commande WHERE id_utilisateur = :userId";
        
        // Préparer la déclaration PDO
        $stmt = $this->conn->prepare($sql);
        
        // Lier les paramètres
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Récupérer les résultats
        $commande = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $commande;
    }


}

?>
