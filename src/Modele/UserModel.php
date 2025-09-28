<?php
namespace App\Modele;
use PDO;
use Exception;
use DateTime;
class UserModel {
    private $db; 
    
    public function __construct($db) {
        error_log("Constructeur UserModel appelé");
        error_log("Type de db reçu: " . gettype($db));
        if (is_object($db)) {
            error_log("Classe de db: " . get_class($db));
        }
        
        if (!($db instanceof \PDO)) {
            error_log("ERREUR: La connexion fournie n'est pas une instance de PDO");
            if (is_object($db)) {
                error_log("La classe de l'objet est: " . get_class($db));
            }
            throw new \Exception("Une instance valide de PDO est requise pour initialiser UserModel");
        }
        
        $this->db = $db;
        error_log("Connexion PDO correctement initialisée dans UserModel");
    }

    // Fonction pour ajouter un utilisateur à la base de données
    public function addUserDB($user) {
        // Démarrer une transaction
        $this->db->beginTransaction();
        error_log("Début de l'ajout d'un utilisateur: " . print_r($user, true));

        try {
            // Insérer l'utilisateur, l'adresse, et les associer
            error_log("Tentative d'insertion de l'utilisateur");
            $id_utilisateur = $this->insertUser($user);
            error_log("Utilisateur inséré avec l'ID: " . $id_utilisateur);
            
            error_log("Tentative d'insertion de l'adresse");
            $id_adresse = $this->insertAddress($user);
            error_log("Adresse insérée avec l'ID: " . $id_adresse);
            
            error_log("Tentative d'association utilisateur-adresse");
            $this->associateUserAddress($id_utilisateur, $id_adresse);
            error_log("Association utilisateur-adresse réussie");
            
            error_log("Tentative d'assignation du rôle");
            $this->assignUserRole($id_utilisateur, 'client');
            error_log("Rôle assigné avec succès");

            // Commit de la transaction
            $this->db->commit();
            error_log("Transaction commitée avec succès");
            return "L'utilisateur a été ajouté avec succès.";
        } catch (Exception $e) {
            // Rollback si une erreur se produit
            $this->db->rollBack();
            $errorMsg = "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
            error_log($errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            return $errorMsg;
        }
    }

    // Insérer l'utilisateur dans la table des utilisateurs
    private function insertUser($user) {
        $sql = "INSERT INTO utilisateur (nom_utilisateur, prenom, date_naissance, couriel, mot_de_pass, telephone, statut) 
                VALUES (:nom_utilisateur, :prenom, :datNaiss, :couriel, :password, :telephone, :statut)";
        
        $stmt = $this->db->prepare($sql);
        
        // Vérifier que tous les champs requis sont présents
        $requiredFields = ['nom_utilisateur', 'prenom', 'datNaiss', 'couriel', 'password', 'telephone'];
        foreach ($requiredFields as $field) {
            if (!isset($user[$field]) || empty($user[$field])) {
                throw new Exception("Le champ '$field' est requis mais n'a pas été fourni.");
            }
        }
        
        // Hash du mot de passe
        $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);
        
        $params = [
            ':nom_utilisateur' => $user['nom_utilisateur'],
            ':prenom' => $user['prenom'],
            ':datNaiss' => $user['datNaiss'],
            ':couriel' => $user['couriel'],
            ':password' => $passwordHash,
            ':telephone' => $user['telephone'],
            ':statut' => 'actif'
        ];
        
        error_log("Paramètres d'insertion utilisateur: " . print_r($params, true));

        try {
            $stmt->execute($params);
            $lastInsertId = $this->db->lastInsertId();
            error_log("Utilisateur inséré avec succès. ID: " . $lastInsertId);
            return $lastInsertId;
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage();
            error_log($errorMsg);
            error_log("Requête SQL: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            throw new Exception($errorMsg);
        }
    }

    // Insérer l'adresse dans la table des adresses
    private function insertAddress($user) {
        $sql = "INSERT INTO adresse (rue, ville, code_postal, pays, numero, province) 
                VALUES (:rue, :ville, :code_postal, :pays, :numero, :province)";
        
        // Vérifier que tous les champs requis sont présents
        $requiredFields = ['rue', 'ville', 'code_postal', 'pays', 'numero', 'province'];
        foreach ($requiredFields as $field) {
            if (!isset($user[$field]) || $user[$field] === '') {
                throw new Exception("Le champ d'adresse '$field' est requis mais n'a pas été fourni.");
            }
        }
        
        $params = [
            ':rue' => $user['rue'],
            ':ville' => $user['ville'],
            ':code_postal' => $user['code_postal'],
            ':pays' => $user['pays'],
            ':numero' => $user['numero'],
            ':province' => $user['province']
        ];
        
        error_log("Paramètres d'insertion d'adresse: " . print_r($params, true));
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $lastInsertId = $this->db->lastInsertId();
            error_log("Adresse insérée avec succès. ID: " . $lastInsertId);
            return $lastInsertId;
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'insertion de l'adresse : " . $e->getMessage();
            error_log($errorMsg);
            error_log("Requête SQL: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            throw new Exception($errorMsg);
        }
    }

    // Associer l'utilisateur à l'adresse dans la table d'association
    private function associateUserAddress($id_utilisateur, $id_adresse) {
        $sql = "INSERT INTO utilisateur_adresse (id_utilisateur, id_adresse) 
                VALUES (:id_utilisateur, :id_adresse)";
        
        $stmt = $this->db->prepare($sql);
        
        error_log("Association utilisateur-adresse - ID Utilisateur: $id_utilisateur, ID Adresse: $id_adresse");

        try {
            $params = [
                ':id_utilisateur' => $id_utilisateur,
                ':id_adresse' => $id_adresse
            ];
            
            $stmt->execute($params);
            error_log("Association utilisateur-adresse réussie");
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'association utilisateur-adresse : " . $e->getMessage();
            error_log($errorMsg);
            error_log("Requête SQL: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            throw new Exception($errorMsg);
        }
    }

    // Assigner un rôle à l'utilisateur
    private function assignUserRole($id_utilisateur, $role_description) {
        error_log("Tentative d'assignation du rôle '$role_description' à l'utilisateur ID: $id_utilisateur");
        
        try {
            $role = $this->getRoleByDescription($role_description);
            error_log("Rôle trouvé: " . print_r($role, true));

            $sql = "INSERT INTO role_utilisateur (id_role, id_utilisateur) 
                    VALUES (:id_role, :id_utilisateur)";
            
            $stmt = $this->db->prepare($sql);
            $params = [
                ':id_role' => $role['id_role'],
                ':id_utilisateur' => $id_utilisateur
            ];
            
            error_log("Exécution de la requête d'assignation de rôle");
            $stmt->execute($params);
            error_log("Rôle assigné avec succès à l'utilisateur ID: $id_utilisateur");
        } catch (Exception $e) {
            $errorMsg = "Erreur lors de l'assignation du rôle à l'utilisateur : " . $e->getMessage();
            error_log($errorMsg);
            if (isset($sql)) error_log("Requête SQL: " . $sql);
            if (isset($params)) error_log("Paramètres: " . print_r($params, true));
            throw new Exception($errorMsg);
        }
    }

    // Récupérer un rôle par description
    private function getRoleByDescription($role_description) {
        error_log("Recherche du rôle avec la description: " . $role_description);
        
        $sql = "SELECT * FROM role WHERE description = :description";
        $stmt = $this->db->prepare($sql);
        $params = [':description' => $role_description];
        
        try {
            $stmt->execute($params);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$role) {
                // Vérifier quels rôles existent dans la base de données
                $allRolesStmt = $this->db->query("SELECT * FROM role");
                $allRoles = $allRolesStmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Rôles disponibles dans la base de données: " . print_r($allRoles, true));
                
                throw new Exception("Rôle non trouvé : " . $role_description);
            }
            
            error_log("Rôle trouvé: " . print_r($role, true));
            return $role;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du rôle: " . $e->getMessage());
            error_log("Requête SQL: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            throw $e;
        }
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
        $stmt = $this->db->prepare($sql);
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
    
   // Récupérer tous les utilisateurs avec leurs détails
    public function getAllUsers() {
        try {
            error_log("Début de getAllUsers()");
            
            // Vérifier que la propriété db existe
            if (!isset($this->db)) {
                error_log("La propriété db n'est pas définie dans UserModel");
                throw new Exception("Erreur de configuration du modèle utilisateur");
            }
            
            // Vérifier que db est une instance de PDO
            if (!($this->db instanceof \PDO)) {
                error_log(sprintf("La propriété db n'est pas une instance de PDO. Type: %s", gettype($this->db)));
                if (is_object($this->db)) {
                    error_log(sprintf("Classe de l'objet: %s", get_class($this->db)));
                }
                throw new Exception("La connexion PDO est invalide dans UserModel.");
            }
            
            error_log("Exécution de la requête SQL...");
            $sql = "SELECT u.*, GROUP_CONCAT(DISTINCT r.description) as roles 
                   FROM utilisateur u
                   LEFT JOIN role_utilisateur ru ON u.id_utilisateur = ru.id_utilisateur
                   LEFT JOIN role r ON ru.id_role = r.id_role
                   GROUP BY u.id_utilisateur
                   ORDER BY u.id_utilisateur DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convertir la chaîne de rôles en tableau pour chaque utilisateur
            foreach ($users as &$user) {
                if (isset($user['roles'])) {
                    $user['roles'] = !empty($user['roles']) ? explode(',', $user['roles']) : [];
                } else {
                    $user['roles'] = [];
                }
                // S'assurer que statut a une valeur par défaut si null
                if (!isset($user['statut']) || $user['statut'] === null) {
                    $user['statut'] = 'Inactif';
                }
                // S'assurer que est_actif a une valeur par défaut si null
                if (!isset($user['est_actif']) || $user['est_actif'] === null) {
                    $user['est_actif'] = 0;
                }
            }
            
            error_log(sprintf("Nombre d'utilisateurs récupérés: %d", count($users)));
            return $users;
            
        } catch (\PDOException $e) {
            error_log("Erreur PDO dans getAllUsers(): " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . ", Ligne: " . $e->getLine());
            throw new Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Erreur dans getAllUsers(): " . $e->getMessage());
            error_log("Fichier: " . $e->getFile() . ", Ligne: " . $e->getLine());
            throw $e;
        }
    }

    // Récupérer les utilisateurs avec leurs commandes
    public function getUsersWithOrders() {
        error_log("=== DÉBUT UserModel::getUsersWithOrders() ===");
        
        try {
            // Vérifier la connexion à la base de données
            if (!$this->db) {
                $errorMsg = "ERREUR: La propriété db n'est pas initialisée dans UserModel";
                error_log($errorMsg);
                throw new \Exception($errorMsg);
            }
            
            // Vérifier que la connexion est active
            try {
                $this->db->query('SELECT 1');
            } catch (\PDOException $e) {
                $errorMsg = "ERREUR: La connexion à la base de données a échoué: " . $e->getMessage();
                error_log($errorMsg);
                throw new \Exception($errorMsg);
            }
            
            // Requête simplifiée pour récupérer les utilisateurs avec leurs rôles
            $sql = "SELECT 
                        u.id_utilisateur,
                        u.nom_utilisateur,
                        u.prenom,
                        u.couriel,
                        u.telephone,
                        u.date_naissance,
                        u.statut,
                        u.est_actif,
                        GROUP_CONCAT(DISTINCT r.description) as roles,
                        c.id_commande,
                        c.date_commande,
                        c.prix_total,
                        c.statut as statut_commande
                    FROM utilisateur u
                    LEFT JOIN commande c ON u.id_utilisateur = c.id_utilisateur
                    LEFT JOIN role_utilisateur ru ON u.id_utilisateur = ru.id_utilisateur
                    LEFT JOIN role r ON ru.id_role = r.id_role
                    GROUP BY u.id_utilisateur, c.id_commande
                    ORDER BY u.nom_utilisateur, c.date_commande DESC";
                    
            error_log("Requête SQL: " . $sql);
            
            error_log("Exécution de la requête SQL");
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                $error = $this->db->errorInfo();
                error_log("Erreur de préparation de la requête: " . print_r($error, true));
                throw new \Exception("Erreur de préparation de la requête SQL");
            }
            
            $result = $stmt->execute();
            
            if ($result === false) {
                $error = $stmt->errorInfo();
                error_log("Erreur d'exécution de la requête: " . print_r($error, true));
                throw new \Exception("Erreur lors de l'exécution de la requête SQL");
            }
            
            // Grouper les commandes par utilisateur
            $users = [];
            $userCount = 0;
            $commandCount = 0;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                try {
                    if (!isset($row['id_utilisateur'])) {
                        error_log("Avertissement: Ligne sans id_utilisateur - " . print_r($row, true));
                        continue;
                    }
                    
                    $userId = $row['id_utilisateur'];
                    
                    if (!isset($users[$userId])) {
                        $userCount++;
                        // Convertir la chaîne de rôles en tableau
                        $roles = [];
                        if (!empty($row['roles'])) {
                            $roles = explode(',', $row['roles']);
                        } elseif (!empty($row['role'])) {
                            $roles = explode(',', $row['role']);
                        }
                        
                        $users[$userId] = [
                            'id_utilisateur' => $row['id_utilisateur'],
                            'nom_utilisateur' => $row['nom_utilisateur'] ?? 'Inconnu',
                            'prenom' => $row['prenom'] ?? '',
                            'couriel' => $row['couriel'] ?? '',
                            'telephone' => $row['telephone'] ?? '',
                            'date_naissance' => $row['date_naissance'] ?? null,
                            'statut' => $row['statut'] ?? 'Inactif',
                            'est_actif' => (bool)($row['est_actif'] ?? false),
                            'role' => in_array('admin', $roles) ? 'admin' : 'user',
                            'roles' => $roles,
                            'commandes' => []
                        ];
                    }
                    
                    // Ajouter la commande si elle existe
                    if (!empty($row['id_commande'])) {
                        $commandCount++;
                        $users[$userId]['commandes'][] = [
                            'id_commande' => $row['id_commande'],
                            'date_commande' => $row['date_commande'] ?? null,
                            'prix_total' => $row['prix_total'] ?? 0,
                            'statut' => $row['statut_commande'] ?? 'inconnu'
                        ];
                    }
                } catch (\Exception $e) {
                    error_log("Erreur lors du traitement d'une ligne de résultat: " . $e->getMessage());
                    error_log("Données problématiques: " . print_r($row, true));
                    continue;
                }
            }
            
            error_log(sprintf("getUsersWithOrders() terminé avec succès - %d utilisateurs, %d commandes", $userCount, $commandCount));
            return array_values($users);
            
        } catch (\PDOException $e) {
            $errorMsg = "Erreur PDO dans getUsersWithOrders(): " . $e->getMessage();
            error_log($errorMsg);
            error_log("Code d'erreur: " . $e->getCode());
            error_log("Fichier: " . $e->getFile() . ", Ligne: " . $e->getLine());
            throw new \Exception("Une erreur est survenue lors de la récupération des utilisateurs. Veuillez réessayer plus tard.");
        } catch (\Exception $e) {
            error_log("Erreur dans getUsersWithOrders(): " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    // Get user information by ID
    public function getUserInfo($id_utilisateur) {
        $sql = "SELECT u.*, a.rue, a.numero, a.ville, a.code_postal, a.province, a.pays
                FROM utilisateur u
                LEFT JOIN utilisateur_adresse ua ON u.id_utilisateur = ua.id_utilisateur
                LEFT JOIN adresse a ON ua.id_adresse = a.id_adresse
                WHERE u.id_utilisateur = :id_utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par email pour la connexion
    public function getElementByEmailForLogin($email) {
        $query = 'SELECT * FROM utilisateur WHERE couriel = :email LIMIT 1'; 
        $stmt = $this->db->prepare($query); 
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // Vérifier les informations d'identification de l'utilisateur
    public function checkUser($email, $password) {
        $query = "
            SELECT 
                u.id_utilisateur, 
                u.nom_utilisateur, 
                u.prenom,
                u.couriel,
                r.description AS role, 
                u.statut, 
                u.mot_de_pass
            FROM utilisateur u
            JOIN role_utilisateur ru ON u.id_utilisateur = ru.id_utilisateur
            JOIN role r ON ru.id_role = r.id_role
            WHERE u.couriel = :email
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier d'abord le mot de passe
            if (password_verify($password, $user['mot_de_pass'])) {
                // Vérifier si le compte est actif
                if (strtolower($user['statut']) !== 'actif') {
                    throw new Exception('Votre compte n\'est pas actif. Veuillez contacter l\'administrateur.');
                }
                
                // Supprimer le mot de passe du tableau retourné pour des raisons de sécurité
                unset($user['mot_de_pass']);
                return $user; // Utilisateur trouvé, mot de passe vérifié et compte actif
            }
            
            // Si on arrive ici, le mot de passe est incorrect
            throw new Exception('Email ou mot de passe incorrect');
        }

        // Si on arrive ici, l'utilisateur n'existe pas
        throw new Exception('Email ou mot de passe incorrect');
    }

    // Fonction pour récupérer les commandes d'un utilisateur avec leurs statuts
    public function getUserCommandWithStatus($userId) {
        $sql = "SELECT * FROM commande WHERE id_utilisateur = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour le statut d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param string $status Nouveau statut ('actif' ou 'inactif')
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public function updateUserStatus($userId, $status) {
        try {
            error_log("Tentative de mise à jour du statut de l'utilisateur ID: $userId, nouveau statut: $status");
            
            $estActif = strtolower($status) === 'actif' ? 1 : 0;
            $statutFormate = ucfirst(strtolower($status));
            
            error_log("Valeurs à mettre à jour - statut: $statutFormate, est_actif: $estActif");
            
            $sql = "UPDATE utilisateur SET statut = :statut, est_actif = :est_actif WHERE id_utilisateur = :id_utilisateur";
            error_log("Requête SQL: $sql");
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':statut', $statutFormate, PDO::PARAM_STR);
            $stmt->bindValue(':est_actif', $estActif, PDO::PARAM_INT);
            $stmt->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            $rowCount = $stmt->rowCount();
            
            error_log("Résultat de l'exécution: " . ($result ? 'succès' : 'échec'));
            error_log("Nombre de lignes affectées: $rowCount");
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur est actif
     * 
     * @param string $email Email de l'utilisateur
     * @return bool True si l'utilisateur est actif, false sinon
     */
    public function isUserActive($email) {
        try {
            $sql = "SELECT statut, est_actif FROM utilisateur WHERE couriel = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $statutActif = isset($result['statut']) && strtolower($result['statut']) === 'actif';
            $estActif = isset($result['est_actif']) && (bool)$result['est_actif'];
            
            return $statutActif && $estActif;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la vérification du statut de l'utilisateur: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère le rôle d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return string|null Le rôle de l'utilisateur ou null si non trouvé
     */
    public function getUserRole($userId) {
        try {
            error_log("=== getUserRole appelé pour l'utilisateur ID: $userId ===");
            
            $sql = "SELECT r.description as role 
                    FROM utilisateur u 
                    JOIN role_utilisateur ur ON u.id_utilisateur = ur.id_utilisateur 
                    JOIN role r ON ur.id_role = r.id_role 
                    WHERE u.id_utilisateur = :userId";
            
            error_log("Exécution de la requête: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Résultat de la requête: " . print_r($result, true));
            
            // Si aucun résultat, vérifier si l'utilisateur existe
            if (!$result) {
                $checkUser = $this->db->prepare("SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur = :userId");
                $checkUser->bindParam(':userId', $userId, PDO::PARAM_INT);
                $checkUser->execute();
                
                if ($checkUser->rowCount() === 0) {
                    error_log("L'utilisateur avec l'ID $userId n'existe pas");
                    return null;
                } else {
                    error_log("L'utilisateur existe mais n'a pas de rôle associé");
                    return 'client'; // Rôle par défaut
                }
            }
            
            return $result ? $result['role'] : 'client'; // Retourne 'client' par défaut si aucun rôle n'est trouvé
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du rôle de l'utilisateur: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les commandes d'un utilisateur avec leurs statuts
     */
    public function getUserOrders($userId) {
        // Préparer la requête SQL
        $sql = "SELECT * FROM commande WHERE id_utilisateur = :userId";
        
        // Préparer la déclaration PDO
        $stmt = $this->db->prepare($sql);
        
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
