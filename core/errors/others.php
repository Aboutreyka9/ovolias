<?php
// Inclure le header

include './inc/header.php';
// var_dump(RACINE_LONG.'inc/header.php');
?>

<!-- 404 Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div id="lottie-animation-rapport" style="width: 200px; height: 200px; margin: 0 auto;"></div>
                <h1 class="mb-4">Vous n'avez aucun rapport a faire aujourd'hui !</h1>
                <p class="mb-4">Si vous ne comprenez pas l'absence du rapport , veuillez contacter le service client.</p>
                <a class="btn btn-primary py-3 px-5" href="/home">Retour à la page d'accueil</a>
            </div>
        </div>
    </div>
</div>

<!-- 404 End -->
<?php

// Inclure le footer si nécessaire
include './inc/footer.php';
?>