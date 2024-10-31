/**
 * Create Poll Javascript File
 *
 * Description: Main Javascript File for Page "Create Poll" in Admin Menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/js
 *
 */

/**
 * Document Ready Set Height
 * Description: If document is ready, set height of iframe to container height
 *              of Wordpress.
 */
jQuery(document).ready(function() {
  var headerHeight = jQuery('.pp-header-line').outerHeight(true);
  var wpadminbarHeight = jQuery('#wpadminbar').outerHeight(true);
  var wpwrapHeight = jQuery('#wpwrap').height();
  var wpcontentpadding = jQuery('#wpbody-content').innerHeight() - jQuery('#wpbody-content').height();
  var wpfooterHeight = jQuery('#wpfooter').outerHeight(true);
  var containerHeight = wpwrapHeight - headerHeight - wpfooterHeight - wpadminbarHeight - wpcontentpadding;
  jQuery('#pp-create-embed').css('height', containerHeight);
});

/**
 * EventListner Submit
 * Description: If create form will be submitted, than redirect to poll details
 *              page.
 */
window.addEventListener('message', function(evt) {
  try {
    var message = (typeof evt.data === 'string' || evt.data instanceof String) ? JSON.parse(evt.data) : evt.data;
    if(message.action == 'embed-edit:submitted') {
      window.location.href = 'admin.php?page=allpolls&action=details&poll=' + message.data.id + '&wpnonceDetail=' + ppNonce;
    }
  } catch(e) {}
});
