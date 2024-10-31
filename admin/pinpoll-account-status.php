<?php

//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES config, api, texts
require_once( 'config/pinpoll-config.php' );
require_once( 'functions/pinpoll-functions.php' );
require_once( 'api/pinpoll-api.php' );
require_once( 'resources/pinpoll-texts.php' );

/**
 * Classname: 	PinpollAccount
 * Description: Contains logic for the whole authentification flow except
 * 							switching account.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollAccount
{
	//global fields
	private $ppApi;
	private $texts;



  //default construct
	function __construct()
	{
		$this->ppApi = new PinpollApi();
		$this->texts = pinpoll_get_accountstatus_texts();
		if( isset( $_GET['switchaccount'] ) && $_GET['switchaccount'] == 'true' ) {
			$this->pinpoll_switchaccount_page();
		} elseif( $this->submit('setpassword') || (!empty($_GET['resetpw']) && $_GET['resetpw'] == 'true' )) {
			if( wp_verify_nonce( $_GET['resetnonce'], 'reset_pw' ) ) {
				$this->pinpoll_setpw();
			}
		} else  {
			$this->pinpoll_user_in_wp();
		}
		add_action('wp_loaded', 'check_redirect');
	}

	/**
	 * Check User in WP
	 * Description: Checks if user exist and checks if there was a login post
	 */
	function pinpoll_user_in_wp() {

		//if redirect from tinymce and user has no password do sign in for user
		if( !empty($_GET['tinymce']) && !empty(get_option('pinpoll_account')['appkey']) ) {
			$this->pinpoll_do_signin_for_user();
		}

		//check for log on post
		if( $this->submit('logon') ) {
			$this->pinpoll_logon();
		}

		$pinpollData = get_option( 'pinpoll_account' );

		if( empty($pinpollData['email']) || empty($pinpollData['appkey']) ) { //if no email in wp_options, call api to check if user exists
			$this->pinpoll_user_exists();
		} else { //if email and jwt already saved in db, redirect to settings view
			$this->pinpoll_settings_page();
		}
	}

	function pinpoll_setpw() {
		$pp = get_option( 'pinpoll_account' );
		$siteurl = get_option( 'siteurl' ) . '/wp-admin/admin.php?page=settings';

		$body = array(
			'email' => $pp['email'],
			'redirect' => $siteurl
		);
		if( $this->ppApi->pinpoll_set_password( $body ) ) {
			printf( '<div class="wrap"><div class="updated notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['infosetpw'], 'pinpoll' ) );
		} else {
			printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['errorsetpw'], 'pinpoll' ) );
		}
	}

	/**
	 * Sign In for User
	 * Description: Calls API v1/auth/signin to automatically sign in user with
	 *   						appkey from wp_options.
	 */
	function pinpoll_do_signin_for_user() {
		$this->ppApi->pinpoll_refresh_sign_in();
		printf( '<div class="wrap"><div class="updated notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['inforefresh'], 'pinpoll' ) );
	}

	/**
	 * Check for Log In
	 * Description: Check if there was a login post and call API v1/auth/signin.
	 */
	function pinpoll_logon() {
		$pp = get_option('pinpoll_account');

		//if there was a post with name logon, then collect email, pw and call API
		$password = isset( $_POST['password'] ) ? $_POST['password'] : '';
		$email = $pp['email'];
		$appkey = uniqid( $email );

		if( !empty( $password ) ) {
			$body = array(
				'email' => $email,
				'cockpit_password' => $password,
				'app_key' => $appkey
			);
			$this->ppApi->pinpoll_signin( $body, false );
		}

	}

  /**
   * User Exist
   * Description: Call Pinpoll api to check if there is already a user
   * 							with the admin email
   */
	function pinpoll_user_exists() {

		$email = get_option( 'admin_email' );
		$responseData = $this->ppApi->pinpoll_users( $email );

		$cockpitRegistered = isset( $responseData['body']['cockpit_registered'] ) ? $responseData['body']['cockpit_registered'] : '';

		if( $responseData['response']['code'] !== 200 && $responseData['response']['code'] != 422 ) {
			printf( '<div class="wrap"><div class="error"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support%20Request&Body=' . $responseData['response']['message'] . '">support@pinpoll.com</a>' );
		} else {
			if( $responseData['response']['code'] == 422 ) {
				$this->pinpoll_register_user(); //user has no account, so create one
			} else {
				$ppDB = get_option('pinpoll_account');
				$ppDB['email'] = $email;
				update_option('pinpoll_account', $ppDB); //user exists, so store email in wp_options

				if( !$cockpitRegistered ) {
					$this->pinpoll_redirect_to_reset_pw();
				} else {
					$this->pinpoll_login_page();
				}
			}
		}
	}

	/**
	 * Redirect to Reset PW Page
	 * Description: Redirect to Pinpoll´s redirect to reset pw page, if user
	 * 							is already registered at Pinpoll, but has no password.
	 */
	function pinpoll_redirect_to_reset_pw() {
		$siteurl = get_option( 'siteurl' );
		$siteurl = $siteurl . '/wp-admin/admin.php?page=settings';
		$resetnonce = wp_create_nonce( 'reset_pw' );
		?>
			<div class="wrap">
				<div class="pp-header-line">
					<h3>
						<i class="fa fa-lg fa-wrench"></i> <?php printf( $this->texts['title'] ); ?>
						<img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img>
					</h3>
				</div>
				<form id="pp-resetpw-form" method="post" action="admin.php?page=settings&setpassword=true&resetnonce=<?php printf( $resetnonce ); ?>">
					<p><?php printf( $this->texts['userexistsetpw'] ); ?></p>
					<?php submit_button( $this->texts['linkset'], 'btn button-primary', 'setpassword', true ); ?>
				</form>
			</div>
		<?php
	}

	/**
	 * Register User
	 * Description: Sign Up user via API v1/auth/signup
	 */
	function pinpoll_register_user() {

		global $wpdb;

		//get all required user data
		$param = 'id';
		$email = get_option('admin_email');
		$website = get_option('home');
		$tableName = $wpdb->prefix . 'users';

		$userQuery = 'select ' . $param . ' from ' . $tableName . ' where user_email = "'. $email .'"';
		$userId = $wpdb->get_var($userQuery);


		$appKey = uniqid( $email );

		$body = array(
			'email' => $email,
			'app_key' => $appKey,
			'language' => 'en',
			'domain' => $website
		);

		$firstName = get_user_meta($userId, 'first_name', true);
		if (!empty($firstName) && $firstName !== '0'){
			$body['firstname'] = $firstName;
		}

		$lastName = get_user_meta($userId, 'last_name', true);
		if (!empty($lastName) && $lastName !== '0'){
			$body['lastname'] = $lastName;
		}

		$body = json_encode( $body );

		$result = $this->ppApi->pinpoll_signup( $body );
		$responseJWT = $result['responseJWT'];
		$responseBody = $result['responseBody'];

		$error = isset( $responseBody['error'] ) ? $responseBody['error'] : '';

		if( empty( $error ) && !empty( $responseJWT ) ) {
			$pp = get_option('pinpoll_account');
			$pp['email'] = $email;
			$pp['appkey'] = $appKey;
			update_option( 'pinpoll_account', $pp ); //save email in wp_options
			update_option( 'pinpoll_jwt', $responseJWT ); //save jwt in wp_options
			$this->pinpoll_settings_page(); //redirect to user_exist view
		} else {
			printf( '<div class="wrap"><div class="error"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support%20Request&Body=' . $error . '">support@pinpoll.com</a>'  );
		}
	}

	/**
	 * Login Page
	 * Description: Render view for login
	 */
	function pinpoll_login_page() {
		$pp = get_option('pinpoll_account');
		$resetnonce = wp_create_nonce( 'reset_pw' );
		$resetPWUrl = 'admin.php?page=settings&resetpw=true&resetnonce=' . $resetnonce;

		//if there is a refresh, show message
		if( !empty( $_GET['refresh'] ) ) {
			printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['errorsession'], 'pinpoll' )  );
		}

		?><div class="wrap">
			<div class="pp-header-line">
				<h3><span class="dashicons dashicons-admin-links"></span> <?php printf( $this->texts['title'] ); ?> <img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></h3>
			</div>
				<p><?php printf( $this->texts['userexist'] ); ?></p>
			<form method="post" id="pinpolluserform" action="admin.php?page=settings&redirectLogon=true">
					<table id="logonTable" class="form-table" border="0">
						<tr>
							<th scope="row"><?php printf( $this->texts['email'] ); ?> </th>
							<td><input type="text" name="txtEmail" size="40" name="email" id="email" placeholder="<?php printf( $pp['email'] ); ?>" readonly="true">
							 <p class="description"><?php printf( $this->texts['emailhint'] ); ?> </p>
							</input></td>
						<tr>
							<th scope="row"><?php printf( $this->texts['pw'] ); ?> </th>
							<td><input type="password" name="password" id="password" size="40" required="true">
								<p class="description"><?php printf( $this->texts['pwhint'] ); ?></p>
							</input></td>
						<tr>
					</table>
					<table class="pp-button-table">
						<tr>
							<td id="pp-button-left"><?php submit_button( $this->texts['btnlink'], 'btn button-primary', 'logon', true, array('redirectLogon' => 'true') ); ?></td>
							<td id="pp-button-right"><a href="<?php printf( $resetPWUrl ); ?>" target="_blank" class="pp-button-secondary"><?php printf( $this->texts['forgotpw'] ); ?></a></td>
						</tr>
					</table>
	</form>
</div>
  <!-- Custom field validation -->
	<?php
		//printf(pinpoll_include_script('pinpoll_login_validate.js'));
	}

	/**
	 * Submit Helper Method
	 * Description: Custom submit function which helps to check if there was a submit post
	 *
	 * @param string $trigger
	 */
	function submit($trigger = 'submit') {
		return (isset($_POST[$trigger]) || isset($_POST[$trigger.'_x']) || isset($_GET[$trigger]) || isset($_GET[$trigger.'_x']));
	}

	/**
	 * Settings Page
	 * Description: Render user_exist view
	 */
  function pinpoll_settings_page() {
		//refresh token on page load
		if (!isset( $_GET['refreshError'] )){
			$this->ppApi->refresh_token_with_signin();
		}
		else {
			$error = $_GET['message'];
			printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support%20Request&Body=' . $error . '">support@pinpoll.com</a>' );
		}
		$pp = get_option('pinpoll_account');
		$accountUrl = PINPOLL_COCKPIT_BASE_URL . '/account?token=' . get_option( 'pinpoll_jwt' );
		//if there is a redirect from succesfull log in, show message
		if( isset( $_GET['redirectLogon'] ) ) {
			printf( '<div class="wrap"><div class="updated notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['successlogin'], 'pinpoll' ) );
		} elseif( isset( $_GET['redirectSwitch'] ) ) { //if there is a redirect from succesfully switched account, show message
			printf( '<div class="wrap"><div class="updated notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['successswitch'], 'pinpoll' ) );
		}
		$cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . get_option( 'pinpoll_jwt' );
			?>
			<div class="wrap">
				<div class="pp-header-line">
					<h3>
						<i class="fa fa-lg fa-wrench"></i> <?php printf( $this->texts['titlesettings'] ); ?>
						<a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
						<a href="admin.php?page=createpoll" id="pp-add-button" class="pp-add-button">
							<i class="fa fa-plus" aria-hidden="true"></i>
							<?php printf( $this->texts['createpoll'] ); ?>
						</a>
						<a href="admin.php?page=createpoll" id="pp-add-button-mobile" class="pp-add-button">
							<i class="fa fa-lg fa-plus"></i>
						</a>
					</h3>
				</div>
					<p><?php printf( $this->texts['accountlink'] ); ?>
						<p style="font-weight:bold;"><?php printf( $pp['email'] ); ?></p>
					</p>
					<table class="pp-button-table">
						<tr>
							<td id="pp-button-left"><a href="admin.php?page=settings&switchaccount=true"><?php printf( $this->texts['changemail'] ) ?></a></td>
							<td id="pp-button-right"><a href="<?php printf( $accountUrl ); ?>" target="_blank"><?php printf( $this->texts['accountinfo'] ); ?></a></td>
						</tr>
					</table>
			</div>
				<?php
	}

	/**
	 * Switch Account Page
	 * Description: Show page "Switch Account"
	 */
	function pinpoll_switchaccount_page() {
		require_once( 'pinpoll-switch-account.php' );
	}
}

$PinpollAccount = new PinpollAccount();
?>
