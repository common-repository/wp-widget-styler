( function( $ ) {

	/**
	 * AJAX Request Queue
	 *
	 * - add()
	 * - remove()
	 * - run()
	 * - stop()
	 *
	 * @since 1.0.0
	 */
	var WPWidgetStylerAjaxQueue = (function() {

		var requests = [];

		return {

			/**
			 * Add AJAX request
			 *
			 * @since 1.2.0.8
			 */
			add:  function(opt) {
			    requests.push(opt);
			},

			/**
			 * Remove AJAX request
			 *
			 * @since 1.2.0.8
			 */
			remove:  function(opt) {
			    if( jQuery.inArray(opt, requests) > -1 )
			        requests.splice($.inArray(opt, requests), 1);
			},

			/**
			 * Run / Process AJAX request
			 *
			 * @since 1.2.0.8
			 */
			run: function() {
			    var self = this,
			        oriSuc;

			    if( requests.length ) {
			        oriSuc = requests[0].complete;

			        requests[0].complete = function() {
			             if( typeof(oriSuc) === 'function' ) oriSuc();
			             requests.shift();
			             self.run.apply(self, []);
			        };

			        jQuery.ajax(requests[0]);

			    } else {

			      self.tid = setTimeout(function() {
			         self.run.apply(self, []);
			      }, 1000);
			    }
			},

			/**
			 * Stop AJAX request
			 *
			 * @since 1.2.0.8
			 */
			stop:  function() {

			    requests = [];
			    clearTimeout(this.tid);
			}
		};

	}());

	WPAdminWidgetStylerAdmin = {

		init: function() {
			/**
			 * Run / Process AJAX request
			 */
			WPWidgetStylerAjaxQueue.run();

			$( document ).delegate( ".wp-widget-styler-activate-addon", "click", WPAdminWidgetStylerAdmin._activate_widget );
			$( document ).delegate( ".wp-widget-styler-deactivate-addon", "click", WPAdminWidgetStylerAdmin._deactivate_widget );

			$( document ).delegate( ".widget-styler-addons-activate-all", "click", WPAdminWidgetStylerAdmin._bulk_activate_widgets );
			$( document ).delegate( ".widget-styler-addons-deactivate-all", "click", WPAdminWidgetStylerAdmin._bulk_deactivate_widgets );
		},

		/**
		 * Activate All Widgets.
		 */
		_bulk_activate_widgets: function( e ) {
			var button = $( this );

			var data = {
				action: 'wp_widget_styler_bulk_addons_activate',
				nonce: wp_widget_styler_admin_js.ajax_nonce,
			};

			if ( button.hasClass( 'updating-message' ) ) {
				return;
			}

			$( button ).addClass('updating-message');

			WPWidgetStylerAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function(data){

					// Bulk add or remove classes to all modules.
					$('.widget-styler-addon-list').children( "li" ).addClass( 'activate' ).removeClass( 'deactivate' );

					$('.widget-styler-addon-list').children( "li" ).find('.wp-widget-styler-activate-addon').addClass('wp-widget-styler-deactivate-addon').removeClass('wp-widget-styler-activate-addon');
					$('.widget-styler-addon-list').children( "li" ).find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');

					$( button ).removeClass('updating-message');
				}
			});

			e.preventDefault();
		},

		/**
		 * Deactivate All Widgets.
		 */
		_bulk_deactivate_widgets: function( e ) {
			var button = $( this );

			var data = {
				action: 'wp_widget_styler_bulk_addons_deactivate',
				nonce: wp_widget_styler_admin_js.ajax_nonce,
			};

			if ( button.hasClass( 'updating-message' ) ) {
				return;
			}

			$( button ).addClass('updating-message');

			WPWidgetStylerAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function(data){

					// Bulk add or remove classes to all modules.
					$('.widget-styler-addon-list').children( "li" ).addClass( 'deactivate' ).removeClass( 'activate' );

					$('.widget-styler-addon-list').children( "li" ).find('.wp-widget-styler-deactivate-addon').addClass('wp-widget-styler-activate-addon').removeClass('wp-widget-styler-deactivate-addon');
					$('.widget-styler-addon-list').children( "li" ).find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');

					$( button ).removeClass('updating-message');
				}
			});
			e.preventDefault();
		},

		/**
		 * Activate Module.
		 */
		_activate_widget: function( e ) {
			var button = $( this ),
				id     = button.parents('li').attr('id');

			var data = {
				module_id : id,
				action: 'wp_widget_styler_addon_activate',
				nonce: wp_widget_styler_admin_js.ajax_nonce,
			};

			if ( button.find('.dashicons').hasClass( 'updating-message' ) ) {
				return;
			}

			$( button ).find('.dashicons').removeClass('dashicons-visibility').addClass('updating-message');

			WPWidgetStylerAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function(data){

					// Add active class.
					$( '#' + id ).addClass('activate').removeClass( 'deactivate' );
					// Change button classes & text.
					$( '#' + id ).find('.wp-widget-styler-activate-addon').addClass('wp-widget-styler-deactivate-addon').removeClass('wp-widget-styler-activate-addon');
					$( '#' + id ).find('.dashicons').removeClass('updating-message').addClass('dashicons-hidden');
				}
			});

			e.preventDefault();
		},

		/**
		 * Deactivate Module.
		 */
		_deactivate_widget: function( e ) {
			var button = $( this ),
				id     = button.parents('li').attr('id');

			var data = {
				module_id: id,
				action: 'wp_widget_styler_addon_deactivate',
				nonce: wp_widget_styler_admin_js.ajax_nonce,
			};

			if ( button.find('.dashicons').hasClass( 'updating-message' ) ) {
				return;
			}

			$( button ).find('.dashicons').removeClass('dashicons-hidden').addClass('updating-message');

			WPWidgetStylerAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function(data){
					// Remove active class.
					$( '#' + id ).addClass( 'deactivate' ).removeClass('activate');
					// Change button classes & text.
					$( '#' + id ).find('.wp-widget-styler-deactivate-addon').addClass('wp-widget-styler-activate-addon').removeClass('wp-widget-styler-deactivate-addon');
					$( '#' + id ).find('.dashicons').removeClass('updating-message').addClass('dashicons-visibility');
				}
			});

			e.preventDefault();
		}
	}

	$( document ).ready(function() {
		WPAdminWidgetStylerAdmin.init();
	});

} )( jQuery ); 
