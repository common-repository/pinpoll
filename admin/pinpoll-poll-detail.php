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
 * Classname:   PinpollDetail
 * Description: Contains page "Details" of a certain poll, which the user
 *              choose from table of page "Polls".
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollDetail
{

  private $ppApi;

  //Default construct
  function __construct()
  {
    $this->ppApi = new PinpollApi();
    $this->pinpoll_details_page();
  }

  /**
   * Details Page
   * Description: Contains the whole view for detailed information of the
   *              choosen poll. So there is a preview/edit box, a stats box,
   *              a box with the current result (chart) and boxes with votes
   *              per city and per country.
   */
  function pinpoll_details_page() {

    $wpnonce = esc_attr( $_REQUEST['wpnonceDetail'] );

    if( !wp_verify_nonce( $wpnonce, 'poll_detail' ) && !wp_verify_nonce( $wpnonce, 'pinpoll_details_question' ) ) {
      die( 'You are not allowed to edit this poll!' );
    } else {
      $id = $_GET['poll'];
      $countryCodes = pinpoll_get_country_codes();
      $texts = pinpoll_get_polldetail_texts();

      $data = $this->ppApi->pinpoll_polls($id);
      $jwt = get_option( 'pinpoll_jwt' );

      $moreDataUrl = PINPOLL_COCKPIT_BASE_URL . '/polls/' . $id . '?token=' . $jwt;
      $cockpitUrl = PINPOLL_COCKPIT_BASE_URL . '?token=' . $jwt;
      $facebookshareUrl = 'https://www.facebook.com/v2.0/dialog/share?'
                        . 'app_id=529673120377726&display=popup'
                        . '&href=' . PINPOLL_EMBED_IFRAME . '/' . $id;

      //CALCULATE STATS

      if( $data['body']['stats']['lifetime']['views_unique'] > 0 ) {
        $interactionLifetime = ( $data['body']['stats']['lifetime']['votes'] / $data['body']['stats']['lifetime']['views_unique'] ) * 100;
      } else { $interactionLifetime = 0; }

      if( $data['body']['stats']['range']['views_unique'] > 0 ) {
        $interactionRange = ( $data['body']['stats']['range']['votes'] / $data['body']['stats']['range']['views_unique'] ) * 100;
      } else { $interactionRange = 0; }

      if( $data['body']['stats']['lifetime']['votes_unique'] > 0 ) {
        $votesPerParticipantLifetime = ( $data['body']['stats']['lifetime']['votes'] / $data['body']['stats']['lifetime']['votes_unique'] );
      } else { $votesPerParticipantLifetime = 0; }

      if( $data['body']['stats']['range']['votes_unique'] > 0 ) {
        $votesPerParticipantRange = ( $data['body']['stats']['range']['votes'] / $data['body']['stats']['range']['votes_unique'] );
      } else { $votesPerParticipantRange = 0; }

      if( $data['body']['stats']['lifetime']['votes'] > 0 ) {
        $clickRateVotesLifetime = ( $data['body']['stats']['lifetime']['recommendation_clicks_after_vote'] / $data['body']['stats']['lifetime']['votes'] ) * 100;
      } else { $clickRateVotesLifetime = 0; }

      if( $data['body']['stats']['lifetime']['views'] > 0 ) {
        $clickRateViewsLifetime = ( $data['body']['stats']['lifetime']['recommendation_clicks'] / $data['body']['stats']['lifetime']['views'] ) * 100;
      } else { $clickRateViewsLifetime = 0; }

      if( $data['body']['stats']['range']['votes'] > 0 ) {
        $clickRateVotesRange = ( $data['body']['stats']['range']['recommendation_clicks_after_vote'] / $data['body']['stats']['range']['votes'] ) * 100;
      } else { $clickRateVotesRange = 0; }

      if( $data['body']['stats']['range']['views'] > 0 ) {
        $clickRateViewsRange = ( $data['body']['stats']['range']['recommendation_clicks'] / $data['body']['stats']['range']['views'] ) * 100;
      } else { $clickRateViewsRange = 0; }

      date_default_timezone_set( empty( get_option( 'timezone_string' ) ) ? 'Europe/Vienna' : get_option( 'timezone_string' ) );
      $currentDay = date( 'd' );
      $currentMonth = date( 'm' );
      $currentYear = date( 'Y' );
      $currentHour = date( 'H' );
      $currentMinute = date( 'i' );
      $plusDays = date( 'd', strtotime( '+7 days' ) );
      $plusMonth = date( 'm', strtotime( '+7 days' ) );

      ?>
        <div class="wrap">
          <div class="pp-header-line">
            <h3>
              <i class="fa fa-lg fa-bar-chart"></i> <?php printf( $data['body']['question'] ) ?>
               <?php if($data['body']['stats']['embeds'] &&  count ($data['body']['stats']['embeds']) > 0) { ?>
                <a href="<?php printf( $data['body']['stats']['embeds'][0]['url'] ); ?>" class="pp-link-symbol" target="_blank"><i class="fa fa-lg fa-external-link"></i></a>
              <?php } ?>
              <a href="<?php printf( $cockpitUrl ); ?>" target="_blank"><img src="<?php printf( plugins_url( '/images/pinpoll_header_black.png', __FILE__ ) ); ?>" class="pp-header-image"></img></a>
            </h3>
          </div>
            <div id="pp-details-left" class="pp-container">
              <div id="pp-timeout" class="pp-animated pp-fade-in-left">
                <div id="pp-poll-active" class="pp-timeout-box pp-active">
                  <i class="fa fa-info-circle"></i>
                   <?php printf( $texts['pollactive']); ?>
                   <div class="pp-buttons">
                     <button id="pp-deactivate-poll" type="button" class="pp-button-warning">
                       <i class="fa fa-pause"></i>
                       <?php printf( $texts['deactivate'] ); ?>
                     </button>
                     <button id="pp-open-timeout" type="button" class="pp-button-primary">
                       <i class="fa fa-clock-o"></i>
                       <?php printf( $texts['timeoutset'] ); ?>
                     </button>
                   </div>
                </div>
                <div id="pp-input-timeout-container" class="pp-input-timeout-container pp-animated pp-fade-in-top">
                  <div style="margin-bottom:20px;">
                    <strong><label for="pp-from-month" style="margin-right:20px;"><?php printf( $texts['from'] ); ?></label></strong>
                    <select id="pp-from-month" style="height: 26px;">
                      <option value="01" data-text="Jan"><?php printf( $texts['months']['jan'] ); ?></option>
                      <option value="02" data-text="Jan"><?php printf( $texts['months']['feb'] ); ?></option>
                      <option value="03" data-text="Jan"><?php printf( $texts['months']['mar'] ); ?></option>
                      <option value="04" data-text="Jan"><?php printf( $texts['months']['apr'] ); ?></option>
                      <option value="05" data-text="Jan"><?php printf( $texts['months']['may'] ); ?></option>
                      <option value="06" data-text="Jan"><?php printf( $texts['months']['jun'] ); ?></option>
                      <option value="07" data-text="Jan"><?php printf( $texts['months']['jul'] ); ?></option>
                      <option value="08" data-text="Jan"><?php printf( $texts['months']['aug'] ); ?></option>
                      <option value="09" data-text="Jan"><?php printf( $texts['months']['sep'] ); ?></option>
                      <option value="10" data-text="Jan"><?php printf( $texts['months']['oct'] ); ?></option>
                      <option value="11" data-text="Jan"><?php printf( $texts['months']['nov'] ); ?></option>
                      <option value="12" data-text="Jan"><?php printf( $texts['months']['dec'] ); ?></option>
                    </select>
                    <input type="text" id="pp-from-day" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $currentDay ); ?>"></input>
                    <strong>,</strong>
                    <input type="text" id="pp-from-year" class="pp-date-field" pattern="\d*" maxlength="4" autocomplete="off" style="width:3.4em;" value="<?php printf( $currentYear ); ?>"></input>
                    <strong>@</strong>
                    <input type="text" id="pp-from-hour" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $currentHour ); ?>"></input>
                    <strong>:</strong>
                    <input type="text" id="pp-from-minute" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $currentMinute ); ?>"></input>
                  </div>
                  <div>
                  <strong><label for="pp-to-month" style="margin-right: 37px;"><?php printf( $texts['to'] ); ?></label></strong>
                    <select id="pp-to-month">
                      <option value="01" data-text="Jan"><?php printf( $texts['months']['jan'] ); ?></option>
                      <option value="02" data-text="Jan"><?php printf( $texts['months']['feb'] ); ?></option>
                      <option value="03" data-text="Jan"><?php printf( $texts['months']['mar'] ); ?></option>
                      <option value="04" data-text="Jan"><?php printf( $texts['months']['apr'] ); ?></option>
                      <option value="05" data-text="Jan"><?php printf( $texts['months']['may'] ); ?></option>
                      <option value="06" data-text="Jan"><?php printf( $texts['months']['jun'] ); ?></option>
                      <option value="07" data-text="Jan"><?php printf( $texts['months']['jul'] ); ?></option>
                      <option value="08" data-text="Jan"><?php printf( $texts['months']['aug'] ); ?></option>
                      <option value="09" data-text="Jan"><?php printf( $texts['months']['sep'] ); ?></option>
                      <option value="10" data-text="Jan"><?php printf( $texts['months']['oct'] ); ?></option>
                      <option value="11" data-text="Jan"><?php printf( $texts['months']['nov'] ); ?></option>
                      <option value="12" data-text="Jan"><?php printf( $texts['months']['dec'] ); ?></option>
                    </select>
                    <input type="text" id="pp-to-day" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $plusDays ); ?>"></input>
                    <strong>,</strong>
                    <input type="text" id="pp-to-year" class="pp-date-field" pattern="\d*" maxlength="4" autocomplete="off" style="width:3.4em;" value="<?php printf( $currentYear ); ?>"></input>
                    <strong>@</strong>
                    <input type="text" id="pp-to-hour" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $currentHour ); ?>"></input>
                    <strong>:</strong>
                    <input type="text" id="pp-to-minute" class="pp-date-field" pattern="\d*" maxlength="2" autocomplete="off" value="<?php printf( $currentMinute ); ?>"></input>
                  </div>
                  <div class="pp-text-right">
                    <button type="button" id="pp-button-set-timeout" class="pp-button-primary">
                      <?php printf( $texts['timeoutsetbtn'] ); ?>
                    </button>
                    <button type="button" id="pp-button-cancel-timeout-form" class="pp-button-warning">
                      <?php printf( $texts['cancel'] ); ?>
                    </button>
                  </div>
                </div>
                <div id="pp-timeout-info" class="pp-timeout-box pp-info">
                  <i class="fa fa-info-circle"></i>
                  <label id="pp-timeout-text" style="vertical-align:top; cursor:default;">
                  </label>
                  <div class="pp-buttons">
                    <button id="pp-deactivate-timeout" type="button" class="pp-button-warning">
                      <i class="fa fa-times"></i>
                      <?php printf( $texts['timeoutcancel'] ); ?>
                    </button>
                  </div>
                </div>
                <div id="pp-poll-inactive" class="pp-timeout-box pp-inactive">
                  <i class="fa fa-info-circle"></i>
                   <?php printf( $texts['pollinactive'] ); ?>
                   <div class="pp-buttons">
                     <button id="pp-activate-poll" type="button" class="pp-button-primary">
                       <i class="fa fa-play"></i>
                       <?php printf( $texts['activate'] ); ?>
                     </button>
                   </div>
                </div>
              </div>
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['preview'] ); ?></span>
                  <span id="pp-buttons-preview" class="pp-header-button-group">
                    <button id="pp-reset-action" class="pp-button-delete pp-button-small"><i class="fa fa-remove"></i> <?php printf( $texts['reset'] ); ?></button>
                    <button id="pp-delete-action" class="pp-button-delete pp-button-small"><i class="fa fa-trash-o"></i> <?php printf( $texts['delete'] ); ?></button>
                    <button id="pp-edit-action" class="pp-button-primary pp-button-small"><i class="fa fa-pencil"></i> <?php printf( $texts['edit'] ); ?></button>
                    <button id="pp-embed-action" class="pp-button-warning pp-button-small"><i class="fa fa-code"></i> <?php printf( $texts['embed'] ); ?></button>
                  </span>
                </h2>
                <div class="pp-details-inside">
                  <div data-pinpoll-id="<?php printf( $id ); ?>" <?php (PINPOLL_JS_URL!=='https://pinpoll.com') ? printf("data-location=\"". PINPOLL_JS_URL. "\"") : printf("") ?> data-editmode="on" data-cdn=""></div>
                </div>
              </div>
              <div class="pp-box">
                <h2 class="pp-box-header">
                  <span><?php printf( $texts['curres'] ); ?></span>
                  <div class="pp-dropdown">
                    <a id="pp-dropdown-menubtn" class="pp-dropdown-btn"><i class="fa fa-wrench" style="color:grey;"></i></a>
                    <div id="pp-dropdown-menu" class="pp-dropdown-content">
                      <a id="pp-btn-doughnut"><?php printf( $texts['pie'] ); ?></a>
                      <a id="pp-btn-bar"><?php printf( $texts['bar'] ); ?></a>
                      <a id="pp-btn-export-chart"><?php printf( $texts['download'] ); ?></a>
                    </div>
                  </div>
                </h2>
                <div class="pp-inside">
                  <canvas id="pp-current-result" width="500px" height="300px"></canvas>
                  <div id="pp-empty-result">
                    <center><h3 style="color:grey;"><?php printf( $texts['nodata'] ); ?></h3></center>
                    <div class="pp-panel pp-panel-warning">
                      <div class="pp-panel-heading">
                        <i class="fa fa-info-circle"></i>
                        <?php printf( $texts['colvotes'] ); ?>
                      </div>
                      <div class="pp-panel-body">
                        <?php printf( $texts['colvoteshint'] ); ?>
                        <ol>
                          <li><a id="pp-embed-link" class="pp-link-text"><?php printf( $texts['hintembed'] ); ?></a></li>
                          <li><a id="pp-share-facebook" href="<?php printf( $facebookshareUrl ); ?>" target="_blank" class="pp-link-text"><?php printf( $texts['hintfacebook'] ); ?></a></li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="pp-details-right" class="pp-container">
              <div class="pp-animated pp-fade-in-top">
              <div class="pp-box pp-stats">
                  <h2 class="pp-box-header">
                    <span><?php printf( $texts['stats'] ); ?></span>
                    <div class="pp-header-button-group">
                      <a href="<?php printf( $moreDataUrl ); ?>" target="blank" style="text-decoration:none"><?php printf( $texts['moredata'] ); ?></a>
                    </div>
                  </h2>
                  <div class="pp-details-inside">
                    <ul class="pp-stats-tabs">
                      <li id="pp-tab-votes" class="pp-tabs">
                        <a><?php printf( $texts['votes'] ); ?></a>
                      </li>
                      <?php if( $data['body']['embedsetting']['showRecommendations'] ) { ?>
                              <li id="pp-tab-recos">
                                <a><?php printf( $texts['reco'] ); ?></a>
                              </li>
                      <?php } ?>
                    </ul>
                    <div id="pp-stats-votes" class="pp-tabs-panel">
                      <table id="pp-stats-table-left" class="pp-stats-table">
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['views_unique'] ); ?></h1>
                              <small><?php printf( $texts['user'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['usertooltip'] ) ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['views'] ); ?></h1>
                              <small><?php printf( $texts['views'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['viewstooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <?php if( $data['body']['multiple_vote_interval'] !== null ) { ?>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['votes_unique'] ); ?></h1>
                              <small><?php printf( $texts['participant'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['participanttooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['votes'] ); ?></h1>
                              <small><?php printf( $texts['votes'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['votestooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $interactionLifetime, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['engagement'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['engagementtooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $votesPerParticipantLifetime, '2', '.', '' ) ); ?></h1>
                              <small><?php printf( $texts['votespart'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['votesparttooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <?php } else { ?>
                          <tr>
                            <td>
                              <div class="pp-tooltip">
                                <h1><?php printf( $data['body']['stats']['lifetime']['votes'] ); ?></h1>
                                <small><?php printf( $texts['votes'] ); ?></small>
                                <span class="pp-tooltip-text"><?php printf( $texts['votestooltip'] ); ?></span>
                              </div>
                            </td>
                            <td>
                              <div class="pp-tooltip">
                                <h1><?php printf( number_format( $interactionLifetime, '2', '.', '' ) ); ?>%</h1>
                                <small><?php printf( $texts['engagement'] ); ?></small>
                                <span class="pp-tooltip-text"><?php printf( $texts['engagementtooltip'] ); ?></span>
                              </div>
                            </td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td colspan="2">
                            <h3 style="color:#a7b1c2;"><i class="fa fa-clock-o"></i> <?php printf( $texts['lifetime'] ); ?></h3>
                          </td>
                        </tr>
                      </table>
                      <table id="pp-stats-table-right" class="pp-stats-table">
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['range']['views_unique'] ); ?></h1>
                              <small><?php printf( $texts['user'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['usertooltip'] ) ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['range']['views'] ); ?></h1>
                              <small><?php printf( $texts['views'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['viewstooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <?php if( $data['body']['multiple_vote_interval'] !== null ) { ?>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['votes_unique'] ); ?></h1>
                              <small><?php printf( $texts['participant'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['participantooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['range']['votes'] ); ?></h1>
                              <small><?php printf( $texts['votes'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['votestooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $interactionRange, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['engagement'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['engagementtooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $votesPerParticipantRange, '2', '.', '' ) ); ?></h1>
                              <small><?php printf( $texts['votespart'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['votesparttooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <?php } else { ?>
                          <tr>
                            <td>
                              <div class="pp-tooltip">
                                <h1><?php printf( $data['body']['stats']['range']['votes'] ); ?></h1>
                                <small><?php printf( $texts['votes'] ); ?></small>
                                <span class="pp-tooltip-text"><?php printf( $texts['votestooltip'] ); ?></span>
                              </div>
                            </td>
                            <td>
                              <div class="pp-tooltip">
                                <h1><?php printf( number_format( $interactionRange, '2', '.', '' ) ); ?>%</h1>
                                <small><?php printf( $texts['engagement'] ); ?></small>
                                <span class="pp-tooltip-text"><?php printf( $texts['engagementtooltip'] ); ?></span>
                              </div>
                            </td>
                          </tr>
                        <?php } ?>
                          <td colspan="2">
                            <h3 style="color:#a7b1c2;"><i class="fa fa-clock-o"></i> <?php printf( $texts['24hrs'] ); ?></h3>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div id="pp-stats-recos" class="pp-tabs-panel">
                      <table id="pp-stats-table-left" class="pp-stats-table">
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['recommendation_clicks_after_vote'] ); ?></h1>
                              <small><?php printf( $texts['clicksvote'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clicksvotetooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['lifetime']['recommendation_clicks'] ); ?></h1>
                              <small><?php printf( $texts['clickstotal'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickstotaltooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $clickRateVotesLifetime, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['clickratevotes'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickratevotestooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $clickRateViewsLifetime, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['clickrateviews'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickrateviewstooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            <h3 style="color:#a7b1c2;"><i class="fa fa-clock-o"></i> <?php printf( $texts['lifetime'] ); ?></h3>
                          </td>
                        </tr>
                      </table>
                      <table id="pp-stats-table-right" class="pp-stats-table">
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['range']['recommendation_clicks_after_vote'] ); ?></h1>
                              <small><?php printf( $texts['clicksvote'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clicksvotetooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( $data['body']['stats']['range']['recommendation_clicks'] ); ?></h1>
                              <small><?php printf( $texts['clickstotalreco'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickstotalrecotooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $clickRateVotesRange, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['clickratevotes'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickratevotestooltip'] ); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="pp-tooltip">
                              <h1><?php printf( number_format( $clickRateViewsRange, '2', '.', '' ) ); ?>%</h1>
                              <small><?php printf( $texts['clickrateviews'] ); ?></small>
                              <span class="pp-tooltip-text"><?php printf( $texts['clickrateviewstooltip'] ); ?></span>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            <h3 style="color:#a7b1c2;"><i class="fa fa-clock-o"></i> <?php printf( $texts['24hrs'] ); ?></h3>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </div>
              </div>
            </div>
              <div class="pp-container-right-a">
                <div class="pp-box">
                  <h2 class="pp-box-header">
                    <span><?php printf( $texts['votescountry'] ); ?></span>
                    <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
                  </h2>
                  <?php if( !empty( $data['body']['countries'] ) ) {
                          $count = 1;
                    ?>
                    <div class="pp-inside">
                      <table class="pp-dashboard-table">
                        <thead>
                          <th><?php printf( $texts['tableno'] ); ?></th>
                          <th><?php printf( $texts['country'] ); ?></th>
                          <th><?php printf( $texts['countryvotes'] ); ?>
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
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } else { ?>
                          <div class="pp-alert">
                            <div class="pp-dashboard-alert">
                              <?php printf( $texts['nodata'] ); ?>
                            </div>
                          </div>
                  <?php } ?>
                </div>
              </div>
              <div class="pp-container-right-b">
                <div class="pp-box">
                  <h2 class="pp-box-header">
                    <span><?php printf( $texts['votescity'] ); ?></span>
                    <span class="pp-widget-inline-info"> <?php printf( $texts['24hrs'] ); ?> </span>
                  </h2>
                  <?php if( !empty( $data['body']['cities'] ) ) {
                          $count = 1;
                    ?>
                    <div class="pp-inside">
                      <table class="pp-dashboard-table">
                        <thead>
                          <th><?php printf( $texts['tableno'] ); ?></th>
                          <th><?php printf( $texts['city'] ); ?></th>
                          <th><?php printf( $texts['cityvotes'] ); ?>
                        </thead>
                        <tbody>
                        <?php
                          foreach ( $data['body']['cities'] as $cities ) {
                            ?>
                            <tr>
                              <td><?php printf( $count++ ); ?></td>
                              <td><?php printf( $cities['location'] ); ?></td>
                              <td><?php printf( $cities['votes'] ); ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } else { ?>
                          <div class="pp-alert">
                            <div class="pp-dashboard-alert">
                              <?php printf( $texts['nodata'] ); ?>
                            </div>
                          </div>
                  <?php } ?>
                </div>
              </div>
            </div>
        </div>
        <?php printf(pinpoll_include_script('sweetalert2.min.js')); ?>
        <?php printf(pinpoll_include_script('Chart.min.js')); ?>
        <script type="text/javascript">
          var jwt = '<?php printf( get_option( 'pinpoll_jwt' ) ); ?>';
          var pollIDDetails = '<?php printf( $id ); ?>';
          var currentMonth = '<?php printf( $currentMonth ); ?>';
          var monthLater = '<?php printf( $plusMonth ); ?>';
          var ppDetailsTexts = {
            'timeoutMessageFirst' : '<?php printf( $texts['timeouta'] ); ?>',
            'timeoutMessageLast' : '<?php printf( $texts['timeoutb'] ); ?>',
            'unauth' : '<?php printf( $texts['unauth'] ); ?>',
            'swalresettitle' : '<?php printf( $texts['swalresettitle'] ); ?>',
            'swalresettext' : '<?php printf( $texts['swalresettext'] ); ?>',
            'swalconfirmreset' : '<?php printf( $texts['swalconfirmreset'] ); ?>',
            'swalresetedtitle' : '<?php printf( $texts['swalresetedtitle'] ); ?>',
            'swalresetedtext' : '<?php printf( $texts['swalresetedtext'] ); ?>',
            'swalerrortitle' : '<?php printf( $texts['swalerrortitle'] ); ?>',
            'swalerrortext' : '<?php printf( $texts['swalerrortext'] ) ?>',
            'swalconfirmdelete' : '<?php printf( $texts['swalconfirmdelete'] ); ?>',
            'swaldeletetitle' : '<?php printf( $texts['swaldeletetitle'] ); ?>',
            'swaldeletetext' : '<?php printf( $texts['swaldeletetext'] ); ?>',
            'swaldeletedtitle' : '<?php printf( $texts['swaldeletedtitle'] ) ?>',
            'swaldeletedtext' : '<?php printf( $texts['swaldeletedtext'] ) ?>',
            'swalembedtitle' : '<?php printf( $texts['swalembedtitle'] ) ?>',
            'swalembedtext' : '<?php printf( $texts['swalembedtext'] ) ?>',
            'swalembedtextWidget' : '<?php printf( $texts['swalembedtextWidget'] ) ?>'
          }
          var ppBaseURL = '<?php printf( PINPOLL_BASE_URL ); ?>';
          var ppJSURL = '<?php printf( PINPOLL_JS_URL ); ?>';
          var ppEmbedURL = '<?php printf( PINPOLL_EMBED_IFRAME ); ?>';
        </script>
      <?php
        printf(pinpoll_include_script('pinpoll_details.js'));
    }
  }
}

$PinpollDetail = new PinpollDetail();
?>
