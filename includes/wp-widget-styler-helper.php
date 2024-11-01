<?php
/**
 * Advaned WP Widget Styler.
 *
 * @package WP_Widget_Styler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WP_Widget_Styler_Helper.
 *
 * @since 1.0.0
 */
class WP_Widget_Styler_Helper {

	/**
	 * Addons Options
	 *
	 * @var addon_options
	 */
	private static $addon_options = null;

	/**
	 * Addon List
	 *
	 * @var addons_list
	 */
	private static $addons_list = null;

	/**
	 * Provide General settings array().
	 *
	 * @return array
	 * @since 1.0.0
	 */
	static public function get_addons_list() {

		self::$addons_list = WP_Widget_Styler_Addons::get_addons_list();

		return apply_filters( 'wp_widget_styler_addons_list', self::$addons_list );
	}

	/**
	 * Returns an option from the database for the admin settings page.
	 *
	 * @param  string  $key               The option key.
	 * @param  mixed   $default           Option default value if option is not available.
	 * @param  boolean $network_override  Whether to allow the network admin setting to be overridden on subsites.
	 * @return string                     Return the option value
	 *
	 * @since 1.0.0
	 */
	public static function get_admin_settings_option( $key, $default = false, $network_override = false ) {

		// Get the site-wide option if we're in the network admin.
		if ( $network_override && is_multisite() ) {
			$value = get_site_option( $key, $default );
		} else {
			$value = get_option( $key, $default );
		}

		return $value;
	}

	/**
	 * Updates an option from the admin settings page.
	 *
	 * @param string $key       The option key.
	 * @param mixed  $value     The value to update.
	 * @param bool   $network   Whether to allow the network admin setting to be overridden on subsites.
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	static public function update_admin_settings_option( $key, $value, $network = false ) {

		// Update the site-wide option since we're in the network admin.
		if ( $network && is_multisite() ) {
			update_site_option( $key, $value );
		} else {
			update_option( $key, $value );
		}
	}

	/**
	 * Provide Widget settings.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	static public function get_addon_options() {

		if ( null === self::$addon_options ) {

			$addons        = self::get_addons_list();
			$saved_widgets = self::get_admin_settings_option( 'wp_widget_styler_addons' );

			if ( is_array( $addons ) ) {

				foreach ( $addons as $slug => $data ) {

					if ( isset( $saved_widgets[ $slug ] ) ) {

						if ( 'disabled' === $saved_widgets[ $slug ] ) {
							$addons[ $slug ]['is_activate'] = false;
						} else {
							$addons[ $slug ]['is_activate'] = true;
						}
					} else {
						$addons[ $slug ]['is_activate'] = ( isset( $data['default'] ) ) ? $data['default'] : false;
					}
				}
			}

			self::$addon_options = $addons;
		}

		return apply_filters( 'wp_widget_styler_active_widgets', self::$addon_options );
	}

	/**
	 * Widget Active.
	 *
	 * @param string $slug Module slug.
	 * @return string
	 *
	 * @since 1.0.0
	 */
	static public function is_addon_active( $slug = '' ) {

		$addons      = self::get_addon_options();
		$is_activate = false;

		if ( isset( $addons[ $slug ] ) ) {
			$is_activate = $addons[ $slug ]['is_activate'];
		}

		return $is_activate;
	}
}
