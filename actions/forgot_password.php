<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- SECTION META & TITRE -->
    <!-- -------------------------------------------------- -->
    <meta charset="UTF-8">
    <title>Mot de passe oublié - Système de gestion médicale</title>
    
    <!-- SECTION CSS -->
    <!-- -------------------------------------------------- -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Styles personnalisés -->
    <style>
        /* Fond de page léger */
        body { 
            background-color: #F7F9FC; 
        }
    </style>
</head>

<body>
    <!-- SECTION PRINCIPALE -->
    <!-- -------------------------------------------------- -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-5">
            <!-- Carte contenant le formulaire -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    
                    <!-- EN-TÊTE DU FORMULAIRE -->
                    <!-- -------------------------------------------------- -->
                    <div class="text-center mb-4">
                        <!-- Icône visuelle -->
                        <i class="bi bi-key-fill" style="font-size: 3rem; color: #154784;"></i>
                        <!-- Titre -->
                        <h3 class="mt-3">Mot de passe oublié ?</h3>
                        <!-- Instructions -->
                        <p class="text-muted">Entrez votre email et nous vous enverrons un lien pour le réinitialiser.</p>
                    </div>
                    
                    <!-- SECTION MESSAGES D'ALERTE -->
                    <!-- -------------------------------------------------- -->
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-info">
                            <!-- Affichage des messages retournés par le serveur -->
                            <?= urldecode($_GET['message']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- FORMULAIRE DE DEMANDE -->
                    <!-- -------------------------------------------------- -->
                    <form action="forgot_password_action.php" method="POST">
                        <!-- Champ Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <!-- Bouton de soumission -->
                        <button type="submit" class="btn w-100" style="background-color: #25A795; color: white;">
                            Envoyer le lien
                        </button>
                    </form>
                    
                    <!-- LIEN DE RETOUR -->
                    <!-- -------------------------------------------------- -->
                    <div class="text-center mt-3">
                        <a href="index.php">Retour à la connexion</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>