<?php
// --- VOTRE CODE PHP EXISTANT RESTE IDENTIQUE ---
// Je le garde ici pour que le fichier soit complet.
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');
    exit();
}

$id_patient = $_SESSION['user_id'];
$nom_patient = htmlspecialchars($_SESSION['user_nom']);

$prochain_rdv = null;
try {
    // CORRECTION : S'assurer que la requête utilise la nouvelle structure de BDD
    $sql = "SELECT r.date_heure_rdv, p.nom AS medecin_nom, p.prenom AS medecin_prenom, s.nom AS specialite
            FROM rendez_vous r
            JOIN personnel p ON r.id_personnel_fk = p.id_personnel
            LEFT JOIN specialites s ON p.id_specialite_fk = s.id_specialite
            WHERE r.id_patient_fk = ? 
              AND r.date_heure_rdv >= NOW() 
              AND r.statut = 'Confirmé'
            ORDER BY r.date_heure_rdv ASC 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_patient]);
    $prochain_rdv = $stmt->fetch();
} catch (PDOException $e) {
    error_log($e->getMessage());
}

setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace Patient - SUNU Clinique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ============================================ -->
    <!--          NOUVEAUX STYLES CSS                 -->
    <!-- ============================================ -->
    <style>
        :root {
            --primary-blue: #154784;
            --secondary-green: #25A795;
            --light-gray-bg: #F7F9FC;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 88px;
            --font-family: 'Poppins', sans-serif;
        }
        body { background-color: var(--light-gray-bg); font-family: var(--font-family); }
        .wrapper { display: flex; align-items: stretch; }

        #sidebar {
            min-width: var(--sidebar-width); max-width: var(--sidebar-width);
            background: var(--primary-blue); color: white; transition: all 0.3s;
        }
        #sidebar.collapsed { min-width: var(--sidebar-collapsed-width); max-width: var(--sidebar-collapsed-width); }
        #sidebar .sidebar-header { padding: 20px; }
        #sidebar.collapsed .sidebar-header .user-info { display: none; }
        
        #sidebar .nav-link { 
            color: rgba(255, 255, 255, 0.8); font-weight: 500;
            padding: 12px 20px; margin: 5px 0; border-radius: 8px;
            display: flex; align-items: center; transition: all 0.2s;
        }
        #sidebar.collapsed .nav-link { justify-content: center; }
        #sidebar.collapsed .nav-link .link-text { display: none; }
        #sidebar .nav-link .bi { font-size: 1.2rem; margin-right: 1rem; }
        #sidebar.collapsed .nav-link .bi { margin-right: 0; }
        
        #sidebar .nav-link.active { background-color: var(--secondary-green); color: white; font-weight: 600; }
        #sidebar .nav-link:not(.active):hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        
        #sidebar-toggle { position: absolute; top: 20px; right: -15px; background-color: white; border: 1px solid #ddd; border-radius: 50%; width: 30px; height: 30px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); cursor: pointer; z-index: 1000; }
        
        .main-content { width: 100%; padding: 2.5rem; }
        
        .stat-card { background: white; border-left: 5px solid var(--secondary-green); border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); }
        
        .action-card { border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); transition: all 0.3s ease; border: none; }
        .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .action-card .icon { font-size: 2.5rem; color: var(--secondary-green); background-color: var(--light-gray-bg); padding: 15px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Barre latérale améliorée -->
        <nav id="sidebar">
            <button type="button" id="sidebar-toggle" class="btn d-flex align-items-center justify-content-center"><i class="bi bi-chevron-left"></i></button>
            <div class="sidebar-header text-center">
                <i class="bi bi-person-circle fs-1 mb-2"></i>
                <div class="user-info">
                    <h5 class="text-white"><?= strtok($nom_patient, " ") ?></h5>
                    <small class="text-white-50">Patient</small>
                </div>
            </div>
            
            <ul class="nav nav-pills flex-column p-3">
                <li class="nav-item"><a href="tableau_de_bord.php" class="nav-link active"><i class="bi bi-grid-fill"></i><span class="link-text">Tableau de bord</span></a></li>
                <li class="nav-item"><a href="prendre_rdv.php" class="nav-link"><i class="bi bi-calendar-plus"></i><span class="link-text">Prendre RDV</span></a></li>
                <li class="nav-item"><a href="mes_rdv.php" class="nav-link"><i class="bi bi-card-list"></i><span class="link-text">Mes rendez-vous</span></a></li>
            </ul>
            
            <div class="mt-auto p-3">
                <a href="../index.php" class="btn btn-light w-100 mb-2"><i class="bi bi-arrow-left-circle me-2"></i><span class="link-text">Retour au site</span></a>
                <a href="../deconnexion.php" class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right me-2"></i><span class="link-text">Se déconnecter</span></a>
            </div>
        </nav>

        <!-- Contenu principal -->
        <div class="main-content">
            <h1 style="color: var(--primary-blue);">Bonjour, <?= strtok($nom_patient, " ") ?> !</h1>
            <p class="text-muted">Bienvenue sur votre espace personnel. Voici un aperçu de votre activité.</p>

            <?php if (isset($_GET['message_succes'])): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['message_succes']) ?></div>
            <?php endif; ?>

            <div class="card stat-card p-4 my-4">
                <div class="row align-items-center">
                    <div class="col-auto"><i class="bi bi-calendar2-check-fill" style="font-size: 3.5rem; color: var(--primary-blue);"></i></div>
                    <div class="col">
                        <h6 class="text-muted mb-1">Votre prochain rendez-vous</h6>
                        <?php if ($prochain_rdv): ?>
                            <h3 class="fw-bold mb-1" style="color: var(--primary-blue);">
                                <?= strftime('%A %d %B %Y', strtotime($prochain_rdv['date_heure_rdv'])) ?> à <?= date('H:i', strtotime($prochain_rdv['date_heure_rdv'])) ?>
                            </h3>
                            <p class="mb-0">Avec Dr. <?= htmlspecialchars($prochain_rdv['medecin_prenom'] . ' ' . $prochain_rdv['medecin_nom']) ?> (<?= htmlspecialchars($prochain_rdv['specialite']) ?>)</p>
                        <?php else: ?>
                            <h4 class="fw-bold" style="color: var(--primary-blue);">Aucun rendez-vous à venir</h4>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <a href="prendre_rdv.php" class="text-decoration-none">
                        <div class="card action-card p-4 text-center h-100">
                            <div class="icon"><i class="bi bi-calendar-plus"></i></div>
                            <h4 class="mt-3" style="color: var(--primary-blue);">Prendre un RDV</h4>
                            <p class="text-muted">Trouvez un créneau avec nos spécialistes.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 mb-4">
                    <a href="mes_rdv.php" class="text-decoration-none">
                        <div class="card action-card p-4 text-center h-100">
                            <div class="icon"><i class="bi bi-card-list"></i></div>
                            <h4 class="mt-3" style="color: var(--primary-blue);">Gérer mes RDV</h4>
                            <p class="text-muted">Consultez ou annulez vos rendez-vous.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Le script pour le menu rétractable est inchangé
        document.addEventListener('DOMContentLoaded', function() { /* ... */ });
    </script>
</body>
</html>