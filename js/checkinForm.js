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

/*
if (query.includes('item_barcode=')) {
  editorProperties.startval = {item_barcode:item_barcode};
}
*/

var editor = new JSONEditor(document.getElementById('editor'),editorProperties);


editor.on('ready',function() {
  bc_list = new Array();
  $("[name = 'root[item_barcode]']").focus();

  editor.watch('root.item_barcode', function() {
    // Do something
    val = editor.getEditor('root.item_barcode').getValue();
    if (val.length > 0) {
      $('#list').append(val + '<br/>');
      bc_list.push(val);
    }
    editor.setValue({item_barcode: ""});
    $("[name = 'root[item_barcode]']").focus();
  });


  // Hook up the submit button to log to the console
  $('#submit').on('click',function() {
    
    item_barcode = editor.getEditor('root.item_barcode').getValue();
    if (bc_list.length > 0)  {
      //empty feedback div
      $('#res').html("");

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
        var barcodeURL = document.location.origin + document.location.pathname+'?bc_list='+bc_list;
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
