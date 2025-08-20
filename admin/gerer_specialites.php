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
 * - Vérifie que l'utilisateur est connecté (session active)
 * - Vérifie que son rôle est bien 'admin'
 * Si non, redirection vers la page d'accueil
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); 
    exit();
}

/* **************************************
 * RÉCUPÉRATION DES DONNÉES
 * **************************************/

try {
    // Requête pour obtenir toutes les spécialités triées par ordre alphabétique
    $stmt = $pdo->query("SELECT * FROM specialites ORDER BY nom_specialite ASC");
    $specialites = $stmt->fetchAll();
} catch (PDOException $e) { 
    die("Erreur: " . $e->getMessage()); // Arrêt en cas d'erreur SQL
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Configuration de base -->
    <meta charset="UTF-8">
    <title>Gérer les Spécialités - Admin</title>
    
    <!-- Inclusion des CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles personnalisés -->
    <style>
        :root { 
            --primary-blue: #154784;    /* Couleur principale */
            --light-gray-bg: #F7F9FC;   /* Fond clair */
            --sidebar-width: 280px;     /* Largeur sidebar */
        }
        
        body { 
            background-color: var(--light-gray-bg); 
            font-family: 'Poppins', sans-serif; 
        }
        
        /* Structure de la sidebar */
        #sidebar { 
            min-width: var(--sidebar-width); 
            background: #212529; 
            color: white; 
        }
        
        /* Style des liens actifs */
        #sidebar .nav-link.active { 
            background-color: var(--primary-blue); 
            color: white; 
        }
    </style>
</head>

<body>
<!-- **************************************
     STRUCTURE PRINCIPALE
     ************************************** -->
<div class="wrapper">
    <!-- ==============================
         SIDEBAR DE NAVIGATION 
         ============================== -->
    <nav id="sidebar" class="d-flex flex-column p-3">
        <div class="sidebar-header">
            <h3>Administration</h3>
        </div>
        
        <!-- Menu de navigation -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link">
                    <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="gerer_personnel.php" class="nav-link">
                    <i class="bi bi-people-fill me-2"></i>Gérer le Personnel
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="gerer_specialites.php" class="nav-link active">
                    <i class="bi bi-card-checklist me-2"></i>Gérer Spécialités
                </a>
            </li>
        </ul>
        
        <!-- Bouton de déconnexion -->
        <div class="mt-auto p-3 text-center">
            <a href="../actions/deconnexion.php" class="btn btn-outline-light w-100">
                Se déconnecter
            </a>
        </div>
    </nav>

    <!-- ==============================
         CONTENU PRINCIPAL
         ============================== -->
    <div class="main-content">
        <!-- En-tête de page -->
        <h1>Gérer les Spécialités</h1>
        <p class="text-muted">
            Ajoutez, modifiez ou supprimez les spécialités proposées par la clinique.
        </p>
        
        <!-- Affichage des messages de feedback -->
        <?php if(isset($_GET['message_succes'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['message_succes']) ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['message_erreur'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['message_erreur']) ?>
            </div>
        <?php endif; ?>

        <!-- Grille à 2 colonnes -->
        <div class="row">
            <!-- Colonne principale : Liste des spécialités -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom de la spécialité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Boucle sur chaque spécialité -->
                                <?php foreach ($specialites as $spe): ?>
                                <tr>
                                    <!-- Nom de la spécialité -->
                                    <td><?= htmlspecialchars($spe['nom_specialite']) ?></td>
                                    
                                    <!-- Actions -->
                                    <td>
                                        <!-- Formulaire de modification -->
                                        <form action="actions_specialites.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="modifier">
                                            <input type="hidden" name="id_specialite" value="<?= $spe['id_specialite'] ?>">
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                       name="nom_specialite" 
                                                       value="<?= htmlspecialchars($spe['nom_specialite']) ?>" 
                                                       required>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-save-fill"></i>
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <!-- Bouton de suppression -->
                                        <a href="actions_specialites.php?action=supprimer&id=<?= $spe['id_specialite'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Attention ! Supprimer cette spécialité la retirera de tous les médecins associés. Êtes-vous sûr ?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Colonne secondaire : Formulaire d'ajout -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        Ajouter une nouvelle spécialité
                    </div>
                    <div class="card-body">
                        <form action="actions_specialites.php" method="POST">
                            <input type="hidden" name="action" value="ajouter">
                            <div class="mb-3">
                                <label class="form-label">Nom de la spécialité</label>
                                <input type="text" name="nom_specialite" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                Ajouter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>