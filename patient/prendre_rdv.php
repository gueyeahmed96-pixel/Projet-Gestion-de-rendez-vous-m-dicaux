<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
// Sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');
    exit();
}

$nom_patient = htmlspecialchars($_SESSION['user_nom']);

// --- LOGIQUE PHP CORRECTEMENT ORDONNÉE ---

// 1. Initialiser les variables à partir des données POST (si elles existent)
$medecin_selectionne_id = $_POST['medecin'] ?? null;
$date_selectionnee = $_POST['date_rdv'] ?? null;
$creneaux_disponibles = [];

// 2. Récupérer la liste de tous les médecins pour la liste déroulante
$medecins = [];
try {
    $sql = "
        SELECT p.id_personnel, p.nom, p.prenom, s.nom_specialite AS specialite
        FROM personnel AS p
        LEFT JOIN specialites AS s ON p.id_specialite_fk = s.id_specialite
        WHERE p.role = 'medecin'
        ORDER BY p.nom, p.prenom
    ";
    $stmt_medecins = $pdo->query($sql);
    $medecins = $stmt_medecins->fetchAll();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// 3. Si le formulaire a été soumis, chercher les créneaux disponibles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($medecin_selectionne_id) && !empty($date_selectionnee)) {
    $duree_creneau = 20;
    $heure_debut_matin = new DateTime($date_selectionnee . ' 09:00');
    $heure_fin_matin = new DateTime($date_selectionnee . ' 12:00');
    $heure_debut_aprem = new DateTime($date_selectionnee . ' 14:00');
    $heure_fin_aprem = new DateTime($date_selectionnee . ' 17:00');
    
    $tous_creneaux_theoriques = [];
    $current = clone $heure_debut_matin;
    while ($current < $heure_fin_matin) {
        $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s');
        $current->modify('+' . $duree_creneau . ' minutes');
    }
    $current = clone $heure_debut_aprem;
    while ($current < $heure_fin_aprem) {
        $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s');
        $current->modify('+' . $duree_creneau . ' minutes');
    }

    $stmt_rdv_pris = $pdo->prepare("SELECT date_heure_rdv FROM rendez_vous WHERE id_personnel_fk = ? AND DATE(date_heure_rdv) = ? AND statut = 'Confirmé'");
    $stmt_rdv_pris->execute([$medecin_selectionne_id, $date_selectionnee]);
    $rdv_pris = $stmt_rdv_pris->fetchAll(PDO::FETCH_COLUMN);

    $creneaux_disponibles = array_diff($tous_creneaux_theoriques, $rdv_pris);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre Rendez-vous - Espace Patient</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Reprise du style du tableau de bord pour la cohérence -->
    <style>
        :root { --primary-blue: #154784; --secondary-green: #25A795; --light-gray-bg: #F7F9FC; --font-family: 'Poppins', sans-serif; --sidebar-width: 280px; --sidebar-collapsed-width: 88px; }
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
    <!-- Menu latéral -->
    <nav id="sidebar">
        <button type="button" id="sidebar-toggle" class="btn d-flex align-items-center justify-content-center"><i class="bi bi-chevron-left"></i></button>
        <div class="sidebar-header"><h4 style="color: var(--primary-blue);">Espace Patient</h4></div>
        <ul class="nav nav-pills flex-column p-3">
            <li class="nav-item"><a href="tableau_de_bord.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span class="link-text">Tableau de bord</span></a></li>
            <li class="nav-item"><a href="prendre_rdv.php" class="nav-link active"><i class="bi bi-calendar-plus-fill"></i><span class="link-text">Prendre RDV</span></a></li>
            <li class="nav-item"><a href="mes_rdv.php" class="nav-link"><i class="bi bi-calendar2-week-fill"></i><span class="link-text">Mes rendez-vous</span></a></li>
            <li class="nav-item mt-3"><a href="../index.php" class="nav-link bg-light text-primary"><i class="bi bi-arrow-left-circle-fill"></i><span class="link-text">Retour au site</span></a></li>
        </ul>
    </nav>

    <!-- Contenu principal -->
    <div class="main-content">
        <h1>Prendre un nouveau rendez-vous</h1>
        <p class="text-muted">Choisissez un spécialiste et une date pour voir les créneaux disponibles.</p>
        <div class="card p-4 shadow-sm border-0">
            <form action="prendre_rdv.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="medecin" class="form-label fw-bold">1. Choisir un spécialiste</label>
                        <select class="form-select form-select-lg" id="medecin" name="medecin" required>
                            <option value="">-- Sélectionnez un médecin --</option>
                            <?php foreach ($medecins as $medecin): ?>
                                <option value="<?= $medecin['id_personnel'] ?>" <?= ($medecin_selectionne_id == $medecin['id_personnel']) ? 'selected' : '' ?>>
                                    Dr. <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?> (<?= htmlspecialchars($medecin['specialite']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_rdv" class="form-label fw-bold">2. Choisir une date</label>
                        <input type="date" class="form-control form-control-lg" id="date_rdv" name="date_rdv" value="<?= htmlspecialchars((string)$date_selectionnee) ?>" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 btn-lg" style="background-color: var(--primary-blue);">Voir les disponibilités</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php if (!empty($creneaux_disponibles)): ?>
                <div class="card p-4 mt-4 shadow-sm border-0">
                    <h4 class="mb-3" style="color: var(--primary-blue);">3. Choisissez un créneau pour le <?= htmlspecialchars(date('d/m/Y', strtotime($date_selectionnee))) ?></h4>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($creneaux_disponibles as $creneau): ?>
                            <a href="confirmer_rdv.php?datetime=<?= urlencode($creneau) ?>&medecin=<?= $medecin_selectionne_id ?>" class="btn btn-lg" style="background-color: var(--secondary-green); color: white;">
                                <?= htmlspecialchars(date('H:i', strtotime($creneau))) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif(!empty($medecin_selectionne_id) && !empty($date_selectionnee)): ?>
                <div class="alert alert-warning mt-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>Aucun créneau disponible pour cette date. Veuillez choisir un autre jour ou un autre médecin.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Le JavaScript pour gérer le menu rétractable
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('sidebar-toggle');
        if (toggleButton) {
            const toggleIcon = toggleButton.querySelector('i');
            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.classList.remove('bi-chevron-left');
                    toggleIcon.classList.add('bi-chevron-right');
                } else {
                    toggleIcon.classList.remove('bi-chevron-right');
                    toggleIcon.classList.add('bi-chevron-left');
                }
            });
        }
    });
</script>
</body>
</html>