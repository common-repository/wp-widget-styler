<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    WP_Widget_Styler
 * @subpackage WP_Widget_Styler/public
 */

$widgets = WP_Widget_Styler_Helper::get_addon_options();

$code_doc_url = apply_filters( 'widget_styler_code_snippets_link', '#' );

?>

<div class="widget-styler-container wp-widget-styler-general">
<div id="poststuff">
	<div id="post-body" class="columns-2">
		<div id="post-body-content">
			<!-- All WordPress Notices below header -->
			<h1 class="screen-reader-text"> <?php _e( 'General', 'wp-widget-styler' ); ?> </h1>

				<div class="widget-styler-widgets-boxes">
					<div class="widget-addon-section">
						<?php if ( is_array( $widgets ) && ! empty( $widgets ) ) : ?>

							<ul class="widget-styler-addon-list">
								<?php
								foreach ( $widgets as $addon => $info ) {
									$anchor_target = ( isset( $info['doc_url'] ) && ! empty( $info['doc_url'] ) ) ? " target='_blank' rel='noopener'" : '';

									$class = 'deactivate';
									$link  = array(
										'link_class' => 'wp-widget-styler-activate-addon',
										'icon_class' => 'dashicons-visibility',
									);

									if ( $info['is_activate'] ) {
										$class = 'activate';
										$link  = array(
											'link_class' => 'wp-widget-styler-deactivate-addon',
											'icon_class' => 'dashicons-hidden',
										);
									}

									echo '<li id="' . esc_attr( $addon ) . '"  class="wp-widget-styler-widget-wrapper ' . esc_attr( $class ) . '">';
									echo '<h3>' . esc_html( $info['title'] ) . '';

									printf(
										'<a href="%1$s" class="%2$s"> <span class="dashicons %3$s"></span> </a>',
										( isset( $link['link_url'] ) && ! empty( $link['link_url'] ) ) ? esc_url( $link['link_url'] ) : '#',
										esc_attr( $link['link_class'] ),
										esc_html( $link['icon_class'] )
									);

									if ( $info['is_activate'] && isset( $info['setting_url'] ) ) {

										printf(
											'<a href="%1$s"> %2$s </a>',
											esc_url( $info['setting_url'] )
										);
									}

									echo '</h3> <div class="styler-widget-link-wrapper">';
									echo '<p> ' . $info['description'] . ' </p>';
									echo '</div></li>';
								}
								?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
		</div>
	<div class="postbox-container wp-widget-styler-connect" id="postbox-container-1">
		<div id="side-sortables">
			<div class="postbox">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Contact Developer', 'wp-widget-styler' ); ?></span>
					<span class="dashicons dashicons-welcome-view-site"></span>
				</h2>
				<div class="inside">
					<p>
						<?php
						esc_html_e( 'Get in touch with our WP Widget Styler developers. We\'re happy to help!', 'wp-widget-styler' );
						?>
					</p>
						<?php
							$get_support      = apply_filters( 'get_support', 'https://webempire.org.in/support/submit-a-ticket/' );
							$get_support_text = apply_filters( 'get_support_text', esc_html__( 'Get Support »', 'wp-widget-styler' ) );

							printf(
								/* translators: %1$s: demos link. */
								'%1$s',
								! empty( $get_support ) ? '<a href=' . esc_url( $get_support ) . ' target="_blank" rel="noopener">' . esc_html( $get_support_text ) . '</a>' :
								esc_html( $get_support_text )
							);
							?>
					</p>
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Code Snippets', 'wp-widget-styler' ); ?></span>
					<span class="dashicons dashicons-editor-code"></span>
				</h2>
				<div class="inside">
					<p>
						<?php esc_html_e( 'Custom codes are listed here, which will help you for custom requirements.', 'wp-widget-styler' ); ?>
					</p>
					<a href='<?php echo esc_url( $code_doc_url ); ?> ' target="_blank" rel="noopener"><?php esc_attr_e( 'Actions / Filters / CSS »', 'wp-widget-styler' ); ?></a>
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Thanking You!', 'wp-widget-styler' ); ?></span>
					<span class="dashicons dashicons-awards"></span>
				</h2>
				<div class="inside">
					<p>
						<?php
						esc_html_e( 'Thanks for choosing the WP Widget Styler. We hope you like it!!!', 'wp-widget-styler' );
						?>
					</p>

					<p>
						<?php
						esc_html_e( 'Could you please do us a BIG favor and give it a 5-star rating on WordPress?', 'wp-widget-styler' );
						?>
					</p>
					<p>
						<?php
						esc_html_e( 'This would boost our motivation and help other users make a comfortable decision while choosing the Widget Styler.', 'wp-widget-styler' );
						?>
					</p>
					<?php
						$widget_styler_review      = 'https://wordpress.org/support/plugin/wp-widget-styler/reviews/#new-post';
						$widget_styler_review_text = esc_html__( 'Ok, you deserve it »', 'wp-widget-styler' );

						printf(
							/* translators: %1$s: WP Widget Styler support link. */
							'%1$s',
							! empty( $widget_styler_review ) ? '<a href=' . esc_url( $widget_styler_review ) . ' target="_blank" rel="noopener">' . esc_html( $widget_styler_review_text ) . '</a>' :
							esc_html( $widget_styler_review_text )
						);
						?>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!-- /post-body -->
	<br class="clear"/>
</div>

<hr />

