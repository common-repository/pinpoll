<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES config and texts
require_once untrailingslashit(__DIR__) . '/../config/pinpoll-config.php';
require_once untrailingslashit(__DIR__) . '/../resources/pinpoll-texts.php';

/**
 * Classname:   PinpollApi
 * Description: Contains the whole logic for api calls to the pinpoll api,
 *              which are used in the plugin.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/api
 *
 */
class PinpollApi
{
    var $texts;

    //Default construct
    function __construct()
    {
      $this->texts = pinpoll_get_accountstatus_texts();
    }

    //AUTH API CALLS

    /**
     * API CALL: v1/auth/signin
     * Description: Sign In User to Pinpoll to receive a valid JWT
     * @param  array     $body     post body
     * @param  boolean   $redirect get param
     */
    public function pinpoll_signin( $body, $redirect ) {

      $url = PINPOLL_BASE_URL . '/auth/signin';
      $args = array(
        'method' => 'POST',
        'timeout' => '45',
        'redirect' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
          'X-Api-Key' => PINPOLL_API_KEY
        ),
        'body' => $body
      );

      $response = wp_remote_post( $url, $args );

      if( !is_wp_error( $response ) ) {
        $responseJWT = $this->parseJwt( $response);
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
        $error = isset( $responseBody['error'] ) ? $responseBody['error'] : '';

        //Update Options in WP DB
        //Add Email and Wordpress Token (conditional)
        if( $response['response']['code'] === 200 ) {
          $pp = get_option('pinpoll_account');
          $pp['email'] = $body['email'];
          $pp['appkey'] = $body['app_key'];
          update_option('pinpoll_account', $pp);
          update_option('pinpoll_jwt', $responseJWT);

          //If sign in gets called from switch-account, it should redirect to settings page
          if( $redirect ) {
            printf('<script>window.location.href="admin.php?page=settings&redirectSwitch=true"</script>');
          }
          //Print error messages in case of invalid credentials or other errors
        } else if( $response['response']['code'] == 401 || $response['response']['code'] == 403 || $response['response']['code'] == 422 ){
          if($response['response']['code'] == 401 && $responseBody['account_deactivated']) {
            printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', $responseBody['account_deactivated'] );
          } else {
            printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['invalidcred'], 'pinpoll' ) );
          }
          
        } else {
          printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support%20Request&Body=' . $error . '">support@pinpoll.com</a>'  );
        }
      } else {
        printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['wperror'], 'pinpoll' ) );
      }
    }

    /**
     * API CALL: v1/users?email=
     * Description: Check if user exist in pinpoll db
     * @param  string    $email        email
     * @return array     $responseData response body
     */
    public function pinpoll_users( $email ) {
      $url = PINPOLL_BASE_URL . '/users?email=' . $email;

  		$args = array(
  			'headers' => array(
  				'X-Api-Key' => PINPOLL_API_KEY,
  				'Content-Type' => 'application/json'
  			)
  		);

  		$response = wp_remote_get( $url, $args );

      if( !is_wp_error( $response ) ) {
        $responseData = json_decode( wp_remote_retrieve_body( $response ), true );

        $result = array(
          'body' => $responseData,
          'response' => $response['response']
        );
      } else {
        $result = array(
          'response' => array(
            'code' => '412',
            'message' => 'WP_ERROR'
          )
        );
      }

      return $result;
    }

    /**
     * API CALL: v1/auth/signup
     * Description: Register user in pinpoll db and receive a valid JWT
     * @param  array    $body    post body
     * @return array    $result  response header (jwt), response body
     */
    public function pinpoll_signup( $body ) {
      $url = PINPOLL_BASE_URL . '/auth/signup';

      $response = wp_remote_post($url, array(
  			'method' => 'POST',
  			'timeout' => '45',
  			'redirect' => '5',
  			'httpversion' => '1.0',
  			'blocking' => true,
  			'headers' => array(
  				'X-Api-Key' => PINPOLL_API_KEY,
  				'Content-Type' => 'application/json'
  			),
  			'body' => $body
  		));

      if( !is_wp_error( $response ) ) {
        $responseJWT = $this->parseJwt( $response);
    		$responseBody = json_decode( wp_remote_retrieve_body( $response ), true );

        $result = array(
          'responseJWT' => $responseJWT,
          'responseBody' => $responseBody
        );
      } else {
        $result = array(
          'responseJWT' => '',
          'responseBody' => array(
            'error' => '412'
          )
        );
      }

      return $result;
    }

    //POLL DETAILS API CALLS

    /**
     * API CALL: v1/polls/pollid
     * Description: Receive information about a specific poll via poll id
     * @param  string   $jwt          auth token
     * @param  int      $pollId       id
     * @return array    $responseBody response body
     */
    public function pinpoll_polls( $pollId ) {
      $response = $this->call_with_refresh_token_and_signin(function () use ($pollId){
        $url = PINPOLL_BASE_URL . '/polls/' . $pollId;

        $args = array(
          'method' => 'GET',
          'timeout' => '45',
          'redirect' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(
            'Authorization' => get_option( 'pinpoll_jwt' )
          )
        );

        return wp_remote_post( $url, $args );
      });

      if( !is_wp_error( $response ) ) {
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );

        $result = array(
          'response' => $response,
          'body' => $responseBody
        );
      } else {
        $result = array(
          'response' => array(
            'code' => '412'
          ),
          'body' => array(
            'error' => 'WP_ERROR'
          )
        );
      }

      return $result;
    }

    //TABLE API CALLS

    /**
     * API CALL: v1/polls/datatables
     * Description: Receive information about all polls of wordpress user
     * @param  array    $body          post body
     * @return array    $responseData  response body
     */
    public function pinpoll_datatables( $body ) {
      $response = $this->call_with_refresh_token_and_signin(function () use ($body) {
        $url = PINPOLL_BASE_URL . '/polls/datatables';

        return wp_remote_post( $url, array(
          'method' => 'POST',
          'timeout' => '45',
          'redirect' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(
            'Authorization' => get_option( 'pinpoll_jwt' )
            ),
          'body' => $body
        ) );
      });

      if( !is_wp_error( $response ) ) {
        $responseData = json_decode( wp_remote_retrieve_body( $response ), true );
        $result = array(
          'response' => $response,
          'body' => $responseData
        );
      } else {
        $result = array(
          'response' => array(
            'code' => '412'
          ),
          'body' => array(
            'error' => 'WP_ERROR'
          )
        );
      }

      return $result;
    }

    /**
     * API CALL: v1/polls/pollid
     * Description: Delete poll with id $pollId
     * @param   int     $pollId       id
     * @return  array   $responseBody response body
     */
     public function pinpoll_delete( $pollId ) {
      $response = $this->call_with_refresh_token_and_signin(function () use($pollId) {
        $url = PINPOLL_BASE_URL . '/polls/' . $pollId;
        $args = array(
          'method' => 'DELETE',
          'timeout' => '45',
          'redirect' => '5',
          'blocking' => true,
          'headers' => array(
            'Authorization' => get_option( 'pinpoll_jwt' )
          )
        );
        return wp_remote_post( $url, $args );
      });
      if( !is_wp_error( $response ) ) {
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
      } else {
        $responseBody = array(
          'error' => '412'
        );
      }
      return $responseBody;
    }

    /**
     * API CALL: v1/categories
     * Description: Receive information about the different categories which exist in pinpoll
     * @return array  $responseData  response body
     */
    public function pinpoll_categories() {
      $response = $this->call_with_refresh_token_and_signin(function () {
        $url = PINPOLL_BASE_URL . '/categories';

        return wp_remote_post( $url, array(
          'method' => 'GET',
          'timeout' => '45',
          'redirect' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(
          'Authorization' => get_option('pinpoll_jwt')
            )
          ) );
      });

      if( !is_wp_error( $response ) ) {
        $responseData = json_decode( wp_remote_retrieve_body( $response ), true );
      } else {
        $responseData = array();
      }

      return $responseData;
    }

    //DASHBOARD API CALLS

    /**
     * API Call: v1/auth/refresh
     * Description: Refresh token if current token in wp_options is expired, receive a new valid JWT
     * @return array succes, status(conditional)
     */
    private function pinpoll_refresh_token() {

      $refreshUrl = PINPOLL_BASE_URL . '/auth/refresh';
      $responseRefresh = wp_remote_post( $refreshUrl, array(
        'method' => 'POST',
        'timeout' => '45',
        'redirect' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
          'Authorization' => get_option('pinpoll_jwt')
        )
      ) );

      if( !is_wp_error( $responseRefresh ) ) {
        $responseJWT = $this->parseJwt( $responseRefresh);
        $responseRefreshBody = json_decode( wp_remote_retrieve_body( $responseRefresh ), true );
        $errorRefresh = isset( $responseRefreshBody['error'] ) ? $responseRefreshBody['error'] : '';

        //If success, then update jwt in wp_options
        if( empty( $errorRefresh ) ) {
          update_option( 'pinpoll_jwt', $responseJWT );
          return ['success' => true];
        } else {
          return [
            'success' => false,
            'status' => $responseRefresh['response']['code']
          ];
        }
      } else {
        return [
          'success' => false,
          'status' => '412'
        ];
      }

    }

    /**
     * API Call: v1/auth/signin
     * Description: If token gets invalid (1 week not refreshed), user has to sign in again to receive a valid token
     */
    public function pinpoll_refresh_sign_in() {

      $url = PINPOLL_BASE_URL . '/auth/signin';
      $account = get_option( 'pinpoll_account' );
      $response = wp_remote_post( $url, array(
        'method' => 'POST',
        'timeout' => '45',
        'redirect' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
          'X-Api-Key' => PINPOLL_API_KEY
        ),
        'body' => array(
          'email' => $account['email'],
          'app_key' => $account['appkey']
        )
      ) );

      if( !is_wp_error( $response ) ) {
        $responseJWT = $this->parseJwt( $response);

        //If success, update wp_options with new valid jwt
        if( $response['response']['code'] === 200 ) {
          update_option( 'pinpoll_jwt', $responseJWT );
          return true;
        }
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
        $error = isset( $responseBody['error'] ) ? $responseBody['error'] : 'error in refresh sign in';
        return ['bool' => false, 'error' => $error];
      }

    }

    /**
     * API CALL: v1/stats
     * Description: Receive information of generell stats used in dashboard
     * @param  string $jwt          auth token
     * @return array  $responseBody response body
     */
    public function pinpoll_stats() {

      $response = $this->call_with_refresh_token_and_signin(function () {
        $url = PINPOLL_BASE_URL . '/stats';

        $args = array(
          'method' => 'GET',
          'timeout' => '45',
          'redirect' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(
            'Authorization' => get_option('pinpoll_jwt')
          )
        );
        return wp_remote_get( $url, $args );
      });

      if( !is_wp_error( $response ) ) {
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
        $result = array(
          'body' => $responseBody,
          'response' => $response
        );
      } else {
        $result = array(
          'response' => array(
            'code' => '412'
          ),
          'body' => array(
            'error' => 'WP_ERROR'
          )
        );
      }

      return $result;
    }

    /**
     * API CALL: v1/stats/top
     * Description: Receive information of top five polls for page "Dashboard"
     * @return array response body
     */
    public function pinpoll_top() {
      $response = $this->call_with_refresh_token_and_signin(function () {
        $url = PINPOLL_BASE_URL . '/stats/top';

        $args = array(
          'method' => 'GET',
          'timeout' => '45',
          'redirect' => '5',
          'httpversion' => '1.0',
          'blocking' => true,
          'headers' => array(
            'Authorization' => get_option('pinpoll_jwt')
          )
        );
        return wp_remote_get( $url, $args );
      });

      if( !is_wp_error( $response ) ) {
        $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
      } else {
        $responseBody = array(
          'body' => array(
            'error' => 'WP_ERROR'
          )
        );
      }

      return $responseBody;
    }

    /**
     * API CALL: v1/password/email
     * Description: Send email to user, which includes a link to set a password.
     *
     * @param array $body post body
     */
    public function pinpoll_set_password( $body ) {
      $url = PINPOLL_BASE_URL . '/password/email';
      $args = array(
        'method' => 'POST',
        'timeout' => '45',
        'redirect' => '5',
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
          'X-Api-Key' => PINPOLL_API_KEY,
        ),
        'body' => $body
      );

      $response = wp_remote_post( $url, $args );

      if( !is_wp_error( $response ) ) {
        if( $response['response']['code'] === 200 ) {
          return true;
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    function call_with_refresh_token_and_signin($callback) {
      $response= $callback();
      // handle unknown error
      if (is_wp_error($response)) {
        $refresh = $this->pinpoll_refresh_sign_in();
        if ($refresh['bool']){
          return $callback();
        } else {
          printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p><p><b>Details:</b></br> %s </p> </div></div>', esc_html__($this->texts['errorfatal'], 'pinpoll') . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support">support@pinpoll.com</a>', $response->get_error_message() );
          die;
        }
      }
      $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
      if(!empty($response) && is_array($response) && ($response['response']['code'] != 200)) {
        // handle token expired
        if (!empty($responseBody) && (array_key_exists('error', $responseBody) && $responseBody['error'] === 'token_expired')) {
          $refreshResponse = $this->pinpoll_refresh_token();
          if (!empty($refreshResponse) && $refreshResponse['success']) {
            return $callback();
          }
        }
        // handle token invalid
        if( (isset($refreshResponse) && !$refreshResponse['success']) || (!empty($responseBody) && array_key_exists('error', $responseBody) && $responseBody['error'] === 'token_invalid')) {
          $refresh = $this->pinpoll_refresh_sign_in();
          if ($refresh['bool']){
            return $callback();
          } else {
            //refresh signin failed
            printf('<script>window.location.href="admin.php?page=settings&refreshError=true&message=' . $refresh['error'] . '"</script>');
          }
        } else {
          // unrecoverable error
          printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p><p><b>Details:</b></br> %s </p> </div></div>', esc_html__($this->texts['errorfatal'], 'pinpoll') . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support">support@pinpoll.com</a>', implode("</br>",$responseBody) );
          die;
        }
      }
      return $response;
    }

    function refresh_token_with_signin() {
      $refreshResponse = $this->pinpoll_refresh_token();
      if( !$refreshResponse['success'] || (!empty($responseBody) && $responseBody['error'] === 'token_invalid')) {
        $refresh = $this->pinpoll_refresh_sign_in();
        if (!$refresh['bool']){
          //printf( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' )  );
          printf('<script>window.location.href="admin.php?page=settings&refreshError=true&message=' . $refresh['error'] . '"</script>');
        }
      }
    }

    function parseJwt($response) {
      // wp versions < 4.6 header cas sensitive, fix for #21
      return empty(wp_remote_retrieve_header( $response, 'Authorization')) ? wp_remote_retrieve_header( $response, 'authorization') : wp_remote_retrieve_header( $response, 'Authorization');
    }

}

?>
