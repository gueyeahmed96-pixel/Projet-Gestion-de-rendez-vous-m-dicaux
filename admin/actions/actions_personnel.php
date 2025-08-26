<?php
session_start();
require_once '../../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') { die("Accès non autorisé."); }

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!$action) { header('Location: ../gerer_personnel.php'); exit(); }

try {
    switch ($action) {
        case 'ajouter':
            $nom = $_POST['nom']; $prenom = $_POST['prenom']; $email = $_POST['email'];
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $id_specialite = !empty($_POST['id_specialite']) ? $_POST['id_specialite'] : null;
            $role = $_POST['role'];
            
            $stmt = $pdo->prepare("INSERT INTO personnel (nom, prenom, email, mot_de_passe, id_specialite_fk, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $id_specialite, $role]);
            
            header('Location: ../gerer_personnel.php?message_succes=Membre ajouté.');
            break;
        
        case 'modifier':
            $id = $_POST['id_personnel']; $nom = $_POST['nom']; $prenom = $_POST['prenom']; $email = $_POST['email'];
            $id_specialite = !empty($_POST['id_specialite_fk']) ? $_POST['id_specialite_fk'] : null;
            $role = $_POST['role'];
            
            if (!empty($_POST['mot_de_passe'])) {
                $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE personnel SET nom=?, prenom=?, email=?, mot_de_passe=?, id_specialite_fk=?, role=? WHERE id_personnel = ?");
                $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $id_specialite, $role, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE personnel SET nom=?, prenom=?, email=?, id_specialite_fk=?, role=? WHERE id_personnel = ?");
                $stmt->execute([$nom, $prenom, $email, $id_specialite, $role, $id]);
            }
            
            header('Location: ../gerer_personnel.php?message_succes=Membre mis à jour.');
            break;
        
        case 'supprimer':
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM personnel WHERE id_personnel = ?");
            $stmt->execute([$id]);
            header('Location: ../gerer_personnel.php?message_succes=Membre supprimé.');
            break;
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header('Location: ../gerer_personnel.php?message_erreur=' . urlencode('Cet email existe déjà.'));
    } else {
        die("Erreur de base de données : " . $e->getMessage());
    }
}
?>