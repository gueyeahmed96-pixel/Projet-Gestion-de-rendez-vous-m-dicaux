<?php
/* 1. INITIALISATION DE LA SESSION ET CONNEXION À LA BDD */
session_start(); // Démarre la session PHP
require_once '../includes/db_connect.php'; // Inclut le fichier de connexion à la base de données
require_once '../includes/tracker.php'; // Inclut le système de suivi d'activité

/* 2. VÉRIFICATION DES DROITS D'ACCÈS */
// Vérifie si l'utilisateur est connecté et a le rôle 'patient'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header('Location: ../index.php'); // Redirige vers la page d'accueil si non autorisé
    exit(); // Arrête l'exécution du script
}

// Stocke l'ID du patient connecté dans une variable
$id_patient = $_SESSION['user_id'];

/* 3. GESTION DE L'ANNULATION DE RENDEZ-VOUS */
// Vérifie si une action d'annulation est demandée et si un ID de RDV est fourni
if (isset($_GET['action']) && $_GET['action'] === 'annuler' && isset($_GET['id_rdv'])) {
    try {
        // Prépare et exécute la requête pour marquer le RDV comme annulé
        $stmt = $pdo->prepare("UPDATE rendez_vous SET statut = 'Annulé par le patient' WHERE id_rdv = ? AND id_patient_fk = ?");
        $stmt->execute([$_GET['id_rdv'], $id_patient]);
        
        // Redirige vers la même page avec un message de succès
        header('Location: mes_rdv.php?message_succes=Rendez-vous annulé.');
        exit();
    } catch (PDOException $e) {
        die("Erreur: " . $e->getMessage()); // Affiche l'erreur SQL si la requête échoue
    }
}

/* 4. RÉCUPÉRATION DES RENDEZ-VOUS DU PATIENT */
try {
    // Requête SQL pour obtenir les RDV du patient avec les infos du médecin
    $sql = "SELECT r.id_rdv, r.date_heure_rdv, r.statut, p.nom, p.prenom, p.specialite
            FROM rendez_vous AS r
            JOIN personnel AS p ON r.id_personnel_fk = p.id_personnel
            WHERE r.id_patient_fk = ?
            ORDER BY r.date_heure_rdv DESC"; // Tri par date décroissante
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_patient]);
    $mes_rdv = $stmt->fetchAll(); // Récupère tous les résultats
} catch (PDOException $e) {
    die("Erreur: " . $e->getMessage()); // Gestion des erreurs SQL
}

/* 5. CONFIGURATION LOCALE POUR LES DATES */
setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); // Formatage des dates en français
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- CONFIGURATION DE BASE DE LA PAGE -->
    <meta charset="UTF-8">
    <title>Mes Rendez-vous</title>
    
    <!-- INCLUSION DES FEUILLES DE STYLE -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- STYLES CSS PERSONNALISÉS -->
    <style>
        :root { 
            --primary-blue: #154784;    /* Couleur principale */
            --secondary-green: #25A795; /* Couleur secondaire */
            --light-gray-bg: #F7F9FC;   /* Couleur de fond */
            --sidebar-width: 280px;     /* Largeur de la sidebar */
        }
        
        body { 
            background-color: var(--light-gray-bg); 
            font-family: 'Poppins', sans-serif; 
        }
        
        /* Styles pour la sidebar */
        #sidebar { 
            min-width: var(--sidebar-width); 
            background: white; 
            box-shadow: 0 0 30px rgba(0,0,0,0.05); 
        }
        
        /* Styles pour les liens actifs */
        #sidebar .nav-link.active { 
            background-color: var(--secondary-green); 
            color: white; 
        }
    </style>
</head>

<body>
    <!-- STRUCTURE PRINCIPALE -->
    <div class="wrapper">
        <!-- BARRE LATÉRALE DE NAVIGATION -->
        <nav id="sidebar" class="d-flex flex-column p-3">
            <h4 class="text-center my-3" style="color: var(--primary-blue);">Espace Patient</h4>
            
            <!-- MENU PRINCIPAL -->
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="tableau_de_bord.php" class="nav-link">
                        <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="prendre_rdv.php" class="nav-link">
                        <i class="bi bi-calendar-plus-fill me-2"></i>Prendre RDV
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mes_rdv.php" class="nav-link active">
                        <i class="bi bi-calendar2-week-fill me-2"></i>Mes rendez-vous
                    </a>
                </li>
            </ul>
            
            <hr>
            
            <!-- LIENS SECONDAIRES -->
            <a href="../index.php" class="btn btn-outline-secondary mb-2">Retour au site</a>
            <a href="../actions/deconnexion.php" class="btn btn-danger">Se déconnecter</a>
        </nav>

        <!-- CONTENU PRINCIPAL -->
        <div class="main-content">
            <h1>Historique des rendez-vous</h1>
            <p class="text-muted">Retrouvez vos rendez-vous passés et à venir.</p>

            <!-- AFFICHAGE DES MESSAGES -->
            <?php if(isset($_GET['message_succes'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_GET['message_succes'])?>
                </div>
            <?php endif; ?>

            <!-- CARTE CONTENANT LA LISTE DES RDV -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php if (empty($mes_rdv)): ?>
                        <!-- MESSAGE SI AUCUN RDV -->
                        <div class="alert alert-info mb-0">Aucun rendez-vous.</div>
                    <?php else: ?>
                        <!-- TABLEAU DES RDV -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Heure</th>
                                        <th>Spécialiste</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- BOUCLE SUR CHAQUE RDV -->
                                    <?php foreach ($mes_rdv as $rdv): ?>
                                        <tr>
                                            <!-- COLONNE DATE/HEURE -->
                                            <td>
                                                <!-- Formatage de la date en français -->
                                                <strong><?= strftime('%A %d %b %Y', strtotime($rdv['date_heure_rdv'])) ?></strong><br>
                                                <!-- Heure seule -->
                                                <span class="text-muted"><?= date('H:i', strtotime($rdv['date_heure_rdv'])) ?></span>
                                            </td>
                                            
                                            <!-- COLONNE MÉDECIN -->
                                            <td>
                                                <!-- Nom complet du médecin -->
                                                Dr. <?= htmlspecialchars($rdv['prenom'] . ' ' . $rdv['nom']) ?><br>
                                                <!-- Spécialité -->
                                                <span class="text-muted small"><?= htmlspecialchars($rdv['specialite']) ?></span>
                                            </td>
                                            
                                            <!-- COLONNE STATUT -->
                                            <td>
                                                <!-- Badge coloré selon le statut -->
                                                <span class="badge rounded-pill <?= $rdv['statut'] === 'Confirmé' ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' ?>">
                                                    <?= htmlspecialchars($rdv['statut']) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- COLONNE ACTIONS -->
                                            <td>
                                                <!-- Affiche les boutons seulement pour les RDV confirmés et futurs -->
                                                <?php if ($rdv['statut'] === 'Confirmé' && new DateTime($rdv['date_heure_rdv']) > new DateTime()): ?>
                                                    <div class="btn-group">
                                                        <!-- Bouton Reporter -->
                                                        <a href="reporter_rdv.php?id_rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-outline-primary btn-sm">Reporter</a>
                                                        <!-- Bouton Annuler avec confirmation -->
                                                        <a href="mes_rdv.php?action=annuler&id_rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Sûr ?');">Annuler</a>
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

    <!-- SCRIPT BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>