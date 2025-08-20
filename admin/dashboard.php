<?php
// Initialisation de la session pour accéder aux variables $_SESSION
session_start();

// Inclusion des fichiers nécessaires
require_once '../includes/db_connect.php';  // Connexion à la base de données
require_once '../includes/tracker.php';     // Système de suivi d'activité

// Vérification des droits d'accès admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); 
    exit(); // Arrêt immédiat si non autorisé
}

// Sécurisation de l'affichage du nom admin
$nom_admin = htmlspecialchars($_SESSION['user_nom']);

// Initialisation du tableau des statistiques
$stats = [];

try {
    // Récupération des statistiques principales
    $stats['patients_total'] = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $stats['personnel_total'] = $pdo->query("SELECT COUNT(*) FROM personnel WHERE role != 'admin'")->fetchColumn();
    $stats['rdv_avenir'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE date_heure_rdv >= NOW() AND statut = 'Confirmé'")->fetchColumn();
    
    // Calcul des utilisateurs en ligne (activité dans les 5 dernières minutes)
    $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $stats['patients_online'] = $pdo->query("SELECT COUNT(*) FROM patients WHERE derniere_activite > '{$five_minutes_ago}'")->fetchColumn();
    $stats['personnel_online'] = $pdo->query("SELECT COUNT(*) FROM personnel WHERE derniere_activite > '{$five_minutes_ago}'")->fetchColumn();
} catch (PDOException $e) { 
    error_log($e->getMessage()); // Journalisation des erreurs sans interrompre l'exécution
}

// Préparation des données pour le graphique
$chart_labels = []; 
$chart_data = [];

try {
    // Requête pour les RDV des 7 derniers jours
    $sql_chart = "SELECT DATE(date_heure_rdv) as jour, COUNT(*) as nombre_rdv 
                 FROM rendez_vous 
                 WHERE date_heure_rdv >= CURDATE() - INTERVAL 6 DAY 
                 AND date_heure_rdv < CURDATE() + INTERVAL 1 DAY 
                 GROUP BY jour ORDER BY jour ASC";
    
    $rdv_par_jour = $pdo->query($sql_chart)->fetchAll(PDO::FETCH_ASSOC);
    $jours_avec_rdv = array_column($rdv_par_jour, 'nombre_rdv', 'jour');
    
    // Construction des tableaux pour Chart.js
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = strftime('%a %d', strtotime($date)); // Format "Lun 01"
        $chart_data[] = $jours_avec_rdv[$date] ?? 0; // Valeur par défaut 0 si pas de RDV
    }
} catch (PDOException $e) { 
    error_log("Chart Error: " . $e->getMessage()); 
}

// Conversion des données en JSON pour JavaScript
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Configuration de base -->
    <meta charset="UTF-8">
    <title>Dashboard Admin - Clinique VitaCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Auto-rafraîchissement toutes les 60 secondes -->
    <meta http-equiv="refresh" content="60">
    
    <!-- Inclusion des CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <!-- Animations -->
    
    <!-- Styles personnalisés -->
    <style>
        :root { 
            --primary-blue: #154784;    /* Couleur principale */
            --secondary-green: #25A795; /* Couleur secondaire */
            --light-bg: #f5f7fa;       /* Fond clair */
            --sidebar-width: 280px;     /* Largeur sidebar */
        }
        
        body { 
            background-color: var(--light-bg); 
            font-family: 'Poppins', sans-serif; 
        }
        
        /* Animation du point "en ligne" */
        @keyframes pulse { 
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); } 
            70% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } 
        }
    </style>
</head>
<body>
<!-- Structure principale -->
<div class="wrapper">
    <!-- Sidebar de navigation -->
    <nav id="sidebar" class="d-flex flex-column">
        <div class="sidebar-header">
            <h3><i class="bi bi-shield-shaded"></i> Admin Panel</h3>
        </div>
        
        <ul class="nav nav-pills flex-column p-3">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link active">
                    <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
                </a>
            </li>
            <!-- Autres liens de navigation -->
        </ul>
        
        <div class="mt-auto p-3">
            <a href="../actions/deconnexion.php" class="btn btn-outline-danger w-100">
                Se déconnecter
            </a>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="main-content">
        <h1 class="display-5" data-aos="fade-right">Tableau de Bord</h1>
        <p class="text-muted mb-5" data-aos="fade-right" data-aos-delay="100">
            Bonjour <?= $nom_admin ?>, bienvenue sur votre panneau de contrôle.
        </p>
        
        <!-- Cartes de statistiques -->
        <div class="row g-4">
            <!-- Carte Patients en ligne -->
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card stat-card p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary-subtle text-primary me-3">
                            <i class="bi bi-person-hearts"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Patients en ligne</h6>
                            <h2 class="fw-bold mb-0">
                                <?= $stats['patients_online'] ?? 0 ?>
                                <small class="text-muted fw-normal">/<?= $stats['patients_total'] ?? 0 ?></small>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Autres cartes de stats -->
            
            <!-- Carte d'action rapide -->
            <div class="col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="500">
                <a href="gerer_personnel.php" class="text-decoration-none">
                    <div class="card stat-card p-3 h-100 d-flex justify-content-center align-items-center bg-dark text-white">
                        <i class="bi bi-plus-circle-fill fs-2"></i>
                        <h6 class="mt-2 mb-0">Gérer le Personnel</h6>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Graphique des RDV -->
        <div class="card chart-card mt-4" data-aos="fade-up" data-aos-delay="600">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Activité des rendez-vous (7 derniers jours)</h5>
            </div>
            <div class="card-body">
                <canvas id="rdvChart" style="height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Bibliothèque de graphiques -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> <!-- Animations -->

<script>
    // Initialisation des animations
    AOS.init({ 
        duration: 600,      // Durée des animations
        once: true,         // Ne jouer qu'une fois
        easing: 'ease-out-quad' // Type d'animation
    });

    // Configuration du graphique
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rdvChart').getContext('2d');
        const labels = <?= $chart_labels_json ?>; // Données PHP converties en JS
        const data = <?= $chart_data_json ?>;

        // Création d'un dégradé pour le fond du graphique
        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(37, 167, 149, 0.5)');
        gradient.addColorStop(1, 'rgba(37, 167, 149, 0)');

        // Initialisation du graphique avec Chart.js
        new Chart(ctx, {
            type: 'line', // Type de graphique
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rendez-vous', 
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#25A795',
                    borderWidth: 4, 
                    tension: 0.4, // Courbure des lignes
                    fill: true, // Remplissage sous la courbe
                    // Style des points
                    pointBackgroundColor: '#25A795',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderWidth: 3,
                    pointHoverBorderColor: '#25A795'
                }]
            },
            options: {
                scales: { 
                    y: { 
                        beginAtZero: true, // Commencer à 0
                        ticks: { stepSize: 1 } // Pas de 1 sur l'axe Y
                    } 
                },
                plugins: { legend: { display: false } }, // Masquer la légende
                responsive: true, // Adaptabilité
                maintainAspectRatio: false,
                interaction: { 
                    intersect: false, 
                    mode: 'index' 
                }
            }
        });
    });
</script>

<?php setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); // Configuration locale pour les dates en français ?>
</body>
</html>