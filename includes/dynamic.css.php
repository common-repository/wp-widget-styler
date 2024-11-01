<?php
/**
 * WPWS Widget - Dynamic CSS
 *
 * @since      1.0.0
 * @author     WebEmpire <webempire143@gmail.com>
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WPWS Widget data
 */
add_filter( 'wpws_dynamic_css', 'wpws_widgets_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          WPWS Dynamic CSS.
 * @param  string $dynamic_css_filtered WPWS Dynamic CSS Filters.
 * @return String Generated dynamic CSS for WPWS Widget.
 *
 * @since x.x.x
 */
function wpws_widgets_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	global $wp_registered_widget_controls;

	foreach ( $wp_registered_widget_controls as $widget_unique_id => $data ) {

		$wpws_widget_data_array = get_wp_registered_widget_controls_data( $widget_unique_id );

		if ( ! empty( $wpws_widget_data_array ) ) {

			/**
			 * WPWS Widget - Support new ID if it's assigned.
			 */
			$custom_id = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'custom_id', 'advanced' );

			if ( '' !== $custom_id ) {
				$widget_unique_id = $custom_id;
			}

			/**
			 * WPWS Widget - Title Color.
			 */
			$title_color = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'title_color', 'style' );

			/**
			 * WPWS Widget - Responsive Alignment.
			 */
			$desktop_widget_alignment = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'desktop_widget_alignment', 'general' );
			$tablet_widget_alignment  = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'tablet_widget_alignment', 'general' );
			$mobile_widget_alignment  = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'smartphone_widget_alignment', 'general' );

			/**
			 * Responsive devices visibility.
			 */
			$desktop_visibility = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'desktop_responsive_visibility', 'responsive_visibility' );
			$tablet_visibility  = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'tablet_responsive_visibility', 'responsive_visibility' );
			$mobile_visibility  = wpws_get_option( $widget_unique_id, $wpws_widget_data_array, 'smartphone_responsive_visibility', 'responsive_visibility' );

			$desktop_css_output = array(

				/**
				 * Widget title color.
				 */
				'#' . esc_attr( $widget_unique_id ) . ' .widget-title' => array(
					'color' => esc_attr( $title_color ),
				),

				/**
				 * Widget desktop alignment.
				 */
				'#' . esc_attr( $widget_unique_id ) . ' *' => array(
					'text-align' => esc_attr( $desktop_widget_alignment ),
				),
			);

			if ( 'yes' === $desktop_visibility ) {

				/**
				 * Hide widget on DESKTOP.
				 */
				$desktop_css_output[ '#' . esc_attr( $widget_unique_id ) ] = array(
					'display' => 'none',
				);
			}

			/* Parse DESKTOP CSS from array() */
			$desktop_css_output = wpws_parse_css( $desktop_css_output );

			$dynamic_css .= $desktop_css_output;

			$tablet_css_output = array(

				/**
				 * Widget desktop alignment.
				 */
				'#' . esc_attr( $widget_unique_id ) . ' *' => array(
					'text-align' => esc_attr( $tablet_widget_alignment ),
				),
			);

			if ( 'yes' === $tablet_visibility ) {

				/**
				 * Hide widget on TABLET.
				 */
				$tablet_css_output[ '#' . esc_attr( $widget_unique_id ) ] = array(
					'display' => 'none',
				);
			}

			/* Parse TABLET CSS from array() */
			$tablet_css_output = wpws_parse_css( $tablet_css_output, '', wpws_tablet_breakpoint() );

			$dynamic_css .= $tablet_css_output;

			$mobile_css_output = array(

				/**
				 * Widget desktop alignment.
				 */
				'#' . esc_attr( $widget_unique_id ) . ' *' => array(
					'text-align' => esc_attr( $mobile_widget_alignment ),
				),
			);

			if ( 'yes' === $mobile_visibility ) {

				/**
				 * Hide widget on MOBILE.
				 */
				$mobile_css_output[ '#' . esc_attr( $widget_unique_id ) ] = array(
					'display' => 'none',
				);
			}

			/* Parse MOBILE CSS from array() */
			$mobile_css_output = wpws_parse_css( $mobile_css_output, '', wpws_mobile_breakpoint() );

			$dynamic_css .= $mobile_css_output;
		}
	}

	return $dynamic_css;
}
