/**
 * Poll Table Javascript File
 *
 * Description: Main Javascript File for Page "Polls" in Admin Menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/js
 *
 */

//Call method to init category labels
pinpoll_change_select_label();

//Call method to diasble "Apply" button
pinpoll_disable_apply();

/**
 * Change Categories
 * Description: Change label of selectbox "Category" to the current
 *              category on page load
 */
function pinpoll_change_select_label() {
  var elemens = document.getElementsByName('pp-selected');
  for (var i = 0; i < elemens.length; i++) {
    elemens[i].value = category;
  }
}

//readonly for page selector
jQuery(document).ready(function() {
  var pageSelector = document.getElementById("current-page-selector");
  pageSelector.setAttribute("readonly", "true");
  var selectedCategory = document.getElementById('pp-select-category');
  document.getElementById('pp-hidden-selected').value = selectedCategory === null ? '' : selectedCategory.value;
});

/**
 * Confrim Delete
 * Description: Confirm message for delete a poll
 * @param  {html}     e         element
 * @return {confirm}  confirm   message
 */
function confirmDelete(e) {
  return confirm(ppPollsTexts.deleteMessage);
}

/**
 * Listener: Switchbuttons in Table
 * Description: Call api if poll gets activated or deactivated
 */
jQuery(document).on('change', 'input[type=checkbox][name=checkboxPPActive]', function(e) {

  //get pollid of selected poll
  var pollId = jQuery(this).val();
  var bodydata = {};
  var form = document.getElementById('pp-table-form');

  //proof if switchbutton is checked
  if(this.checked) {
    bodyData = { 'active' : '0' };
    console.info(pollId + " checked");

    jQuery.ajax({
      url: baseURL + '/polls/' + pollId + '/activate',
      type: 'POST',
      headers: {
        'Authorization' : jwt,
      },
      data : bodyData,
      dataType : 'json',
      success: function(response) {
        console.info('Poll activated');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert(ppPollsTexts.alertMessageActivate);
        form.submit();
      }
    });

  } else {
    console.info(pollId + " unchecked");
    bodyData = { 'active' : '1' };

    jQuery.ajax({
      url: baseURL + '/polls/' + pollId + '/activate',
      type: 'POST',
      headers: {
        'Authorization' : jwt,
      },
      data : bodyData,
      dataType : 'json',
      success: function(response) {
        console.info('Poll deactivated');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert(ppPollsTexts.alertMessageDeactivate);
        form.submit();
      }
    });
  }
});

/**
 * Init Hidden Field
 * Description: Init hidden field with selected category to get the current
 *              category on page load.
 * @param  {html} elem selectedbox
 */
function pinpoll_select_value_change(elem) {
  var elemens = document.getElementsByName('pp-selected');
  for (var i = 0; i < elemens.length; i++) {
    elemens[i].value = elem.value;
  }
  document.getElementById('pp-hidden-selected').value = elem.value;
  var form = document.getElementById('pp-table-form');
  form.submit();
}

/**
 * Disable Bulk Action Button
 * Description: Disable button "Apply" by default
 */
function pinpoll_disable_apply() {
  jQuery('#doaction').attr('disabled', 'true');
  jQuery('#doaction2').attr('disabled', 'true');
}

/**
 * Enable Bulk Action Button
 * Description: Enables button "Apply" if user selected a poll via checkbox
 */
jQuery(document).on('change', 'input[type=checkbox][class=pp-polls-cb]' ,function() {
  var disable = true;
  var boxes = document.getElementsByClassName('pp-polls-cb');

  for (var i = 0; i < boxes.length; i++) {
    if(boxes[i].checked) {
      disable = false;
    }
  }

  if(!disable) {
    jQuery('#doaction').removeAttr('disabled');
    jQuery('#doaction2').removeAttr('disabled');
  } else {
    if(!document.getElementById('doaction').hasAttribute('disabled')) {
      jQuery('#doaction').attr('disabled', 'true');
    }
    if(!document.getElementById('doaction2').hasAttribute('disabled')) {
      jQuery('#doaction2').attr('disabled', 'true');
    }
  }
});
