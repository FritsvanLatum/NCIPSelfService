<?php
require_once 'vendor/autoload.php';
require_once 'NCIP_Staff_Service.php';
require_once 'messages.php';

$debug = TRUE;
if (array_key_exists('debug',$_GET)) $debug = TRUE;

$ncip_template_file = './templates/ncip_template.html';

$ncip = new NCIP_Staff_Service('keys_ncip.php');

$user_barcode = null;
$bc_list = null;
$barcodes = [];
if (array_key_exists('user_barcode',$_GET) && array_key_exists('bc_list',$_GET)) {
  //get user_barcode
  $user_barcode = trim($_GET['user_barcode']);
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
    <title>Circulation - checkout</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/circ.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jsoneditor.min.js"></script>
    <script type="text/javascript" src="schema/checkoutSchema.js"></script>
    <script>
      <?php if ($user_barcode) echo "user_barcode = '$user_barcode';" ?>
    </script>
  </head>

  <body>
    <a href="index.html">Back to menu</a>
    <div id="editor"></div>
    <div id="list"></div>

    <div id="buttons">
      <button id='submit'>Check out</button>
      <button id='empty'>Empty form</button>
    </div>
    <div id="res">
      <?php
      if ($user_barcode == null) {
        if ($bc_list != null) echo("No user barcode given. Please scan a valid library card.<br/>");
      }
      else if ($bc_list == null) {
        echo("No items scanned.<br/>");
      }
      else {
        //checkout
        foreach ($barcodes as $c) {
          if ($ncip->checkout_barcode($user_barcode, $c)) {
            if (array_key_exists("NCIPMessage",$ncip->response_json)) {
              if (array_key_exists("Problem",$ncip->response_json["NCIPMessage"][0])) {
                //response_json["NCIPMessage"][0]["Problem"][0]["ProblemDetail"][0] = "java.lang.IllegalArgumentException: Unknown user barcode,123"
                if (strpos($ncip->response_json["NCIPMessage"][0]["Problem"][0]["ProblemDetail"][0], "Unknown user barcode") !== FALSE) {
                  echo $m['user_unknown'];
                }
                else {
                  //other problem
                  echo $m['out_not_av'];
                  if ($debug) echo $ncip->response_str('html');
                }
                break;
              }
              else if (array_key_exists("CheckOutItemResponse",$ncip->response_json["NCIPMessage"][0])) {
                //a real response on check out, but might have a problem
                //response_json["NCIPMessage"][0]["CheckOutItemResponse"][0]["Problem"][0]["ProblemType"][0] == "Unknown Item"
                if (array_key_exists("Problem",$ncip->response_json["NCIPMessage"][0]["CheckOutItemResponse"][0])) {
                  if (strpos($ncip->response_json["NCIPMessage"][0]["CheckOutItemResponse"][0]["Problem"][0]["ProblemType"][0], "Unknown Item") !== FALSE) {
                    echo $m['item_unknown']; 
                  }
                  else {
                    //other problem
                    echo $m['out_not_av'];
                    if ($debug) echo $ncip->response_str('html');
                    break;
                  }
                }
                else {
                  //a real response on check out and no problem
                  echo $m['out_ok'];
                  if ($debug) echo $ncip->response_str('html');
                }
              }
              else {
                //situation cannot happen?
                echo $m['out_not_av'];
                if ($debug) echo $ncip->response_str('html');
              }
            }
            else {
              //serious error: no response from server
              echo $m['out_not_av'];
            }
          }
          else {
            //serious error: nothing happened on WMS server
            echo $m['out_not_av'];
            break;
          }
        } //end for
      }
    
    ?>
  </div>
  <?php if ($debug) { ?>
  <div>
    Patron:
    NCIP:
    <pre>
      <?php if ($user_barcode) echo $ncip;?>
    </pre>
  </div>
  <?php } ?>

  <script type="text/javascript" src="js/checkoutForm.js"></script>
</body>

</html>