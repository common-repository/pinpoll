<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES texts
require_once( 'resources/pinpoll-texts.php' );
require_once( 'api/pinpoll-api.php' );


/**
 * Classname:   PinpollCreate
 * Description: View for creating a poll via iframe
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollCreate
{
  private $ppApi;
  private $texts;
  //Default construct
  function __construct()
  {
    $this->ppApi = new PinpollApi();
    $this->texts = pinpoll_get_createpoll_texts();
    $this->pinpoll_create_page();
  }

  /**
   * Create Page
   * Description: Show page "Create Poll" and load iframe
   */
  function pinpoll_create_page() {

    //refresh token on page load
    $this->ppApi->refresh_token_with_signin();

    $jwt = get_option( 'pinpoll_jwt' );
    $cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . $jwt;
    $embedCreateUrl = PINPOLL_COCKPIT_BASE_URL . '/widget/polls/create?token=' . $jwt;

    $wpnonce = wp_create_nonce( 'poll_detail' );

    ?><div class="wrap">
      <div class="pp-header-line">
        <h3>
          <i class="fa fa-lg fa-plus-square"></i>
          <?php printf( $this->texts['title'] ); ?>
          <a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
        </h3>
      </div>
      <iframe id="pp-create-embed" class="pp-embed" src="<?php printf( $embedCreateUrl ); ?>"></iframe>
      <script>
        var ppNonce = '<?php printf( $wpnonce ); ?>';
      </script>
      <?php printf(pinpoll_include_script('pinpoll_create.js')); ?>
    </div>
    <?php
  }

}

$PinpollCreate = new PinpollCreate();
?>
