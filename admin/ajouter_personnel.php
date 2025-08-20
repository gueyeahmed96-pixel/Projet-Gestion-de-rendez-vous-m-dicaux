<?php
// Démarrage de la session pour accéder aux variables de session
session_start();

// 1. CONNEXION À LA BASE DE DONNÉES
// Inclusion du fichier de connexion qui initialise l'objet $pdo
require_once '../includes/db_connect.php';

// 2. VÉRIFICATION DES DROITS ADMIN
// On vérifie que l'utilisateur est connecté ET qu'il a le rôle admin
// Si non, on redirige vers la page d'accueil et on arrête le script
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); 
    exit();
}

// 3. RÉCUPÉRATION DE LA LISTE DES SPÉCIALITÉS
try {
    // Requête SQL pour obtenir toutes les spécialités par ordre alphabétique
    $stmt = $pdo->query("SELECT nom_specialite FROM specialites ORDER BY nom_specialite ASC");
    // Récupération sous forme de tableau simple (juste les noms)
    $liste_specialites = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    // En cas d'erreur SQL, on initialise un tableau vide et on affiche l'erreur
    $liste_specialites = [];
    die("Erreur: Impossible de charger la liste des spécialités. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Configuration de base de la page -->
    <meta charset="UTF-8">
    <title>Ajouter un Membre</title>
    
    <!-- Inclusion des feuilles de style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles CSS personnalisés -->
    <style>
        :root {
            --primary-blue: #154784;  /* Couleur principale bleue */
            --light-gray-bg: #F7F9FC; /* Couleur de fond gris clair */
        } 
        body {
            background-color: var(--light-gray-bg); 
            font-family: 'Poppins', sans-serif; /* Police Poppins pour tout le texte */
        }
    </style>
</head>
<body>
<!-- Structure principale de la page -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Titre de la page -->
            <h1 class="mb-4" style="color: var(--primary-blue);">Ajouter un nouveau membre</h1>
            
            <!-- Carte contenant le formulaire -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Formulaire d'ajout qui envoie vers actions_personnel.php -->
                    <form action="actions_personnel.php" method="POST">
                        <!-- Champ caché pour identifier l'action -->
                        <input type="hidden" name="action" value="ajouter">
                        
                        <!-- Ligne avec prénom et nom -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                        </div>
                        
                        <!-- Champ email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <!-- Champ mot de passe provisoire -->
                        <div class="mb-3">
                            <label class="form-label">Mot de passe (provisoire)</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        
                        <!-- Liste déroulante des spécialités -->
                        <div class="mb-3">
                            <label class="form-label">Spécialité</label>
                            <select name="specialite" class="form-select" required>
                                <option value="" disabled selected>-- Choisir une spécialité --</option>
                                <!-- Boucle PHP pour générer les options à partir de la BDD -->
                                <?php foreach ($liste_specialites as $spe): ?>
                                    <!-- htmlspecialchars pour la sécurité XSS -->
                                    <option value="<?= htmlspecialchars($spe) ?>"><?= htmlspecialchars($spe) ?></option>
                                <?php endforeach; ?>
                                <!-- Option pour les rôles sans spécialité -->
                                <option value="N/A">Non Applicable (ex: Secrétaire)</option>
                            </select>
                        </div>
                        
                        <!-- Liste déroulante des rôles -->
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select" required>
                                <option value="medecin">Médecin</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        
                        <!-- Boutons de soumission et d'annulation -->
                        <button type="submit" class="btn btn-success">Ajouter le membre</button>
                        <a href="gerer_personnel.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>