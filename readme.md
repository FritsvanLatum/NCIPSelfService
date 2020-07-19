# Peace Palace Library Self Service

This repository contains an application for self service loans and returns by members of the library.

## Barcode reader

The production version works with barcodes that are read by a barcode reader that adds a Carriage Return character (13) as a suffix to each barcode read.
The checkout and the checkin page have a dialog part `<div id="dialog">` in which the consecutive questions are shown and a results part `<div id="results">` in which names, authors and titles are shown or error messages. 

When processing "Please wait" is shown.

The "Done" button is shown after the first item barcode is scanned and processed. A click on "Done" shows the index page again.

The essential files of the production version are:
* `index.html`
* `checkout.html`
* `js/checkout.js`
* `php/aj_patron_info.php`
* `php/aj_checkout_one.php`
* `php/ncip_templates/checkout_request_template.xml`
* `checkin.html`
* `js/checkin.js`
* `php/aj_checkin_one.php`
* `php/ncip_templates/checkin_request_template.xml`
* `js/messages.js`

jQuery and libraries that interface with the API's are also provided in the repository.

#### Test version
The application provides a test interface in index_test.php. This version uses HTML forms and gives feedback for debugging.

[https://github.com/json-editor/json-editor](https://github.com/json-editor/json-editor) is used  for defining and handling forms.

The essential files of the test version are:
* `index.html`
* `checkout_test.php`
* `js/checkoutForm.js`
* `schema/checkout_oneSchema.js`
* `checkin_test.php`
* `js/checkinForm.js`
* `schema/checkin_oneSchema.js`


## Dependencies
* This application must be installed in a [XAMP](https://www.apachefriends.org/index.html) (or LAMP, WAMP) environment.
* The applications uses the [NCIP API](https://www.oclc.org/developer/develop/web-services/wms-ncip-service.en.html) and the [IDM API](https://www.oclc.org/developer/develop/web-services/worldshare-identity-management-api.en.html) of OCLC's [WMS](https://www.oclc.org/nl/worldshare-management-services.html).
* In order to authenticate key files must be provided
