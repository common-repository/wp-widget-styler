<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @author     WebEmpire <webempire143@gmail.com>
 * @since      1.0.0
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WP_Widget_Styler_Admin.
 *
 * @since 1.0.0
 */
final class WP_Widget_Styler_Admin {

	/**
	 * Calls on initialization
	 *
	 * @since 1.0.0
	 */
	public static function init() {

		self::initialize_ajax();
		self::initialise_plugin();
		add_action( 'after_setup_theme', __CLASS__ . '::init_hooks' );
	}

	/**
	 * Adds the admin menu and enqueues CSS/JS if we are on the builder admin settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function init_hooks() {

		if ( ! is_admin() ) {
			return;
		}

		// Add Powerful VC menu option to admin.
		add_action( 'network_admin_menu', __CLASS__ . '::menu' );
		add_action( 'admin_menu', __CLASS__ . '::menu' );

		add_action( 'wp_widget_styler_render_admin_content', __CLASS__ . '::render_content' );

		if ( isset( $_REQUEST['page'] ) && WP_WIDGET_STYLER_SLUG === $_REQUEST['page'] ) {

			// Enqueue admin scripts.
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
			self::save_settings();
		}
	}

	/**
	 * Initialises the Plugin Name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function initialise_plugin() {

		$name = 'WP Widget Styler';

		$short_name = 'Widget Styler';

		if ( ! defined( 'WP_WIDGET_STYLER_PLUGIN_NAME' ) ) {
			define( 'WP_WIDGET_STYLER_PLUGIN_NAME', $name );
		}

		if ( ! defined( 'WP_WIDGET_STYLER_PLUGIN_SHORT_NAME' ) ) {
			define( 'WP_WIDGET_STYLER_PLUGIN_SHORT_NAME', $short_name );
		}
	}

	/**
	 * Renders the admin settings menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function menu() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			'themes.php',
			WP_WIDGET_STYLER_PLUGIN_SHORT_NAME,
			WP_WIDGET_STYLER_PLUGIN_SHORT_NAME,
			'edit_posts',
			WP_WIDGET_STYLER_SLUG,
			__CLASS__ . '::render'
		);
	}

	/**
	 * Renders the admin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function render() {
		$action = ( isset( $_GET['action'] ) ) ? esc_attr( $_GET['action'] ) : '';
		$action = ( ! empty( $action ) && '' !== $action ) ? $action : 'general';
		$action = str_replace( '_', '-', $action );

		// Enable header icon filter below.
		$wp_widget_styler_header_wrap_class = apply_filters( 'wp_widget_styler_header_wrap_class', array( $action ) );

		include_once WP_WIDGET_STYLER_DIR . 'public/widget-styler-admin.php';
	}

	/**
	 * Renders the admin settings content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function render_content() {

		$action = ( isset( $_GET['action'] ) ) ? esc_attr( $_GET['action'] ) : '';
		$action = ( ! empty( $action ) && '' !== $action ) ? $action : 'general';
		$action = str_replace( '_', '-', $action );

		$wp_widget_styler_header_wrap_class = apply_filters( 'wp_widget_styler_header_wrap_class', array( $action ) );

		include_once WP_WIDGET_STYLER_DIR . 'public/widget-styler-' . $action . '.php';
	}

	/**
	 * Enqueues the needed CSS/JS for the builder's admin settings page.
	 *
	 * @since 1.0.0
	 */
	static public function styles_scripts() {

		// Styles.
		wp_enqueue_style( 'wp-widget-styler-admin-settings', WP_WIDGET_STYLER_URL . 'public/css/wp-widget-styler-admin.css', array(), WP_WIDGET_STYLER_VER );
		// Script.
		wp_enqueue_script( 'wp-widget-styler-admin-settings', WP_WIDGET_STYLER_URL . 'public/js/wp-widget-styler-admin.js', array( 'jquery', 'wp-util', 'updates' ), WP_WIDGET_STYLER_VER );

		$localize = array(
			'ajax_nonce' => wp_create_nonce( 'widget-styler-widget-nonce' ),
		);

		wp_localize_script( 'wp-widget-styler-admin-settings', 'wp_widget_styler_admin_js', apply_filters( 'wp_widget_styler_admin_js_localize', $localize ) );
	}

	/**
	 * Save All admin settings here.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function save_settings() {

		// Only admins can save settings.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Let extensions hook into saving.
		do_action( 'wp_widget_styler_admin_settings_save' );
	}

	/**
	 * Initialize Ajax admin actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function initialize_ajax() {
		// Ajax requests.
		add_action( 'wp_ajax_wp_widget_styler_addon_activate', __CLASS__ . '::activate_widget' );
		add_action( 'wp_ajax_wp_widget_styler_addon_deactivate', __CLASS__ . '::deactivate_widget' );

		add_action( 'wp_ajax_wp_widget_styler_bulk_addons_activate', __CLASS__ . '::bulk_activate_widgets' );
		add_action( 'wp_ajax_wp_widget_styler_bulk_addons_deactivate', __CLASS__ . '::bulk_deactivate_widgets' );
	}

	/**
	 * Activate individual module.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function activate_widget() {

		if ( ! apply_filters( 'wp_widget_styler_user_cap_check', current_user_can( 'manage_options' ) ) ) {
			return;
		}

		check_ajax_referer( 'widget-styler-widget-nonce', 'nonce' );

		$widgets               = WP_Widget_Styler_Helper::get_admin_settings_option( 'wp_widget_styler_addons', array() );
		$module_id             = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
		$widgets[ $module_id ] = $module_id;
		$widgets               = array_map( 'esc_attr', $widgets );

		// Update widgets.
		WP_Widget_Styler_Helper::update_admin_settings_option( 'wp_widget_styler_addons', $widgets );

		echo $module_id;

		die();
	}

	/**
	 * Deactivate individual module.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function deactivate_widget() {

		if ( ! apply_filters( 'wp_widget_styler_user_cap_check', current_user_can( 'manage_options' ) ) ) {
			return;
		}

		check_ajax_referer( 'widget-styler-widget-nonce', 'nonce' );

		$widgets               = WP_Widget_Styler_Helper::get_admin_settings_option( 'wp_widget_styler_addons', array() );
		$module_id             = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
		$widgets[ $module_id ] = 'disabled';
		$widgets               = array_map( 'esc_attr', $widgets );

		// Update widgets.
		WP_Widget_Styler_Helper::update_admin_settings_option( 'wp_widget_styler_addons', $widgets );

		echo $module_id;

		die();
	}

	/**
	 * Activate all bulk module.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function bulk_activate_widgets() {

		if ( ! apply_filters( 'wp_widget_styler_user_cap_check', current_user_can( 'manage_options' ) ) ) {
			return;
		}

		check_ajax_referer( 'widget-styler-widget-nonce', 'nonce' );

		// Get all widgets.
		$all_widgets = WP_Widget_Styler_Helper::get_addons_list();
		$new_widgets = array();

		// Set all extension to enabled.
		foreach ( $all_widgets  as $slug => $value ) {
			$new_widgets[ $slug ] = $slug;
		}

		// Escape attrs.
		$new_widgets = array_map( 'esc_attr', $new_widgets );

		// Update new_extensions.
		WP_Widget_Styler_Helper::update_admin_settings_option( 'wp_widget_styler_addons', $new_widgets );

		echo 'success';

		die();
	}

	/**
	 * Deactivate all bulk module.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	static public function bulk_deactivate_widgets() {

		if ( ! apply_filters( 'wp_widget_styler_user_cap_check', current_user_can( 'manage_options' ) ) ) {
			return;
		}

		check_ajax_referer( 'widget-styler-widget-nonce', 'nonce' );

		// Get all extensions.
		$old_widgets = WP_Widget_Styler_Helper::get_addons_list();
		$new_widgets = array();

		// Set all extension to enabled.
		foreach ( $old_widgets as $slug => $value ) {
			$new_widgets[ $slug ] = 'disabled';
		}

		// Escape attrs.
		$new_widgets = array_map( 'esc_attr', $new_widgets );

		// Update new_extensions.
		WP_Widget_Styler_Helper::update_admin_settings_option( 'wp_widget_styler_addons', $new_widgets );

		echo 'success';

		die();
	}
}

/**
 *  Let's initialize the class by calling its init() method.
 */
WP_Widget_Styler_Admin::init();
