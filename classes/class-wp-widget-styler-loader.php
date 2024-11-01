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
 * Class WP_Widget_Styler_Loader.
 *
 * @since 1.0.0
 */
final class WP_Widget_Styler_Loader {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Member Variable
	 *
	 * @var meta
	 */
	public $meta = null;

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
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->define_constants();

		// Activation hook.
		register_activation_hook( WP_WIDGET_STYLER_FILE, array( $this, 'activation_reset' ) );

		// deActivation hook.
		register_deactivation_hook( WP_WIDGET_STYLER_FILE, array( $this, 'deactivation_reset' ) );

		add_action( 'plugins_loaded', array( $this, 'load_plugin' ), 99 );

		add_action( 'admin_init', array( $this, 'init' ) );

		add_action( 'wp_ajax_wpws_get_query_posts', array( $this, 'wpws_get_query_posts' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_widget_meta_scripts' ) );
	}

	/**
	 * Initialize update compatibility.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function init() {

		do_action( 'wp_widget_styler_before_update' );

		// Get auto saved version number.
		$saved_version = get_option( 'wp-widget-styler-version', false );

		// Update auto saved version number.
		if ( ! $saved_version ) {
			update_option( 'wp-widget-styler-version', WP_WIDGET_STYLER_VER );
			return;
		}

		// If equals then return.
		if ( version_compare( $saved_version, WP_WIDGET_STYLER_VER, '=' ) ) {
			return;
		}

		// Update auto saved version number.
		update_option( 'wp-widget-styler-version', WP_WIDGET_STYLER_VER );

		do_action( 'wp_widget_styler_after_update' );
	}

	/**
	 * Admin meta scripts
	 */
	public function admin_widget_meta_scripts( $hook ) {

		if ( 'widgets.php' === $hook ) {

			wp_enqueue_script( 'wpws-widget-select2', WP_WIDGET_STYLER_URL . 'admin/assets/js/select2.js', array( 'jquery' ), WP_WIDGET_STYLER_VER, true );

			wp_enqueue_style( 'wpws-widget-select2', WP_WIDGET_STYLER_URL . 'admin/assets/css/select2.css', '', WP_WIDGET_STYLER_VER );

			wp_enqueue_script(
				'wpws-widgets-meta',
				WP_WIDGET_STYLER_URL . 'admin/assets/js/meta-edit.js',
				array( 'jquery', 'wp-color-picker', 'wpws-widget-select2' ),
				WP_WIDGET_STYLER_VER,
				true
			);

			wp_enqueue_style( 'wpws-admin-meta', WP_WIDGET_STYLER_URL . 'admin/assets/css/meta-edit.css', array( 'wp-color-picker' ), WP_WIDGET_STYLER_VER );
			wp_style_add_data( 'wpws-admin-meta', 'rtl', 'replace' );

			$localize = array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'search_select2_text' => esc_html( 'Specific pages / post / categories', 'wp-widget-styler' ),
				'ajax_nonce'          => wp_create_nonce( 'wpws-get-query-posts' ),
			);

			wp_localize_script( 'wpws-widgets-meta', 'wpws', apply_filters( 'wpws_js_localize', $localize ) );

			do_action( 'widget_styler_admin_widget_meta_scripts' );
		}
	}

	/**
	 * Ajax handeler to return the posts based on the search query.
	 * When searching for the post/pages only titles are searched for.
	 *
	 * @since  1.0.0
	 */
	public function wpws_get_query_posts() {

		check_ajax_referer( 'wpws-get-query-posts', 'nonce' );

		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( $_POST['q'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data          = array();
		$result        = array();

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$output     = 'names'; // names or objects, note names is the default.
		$operator   = 'and'; // also supports 'or'.
		$post_types = get_post_types( $args, $output, $operator );

		$post_types['Posts'] = 'post';
		$post_types['Pages'] = 'page';

		foreach ( $post_types as $key => $post_type ) {

			$data = array();

			add_filter( 'posts_search', array( $this, 'search_only_titles' ), 10, 2 );

			$query = new WP_Query(
				array(
					's'              => $search_string,
					'post_type'      => $post_type,
					'posts_per_page' => - 1,
				)
			);

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$title  = get_the_title();
					$title .= ( 0 != $query->post->post_parent ) ? ' (' . get_the_title( $query->post->post_parent ) . ')' : '';
					$id     = get_the_id();
					$data[] = array(
						'id'   => 'post-' . $id,
						'text' => $title,
					);
				}
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				$result[] = array(
					'text'     => $key,
					'children' => $data,
				);
			}
		}

		$data = array();

		wp_reset_postdata();

		$args = array(
			'public' => true,
		);

		$output     = 'objects'; // names or objects, note names is the default.
		$operator   = 'and'; // also supports 'or'.
		$taxonomies = get_taxonomies( $args, $output, $operator );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms(
				$taxonomy->name,
				array(
					'orderby'    => 'count',
					'hide_empty' => 0,
					'name__like' => $search_string,
				)
			);

			$data = array();

			$label = ucwords( $taxonomy->label );

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term ) {

					$term_taxonomy_name = ucfirst( str_replace( '_', ' ', $taxonomy->name ) );

					$data[] = array(
						'id'   => 'tax-' . $term->term_id,
						'text' => $term->name . ' archive page',
					);

					$data[] = array(
						'id'   => 'tax-' . $term->term_id . '-single-' . $taxonomy->name,
						'text' => 'All singulars from ' . $term->name,
					);

				}
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				$result[] = array(
					'text'     => $label,
					'children' => $data,
				);
			}
		}

		// return the result in json.
		wp_send_json( $result );
	}

	/**
	 * Return search results only by post title.
	 * This is only run from wpws_get_query_posts()
	 *
	 * @param  string   $search   Search SQL for WHERE clause.
	 * @param  WP_Query $wp_query The current WP_Query object.
	 *
	 * @return string The Modified Search SQL for WHERE clause.
	 */
	public function search_only_titles( $search, $wp_query ) {
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';

			$search = array();

			foreach ( (array) $q['search_terms'] as $term ) {
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );
			}

			if ( ! is_user_logged_in() ) {
				$search[] = "$wpdb->posts.post_password = ''";
			}

			$search = ' AND ' . implode( ' AND ', $search );
		}

		return $search;
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param   mixed $links Plugin Action links.
	 * @return  array
	 * @since   1.0.0
	 */
	public function action_links( $links = array() ) {

		$slug = 'wp-widget-styler';

		$action_links = array(
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=' . $slug ) ) . '" aria-label="' . esc_attr__( 'Confiure WP Widget Styler Settings', 'wp-widget-styler' ) . '">' . esc_html__( 'Configure', 'wp-widget-styler' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Defines all constants
	 *
	 * @since 1.0.0
	 */
	public function define_constants() {
		define( 'WP_WIDGET_STYLER_BASE', plugin_basename( WP_WIDGET_STYLER_FILE ) );
		define( 'WP_WIDGET_STYLER_ROOT', dirname( WP_WIDGET_STYLER_BASE ) );
		define( 'WP_WIDGET_STYLER_DIR', plugin_dir_path( WP_WIDGET_STYLER_FILE ) );
		define( 'WP_WIDGET_STYLER_URL', plugins_url( '/', WP_WIDGET_STYLER_FILE ) );
		define( 'WP_WIDGET_STYLER_VER', '1.0.0' );
		define( 'WP_WIDGET_STYLER_MODULES_DIR', WP_WIDGET_STYLER_DIR . 'addons/' );
		define( 'WP_WIDGET_STYLER_MODULES_URL', WP_WIDGET_STYLER_URL . 'addons/' );
		define( 'WP_WIDGET_STYLER_SLUG', 'wp-widget-styler' );
	}

	/**
	 * Loads plugin files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function load_plugin() {

		// Action Links.
		add_action( 'plugin_action_links_' . WP_WIDGET_STYLER_BASE, array( $this, 'action_links' ) );

		$this->load_textdomain();

		$this->load_core_files();
		$this->load_core_components();

		require_once WP_WIDGET_STYLER_DIR . 'classes/class-wp-widget-styler-config.php';
	}

	/**
	 * Load Core Files for WP Widget Styler.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_core_files() {

		include_once WP_WIDGET_STYLER_DIR . 'classes/class-wp-widget-styler-meta-fields.php';
		include_once WP_WIDGET_STYLER_DIR . 'includes/helper-functions.php';
		include_once WP_WIDGET_STYLER_DIR . 'classes/class-wp-widget-styler-markup.php';
		include_once WP_WIDGET_STYLER_DIR . 'includes/dynamic.css.php';
	}

	/**
	 * Load Core Components for WP Widget Styler.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_core_components() {
		$this->meta = Widget_Styler_Meta_Fields::get_instance();
	}

	/**
	 * Load WP Widget Styler Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/wp-widget-styler/ folder
	 *      2. Local dorectory /wp-content/plugins/wp-widget-styler/languages/ folder
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		/**
		 * Filters the languages directory path to use for WP Widget Styler.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'wp_widget_styler_domain_loader', WP_WIDGET_STYLER_ROOT . '/languages/' );
		load_plugin_textdomain( 'wp-widget-styler', false, $lang_dir );
	}

	/**
	 * Activation Reset
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function activation_reset() { }

	/**
	 * Deactivation Reset
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function deactivation_reset() { }
}

/**
 *  Prepare if class 'WP_Widget_Styler_Loader::get_instance' exist. Kicking this off by creating 'new' instance.
 */
WP_Widget_Styler_Loader::get_instance();

/**
 * Get global Access.
 *
 * @return object
 */
function wpws() {
	return WP_Widget_Styler_Loader::get_instance();
}
