var debug = true;

//JSONEditor defaults
JSONEditor.defaults.theme = 'bootstrap3'; //'barebones';
JSONEditor.defaults.iconlib = 'fontawesome3'; //'';

var editorProperties =
{
  //  show_errors: 'change',  //interaction (default), change, always, never
  //  ajax:true,
  schema: schemaObj,
  //remove_empty_properties:true,
  required_by_default: true,
  keep_oneof_values: false,
  no_additional_properties: true,
  disable_array_reorder: true,
  disable_edit_json: true,
  disable_properties: true,
  disable_collapse: true
};

// Initialize the editor
var query = document.location.search;

editorProperties.startval ={};
if (query.includes('user_barcode=')) {
  editorProperties.startval.user_barcode = user_barcode;
}

var editor = new JSONEditor(document.getElementById('editor'),editorProperties);

editor.on('ready',function() {

  bc_list = new Array();
  $("[name = 'root[item_barcode]']").focus();
  $('#list').html("");

  editor.watch('root.item_barcode', function() {
    val = editor.getEditor('root.item_barcode').getValue();
    val = val.trim();
    if (val.length > 0) {
      $('#list').append(val + '<br/>');
      bc_list.push(val);
    }
    editor.setValue({user_barcode: user_barcode, item_barcode: ""});
    $("[name = 'root[item_barcode]']").focus();
  });


  // Hook up the submit button to log to the console
  $('#submit').on('click',function() {
    $('#res').html("");
	  user_barcode = editor.getEditor('root.user_barcode').getValue();
	  //alert('|'+user_barcode + '|--|' + bc_list+ '|');
    if (user_barcode.length == 0) {
      msg = "Please scan the barcode of a valid library card.";
      $('#res').html(msg);
    }
    else {
      //empty feedback div

      //Validate
      var errors = editor.validate();

      if(errors.length) {
        //collect and show error messages
        if (debug) console.log(errors);
        msg = '<p>Your request has NOT been sent. Correct the following fields.</p>';
        errors.forEach(function(err) {
          msg += '<p>' + editor.getEditor(err.path).schema.title + ': ' + err.message + '</p>';
        });
        $('#res').html(msg);
      }
      else {
        var barcodeURL = document.location.origin + document.location.pathname+'?user_barcode='+user_barcode+'&bc_list='+bc_list;
        window.location.assign(barcodeURL);
      }
    }
  });

  // Hook up the Empty button
  $('#empty').on('click',function() {
    var emptyURL = document.location.origin + document.location.pathname;
    window.location.assign(emptyURL);
  });

});
