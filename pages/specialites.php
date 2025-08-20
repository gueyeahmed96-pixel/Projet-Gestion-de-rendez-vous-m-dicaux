<!-- SECTION DES SPÉCIALITÉS MÉDICALES -->
<section id="specialites" class="py-5 bg-light">
    <!-- Conteneur principal pour le contenu -->
    <div class="container">
        <!-- En-tête de section avec titre et description -->
        <div class="text-center mb-5 section-title">
            <h2>Nos Spécialités</h2>
            <p>Des équipes d'experts dédiés à votre bien-être.</p>
        </div>

        <!-- Vérification s'il y a des spécialités à afficher -->
        <?php if (!empty($specialites)): ?>
            <!-- Grille Bootstrap pour l'affichage responsive -->
            <div class="row g-4">
                <!-- Boucle sur chaque spécialité -->
                <?php foreach ($specialites as $spe): 
                    // Récupération des détails supplémentaires (image, icône)
                    $details = getSpecialiteDetails($spe); ?>
                    
                    <!-- Colonne pour chaque spécialité (3 colonnes par ligne sur desktop) -->
                    <div class="col-md-4 d-flex align-items-stretch">
                        <!-- Carte de spécialité - s'étire pour avoir la même hauteur -->
                        <div class="specialite-card text-center w-100">
                            <!-- Image représentative de la spécialité -->
                            <img src="<?= htmlspecialchars($details['image']) ?>" 
                                 class="card-img-top" 
                                 alt="Image pour <?= htmlspecialchars($spe) ?>">
                            
                            <!-- Corps de la carte -->
                            <div class="card-body">
                                <!-- Titre avec icône -->
                                <h5 class="card-title mt-2">
                                    <i class="bi <?= htmlspecialchars($details['icon']) ?> me-2"></i>
                                    <?= htmlspecialchars($spe) ?>
                                </h5>
                                
                                <!-- Description courte -->
                                <p class="text-muted">Consultations de pointe.</p>
                                
                                <!-- Lien invisible qui couvre toute la carte -->
                                <a href="templates/specialite.php?nom=<?= urlencode($spe) ?>" 
                                   class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>