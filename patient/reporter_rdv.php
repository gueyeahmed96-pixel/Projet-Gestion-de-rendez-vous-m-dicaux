<?php
/* 1. INITIALISATION */
// Démarrage de la session pour accéder aux données utilisateur
session_start();

// Inclusion des fichiers nécessaires
require_once '../includes/db_connect.php';  // Connexion à la base de données
require_once '../includes/tracker.php';     // Suivi d'activité utilisateur

/* 2. VÉRIFICATION DES DROITS D'ACCÈS */
// Seuls les patients connectés peuvent accéder à cette page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php'); // Redirection si non autorisé
    exit();
}

// Récupération de l'ID du patient connecté
$id_patient = $_SESSION['user_id'];

/* 3. VALIDATION DE L'ID DU RENDEZ-VOUS */
// Vérifie que l'ID du RDV à reporter est bien présent et numérique
if (!isset($_GET['id_rdv']) || !is_numeric($_GET['id_rdv'])) {
    header('Location: mes_rdv.php'); // Redirection si ID invalide
    exit();
}

// Récupération sécurisée de l'ID du RDV
$id_rdv_a_reporter = $_GET['id_rdv'];

/* 4. RÉCUPÉRATION DU RENDEZ-VOUS ACTUEL */
try {
    // Requête pour obtenir les détails du RDV avec les infos du médecin
    $sql = "SELECT r.*, p.nom, p.prenom, p.specialite 
            FROM rendez_vous r 
            JOIN personnel p ON r.id_personnel_fk = p.id_personnel 
            WHERE r.id_rdv = ? AND r.id_patient_fk = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_rdv_a_reporter, $id_patient]);
    $rdv_actuel = $stmt->fetch();
    
    // Si le RDV n'existe pas ou n'appartient pas au patient
    if (!$rdv_actuel) {
        header('Location: mes_rdv.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage()); // Gestion des erreurs SQL
}

/* 5. GESTION DES CRÉNEAUX DISPONIBLES */
$creneaux_disponibles = [];
$date_selectionnee = $_POST['date_rdv'] ?? null; // Date sélectionnée par l'utilisateur
$medecin_id = $rdv_actuel['id_personnel_fk'];   // ID du médecin du RDV actuel

// Si une date a été sélectionnée
if ($date_selectionnee) {
    /* 5.1. DÉFINITION DES HORAIRES */
    $duree_creneau = 20; // Durée d'un créneau en minutes
    
    // Plage horaire matinale
    $heure_debut_matin = new DateTime($date_selectionnee . ' 09:00');
    $heure_fin_matin = new DateTime($date_selectionnee . ' 12:00');
    
    // Plage horaire après-midi
    $heure_debut_aprem = new DateTime($date_selectionnee . ' 14:00');
    $heure_fin_aprem = new DateTime($date_selectionnee . ' 17:00');

    /* 5.2. GÉNÉRATION DES CRÉNEAUX THÉORIQUES */
    $tous_creneaux_theoriques = [];
    
    // Génération des créneaux du matin
    $current = clone $heure_debut_matin;
    while($current < $heure_fin_matin) {
        $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s');
        $current->modify('+' . $duree_creneau . ' minutes');
    }
    
    // Génération des créneaux de l'après-midi
    $current = clone $heure_debut_aprem;
    while($current < $heure_fin_aprem) {
        $tous_creneaux_theoriques[] = $current->format('Y-m-d H:i:s');
        $current->modify('+' . $duree_creneau . ' minutes');
    }

    /* 5.3. RÉCUPÉRATION DES CRÉNEAUX DÉJÀ PRIS */
    // On exclut le RDV actuel de la liste des RDV pris
    $stmt_rdv_pris = $pdo->prepare("SELECT date_heure_rdv 
                                   FROM rendez_vous 
                                   WHERE id_personnel_fk = ? 
                                   AND DATE(date_heure_rdv) = ? 
                                   AND statut = 'Confirmé' 
                                   AND id_rdv != ?");
    $stmt_rdv_pris->execute([$medecin_id, $date_selectionnee, $id_rdv_a_reporter]);
    $rdv_pris = $stmt_rdv_pris->fetchAll(PDO::FETCH_COLUMN);
    
    /* 5.4. CALCUL DES CRÉNEAUX DISPONIBLES */
    // Différence entre les créneaux théoriques et ceux déjà pris
    $creneaux_disponibles = array_diff($tous_creneaux_theoriques, $rdv_pris);
}

/* 6. TRAITEMENT DU REPORT DE RENDEZ-VOUS */
if (isset($_GET['new_datetime'])) {
    try {
        // Mise à jour de la date/heure du RDV
        $stmt_update = $pdo->prepare("UPDATE rendez_vous 
                                     SET date_heure_rdv = ? 
                                     WHERE id_rdv = ? AND id_patient_fk = ?");
        $stmt_update->execute([$_GET['new_datetime'], $id_rdv_a_reporter, $id_patient]);
        
        // Redirection avec message de succès
        header('Location: mes_rdv.php?message_succes=Rendez-vous reporté !');
        exit();
    } catch(PDOException $e) {
        die("Erreur : " . $e->getMessage()); // Gestion des erreurs SQL
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- CONFIGURATION DE BASE -->
    <meta charset="UTF-8">
    <title>Reporter Rendez-vous</title>
    
    <!-- INCLUSION DES CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- STYLES PERSONNALISÉS -->
    <style>
        :root { 
            --primary-blue: #154784;    /* Couleur principale */
            --secondary-green: #25A795; /* Couleur secondaire */
            --light-gray-bg: #F7F9FC;   /* Couleur de fond */
        }
        
        body { 
            background-color: var(--light-gray-bg); 
            font-family: 'Poppins', sans-serif; 
        }
        
        /* Structure de la sidebar */
        #sidebar { 
            min-width: 280px; 
            background: white; 
            box-shadow: 0 0 30px rgba(0,0,0,0.05); 
        }
    </style>
</head>

<body>
    <!-- STRUCTURE PRINCIPALE -->
    <div class="wrapper">
        <!-- ======================
             SIDEBAR DE NAVIGATION 
             ====================== -->
        <nav id="sidebar" class="p-4">
            <h4 class="text-center my-3" style="color: var(--primary-blue);">Reporter un RDV</h4>
            
            <!-- AFFICHAGE DU RDV ACTUEL -->
            <div class="alert alert-info">
                <p class="fw-bold mb-1">RDV actuel :</p>
                <p class="small mb-1">Dr. <?= htmlspecialchars($rdv_actuel['prenom'] . ' ' . $rdv_actuel['nom']) ?></p>
                <p class="small mb-0"><?= strftime('%A %d %b %Y à %H:%M', strtotime($rdv_actuel['date_heure_rdv'])) ?></p>
            </div>
            
            <!-- BOUTON DE RETOUR -->
            <a href="mes_rdv.php" class="btn btn-outline-secondary w-100">Annuler le report</a>
        </nav>

        <!-- ======================
             CONTENU PRINCIPAL
             ====================== -->
        <div class="main-content">
            <h1>Choisissez une nouvelle date</h1>
            <p class="text-muted">Avec Dr. <?= htmlspecialchars($rdv_actuel['prenom'] . ' ' . $rdv_actuel['nom']) ?>.</p>
            
            <!-- FORMULAIRE DE SÉLECTION DE DATE -->
            <div class="card p-4 shadow-sm border-0">
                <form action="reporter_rdv.php?id_rdv=<?= $id_rdv_a_reporter ?>" method="POST">
                    <div class="row g-3 align-items-end">
                        <!-- CHAMP DE DATE -->
                        <div class="col-md-8">
                            <label for="date_rdv" class="form-label fw-bold">Nouvelle date</label>
                            <input type="date" class="form-control form-control-lg" 
                                   id="date_rdv" name="date_rdv" 
                                   required min="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <!-- BOUTON DE VALIDATION -->
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btn-lg" 
                                    style="background-color: var(--primary-blue);">
                                Voir
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- AFFICHAGE DES CRÉNEAUX DISPONIBLES -->
            <?php if (!empty($creneaux_disponibles)): ?>
                <div class="card p-4 mt-4 shadow-sm border-0">
                    <h4 class="mb-3">Créneaux pour le <?= htmlspecialchars(date('d/m/Y', strtotime($date_selectionnee))) ?></h4>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($creneaux_disponibles as $creneau): ?>
                            <a href="reporter_rdv.php?id_rdv=<?= $id_rdv_a_reporter ?>&new_datetime=<?= urlencode($creneau) ?>" 
                               class="btn btn-lg" 
                               style="background-color: var(--secondary-green); color: white;">
                                <?= htmlspecialchars(date('H:i', strtotime($creneau))) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-warning mt-4">Aucun créneau disponible.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SCRIPT BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CONFIGURATION LOCALE POUR LES DATES -->
    <?php setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); ?>
</body>
</html>