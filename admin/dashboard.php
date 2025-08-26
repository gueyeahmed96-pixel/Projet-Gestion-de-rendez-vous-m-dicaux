<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('Accès non autorisé.'));
    exit();
}
// --- RÉCUPÉRATION DES STATISTIQUES ---

$nom_admin = htmlspecialchars($_SESSION['user_nom']);

// Initialisation du tableau des statistiques avec des valeurs par défaut
$stats = [
    'total_patients' => 0, 
    'total_medecins' => 0, 
    'total_rdv_confirmes' => 0, 
    'prochains_rdv' => 0,
    'patients_online' => 0,  // Ajout pour les patients en ligne
    'personnel_online' => 0  // Ajout pour le personnel en ligne
];

try {
    // --- Statistiques Générales (votre code existant) ---
    $stats['total_patients'] = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $stats['total_medecins'] = $pdo->query("SELECT COUNT(*) FROM personnel WHERE role = 'medecin'")->fetchColumn();
    $stats['total_rdv_confirmes'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'Confirmé'")->fetchColumn();
    $stats['prochains_rdv'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE date_heure_rdv >= CURDATE() AND statut = 'Confirmé'")->fetchColumn();

    // --- NOUVEAU : Calcul des utilisateurs en ligne ---
    // On définit un intervalle de 5 minutes
    $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    
    // Compter les patients actifs
    $stmt_patients_online = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE derniere_activite > ?");
    $stmt_patients_online->execute([$five_minutes_ago]);
    $stats['patients_online'] = $stmt_patients_online->fetchColumn();
    
    // Compter le personnel actif
    $stmt_personnel_online = $pdo->prepare("SELECT COUNT(*) FROM personnel WHERE derniere_activite > ?");
    $stmt_personnel_online->execute([$five_minutes_ago]);
    $stats['personnel_online'] = $stmt_personnel_online->fetchColumn();

} catch (PDOException $e) { 
    // En cas d'erreur, on enregistre le message dans les logs du serveur
    // sans interrompre l'affichage de la page.
    error_log("Dashboard Stats Error: " . $e->getMessage()); 
}

$chart_labels = []; $chart_data = [];
try {
    $sql_chart = "SELECT DATE(date_heure_rdv) as jour, COUNT(*) as nombre_rdv FROM rendez_vous WHERE date_heure_rdv >= CURDATE() - INTERVAL 6 DAY AND date_heure_rdv < CURDATE() + INTERVAL 1 DAY GROUP BY jour ORDER BY jour ASC";
    $rdv_par_jour = $pdo->query($sql_chart)->fetchAll(PDO::FETCH_ASSOC);
    $jours_avec_rdv = array_column($rdv_par_jour, 'nombre_rdv', 'jour');
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = strftime('%a %d', strtotime($date));
        $chart_data[] = $jours_avec_rdv[$date] ?? 0;
    }
} catch (PDOException $e) { error_log("Chart Error: " . $e->getMessage()); }
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SUNU Clinique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css"> <!-- Fichier de style centralisé -->
    <style>
        /* Le CSS a été simplifié, mettez-le à jour */
        :root { 
            --primary-blue: #154784; --secondary-green: #25A795; --light-bg: #f5f7fa; 
            --font-family: 'Poppins', sans-serif;
        }
        body { background-color: var(--light-bg); font-family: var(--font-family); }
        /* Style pour les liens actifs dans la sidebar Offcanvas */
        .offcanvas .nav-link.active { background-color: var(--primary-blue); }
        .main-content { padding: 2.5rem; }
        /* ... (vos autres styles comme .stat-card, .chart-card peuvent rester) ... */
    </style>
</head>
<body>
 <?php 
    // 1. On inclut la sidebar Offcanvas (elle est cachée par défaut)
    include 'includes/sidebar.php'; 
    
    // 2. On inclut la barre de navigation du haut (visible)
    include 'includes/topbar.php'; 
    ?>
    
    <div class="main-content">
        <h1 class="display-5">Tableau de Bord</h1>
        <p class="text-muted mb-5">Bonjour <?= $nom_admin ?>, bienvenue sur votre panneau de contrôle.</p>
        <div class="row g-4">
            <div class="col-md-6 col-xl-3"><div class="card stat-card p-3 h-100"><div class="d-flex align-items-center"><div class="icon-circle bg-primary-subtle text-primary me-3"><i class="bi bi-people-fill"></i></div><div><h6 class="text-muted mb-0">Patients Inscrits</h6><h2 class="fw-bold mb-0"><?= $stats['total_patients'] ?></h2></div></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card stat-card p-3 h-100"><div class="d-flex align-items-center"><div class="icon-circle bg-info-subtle text-info me-3"><i class="bi bi-heart-pulse-fill"></i></div><div><h6 class="text-muted mb-0">Médecins Actifs</h6><h2 class="fw-bold mb-0"><?= $stats['total_medecins'] ?></h2></div></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card stat-card p-3 h-100"><div class="d-flex align-items-center"><div class="icon-circle bg-success-subtle text-success me-3"><i class="bi bi-calendar-check-fill"></i></div><div><h6 class="text-muted mb-0">RDV Confirmés</h6><h2 class="fw-bold mb-0"><?= $stats['total_rdv_confirmes'] ?></h2></div></div></div></div>
            <div class="col-md-6 col-xl-3"><div class="card stat-card p-3 h-100"><div class="d-flex align-items-center"><div class="icon-circle bg-warning-subtle text-warning me-3"><i class="bi bi-calendar-event-fill"></i></div><div><h6 class="text-muted mb-0">RDV à Venir</h6><h2 class="fw-bold mb-0"><?= $stats['prochains_rdv'] ?></h2></div></div></div></div>
            <!-- Carte "Utilisateurs en ligne" dans la partie HTML de dashboard.php -->
<div class="col-md-6 col-xl-3">
    <div class="card stat-card p-3 h-100">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-success-subtle text-success me-3">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <h6 class="text-muted mb-0">Utilisateurs en ligne</h6>
                <h2 class="fw-bold mb-0">
                    <?= ($stats['patients_online'] ?? 0) + ($stats['personnel_online'] ?? 0) ?>
                </h2>
            </div>
        </div>
    </div>
</div>
        </div>
        <div class="card chart-card mt-4"><div class="card-header bg-white border-0"><h5 class="mb-0">Activité des rendez-vous (7 derniers jours)</h5></div><div class="card-body"><canvas id="rdvChart" style="height: 350px;"></canvas></div></div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('rdvChart').getContext('2d');
        const labels = <?= $chart_labels_json ?>;
        const data = <?= $chart_data_json ?>;
        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(37, 167, 149, 0.5)');
        gradient.addColorStop(1, 'rgba(37, 167, 149, 0)');
        new Chart(ctx, { type: 'line', data: { labels: labels, datasets: [{ label: 'Rendez-vous', data: data, backgroundColor: gradient, borderColor: '#25A795', borderWidth: 4, tension: 0.4, fill: true, pointBackgroundColor: '#25A795', pointBorderColor: '#fff', pointHoverRadius: 8, pointHoverBackgroundColor: '#fff', pointHoverBorderWidth: 3, pointHoverBorderColor: '#25A795' }] }, options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' } } });
    });
</script>
<?php setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); ?>
</body>
</html>