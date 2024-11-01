<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 * @author     WebEmpire <webempire143@gmail.com>
 */

/**
 * Class WP_Widget_Styler_Markup.
 *
 * @since 1.0.0
 */
class WP_Widget_Styler_Markup {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'widget_title', array( $this, 'wp_widget_title_update' ), 10, 3 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'wp_widget_title_tag_update' ) );
		add_filter( 'widget_display_callback', array( $this, 'wpws_widget_display_callback' ), 50, 3 );
	}

	/**
	 * Return Widget title based on WP-Widget-Styler conditions.
	 *
	 * @param  string $widget_title Widget Title.
	 * @param  array  $instance     Instance of widget.
	 * @param  string $widget_id    Specific widget ID.
	 *
	 * @since x.x.x
	 * @return string Current widget title.
	 */
	public function wp_widget_title_update( $widget_title, $instance = array(), $widget_id = '' ) {

		if ( ! WP_Widget_Styler_Helper::is_addon_active( 'general' ) ) {
			return $widget_title;
		}

		$widget_unique_id = isset( $instance['wpws_widget_unique_id'] ) ? $instance['wpws_widget_unique_id'] : '';

		$is_title_hide = get_wpws_widget_instance_option( $widget_unique_id, 'hide_title', $instance );

		if ( 'yes' === $is_title_hide ) {
			return;
		}

		return $widget_title;
	}

	/**
	 * Return Widget params based on update conditions.
	 *
	 * @param  array $params     Params of sidebar title.
	 *
	 * @since x.x.x
	 * @return string Current widget title.
	 */
	public function wp_widget_title_tag_update( $params ) {

		$widget_unique_id = isset( $params[0]['widget_id'] ) ? $params[0]['widget_id'] : '';

		if ( WP_Widget_Styler_Helper::is_addon_active( 'general' ) ) {

			$title_tag = get_wpws_widget_param_option( $widget_unique_id, 'title_tag', $params );

			if ( '' !== $title_tag ) {
				$params[0]['before_title'] = '<' . $title_tag . ' class="widget-title wpws-widget-title">';
				$params[0]['after_title']  = '</' . $title_tag . '>';
			}
		}

		if ( WP_Widget_Styler_Helper::is_addon_active( 'advanced' ) ) {

			$custom_id    = get_wpws_widget_param_option( $widget_unique_id, 'custom_id', $params );
			$custom_class = get_wpws_widget_param_option( $widget_unique_id, 'custom_class', $params );

			// Custom ID support.
			$id_match = 'id="' . $widget_unique_id . '"';

			if ( '' !== $custom_id && ( strpos( $params[0]['before_widget'], $id_match ) !== false ) ) {

				$replacable_id = 'id="' . $custom_id . '"';

				$params[0]['before_widget'] = str_replace( $id_match, $replacable_id, $params[0]['before_widget'] );
			}

			// Custom classname support.
			$class_match = 'class="widget ';

			if ( '' !== $custom_class && ( strpos( $params[0]['before_widget'], $class_match ) !== false ) ) {

				$replacable_class = 'class="widget ' . $custom_class . ' ';

				$params[0]['before_widget'] = str_replace( $class_match, $replacable_class, $params[0]['before_widget'] );
			}
		}

		return $params;
	}

	/**
	 * Return Widget based on WP-Widget-Styler conditions.
	 *
	 * @param  array  $instance     Instance of widget.
	 * @param  string $widget       Current Widget.
	 * @param  string $args         Widget data.
	 *
	 * @since x.x.x
	 * @return bool true | false based on conditional logic.
	 */
	public function wpws_widget_display_callback( $instance, $widget, $args ) {

		$widget_unique_id = isset( $instance['wpws_widget_unique_id'] ) ? $instance['wpws_widget_unique_id'] : '';

		if ( '' === $widget_unique_id ) {
			return $instance;
		}

		if ( WP_Widget_Styler_Helper::is_addon_active( 'user_roles_logic' ) ) {

			/**
			 * Hide | Show widget based on user login status.
			 */
			$user_state_display = get_wpws_widget_instance_option( $widget_unique_id, 'userstate', $instance );

			if ( 'logged_in' === $user_state_display && is_user_logged_in() ) {
				return false;
			} elseif ( 'logged_out' === $user_state_display && ! is_user_logged_in() ) {
				return false;
			}
		}

		if ( WP_Widget_Styler_Helper::is_addon_active( 'display_rules' ) ) {

			/**
			 * Process all display ruleset data.
			 */
			$display_locations_data = $this->get_all_target_specific_data( $instance, $widget_unique_id, 'display_on' );

			$visibility_location_status = $this->process_filtered_location_data( $display_locations_data, $widget_unique_id, 'display_on' );

			if (
				! empty( $visibility_location_status ) &&
				( isset( $visibility_location_status['data_for'] ) && 'display_on' === $visibility_location_status['data_for'] ) &&
				( isset( $visibility_location_status['condition'] ) && $visibility_location_status['condition'] )
			) {
				return $instance;
			}
		}

		if ( WP_Widget_Styler_Helper::is_addon_active( 'exclusion_rules' ) ) {

			/**
			 * Process all hidden ruleset data.
			 */
			$hide_locations_data = $this->get_all_target_specific_data( $instance, $widget_unique_id, 'hide_on' );

			$hiding_location_status = $this->process_filtered_location_data( $hide_locations_data, $widget_unique_id, 'hide_on' );

			if (
				! empty( $hiding_location_status ) &&
				( isset( $hiding_location_status['data_for'] ) && 'hide_on' === $hiding_location_status['data_for'] ) &&
				( isset( $hiding_location_status['condition'] ) && $hiding_location_status['condition'] )
			) {
				return false;
			}
		}

		return $instance;
	}

	/**
	 * Return all widget data based on display_on | hide_on.
	 *
	 * @param  array  $instance     Instance of widget.
	 * @param  string $widget_id    Current Widget ID.
	 * @param  string $condition    display_on | hide_on.
	 *
	 * @since x.x.x
	 * @return array data.
	 */
	public function get_all_target_specific_data( $instance, $widget_id, $condition ) {

		$wpws_widget_data_conditional_data = array();

		if ( isset( $instance[ 'wpws_widget_data-' . $widget_id ] ) && is_array( $instance[ 'wpws_widget_data-' . $widget_id ] ) && ! empty( $instance[ 'wpws_widget_data-' . $widget_id ] ) ) {

			$wpws_widget_data = $instance[ 'wpws_widget_data-' . $widget_id ];

			foreach ( $wpws_widget_data as $key => $data ) {

				if ( strpos( $key, $condition ) !== false ) {

					$updated_key = str_replace( $condition . '_', '', $key );

					$wpws_widget_data_conditional_data[ $updated_key ] = $data;
				}
			}
		}

		return $wpws_widget_data_conditional_data;
	}

	/**
	 * Return all widget data based on display_on | hide_on.
	 *
	 * @param  array  $filtered_data   Filtered Display | Hide location data.
	 * @param  string $widget_id    Current Widget ID.
	 * @param  string $condition    display_on | hide_on.
	 *
	 * @since x.x.x
	 * @return array conditional status.
	 */
	public function process_filtered_location_data( $filtered_data, $widget_id, $condition ) {

		$condiional_status = array();
		$post_id           = get_the_ID();
		$current_post_type = get_post_type( $post_id );

		if ( ! empty( $filtered_data ) ) {

			$condiional_status['data_for']  = $condition;
			$condiional_status['condition'] = false;

			foreach ( $filtered_data as $location => $value ) {

				switch ( $location ) {
					case 'entire_website':
						$condiional_status['condition'] = true;
						break;

					case 'all_singulars':
						if ( is_singular() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'all_archives':
						if ( is_archive() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case '404_page':
						if ( is_404() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'search_page':
						if ( is_search() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'blog_posts-page':
						if ( is_home() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'front_page':
						if ( is_front_page() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'date_archive':
						if ( is_date() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'author_archive':
						if ( is_author() ) {
							$condiional_status['condition'] = true;
						}
						break;

					case 'display_rule':
					case 'exclusion_rule':
						if ( isset( $value ) && is_array( $value ) ) {
							foreach ( $value as $specific_page ) {

								$specific_data = explode( '-', $specific_page );

								$specific_post_type = isset( $specific_data[0] ) ? $specific_data[0] : false;
								$specific_post_id   = isset( $specific_data[1] ) ? (int) $specific_data[1] : false;

								if ( 'post' === $specific_post_type ) {
									if ( $specific_post_id === $post_id ) {
										$condiional_status['condition'] = true;
									}
								} elseif ( isset( $specific_data[2] ) && ( 'single' === $specific_data[2] ) && 'tax' === $specific_post_type ) {

									if ( is_singular() ) {

										$term_details = get_term( $specific_post_id );

										if ( isset( $term_details->taxonomy ) ) {

											$has_term = has_term( (int) $specific_post_id, $term_details->taxonomy, $post_id );

											if ( $has_term ) {
												$condiional_status['condition'] = true;
											}
										}
									}
								} elseif ( 'tax' === $specific_post_type ) {

									$tax_id = get_queried_object_id();

									if ( $specific_post_id == $tax_id ) {
										$condiional_status['condition'] = true;
									}
								}
							}
						}
						break;

					default:
						$condiional_status['condition'] = false;
						break;
				}

				if ( $condiional_status['condition'] ) {
					break;
				}
			}
		}

		return $condiional_status;
	}
}

new WP_Widget_Styler_Markup();
