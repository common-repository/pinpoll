<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES table, config, texts
require_once( 'pinpoll-table.php' );
require_once( 'config/pinpoll-config.php' );
require_once( 'functions/pinpoll-functions.php' );
require_once( 'resources/pinpoll-texts.php' );


/**
 * Classname:   PinpollOverview
 * Description: Generate table witch PinpollTable class to list all polls
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollOverview
{
  //DEFAULT CONSTRUCT
  function __construct()
  {
    $this->pinpoll_check_for_actions();
  }

  /**
   * Check Post Actions
   * Description: Check if the users has clicked on details link of a poll.
   */
  function pinpoll_check_for_actions() {

    //choose between table and detail view
    if( !empty($_GET['action']) && 'details' == $_GET['action'] ) {
      $this->pinpoll_include_detail();
    } else {
      $this->pinpoll_overview_page();
    }
  }

  /**
   * Overview Page
   * Description: Show Page "Polls"
   */
  function pinpoll_overview_page() {
    //prepare table
    $ppTable = new PinpollTable();
    $ppTable->prepare_items();
    $jwt = get_option('pinpoll_jwt');
    //get current category (selectbox) from hidden field
    $category = empty( $_REQUEST['hiddenSelectedCat'] ) ? 'Category' : $_REQUEST['hiddenSelectedCat'];
    $texts = pinpoll_get_allpolls_texts();
    $cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . get_option( 'pinpoll_jwt' );

    ?>
    <div class="wrap">
      <div class="pp-header-line">
            <h3>
                <i class="fa fa-lg fa-tasks"></i>
              </span> <?php printf( $texts['title'] ); ?>
                <a href="admin.php?page=createpoll" id="pp-add-button" class="pp-add-button">
                        <i class="fa fa-sm fa-plus"></i>
                        <?php printf( $texts['createpoll'] ); ?>
                </a>
                <a href="admin.php?page=createpoll" id="pp-add-button-mobile" class="pp-add-button">
                  <i class="fa fa-lg fa-plus" aria-hidden="true"></i>
                </a>
              	<a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
              </h3>
        </div>
        <form id="pp-table-form" method="post">
          <input type="hidden" id="pp-hidden-selected" name="hiddenSelectedCat" value="" />
          <?php
          $ppTable->search_box( $texts['search'], 'searchTable' );
          $ppTable->display(); ?>
      </form>
  </div>
  <script type="text/javascript">
    var jwt = '<?php printf( $jwt ); ?>';
    var pluginurl = '<?php printf( plugins_url( 'pinpoll-all-polls.php', __FILE__ ) ); ?>';
    var category = '<?php printf( $category ); ?>';
    var ppPollsTexts = {
      'deleteMessage' : '<?php printf( $texts['delete'] ); ?>',
      'alertMessageActivate' : '<?php printf( $texts['activate'] ); ?>',
      'alertMessageDeactivate' : '<?php printf( $texts['deactivate'] ); ?>',
      'createPollText' : '<?php printf( $texts['createpoll'] ); ?>'
    }
    var baseURL = '<?php printf( PINPOLL_BASE_URL ); ?>';
  </script>
    <?php
      printf(pinpoll_include_script('pinpoll_polls.js'));
  }

  /**
   * Detail Page
   * Description: Show page "Detail"
   */
  function pinpoll_include_detail() {
    require_once('pinpoll-poll-detail.php');
  }

}

$PinpollOverview = new PinpollOverview();
?>
