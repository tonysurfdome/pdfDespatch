<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'pdf_despatch.php';

$json = "";

if (isset($_REQUEST['shipment_number']) && !(empty($_REQUEST['shipment_number'])))
{
	$pdfDespatch = new PdfDespatch();
	$pdfDespatch->despatchNumber($_REQUEST['shipment_number']);
	$json = $pdfDespatch->run();

}

echo $json;
?>