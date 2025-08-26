<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('Accès non autorisé.'));
    exit();
}

// Récupération de toute la liste du personnel
$personnel_list = [];
try {
    // Cette requête est correcte, elle récupère 'specialite_nom'
    $sql = "SELECT p.id_personnel, p.nom, p.prenom, p.email, p.role, s.nom_specialite AS specialite_nom
            FROM personnel AS p
            LEFT JOIN specialites AS s ON p.id_specialite_fk = s.id_specialite
            ORDER BY p.role DESC, p.nom ASC";
    $personnel_list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { error_log($e->getMessage()); }

// Récupération des spécialités pour les formulaires
$specialites = [];
try {
    // CORRECTION : S'assurer qu'on utilise le bon nom de colonne 'nom_specialite'
    $sql_specialites = "SELECT id_specialite, nom_specialite FROM specialites ORDER BY nom_specialite ASC";
    $specialites = $pdo->query($sql_specialites)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { error_log($e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer le Personnel - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_style.css">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--primary-blue);">Gestion du Personnel</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterPersonnelModal"><i class="bi bi-plus-circle me-2"></i>Ajouter un membre</button>
        </div>
        <?php if(isset($_GET['message_succes'])): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['message_succes']) ?></div><?php endif; ?>
        <?php if(isset($_GET['message_erreur'])): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_GET['message_erreur']) ?></div><?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Nom Complet</th><th>Email</th><th>Spécialité</th><th>Rôle</th><th class="text-end">Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($personnel_list as $membre): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($membre['email']) ?></td>
                                <td>
                                    <?php 
                                    if (!empty($membre['specialite_nom'])) {
                                        echo htmlspecialchars($membre['specialite_nom']);
                                    } else {
                                        echo '<span class="text-muted fst-italic">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td><span class="badge rounded-pill text-bg-<?= $membre['role'] === 'admin' ? 'danger' : 'info' ?>"><?= htmlspecialchars(ucfirst($membre['role'])) ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modifierPersonnelModal<?= $membre['id_personnel'] ?>"><i class="bi bi-pencil-fill"></i></button>
                                    <a href="actions/actions_personnel.php?action=supprimer&id=<?= $membre['id_personnel'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr ?');"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Modale d'Ajout -->
<div class="modal fade" id="ajouterPersonnelModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Ajouter un Membre</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="actions/actions_personnel.php" method="POST"><div class="modal-body">
        <input type="hidden" name="action" value="ajouter">
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required></div><div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required></div></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe (provisoire)</label><input type="password" name="mot_de_passe" class="form-control" required></div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Spécialité</label>
                <!-- CORRECTION : Le name est id_specialite_fk -->
                <select name="id_specialite_fk" class="form-select">
                    <option value="">Non Applicable</option>
                    <?php foreach($specialites as $spe): ?>
                        <!-- CORRECTION : La value est l'ID, le texte est nom_specialite -->
                        <option value="<?= $spe['id_specialite'] ?>"><?= htmlspecialchars($spe['nom_specialite']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" required><option value="medecin" selected>Médecin</option><option value="secretaire">Secrétaire</option><option value="admin">Administrateur</option></select>
            </div>
        </div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter le membre</button></div></form>
  </div></div>
</div>

<!-- Modales de Modification -->
<?php foreach ($personnel_list as $membre): ?>
<div class="modal fade" id="modifierPersonnelModal<?= $membre['id_personnel'] ?>" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="actions/actions_personnel.php" method="POST"><div class="modal-body">
        <input type="hidden" name="action" value="modifier"><input type="hidden" name="id_personnel" value="<?= $membre['id_personnel'] ?>">
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($membre['prenom']) ?>" required></div><div class="col-md-6 mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($membre['nom']) ?>" required></div></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($membre['email']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="mot_de_passe" class="form-control" placeholder="Laisser vide pour ne pas changer"></div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Spécialité</label>
                <!-- CORRECTION : Le name est id_specialite_fk -->
                <select name="id_specialite_fk" class="form-select">
                    <option value="">Non Applicable</option>
                    <?php foreach($specialites as $spe): ?>
                        <!-- CORRECTION : On compare specialite_nom avec nom_specialite -->
                        <option value="<?= $spe['id_specialite'] ?>" <?= (isset($membre['specialite_nom']) && $membre['specialite_nom'] === $spe['nom_specialite']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($spe['nom_specialite']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3"><label class="form-label">Rôle</label><select name="role" class="form-select" required><option value="medecin" <?= $membre['role'] === 'medecin' ? 'selected' : '' ?>>Médecin</option><option value="secretaire" <?= $membre['role'] === 'secretaire' ? 'selected' : '' ?>>Secrétaire</option><option value="admin" <?= $membre['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option></select></div></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à jour</button></div></form>
  </div></div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>