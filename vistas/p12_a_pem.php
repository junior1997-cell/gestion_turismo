<?php 
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;

require '../vendor/autoload.php';

$pfx = file_get_contents('../assets/certificado/certificado.p12');
$password = 'Brartnet44685134';

$certificate = new X509Certificate($pfx, $password);
$pem = $certificate->export(X509ContentType::PEM);
    
file_put_contents('../assets/certificado/certificate.pem', $pem);


?>