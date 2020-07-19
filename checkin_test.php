<?php
/*  checkin_test.php
/ webpage with an input elements for item barcode
/ for TESTing checkins by library patrons
/ shows response from the NCIP API on WMS
/
/ in PRODUCTION use checkin.html (has no input fields)

/ script uses https://github.com/json-editor/json-editor
/ for defining and handling forms
*/

//for lookups, holds, cancel holds and renewal in WMS
require_once 'php/NCIP_Staff_Service.php';

$debug = TRUE;
//add &debug to the url for getting more output from library classes that use API's:
if (array_key_exists('debug',$_GET)) $debug = TRUE;

//classes for circulation services
$ncip = new NCIP_Staff_Service('keys_ncip.php');

//after the patron hits the checkin button,
//this script reloads with an url parameter 'item_barcode'
$item_barcode = null;
if (array_key_exists('item_barcode',$_GET)) {
  $item_barcode = trim($_GET['item_barcode']);
}
?>



<!DOCTYPE html>
<html>
  <head>
    <title>TEST - Circulation - checkin one</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/circ.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jsoneditor.min.js"></script>
    <script type="text/javascript" src="schema/checkin_oneSchema.js"></script>
    <script>
      <?php
      //write little javascript for definition of variable used in js/checkin_oneForm.js (form handling)
      if ($item_barcode) echo "item_barcode = '$item_barcode';";
      ?>
    </script>
  </head>


  <body>
    <a href="index_test.html">Back to TEST menu</a>
    <div id="editor"></div>
    <div id="buttons">
      <button id='submit'>Check In</button>
      <button id='empty'>Empty form</button>
    </div>
    <div id="res">
      <?php
      if (array_key_exists('item_barcode',$_GET)) {
        //check in one
        $ncip->checkin_barcode($item_barcode);
        echo $ncip->response_str('html');
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

      <script type="text/javascript" src="js/checkin_oneForm.js"></script>
    </body>
  </html>