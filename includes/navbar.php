<!-- =================================================== -->
<!-- BARRE DE NAVIGATION PRINCIPALE - RESPONSIVE ET MODERNE -->
<!-- =================================================== -->
<nav class="navbar navbar-expand-lg fixed-top py-3 navbar-dark">
    <!-- Conteneur principal pour le contenu de la navbar -->
    <div class="container">
        <!-- Logo / Nom de la clinique (lien vers l'accueil) -->
        <a class="navbar-brand fw-bold fs-4" href="<?= BASE_URL ?>index.php">
            SUNU Clinique
        </a>

        <!-- Bouton hamburger pour version mobile -->
        <button class="navbar-toggler" type="button" 
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenu principal de la navbar (s'effondre en mobile) -->
        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Navigation principale - Centrée horizontalement -->
            <ul class="navbar-nav mx-auto">
                <!-- Lien vers la section Hero/Accueil -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php#hero">Accueil</a>
                </li>
                
                <!-- Lien vers la section À Propos -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php#about">À Propos</a>
                </li>
                
                <!-- Lien vers la section Spécialités -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php#specialites">Spécialités</a>
                </li>
                
                <!-- Lien vers la section Actualités -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php#news">Actualités</a>
                </li>
                
                <!-- Lien vers la section Contact -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>index.php#contact">Contact</a>
                </li>
            </ul>

            <!-- Zone utilisateur (à droite) -->
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Si utilisateur connecté : Menu déroulant profil -->
                    <div class="dropdown">
                        <!-- Bouton du menu profil -->
                        <button class="btn btn-primary-gradient dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i> 
                            <?= htmlspecialchars($_SESSION['user_nom']) ?>
                        </button>
                        
                        <!-- Contenu du menu déroulant -->
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Lien vers l'espace personnel selon le rôle -->
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?><?= 
                                    $_SESSION['user_role'] === 'patient' ? 'patient/tableau_de_bord.php' : 
                                    ($_SESSION['user_role'] === 'admin' ? 'admin/dashboard.php' : 'personnel/planning.php') 
                                ?>">
                                    Mon Espace
                                </a>
                            </li>
                            
                            <!-- Séparateur -->
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Lien de déconnexion -->
                            <li>
                                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>actions/deconnexion.php">
                                    Se déconnecter
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Si visiteur non connecté : Bouton de prise de RDV -->
                    <a href="<?= BASE_URL ?>index.php#section-formulaires" class="btn btn-primary-gradient">
                        Prendre Rendez-vous
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>