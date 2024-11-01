<?php
/**
 * WP Widget Styler
 *
 * @since             1.0.0
 * @package           WP_Widget_Styler
 * @author            WebEmpire <webempire143@gmail.com>
 * @link              https://webempire.org.in/
 *
 * @wordpress-plugin
 * Plugin Name:       WP Widget Styler
 * Plugin URI:        https://wordpress.org/plugins/wp-widget-styler/
 * Description:       Power-up your WordPress widgets using these awesome styler configurations.
 * Version:           1.0.0
 * Author:            WebEmpire
 * Author URI:        https://webempire.org.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-widget-styler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently active plugin file.
 */
define( 'WP_WIDGET_STYLER_FILE', __FILE__ );

/**
 * The core plugin class that is used to define admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-wp-widget-styler-loader.php';
