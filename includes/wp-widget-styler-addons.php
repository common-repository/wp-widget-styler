<?php
/**
 * Powerful VC Config.
 *
 * @package WP_Widget_Styler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WP_Widget_Styler_Addons.
 */
class WP_Widget_Styler_Addons {

	/**
	 * Widget List
	 *
	 * @var widget_list
	 */
	public static $addons_list = null;

	/**
	 * Get Widget List.
	 *
	 * @since 1.0.0
	 *
	 * @return array The Widget List.
	 */
	public static function get_addons_list() {
		if ( null === self::$addons_list ) {
			self::$addons_list = apply_filters(
				'wpws_addon_list',
				array(
					'general'               => array(
						'title'       => esc_html__( 'General', 'wp-widget-styler' ),
						'description' => esc_html__( 'A General section includes Title Tag, Visibility, Alignment options.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'style'                 => array(
						'title'       => esc_html__( 'Style', 'wp-widget-styler' ),
						'description' => esc_html__( 'It includes all Colors & Background settings for your widget.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'responsive_visibility' => array(
						'title'       => esc_html__( 'Responsive Visibility', 'wp-widget-styler' ),
						'description' => esc_html__( 'A Responsive Visibility section provides you privilege to Show / Hide your widget on responsive devices.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'display_rules'         => array(
						'title'       => esc_html__( 'Display Rules', 'wp-widget-styler' ),
						'description' => esc_html__( 'A Display Rules section let\'s you enable your current widget based on Display rulesets.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'exclusion_rules'       => array(
						'title'       => esc_html__( 'Exclusion Rules', 'wp-widget-styler' ),
						'description' => esc_html__( 'A Exclusion Rules section let\'s you disable your current widget based on Exclusion rulesets.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'user_roles_logic'      => array(
						'title'       => esc_html__( 'User Login State Conditional Logic', 'wp-widget-styler' ),
						'description' => esc_html__( 'A User Login State Conditional Logic section grants you to control widget visibility based on user login status.', 'wp-widget-styler' ),
						'default'     => true,
					),
					'advanced'              => array(
						'title'       => esc_html__( 'Advanced', 'wp-widget-styler' ),
						'description' => esc_html__( 'An Advanced section includes Custom Classes & ID privilege for the widget.', 'wp-widget-styler' ),
						'default'     => true,
					),
				)
			);
		}

		return self::$addons_list;
	}
}
