<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

//INCLUDES config, country, api, texts
require_once( 'config/pinpoll-config.php' );
require_once( 'functions/pinpoll-functions.php' );
require_once( 'resources/pinpoll-country.php' );
require_once( 'api/pinpoll-api.php' );
require_once( 'resources/pinpoll-texts.php' );

/**
 * Classname:   PinpollDashboard
 * Description: Contains page "Dashboard" in admin menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollDashboard
{
  private $ppApi;

  //Default construct
  function __construct()
  {
    $this->ppApi = new PinpollApi();
    $this->pinpoll_dashboard_page();
  }

  /**
   * Dashboard Page
   * Description: Show page "Dashboard" in admin menu
   */
  function pinpoll_dashboard_page() {

    $texts = pinpoll_get_dashboard_texts();
    $data = $this->ppApi->pinpoll_stats();

    //Init fields

    $top = $this->ppApi->pinpoll_top();

    $countryCodes = pinpoll_get_country_codes();
    $cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . get_option( 'pinpoll_jwt' );

    ?>
      <div class="wrap">
          <div class="pp-header-line">
            <h3>
               <i class="fa fa-lg fa-tachometer"></i>
               <?php printf( $texts['title'] ) ?>
                <a href="admin.php?page=createpoll" id="pp-add-button" class="pp-add-button">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  <?php printf( $texts['createpoll'] ); ?>
                </a>
                <a href="admin.php?page=createpoll" id="pp-add-button-mobile" class="pp-add-button">
                  <i class="fa fa-lg fa-plus" aria-hidden="true"></i>
                </a>
                <a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
            </h3>
          </div>
          <div id="pp-dashboard-left" class="pp-container">
            <div class="pp-container-left-a">
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['allvotes'] ); ?></span>
                  <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
                </h2>
                <div class="pp-inside">
                      <h1><span class="pp-count-votes"><?php if(isset($data['body']['votes'])) printf( $data['body']['votes'] ); ?></span></h1>
                      <small><?php printf( $texts['votestotal'] ); ?></small>
                </div>
              </div>
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['votescountry'] ); ?></span>
                  <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
                </h2>

                  <?php
                  if( !empty( $data['body']['countries'] ) ) {
                    $count = 1;
                    ?>
                    <div class="pp-inside">
                    <table class="pp-dashboard-table">
                      <thead>
                        <th><?php printf( $texts['tableno'] ); ?></th>
                        <th><?php printf( $texts['country'] ); ?></th>
                        <th><?php printf( $texts['votes'] ); ?></th>
                      </thead>
                      <tbody>
                    <?php
                    foreach ( $data['body']['countries'] as $country ) {
                      ?>
                      <tr>
                        <td><?php printf( $count++ ); ?></td>
                        <td><?php printf( $countryCodes[$country['location']] ); ?></td>
                        <td><?php printf( $country['votes'] ); ?></td>
                      </tr>
                      <?php
                    }
                     ?>
                   </tbody>
                  </table>
                  </div>
                    <?php
                  } else {
                    ?>
                  <div class="pp-alert">
                    <div class="pp-dashboard-alert">
                      <?php printf( $texts['nodata'] ); ?>
                    </div>
                  </div>
                    <?php
                  }
                   ?>
              </div>
            </div>
            <div class="pp-container-left-b">
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['allvotes'] ); ?></span>
                  <span class="pp-widget-inline-info"> <?php printf( $texts['30days'] ); ?> </span>
                </h2>
                <div class="pp-inside">
                  <div class="main">
                      <h1><span class="pp-count-votes"><?php if(isset($data['body']['votes_month'])) printf( $data['body']['votes_month'] ); ?></span></h1>
                      <small><?php printf( $texts['votestotal'] ); ?></small>
                  </div>
                </div>
              </div>
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['votescity'] ); ?></span>
                  <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
                </h2>
                    <?php
                    if( !empty( $data['body']['cities'] ) ) {

                      $count = 1;
                      ?>
                    <div class="pp-inside">
                      <table class="pp-dashboard-table">
                        <thead>
                          <th><?php printf( $texts['tableno'] ); ?></th>
                          <th><?php printf( $texts['city'] ); ?></th>
                          <th><?php printf( $texts['votes'] ); ?></th>
                        </thead>
                        <tbody>
                      <?php
                      foreach ( $data['body']['cities'] as $country ) {
                        ?>
                        <tr>
                          <td><?php printf( $count++ ); ?></td>
                          <td><?php printf( $country['location'] ); ?></td>
                          <td><?php printf( $country['votes'] ); ?></td>
                        </tr>
                        <?php
                      }
                       ?>
                     </tbody>
                    </table>
                   </div>
                      <?php
                    } else {
                      ?>
                      <div class="pp-alert">
                      <div class="pp-dashboard-alert">
                        <?php printf( $texts['nodata'] ); ?>
                      </div>
                    </div>
                      <?php
                    }
                     ?>
              </div>
            </div>
          </div>
          <div id="pp-dashboard-right" class="pp-container">
          <div id="pp-feedback">
            <div class="pp-dashboard-feedback">
                <table class="pp-dashboard-feedbacktable">
                  <tr>
                    <td style="width:70%;"><h1 id="pp-feedback-header-text" style="color: #31708f;"><?php printf( $texts['feedbackenjoy'] ); ?></h1></td>
                    <td style="width:15%;">
                      <button id="pp-love" type="button" class="pp-button-circle pp-button-like">
                        <i class="fa fa-heart"></i>
                      </button>
                      <button id="pp-check" type="button" class="pp-button-circle pp-button-primary">
                        <i class="fa fa-lg fa-check"></i>
                      </button>
                    </td>
                    <td style="width:15%;">
                      <button id="pp-hate" type="button" class="pp-button-circle pp-button-default">
                        <i class="fa fa-times"></i>
                      </button>
                      <button id="pp-check-hate" type="button" class="pp-button-circle pp-button-default">
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  <tr>
               </table>
             </div>
             <div id="pp-review-text">
               <div class="pp-feedback-formular">
                 <strong><?php printf( $texts['feedbackthanktitle'] ) ?></strong>
                 <p>
                   <?php printf( $texts['feedbackimprovemessagea'] ); ?>
                   <?php printf( $texts['feedbackimprovemessageb'] ); ?>
                   <br>
                   <br>
                   <a id="pp-plugin-review" target="_blank" href="<?php printf( PINPOLL_REVIEW_URL ); ?>">
                     <?php printf( $texts['feedbackreview'] ); ?>
                   </a>
                 </p>
               </div>
             </div>
             <div id="pp-improve-text">
               <div class="pp-feedback-formular">
                 <div class="pp-feedback-chat">
                   <strong>
                     <?php printf( $texts['feedbackimprove']) ?>
                   </strong>
                   <p><?php printf( $texts['feedbackmessagetextbox'] ); ?></p>
                 </div>
                 <textarea id="pp-feedback-hate-text" class="pp-feedback-textarea" placeholder="<?php printf( $texts['feedbackplaceholderhonest'] ); ?>"></textarea>
                 <br>
                 <div style="width:100%; position:relative;s">
                   <button id="pp-no-feedback"type="button" class="pp-button-primary" style=""><?php printf( $texts['feedbackquit'] ); ?></button>
                   <button id="pp-send-feedback" type="button" class="pp-button-primary" style="position:absolute; right:0px;"><?php printf( $texts['feedbacksend'] ); ?></button>
                 </div>
               </div>
             </div>
             <div id="pp-make-a-wish-text">
               <div class="pp-feedback-formular">
                 <div class="pp-feedback-chat">
                   <strong>
                     <?php printf( $texts['feedbackhint'] ) ?>
                   </strong>
                   <p><?php printf( $texts['feedbackhintmessage'] ); ?></p>
                 </div>
                 <textarea id="pp-feedback-wish-text" class="pp-feedback-textarea" placeholder="<?php printf( $texts['feedbackwish'] ); ?>"></textarea>
                 <br>
                 <div style="width:100%; position:relative;s">
                   <button id="pp-no-feedback-wish"type="button" class="pp-button-primary" style=""><?php printf( $texts['feedbackquit'] ); ?></button>
                   <button id="pp-send-feedback-wish" type="button" class="pp-button-primary" style="position:absolute; right:0px;"><?php printf( $texts['feedbacksend'] ); ?></button>
                 </div>
               </div>
             </div>
            </div>
            <div class="pp-box">
              <h2 class="pp-box-header">
                <?php if( !empty( $top['polls'] ) ) { ?>
                  <span><?php printf( $texts['top'] ); ?></span>
                  <select id="pp-select-box">
                  </select>
                <?php } else { ?>
                  <span><?php printf( $texts['topnodots'] ); ?></span>
                <?php } ?>
                <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
              </h2>
                <div class="main">
                  <?php
                  if( !empty( $top['polls'] ) ) {
                  ?>
                    <canvas id="pp-top-five" width="500px" height="300px"></canvas>
                  <?php
                  } else {
                  ?>
                    <div class="pp-alert">
                      <div class="pp-dashboard-alert">
                        <?php printf( $texts['nodatacollect'] ); ?><a href="admin.php?page=createpoll"><?php printf( $texts['nodatacollectlink'] ); ?></a>
                      </div>
                    </div>
                  <?php
                  }
                  ?>
                </div>
            </div>
          </div>
      </div>
      <?php printf(pinpoll_include_script('Chart.min.js')); ?>
      <script type="text/javascript">
        var ppJwt = '<?php printf( get_option('pinpoll_jwt') ); ?>';
        var ppShowFeedback = '<?php printf( $this->pinpoll_show_feedback() ) ?>';
        var ppFileUrl = '<?php  printf( plugins_url( 'pinpoll-store-js.php', __FILE__ ) ) ?>';
        var ppStatsBaseURL = '<?php printf( PINPOLL_STATS_BASE_URL ); ?>';
        var ppFeedbackBaseURL = '<?php printf( PINPOLL_FEEDBACK_BASE_URL ); ?>';
        var ppReviewText = '<?php printf( $texts['review'] ); ?>';
      </script>
    <?php
      printf(pinpoll_include_script('pinpoll_dashboard.js'));

  }

  /**
   * Helper Method Feedback
   * Description: Determines if feedback is allowed to show or not (Poll Created > 1 || elapsed time > 30 days)
   *
   * @return string show or hide
   */
  function pinpoll_show_feedback() {

    $ppFeedback = get_option('pinpoll_feedback');
    if(!empty($ppFeedback['date'])) {
      $now = date('Y-m-d H:i:s');
      $datediffSecounds = strtotime( $now ) - strtotime( $ppFeedback['date'] );
      $datediffDays = ( ( floatval( $datediffSecounds ) / 60 ) / 60 ) / 24;
    }
    if( ( !empty( $ppFeedback['date'] ) && ( round( $datediffDays, 0 ) > 30 ) || $ppFeedback['pollCreated'] == 'true' ) ) {
      return 'show';
    } else {
      return 'hide';
    }
  }

}

$PinpollDashboard = new PinpollDashboard();
?>
