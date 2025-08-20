<?php
// On inclut la connexion à la base de données. '../' est nécessaire car ce script est dans le sous-dossier /actions/
require_once '../includes/db_connect.php';

// Étape 1 : Vérifier que les données nécessaires (jeton, mot de passe) sont bien présentes
if (!isset($_GET['token']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password']) || !isset($_POST['password_confirm'])) {
    // Si des informations manquent, on redirige vers l'accueil. C'est une tentative d'accès anormale.
    header('Location: ../index.php');
    exit();
}

// Étape 2 : Récupérer et nettoyer les données
$token = $_GET['token'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];

// Étape 3 : Valider le jeton. Est-il dans la base de données ET n'est-il pas expiré ?
try {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset_request = $stmt->fetch();

    if (!$reset_request) {
        // Si le jeton est invalide ou expiré, on redirige vers la page de demande avec un message clair.
        header('Location: forgot_password.php?message=' . urlencode('Ce lien de réinitialisation est invalide ou a expiré. Veuillez refaire une demande.'));
        exit();
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Étape 4 : Valider les nouveaux mots de passe
if (strlen($password) < 6) {
    $error = "Le mot de passe doit contenir au moins 6 caractères.";
    // On redirige vers la page du formulaire en passant le message d'erreur et le jeton
    header('Location: reset_password.php?token=' . $token . '&error=' . urlencode($error));
    exit();
}
if ($password !== $password_confirm) {
    $error = "Les deux mots de passe ne correspondent pas.";
    header('Location: reset_password.php?token=' . $token . '&error=' . urlencode($error));
    exit();
}

// Si toutes les validations sont passées, on peut procéder à la mise à jour
try {
    // Étape 5 : Hacher le nouveau mot de passe
    $email_to_update = $reset_request['email'];
    $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Étape 6 : Mettre à jour le mot de passe dans la bonne table (patients OU personnel)
    // On peut faire les deux mises à jour, une seule affectera une ligne.
    $stmt_update_patient = $pdo->prepare("UPDATE patients SET mot_de_passe = ? WHERE email = ?");
    $stmt_update_patient->execute([$new_hashed_password, $email_to_update]);
    
    $stmt_update_personnel = $pdo->prepare("UPDATE personnel SET mot_de_passe = ? WHERE email = ?");
    $stmt_update_personnel->execute([$new_hashed_password, $email_to_update]);
    
    // Étape 7 : Supprimer le jeton de la base de données pour qu'il ne puisse pas être réutilisé
    $stmt_delete = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt_delete->execute([$token]);
    
    // Étape 8 : Rediriger l'utilisateur vers la page de connexion avec un message de succès
    header('Location: ../index.php?message=' . urlencode('Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.'));
    exit();

} catch (PDOException $e) {
    die("Une erreur est survenue lors de la mise à jour de votre mot de passe : " . $e->getMessage());
}