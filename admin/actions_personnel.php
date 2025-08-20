<?php
// Démarrage de la session pour accéder aux variables de session
session_start();

// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

// 1. VÉRIFICATION DES DROITS D'ACCÈS
// On vérifie que l'utilisateur est connecté ET qu'il a le rôle 'admin'
// Si non, on arrête l'exécution avec un message d'erreur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Accès non autorisé.");
}

// 2. RÉCUPÉRATION DE L'ACTION À EFFECTUER
// L'action peut venir :
// - D'un formulaire (méthode POST) pour l'ajout ou la modification
// - D'un lien (méthode GET) pour la suppression
// Si aucune action n'est trouvée, on redirige vers la page de gestion
$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!$action) {
    header('Location: gerer_personnel.php'); 
    exit(); // On arrête l'exécution après la redirection
}

// 3. TRAITEMENT DE L'ACTION DEMANDÉE
try {
    // On utilise un switch pour gérer les différentes actions possibles
    switch ($action) {
        // CAS 1 : AJOUT D'UN NOUVEAU MEMBRE DU PERSONNEL
        case 'ajouter':
            // Récupération des données du formulaire
            $nom = $_POST['nom']; 
            $prenom = $_POST['prenom']; 
            $email = $_POST['email'];
            // Hashage sécurisé du mot de passe avant stockage
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $specialite = $_POST['specialite']; 
            $role = $_POST['role'];
            
            // Préparation et exécution de la requête d'insertion
            $stmt = $pdo->prepare("INSERT INTO personnel (nom, prenom, email, mot_de_passe, specialite, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $specialite, $role]);
            
            // Redirection avec message de succès
            header('Location: gerer_personnel.php?message_succes=Membre ajouté avec succès.');
            break;
        
        // CAS 2 : MODIFICATION D'UN MEMBRE EXISTANT
        case 'modifier':
            $id = $_POST['id_personnel']; 
            $nom = $_POST['nom']; 
            $prenom = $_POST['prenom']; 
            $email = $_POST['email'];
            $specialite = $_POST['specialite']; 
            $role = $_POST['role'];
            
            // On vérifie si un nouveau mot de passe a été fourni
            if (!empty($_POST['mot_de_passe'])) {
                // Si oui, on le hash et on met à jour tous les champs
                $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE personnel SET nom=?, prenom=?, email=?, mot_de_passe=?, specialite=?, role=? WHERE id_personnel = ?");
                $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $specialite, $role, $id]);
            } else {
                // Si non, on met à jour tous les champs sauf le mot de passe
                $stmt = $pdo->prepare("UPDATE personnel SET nom=?, prenom=?, email=?, specialite=?, role=? WHERE id_personnel = ?");
                $stmt->execute([$nom, $prenom, $email, $specialite, $role, $id]);
            }
            
            // Redirection avec message de succès
            header('Location: gerer_personnel.php?message_succes=Membre mis à jour avec succès.');
            break;
        
        // CAS 3 : SUPPRESSION D'UN MEMBRE
        case 'supprimer':
            // Récupération de l'ID depuis l'URL
            $id = $_GET['id'];
            
            // Préparation et exécution de la requête de suppression
            $stmt = $pdo->prepare("DELETE FROM personnel WHERE id_personnel = ?");
            $stmt->execute([$id]);
            
            // Redirection avec message de succès
            header('Location: gerer_personnel.php?message_succes=Membre supprimé avec succès.');
            break;
        
        // CAS PAR DÉFAUT : SI L'ACTION N'EST PAS RECONNUE
        default:
            header('Location: gerer_personnel.php');
            break;
    }
} catch (PDOException $e) {
    // Gestion des erreurs de base de données
    die("Erreur de base de données : " . $e->getMessage());
}
?>