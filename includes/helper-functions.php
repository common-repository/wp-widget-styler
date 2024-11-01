<?php
/**
 * Advaned WP Widget Styler.
 *
 * Helper file to register & reuse functionalities easily.
 *
 * @package WP_Widget_Styler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Return Widget option based on Instance.
 */
if ( ! function_exists( 'get_wpws_widget_instance_option' ) ) {

	/**
	 * Return widget option.
	 *
	 * @param  string $widget_unique_id Widget unique ID.
	 * @param  string $option Option key.
	 * @param  array  $instance Widget instance.
	 * @param  string $default Option default value.
	 * @return Mixed Return option value.
	 *
	 * @since x.x.x
	 */
	function get_wpws_widget_instance_option( $widget_unique_id = '', $option, $instance = array(), $default = '' ) {

		$value = '';

		if ( '' !== $widget_unique_id ) {

			$wpws_widget_data_array = $instance[ 'wpws_widget_data-' . $widget_unique_id ];

			/**
			 * Filter the options array for Astra Settings.
			 *
			 * @since  x.x.x
			 * @var Array
			 */
			$wpws_widget_data_array = apply_filters( 'get_wpws_widget_{$widget_unique_id}_option_array', $wpws_widget_data_array, $widget_unique_id, $option, $default );

			$value = ( isset( $wpws_widget_data_array[ $option ] ) && '' !== $wpws_widget_data_array[ $option ] ) ? $wpws_widget_data_array[ $option ] : $default;
		}

		/**
		 * Dynamic filter get_wpws_widget_$widget_unique_id_option_$option.
		 *
		 * @since  x.x.x
		 * @var Mixed.
		 */
		return apply_filters( "get_wpws_widget_{$widget_unique_id}_option_{$option}", $value, $widget_unique_id, $option, $default );
	}
}

/**
 * Return Widget option based on Params.
 */
if ( ! function_exists( 'get_wp_registered_widget_controls_data' ) ) {

	/**
	 * Return widget option.
	 *
	 * @param  string $widget_unique_id Widget unique ID.
	 * @param  string $option Option key.
	 * @param  array  $instance Widget instance.
	 * @param  string $default Option default value.
	 * @return Mixed Return option value.
	 *
	 * @since x.x.x
	 */
	function get_wp_registered_widget_controls_data( $widget_unique_id ) {

		global $wp_registered_widget_controls;

		$id_base                = $wp_registered_widget_controls[ $widget_unique_id ]['id_base'];
		$instance               = get_option( 'widget_' . $id_base );
		$wpws_widget_data_array = array();

		if ( isset( $wp_registered_widget_controls[ $widget_unique_id ]['params'][0]['number'] ) ) {
			$count = $wp_registered_widget_controls[ $widget_unique_id ]['params'][0]['number'];
		} elseif ( isset( $wp_registered_widget_controls[ $widget_unique_id ]['callback'][0]->number ) ) {
			$count = $wp_registered_widget_controls[ $widget_unique_id ]['callback'][0]->number;
		} else {
			$count = substr( $widget_unique_id, -1 );
		}

		if ( isset( $instance[ $count ] ) ) {
			$wpws_widget_data_array = ( isset( $instance[ $count ][ 'wpws_widget_data-' . $widget_unique_id ] ) ) ? $instance[ $count ][ 'wpws_widget_data-' . $widget_unique_id ] : array();
		}

		return $wpws_widget_data_array;
	}
}

/**
 * Return Widget option based on Params.
 */
if ( ! function_exists( 'get_wpws_widget_param_option' ) ) {

	/**
	 * Return widget option.
	 *
	 * @param  string $widget_unique_id Widget unique ID.
	 * @param  string $option Option key.
	 * @param  array  $instance Widget instance.
	 * @param  string $default Option default value.
	 * @return Mixed Return option value.
	 *
	 * @since x.x.x
	 */
	function get_wpws_widget_param_option( $widget_unique_id = '', $option, $params = array(), $default = '' ) {

		$value = '';

		if ( '' !== $widget_unique_id ) {

			$wpws_widget_data_array = get_wp_registered_widget_controls_data( $widget_unique_id );

			/**
			 * Filter the options array for Astra Settings.
			 *
			 * @since  x.x.x
			 * @var Array
			 */
			$wpws_widget_data_array = apply_filters( 'get_wpws_widget_{$widget_unique_id}_option_array', $wpws_widget_data_array, $widget_unique_id, $option, $default );

			$value = ( isset( $wpws_widget_data_array[ $option ] ) && '' !== $wpws_widget_data_array[ $option ] ) ? $wpws_widget_data_array[ $option ] : $default;
		}

		/**
		 * Dynamic filter get_wpws_widget_$widget_unique_id_option_$option.
		 *
		 * @since  x.x.x
		 * @var Mixed.
		 */
		return apply_filters( "get_wpws_widget_{$widget_unique_id}_option_{$option}", $value, $widget_unique_id, $option, $default );
	}
}

/**
 * Get unique widget option value.
 */
if ( ! function_exists( 'wpws_get_option' ) ) {

	/**
	 * Return widget option.
	 *
	 * @param  array  $data Widget WPWS data.
	 * @param  string $option Option key.
	 * @param  string $default Option default value.
	 * @return Mixed Return option value.
	 *
	 * @since x.x.x
	 */
	function wpws_get_option( $widget_unique_id, $data, $option, $addon = '', $default = '' ) {

		if ( '' !== $addon && ! WP_Widget_Styler_Helper::is_addon_active( $addon ) ) {
			return $default;
		}

		$value = ( isset( $data[ $option ] ) && '' !== $data[ $option ] ) ? $data[ $option ] : $default;

		/**
		 * Dynamic filter get_wpws_widget_$widget_unique_id_option_$option.
		 *
		 * @since  x.x.x
		 * @var Mixed.
		 */
		return apply_filters( "wpws_{$widget_unique_id}_widget_{$option}_option", $value, $widget_unique_id, $option, $default );
	}
}

/**
 * Tablet breakpoint for WPWS configs.
 */
if ( ! function_exists( 'wpws_tablet_breakpoint' ) ) {

	/**
	 * Get the tablet breakpoint value.
	 *
	 * @param string $min min.
	 * @param string $max max.
	 *
	 * @since x.x.x
	 *
	 * @return number $breakpoint.
	 */
	function wpws_tablet_breakpoint( $min = '', $max = '' ) {

		$tablet_breakpoint = apply_filters( 'wpws_tablet_breakpoint', 768 );

		if ( '' !== $min ) {
			$tablet_breakpoint = $tablet_breakpoint - $min;
		} elseif ( '' !== $max ) {
			$tablet_breakpoint = $tablet_breakpoint + $max;
		}

		return absint( $tablet_breakpoint );
	}
}

/**
 * Mobile breakpoint for WPWS configs.
 */
if ( ! function_exists( 'wpws_mobile_breakpoint' ) ) {

	/**
	 * Get the mobile breakpoint value.
	 *
	 * @param string $min min.
	 * @param string $max max.
	 *
	 * @since x.x.x
	 *
	 * @return number header_breakpoint.
	 */
	function wpws_mobile_breakpoint( $min = '', $max = '' ) {

		$mobile_breakpoint = apply_filters( 'wpws_mobile_breakpoint', 480 );

		if ( '' !== $min ) {
			$mobile_breakpoint = $mobile_breakpoint - $min;
		} elseif ( '' !== $max ) {
			$mobile_breakpoint = $mobile_breakpoint + $max;
		}

		return absint( $mobile_breakpoint );
	}
}

/**
 * Parse CSS for widget stylings.
 */
if ( ! function_exists( 'wpws_parse_css' ) ) {

	/**
	 * Parse CSS
	 *
	 * @param  array  $css_output Array of CSS.
	 * @param  string $min_media  Min Media breakpoint.
	 * @param  string $max_media  Max Media breakpoint.
	 * @return string             Generated CSS.
	 *
	 * @since x.x.x
	 */
	function wpws_parse_css( $css_output = array(), $min_media = '', $max_media = '' ) {

		$parse_css = '';

		if ( is_array( $css_output ) && count( $css_output ) > 0 ) {

			foreach ( $css_output as $selector => $properties ) {

				if ( null === $properties ) {
					break;
				}

				if ( ! count( $properties ) ) {
					continue; }

				$temp_parse_css   = $selector . '{';
				$properties_added = 0;

				foreach ( $properties as $property => $value ) {

					if ( '' === $value ) {
						continue; }

					$properties_added++;
					$temp_parse_css .= $property . ':' . $value . ';';
				}

				$temp_parse_css .= '}';

				if ( $properties_added > 0 ) {
					$parse_css .= $temp_parse_css;
				}
			}

			if ( '' != $parse_css && ( '' !== $min_media || '' !== $max_media ) ) {

				$media_css       = '@media ';
				$min_media_css   = '';
				$max_media_css   = '';
				$media_separator = '';

				if ( '' !== $min_media ) {
					$min_media_css = '(min-width:' . $min_media . 'px)';
				}
				if ( '' !== $max_media ) {
					$max_media_css = '(max-width:' . $max_media . 'px)';
				}
				if ( '' !== $min_media && '' !== $max_media ) {
					$media_separator = ' and ';
				}

				$media_css .= $min_media_css . $media_separator . $max_media_css . '{' . $parse_css . '}';

				return $media_css;
			}
		}

		return $parse_css;
	}
}
