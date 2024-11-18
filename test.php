<?php
echo "Avant la connexion à la base de données...<br>";
$dsn = "mysql:host=localhost;dbname=cours343;port=3306;charset=utf8";
$username = "dev434";
$password = "dev434";

try {
    $pdo = new PDO($dsn, $username, $password);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>