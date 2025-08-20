<?php
// 1. DÉMARRER LA SESSION ET INCLURE LES FICHIERS DE CONFIGURATION
session_start();
require_once 'includes/db_connect.php';

// 2. LOGIQUE PHP NÉCESSAIRE POUR LES VUES PARTIELLES
// Cette logique doit rester dans le fichier principal pour être accessible par les 'include'
try {
    $stmt = $pdo->query("SELECT DISTINCT specialite FROM personnel WHERE role = 'medecin' ORDER BY specialite");
    $specialites = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { 
    $specialites = []; 
}

function getSpecialiteDetails($specialite) {
    $details = [
        'Cardiologie' => ['icon' => 'bi-heart-pulse-fill', 'image' => './image/cardiologie.jpg'],
        'Dermatologie' => ['icon' => 'bi-bandaid-fill', 'image' => './image/dermatologie.jpg'],
        'Gynécologie' => ['icon' => 'bi-person-standing-dress', 'image' => './image/gynecologie.jpg'],
        'Médecine Générale' => ['icon' => 'bi-person-fill', 'image' => './image/generale.jpg'],
        'Ophtalmologie' => ['icon' => 'bi-eye-fill', 'image' => './image/ophtalmologie.jpg'],
        'Pédiatrie' => ['icon' => 'bi-person-arms-up', 'image' => './image/pediatrie.jpg'],
        'default' => ['icon' => 'bi-clipboard2-pulse-fill', 'image' => './image/default.jpg']
    ];
    return $details[$specialite] ?? $details['default'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUNU_CLINIQUE</title>
    <!-- Les liens CSS restent ici pour s'appliquer à toute la page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Inclure votre fichier de style personnalisé -->
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>

    <?php 
    // 3. ASSEMBLAGE DE LA PAGE
    
    // Inclusion de la barre de navigation
    include 'includes/navbar.php'; 

    // Inclusion des différentes sections de la page
    include 'pages/hero.php';
    include 'pages/about.php';
    include 'pages/specialites.php';
    include 'pages/news.php';
    include 'pages/forms.php';

    // Inclusion du pied de page
    include 'includes/footer.php'; 
    ?>

    <!-- Les scripts JS restent à la fin -->
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