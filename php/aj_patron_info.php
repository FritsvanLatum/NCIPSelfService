<?php

/* aj_patron_info.php
/ script is used in ../js/checkout.js as a jQuery ajax call
/ given a patron_barcode it collects the name of the patron 
/ uses the IDM API
/ 
/ returns the name of the patron when it finds one
/ or an internal service error in all other cases 
*/

require_once 'IDM_Service.php';

$debug = TRUE;
//add &debug to the url for getting output from library classes that use API's:
if (array_key_exists('debug',$_GET)) $debug = TRUE;

if (array_key_exists('patron_barcode',$_GET)) {
  //classes for Patrons and circulation services
  $patron = new IDM_Service('keys_idm.php');

  if ($patron->read_patron_barcode($_GET['patron_barcode'])) {
    if ($patron->patron && (array_key_exists('name',$patron->patron))) {
      $name = array();
      if (array_key_exists('givenName',$patron->patron["name"])) $name[] = $patron->patron["name"]["givenName"];
      if (array_key_exists('familyName',$patron->patron["name"])) $name[] = $patron->patron["name"]["familyName"];
      echo join(' ', $name);
    }
    else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
    }
  }
  else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
  }
}
else {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
}
?>
