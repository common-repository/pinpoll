<?php
/*
 * Pinpoll Plugin File
 *
 * @link https://pinpoll.com
 * @since 3.0.0
 * @package Pinpoll
 *
 * Plugin Name: Pinpoll
 * Plugin URI: https://pinpoll.com
 * Text Domain: pinpoll
 * Domain Path: /lang
 * Description: Create fun polls & understand your audience!
 * Version: 4.0.0
 * Min WP Version: 3.3.0
 * Author: Pinpoll
 * Author URI: https://pinpoll.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

 //Security
 if (! defined('ABSPATH')) {
     exit();
 }

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

//INCLUDES functions
require_once('admin/functions/pinpoll-functions.php');
//INCLUDES config, texts
require_once('admin/config/pinpoll-config.php');


/**
 * Classname: Pinpoll
 * Description: Initiate the plugin, with all configurations
 *
 * @package Pinpoll
 *
 */
class Pinpoll
{

    /**
     * construct where admin hooks are performed
     * which register settings, call menu structure, ...
     */
    public function __construct()
    {
        //hooks while loading plugin
        add_action('admin_init', array( $this, 'check_version' ));
        add_shortcode('pinpoll', array( $this, 'pinpoll_shortcode_handler' ));
        // Don't run anything else in the plugin, if we're on an incompatible WordPress version
        if (! self::compatible_version()) {
            return;
        }
        add_action('init', 'pinpoll_add_oembed');
        register_deactivation_hook(__FILE__, array(
            $this,
            'pinpoll_deactivate'
        ));
    }

    /**
     * Deactivation Hook
     **/
    function pinpoll_deactivate()
    {
        remove_action('init', 'pinpoll_add_oembed');
        pinpoll_remove_oembed();
    }

    // The backup sanity check, in case the plugin is activated in a weird way,
    // or the versions change after activation.
    public function check_version()
    {
        if (! self::compatible_version()) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(plugin_basename(__FILE__));

                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    public static function compatible_version()
    {
        if (version_compare(PHP_VERSION, '5.5', '<')) {
            return false;
        }
        if (version_compare($GLOBALS['wp_version'], '3.3', '<')) {
            return false;
        }
        return true;
    }

    /**
     * Shortcode Handling
     * Description: Handle shortcode [pinpoll] in posts and pages.
     *              Html code will be generated instead of the tag.
     *
     * @param  array $atts attributes of tag pinpoll
     * @return html        poll
     */
    public function pinpoll_shortcode_handler($atts, $content = null)
    {
        if (!empty($atts)) {
            $a = shortcode_atts(
            array(
            'id' => '404'
          ),
            $atts
        );
            $id = $a['id'];
        } elseif (!empty($content)) {
            $matches = array();
            preg_match('/\/embed\/(\d+)/', $content, $matches);
            $id = $matches[1];
        }
        return wp_oembed_get( PINPOLL_URL.'/embed/'.$id);
    }

}

$Pinpoll = new Pinpoll();
?>
