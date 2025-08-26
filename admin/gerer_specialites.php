<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/tracker.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('Accès non autorisé.'));
    exit();
}

$specialites = [];
try {
    // On sélectionne et on trie par la colonne 'nom_specialite'
    $stmt = $pdo->query("SELECT id_specialite, nom_specialite FROM specialites ORDER BY nom_specialite ASC");
    $specialites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Erreur lors de la récupération des spécialités : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Spécialités - Admin Panel</title>
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
            <h1 style="color: var(--primary-blue);">Gérer les Spécialités</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterSpecialiteModal"><i class="bi bi-plus-circle me-2"></i>Ajouter une spécialité</button>
        </div>
        <?php if(isset($_GET['message_succes'])): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_GET['message_succes']) ?></div><?php endif; ?>
        <?php if(isset($_GET['message_erreur'])): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_GET['message_erreur']) ?></div><?php endif; ?>
        <div class="card shadow-sm border-0"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead class="table-light"><tr><th>ID</th><th>Nom</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php foreach ($specialites as $spe): ?>
        <tr>
            <td><span class="badge bg-secondary">#<?= $spe['id_specialite'] ?></span></td>
            <td><strong><?= htmlspecialchars($spe['nom_specialite']) ?></strong></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modifierSpecialiteModal<?= $spe['id_specialite'] ?>"><i class="bi bi-pencil-fill"></i></button>
                <a href="actions/actions_specialites.php?action=supprimer&id=<?= $spe['id_specialite'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Attention ! Supprimer cette spécialité la retirera de tous les médecins associés. Êtes-vous sûr ?');"><i class="bi bi-trash-fill"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody></table></div></div></div>
    </div>

<!-- Modale d'Ajout -->
<div class="modal fade" id="ajouterSpecialiteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Ajouter une Spécialité</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="actions/actions_specialites.php" method="POST"><div class="modal-body">
        <input type="hidden" name="action" value="ajouter">
        <div class="mb-3"><label for="nom" class="form-label">Nom de la spécialité</label><input type="text" id="nom" name="nom_specialite" class="form-control" required></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div></form>
</div></div></div>

<!-- Modales de Modification -->
<?php foreach ($specialites as $spe): ?>
<div class="modal fade" id="modifierSpecialiteModal<?= $spe['id_specialite'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier "<?= htmlspecialchars($spe['nom_specialite']) ?>"</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="actions/actions_specialites.php" method="POST"><div class="modal-body">
        <input type="hidden" name="action" value="modifier"><input type="hidden" name="id_specialite" value="<?= $spe['id_specialite'] ?>">
        <div class="mb-3"><label for="nom_<?= $spe['id_specialite'] ?>" class="form-label">Nouveau nom</label><input type="text" id="nom_<?= $spe['id_specialite'] ?>" class="form-control" name="nom" value="<?= htmlspecialchars($spe['nom_specialite']) ?>" required></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div></form>
</div></div></div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>