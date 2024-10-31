<?php
/**
 * Global Functions File
 *
 * Description: Contains functions used throughout this plugin.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/config
 *
 */

 //INCLUDES config and texts
require_once untrailingslashit(__DIR__) . '/../config/pinpoll-config.php';

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
 function pinpoll_get_version() {
   $plugin_data = get_plugin_data(trailingslashit(dirname(dirname( dirname( __FILE__ )))) .'pinpoll.php', false, false);
   $plugin_version = $plugin_data["Version"];
   return $plugin_version;
 }

/**
 * Ads Pinpoll as oembed provider.
 *
 */
function pinpoll_add_oembed(){
  wp_oembed_add_provider(PINPOLL_URL.'/*', PINPOLL_URL.'/oembed');
}

/**
 * Removed Pinpoll as oembed provider.
 *
 */
function pinpoll_remove_oembed(){
  wp_oembed_remove_provider(PINPOLL_URL.'/*');
}

?>
