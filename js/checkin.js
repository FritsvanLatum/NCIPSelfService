var debug = true;

jQuery(document).ready(function() {

  // Variable to keep the barcode when scanned. When we scan each
  // character is a keypress and hence we push it onto the array. Later we check
  // the length and final char to ensure it is a carriage return - ascii code 13
  // this will tell us if it is a scan or just someone writing on the keyboard
  var chars = [];
  var checkin_state = 'first_item';
  jQuery("#dialog").append(message('item',[]));
  
  //hide 'Done' button
  jQuery('#done').css( "display", "none" );
  jQuery('#wait').css( "visibility", "hidden" );
  
  //checks in the item and returns item information after scanning the item
  //uses NCIP library and API via ajax call
  //async: false is essential!
  function item(barcode) {
    jQuery('#wait').css( "visibility", "visible" );
    request = jQuery.ajax({
      url: 'php/aj_checkin_one.php',
      data: {item_barcode: barcode},
      async: false
    });

    request.done( function(data, textStatus, jqXHR) {
      if (debug) {console.log("Data: "+data+' - textStatus: ' + textStatus)}
      jQuery("#results").append(message('item_code',[barcode, data]));
      checkin_state = 'next_item';
      jQuery("#dialog").append(message('item',[]));
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      // Log the error to the console
      if (debug) {console.log(textStatus, errorThrown)}
      checkin_state = 'done';
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
        checkin_state = 'done';
      }
      // debug barcode to console (e.g. for use in Firebug)
      if (debug) {console.log("State: " + checkin_state)}

      switch (checkin_state) {
        case 'first_item':
        //page is in checkin_state 'first_item', meaning the first item must be scanned
        item(barcode);
        //now the checkin_state is either 'next_item' or 'done'
        
        //from now on the user is able to end the transaction by clicking the button 'Done'
        jQuery('#done').css( "display", "block" );
        break;

        case 'next_item':
        //hide button 'Done' while processing
        jQuery('#done').css( "display", "none" );
        item(barcode);
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
      if (debug) {console.log("Barcode scanned: " + barcode + ", New state: " + checkin_state)}

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
