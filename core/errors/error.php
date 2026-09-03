
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Stack admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, stack admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Dashboard eCommerce - Stack Responsive Bootstrap 4 Admin Template</title>
    <link rel="apple-touch-icon" href="<?=RACINE?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?=RACINE?>app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/vendors/css/extensions/unslider.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/vendors/css/weather-icons/climacons.min.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/fonts/meteocons/style.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/vendors/css/charts/morris.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/vendors/css/tables/datatable/datatables.min.css">

    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/components.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/pages/timeline.css">
    <!-- END: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/pages/page-users.css">

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>assets/css/style.css">
    <!-- END: Custom CSS -->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/components.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/plugins/forms/wizard.css">
        <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css-rtl/plugins/file-uploaders/dropzone.css">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <!-- <link rel="stylesheet" type="text/css" href="<?=RACINE?>app-assets/css/plugins/pickers/daterange/daterange.css"> -->
    <!-- END: Page CSS-->
    <style>
        body {
            background-color: #f8d7da; /* Fond rouge pâle */
            color: #721c24;
            font-family: 'Lato', sans-serif;
        }
        .payment-error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .error-icon {
            font-size: 5rem;
            color: #721c24;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 20px;
            color: #721c24;
        }
        .error-message {
            font-size: 1.2rem;
            margin-top: 10px;
            margin-bottom: 30px;
            color: #333;
        }
        .btn-try-again {
            background-color: #fbb13c;
            border-color: #e08a00;
            color: #fff;
            padding: 10px 20px;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-try-again:hover {
            background-color: #e08a00;
            border-color: #c97a00;
        }
        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #aaa;
        }
        footer a {
            color: #fbb13c;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="payment-error-container">
        <div class="error-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1 class="error-title">Paiement Échoué</h1>
        <p class="error-message">Votre paiement n'a pas pu être traité. Veuillez vérifier vos informations de paiement ou réessayer.</p>
        <a class="btn btn-try-again" href="<?=RACINE?>heberge/list">Réessayer</a>
        <p class="mt-3">Si le problème persiste, veuillez contacter notre service client.</p>
    </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> beta
    </div>
    <strong>Copyright &copy; 2025 <a href="">E-loyer</a>.</strong> All rights
    reserved.
  </footer>
  
</body>

</html>