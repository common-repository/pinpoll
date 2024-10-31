<?php
/**
 * Resource "Texts" from Pinpoll
 *
 * Description: All texts which are use in the whole plugin.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/resources
 *
 */

/**
 * Dashboard Texts
 * Description: Texts for page "Dashboard"
 *
 * @return array texts
 */
function pinpoll_get_dashboard_texts() {
  return array(
    'title' => __( 'Dashboard', 'pinpoll' ),
    'review' => __( 'Great! Want to help us by writing a <strong>WordPress Review</strong>?', 'pinpoll' ),
    'createpoll' => __( 'Create Poll', 'pinpoll' ),
    'allvotes' => __( 'All Votes', 'pinpoll' ),
    'votestotal' => __( 'Votes total', 'pinpoll' ),
    '24hrs' => __( '24 Hours', 'pinpoll' ),
    '30days' => __( '30 Days', 'pinpoll' ),
    'votescountry' => __( 'Votes per Country', 'pinpoll' ),
    'votescity' => __( 'Votes per City', 'pinpoll' ),
    'tableno' => __( '#', 'pinpoll' ),
    'country' => __( 'Country', 'pinpoll' ),
    'city' => __( 'City', 'pinpoll' ),
    'votes' => __( 'Votes', 'pinpoll' ),
    'nodata' => __( 'No data available.', 'pinpoll' ),
    'feedbackenjoy' => __( 'Do you enjoy Pinpoll?', 'pinpoll' ),
    'feedbackthanktitle' => __( 'Thank you!', 'pinpoll' ),
    'feedbackimprove' => __( 'How can we improve?', 'pinpoll' ),
    'feedbackimprovemessagea' => __( 'Your WordPress review will help us big times.', 'pinpoll' ),
    'feedbackimprovemessageb' => __( 'Click on "Write a review" to post your feedback - thanks so much! ', 'pinpoll' ),
    'feedbackreview' => __( 'Write a review', 'pinpoll' ),
    'feedbackmessagetextbox' => __( 'Your feedback would help us big times! Please use the form below to let us know what we need to improve - thanks so much.', 'pinpoll' ),
    'feedbackplaceholderhonest' => __( 'Be honest...', 'pinpoll' ),
    'feedbackquit' => __( 'Not now, thanks.', 'pinpoll' ),
    'feedbacksend' => __( 'Send feedback', 'pinpoll' ),
    'feedbackhint' => __( 'Any hints?', 'pinpoll' ),
    'feedbackhintmessage' => __( 'Your feedback would help us a lot! Please use the form below to send us your thoughts - thanks so much.', 'pinpoll' ),
    'feedbackwish' => __( 'Make a wish...', 'pinpoll' ),
    'top' => __( 'Top 5:', 'pinpoll' ),
    'topnodots' => __( 'Top 5', 'pinpoll' ),
    'nodatacollect' => __( 'No data available. To create new polls for more votes ', 'pinpoll' ),
    'nodatacollectlink' => __( 'click here.', 'pinpoll' ),
    'error' => __( 'Our server could not complete the request. Please refresh page!', 'pinpoll' ),
    'wperror' => __( 'Our server could not complete the request. Please refresh page (WP)!', 'pinpoll' )
  );
}

/**
 * Accountstatus Texts
 * Description: Texts for page "Settings"
 *
 * @return array texts
 */
function pinpoll_get_accountstatus_texts() {
  return array(
    'title' => __( 'Link your Pinpoll Account', 'pinpoll' ),
    'titlesettings' => __( 'Account Settings', 'pinpoll' ),
    'userexist' => __( 'Looks like your account already exists. Simply link your account now:', 'pinpoll' ),
    'userexistsetpw' => __( 'Looks like your account already exists. Simply link your account now:', 'pinpoll' ),
    'createpoll' => __( 'Create Poll', 'pinpoll' ),
    'email' => __( 'E-Mail:', 'pinpoll' ),
    'emailhint' => __( 'Provide your e-mail used with Pinpoll.', 'pinpoll' ),
    'pw' => __( 'Password:', 'pinpoll' ),
    'pwhint' => __( 'Provide your password used with Pinpoll.', 'pinpoll' ),
    'btnlink' => __( 'Link Account', 'pinpoll' ),
    'linkset' => __( 'Set Password', 'pinpoll' ),
    'forgotpw' => __( 'Forgot Password?', 'pinpoll' ),
    'successlogin' => __( 'Congratulations, your Pinpoll account is now linked with WordPress.', 'pinpoll' ),
    'successswitch' => __( 'Successfully switched Pinpoll account.', 'pinpoll' ),
    'accountlink' => __( 'The following e-mail address is linked with Pinpoll:', 'pinpoll' ),
    'changemail' => __( 'Change E-Mail Address', 'pinpoll' ),
    'accountinfo' => __( 'Show Account Settings', 'pinpoll' ),
    'error' => __( 'Our server could not complete the request. Please refresh page. If problems persist, please contact: ', 'pinpoll' ),
    'errorfatal' => __( 'Our server could not complete the request. Please refresh page. If problems persist, please contact: ', 'pinpoll' ),
    'errorsession' => __( 'Your session has expired. Please log in again!', 'pinpoll' ),
    'invalidcred' => __( 'Sorry, invalid credentials provided. Please try again!', 'pinpoll' ),
    'inforefresh' => __( 'Your session has expired, but we refreshed it. Keep on polling!', 'pinpoll' ),
    'infosetpw' => __( 'You should have received an e-mail with a link to set your password, please check.', 'pinpoll' ),
    'errorsetpw' => __( 'Our server could not complete the request. Please click the link again!', 'pinpoll' ),
    'wperror' => __( 'Our server could not complete the request. Please click the link again (WP)!', 'pinpoll' )
  );
}

/**
 * Polls Texts
 * Description: Texts for page "Polls"
 *
 * @return array texts
 */
function pinpoll_get_allpolls_texts() {
  return array(
    'title' => __( 'All Polls', 'pinpoll' ),
    'search' => __( 'Search', 'pinpoll' ),
    'delete' => __( 'Are you sure?', 'pinpoll' ),
    'activate' => __( 'You are not allowed to activate this poll.', 'pinpoll' ),
    'deactivate' => __( 'You are not allowed to deactivate this poll.', 'pinpoll' ),
    'createpoll' => __( 'Create Poll', 'pinpoll' ),
  );
}

/**
 * Create Poll Texts
 * Description: Texts for page "Create Poll"
 *
 * @return array texts
 */
function pinpoll_get_createpoll_texts() {
  return array(
    'title' => __( 'Create Poll', 'pinpoll' ),
    'wperror' => __( 'Our server could not complete the request. Please refresh page (WP)!', 'pinpoll' )
  );
}

/**
 * Poll Detail Texts
 * Description: Texts for page "Details"
 *
 * @return array texts
 */
function pinpoll_get_polldetail_texts() {
  return array(
    'title' => __( 'Details', 'pinpoll' ),
    'pollactive' => __( 'Your poll is active.', 'pinpoll' ),
    'pollinactive' => __( 'Your poll is inactive.', 'pinpoll' ),
    'deactivate' => __('Deactivate Poll', 'pinpoll'),
    'timeoutset' => __('Set Timeout', 'pinpoll'),
    'months' => array(
      'jan' => __( '01-Jan', 'pinpoll' ),
      'feb' => __( '02-Feb', 'pinpoll' ),
      'mar' => __( '03-Mar', 'pinpoll' ),
      'apr' => __( '04-Apr', 'pinpoll' ),
      'may' => __( '05-May', 'pinpoll' ),
      'jun' => __( '06-Jun', 'pinpoll' ),
      'jul' => __( '07-Jul', 'pinpoll' ),
      'aug' => __( '08-Aug', 'pinpoll' ),
      'sep' => __( '09-Sep', 'pinpoll' ),
      'oct' => __( '10-Oct', 'pinpoll' ),
      'nov' => __( '11-Nov', 'pinpoll' ),
      'dec' => __( '12-Dec', 'pinpoll' ),
    ),
    'timeouta' => __( 'Voting will be possible from', 'pinpoll' ),
    'timeoutb' => __( 'to', 'pinpoll' ),
    'from' => __( 'From', 'pinpoll' ),
    'to' => __( 'To', 'pinpoll' ),
    'timeoutsetbtn' => __( 'Set', 'pinpoll' ),
    'cancel' => __( 'Cancel', 'pinpoll' ),
    'timeoutcancel' => __( 'Cancel Timeout', 'pinpoll' ),
    'activate' => __('Activate Poll', 'pinpoll'),
    'preview' => __( 'Preview', 'pinpoll' ),
    'edit' => __( 'Edit', 'pinpoll' ),
    'embed' => __( 'Embed', 'pinpoll' ),
    'delete' => __( 'Delete', 'pinpoll' ),
    'reset' => __( 'Reset', 'pinpoll' ),
    'update' => __( 'Update', 'pinpoll' ),
    'curres' => __( 'Current Result', 'pinpoll' ),
    'pie' => __( 'Show Doughnut', 'pinpoll' ),
    'bar' => __( 'Show Bar Chart', 'pinpoll' ),
    'download' => __( 'Download Chart', 'pinpoll' ),
    'colvotes' => __( 'Collect more votes', 'pinpoll' ),
    'colvoteshint' => __( 'In order to collect more votes, try this:', 'pinpoll' ),
    'hintembed' => __( 'Embed this poll in your articles.', 'pinpoll' ),
    'hintfacebook' => __( 'Share your poll on Facebook.', 'pinpoll' ),
    'stats' => __( 'Stats', 'pinpoll' ),
    'moredata' => __( 'More data...', 'pinpoll' ),
    'votes' => __( 'Votes', 'pinpoll' ),
    'reco' => __( 'Recommendations', 'pinpoll' ),
    'user' => __( 'Users', 'pinpoll' ),
    'usertooltip' => __( 'Number of unique users who have viewed this poll.', 'pinpoll' ),
    'views' => __( 'Views', 'pinpoll' ),
    'viewstooltip' => __( 'Total number of times this poll has been viewed.', 'pinpoll' ),
    'participant' => __( 'Participants', 'pinpoll' ),
    'participanttooltip' => __( 'Number of unique users who have voted.', 'pinpoll' ),
    'votestooltip' => __( 'Total number of votes generated by this poll.', 'pinpoll' ),
    'engagement' => __( 'Engagement', 'pinpoll' ),
    'engagementtooltip' => __( 'Votes / Users', 'pinpoll' ),
    'votespart' => __( 'Votes/Participant', 'pinpoll' ),
    'votesparttooltip' => __( 'Votes / Participant', 'pinpoll' ),
    'lifetime' => __( 'Lifetime', 'pinpoll' ),
    '24hrs' => __( '24 Hours', 'pinpoll' ),
    'clicksvote' => __( 'Clicks (after vote)', 'pinpoll' ),
    'clicksvotetooltip' => __( 'Number of clicks on recommendations directly after user has voted.', 'pinpoll' ),
    'clickstotal' => __( 'Clicks (total)', 'pinpoll' ),
    'clickstotaltooltip' => __( 'Clicks (total)', 'pinpoll' ),
    'clickratevotes' => __( 'Click Rate (Votes)', 'pinpoll' ),
    'clickratevotestooltip' => __( 'Clicks (after vote) / Votes.', 'pinpoll' ),
    'clickrateviews' => __( 'Click Rate (Views)', 'pinpoll' ),
    'clickrateviewstooltip' => __( 'Clicks (total) / Views.', 'pinpoll' ),
    'clickstotalreco' => __( 'Clicks (total)', 'pinpoll' ),
    'clickstotalrecotooltip' => __( 'Number of clicks on recommendations.', 'pinpoll' ),
    'votescountry' => __( 'Votes per Country', 'pinpoll' ),
    'tableno' => __( '#', 'pinpoll' ),
    'country' => __( 'Country', 'pinpoll' ),
    'countryvotes' => __( 'Votes', 'pinpoll' ),
    'nodata' => __( 'No data available.', 'pinpoll' ),
    'city' => __( 'City', 'pinpoll' ),
    'votescity' => __( 'Votes per City', 'pinpoll' ),
    'cityvotes' => __( 'Votes', 'pinpoll' ),
    'swalresettitle' => __( 'Reset poll?', 'pinpoll' ),
    'swalresettext' => __( 'Attention: This action cannot be undone. Please confirm to reset your poll and let users vote from zero. Old votes can still be retrieved from the history view.', 'pinpoll' ),
    'swalconfirmreset' => __( 'Yes, reset', 'pinpoll' ),
    'swalconfirmdelete' => __( 'Yes, delete', 'pinpoll' ),
    'swalresetedtitle' => __( 'Reset!', 'pinpoll' ),
    'swalresetedtext' => __( 'Your poll was successfully reset!', 'pinpoll' ),
    'swaldeletetitle' => __( 'Are you sure?', 'pinpoll' ),
    'swaldeletetext' => __( 'Attention: This action cannot be undone. Please confirm to delete the poll and all related data.', 'pinpoll' ),
    'swaldeletedtitle' => __( 'Deleted!', 'pinpoll' ),
    'swaldeletedtext' => __( 'Your poll was successfully deleted.', 'pinpoll' ),
    'swalerrortitle' => __( 'Error', 'pinpoll' ),
    'swalerrortext' => __( 'Something went wrong.', 'pinpoll' ),
    'swalembedtitle' => __( 'Embed', 'pinpoll' ),
    'swalembedtext' => __( 'Copy & paste this shortcode to your articles:', 'pinpoll' ),
    'swalembedtextWidget' => __( 'Copy & paste this code to your widgets:', 'pinpoll' ),
    'unauth' => __( 'You are not allowed to modify this poll!', 'pinpoll' ),
    'wperror' => __( 'Our server could not complete the request. Please refresh page (WP)!', 'pinpoll' )
  );
}

/**
 * Switch Account Texts
 * Description: Texts for page "Switch Account"
 *
 * @return array texts
 */
function pinpoll_get_switchaccount_texts() {
  return array(
    'title' => __( 'Switch your Pinpoll Account', 'pinpoll' ),
    'message' => __( 'Please enter your e-mail address and password for Pinpoll to link this account with WordPress.', 'pinpoll' ),
    'email' => __( 'E-Mail:', 'pinpoll' ),
    'emailhint' => __( 'Your e-mail address used with Pinpoll', 'pinpoll' ),
    'pw' => __( 'Password:', 'pinpoll' ),
    'pwhint' => __( 'Your password used with Pinpoll', 'pinpoll' ),
    'btn' => __( 'Switch Account', 'pinpoll' ),
    'emailmessage' => __( 'E-Mail address is required!', 'pinpoll' ),
    'passwordmessage' => __('Password is required!', 'pinpoll' ),
    'forgotpw' => __( 'Forgot Password?', 'pinpoll' ),
    'invalidcred' => __( 'Sorry, invalid credentials provided. Please try again!', 'pinpoll' ),
  );
}

/**
 * Table Texts
 * Description: Texts for page "Polls"
 *
 * @return array texts
 */
function pinpoll_get_table_texts() {
  return array(
    'selectpoll' => __( 'Please select a poll to edit!', 'pinpoll' ),
    'die' => __( 'You are not allowed to delete something here!', 'pinpoll' ),
    'deleteonea' => __( 'Poll ', 'pinpoll' ),
    'deleteoneb' => __( 'deleted! ', 'pinpoll' ),
    'error' => __( 'Our server could not complete the request. Please refresh page!', 'pinpoll' ),
    'deletemultiple' => __( 'Polls deleted!', 'pinpoll' ),
    'category' => __( 'Select Category', 'pinpoll' ),
    'bulkdetails' => __( 'Details', 'pinpoll' ),
    'bulkdelete' => __( 'Delete', 'pinpoll' ),
    'wperror' => __( 'Our server could not complete the request. Please refresh page (WP)!', 'pinpoll' )
  );
}

/**
 * TinyMCE Texts
 * Description: Texts for "TinyMCE" plugin
 *
 * @return array texts
 */
function pinpoll_get_tinymce_texts() {
  return array(
    'insert' => __( 'Insert Poll', 'pinpoll' ),
    'quickinsert' => __( 'Insert Shortcode', 'pinpoll' ),
    'selectpoll' => __( 'Select Poll', 'pinpoll' ),
    'title' => __( 'Search for poll', 'pinpoll' ),
    'emptytable' => __( 'No polls found.', 'pinpoll' ),
    'error' => __( 'Our server could not complete the request. Please refresh page!', 'pinpoll' ),
    'search' => __( 'Search for:', 'pinpoll' ),
    'btnsearch' => __( 'Search', 'pinpoll' ),
    'tableselect' => __( 'Select', 'pinpoll' ),
    'tableid' => __( 'ID', 'pinpoll' ),
    'quickinsertlabel' => __( 'Poll ID: ', 'pinpoll' ),
    'tablequestion' => __( 'Poll', 'pinpoll' ),
    'searchmessage' => __( 'Search for polls by ID or question.', 'pinpoll' ),
    'sessionexpired' => __( 'Your session has expired. Please visit your Dashboard!' )
  );
}

/**
 * Admin Menu Texts
 * Description: Texts for "Menuslug (admin page)"
 *
 * @return array texts
 */
function pinpoll_get_menuslug_texts() {
  return array(
    'title' => __( 'Pinpoll', 'pinpoll' ),
    'dashboard' => __( 'Dashboard', 'pinpoll' ),
    'polls' => __( 'Polls', 'pinpoll' ),
    'create' => __( 'Create Poll', 'pinpoll' ),
    'settings' => __( 'Account Settings', 'pinpoll' ),
  );
}

?>
