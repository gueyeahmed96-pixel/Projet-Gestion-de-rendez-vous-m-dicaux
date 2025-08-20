<!-- SECTION FORMULAIRES DE CONNEXION/INSCRIPTION -->
<section id="section-formulaires" class="py-5 bg-light form-section">
    <div class="container">
        <!-- En-tête de section -->
        <div class="text-center mb-5 section-title">
            <h2>Accédez à votre Espace</h2>
            <p>Inscrivez-vous ou connectez-vous pour gérer vos rendez-vous.</p>
        </div>

        <!-- Conteneur principal des formulaires -->
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <!-- Affichage des messages système -->
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-info text-center" role="alert">
                        <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                    </div>
                <?php endif; ?>

                <!-- Carte contenant les onglets -->
                <div class="card">
                    <!-- En-tête avec onglets de navigation -->
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="authTabs" role="tablist">
                            <!-- Onglet Connexion -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#connexion" 
                                        type="button">
                                    Connexion
                                </button>
                            </li>
                            <!-- Onglet Inscription -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#inscription" 
                                        type="button">
                                    Inscription
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Corps de la carte -->
                    <div class="card-body p-4 p-md-5">
                        <div class="tab-content">
                            <!-- PANEL DE CONNEXION -->
                            <div class="tab-pane fade show active" id="connexion" role="tabpanel">
                                <h5 class="card-title mb-4">Heureux de vous revoir !</h5>
                                
                                <!-- Formulaire de connexion -->
                                <form action="actions/connexion_action.php" method="POST">
                                    <!-- Champ Email -->
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    
                                    <!-- Champ Mot de passe -->
                                    <div class="mb-3">
                                        <label class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" name="mot_de_passe" required>
                                    </div>
                                    
                                    <!-- Lien mot de passe oublié -->
                                    <div class="text-end mb-3">
                                        <a href="actions/forgot_password.php" class="small">
                                            Mot de passe oublié ?
                                        </a>
                                    </div>
                                    
                                    <!-- Bouton de soumission -->
                                    <button type="submit" class="btn btn-submit w-100">
                                        Se connecter
                                    </button>
                                </form>
                            </div>

                            <!-- PANEL D'INSCRIPTION -->
                            <div class="tab-pane fade" id="inscription" role="tabpanel">
                                <h5 class="card-title mb-4">Créez votre compte</h5>
                                
                                <!-- Formulaire d'inscription -->
                                <form action="actions/inscription_action.php" method="POST">
                                    <!-- Ligne Nom/Prénom -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom</label>
                                            <input type="text" class="form-control" name="nom" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Prénom</label>
                                            <input type="text" class="form-control" name="prenom" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Champ Email -->
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    
                                    <!-- Champ Téléphone -->
                                    <div class="mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" name="telephone" required>
                                    </div>
                                    
                                    <!-- Champ Mot de passe -->
                                    <div class="mb-3">
                                        <label class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" name="mot_de_passe" required>
                                    </div>
                                    
                                    <!-- Bouton de soumission -->
                                    <button type="submit" class="btn btn-submit w-100">
                                        Créer mon compte
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>