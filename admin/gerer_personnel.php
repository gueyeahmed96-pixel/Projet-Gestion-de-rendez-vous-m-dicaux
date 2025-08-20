<?php
/* **************************
 * INITIALISATION ET SÉCURITÉ
 * **************************/

// Démarrage de la session pour accéder aux variables $_SESSION
session_start();

// Inclusion des fichiers nécessaires
require_once '../includes/db_connect.php';  // Connexion à la base de données
require_once '../includes/tracker.php';     // Système de suivi d'activité

/**
 * VÉRIFICATION DES DROITS ADMIN
 * - Vérifie que l'utilisateur est connecté (user_id existe)
 * - Vérifie que son rôle est bien 'admin'
 * Si non, redirection vers la page d'accueil
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); 
    exit();
}

/* **************************
 * RÉCUPÉRATION DES DONNÉES
 * **************************/

try {
    // Requête pour obtenir la liste complète du personnel triée par nom/prénom
    $stmt = $pdo->query("SELECT * FROM personnel ORDER BY nom, prenom");
    $personnels = $stmt->fetchAll();
} catch (PDOException $e) { 
    die("Erreur: " . $e->getMessage()); // Arrêt en cas d'erreur SQL
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Configuration de base -->
    <meta charset="UTF-8">
    <title>Gérer le Personnel - Admin</title>
    
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
<!-- **************************
     STRUCTURE PRINCIPALE
     ************************** -->
<div class="wrapper">
    <!-- ======================
         SIDEBAR DE NAVIGATION 
         ====================== -->
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
                <a href="gerer_personnel.php" class="nav-link active">
                    <i class="bi bi-people-fill me-2"></i>Gérer le Personnel
                </a>
            </li>
            <!-- Autres liens -->
        </ul>
        
        <!-- Bouton de déconnexion -->
        <div class="mt-auto p-3 text-center">
            <a href="../actions/deconnexion.php" class="btn btn-outline-light w-100">
                Se déconnecter
            </a>
        </div>
    </nav>

    <!-- ======================
         CONTENU PRINCIPAL
         ====================== -->
    <div class="main-content">
        <!-- En-tête avec titre et bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion du Personnel</h1>
            <a href="ajouter_personnel.php" class="btn btn-success">
                <i class="bi bi-plus-circle-fill me-2"></i>Ajouter un membre
            </a>
        </div>
        
        <!-- Affichage des messages de succès -->
        <?php if(isset($_GET['message_succes'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['message_succes']) ?>
            </div>
        <?php endif; ?>

        <!-- Tableau du personnel -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Spécialité</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Boucle sur chaque membre du personnel -->
                            <?php foreach ($personnels as $membre): ?>
                                <tr>
                                    <!-- Affichage des informations -->
                                    <td><?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?></td>
                                    <td><?= htmlspecialchars($membre['email']) ?></td>
                                    <td><?= htmlspecialchars($membre['specialite']) ?></td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= htmlspecialchars($membre['role']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Boutons d'action -->
                                    <td>
                                        <!-- Lien de modification -->
                                        <a href="modifier_personnel.php?id=<?= $membre['id_personnel'] ?>" 
                                           class="btn btn-primary btn-sm" 
                                           title="Modifier">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        
                                        <!-- Lien de suppression avec confirmation -->
                                        <a href="actions_personnel.php?action=supprimer&id=<?= $membre['id_personnel'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           title="Supprimer" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?');">
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
    </div>
</div>
</body>
</html>