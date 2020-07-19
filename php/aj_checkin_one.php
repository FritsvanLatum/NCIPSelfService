<?php
/* aj_checkin_one.php
/ script is used in ../js/checkin.js as a jQuery ajax call
/ given a item_barcode it collects the the author and title
/ uses the NCIP API
/ 
/ returns author and title, or one of the two or an empty string when the checkin is succesful
/ or an internal service error in all other cases 
*/

//for lookups, holds, cancel holds and renewal in WMS
require_once 'NCIP_Staff_Service.php';

$debug = TRUE;
//add &debug to the url for getting output from library classes that use API's:
if (array_key_exists('debug',$_GET)) $debug = TRUE;

if (array_key_exists('item_barcode',$_GET)) {
  //classes for Patrons and circulation services
  $ncip = new NCIP_Staff_Service('keys_ncip.php');
  if ($ncip->checkin_barcode($_GET['item_barcode'])) {
    if (array_key_exists("NCIPMessage",$ncip->response_json)) {
      if (array_key_exists("CheckInItemResponse",$ncip->response_json["NCIPMessage"][0])) {
        if (array_key_exists("RoutingInformation",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0])) {
          $m = array();
          if (
          array_key_exists("ItemOptionalFields",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]) &&
          array_key_exists("BibliographicDescription",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]['ItemOptionalFields'][0])
          ) {

            if (array_key_exists("Author",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]['ItemOptionalFields'][0]['BibliographicDescription'][0]))
            $m[] = $ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]['ItemOptionalFields'][0]['BibliographicDescription'][0]['Author'][0];
            if (array_key_exists("Title",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]['ItemOptionalFields'][0]['BibliographicDescription'][0]))
            $m[] = $ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]['ItemOptionalFields'][0]['BibliographicDescription'][0]['Title'][0];

          }
          echo join(' ',$m);
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
