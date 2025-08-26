<!-- Fichier : admin/includes/sidebar.php (Version Offcanvas) -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
  
  <div class="offcanvas-header border-bottom border-secondary">
    <h5 class="offcanvas-title" id="adminSidebarLabel">
        <i class="bi bi-shield-shaded me-2"></i>Admin Panel
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column p-0">
    <ul class="nav nav-pills flex-column p-3 mb-auto">
        <?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link text-white <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill me-2"></i>Tableau de bord
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="gerer_personnel.php" class="nav-link text-white <?= ($currentPage == 'gerer_personnel.php') ? 'active' : '' ?>">
                <i class="bi bi-people-fill me-2"></i>Gérer le Personnel
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="gerer_specialites.php" class="nav-link text-white <?= ($currentPage == 'gerer_specialites.php') ? 'active' : '' ?>">
                <i class="bi bi-tag-fill me-2"></i>Gérer Spécialités
            </a>
        </li>
    </ul>
    <div class="p-3 border-top border-secondary">
        <a href="../index.php" class="btn btn-outline-light w-100 mb-2">Retour au site</a>
        <a href="../deconnexion.php" class="btn btn-danger w-100">Se déconnecter</a>
    </div>
  </div>
</div>