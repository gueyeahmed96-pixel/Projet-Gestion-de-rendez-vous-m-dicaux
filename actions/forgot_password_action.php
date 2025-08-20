<?php
// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

// Vérification que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération de l'email depuis le formulaire
    $email = $_POST['email'];

    // ÉTAPE 1: VÉRIFICATION DE L'EXISTENCE DE L'EMAIL
    // --------------------------------------------------
    // Préparation et exécution de la requête pour vérifier l'email dans la table patients
    $stmt_patient = $pdo->prepare("SELECT email FROM patients WHERE email = ?");
    $stmt_patient->execute([$email]);
    
    // Préparation et exécution de la requête pour vérifier l'email dans la table personnel
    $stmt_personnel = $pdo->prepare("SELECT email FROM personnel WHERE email = ?");
    $stmt_personnel->execute([$email]);

    // Vérification si l'email existe dans au moins une des tables
    if ($stmt_patient->fetch() || $stmt_personnel->fetch()) {
        // ÉTAPE 2: CRÉATION DU TOKEN ET DATE D'EXPIRATION
        // --------------------------------------------------
        // Génération d'un token sécurisé (50 octets convertis en hexadécimal)
        $token = bin2hex(random_bytes(50));
        
        // Création de la date d'expiration (1 heure à partir de maintenant)
        $expires = new DateTime('NOW');
        $expires->add(new DateInterval('PT1H')); // PT1H = Période de Temps 1 Heure

        // ÉTAPE 3: ENREGISTREMENT DE LA DEMANDE DE RÉINITIALISATION
        // --------------------------------------------------
        // Préparation et exécution de la requête d'insertion dans la table password_resets
        $stmt_insert = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt_insert->execute([
            $email, 
            $token, 
            $expires->format('Y-m-d H:i:s') // Formatage de la date pour MySQL
        ]);

        // ÉTAPE 4: PRÉPARATION DU LIEN DE RÉINITIALISATION
        // --------------------------------------------------
        // Construction du lien de réinitialisation (à adapter selon l'environnement)
        $reset_link = "http://localhost/Projet_Gestion_rdv_médicaux/actions/reset_password.php?token=" . $token;
        
        // Message de simulation (remplacé par un vrai email en production)
        $message_simulation = "<strong>Simulation d'envoi d'email :</strong><br>"
                           . "Cliquez sur ce lien pour réinitialiser votre mot de passe : <br>"
                           . "<a href='{$reset_link}' class='btn btn-success mt-2'>Réinitialiser</a>";
        
        // Redirection avec le message de simulation
        header('Location: forgot_password.php?message=' . urlencode($message_simulation));
        exit();
    } else {
        // Cas où l'email n'existe pas dans la base
        // Message volontairement vague pour ne pas révéler si l'email existe
        header('Location: actions/forgot_password.php?message=' . urlencode('Si un compte est associé à cet email, un lien a été envoyé.'));
        exit();
    }
}
?>