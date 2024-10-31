<?php
/**
 * Uinstall File
 *
 * Description: File which will automaticall called by wordpress if the plugin will be uinstalled
 *
 * @package Pinpoll
 *
 */

 //Security
 if ( ! defined( 'ABSPATH' ) ) { exit(); }

//exit if uninstall not called from wordpress
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//delete options in wp_options
delete_option('pinpoll_plugin_on_activation');
delete_option('pinpoll_account');
delete_option('pinpoll_jwt');
delete_option('pinpoll_feedback');

?>
