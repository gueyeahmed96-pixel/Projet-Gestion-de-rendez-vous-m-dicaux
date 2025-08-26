<?php
session_start();
// Le chemin vers db_connect.php est maintenant '../' car on a descendu d'un niveau
require_once '../../includes/db_connect.php';

// Sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Accès non autorisé."); 
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;
// La redirection pointe vers le bon fichier, en remontant de deux niveaux (../)
if (!$action) {
    header('Location: ../gerer_specialites.php');
    exit();
}

try {
    switch ($action) {
        case 'ajouter':
            // On lit bien la variable du formulaire : nom_specialite
            $nom = trim($_POST['nom_specialite']);
            if (!empty($nom)) {
                // On insère dans la bonne colonne de la BDD : nom_specialite
                $stmt = $pdo->prepare("INSERT INTO specialites (nom_specialite) VALUES (?)");
                $stmt->execute([$nom]);
                header('Location: ../gerer_specialites.php?message_succes=Spécialité ajoutée.');
            } else {
                header('Location: ../gerer_specialites.php?message_erreur=Le nom ne peut pas être vide.');
            }
            break;
        
        case 'modifier':
            $id = $_POST['id_specialite'];
            // On lit bien la variable du formulaire : nom_specialite
            $nom = trim($_POST['nom_specialite']);
            if (!empty($nom) && !empty($id)) {
                // On met à jour la bonne colonne : nom_specialite
                $stmt = $pdo->prepare("UPDATE specialites SET nom_specialite = ? WHERE id_specialite = ?");
                $stmt->execute([$nom, $id]);
                header('Location: ../gerer_specialites.php?message_succes=Spécialité mise à jour.');
            } else {
                header('Location: ../gerer_specialites.php?message_erreur=Informations incorrectes.');
            }
            break;
        
        case 'supprimer':
            $id = $_GET['id'];
            if (!empty($id)) {
                $stmt = $pdo->prepare("DELETE FROM specialites WHERE id_specialite = ?");
                $stmt->execute([$id]);
                header('Location: ../gerer_specialites.php?message_succes=Spécialité supprimée.');
            }
            break;
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header('Location: ../gerer_specialites.php?message_erreur=' . urlencode('Cette spécialité existe déjà.'));
    } elseif ($e->getCode() == 1451) { 
        header('Location: ../gerer_specialites.php?message_erreur=' . urlencode('Erreur : Impossible de supprimer, cette spécialité est utilisée.'));
    } else {
        die("Erreur de base de données : " . $e->getMessage());
    }
}
exit();
?>