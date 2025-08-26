<?php
/* 1. INITIALISATION DE LA SESSION ET CONNEXION À LA BDD */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
/* 2. VÉRIFICATION DES DROITS D'ACCÈS */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');
    exit();
}
$id_patient = $_SESSION['user_id'];

/* 3. GESTION DE L'ANNULATION DE RENDEZ-VOUS */
if (isset($_GET['action']) && $_GET['action'] === 'annuler' && isset($_GET['id_rdv'])) {
    try {
        $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = 'Annulé par le patient' WHERE id_rdv = ? AND id_patient_fk = ?");
        $stmt->execute([$_GET['id_rdv'], $id_patient]);
        header('Location: mes_rdv.php?message_succes=Rendez-vous annulé.');
        exit();
    } catch (PDOException $e) { die("Erreur: " . $e->getMessage()); }
}

/* 4. RÉCUPÉRATION DES RENDEZ-VOUS DU PATIENT */
try {
    // CORRECTION : S'assurer que le nom de la colonne est correct. Je suppose 'nom' d'après nos corrections précédentes.
    $sql = "SELECT r.id_rdv, r.date_heure_rdv, r.statut, p.nom, p.prenom, s.nom_specialite AS specialite 
        FROM rendez_vous AS r 
        JOIN personnel AS p ON r.id_personnel_fk = p.id_personnel
        LEFT JOIN specialites AS s ON p.id_specialite_fk = s.id_specialite
        WHERE r.id_patient_fk = ? 
        ORDER BY r.date_heure_rdv DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_patient]);
    $mes_rdv = $stmt->fetchAll();
} catch (PDOException $e) { die("Erreur: " . $e->getMessage()); }

/* 5. CONFIGURATION LOCALE POUR LES DATES */
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Rendez-vous - Espace Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ============================================ -->
    <!--     STYLES CSS DU TABLEAU DE BORD MODERNE    -->
    <!-- ============================================ -->
    <style>
        :root { 
            --primary-blue: #154784; --secondary-green: #25A795; --light-gray-bg: #F7F9FC; 
            --font-family: 'Poppins', sans-serif; --sidebar-width: 280px; --sidebar-collapsed-width: 88px;
        }
        body { background-color: var(--light-gray-bg); font-family: var(--font-family); }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #sidebar { min-width: var(--sidebar-width); max-width: var(--sidebar-width); background: white; transition: all 0.3s; position: relative; box-shadow: 0 0 30px rgba(0,0,0,0.05); }
        #sidebar.collapsed { min-width: var(--sidebar-collapsed-width); max-width: var(--sidebar-collapsed-width); text-align: center; }
        #sidebar .sidebar-header { padding: 20px; text-align: center; }
        #sidebar.collapsed .sidebar-header h4 { display: none; }
        #sidebar .nav-link { color: #555; font-weight: 500; border-radius: 8px; padding: 10px 15px; display: flex; align-items: center; }
        #sidebar.collapsed .nav-link { justify-content: center; padding: 15px; }
        #sidebar.collapsed .nav-link .link-text { display: none; }
        #sidebar .nav-link .bi { font-size: 1.2rem; margin-right: 1rem; }
        #sidebar.collapsed .nav-link .bi { margin-right: 0; }
        #sidebar .nav-link.active { background-color: var(--secondary-green); color: white; }
        #sidebar .nav-link:hover { background-color: #f0f0f0; }
        #sidebar .nav-link.active:hover { background-color: var(--secondary-green); }
        #sidebar-toggle { position: absolute; top: 20px; right: -15px; background-color: white; border: 1px solid #ddd; border-radius: 50%; width: 30px; height: 30px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); cursor: pointer; z-index: 1000; }
        .main-content { width: 100%; padding: 2rem; min-height: 100vh; transition: all 0.3s; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- ============================================ -->
        <!--        MENU LATÉRAL RETRACTABLE             -->
        <!-- ============================================ -->
        <nav id="sidebar">
            <button type="button" id="sidebar-toggle" class="btn d-flex align-items-center justify-content-center"><i class="bi bi-chevron-left"></i></button>
            <div class="sidebar-header"><h4 style="color: var(--primary-blue);">Espace Patient</h4></div>
            <ul class="nav nav-pills flex-column p-3">
                <li class="nav-item"><a href="tableau_de_bord.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span class="link-text">Tableau de bord</span></a></li>
                <li class="nav-item"><a href="prendre_rdv.php" class="nav-link"><i class="bi bi-calendar-plus-fill"></i><span class="link-text">Prendre RDV</span></a></li>
                <li class="nav-item"><a href="mes_rdv.php" class="nav-link active"><i class="bi bi-calendar2-week-fill"></i><span class="link-text">Mes rendez-vous</span></a></li>
                <li class="nav-item mt-3"><a href="../index.php" class="nav-link bg-light text-primary"><i class="bi bi-arrow-left-circle-fill"></i><span class="link-text">Retour au site</span></a></li>
            </ul>
        </nav>

        <!-- CONTENU PRINCIPAL -->
        <div class="main-content">
            <h1 style="color: var(--primary-blue);">Historique de vos rendez-vous</h1>
            <p class="text-muted">Retrouvez ici la liste de tous vos rendez-vous passés et à venir.</p>

            <?php if(isset($_GET['message_succes'])): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['message_succes'])?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <?php if (empty($mes_rdv)): ?>
                        <div class="text-center p-5">
                            <i class="bi bi-calendar2-x fs-1 text-muted"></i>
                            <h4 class="mt-3">Aucun rendez-vous</h4>
                            <p class="text-muted">Vous n'avez pas encore de rendez-vous dans votre historique.</p>
                            <a href="prendre_rdv.php" class="btn mt-3" style="background-color: var(--secondary-green); color: white;">Prendre mon premier rendez-vous</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr><th>Date & Heure</th><th>Spécialiste</th><th>Statut</th><th class="text-end">Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mes_rdv as $rdv): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?= strftime('%A %d %B %Y', strtotime($rdv['date_heure_rdv'])) ?></div>
                                                <div class="text-muted fs-5"><?= date('H:i', strtotime($rdv['date_heure_rdv'])) ?></div>
                                            </td>
                                            <td>
                                                <div>Dr. <?= htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($rdv['specialite']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge fs-6 rounded-pill <?= $rdv['statut'] === 'Confirmé' ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' ?>">
                                                    <?= htmlspecialchars($rdv['statut']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($rdv['statut'] === 'Confirmé' && new DateTime($rdv['date_heure_rdv']) > new DateTime()): ?>
                                                    <div class="btn-group">
                                                        <a href="reporter_rdv.php?id_rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-calendar-event me-1"></i> Reporter</a>
                                                        <a href="mes_rdv.php?action=annuler&id_rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');"><i class="bi bi-trash me-1"></i> Annuler</a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Le script pour le menu rétractable
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle');
            if(toggleButton) {
                const toggleIcon = toggleButton.querySelector('i');
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    if (sidebar.classList.contains('collapsed')) { toggleIcon.classList.remove('bi-chevron-left'); toggleIcon.classList.add('bi-chevron-right'); } 
                    else { toggleIcon.classList.remove('bi-chevron-right'); toggleIcon.classList.add('bi-chevron-left'); }
                });
            }
        });
    </script>
</body>
</html>