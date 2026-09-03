<?php
// Inclure le header

include './inc/header.php';
?>

<!-- 404 Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="bi bi-exclamation-triangle display-1 text-primary"></i>
                <h3 class="display-">Erreur</h3>
                <h1 class="display-1">500</h1>
                <h1 class="mb-4">Aucune information trouvée </h1>
                <p class="mb-4">Si vous ne trouvez tourjour pas de solution, veuillez contacter le service client.</p>
                <a class="btn btn-primary py-3 px-5" href="/home">Rétour à la page d'accueil</a>
            </div>
        </div>
    </div>
</div>
<!-- 404 End -->
<?php

// Inclure le footer si nécessaire
include './inc/footer.php';
?>