<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php'); 
    exit();
}

// CORRECTION : On récupère l'ID ET le nom de la spécialité
$liste_specialites = [];
try {
    $stmt = $pdo->query("SELECT id_specialite, nom_specialite FROM specialites ORDER BY nom_specialite ASC");
    $liste_specialites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur: Impossible de charger la liste des spécialités. " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Membre - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-blue: #154784; --light-gray-bg: #F7F9FC; } 
        body { background-color: var(--light-gray-bg); font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4" style="color: var(--primary-blue);">Ajouter un nouveau membre</h1>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- CORRECTION : Le chemin pointe maintenant vers admin/actions/actions_personnel.php -->
                    <form action="actions/actions_personnel.php" method="POST">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe (provisoire)</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Spécialité</label>
                            <!-- CORRECTION : Le name est maintenant "id_specialite_fk" -->
                            <select name="id_specialite_fk" class="form-select">
                                <option value="">-- Non Applicable / Choisir --</option>
                                <?php foreach ($liste_specialites as $spe): ?>
                                    <!-- CORRECTION : La value est l'ID, le texte est le nom -->
                                    <option value="<?= $spe['id_specialite'] ?>"><?= htmlspecialchars($spe['nom_specialite']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select" required>
                                <option value="medecin" selected>Médecin</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Ajouter le membre</button>
                        <a href="gerer_personnel.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>