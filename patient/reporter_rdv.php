<?php
/* 1. INITIALISATION */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
/* 2. SÉCURITÉ ET ACCÈS */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php');
    exit();
}
$id_patient = $_SESSION['user_id'];

if (!isset($_GET['id_rdv']) || !is_numeric($_GET['id_rdv'])) {
    header('Location: mes_rdv.php');
    exit();
}
$id_rdv_a_reporter = $_GET['id_rdv'];

/* 4. RÉCUPÉRATION DU RENDEZ-VOUS ACTUEL (AVEC LA REQUÊTE CORRIGÉE) */
try {
    $sql = "
        SELECT r.*, p.nom, p.prenom, s.nom_specialite AS specialite 
        FROM rendez_vous r 
        JOIN personnel p ON r.id_personnel_fk = p.id_personnel 
        LEFT JOIN specialites s ON p.id_specialite_fk = s.id_specialite
        WHERE r.id_rdv = ? AND r.id_patient_fk = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_rdv_a_reporter, $id_patient]);
    $rdv_actuel = $stmt->fetch();
    
    if (!$rdv_actuel) {
        header('Location: mes_rdv.php?message_erreur=Rendez-vous introuvable.');
        exit();
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

/* 5. GESTION DES CRÉNEAUX DISPONIBLES */
$creneaux_disponibles = [];
$date_selectionnee = $_POST['date_rdv'] ?? null;
$medecin_id = $rdv_actuel['id_personnel_fk'];

if ($date_selectionnee) {
    $duree_creneau = 20;
    $heure_debut_matin = new DateTime($date_selectionnee . ' 09:00');
    $heure_fin_matin = new DateTime($date_selectionnee . ' 12:00');
    $heure_debut_aprem = new DateTime($date_selectionnee . ' 14:00');
    $heure_fin_aprem = new DateTime($date_selectionnee . ' 17:00');

    $tous_creneaux_theoriques = [];
    $current = clone $heure_debut_matin;
    while($current < $heure_fin_matin) { $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s'); $current->modify('+' . $duree_creneau . ' minutes'); }
    $current = clone $heure_debut_aprem;
    while($current < $heure_fin_aprem) { $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s'); $current->modify('+' . $duree_creneau . ' minutes'); }

    $stmt_rdv_pris = $pdo->prepare("SELECT date_heure_rdv FROM rendez_vous WHERE id_personnel_fk = ? AND DATE(date_heure_rdv) = ? AND statut = 'Confirmé' AND id_rdv != ?");
    $stmt_rdv_pris->execute([$medecin_id, $date_selectionnee, $id_rdv_a_reporter]);
    $rdv_pris = $stmt_rdv_pris->fetchAll(PDO::FETCH_COLUMN);
    $creneaux_disponibles = array_diff($tous_creneaux_theoriques, $rdv_pris);
}

/* 6. TRAITEMENT DU REPORT DE RENDEZ-VOUS */
if (isset($_GET['new_datetime'])) {
    try {
        $stmt_update = $pdo->prepare("UPDATE rendez_vous SET date_heure_rdv = ? WHERE id_rdv = ? AND id_patient_fk = ?");
        $stmt_update->execute([$_GET['new_datetime'], $id_rdv_a_reporter, $id_patient]);
        header('Location: mes_rdv.php?message_succes=Rendez-vous reporté avec succès !');
        exit();
    } catch(PDOException $e) {
        die("Erreur lors de la mise à jour : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reporter Rendez-vous - Clinique VitaCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-blue: #154784; --secondary-green: #25A795; --light-gray-bg: #F7F9FC; --font-family: 'Poppins', sans-serif; }
        body { background-color: var(--light-gray-bg); font-family: var(--font-family); }
        .wrapper { display: flex; }
        #sidebar { min-width: 320px; background: white; box-shadow: 0 0 30px rgba(0,0,0,0.05); }
        .main-content { width: 100%; padding: 2rem; }
    </style>
</head>
<body>
    <div class="wrapper d-flex vh-100">
        <nav id="sidebar" class="p-4 d-flex flex-column">
            <h4 class="text-center my-3" style="color: var(--primary-blue);">Reporter un RDV</h4>
            <div class="alert alert-info">
                <p class="fw-bold mb-1">Rendez-vous actuel :</p>
                <p class="small mb-1"><i class="bi bi-person-fill"></i> Dr. <?= htmlspecialchars($rdv_actuel['prenom'] . ' ' . $rdv_actuel['nom']) ?></p>
                <p class="small mb-1"><i class="bi bi-tag-fill"></i> <?= htmlspecialchars($rdv_actuel['specialite']) ?></p>
                <p class="small mb-0"><i class="bi bi-calendar-event-fill"></i> <?= strftime('%A %d %b %Y à %H:%M', strtotime($rdv_actuel['date_heure_rdv'])) ?></p>
            </div>
            <div class="mt-auto">
                <a href="mes_rdv.php" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-2"></i>Annuler le report</a>
            </div>
        </nav>
        <div class="main-content flex-grow-1">
            <h1>Choisissez une nouvelle date</h1>
            <p class="text-muted">Le rendez-vous restera avec Dr. <?= htmlspecialchars($rdv_actuel['prenom'] . ' ' . $rdv_actuel['nom']) ?>.</p>
            <div class="card p-4 shadow-sm border-0">
                <form action="reporter_rdv.php?id_rdv=<?= $id_rdv_a_reporter ?>" method="POST">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="date_rdv" class="form-label fw-bold">Nouvelle date souhaitée</label>
                            <input type="date" class="form-control form-control-lg" id="date_rdv" name="date_rdv" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btn-lg" style="background-color: var(--primary-blue);">Voir les disponibilités</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <?php if (!empty($creneaux_disponibles)): ?>
                    <div class="card p-4 mt-4 shadow-sm border-0">
                        <h4 class="mb-3">Créneaux disponibles pour le <?= htmlspecialchars(date('d/m/Y', strtotime($date_selectionnee))) ?></h4>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($creneaux_disponibles as $creneau): ?>
                                <a href="reporter_rdv.php?id_rdv=<?= $id_rdv_a_reporter ?>&new_datetime=<?= urlencode($creneau) ?>" class="btn btn-lg" style="background-color: var(--secondary-green); color: white;"><?= htmlspecialchars(date('H:i', strtotime($creneau))) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-4">Aucun créneau disponible pour cette date. Veuillez en choisir une autre.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); ?>
</body>
</html>