<!-- CARROUSEL HÉRO PRINCIPAL -->
<header id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <!-- =========================================== -->
    <!-- 1. INDICATEURS DE SLIDE (PETITS POINTS EN BAS) -->
    <!-- =========================================== -->
    <div class="carousel-indicators">
        <!-- Chaque bouton correspond à un slide du carrousel -->
        <!-- L'indicateur actif a la classe 'active' et aria-current="true" -->
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
    </div>

    <!-- =========================================== -->
    <!-- 2. CONTENU DES SLIDES -->
    <!-- =========================================== -->
    <div class="carousel-inner">
        <!-- SLIDE 1 (ACTIF PAR DÉFAUT) -->
        <div class="carousel-item active" style="background-image: url('./image/slider-acceuil.jpg')">
            <!-- Contenu superposé sur l'image -->
            <div class="carousel-caption ">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <h1 class="display-4 fw-bold mb-4">La Santé est le plus précieux de tous les trésors.</h1>
                            <p class="lead mb-4" style="max-width: 500px;">Nous offrons des soins médicaux de qualité, accessibles et personnalisés.</p>
                            <!-- Boutons d'action -->
                            <a href="#specialites" class="btn btn-outline-light me-2 btn-lg">Découvrir nos spécialités</a>
                            <a href="#section-formulaires" class="btn btn-green btn-lg">Prendre RDV en Ligne</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 -->
        <div class="carousel-item" style="background-image: url('./image/slider-acceuil3.jpg')">
            <div class="carousel-caption">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <h1 class="display-4 fw-bold mb-4">Une technologie de pointe à votre service.</h1>
                            <p class="lead mb-4" style="max-width: 500px;">Nos équipements modernes garantissent des diagnostics précis et des traitements efficaces.</p>
                            <a href="#specialites" class="btn btn-outline-light me-2 btn-lg">Découvrir nos spécialités</a>
                            <a href="#section-formulaires" class="btn btn-green btn-lg">Prendre RDV en Ligne</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 4 -->
        <div class="carousel-item" style="background-image: url('./image/slider-acceuil4.jpg')">
            <div class="carousel-caption">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <h1 class="display-4 fw-bold mb-4">Votre bien-être, notre mission quotidienne.</h1>
                            <p class="lead mb-4" style="max-width: 500px;">Une prise en charge humaine et attentive pour un rétablissement en toute sérénité.</p>
                            <a href="#specialites" class="btn btn-outline-light me-2 btn-lg">Découvrir nos spécialités</a>
                            <a href="#section-formulaires" class="btn btn-green btn-lg">Prendre RDV en Ligne</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 5 -->
        <div class="carousel-item" style="background-image: url('./image/slider-acceuil6.jpg')">
            <div class="carousel-caption">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <h1 class="display-4 fw-bold mb-4">La précision au service de votre santé.</h1>
                            <p class="lead mb-4" style="max-width: 500px;">Nos blocs opératoires sont équipés pour garantir des interventions sûres et efficaces.</p>
                            <a href="#specialites" class="btn btn-outline-light me-2 btn-lg">Découvrir nos spécialités</a>
                            <a href="#section-formulaires" class="btn btn-green btn-lg">Prendre RDV en Ligne</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 6 -->
        <div class="carousel-item" style="background-image: url('./image/slider-0.jpg')">
            <div class="carousel-caption">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7">
                            <h1 class="display-4 fw-bold mb-4">Un diagnostic clair pour des soins adaptés.</h1>
                            <p class="lead mb-4" style="max-width: 500px;">L'imagerie médicale est au cœur de notre approche pour comprendre et traiter.</p>
                            <a href="#specialites" class="btn btn-outline-light me-2 btn-lg">Découvrir nos spécialités</a>
                            <a href="#section-formulaires" class="btn btn-green btn-lg">Prendre RDV en Ligne</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================== -->
    <!-- 3. CONTRÔLES DE NAVIGATION (FLÈCHES) -->
    <!-- =========================================== -->
    <!-- Bouton Précédent -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Précédent</span>
    </button>
    
    <!-- Bouton Suivant -->
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Suivant</span>
    </button>
</header>