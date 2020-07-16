m = {
  lib_card:        'Please scan library card. 090037604',
  lib_card_code :  'Barcode: {{0}}, Name: {{1}}',
  lib_card_fail:   "Checkout not permitted on card number '{{0}}', please check out at the desk.",
  item:            'Please scan an item.',
  item_code:       'Barcode: {{0}}, Item: {{1}}',
  item_fail:       "Checkout not permitted on item number '{{0}}', please check out at the desk.",

};

function message(key, f) {
  //replace {{i}} by f[i]
  var i;
  var s = m[key];
  for (i = 0; i < f.length; i++) {
    s = s.replaceAll('{{'+i+'}}', f[i]);
  }
  
  //return the message inside a p tag
  return '<p class="msg">' + s + '</p>';
}