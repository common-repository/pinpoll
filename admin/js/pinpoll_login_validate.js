/**
 * Login Field Validation Javascript File
 *
 * Description: Main Javascript File for Custom Field Validation
 *              for page "Login" and "Switch Account" in admin menu.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/js
 *
 */

/**
 * Field Validation
 * Description: Custom field validation for fields email and password.
 */
document.addEventListener("DOMContentLoaded", function() {
  var email = document.getElementById("email");
  var pw = document.getElementById("password");

  if(email !== null && pw !== null) {
    email.oninvalid = function(e) {
        e.target.setCustomValidity("");
        if (!e.target.validity.valid) {
            e.target.setCustomValidity(ppTrans.emailMessage);
        }
    };
    email.oninput = function(e) {
        e.target.setCustomValidity("");
    };

    pw.oninvalid = function(e) {
        e.target.setCustomValidity("");
        if (!e.target.validity.valid) {
            e.target.setCustomValidity(ppTrans.passwordMessage);
        }
    };
    pw.oninput = function(e) {
        e.target.setCustomValidity("");
    };
  }
});

/**
 * Pass Email to Input Field
 * Description: Set value of input field "#pp-email", if user has
 *              invalid credentials.
 */
jQuery(document).ready(function() {
  try {
    if(email) {
      jQuery('#pp-email').val(email);
    }
  } catch(e) {

  }
});
