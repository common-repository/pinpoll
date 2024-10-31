<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES config, api, texts
require_once( 'config/pinpoll-config.php' );
require_once( 'functions/pinpoll-functions.php' );
require_once( 'api/pinpoll-api.php' );
require_once( 'resources/pinpoll-texts.php' );


/**
 * Classname:   PinpollSwitchAccount
 * Description: Enables users to switch their pinpoll account, if they have
 *              more than one.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollSwitchAccount {


  private $ppApi;


  //Default construct
  function __construct() {
    $this->ppApi = new PinpollApi();
    $this->pinpoll_switch_account_page();
  }

  /**
   * Switch Account Page
   * Description: Show page "Switch Account" if user clicks on "Change Email"
   *              in page "Settings"
   */
  function pinpoll_switch_account_page() {

    $texts = pinpoll_get_switchaccount_texts();
    $pp = get_option( 'pinpoll_account' );
    $resetnonce = wp_create_nonce( 'reset_pw' );

    //check for submit and collect email and password
    if( $this->submit('submit_logon_switch') ) {
      $email = isset( $_POST['emailSwitch'] ) ? $_POST['emailSwitch'] : '';
      $password = isset( $_POST['passwordSwitch'] ) ? $_POST['passwordSwitch'] : '';
      $appkey = uniqid( $email );

      if( !empty( $email ) && !empty( $password ) ) {
        $body = array(
          'email' => $email,
          'cockpit_password' => $password,
          'app_key' => $appkey
        );
        $this->ppApi->pinpoll_signin( $body, true );
      } else {
        printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $texts['invalidcred'], 'pinpoll' )  );
      }
    }

    $cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . get_option( 'pinpoll_jwt' );
    $resetPWUrl = 'admin.php?page=settings&resetpw=true&resetnonce=' . $resetnonce;

    ?><div class="wrap">
      <form method="post">
        <div class="pp-header-line">
          <h3>
            <i class="fa fa-lg fa-user"></i>
            <?php printf( $texts['title'] ); ?>
            <a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
          </h3>
        </div>
        <p><?php printf( $texts['message'] ); ?></p>
          <table id="logonTable" class="form-table" border="0">
            <tr>
              <th scope="row"><?php printf( $texts['email'] ); ?></th>
              <td><input type="text" name="emailSwitch" id="pp-email" size="40" required="true">
               <p class="description"><?php printf( $texts['emailhint'] ); ?></p>
              </input></td>
            </tr>
            <tr>
              <th scope="row"><?php printf( $texts['pw'] ); ?></th>
              <td><input type="password" name="passwordSwitch" id="pp-password" size="40" required="true">
                <p class="description"><?php printf( $texts['pwhint'] ); ?></p>
              </input></td>
            </tr>
          </table>
          <table class="pp-button-table">
            <tr>
              <td id="pp-button-left"><?php submit_button( $texts['btn'], 'btn button-primary', 'submit_logon_switch' ) ?></td>
              <td id="pp-button-right"><a href="<?php printf( $resetPWUrl ); ?>" class="pp-button-secondary"><?php printf( $texts['forgotpw'] ); ?></a></td>
            </tr>
          </table>
      </div>
  </form>
  <!-- Customized field validation -->
  <script>
    var email = '<?php printf( $_POST['emailSwitch'] ); ?>'
  </script>


  <?php
    //printf(pinpoll_include_script('pinpoll_login_validate.js'));
  }

  /**
   * Helper Method Submit
   * Description: Helps to access on submits with different names
   *              (default submit)
   * @param  string $trigger name of submit
   * @return HTTP            post
   */
  function submit($trigger = 'submit') {
    return (isset($_POST[$trigger]) || isset($_POST[$trigger.'_x']) || isset($_GET[$trigger]) || isset($_GET[$trigger.'_x']));
  }

}
$PinpollSwitchAccount = new PinpollSwitchAccount();
?>
