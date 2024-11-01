<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/public
 */

?>

<div class="widget-styler-menu-page-wrapper">
	<div id="widget-styler-menu-page">
		<div class="widget-styler-menu-page-header <?php echo esc_attr( implode( ' ', $wp_widget_styler_header_wrap_class ) ); ?>">
			<div class="widget-styler-container widget-styler-flex">
				<div class="widget-styler-title">
					<a href="#" target="_blank" rel="noopener" >
						<h1 class="widget-styler-author-title"> <?php echo WP_WIDGET_STYLER_PLUGIN_NAME; ?> </h1>
						<span class="widget-styler-plugin-version"><?php echo WP_WIDGET_STYLER_VER; ?></span>
					</a>
				</div>
				<div class="widget-styler-admin-top-links widget-styler-bulk-actions-wrap">
					<a class="bulk-action widget-styler-addons-activate-all button"> <?php esc_html_e( 'Activate All', 'wp-widget-styler' ); ?> </a>
					<a class="bulk-action widget-styler-addons-deactivate-all button"> <?php esc_html_e( 'Deactivate All', 'wp-widget-styler' ); ?> </a>
				</div>
			</div>
		</div>

		<?php
		// Settings update message.
		if ( isset( $_REQUEST['message'] ) && ( 'saved' === $_REQUEST['message'] || 'saved_ext' === $_REQUEST['message'] ) ) {
			?>
				<div id="message" class="notice notice-success is-dismissive widget-styler-notice"><p> <?php esc_html_e( 'Settings saved successfully.', 'wp-widget-styler' ); ?> </p></div>
			<?php
		}
		do_action( 'wp_widget_styler_render_admin_content' );
		?>
	</div>
</div>
