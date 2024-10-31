<?php

//INLCUDES wp-load to use wp functions
include_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );

/**
 * Classname:   PinpollStoreJS
 * Description: Helper class to store js vars from pinpoll-dashboard.js
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollStoreJS
{
  //Default construct
  function __construct()
  {
    $this->pinpoll_store_vars();
  }

  /**
   * Store Data
   * Description: Stores information about the time of feedback,
   *              that is received via POST
   */
  function pinpoll_store_vars() {
    $date = isset( $_POST['ppFeedbackDate'] ) ? $_POST['ppFeedbackDate'] : '';
    if( !empty( $date ) ) {
      $feedback = get_option( 'pinpoll_feedback' );
      $feedback['date'] = date( 'Y-m-d H:i:s' );
      $feedback['pollCreated'] = 'inactive';
      update_option( 'pinpoll_feedback', $feedback );
    }
  }
}

$PinpollStoreJS = new PinpollStoreJS();

?>
