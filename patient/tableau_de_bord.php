<?php
/**
 * Page du tableau de bord patient
 * 
 * Cette page affiche le tableau de bord principal pour les patients connectés,
 * avec leur prochain rendez-vous et des actions rapides.
 */

// --- INITIALISATION ---
// Démarrage de la session et inclusion des fichiers nécessaires
session_start();
require_once '../includes/db_connect.php';  // Connexion à la base de données
require_once '../includes/tracker.php';     // Fonctions de suivi d'activité

// --- SÉCURITÉ ---
// Vérification que l'utilisateur est connecté et a le rôle patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');  // Redirection si non autorisé
    exit();
}

// Récupération des informations du patient depuis la session
$id_patient = $_SESSION['user_id'];
$nom_patient = htmlspecialchars($_SESSION['user_nom']);  // Protection XSS

// --- RÉCUPÉRATION DES DONNÉES ---
// Requête pour obtenir le prochain rendez-vous confirmé du patient
$prochain_rdv = null;
try {
    $sql = "SELECT r.date_heure_rdv, p.nom AS medecin_nom, p.prenom AS medecin_prenom, p.specialite
            FROM rendez_vous r
            JOIN personnel p ON r.id_personnel_fk = p.id_personnel
            WHERE r.id_patient_fk = ? 
              AND r.date_heure_rdv >= NOW() 
              AND r.statut = 'Confirmé'
            ORDER BY r.date_heure_rdv ASC 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_patient]);
    $prochain_rdv = $stmt->fetch();
} catch (PDOException $e) {
    error_log($e->getMessage());  // Journalisation des erreurs sans affichage à l'utilisateur
}

// Configuration locale pour l'affichage des dates en français
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et liens CSS -->
    <meta charset="UTF-8">
    <title>Mon Espace Patient</title>
    
    <!-- Framework Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Police Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles personnalisés -->
    <style>
        :root {
            --primary-blue: #154784;
            --secondary-green: #25A795;
            --light-gray-bg: #F7F9FC;
            --sidebar-width: 280px;
        }
        
        body {
            background-color: var(--light-gray-bg);
            font-family: 'Poppins', sans-serif;
        }
        
        .wrapper {
            display: flex;
        }
        
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: white;
            transition: all 0.3s;
            box-shadow: 0 0 30px rgba(0,0,0,0.05);
        }
        
        #sidebar .nav-link {
            color: #555;
            font-weight: 500;
        }
        
        #sidebar .nav-link.active {
            background-color: var(--secondary-green);
            color: white;
        }
        
        .main-content {
            width: 100%;
            padding: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-green) 100%);
            color: white;
            border-radius: 15px;
        }
        
        .action-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .action-card .icon {
            font-size: 3rem;
            color: var(--secondary-green);
        }
    </style>
</head>
<body>
    <!-- Structure principale -->
    <div class="wrapper">
        <!-- Barre latérale de navigation -->
        <nav id="sidebar" class="d-flex flex-column p-3">
            <h4 class="text-center my-3" style="color: var(--primary-blue);">Espace Patient</h4>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="tableau_de_bord.php" class="nav-link active">
                        <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="prendre_rdv.php" class="nav-link">
                        <i class="bi bi-calendar-plus-fill me-2"></i>Prendre RDV
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mes_rdv.php" class="nav-link">
                        <i class="bi bi-calendar2-week-fill me-2"></i>Mes rendez-vous
                    </a>
                </li>
            </ul>
            
            <hr>
            
            <!-- Boutons de navigation -->
            <a href="../index.php" class="btn btn-outline-secondary mb-2">Retour au site</a>
            <a href="../actions/deconnexion.php" class="btn btn-danger">Se déconnecter</a>
        </nav>

        <!-- Contenu principal -->
        <div class="main-content">
            <h1>Bonjour, <?= strtok($nom_patient, " ") ?> !</h1>
            <p class="text-muted">Bienvenue sur votre espace personnel.</p>

            <!-- Affichage des messages de succès -->
            <?php if (isset($_GET['message_succes'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_GET['message_succes']) ?>
                </div>
            <?php endif; ?>

            <!-- Carte du prochain rendez-vous -->
            <div class="card stat-card p-4 my-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-calendar2-check-fill" style="font-size: 4rem;"></i>
                    </div>
                    <div class="col">
                        <h5>Votre prochain rendez-vous</h5>
                        <?php if ($prochain_rdv): ?>
                            <h3 class="fw-bold mb-1">
                                <?= strftime('%A %d %B %Y', strtotime($prochain_rdv['date_heure_rdv'])) ?> 
                                à <?= date('H:i', strtotime($prochain_rdv['date_heure_rdv'])) ?>
                            </h3>
                            <p class="mb-0">
                                Avec Dr. <?= htmlspecialchars($prochain_rdv['medecin_prenom'] . ' ' . $prochain_rdv['medecin_nom']) ?> 
                                (<?= htmlspecialchars($prochain_rdv['specialite']) ?>)
                            </p>
                        <?php else: ?>
                            <h4 class="fw-bold">Aucun rendez-vous à venir</h4>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cartes d'action rapide -->
            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <a href="prendre_rdv.php" class="text-decoration-none">
                        <div class="card action-card p-4 text-center h-100">
                            <div class="icon"><i class="bi bi-calendar-plus-fill"></i></div>
                            <h4 class="mt-3">Prendre un RDV</h4>
                            <p class="text-muted">Trouvez un créneau avec nos spécialistes.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 mb-4">
                    <a href="mes_rdv.php" class="text-decoration-none">
                        <div class="card action-card p-4 text-center h-100">
                            <div class="icon"><i class="bi bi-calendar2-week-fill"></i></div>
                            <h4 class="mt-3">Gérer mes RDV</h4>
                            <p class="text-muted">Consultez ou annulez vos rendez-vous.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>