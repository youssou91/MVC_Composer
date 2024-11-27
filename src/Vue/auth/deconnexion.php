<?php

session_start();
session_unset();
// session_destroy();

header("Location: /connexion.php");
// require_once __DIR__ . '/../home.php'; // Exemple de chemin vers la vue

exit();
