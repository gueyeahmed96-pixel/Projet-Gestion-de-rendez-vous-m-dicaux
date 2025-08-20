# Système de Gestion de Rendez-vous Médicaux - Sunu Clinique

## 📖 À Propos du Projet

Ce projet est une application web complète de prise de rendez-vous en ligne conçue pour un centre de santé. Il permet aux patients de s'inscrire, de prendre rendez-vous avec des spécialistes, et de gérer leurs consultations. Il offre également un espace personnel sécurisé pour le personnel médical afin de consulter et gérer leur planning quotidien.

Ce projet a été développé dans le cadre du cours [Nom du cours ou de l'UE] pour l'année de Licence 2.

**Auteur :** [Votre Nom Complet]
**Encadrant :** [Nom de votre professeur, si applicable]
**Date :** Août 2024

---

## ✨ Fonctionnalités Principales

### Pour les Patients :
*   **Inscription et Connexion :** Création d'un compte personnel sécurisé avec hachage de mot de passe.
*   **Tableau de Bord Personnalisé :** Vue d'ensemble avec un rappel du prochain rendez-vous.
*   **Prise de Rendez-vous Intuitive :** Processus guidé pour choisir une spécialité, un médecin et un créneau horaire disponible.
*   **Gestion des Rendez-vous :** Consultation de l'historique des rendez-vous (passés et à venir).
*   **Annulation et Report :** Possibilité d'annuler ou de reporter un rendez-vous à venir.
*   **Récupération de Mot de Passe :** Processus sécurisé par jeton pour réinitialiser un mot de passe oublié.

### Pour le Personnel Médical :
*   **Authentification Sécurisée :** Espace de connexion distinct pour le personnel.
*   **Tableau de Bord du Planning :** Vue synthétique de la journée avec des statistiques (nombre de RDV confirmés/annulés).
*   **Consultation du Planning Détaillé :** Liste chronologique des rendez-vous du jour avec les informations des patients (nom, téléphone).
*   **Navigation par Date :** Sélecteur de date pour consulter le planning des jours passés ou futurs.
*   **Menu Latéral Rétractable :** Interface moderne et ergonomique pour une navigation facile.

---

## 🛠️ Technologies et Compétences Mises en Œuvre

Ce projet mobilise une pile technologique classique et robuste pour le développement web côté serveur.

*   **Langages :** `PHP`, `SQL`, `HTML5`, `CSS3`
*   **Base de Données :** `MySQL` (gérée via phpMyAdmin)
*   **Serveur Local :** `Apache` (via XAMPP)
*   **Framework Front-end :** `Bootstrap 5` pour une interface responsive et moderne.
*   **Icônes :** `Bootstrap Icons`

### Compétences Clés :
*   **Modélisation de Données :** Conception d'une base de données relationnelle (schéma MCD et MLD).
*   **Programmation Orientée Serveur :** Utilisation de PHP pour la logique métier, la gestion des formulaires et les interactions avec la base de données.
*   **Sécurité :** Hachage des mots de passe (`password_hash`), utilisation de requêtes préparées (PDO) pour prévenir les injections SQL.
*   **Gestion de Sessions :** Maintien de l'authentification utilisateur sur l'ensemble du site.
*   **Développement Front-end :** Création d'interfaces utilisateur claires, responsives et ergonomiques.

---

## 🚀 Guide d'Installation et de Lancement

Pour lancer ce projet en local, veuillez suivre les étapes suivantes.

### Prérequis
*   Un serveur web local comme **XAMPP** ou WAMP.
*   Un navigateur web (Chrome, Firefox, etc.).
*   Un accès à **phpMyAdmin**.

### Étapes d'Installation
1.  **Cloner ou Télécharger le Projet :**
    Placez le dossier complet du projet (`gestion_rdv` ou `Projet_Gestion_rdv_médicaux`) dans le répertoire racine de votre serveur web (généralement `C:\xampp\htdocs\` pour XAMPP).

2.  **Démarrer le Serveur :**
    Lancez le panneau de contrôle de XAMPP et démarrez les services **Apache** et **MySQL**.

3.  **Créer la Base de Données :**
    *   Ouvrez phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Créez une nouvelle base de données nommée `centre_sante_db` avec l'interclassement `utf8mb4_general_ci`.
    *   Sélectionnez cette nouvelle base de données, allez dans l'onglet **"Importer"** et importez le fichier `database.sql` fourni avec le projet.
    *   (Alternativement, si un script SQL est fourni dans le README, copiez-collez son contenu dans l'onglet "SQL").

4.  **Vérifier la Connexion :**
    Le fichier de connexion à la base de données se trouve dans `includes/db_connect.php`. Par défaut, il est configuré pour un environnement XAMPP standard (utilisateur `root`, pas de mot de passe).

    ```php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'centre_sante_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    ```

5.  **Lancer l'Application :**
    Ouvrez votre navigateur et accédez à l'URL correspondant au dossier de votre projet. Par exemple :
    `http://localhost/Projet_Gestion_rdv_m%C3%A9dicaux/`

---

## 👤 Comptes de Test

Pour faciliter la démonstration, des comptes pour le personnel médical sont pré-remplis dans la base de données.

*   **Email :** `fall.massour@sunuclinique.sn` (ou les autres emails de médecins)
*   **Mot de Passe :** `abc1234`

Pour les patients, vous pouvez créer un nouveau compte directement via le formulaire d'inscription sur la page d'accueil.

---

Merci d'avoir pris le temps de découvrir ce projet !
