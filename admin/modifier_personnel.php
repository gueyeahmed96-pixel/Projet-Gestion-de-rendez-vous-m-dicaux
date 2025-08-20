<?php
/* **************************************
 * INITIALISATION ET CONTRÔLE D'ACCÈS
 * **************************************/

// Démarrage de la session pour accéder aux variables $_SESSION
session_start();

// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

/**
 * VÉRIFICATION DES DROITS ADMIN
 * - Vérifie que l'utilisateur est connecté
 * - Vérifie que son rôle est 'admin'
 * - Vérifie qu'un ID valide est fourni en paramètre
 * Si une condition n'est pas remplie, redirection vers l'accueil
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php'); 
    exit();
}

// Récupération sécurisée de l'ID du membre
$id_membre = $_GET['id'];

/* **************************************
 * RÉCUPÉRATION DES DONNÉES
 * **************************************/

// 1. Récupération des informations du membre
try {
    $stmt = $pdo->prepare("SELECT * FROM personnel WHERE id_personnel = ?");
    $stmt->execute([$id_membre]);
    $membre = $stmt->fetch();
    
    // Si aucun membre trouvé, redirection avec message d'erreur
    if (!$membre) { 
        header('Location: gerer_personnel.php?message_erreur=Membre introuvable.'); 
        exit(); 
    }
} catch (PDOException $e) { 
    die("Erreur: " . $e->getMessage()); 
}

// 2. Récupération de la liste des spécialités pour le menu déroulant
try {
    $stmt_all_spec = $pdo->query("SELECT nom_specialite FROM specialites ORDER BY nom_specialite ASC");
    $liste_specialites = $stmt_all_spec->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $liste_specialites = [];
    die("Erreur: Impossible de charger la liste des spécialités. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Configuration de base -->
    <meta charset="UTF-8">
    <title>Modifier un Membre - Admin</title>
    
    <!-- Inclusion des CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles personnalisés -->
    <style>
        :root {
            --primary-blue: #154784;  /* Couleur principale */
            --light-gray-bg: #F7F9FC; /* Couleur de fond */
        } 
        body {
            background-color: var(--light-gray-bg); 
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
<!-- **************************************
     STRUCTURE PRINCIPALE
     ************************************** -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête avec nom du membre -->
            <h1 class="mb-4" style="color: var(--primary-blue);">
                Modifier : <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?>
            </h1>
            
            <!-- Carte contenant le formulaire -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Formulaire de modification -->
                    <form action="actions_personnel.php" method="POST">
                        <!-- Champs cachés pour l'action et l'ID -->
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id_personnel" value="<?= $membre['id_personnel'] ?>">
                        
                        <!-- Ligne avec prénom et nom -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" 
                                       value="<?= htmlspecialchars($membre['prenom']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" 
                                       value="<?= htmlspecialchars($membre['nom']) ?>" required>
                            </div>
                        </div>
                        
                        <!-- Champ email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($membre['email']) ?>" required>
                        </div>
                        
                        <!-- Champ mot de passe (optionnel) -->
                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" 
                                   placeholder="Laisser vide pour ne pas changer">
                        </div>
                        
                        <!-- Menu déroulant des spécialités -->
                        <div class="mb-3">
                            <label class="form-label">Spécialité</label>
                            <select name="specialite" class="form-select" required>
                                <!-- Options des spécialités -->
                                <?php foreach ($liste_specialites as $spe): ?>
                                    <option value="<?= htmlspecialchars($spe) ?>" 
                                        <?= ($membre['specialite'] === $spe) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($spe) ?>
                                    </option>
                                <?php endforeach; ?>
                                <!-- Option pour les rôles sans spécialité -->
                                <option value="N/A" <?= ($membre['specialite'] === 'N/A') ? 'selected' : '' ?>>
                                    Non Applicable (ex: Secrétaire)
                                </option>
                            </select>
                        </div>
                        
                        <!-- Menu déroulant des rôles -->
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select" required>
                                <option value="medecin" <?= $membre['role'] === 'medecin' ? 'selected' : '' ?>>
                                    Médecin
                                </option>
                                <option value="secretaire" <?= $membre['role'] === 'secretaire' ? 'selected' : '' ?>>
                                    Secrétaire
                                </option>
                                <option value="admin" <?= $membre['role'] === 'admin' ? 'selected' : '' ?>>
                                    Administrateur
                                </option>
                            </select>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="gerer_personnel.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>