<?php
//for TWIG templating:
require_once 'vendor/autoload.php';
//for lookups, holds, cancel holds and renewal in WMS
require_once 'NCIP_Staff_Service.php';
require_once 'messages.php';

$debug = FALSE;
//add &debug to the url for getting output from library classes that use API's:
if (array_key_exists('debug',$_GET)) $debug = TRUE;

//classes for Patrons and circulation services
$ncip = new NCIP_Staff_Service('keys_ncip.php');

//if this script is called with an url parameter 'bc_list'
$bc_list = null;
$barcodes = [];
if (array_key_exists('bc_list',$_GET)) {
  $bc_list = $_GET['bc_list'];
  $barcodes_raw = explode(',',$bc_list);
  foreach ($barcodes_raw as $c) {
    if ((strlen($c) > 0) && (!in_array($c,$barcodes))) $barcodes[] = trim($c);
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Circulation - checkin</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/circ.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jsoneditor.min.js"></script>
    <script type="text/javascript" src="schema/checkinSchema.js"></script>
    <link rel="stylesheet" href="./css/circ.css">
  </head>

  <body>
    <a href="index.html">Back to menu</a>
    <div id="editor"></div>
    <div id="list">
      <?php
      echo implode('<br/>',$barcodes);
      ?>
    </div>

    <div id="buttons">
      <button id='submit'>Check In</button>
      <button id='empty'>Empty form</button>
    </div>
    <div id="res">
      <?php
      if (!is_null($bc_list)) {
        foreach ($barcodes as $c) {
          if ($ncip->checkin_barcode($c)) {

            if (array_key_exists("NCIPMessage",$ncip->response_json)) {
              if (array_key_exists("Problem",$ncip->response_json["NCIPMessage"][0])) {
                //some problem
                echo $m['in_not_av'];
                if ($debug) echo $ncip->response_str('html');
                break;
              }
              else if (array_key_exists("CheckInItemResponse",$ncip->response_json["NCIPMessage"][0])) {
                //a real response on check in, but might have a problem
                //response_json["NCIPMessage"][0]["CheckInItemResponse"][0]["Problem"][0]["ProblemType"][0] == "Unknown Item"
                if (array_key_exists("Problem",$ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0])) {
                  if (strpos($ncip->response_json["NCIPMessage"][0]["CheckInItemResponse"][0]["Problem"][0]["ProblemType"][0], "Unknown Item") !== FALSE) {
                    echo $m['item_unknown']; 
                  }
                  else {
                    //other problem
                    echo $m['in_not_av'];
                    if ($debug) echo $ncip->response_str('html');
                    break;
                  }
                }
                else {
                  //a real response on check out and no problem
                  echo $m['in_ok'];
                  if ($debug) echo $ncip->response_str('html');
                }
              }
              else {
                //situation cannot happen?
                echo $m['in_not_av'];
                if ($debug) echo $ncip->response_str('html');
              }
            }
            else {
              //serious error: no response from server
              echo $m['in_not_av'];
              break;
            }
          }
          else {
            //serious error: nothing happened on WMS server
            echo $m['in_not_av'];
            break;
          }
        }
      }
      ?>
    </div>
    <?php
    //show information from library classes
    //use echo $patron; and/or echo $circulation; for even more info
    if ($debug) { ?>
      <div>
        NCIP:
        <pre>
          <?php echo $ncip;?>
        </pre>
      </div>
      <?php } ?>

      <script type="text/javascript" src="js/checkinForm.js"></script>
    </body>

  </html>