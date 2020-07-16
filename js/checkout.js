var debug = true;

jQuery(document).ready(function() {

  // Variable to keep the barcode when scanned. When we scan each
  // character is a keypress and hence we push it onto the array. Later we check
  // the length and final char to ensure it is a carriage return - ascii code 13
  // this will tell us if it is a scan or just someone writing on the keyboard
  var chars = [];
  var checkout_state = 'patron';
  var patron_barcode = '';
  jQuery("#dialog").append(message('lib_card',[]));
  jQuery('#wait').css( "visibility", "hidden" );
  
  //hide 'Done' button
  jQuery('#done').css( "display", "none" );
  
  //gets patron information after scanning the member card
  //uses IDM library and API via ajax call
  //async: false is essential!
  function patron(barcode) {
    jQuery('#wait').css( "visibility", "visible" );
    request = jQuery.ajax({
      url: 'php/aj_patron_info.php',
      data: {patron_barcode: barcode},
      async: false
    });

    request.done( function(data, textStatus, jqXHR) {
      if (debug) {console.log("Data: "+data+' - textStatus: ' + textStatus)}
      jQuery("#results").append(message('lib_card_code',[barcode, data]));
      checkout_state = 'first_item';
      jQuery("#dialog").append(message('item',[]));
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      // Log the error to the console
      if (debug) {console.log(textStatus, errorThrown)}
      checkout_state = 'done';
      jQuery('#results').append(message('lib_card_fail',[barcode]));
    });
    jQuery('#wait').css( "visibility", "hidden" );
  }

  //checks out the item and returns item information after scanning the item
  //uses NCIP library and API via ajax call
  //async: false is essential!
  function item(patron_barcode, barcode) {
    jQuery('#wait').css( "visibility", "visible" );
    request = jQuery.ajax({
      url: 'php/aj_checkout_one.php',
      data: {patron_barcode: patron_barcode, item_barcode: barcode},
      async: false
    });

    request.done( function(data, textStatus, jqXHR) {
      if (debug) {console.log("Data: "+data+' - textStatus: ' + textStatus)}
      jQuery("#results").append(message('item_code',[barcode, data]));
      checkout_state = 'next_item';
      jQuery("#dialog").append(message('item',[]));
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      // Log the error to the console
      if (debug) {console.log(textStatus, errorThrown)}
      checkout_state = 'done';
      jQuery('#results').append(message('item_fail',[barcode]));
    });
    jQuery('#wait').css( "visibility", "hidden" );
  }

  // trigger an event on any keypress on this webpage
  jQuery(window).keypress(function(e) {
    // check the keys pressed are numbers
    if (e.which >= 48 && e.which <= 57) {
      // if a number is pressed we add it to the chars array
      chars.push(String.fromCharCode(e.which));
    }
    // debug to help you understand how scanner works
    if (debug) {console.log(e.which + ":" + chars.length + ":" + chars.join("|"))}

    if (e.which == 13) {
      // join the chars array to make a string of the barcode scanned
      barcode = chars.join("");
      if (barcode.length == 0) {
        //just hitting Enter stops this page and returns to the index page
        checkout_state = 'done';
      }
      // debug barcode to console (e.g. for use in Firebug)
      if (debug) {console.log("State: " + checkout_state)}

      switch (checkout_state) {
        case 'patron':
        //page starts in checkout_state 'patron', meaning the patron barcode must be scanned
        patron_barcode = barcode;
        patron(barcode);
        //now the checkout_state is either 'first_item' or 'done'
        break;

        case 'first_item':
        //page is in checkout_state 'first_item', meaning the first item must be scanned
        item(patron_barcode, barcode);
        //now the checkout_state is either 'next_item' or 'done'
        
        //from now on the user is able to end the transaction by clicking the button 'Done'
        jQuery('#done').css( "display", "block" );
        break;

        case 'next_item':
        //hide button 'Done' while processing
        jQuery('#done').css( "display", "none" );
        item(patron_barcode, barcode);
        //show button 'Done'
        jQuery('#done').css( "display", "block" );
        break;

        case 'done':
        //show the index page
        paths = document.location.pathname.split('/');
        paths.pop();
        newPath = '/' + paths.join('/');
        window.location.assign(document.location.origin + newPath);
        break;
      }
      if (debug) {console.log("Barcode scanned: " + barcode + ", New state: " + checkout_state)}

      //empty chars for the next barcode scan
      chars = [];
    }
  }); //end of keypress

  jQuery('#done').on('click',function() {
    paths = document.location.pathname.split('/');
    paths.pop();
    newPath = '/' + paths.join('/');
    window.location.assign(document.location.origin + newPath);
  });

}); //end of ready
