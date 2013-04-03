<?php

include 'tools.php';
include 'soap.php';

// Define Soap API urls
$url = array(
    1 => 'http://www.example.com/api/?wsdl',
    2 => 'http://www.example.com/api/v2_soap?wsdl'
);

// The version for this call
$version = 2;

$soap = new MultiVersionSoapClient($url[$version], "username", "key/password", $version);

// If login/url retrieve failed
if (!$soap) exit;

// Make the call
$result = $soap->call(
    "catalog_product.info",
    "SKU-123",
    0,
    array(
        "attributes" => array("description", "price"),
        "additional_attributes" => array("package_id", "enable_google_checkout")
    )
);

note("The following was returned:\n");
// Dump results without time
note($result, false);
