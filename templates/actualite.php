<?php
/**
 * Page de détail d'un article de santé
 * 
 * Cette page affiche le contenu complet d'un article avec son image, titre,
 * catégorie, date de publication et contenu.
 */

// --- INITIALISATION ---
// Démarrage de la session et connexion à la base de données
session_start();
require_once '../includes/db_connect.php';

// --- VÉRIFICATION DE L'ARTICLE ---
// Vérification que l'ID de l'article est présent et valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');  // Redirection si ID manquant ou invalide
    exit();
}
$article_id = (int)$_GET['id'];  // Conversion en entier pour sécurité

// --- FONCTION DE RÉCUPÉRATION DES ARTICLES ---
/**
 * Récupère les détails d'un article spécifique
 * @param int $id L'identifiant de l'article
 * @return array|false Les détails de l'article ou false si non trouvé
 */
function getArticleDetails($id) {
    // Base de données simulée des articles
    $all_articles = [
        1 => [
            'image' => '../image/nutrition.jpg',
            'titre' => "Les 5 clés d'une alimentation saine pour un cœur en pleine forme",
            'categorie' => 'Nutrition',
            'date' => '15 Juin 2024',
            'auteur' => 'Dr. Sophie Sylla',
            'contenu' => "<p class=\"lead\">Adopter une alimentation équilibrée est fondamental pour une bonne santé cardiovasculaire. Voici cinq règles d'or.</p><h5>1. Privilégiez les 'bons gras'</h5><p>Les acides gras insaturés (huile d'olive, avocats, noix, poissons gras) sont bénéfiques pour votre cœur.</p><h5>2. Mangez des fruits et légumes</h5><p>Riches en vitamines, fibres et antioxydants, ils protègent vos artères. Visez au moins cinq portions par jour.</p><h5>3. Réduisez le sel</h5><p>L'excès de sodium favorise l'hypertension. Évitez les plats préparés et limitez la charcuterie.</p><h5>4. Choisissez les bonnes céréales</h5><p>Optez pour les céréales complètes (pain complet, riz brun) plutôt que les céréales raffinées.</p><h5>5. Limitez le sucre ajouté</h5><p>Les boissons sucrées et pâtisseries augmentent le risque de surpoids et de diabète.</p>"
        ],
        2 => [
            'image' => '../image/sommeil.jpg',
            'titre' => "L'importance du sommeil : comment améliorer vos nuits ?",
            'categorie' => 'Bien-être',
            'date' => '10 Juin 2024',
            'auteur' => 'Dr. Moussa Faye',
            'contenu' => "<p class=\"lead\">Un sommeil de mauvaise qualité peut avoir des conséquences graves. Voici quelques conseils pratiques.</p><h5>Créez un rituel de coucher</h5><p>Trente minutes avant de dormir, déconnectez-vous des écrans. Préférez une activité calme comme la lecture.</p><h5>Respectez des horaires réguliers</h5><p>Essayez de vous coucher et de vous lever à la même heure tous les jours, même le week-end, pour réguler votre horloge biologique.</p>"
        ],
        3 => [
            'image' => '../image/sport.jpg',
            'titre' => "Activité physique : nos conseils pour débuter en douceur",
            'categorie' => 'Sport & Santé',
            'date' => '05 Juin 2024',
            'auteur' => 'Dr. Mor Diop',
            'contenu' => "<p class=\"lead\">Reprendre une activité physique peut sembler intimidant, mais les bienfaits sont immenses.</p><h5>Choisissez une activité que vous aimez</h5><p>La clé de la régularité est le plaisir. Marche rapide, natation, vélo, danse...</p><h5>Fixez-vous des objectifs réalistes</h5><p>Commencez par 2 à 3 séances de 30 minutes par semaine. L'important est de bouger régulièrement.</p>"
        ]
    ];
    
    return $all_articles[$id] ?? false;  // Retourne l'article ou false si non trouvé
}

// --- RÉCUPÉRATION DE L'ARTICLE ---
$article_details = getArticleDetails($article_id);
if (!$article_details) {
    header('Location: ../index.php?message=Article introuvable');  // Redirection si article non trouvé
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées et liens CSS -->
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article_details['titre']) ?></title>
    
    <!-- Framework Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Police Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Feuille de style personnalisée -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Inclusion de la barre de navigation -->
    <?php include '../includes/navbar.php'; ?>

    <!-- En-tête de la page -->
    <header class="page-header text-center">
        <div class="container">
            <!-- Catégorie de l'article -->
            <p class="text-uppercase fw-bold" style="color: var(--secondary-green);">
                <?= htmlspecialchars($article_details['categorie']) ?>
            </p>
            
            <!-- Titre de l'article -->
            <h1 class="display-4">
                <?= htmlspecialchars($article_details['titre']) ?>
            </h1>
            
            <!-- Date et auteur -->
            <p>
                Publié le <?= htmlspecialchars($article_details['date']) ?> par <?= htmlspecialchars($article_details['auteur']) ?>
            </p>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Image de l'article -->
                <img src="<?= htmlspecialchars($article_details['image']) ?>" 
                     alt="<?= htmlspecialchars($article_details['titre']) ?>" 
                     class="img-fluid mb-5 rounded shadow">
                
                <!-- Contenu de l'article -->
                <div class="article-content">
                    <?= $article_details['contenu'] ?>  <!-- Contenu déjà formaté en HTML -->
                </div>
                
                <hr class="my-5">
                
                <!-- Bouton de retour -->
                <a href="../index.php#news" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux actualités
                </a>
            </div>
        </div>
    </main>

    <!-- Inclusion du pied de page -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour la gestion de la navbar -->
    <script>
    // On sélectionne les éléments nécessaires
    const navbar = document.querySelector('.navbar');
    const navbarToggler = document.querySelector('.navbar-toggler');

    // On récupère le nom de la page actuelle pour adapter le comportement
    const currentPage = "<?= basename($_SERVER['PHP_SELF']) ?>";

    /**
     * Gère le style de la navbar en fonction du défilement
     */
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

    /**
     * Gère l'affichage de la navbar quand le menu est ouvert sur mobile
     */
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