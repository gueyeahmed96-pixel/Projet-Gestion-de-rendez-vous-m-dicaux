<?php
session_start();
require_once '../includes/db_connect.php';


// --- AMÉLIORATION 1 : Sécurisation et validation des entrées ---
// On s'assure que le paramètre 'nom' existe et n'est pas vide.
if (empty($_GET['nom'])) {
    header('Location: index.php');
    exit();
}
// On nettoie la variable pour plus de sécurité, même si on l'utilise dans un tableau.
$nom_specialite = trim(strip_tags($_GET['nom']));


// --- AMÉLIORATION 2 : Externalisation du contenu pour plus de clarté ---
// Dans un projet plus grand, ce tableau viendrait d'un autre fichier ou de la BDD.
function getSpecialitePageDetails($nom) {
    $all_details = [
        'Médecine Générale' => [
            'image' => '../image/generale.jpg', 
            'titre' => 'Médecine Générale',
            'description' => 'Le médecin généraliste est votre premier point de contact pour tout problème de santé. Il assure un suivi global et coordonné, de la prévention au traitement des maladies courantes, et vous oriente vers des spécialistes si nécessaire.',
            'prestations' => ['Consultation de suivi', 'Bilan de santé complet', 'Vaccinations', 'Gestion des maladies chroniques', 'Certificats médicaux']
        ],
        'Cardiologie' => [
            'image' => '../image/cardiologie.jpg', 
            'titre' => 'Cardiologie',
            'description' => 'Notre pôle de cardiologie est dédié au diagnostic et au traitement des maladies du cœur et des vaisseaux sanguins. Nous utilisons des technologies de pointe pour assurer des soins optimaux.',
            'prestations' => ['Électrocardiogramme (ECG)', 'Échographie cardiaque', 'Test d’effort', 'Suivi de l’hypertension', 'Prévention des risques cardiaques']
        ],
        'Pédiatrie' => [
            'image' => '../image/pediatrie.jpg', 
            'titre' => 'Pédiatrie',
            'description' => 'La santé de vos enfants est notre priorité. Nos pédiatres accompagnent la croissance et le développement de vos enfants, de la naissance à l\'adolescence, en offrant des soins préventifs et un suivi régulier.',
            'prestations' => ['Suivi du nouveau-né', 'Urgences pédiatriques', 'Conseils en nutrition infantile', 'Dépistage des troubles', 'Vaccination du nourrisson']
        ],
        'Dermatologie' => [
            'image' => '../image/dermatologie.jpg', 
            'titre' => 'Dermatologie',
            'description' => 'La dermatologie traite les maladies de la peau, des cheveux et des ongles. Notre service propose des consultations pour toutes les affections cutanées, des plus courantes (acné, eczéma) aux plus complexes.',
            'prestations' => ['Traitement de l\'acné', 'Dépistage des cancers de la peau', 'Chirurgie dermatologique mineure', 'Soins esthétiques (peeling, laser)']
        ],
        'Gynécologie' => [
            'image' => '../image/gynecologie.jpg', 
            'titre' => 'Gynécologie',
            'description' => 'Notre service offre un accompagnement complet pour la santé des femmes à toutes les étapes de leur vie, du suivi de routine au suivi de grossesse et à la ménopause.',
            'prestations' => ['Consultation annuelle', 'Suivi de grossesse et échographie', 'Contraception', 'Dépistage (frottis)', 'Prise en charge de la ménopause']
        ],
        'Ophtalmologie' => [
            'image' => '../image/ophtalmologie.jpg', 
            'titre' => 'Ophtalmologie', 
            'description' => 'L\'ophtalmologie est la spécialité dédiée à la santé de vos yeux. Nos spécialistes diagnostiquent et traitent les troubles de la vision et les maladies oculaires.', // CORRECTION : La description était celle de la Gynécologie
            'prestations' => ['Examen de la vue complet', 'Fond d\'œil', 'Dépistage du glaucome', 'Suivi de la cataracte', 'Prescription de lunettes']
        ]
    ];
    return $all_details[$nom] ?? false;
}

$specialty_details = getSpecialitePageDetails($nom_specialite);

if (!$specialty_details) {
    header('Location: index.php?message=Specialite inconnue');
    exit();
}

// --- AMÉLIORATION 3 : Gestion d'erreur plus robuste ---
$medecins_specialistes = [];
try {
    $stmt = $pdo->prepare("SELECT nom, prenom FROM personnel WHERE specialite = ? AND role = 'medecin'");
    $stmt->execute([$nom_specialite]);
    $medecins_specialistes = $stmt->fetchAll();
} catch (PDOException $e) {
    // Dans un vrai site, on loggerait l'erreur sans arrêter le script pour l'utilisateur
    error_log("Erreur SQL dans specialite.php : " . $e->getMessage());
    // On laisse $medecins_specialistes en tant que tableau vide, la page peut continuer à s'afficher.
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spécialité : <?= htmlspecialchars($specialty_details['titre']) ?> - Clinique VitaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylespecialite.css">
</head>
<body>

    <?php include '../includes/navbar.php'; ?> 

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold"><?= htmlspecialchars($specialty_details['titre']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="../index.php">Accueil</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($specialty_details['titre']) ?></li>
                </ol>
            </nav>
        </div>
    </header>

    <main class="container py-5 content-section">
        <div class="row g-5">
            <div class="col-lg-5">
                <img src="<?= htmlspecialchars($specialty_details['image']) ?>" class="img-fluid" alt="Illustration pour <?= htmlspecialchars($specialty_details['titre']) ?>">
            </div>

            <div class="col-lg-7">
                <h2><?= htmlspecialchars($specialty_details['titre']) ?></h2>
                <p class="lead text-muted"><?= htmlspecialchars($specialty_details['description']) ?></p>

                <h3>Nos prestations</h3>
                <div class="row">
                    <?php foreach ($specialty_details['prestations'] as $prestation): ?>
                        <div class="col-md-6">
                            <p><i class="bi bi-check-lg text-success me-2"></i><?= htmlspecialchars($prestation) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h3>Nos spécialistes</h3>
                <?php if (!empty($medecins_specialistes)): ?>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <?php foreach ($medecins_specialistes as $medecin): ?>
                            <div class="p-2 bg-light border rounded">
                                <i class="bi bi-person-fill me-1"></i>Dr. <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted fst-italic">Aucun médecin n'est actuellement listé pour cette spécialité.</p>
                <?php endif; ?>

                <div class="mt-5">
                    <a href="../patient/prendre_rdv.php" class="btn btn-green btn-lg">Prendre rendez-vous dans ce service</a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // On sélectionne les éléments nécessaires
    const navbar = document.querySelector('.navbar');
    const navbarToggler = document.querySelector('.navbar-toggler');

    // On récupère le nom de la page actuelle pour adapter le comportement
    const currentPage = "<?= basename($_SERVER['PHP_SELF']) ?>";

    // Fonction qui gère le style de la navbar en fonction du scroll
    function handleScrollStyle() {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
            navbar.classList.remove('navbar-dark');
            navbar.classList.add('navbar-light');
        } else {
            // On ne redevient transparent que si on est sur la page d'accueil
            // et que le menu n'est pas déplié
            if (currentPage === 'index.php' && !navbar.classList.contains('navbar-expanded')) {
                navbar.classList.remove('navbar-scrolled');
                navbar.classList.add('navbar-dark');
                navbar.classList.remove('navbar-light');
            }
        }
    }

    // Fonction qui s'assure que la navbar a un fond quand le menu est ouvert sur mobile
    function handleTogglerClick() {
        // Si la navbar n'a pas encore de fond blanc (on est en haut de index.php)
        if (!navbar.classList.contains('navbar-scrolled')) {
            // On ajoute ou retire une classe spéciale pour forcer un fond
            navbar.classList.toggle('navbar-expanded');
        }
    }

    // Comportement initial au chargement de la page
    if (currentPage !== 'index.php') {
        // Sur les autres pages, la navbar est blanche dès le début
        navbar.classList.add('navbar-scrolled');
        navbar.classList.add('navbar-light');
        navbar.classList.remove('navbar-dark');
    } else {
        // Sur l'index, on vérifie la position de scroll au cas où la page est rechargée en cours de défilement
        handleScrollStyle();
    }

    // On attache les écouteurs d'événements
    window.addEventListener('scroll', handleScrollStyle);
    if (navbarToggler) {
        navbarToggler.addEventListener('click', handleTogglerClick);
    }
</script>
</body>
</html>