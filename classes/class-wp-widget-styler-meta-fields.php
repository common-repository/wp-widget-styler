<?php
/**
 * WP Widget Styler Meta Fields.
 *
 * @since      1.0.0
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 */

/**
 * Class Widget_Styler_Meta_Fields.
 *
 * @since      1.0.0
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 * @author     WebEmpire <webempire143@gmail.com>
 */
class Widget_Styler_Meta_Fields {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 *  Initiator
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() { }

	/**
	 * Get field.
	 *
	 * @param array  $field_data Field data.
	 * @param string $field_content Field content.
	 *
	 * @return string field.
	 */
	public function get_field( $field_data, $field_content, $row_class = '' ) {

		$label               = isset( $field_data['label'] ) ? $field_data['label'] : '';
		$help                = isset( $field_data['help'] ) ? $field_data['help'] : '';
		$after_html          = isset( $field_data['after_html'] ) ? $field_data['after_html'] : '';
		$description         = isset( $field_data['description'] ) ? $field_data['description'] : '';
		$section_description = isset( $field_data['section-description'] ) ? $field_data['section-description'] : '';

		$name_class = isset( $field_data['name'] ) ? 'field-' . $field_data['name'] : '';

		$field_html = '<div class="wpws-field-row ' . $name_class . ' ' . $row_class . '">';

		if ( ! empty( $label ) || ! empty( $help ) ) {
			$field_html .= '<div class="wpws-field-row-heading">';
			if ( ! empty( $label ) ) {
				$field_html .= '<label>' . esc_html( $label ) . '</label>';
			}
			if ( ! empty( $help ) ) {
				$field_html     .= '<i class="wpws-field-heading-help dashicons dashicons-editor-help">';
				$field_html     .= '</i>';
				$field_html     .= '<span class="wpws-tooltip-text">';
					$field_html .= $help;
				$field_html     .= '</span>';
			}
			$field_html .= '</div>';
		}

		$field_html .= '<div class="wpws-field-row-content">';
		$field_html .= $field_content;

		if ( ! empty( $after_html ) ) {
			$field_html .= $after_html;
		}

		if ( ! empty( $description ) ) {
			$field_html .= '<p class="description">' . $description . '</p>';
		}

		$field_html .= '</div>';

		if ( ! empty( $section_description ) ) {
			$field_html .= '<p class="section-description description">' . $section_description . '</p>';
		}

		$field_html .= '</div>';

		return $field_html;
	}

	/**
	 * Checkbox field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_checkbox_field( $args, $field_data ) {

		$value = isset( $args['params'][ $field_data['name'] ] ) ? $args['params'][ $field_data['name'] ] : '';

		$field_content = '';
		if ( isset( $field_data['before'] ) ) {
			$field_content .= '<span>' . $field_data['before'] . '</span>';
		}

		$field_content .= '<input type="checkbox" id="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" value="yes" ' . checked( 'yes', $value, false ) . '>';

		if ( isset( $field_data['after'] ) ) {
			$field_content .= '<span>' . $field_data['after'] . '</span>';
		}

		return $this->get_field( $field_data, $field_content );
	}

	/**
	 * Shortcode field.
	 *
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_shortcode_field( $args, $field_data ) {

		$attr = '';

		$attr_fields = array(
			'readonly'  => 'readonly',
			'onfocus'   => 'this.select()',
			'onmouseup' => 'return false',
		);

		if ( $attr_fields && is_array( $attr_fields ) ) {

			foreach ( $attr_fields as $attr_key => $attr_value ) {
				$attr .= ' ' . $attr_key . '="' . $attr_value . '"';
			}
		}

		$field_content = '<input type="text" class="widget-shortcode-input-field" name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" value="' . $field_data['content'] . '" ' . $attr . '>';

		return $this->get_field( $field_data, $field_content );
	}

	/**
	 * Color picker field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_colorpicker_field( $args, $field_data ) {

		$value = isset( $args['params'][ $field_data['name'] ] ) ? $args['params'][ $field_data['name'] ] : '';

		$field_content = '<input class="wpws-color-picker" type="text" name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" value="' . $value . '">';

		return $this->get_field( $field_data, $field_content );
	}

	/**
	 * Setting Group.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_setting_group( $args, $field_data ) {

		$settings   = isset( $field_data['setting'] ) && is_array( $field_data['setting'] ) ? $field_data['setting'] : array();
		$title      = isset( $field_data['title'] ) ? $field_data['title'] : '';
		$field_html = '';

		if ( ! empty( $settings ) ) {

			$field_html .= '<div class="wpws-setting-field-row">';

			$field_html .= '<div class="wpws-setting-field-row-heading">';

			$field_html .= esc_html( $title ) . '<span class="dashicons dashicons-arrow-down"></span>';

			$field_html .= '</div>';

			foreach ( $settings as $option => $control ) {

				$field_html .= '<div class="wpws-setting-field-row-content setting-group-' . $control . ' setting-group-slideup">';

					$field_html .= $this->{$control}( $args, $field_data, $option );

					$last_element = end( $settings );
					$description  = isset( $field_data['description'] ) ? $field_data['description'] : '';

				if ( ! empty( $description ) && $control === $last_element ) {
					$field_html .= '<p class="description"><i>' . $description . '</i></p>';
				}

				$field_html .= '</div>';
			}

			$field_html .= '</div>';
		}

		return $field_html;
	}

	/**
	 * Setting Group > Alignment field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function alignment_field( $args, $field_data, $name ) {

		$field_html    = '';
		$title         = isset( $field_data['title'] ) ? $field_data['title'] : '';
		$devices       = isset( $field_data['devices'] ) ? $field_data['devices'] : '';
		$is_responsive = isset( $field_data['responsive'] ) ? $field_data['responsive'] : false;

		if ( $is_responsive && is_array( $devices ) && ! empty( $devices ) ) {
			foreach ( $devices as $device => $device_title ) {

				$field_html .= '<div class="wpws-field-row-heading">';

				$field_html .= '<span class="dashicons dashicons-' . $device . '"></span>';

				$field_html .= '</div>';

				if ( is_array( $field_data['options'] ) && ! empty( $field_data['options'] ) ) {

					$field_html .= '<div class="wpws-field-row-content wpws-group-field-options">';
					$value       = isset( $args['params'][ $device . '_' . $name ] ) ? $args['params'][ $device . '_' . $name ] : '';

					foreach ( $field_data['options'] as $data_key => $align_title ) {
						$field_html .= '<input type="radio" name="' . $args['namespace'] . '[wpws_widget_data][' . $device . '_' . $name . ']"' . checked( $data_key, $value, false ) . ' id="' . $device . '-widget-align-' . $data_key . '" value="' . $data_key . '">';

						$field_html .= '<label title="' . $align_title . '" class="wpws-group-field-option" for="' . $device . '-widget-align-' . $data_key . '"> <span class="dashicons dashicons-editor-align' . $data_key . '"></span> </label>';
					}

					$field_html .= '</div> <br/>';
				}
			}
		} else {
			$field_html = '<div class="wpws-field-row-heading">' . esc_html( $title ) . '</div>';

			if ( is_array( $field_data['options'] ) && ! empty( $field_data['options'] ) ) {

				$field_html .= '<div class="wpws-field-row-content wpws-group-field-options">';
				$value       = isset( $args['params'][ $name ] ) ? $args['params'][ $name ] : '';

				foreach ( $field_data['options'] as $data_key => $align_title ) {
					$field_html .= '<input type="radio" name="' . $args['namespace'] . '[wpws_widget_data][' . $name . ']"' . checked( $data_key, $value, false ) . ' id="widget-align-' . $data_key . '" value="' . $data_key . '">';

					$field_html .= '<label title="' . $align_title . '" class="wpws-group-field-option" for="widget-align-' . $data_key . '"> <span class="dashicons dashicons-editor-align' . $data_key . '"></span> </label>';
				}

				$field_html .= '</div>';
			}
		}

		return $field_html;
	}

	/**
	 * Setting Group > Devivces field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function checkbox_field( $args, $field_data, $name ) {

		$name_class    = isset( $name ) ? 'field-' . $name : '';
		$devices       = isset( $field_data['devices'] ) ? $field_data['devices'] : '';
		$is_responsive = isset( $field_data['responsive'] ) ? $field_data['responsive'] : false;

		$field_html = '';

		if ( $is_responsive && is_array( $devices ) && ! empty( $devices ) ) {

			foreach ( $devices as $device => $device_title ) {

				$value = isset( $args['params'][ $device . '_' . $name ] ) ? $args['params'][ $device . '_' . $name ] : '';

				$field_html .= '<div class="wpws-field-row-heading">';

				$field_html .= '<span class="dashicons dashicons-' . $device . '"></span>';

				$field_html .= '</div>';

				$field_html .= '<div class="wpws-field-row-content wpws-group-field-options">';

				$field_html .= '<input type="checkbox" id="' . $args['namespace'] . '[wpws_widget_data][' . $device . '_' . $name . ']" name="' . $args['namespace'] . '[wpws_widget_data][' . $device . '_' . $name . ']" value="yes" ' . checked( 'yes', $value, false ) . '>';

				$field_html .= '</div> <br/>';
			}
		} else {

			$title = str_replace( '_', ' ', $name );

			if ( stripos( $title, 'display on' ) !== false ) {
				$title = str_replace( 'display on', ' ', $title );
			} elseif ( stripos( $title, 'hide on' ) !== false ) {
				$title = str_replace( 'hide on', ' ', $title );
			}

			$value = isset( $args['params'][ $name ] ) ? $args['params'][ $name ] : '';

			$field_html = '<div class="wpws-field-row-heading">' . ucwords( $title ) . '</div>';

			$field_html .= '<div class="wpws-field-row-content wpws-group-field-options">';

			$field_html .= '<input type="checkbox" id="' . $args['namespace'] . '[wpws_widget_data][' . $name . ']" name="' . $args['namespace'] . '[wpws_widget_data][' . $name . ']" value="yes" ' . checked( 'yes', $value, false ) . '>';

			$field_html .= '</div>';
		}

		return $field_html;
	}

	/**
	 * Setting Group > WordPress Rules field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function wordpress_rules( $args, $field_data, $name ) {

		$options = isset( $field_data['options'] ) ? $field_data['options'] : '';

		$field_html = '';

		$value = isset( $args['params'][ $name ] ) ? $args['params'][ $name ] : '';

		$field_html .= '<div class="wpws-field-row-content wpws-group-field-options">';

		$field_html .= '<select name="' . $args['namespace'] . '[wpws_widget_data][' . $name . '][]" class="target-rule-select2 target_rule-specific-page form-control ast-input " multiple="multiple">';

		if ( is_array( $value ) && ! empty( $value ) ) {

			foreach ( $value as $key => $id ) {
				// Post | Page option.
				if ( strpos( $id, 'post-' ) !== false ) {
					$id          = (int) str_replace( 'post-', '', $id );
					$post_title  = get_the_title( $id );
					$field_html .= '<option value="post-' . $id . '" selected="selected" >' . $post_title . '</option>';
				}

				// Taxonamy option.
				if ( strpos( $id, 'tax-' ) !== false ) {

					$tax_data = explode( '-', $id );

					$tax_id    = (int) str_replace( 'tax-', '', $id );
					$term      = get_term( $tax_id );
					$term_name = '';

					if ( ! is_wp_error( $term ) ) {
						$term_taxonomy = ucfirst( str_replace( '_', ' ', $term->taxonomy ) );

						if ( isset( $tax_data[2] ) && 'single' === $tax_data[2] ) {
							$term_name = 'All singulars from ' . $term->name;
						} else {
							$term_name = $term->name . ' - ' . $term_taxonomy;
						}
					}

					$field_html .= '<option value="' . $id . '" selected="selected" >' . $term_name . '</option>';
				}
			}
		}

		$field_html .= '</select>';

		$field_html .= '</div> <br/>';

		return $field_html;
	}

	/**
	 * Setting Group > Heading field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function heading_field( $args, $field_data, $name ) {

		$title = str_replace( '_', ' ', $name );

		if ( stripos( $title, 'display on' ) !== false ) {
			$title = str_replace( 'display on', ' ', $title );
		} elseif ( stripos( $title, 'hide on' ) !== false ) {
			$title = str_replace( 'hide on', ' ', $title );
		}

		$field_html      = '<h4 class="wpws-field-heading-content">';
			$field_html .= ucwords( $title );
		$field_html     .= '</h4>';

		return $field_html;
	}

	/**
	 * Get Upgrade field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_upgrade_field( $args, $field_data ) {

		$field_html          = '<div class="wcf-field-row wcf-field-section">';
			$field_html     .= '<div class="wcf-field-section-heading" colspan="2">';
				$field_html .= '<h4>' . esc_html( $field_data['heading_1'] ) . '</h4>';
				$field_html .= '<h4>' . esc_html( $field_data['heading_2'] ) . '</h4>';

		if ( isset( $field_data['help'] ) ) {
			$field_html .= '<i class="wcf-field-heading-help dashicons dashicons-editor-help" title="' . esc_attr( $field_data['help'] ) . '"></i>';
		}

			$field_html .= '</div>';

			$field_html .= '<ol>';
		if ( is_array( $field_data['feature_list'] ) && ! empty( $field_data['feature_list'] ) ) {
			foreach ( $field_data['feature_list'] as $key => $feature ) {
				$field_html .= '<li>' . $feature . '</li>';
			}
		}
			$field_html .= '</ol>';

			$field_html .= '<div class="wpws-rating-section">';

			$field_html .= '<hr /> <p>' . esc_html( 'Thanks for choosing the WP Widget Styler. We hope you like it!!!', 'wp-widget-styler' ) . '</p>';

			$field_html .= '<p>' . esc_html( 'Could you please do us a BIG favor and give it a 5-star rating on WordPress?', 'wp-widget-styler' ) . '</p>';

			$field_html .= '<p>' . esc_html( 'This would boost our motivation and help other users make a comfortable decision while choosing the Widget Styler.', 'wp-widget-styler' ) . '</p>';

			$widget_styler_review      = 'https://wordpress.org/support/plugin/wp-widget-styler/reviews/#new-post';
			$widget_styler_review_text = esc_html__( 'Ok, You Deserve It »', 'wp-widget-styler' );

			$field_html .= '<a href=' . esc_url( $widget_styler_review ) . ' target="_blank" rel="noopener">' . esc_html( $widget_styler_review_text ) . '</a>';

			$field_html .= '</div>';
		$field_html     .= '</div>';

		return $field_html;
	}

	/**
	 * Select field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_select_field( $args, $field_data ) {

		$value       = isset( $args['params'][ $field_data['name'] ] ) ? $args['params'][ $field_data['name'] ] : '';
		$pro_options = isset( $field_data['pro-options'] ) ? $field_data['pro-options'] : array();

		$field_content = '<select name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']">';

		if ( is_array( $field_data['options'] ) && ! empty( $field_data['options'] ) ) {

			if ( '' === $value ) {
				$field_content .= '<option value="">' . esc_html( 'Select Option', 'wp-widget-styler' ) . '</option>';
			}

			foreach ( $field_data['options'] as $data_key => $data_value ) {

				$disabled = '';

				if ( array_key_exists( $data_key, $pro_options ) ) {
					$disabled   = 'disabled ';
					$data_value = $pro_options[ $data_key ];
				}

				$field_content .= '<option value="' . $data_key . '" ' . selected( $value, $data_key, false ) . ' ' . $disabled . '>' . $data_value . '</option>';
			}
		}

		$field_content .= '</select>';

		if ( isset( $field_data['after'] ) ) {
			$field_content .= '<span>' . $field_data['after'] . '</span>';
		}

		return $this->get_field( $field_data, $field_content );
	}

	/**
	 * Input text field.
	 *
	 * @param array $args Widget Data.
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_text_field( $args, $field_data ) {

		$value = isset( $args['params'][ $field_data['name'] ] ) ? $args['params'][ $field_data['name'] ] : '';

		$attr = '';

		if ( isset( $field_data['attr'] ) && is_array( $field_data['attr'] ) ) {

			foreach ( $field_data['attr'] as $attr_key => $attr_value ) {
				$attr .= ' ' . $attr_key . '="' . $attr_value . '"';
			}
		}

		$field_content = '<input type="text" name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" value="' . esc_attr( $value ) . '" ' . $attr . '>';

		return $this->get_field( $field_data, $field_content );
	}

	/**
	 * Text area field.
	 *
	 * @param array $field_data Field data.
	 *
	 * @return string field.
	 */
	public function get_area_field( $args, $field_data ) {

		$value = isset( $args['params'][ $field_data['name'] ] ) ? $args['params'][ $field_data['name'] ] : '';

		$attr = '';

		if ( isset( $field_data['attr'] ) && is_array( $field_data['attr'] ) ) {

			foreach ( $field_data['attr'] as $attr_key => $attr_value ) {
				$attr .= ' ' . $attr_key . '="' . $attr_value . '"';
			}
		}

		$field_content  = '<br /> <textarea name="' . $args['namespace'] . '[wpws_widget_data][' . $field_data['name'] . ']" rows="4" cols="60" ' . $attr . '>';
		$field_content .= $value;
		$field_content .= '</textarea>';

		$row_class = 'wpws-textarea-field';

		return $this->get_field( $field_data, $field_content, $row_class );
	}
}
