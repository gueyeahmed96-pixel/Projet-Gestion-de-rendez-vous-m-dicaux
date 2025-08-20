<?php
/**
 * Page de planning pour le personnel médical
 * 
 * Cette page affiche le planning quotidien avec les statistiques des rendez-vous
 * pour les médecins et secrétaires connectés.
 */

// --- INITIALISATION ---
// Démarrage de la session et inclusion des fichiers nécessaires
session_start();
require_once '../includes/db_connect.php';  // Connexion à la base de données
require_once '../includes/tracker.php';     // Fonctions de suivi d'activité

// --- SÉCURITÉ ---
// Vérification que l'utilisateur est connecté et a un rôle autorisé
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['medecin', 'secretaire'])) {
    header('Location: ../index.php');  // Redirection si non autorisé
    exit();
}

// Récupération des informations du personnel depuis la session
$id_personnel = $_SESSION['user_id'];
$nom_personnel = htmlspecialchars($_SESSION['user_nom']);  // Protection XSS

// Gestion de la date à afficher (date courante ou date spécifiée dans l'URL)
$date_a_afficher = $_GET['date'] ?? date('Y-m-d');

// --- STATISTIQUES DU JOUR ---
$stats_du_jour = ['total_rdv' => 0, 'rdv_confirmes' => 0];
try {
    // Requête pour compter les RDV totaux et confirmés
    $sql_stats = "SELECT COUNT(*) as total_rdv, 
                         SUM(CASE WHEN statut = 'Confirmé' THEN 1 ELSE 0 END) as rdv_confirmes
                  FROM rendez_vous 
                  WHERE id_personnel_fk = ? 
                    AND DATE(date_heure_rdv) = ?";
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute([$id_personnel, $date_a_afficher]);
    $result_stats = $stmt_stats->fetch();
    
    if ($result_stats) {
        $stats_du_jour = $result_stats;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());  // Journalisation des erreurs
}

// --- PLANNING DU JOUR ---
$planning_du_jour = [];
try {
    // Requête pour récupérer les RDV du jour avec les infos patients
    $sql_planning = "SELECT r.date_heure_rdv, r.statut, 
                            p.nom AS patient_nom, p.prenom AS patient_prenom, 
                            p.telephone AS patient_telephone
                     FROM rendez_vous AS r
                     JOIN patients AS p ON r.id_patient_fk = p.id_patient
                     WHERE r.id_personnel_fk = ? 
                       AND DATE(r.date_heure_rdv) = ?
                     ORDER BY r.date_heure_rdv ASC";
    $stmt_planning = $pdo->prepare($sql_planning);
    $stmt_planning->execute([$id_personnel, $date_a_afficher]);
    $planning_du_jour = $stmt_planning->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());  // Journalisation des erreurs
}

// Configuration locale pour l'affichage des dates en français
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et liens CSS -->
    <meta charset="UTF-8">
    <title>Mon Planning</title>
    
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
            box-shadow: 0 0 30px rgba(0,0,0,0.05);
        }
        
        #sidebar .nav-link {
            color: #555;
            font-weight: 500;
        }
        
        #sidebar .nav-link.active {
            background-color: var(--primary-blue);
            color: white;
        }
        
        .main-content {
            width: 100%;
            padding: 2rem;
        }
        
        .stat-card {
            border-radius: 15px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Structure principale -->
    <div class="wrapper">
        <!-- Barre latérale de navigation -->
        <nav id="sidebar" class="d-flex flex-column p-3">
            <h4 class="text-center my-3" style="color: var(--primary-blue);">Espace Personnel</h4>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="planning.php" class="nav-link active">
                        <i class="bi bi-calendar-week-fill me-2"></i>Mon Planning
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link disabled">
                        <i class="bi bi-toggles me-2"></i>Gérer dispos
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
            <h1>Bonjour, Dr. <?= strtok($nom_personnel, " ") ?></h1>
            <p class="text-muted">Planning du <?= strftime('%A %d %B %Y', strtotime($date_a_afficher)) ?>.</p>

            <!-- Cartes de statistiques -->
            <div class="row">
                <!-- Carte RDV confirmés -->
                <div class="col-md-6">
                    <div class="card stat-card border-0 p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="p-3 me-3 rounded-circle" style="background-color: var(--light-gray-bg);">
                                <i class="bi bi-calendar2-check-fill fs-2 text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">RDV Confirmés</h6>
                                <h2 class="fw-bold mb-0"><?= (int)($stats_du_jour['rdv_confirmes'] ?? 0) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carte Total RDV -->
                <div class="col-md-6">
                    <div class="card stat-card border-0 p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="p-3 me-3 rounded-circle" style="background-color: var(--light-gray-bg);">
                                <i class="bi bi-calendar2-x-fill fs-2 text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Total des RDV</h6>
                                <h2 class="fw-bold mb-0"><?= (int)($stats_du_jour['total_rdv'] ?? 0) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte principale du planning -->
            <div class="card shadow-sm border-0">
                <!-- En-tête avec sélecteur de date -->
                <div class="card-header bg-white border-0 p-3">
                    <form action="planning.php" method="GET" class="d-flex gap-2">
                        <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date_a_afficher) ?>">
                        <button type="submit" class="btn" style="background-color: var(--primary-blue); color: white;">Afficher</button>
                    </form>
                </div>
                
                <!-- Corps du planning -->
                <div class="card-body">
                    <?php if (empty($planning_du_jour)): ?>
                        <!-- Message si aucun RDV -->
                        <div class="text-center p-5">
                            <i class="bi bi-calendar2-heart fs-1 text-muted"></i>
                            <h4 class="mt-3">Aucun rendez-vous</h4>
                        </div>
                    <?php else: ?>
                        <!-- Liste des RDV -->
                        <ul class="list-group list-group-flush">
                            <?php foreach ($planning_du_jour as $rdv): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Heure du RDV -->
                                        <div class="me-3 text-center" style="min-width: 80px;">
                                            <h4 class="mb-0" style="color: var(--primary-blue);">
                                                <?= date('H:i', strtotime($rdv['date_heure_rdv'])) ?>
                                            </h4>
                                        </div>
                                        
                                        <!-- Infos patient -->
                                        <div>
                                            <h5 class="mb-0">
                                                <?= htmlspecialchars($rdv['patient_prenom'] . ' ' . $rdv['patient_nom']) ?>
                                            </h5>
                                            <span class="text-muted small">
                                                <i class="bi bi-telephone-fill"></i> 
                                                <?= htmlspecialchars($rdv['patient_telephone'] ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Statut du RDV -->
                                    <span class="badge fs-6 rounded-pill <?= $rdv['statut'] === 'Confirmé' ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' ?>">
                                        <?= htmlspecialchars($rdv['statut']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>