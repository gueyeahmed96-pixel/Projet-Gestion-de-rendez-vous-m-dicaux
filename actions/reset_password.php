<?php
// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

// 1. Vérification de la présence du token dans l'URL
if (!isset($_GET['token'])) {
    die("Accès non autorisé : jeton manquant.");
}
$token = $_GET['token']; // Récupération du token depuis l'URL

// Préparation et exécution de la requête pour vérifier la validité du token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
if (!$stmt->fetch()) {
    die("Ce lien de réinitialisation est invalide ou a expiré. Veuillez refaire une demande.");
}
// Si le token est valide, on continue et affiche le formulaire
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et liens CSS -->
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <!-- Intégration de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style CSS personnalisé -->
    <style>body { background-color: #F7F9FC; }</style>
</head>
<body>
    <!-- Conteneur principal centré verticalement et horizontalement -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-5">
            <!-- Carte avec ombre pour le formulaire -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <!-- Titre du formulaire -->
                    <h3 class="text-center mb-4">Choisissez un nouveau mot de passe</h3>

                    <!-- Affichage des erreurs si présentes dans l'URL -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars(urldecode($_GET['error'])) ?></div>
                    <?php endif; ?>

                    <!-- 2. Formulaire de réinitialisation de mot de passe -->
                    <!-- L'action pointe vers le script de traitement avec le token en paramètre -->
                    <form method="POST" action="reset_password_action.php?token=<?= htmlspecialchars($token) ?>">
                        <!-- Champ pour le nouveau mot de passe -->
                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <!-- Champ de confirmation du mot de passe -->
                        <div class="mb-3">
                            <label class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" name="password_confirm" required>
                        </div>
                        <!-- Bouton de soumission -->
                        <button type="submit" class="btn w-100" style="background-color: #25A795; color: white;">Réinitialiser</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>