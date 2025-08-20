<?php
// Inclusion du fichier de connexion à la base de données
require_once '../includes/db_connect.php';

// Vérification que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ÉTAPE 1: RÉCUPÉRATION DES DONNÉES DU FORMULAIRE
    // --------------------------------------------------
    $nom = trim($_POST['nom']);            // Récupération et nettoyage du nom
    $prenom = trim($_POST['prenom']);      // Récupération et nettoyage du prénom
    $email = trim($_POST['email']);        // Récupération et nettoyage de l'email
    $telephone = trim($_POST['telephone']); // Récupération et nettoyage du téléphone
    $mot_de_passe_clair = $_POST['mot_de_passe']; // Récupération du mot de passe

    // ÉTAPE 2: VALIDATION DES CHAMPS OBLIGATOIRES
    // --------------------------------------------------
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($mot_de_passe_clair)) {
        // Redirection avec message d'erreur si un champ est vide
        header('Location: ../index.php?message=' . urlencode('Veuillez remplir tous les champs.'));
        exit();
    }

    // ÉTAPE 3: HACHAGE DU MOT DE PASSE
    // --------------------------------------------------
    // Création d'un hash sécurisé du mot de passe
    $mot_de_passe_hache = password_hash($mot_de_passe_clair, PASSWORD_DEFAULT);

    // ÉTAPE 4: VÉRIFICATION DE L'EXISTENCE DE L'EMAIL
    // --------------------------------------------------
    try {
        // Préparation de la requête de vérification
        $stmt_check = $pdo->prepare("SELECT id_patient FROM patients WHERE email = ?");
        $stmt_check->execute([$email]);
        
        // Si l'email existe déjà, on redirige avec un message d'erreur
        if ($stmt_check->fetch()) {
            header('Location: ../index.php?message=' . urlencode('Cet email est déjà utilisé.'));
            exit();
        }

        // ÉTAPE 5: INSERTION DU NOUVEAU PATIENT
        // --------------------------------------------------
        // Préparation de la requête d'insertion
        $stmt_insert = $pdo->prepare("INSERT INTO patients (nom, prenom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
        
        // Exécution de la requête avec les données nettoyées
        $stmt_insert->execute([$nom, $prenom, $email, $telephone, $mot_de_passe_hache]);

        // Redirection vers la page de connexion avec message de succès
        header('Location: ../index.php?message=' . urlencode('Inscription réussie ! Vous pouvez vous connecter.'));
        exit();

    } catch (PDOException $e) {
        // Gestion des erreurs PDO
        die("Erreur : " . $e->getMessage());
    }
}