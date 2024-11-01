<?php
/**
 * Register & load all actions and filters & required files for the plugin
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
 * Register & load all actions and filters & required files for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/classes
 * @author     WebEmpire <webempire143@gmail.com>
 */
class WP_Widget_Styler_Config {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->includes();
		$this->setup_actions_filters();
	}

	/**
	 * Includes.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		require WP_WIDGET_STYLER_DIR . 'includes/wp-widget-styler-addons.php';
		require WP_WIDGET_STYLER_DIR . 'includes/wp-widget-styler-helper.php';
		require WP_WIDGET_STYLER_DIR . 'includes/class-wp-widget-styler-admin.php';
	}

	/**
	 * Setup Actions Filters.
	 *
	 * @since 1.0.0
	 */
	private function setup_actions_filters() {

		add_action( 'wp_enqueue_scripts', array( $this, 'wpws_enqueue_scripts' ), 1 );

		add_filter( 'body_class', array( $this, 'body_classes' ), 10, 1 );

		add_action( 'in_widget_form', array( $this, 'wpws_configure_styler' ), 10, 3 );

		add_filter( 'widget_update_callback', array( $this, 'wpws_save_configured_styles' ), 10, 3 );
	}

	/**
	 * Init widget config setup.
	 *
	 * @since 1.0.0
	 */
	public function wpws_configure_styler( $widget, $return, $instance ) {

		global $wp_registered_widget_controls;
		$widget_id = $widget->id;
		$width     = ( isset( $wp_registered_widget_controls[ $widget_id ]['width'] ) ) ? (int) $wp_registered_widget_controls[ $widget_id ]['width'] : 250;
		$unique_id = ( ! empty( $instance['wpws_widget_unique_id'] ) ) ? $instance['wpws_widget_unique_id'] : $widget_id;
		$opts      = ( isset( $instance[ 'wpws_widget_data-' . $widget_id ] ) ) ? $instance[ 'wpws_widget_data-' . $widget_id ] : array();
		$namespace = 'wpws_widget_data-' . $widget_id;

		$args = apply_filters(
			'wpws_widgets_data_args',
			array(
				'width'     => $width,
				'id'        => $widget_id,
				'params'    => $opts,
				'namespace' => $namespace,
			)
		);

		$tab_array = apply_filters(
			'wpws_widget_styler_tabs',
			array(
				array(
					'id'            => 'wpws-general-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'general',
					'callback'      => array( $this, 'wpws_general_tab' ),
					'icon'          => 'dashicons-admin-tools',
					'title'         => esc_html( 'General', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-style-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'style',
					'callback'      => array( $this, 'wpws_style_tab' ),
					'icon'          => 'dashicons-art',
					'title'         => esc_html( 'Style', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-devices-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'responsive_visibility',
					'callback'      => array( $this, 'wpws_devices_tab' ),
					'icon'          => 'dashicons-tablet',
					'title'         => esc_html( 'Devices', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-visibility-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'display_rules',
					'callback'      => array( $this, 'wpws_visibility_tab' ),
					'icon'          => 'dashicons-visibility',
					'title'         => esc_html( 'Display on', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-hidden-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'exclusion_rules',
					'callback'      => array( $this, 'wpws_hidden_tab' ),
					'icon'          => 'dashicons-hidden',
					'title'         => esc_html( 'Hide on', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-userstate-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'user_roles_logic',
					'callback'      => array( $this, 'wpws_userstate_tab' ),
					'icon'          => 'dashicons-admin-users',
					'title'         => esc_html( 'User State', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-advanced-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => 'advanced',
					'callback'      => array( $this, 'wpws_advanced_tab' ),
					'icon'          => 'dashicons-admin-generic',
					'title'         => esc_html( 'Advanced', 'wp-widget-styler' ),
				),
				array(
					'id'            => 'wpws-pro-config',
					'class'         => 'wpws-tab',
					'part_of_addon' => '',
					'callback'      => array( $this, 'wpws_upgrade_tab' ),
					'icon'          => 'dashicons-megaphone',
					'title'         => esc_html( 'What\'s New?', 'wp-widget-styler' ),
				),
			)
		);

		?>
			<input type="hidden" name="wpws_widget_data_handler" value="wpws_widget_data-<?php echo esc_attr( $widget_id ); ?>">
			<input type="hidden" name="wpws_widget_unique_id" value="<?php echo esc_attr( $unique_id ); ?>">

			<div class="wpws-landing-table widefat">
				<div class="wpws-table-container">
					<div class="wpws-column-left">
						<div class="wpws-tab-wrapper">
							<?php
							foreach ( $tab_array as $key => $tab ) {
								if ( '' !== $tab['part_of_addon'] && ! WP_Widget_Styler_Helper::is_addon_active( $tab['part_of_addon'] ) ) {
									continue;
								}

								$tab_classes = $tab['class'] . ' active';
								?>

								<div class="<?php echo esc_attr( $tab_classes ); ?>" data-tab="<?php echo esc_attr( $tab['id'] ); ?>">
									<span data-hover="<?php echo $tab['title']; ?>" class="tab-title"> <i class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"> </i> </span>
								</div>
							<?php } ?>
						</div>
					</div>

					<div class="wpws-column-right">
						<?php
						foreach ( $tab_array as $key => $tab ) {

							if ( '' !== $tab['part_of_addon'] && ! WP_Widget_Styler_Helper::is_addon_active( $tab['part_of_addon'] ) ) {
								continue;
							}

							call_user_func_array( $tab['callback'], array( $args, $widget_id ) );
						}
						?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * General tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_general_tab( $args, $widget_id ) {

		do_action( 'wpws_general_tab_before', $args, $widget_id );

		?>
			<div class="wpws-general-config wpws-tab-content active widefat">
				<?php

					echo wpws()->meta->get_select_field(
						$args,
						array(
							'label'   => __( 'Title Tag', 'wp-widget-styler' ),
							'name'    => 'title_tag',
							'options' => array(
								'h1'   => esc_html__( 'H1', 'wp-widget-styler' ),
								'h2'   => esc_html__( 'H2', 'wp-widget-styler' ),
								'h3'   => esc_html__( 'H3', 'wp-widget-styler' ),
								'h4'   => esc_html__( 'H4', 'wp-widget-styler' ),
								'h5'   => esc_html__( 'H5', 'wp-widget-styler' ),
								'span' => esc_html__( 'Span', 'wp-widget-styler' ),
								'p'    => esc_html__( 'Paragraph', 'wp-widget-styler' ),
							),
						)
					);

					echo wpws()->meta->get_checkbox_field(
						$args,
						array(
							'name'  => 'hide_title',
							'label' => esc_html__( 'Hide Widget Title', 'wp-widget-styler' ),
						)
					);

					echo wpws()->meta->get_setting_group(
						$args,
						array(
							'setting'    => array(
								'widget_alignment' => 'alignment_field',
							),
							'responsive' => true,
							'devices'    => array(
								'desktop'    => esc_html__( 'Desktop', 'wp-widget-styler' ),
								'tablet'     => esc_html__( 'Tablet', 'wp-widget-styler' ),
								'smartphone' => esc_html__( 'Mobile', 'wp-widget-styler' ),
							),
							'title'      => esc_html__( 'Overall Alignment', 'wp-widget-styler' ),
							'options'    => array(
								'left'   => esc_html__( 'Left', 'wp-widget-styler' ),
								'center' => esc_html__( 'Center', 'wp-widget-styler' ),
								'right'  => esc_html__( 'Right', 'wp-widget-styler' ),
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_general_tab_after', $args, $widget_id );
	}

	/**
	 * Style tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_style_tab( $args, $widget_id ) {

		do_action( 'wpws_style_tab_before', $args, $widget_id );

		?>
			<div class="wpws-style-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_colorpicker_field(
						$args,
						array(
							'label' => 'Title Color',
							'name'  => 'title_color',
							// 'section-description'  => '<i>' . sprintf( esc_html__( 'Upgrade to %1$sAdvanced WP Widget Styler%2$s plugin for advanced Background Image & Color settings.', 'wp-widget-styler' ), '<a href="#" class="wpws-section-description" target="_blank">', '</a>' ) . '</i>',
							'help'  => esc_html__( 'Apply text color to widget title.', 'wp-widget-styler' ),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_style_tab_after', $args, $widget_id );
	}

	/**
	 * Devices tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_devices_tab( $args, $widget_id ) {

		do_action( 'wpws_devices_tab_before', $args, $widget_id );

		?>
			<div class="wpws-devices-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_setting_group(
						$args,
						array(
							'help'        => __( 'Display rule for responsive devices.', 'wp-widget-styler' ),
							'title'       => esc_html__( 'Responsive Visibility', 'wp-widget-styler' ),
							'description' => esc_html__( 'Checked devices will not show this widget.', 'wp-widget-styler' ),
							'responsive'  => true,
							'setting'     => array(
								'responsive_visibility' => 'checkbox_field',
							),
							'devices'     => array(
								'desktop'    => esc_html__( 'Desktop', 'wp-widget-styler' ),
								'tablet'     => esc_html__( 'Tablet', 'wp-widget-styler' ),
								'smartphone' => esc_html__( 'Mobile', 'wp-widget-styler' ),
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_devices_tab_after', $args, $widget_id );
	}

	/**
	 * Visibility tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_visibility_tab( $args, $widget_id ) {

		do_action( 'wpws_visibility_tab_before', $args, $widget_id );

		?>
			<div class="wpws-visibility-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_setting_group(
						$args,
						array(
							'title'       => esc_html__( 'Display Locations', 'wp-widget-styler' ),
							'description' => esc_html__( 'Add locations for where this widget should appear.', 'wp-widget-styler' ),
							'setting'     => array(
								'basic'                    => 'heading_field',
								'display_on_entire_website' => 'checkbox_field',
								'display_on_all_singulars' => 'checkbox_field',
								'display_on_all_archives'  => 'checkbox_field',
								'display_on_special_pages' => 'heading_field',
								'display_on_404_page'      => 'checkbox_field',
								'display_on_search_page'   => 'checkbox_field',
								'display_on_blog_posts-page' => 'checkbox_field',
								'display_on_front_page'    => 'checkbox_field',
								'display_on_date_archive'  => 'checkbox_field',
								'display_on_author_archive' => 'checkbox_field',
								'display_on_display_rule'  => 'wordpress_rules',
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_visibility_tab_after', $args, $widget_id );
	}

	/**
	 * Visibility tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_hidden_tab( $args, $widget_id ) {

		do_action( 'wpws_hidden_tab_before', $args, $widget_id );

		?>
			<div class="wpws-hidden-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_setting_group(
						$args,
						array(
							'title'       => esc_html__( 'Exclude Locations', 'wp-widget-styler' ),
							'description' => esc_html__( 'This widget will not appear at these locations.', 'wp-widget-styler' ),
							'setting'     => array(
								'basic'                   => 'heading_field',
								'hide_on_entire_website'  => 'checkbox_field',
								'hide_on_all_singulars'   => 'checkbox_field',
								'hide_on_all_archives'    => 'checkbox_field',
								'hide_on_special_pages'   => 'heading_field',
								'hide_on_404_page'        => 'checkbox_field',
								'hide_on_search_page'     => 'checkbox_field',
								'hide_on_blog_posts-page' => 'checkbox_field',
								'hide_on_front_page'      => 'checkbox_field',
								'hide_on_date_archive'    => 'checkbox_field',
								'hide_on_author_archive'  => 'checkbox_field',
								'hide_on_exclusion_rule'  => 'wordpress_rules',
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_hidden_tab_after', $args, $widget_id );
	}

	/**
	 * Userstate tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_userstate_tab( $args, $widget_id ) {

		do_action( 'wpws_userstate_tab_before', $args, $widget_id );

		?>
			<div class="wpws-userstate-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_select_field(
						$args,
						array(
							'label'   => __( 'User Logic', 'wp-widget-styler' ),
							'name'    => 'userstate',
							'help'    => esc_html__( 'Users with this targeted login status roles will not load this widget.', 'wp-widget-styler' ),
							// 'section-description'  => '<i>' . sprintf( esc_html__( 'Upgrade to %1$sAdvanced WP Widget Styler%2$s plugin for user role based conditional logic.', 'wp-widget-styler' ), '<a href="#" class="wpws-section-description" target="_blank">', '</a>' ) . '</i>',
							'options' => array(
								'logged_in'  => esc_html__( 'All Logged In Users', 'wp-widget-styler' ),
								'logged_out' => esc_html__( 'All Logged Out Users', 'wp-widget-styler' ),
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_userstate_tab_after', $args, $widget_id );
	}

	/**
	 * Advanced tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_advanced_tab( $args, $widget_id ) {

		do_action( 'wpws_advanced_tab_before', $args, $widget_id );

		?>
			<div class="wpws-advanced-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_text_field(
						$args,
						array(
							'label' => __( 'Custom ID', 'wp-widget-styler' ),
							'name'  => 'custom_id',
							'help'  => __( 'No need to add "#" in ID name.', 'wp-widget-styler' ),
							'attr'  => array(
								'placeholder' => __( 'idname', 'wp-widget-styler' ),
							),
						)
					);

					echo wpws()->meta->get_text_field(
						$args,
						array(
							'label' => __( 'Custom Class', 'wp-widget-styler' ),
							'name'  => 'custom_class',
							'help'  => __( 'No need to add "." in class name. Separate each class with space.', 'wp-widget-styler' ),
							'attr'  => array(
								'placeholder' => __( 'classname', 'wp-widget-styler' ),
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_advanced_tab_after', $args, $widget_id );
	}

	/**
	 * Shortcodes tab.
	 *
	 * @param array $args options.
	 * @param mixed $widget_id Widget ID.
	 *
	 * @since x.x.x
	 */
	public function wpws_upgrade_tab( $args, $widget_id ) {

		do_action( 'wpws_upgrade_tab_before', $args, $widget_id );

		?>
			<div class="wpws-pro-config wpws-tab-content widefat">
				<?php
					echo wpws()->meta->get_upgrade_field(
						$args,
						array(
							'heading_1'    => esc_html( 'Love using WP Widget Styler?', 'wp-widget-styler' ),
							'heading_2'    => esc_html( 'Here Are the More Surprising Upcoming Features.', 'wp-widget-styler' ),
							'pro_heading'  => esc_html( 'Love using WP Widget Styler? Then Get more with Advanced version.', 'wp-widget-styler' ),
							'pro_button'   => array(
								'link' => '#',
								'text' => esc_html( 'Go Pro', 'wp-widget-styler' ),
							),
							'feature_list' => array(
								'typo_option'          => esc_html( 'Advanced Typography Options', 'wp-widget-styler' ),
								'spacing_option'       => esc_html( 'Advanced Spacing Controls', 'wp-widget-styler' ),
								'shortcode_option'     => esc_html( 'Widget Shortcode Provision', 'wp-widget-styler' ),
								'sticky_option'        => esc_html( 'Sticky Widget Settings', 'wp-widget-styler' ),
								'user_roles'           => esc_html( 'Visibility Configurations Based on User Roles', 'wp-widget-styler' ),
								// Beta list starts here.
								'page_builder_support' => esc_html( 'Page Builder Support.', 'wp-widget-styler' ),
								'sidebar_support'      => esc_html( 'Controls for Sidebar as well.', 'wp-widget-styler' ),
								'live_preview'         => esc_html( 'Live Preview Support', 'wp-widget-styler' ),
								'import_export'        => esc_html( 'Import-Export Privilege', 'wp-widget-styler' ),
							),
						)
					);
				?>
			</div>
		<?php

		do_action( 'wpws_upgrade_tab_after', $args, $widget_id );
	}

	/**
	 * Save Widget Configurations.
	 *
	 * @since x.x.x
	 */
	public function wpws_save_configured_styles( $instance, $new_instance, $this_widget ) {

		if ( isset( $_POST['wpws_widget_data_handler'] ) ) {

			if ( is_array( $new_instance ) && isset( $new_instance['wpws_widget_data'] ) ) {
				$name    = 'wpws_widget_data';
				$options = $this->wpws_sanitize_widget_data( $new_instance );
			} else {
				$name    = sanitize_text_field( $_POST['wpws_widget_data_handler'] );
				$options = $this->wpws_sanitize_widget_data( $_POST[ $name ] );
			}

			if ( isset( $options['wpws_widget_data'] ) ) {
				$instance[ $name ] = $this->wpws_sanitize_widget_data( $options['wpws_widget_data'] );
			}
		}

		if ( isset( $_POST['wpws_widget_unique_id'] ) && isset( $_POST['wpws_widget_data_handler'] ) ) {

			$instance['wpws_widget_unique_id'] = str_replace( 'wpws_widget_data-', '', sanitize_text_field( $_POST['wpws_widget_data_handler'] ) );
		}

		return $instance;
	}

	/**
	 * Sanitization configured widget data.
	 *
	 * @param array $args options.
	 *
	 * @since x.x.x
	 */
	public function wpws_sanitize_widget_data( &$array ) {
		foreach ( $array as &$value ) {
			if ( ! is_array( $value ) ) {
				$value = sanitize_text_field( $value );
			} else {
				$this->wpws_sanitize_widget_data( $value );
			}
		}

		return $array;
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since x.x.x
	 */
	public function wpws_enqueue_scripts() {

		/* Directory and Extension */
		$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
		$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';

		$js_uri  = WP_WIDGET_STYLER_URL . 'assets/js/' . $dir_name . '/';
		$css_uri = WP_WIDGET_STYLER_URL . 'assets/css/' . $dir_name . '/';

		// Generate CSS URL.
		$css_file = $css_uri . 'style' . $file_prefix . '.css';

		// Register.
		wp_register_style( 'wpws-style', $css_file, array(), WP_WIDGET_STYLER_VER, 'all' );

		// Enqueue.
		wp_enqueue_style( 'wpws-style' );

		// RTL support.
		wp_style_add_data( 'wpws-style', 'rtl', 'replace' );

		$wpws_dynamic_css_data = apply_filters( 'wpws_dynamic_css', '' );

		wp_add_inline_style( 'wpws-style', $wpws_dynamic_css_data );
	}

	/**
	 * Add identity Body Class.
	 *
	 * @param    string $classes    The existing classes added to <body> tag.
	 * @return   array     $classes    The collection of clases append to <body> tag.
	 */
	function body_classes( $classes ) {

		$classes[] = 'wp-widget-styler-' . WP_WIDGET_STYLER_VER . '';
		return $classes;
	}
}

/**
 *  Prepare if class 'WP_Widget_Styler_Config' exist. Kicking this off by creating 'new' instance.
 */
new WP_Widget_Styler_Config();
