<?php
// On s'assure que la session est bien démarrée.
// Le '@' évite un avertissement si la session est déjà active.
@session_start();

// On définit la constante BASE_URL si elle n'existe pas déjà.
// Assurez-vous que le nom du dossier est correct !
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Projet_Gestion_rdv_médicaux/');
}
?>
<!-- =================================================== -->
<!-- BARRE DE NAVIGATION PRINCIPALE - RESPONSIVE ET MODERNE -->
<!-- =================================================== -->
<nav class="navbar navbar-expand-lg fixed-top py-3 navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= BASE_URL ?>index.php">
            SUNU Clinique
        </a>
        <button class="navbar-toggler" type="button" 
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php#hero">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php#about">À Propos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php#specialites">Spécialités</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php#news">Actualités</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php#contact">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <?php 
                // CORRECTION : On vérifie que TOUTES les clés de session existent avant de les utiliser.
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_nom']) && isset($_SESSION['user_role'])): 
                ?>
                    <div class="dropdown">
                        <button class="btn btn-primary-gradient dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i> 
                            <?= htmlspecialchars($_SESSION['user_nom']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php
                                // AMÉLIORATION : On détermine le lien de manière plus lisible
                                $dashboard_link = '';
                                switch ($_SESSION['user_role']) {
                                    case 'patient':
                                        $dashboard_link = BASE_URL . 'patient/tableau_de_bord.php';
                                        break;
                                    case 'admin':
                                        $dashboard_link = BASE_URL . 'admin/dashboard.php';
                                        break;
                                    default:
                                        $dashboard_link = BASE_URL . 'personnel/planning.php';
                                        break;
                                }
                            ?>
                            <li>
                                <a class="dropdown-item" href="<?= $dashboard_link ?>">
                                    Mon Espace
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>actions/deconnexion.php">
                                    Se déconnecter
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>index.php#section-formulaires" class="btn btn-primary-gradient">
                        Prendre Rendez-vous
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>