<?php
// Inclure la connexion à la base de données
include 'db.php'; // Remplacez cela par votre propre fichier de connexion PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées via POST
    $id_produit = $_POST['id_produit'];
    $valeur = $_POST['valeur'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Valider les données (optionnel, selon vos besoins)
    if (empty($id_produit) || empty($valeur) || empty($date_debut) || empty($date_fin)) {
        // Redirection avec un message d'erreur si un champ est manquant
        header("Location: add_promotion_form.php?error=1");
        exit;
    }

    // Préparer la requête SQL pour insérer la promotion
    $query = "INSERT INTO promotions (id_produit, valeur, date_debut, date_fin) VALUES (:id_produit, :valeur, :date_debut, :date_fin)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_produit', $id_produit, PDO::PARAM_INT);
    $stmt->bindParam(':valeur', $valeur, PDO::PARAM_INT);
    $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
    $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);

    // Exécuter la requête
    if ($stmt->execute()) {
        // Si l'insertion réussit, rediriger vers la page des promotions ou afficher un message de succès
        header("Location: promotions.php?success=1");
        exit;
    } else {
        // Si l'insertion échoue, rediriger avec un message d'erreur
        header("Location: add_promotion_form.php?error=2");
        exit;
    }
}
?>
