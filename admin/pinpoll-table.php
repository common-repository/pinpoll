<?php
//Security
if ( ! defined( 'ABSPATH' ) ) { exit(); }

/**
 * Check if WP_List_Table class already exists
 */
if( ! class_exists( 'WP_List_Table' ) ) {
  require_once('includes/class-wp-list-table.php');
}

//INCLUDES category, config, api, texts
require_once( 'resources/pinpoll-category.php' );
require_once( 'config/pinpoll-config.php' );
require_once( 'functions/pinpoll-functions.php' );
require_once( 'api/pinpoll-api.php' );
require_once( 'resources/pinpoll-texts.php' );

/**
 * Classname:   PinpollTable
 * Description: TableView for all user polls which extends from WP_List_Table
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin
 *
 */
class PinpollTable extends WP_List_Table
{
  private $pollData;
  private $categories;
  private $selectedCategory;
  private $categoryLabels;
  private $ppApi;
  private $texts;

  /**
   * Construct which call super construct
   */
  public function __construct() {

    parent::__construct(
      array(
        'singular' => 'poll',
        'plural' => 'polls',
      )
    );
  }

  /**
   * Empty Message
   * Description: Override default message if no items found
   */
  public function no_items() {
    _e('No polls found.', 'pinpoll');
  }

  /**
   * Bulk Actions
   * Description: Define bulk actions for dropdown in page "Polls"
   * @return array $actions bulk actions
   */
  function get_bulk_actions() {
    $actions = array(
      'deleteBulk' => __( 'Delete', 'pinpoll' )
    );

    return $actions;
  }

  /**
   * Column CB
   * Description: Edit column 'cb' to show checkboxes in each row
   *
   * @param   object $item rowitem
   * @return  html checkbox
   */
  function column_cb( $item ) {
    return sprintf( '<input type="checkbox" class="pp-polls-cb" name="polls[]" value="%s" />' , $item['ID'] );
  }

  /**
   * Prepare Table
   * Description: Prepares table and init all elements that are needed like:
   *              - set sortable columns
   *              - enable pagnition
   *              - ...
   */
  public function prepare_items() {

    $this->ppApi = new PinpollApi();
    $this->texts = pinpoll_get_table_texts();

    //Get selected category
    if( empty( $_REQUEST['hiddenSelectedCat'] ) || $_REQUEST['hiddenSelectedCat'] == 'Category' ) {
      $this->selectedCategory = '';
    } else {
      $this->selectedCategory = $_REQUEST['hiddenSelectedCat'];
    }

    $this->categoryLabels = pinpoll_get_category_labels();

    $this->check_redirect();

    $this->check_for_bulk_actions();

    $this->pollData = $this->pinpoll_get_polls_from_api();

    $feedback = get_option('pinpoll_feedback');

    $totalItems = empty($this->pollData['body']['recordsFiltered']) ? 0 : $this->pollData['body']['recordsFiltered'];

    $data = $this->pinpoll_prepare_table_data( $this->pollData['body'] );

    //Check if there is one or more than one poll in table
    if( $feedback['pollCreated'] != 'inactive' ) {
      $feedback['pollCreated'] = $totalItems >= 1 ? 'true' : 'false';
      update_option('pinpoll_feedback', $feedback);
    }

    //Define rows per page
    $perPage = POLLS_PER_PAGE;

    $this->set_pagination_args( array (
      'total_items' => $totalItems,
      'per_page' => $perPage,
    ) );

    if( !empty( $_POST['s'] ) || !empty( $this->selectedCategory ) ) {
      $this->set_pagination_args( array (
        'total_items' => $totalItems,
        'per_page' => $perPage,
      ) );
    }

    $this->categories = $this->pinpoll_init_categories();

    $columns = $this->get_columns();
    $hidden = $this->get_hidden_columns();
    $sortable = $this->get_sortable_columns();

    //init column headers
    $this->_column_headers = array($columns, $hidden, $sortable);
    //init table data
    $this->items = $data;
  }

  /**
   * Check Redirect
   * Description: Check if user clicked edit in menu without selecting a poll
   *              in table and print message
   */
  function check_redirect() {
    if( isset( $_GET['redirectEdit'] ) ) {
      printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['selectpoll'], 'pinpoll' ) );
    }
  }

  /**
   * Check for Bulk Actions
   * Description: Proof if there is any delete bulk action posted.
   *              Call api to delete poll with id
   */
  function check_for_bulk_actions() {

    //delete via row action
    if('delete' === $this->current_action()) {
      $wpnonce = esc_attr( $_REQUEST['wpnonce'] );

      if( !wp_verify_nonce( $wpnonce, 'delete_poll') ) {
        die( $this->texts['die'] );
      } else {

        $pollId = esc_attr( $_GET['poll'] );

        $responseBody = $this->ppApi->pinpoll_delete( $pollId );
        $error = isset( $responseBody['error'] ) ? $responseBody['error'] : '';

        if( empty( $error ) ) {
          printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s <b>' . $pollId . '</b> %s </p> </div></div>', esc_html__( $this->texts['deleteonea'], 'pinpoll' ), esc_html__( $this->texts['deleteoneb'], 'pinpoll' )  );
        } else {
          printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support%20Request&Body=' . $error . '">support@pinpoll.com</a>' );
        }

      }
    }

    //delete via checkbox
    if( ( isset( $_POST['action'] ) && $_POST['action'] == 'deleteBulk' )
      || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'deleteBulk' ) ) {
        $pollIds = $_POST['polls'];
        $isError = false;

        foreach ($pollIds as $id) {
          $responseBody = $this->ppApi->pinpoll_delete( $id );
          $error = isset( $responseBody['error'] ) ? $responseBody['error'] : '';

          if( !empty( $error ) ) {
            $isError = true;
          }
        }

        if( $isError ) {
          printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['error'], 'pinpoll' ) . '<a href="mailto:support@pinpoll.com?Subject=Wordpress%20Support">support@pinpoll.com</a>' );
        }
        else {
          if( count( $pollIds ) > 1) {
            printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s </p> </div></div>', esc_html__( $this->texts['deletemultiple'], 'pinpoll' )  );
          } else {
            printf( '<div class="wrap"><div class="updated notice notice-success is-dismissible"> <p> %s <b>' . $pollIds[0] . '</b> %s </p> </div></div>', esc_html__( $this->texts['deleteonea'], 'pinpoll' ), esc_html__( $this->texts['deleteoneb'], 'pinpoll' )  );
          }
        }
    }
  }

  /**
   * Get Columns
   * Description: Define columns in table
   *
   * @return array $columns colums of table
   */
  public function get_columns() {

    $columns = array(
      'cb' => '',
      'ID' => __( 'ID', 'pinpoll' ),
      'question' => __( 'Question', 'pinpoll' ),
      'votes' => __( 'Votes', 'pinpoll' ),
      'category' => $this->categories,
      'created' => __( 'Created', 'pinpoll' ),
      'embed' => __( 'Shortcode', 'pinpoll' ),
      'active' => __( 'Active', 'pinpoll' )
    );

    return $columns;
  }

  /**
   * Initiate Category Dropdown
   * Description: Create a HTML dropdown element which includes all categories
   *
   * @return array $categories html dropdown
   */
  function pinpoll_init_categories() {

    $responseData = $this->ppApi->pinpoll_categories();

    if(!array_key_exists('error', $responseData)) {
      $this->categories .= '<select id="pp-select-category" name="pp-selected" onchange="pinpoll_select_value_change(this)" style="width:100%">';
      $this->categories .= '<option value="Category">' . $this->texts['category'] . '</option>';

      for ( $i = 0; $i < count( $responseData ); $i++ ) {
        $this->categories .= '<option value="' . $responseData[$i]['shortcut'] . '">' . $this->categoryLabels[$responseData[$i]['shortcut']] . '</option>';
      }

      $this->categories .= '</select>';

      return $this->categories;
    }
  }

  /**
   * Column Question
   * Description: Modify column 'question', adds row actions 'edit' and 'delete'
   *
   * @param  int $item ID
   * @return html rowactions
   */
  function column_question( $item ) {

    $nonce = wp_create_nonce('delete_poll');
    $nonceDetail = wp_create_nonce('poll_detail');

    $actions = array(
      'details' => sprintf( '<a href="?page=%s&action=%s&poll=%s&wpnonceDetail=%s">' . $this->texts['bulkdetails'] . '</a>', 'allpolls', 'details', $item['ID'], $nonceDetail ),
      'delete' => sprintf( '<a href="?page=%s&action=%s&poll=%s&wpnonce=%s" onClick="return confirmDelete()">' . $this->texts['bulkdelete'] . '</a>', $_REQUEST['page'], 'delete', $item['ID'], $nonce )
    );

    return sprintf( '%1$s %2$s', $item['question'], $this->row_actions($actions) );
  }

  /**
   * Hidden Columns
   * Description: Define hidden columns
   *
   * @return array empty
   */
  public function get_hidden_columns() {
    return array();
  }

  /**
   * Sortable Columns
   * Description: Define sortable columns
   *
   * @return type description
   */
  public function get_sortable_columns() {
    $sortable_columns = array(
      'question' => array( 'question', false ),
      'votes' => array( 'votes', false ),
      'created' => array( 'created', false ),
      'active' => array( 'active', false )
    );

    return $sortable_columns;
  }

  /**
   * Polls from Api
   * Description: Receiving polls from api
   *
   * @return array polls
   */
  public function pinpoll_get_polls_from_api() {
    $paged =  1;
    $start = 0;
    $length = POLLS_PER_PAGE;
    $search = '';
    if( !empty( $_POST['s'] ) || !empty( $this->selectedCategory ) ) {
      $search = $_POST['s'];
    } else {
      $paged = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
      $start = (POLLS_PER_PAGE * $paged) - POLLS_PER_PAGE;
      $length = POLLS_PER_PAGE;
    }

    //collect order params
    $orderby = empty( $_GET['orderby'] ) ? 'created' : $_GET['orderby'];
    $order = empty( $_GET['order'] ) ? 'desc' : $_GET['order'];

    $searchBody = array(
      'start' => $start,
      'length' => $length,
      'columns[0][data]' => 'id',
      'columns[0][name]' => 'id',
      'columns[0][searchable]' => 'true',
      'columns[0][orderable]' => 'true',
      'columns[0][search][value]' => '',
      'columns[0][search][regex]' => 'false',
      'columns[1][data]' => 'question',
      'columns[1][name]' => 'question',
      'columns[1][searchable]' => 'true',
      'columns[1][orderable]' => 'true',
      'columns[1][search][value]' => '',
      'columns[1][search][regex]' => 'false',
      'columns[2][data]' => 'votes',
      'columns[2][name]' => 'votes',
      'columns[2][searchable]' => 'true',
      'columns[2][orderable]' => 'true',
      'columns[2][search][value]' => '',
      'columns[2][search][regex]' => 'false',
      'columns[3][data]' => 'category',
      'columns[3][name]' => 'category.shortcut',
      'columns[3][searchable]' => 'true',
      'columns[3][orderable]' => 'true',
      'columns[3][search][value]' => $this->selectedCategory,
      'columns[3][search][regex]' => 'false',
      'columns[4][data]' => 'created_at',
      'columns[4][name]' => 'poll.created_at',
      'columns[4][searchable]' => 'true',
      'columns[4][orderable]' => 'true',
      'columns[4][search][value]' => '',
      'columns[4][search][regex]' => 'false',
      'columns[5][data]' => 'active',
      'columns[5][name]' => 'poll.active',
      'columns[5][searchable]' => 'true',
      'columns[5][orderable]' => 'true',
      'columns[5][search][value]' => '',
      'columns[5][search][regex]' => 'false',
      'order[0][column]' => $this->pinpoll_get_order_column( $orderby ),
      'order[0][dir]' => $order,
      'search[value]' => $search
    );

    return $this->ppApi->pinpoll_datatables( $searchBody );
  }

  /**
   * Prepare Table Data
   * Description: Preparing data received from api for table
   * @param  array $responseData response body
   * @return array               table data
   */
  function pinpoll_prepare_table_data( $responseData ) {

    $data = array();

    if(!array_key_exists('error', $responseData)) {

      $detailPollNonce = wp_create_nonce('pinpoll_details_question');

      foreach ($responseData['data'] as $p) {
        $embed = '[pinpoll id="' . $p['id'] . '"]';
        $checked = $p['active'] == '1' ? 'checked' : '';
        $data[] = array(
          'ID' => $p['id'],
          'question' => sprintf( '<a href="?page=%s&action=%s&poll=%s&wpnonceDetail=%s">%s</a>', 'allpolls', 'details', $p['id'], $detailPollNonce, $p['question'] ),
          'votes' => $p['votes'],
          'category' => $this->categoryLabels[$p['category']],
          'created' => $p['created_at'],
          'embed' => sprintf( '<input type="text" value="%s" onClick="this.setSelectionRange(0, this.value.length)" readonly="true" style="%s"/>', esc_html__( $embed ), esc_html__( 'width:100%;' ) ),
          'active' => sprintf( '<label class="pp-switch-active"><input type="checkbox" name="checkboxPPActive" value="%s" %s><div class="pp-slider-active round"></div></label>', esc_html__( $p['id'] ), esc_html__( $checked ) )
        );
      }
    }
    return $data;
  }

  /**
   * Helper Method Order Column
   * Description: Column index which identifies the column in api call
   *
   * @param  string $index column name
   * @return int           column index
   */
  function pinpoll_get_order_column( $index ) {
    $orderColumns = array(
      'ID' => '0',
      'question' => '1',
      'votes' => '2',
      'created' => '4',
      'active' => '5'
    );

    return $orderColumns[$index];
  }

  /**
   * Default Columns
   * Description: Default columns that are shown in table
   *
   * @param  object   $item         row
   * @param  string   $column_name  column name
   * @return array                  column
   */
  public function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'ID':
      case 'question':
      case 'votes':
      case 'category':
      case 'created':
      case 'embed':
      case 'active':
        return $item[ $column_name ];
      default:
        return print_r( $item, true );
    }
  }
}
?>
