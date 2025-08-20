<?php
// Initialisation de la session
session_start();

// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

// Vérification que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $mot_de_passe_clair = $_POST['mot_de_passe'];

    try {
        // 1. Tentative de connexion en tant que patient
        $stmt_patient = $pdo->prepare("SELECT * FROM patients WHERE email = ?");
        $stmt_patient->execute([$email]);
        $patient = $stmt_patient->fetch();

        // Vérification du mot de passe pour un patient
        if ($patient && password_verify($mot_de_passe_clair, $patient['mot_de_passe'])) {
            // Création des variables de session pour le patient
            $_SESSION['user_id'] = $patient['id_patient'];
            $_SESSION['user_nom'] = $patient['prenom'] . ' ' . $patient['nom'];
            $_SESSION['user_role'] = 'patient';
            
            // Redirection vers le tableau de bord patient
            header('Location: ../patient/tableau_de_bord.php');
            exit();
        }
        
        // 2. Tentative de connexion en tant que personnel (ou admin)
        $stmt_personnel = $pdo->prepare("SELECT * FROM personnel WHERE email = ?");
        $stmt_personnel->execute([$email]);
        $membre_personnel = $stmt_personnel->fetch();

        // Vérification du mot de passe pour un membre du personnel
        if ($membre_personnel && password_verify($mot_de_passe_clair, $membre_personnel['mot_de_passe'])) {
            // Création des variables de session pour le personnel
            $_SESSION['user_id'] = $membre_personnel['id_personnel'];
            $_SESSION['user_nom'] = $membre_personnel['prenom'] . ' ' . $membre_personnel['nom'];
            $_SESSION['user_role'] = $membre_personnel['role'];

            // Redirection en fonction du rôle
            if ($membre_personnel['role'] === 'admin') {
                // Redirection vers le dashboard admin
                header('Location: ../admin/dashboard.php');
            } else {
                // Redirection vers le planning pour les autres rôles (médecin, secrétaire)
                header('Location: ../personnel/planning.php');
            }
            exit();
        }

        // Si aucune correspondance n'est trouvée, redirection avec message d'erreur
        header('Location: ../index.php?message=Email ou mot de passe incorrect.');
        exit();

    } catch (PDOException $e) {
        // Gestion des erreurs de connexion à la base de données
        die("Erreur de connexion : " . $e->getMessage());
    }
}
?>