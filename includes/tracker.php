<?php
/**
 * FICHIER : includes/tracker.php
 * BUT : Suivi de l'activité des utilisateurs connectés
 * PRÉ-REQUIS : 
 * - Doit être inclus après session_start()
 * - Requiert une connexion active à la base de données ($pdo)
 */

// Vérifie si un utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // Date/heure actuelle au format SQL
    $now = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user_id'];
    $table_to_update = ''; // Variable pour stocker le nom de la table à mettre à jour

    /**
     * DÉTERMINATION DE LA TABLE CIBLE
     * Selon le rôle de l'utilisateur :
     * - Patients -> table 'patients'
     * - Personnel (médecin, secrétaire, admin) -> table 'personnel'
     */
    if ($_SESSION['user_role'] === 'patient') {
        $table_to_update = 'patients';
        $id_column = 'id_patient'; // Colonne ID pour les patients
    } elseif (in_array($_SESSION['user_role'], ['medecin', 'secretaire', 'admin'])) {
        $table_to_update = 'personnel';
        $id_column = 'id_personnel'; // Colonne ID pour le personnel
    }

    // Si une table valide a été identifiée
    if (!empty($table_to_update)) {
        try {
            /**
             * MISE À JOUR DE LA DERNIÈRE ACTIVITÉ
             * Note : Pas besoin de requête préparée car :
             * - $table_to_update et $id_column sont contrôlés par nous
             * - $now est généré par date()
             * - $user_id vient de $_SESSION (déjà filtré)
             */
            $pdo->query("UPDATE {$table_to_update} 
                        SET derniere_activite = '{$now}' 
                        WHERE {$id_column} = {$user_id}");
                        
        } catch (PDOException $e) {
            /**
             * GESTION DES ERREURS
             * On utilise error_log() plutôt que die() pour :
             * - Ne pas interrompre l'expérience utilisateur
             * - Conserver une trace des erreurs pour le débogage
             */
            error_log('Tracker Error: ' . $e->getMessage());
        }
    }
}
?>