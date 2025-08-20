<?php
/**
 * FICHIER : confirmation_rendezvous.php
 * BUT : Traiter la confirmation d'un rendez-vous médical
 * PRÉ-REQUIS :
 * - Session utilisateur active
 * - Connexion à la base de données établie
 */

// 1. INITIALISATION DE LA SESSION ET CONNEXION À LA BDD
session_start();
require_once '../includes/db_connect.php';

// 2. VÉRIFICATION DES DROITS D'ACCÈS
// Seuls les patients connectés peuvent accéder à cette page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');
    exit();
}

// 3. VÉRIFICATION DES PARAMÈTRES REQUIS
// On vérifie que les données nécessaires sont présentes dans l'URL
if (!isset($_GET['datetime']) || !isset($_GET['medecin'])) {
    header('Location: prendre_rdv.php');
    exit();
}

// 4. RÉCUPÉRATION ET VALIDATION DES DONNÉES
$date_heure_rdv = $_GET['datetime'];  // Date et heure du rendez-vous
$id_medecin = $_GET['medecin'];       // ID du médecin sélectionné
$id_patient = $_SESSION['user_id'];   // ID du patient connecté

// 5. ENREGISTREMENT DU RENDEZ-VOUS EN BASE DE DONNÉES
try {
    // Préparation de la requête SQL avec des paramètres sécurisés
    $sql = "INSERT INTO rendez_vous 
            (date_heure_rdv, id_patient_fk, id_personnel_fk, statut) 
            VALUES (?, ?, ?, 'Confirmé')";
    
    $stmt = $pdo->prepare($sql);
    
    // Exécution avec les paramètres
    $stmt->execute([$date_heure_rdv, $id_patient, $id_medecin]);
    
    // 6. REDIRECTION AVEC MESSAGE DE SUCCÈS
    header('Location: tableau_de_bord.php?message_succes=Votre rendez-vous a bien été confirmé !');
    exit();

} catch (PDOException $e) {
    // En cas d'erreur SQL, affichage d'un message clair
    die("Erreur lors de la prise de rendez-vous : " . $e->getMessage());
}