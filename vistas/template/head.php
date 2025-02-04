
<?php 
  $scheme_host  =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/brartnet/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-4TXTGYFYT8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-4TXTGYFYT8');
</script>

<!-- Meta Data -->
<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title_page; ?> | Facturación Brartnet</title>
<meta name="Description" content="Sistema de Facturacion Electronica">
<meta name="Author" content="JDL TECNOLOGY SAC">
<meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

<meta name="msapplication-navbutton-color" content="#444">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<link rel="manifest" href="<?php echo $scheme_host;?>assets/images/app-download/manifest.json?v=<?php echo date('ymd'); ?>">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="../assets/images/brand-logos/toggle-dark">
<meta name="theme-color" content="#ffffff">

<!-- Favicon -->
<link rel="icon" href="../assets/images/brand-logos/ico-brartnet.svg" type="image/x-icon">

<!-- Font Awesome 6.2 -->
<link rel="stylesheet" href="../assets/libs/fontawesome-free-6.2.0/css/all.min.css" />

<!-- Choices JS -->
<script src="../assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Main Theme Js -->
<script src="../assets/js/main.js"></script>

<!-- Bootstrap Css -->
<link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Style Css -->
<link href="../assets/css/styles.min.css" rel="stylesheet">
<!-- <link href="../assets/css/styles.css" rel="stylesheet"> -->

<!-- Icons Css -->
<link href="../assets/css/icons.css" rel="stylesheet">

<!-- Node Waves Css -->
<link href="../assets/libs/node-waves/waves.min.css" rel="stylesheet">

<!-- Simplebar Css -->
<link href="../assets/libs/simplebar/simplebar.min.css" rel="stylesheet">

<!-- Color Picker Css -->
<link rel="stylesheet" href="../assets/libs/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css">

<!-- Choices Css -->
<link rel="stylesheet" href="../assets/libs/choices.js/public/assets/styles/choices.min.css">

<!-- :::::::::::::::::: P L U G I N   D I F E R E N T E S   A L   P R O Y E C T O :::::::::::::::::: -->

<!-- Data Table -->
<link href="../assets/libs/data-table/datatables.css" rel="stylesheet">

<!-- Select2 -->
<link rel="stylesheet" href="../assets/libs/select2/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- Toastr -->
<link rel="stylesheet" href="../assets/libs/toastr/toastr.min.css">

<!-- Mi stylo -->
<link href="../assets/css/style_new.css" rel="stylesheet">