<?php
// Démarrage de la session pour accéder aux variables $_SESSION
session_start();

// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

/**
 * VÉRIFICATION DES PERMISSIONS ADMIN
 * On vérifie que :
 * 1. L'utilisateur est connecté (user_id existe dans la session)
 * 2. Son rôle est bien 'admin'
 * Si une de ces conditions n'est pas remplie, on bloque l'accès
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Accès non autorisé.");
}

/**
 * RÉCUPÉRATION DE L'ACTION À EFFECTUER
 * On cherche l'action dans :
 * - $_POST (pour les formulaires)
 * - $_GET (pour les liens)
 * Si aucune action n'est trouvée, on utilise null
 */
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Si aucune action n'est spécifiée, on redirige vers la page de gestion
if (!$action) {
    header('Location: gerer_specialites.php'); 
    exit(); // On arrête immédiatement l'exécution du script
}

// Début du bloc try-catch pour gérer les erreurs potentielles
try {
    /**
     * TRAITEMENT EN FONCTION DE L'ACTION
     * On utilise une structure switch pour gérer les différents cas
     */
    switch ($action) {
        // CAS 1 : AJOUT D'UNE NOUVELLE SPÉCIALITÉ
        case 'ajouter':
            // On nettoie le nom en supprimant les espaces superflus
            $nom = trim($_POST['nom_specialite']);
            
            // On vérifie que le nom n'est pas vide après nettoyage
            if (!empty($nom)) {
                // Préparation de la requête SQL avec un paramètre
                $stmt = $pdo->prepare("INSERT INTO specialites (nom_specialite) VALUES (?)");
                // Exécution avec le nom nettoyé
                $stmt->execute([$nom]);
                // Redirection avec message de succès
                header('Location: gerer_specialites.php?message_succes=Spécialité ajoutée.');
            } else {
                // Redirection avec message d'erreur si nom vide
                header('Location: gerer_specialites.php?message_erreur=Le nom ne peut pas être vide.');
            }
            break;
        
        // CAS 2 : MODIFICATION D'UNE SPÉCIALITÉ EXISTANTE
        case 'modifier':
            // Récupération de l'ID et nettoyage du nouveau nom
            $id = $_POST['id_specialite'];
            $nom = trim($_POST['nom_specialite']);
            
            // Vérification que le nom n'est pas vide
            if (!empty($nom)) {
                // Préparation de la requête de mise à jour
                $stmt = $pdo->prepare("UPDATE specialites SET nom_specialite = ? WHERE id_specialite = ?");
                // Exécution avec le nouveau nom et l'ID
                $stmt->execute([$nom, $id]);
                // Redirection avec message de succès
                header('Location: gerer_specialites.php?message_succes=Spécialité mise à jour.');
            } else {
                // Redirection avec message d'erreur si nom vide
                header('Location: gerer_specialites.php?message_erreur=Le nom ne peut pas être vide.');
            }
            break;
        
        // CAS 3 : SUPPRESSION D'UNE SPÉCIALITÉ
        case 'supprimer':
            // Récupération de l'ID depuis les paramètres GET
            $id = $_GET['id'];
            // Préparation de la requête de suppression
            $stmt = $pdo->prepare("DELETE FROM specialites WHERE id_specialite = ?");
            // Exécution avec l'ID
            $stmt->execute([$id]);
            // Redirection avec message de succès
            header('Location: gerer_specialites.php?message_succes=Spécialité supprimée.');
            break;
    }
} catch (PDOException $e) {
    /**
     * GESTION DES ERREURS
     * On vérifie si c'est une violation de contrainte d'unicité (code 23000)
     */
    if ($e->getCode() == 23000) {
        // Redirection avec message d'erreur spécifique pour les doublons
        header('Location: gerer_specialites.php?message_erreur=Cette spécialité existe déjà.');
    } else {
        // Pour les autres erreurs, affichage du message et arrêt du script
        die("Erreur de base de données : " . $e->getMessage());
    }
}
?>