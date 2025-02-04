
<?php
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

$see = new See();
$see->setCertificate(file_get_contents('../assets/certificado/certificado_demo.pem'));
$see->setService(SunatEndpoints::FE_BETA);
$see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');

// $see->setCertificate(file_get_contents('../assets/certificado/certificate.pem'));
// $see->setService(SunatEndpoints::FE_PRODUCCION);
// $see->setClaveSOL('20610630431', 'GISELA76', 'Gis76049857');

return $see;