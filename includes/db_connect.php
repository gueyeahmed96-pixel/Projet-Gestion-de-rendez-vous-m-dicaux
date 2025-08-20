<?php
/* **************************************
 * CONFIGURATION DE LA BASE DE DONNÉES
 * **************************************/

// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost'); // Serveur de base de données
define('DB_NAME', 'gestion_de_rendez-vous_médicaux'); // Nom de la base de données
define('DB_USER', 'root'); // Nom d'utilisateur MySQL
define('DB_PASS', ''); // Mot de passe MySQL (vide par défaut en local)

// URL de base du projet pour les liens absolus
define('BASE_URL', 'http://localhost/Projet_Gestion_rdv_médicaux/');

/* **************************************
 * CONNEXION À LA BASE DE DONNÉES
 * **************************************/

try {
    // Création de l'instance PDO pour la connexion MySQL
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    
    // Configuration des attributs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gestion des erreurs sous forme d'exceptions
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Format de récupération des données par défaut
    
} catch (PDOException $e) {
    // En cas d'échec de connexion, affichage d'un message d'erreur clair
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}