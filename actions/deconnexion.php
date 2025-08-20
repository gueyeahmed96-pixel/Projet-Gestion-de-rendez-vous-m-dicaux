<?php
// Démarrage de la session pour accéder aux variables de session
session_start();

// 1. VIDER LES DONNÉES DE SESSION
// Réinitialisation du tableau de session (supprime toutes les variables de session)
$_SESSION = array();

// 2. SUPPRESSION DU COOKIE DE SESSION
// Vérification si l'utilisation des cookies est activée dans la configuration PHP
if (ini_get("session.use_cookies")) {
    // Récupération des paramètres du cookie de session
    $params = session_get_cookie_params();
    
    // Création d'un cookie expiré (pour forcer sa suppression côté client)
    setcookie(
        session_name(),        // Nom du cookie de session (généralement 'PHPSESSID')
        '',                    // Valeur vide
        time() - 42000,        // Date d'expiration dans le passé
        $params["path"],       // Chemin du cookie
        $params["domain"],     // Domaine du cookie
        $params["secure"],     // Option secure (HTTPS)
        $params["httponly"]    // Option httpOnly (protection contre XSS)
    );
}

// 3. DESTRUCTION DE LA SESSION COTÉ SERVEUR
session_destroy();

// 4. REDIRECTION VERS LA PAGE D'ACCUEIL
// Avec un message de confirmation de déconnexion
header('Location: ../index.php?message=Vous avez été déconnecté.');

// Arrêt de l'exécution du script pour éviter toute continuation indésirable
exit();
?>