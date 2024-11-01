( function( $ ) {

	var wpws_add_tool_tip_msg = function(){

		$( '.widget-content' ).on('click', '.wpws-field-heading-help', function(){
			var tip_wrap = $(this).closest('.wpws-field-row');
	        	closest_tooltip = tip_wrap.find('.wpws-tooltip-text');
	        closest_tooltip.toggleClass('display_tool_tip');
	    });
	};

	var wpws_settings_tab = function( widget ) {

		var wpws_tab = widget.find('.wpws-tab'),
			wpws_active_tab = widget.find('.wpws-tab.active');

		if( wpws_active_tab.length ) {
			$active_tab = wpws_active_tab;

			$active_tab_markup = '.' + $active_tab.data('tab');

			if( $( $active_tab_markup ).length ) {
				$( $active_tab_markup ).siblings().removeClass('active');
				$( $active_tab_markup ).addClass('active');
			}
		}

		wpws_tab.on('click', function(e) {
			e.preventDefault();

			$this 		= $(this),
			tab_class 	= $this.data('tab');
			content_tab_class = widget.find( '.' + tab_class );

			$this.siblings().removeClass('wp-ui-text-highlight active');
			$this.addClass('wp-ui-text-highlight active');

			if( $( content_tab_class ).length ) {
				$( content_tab_class ).siblings().removeClass('active');
				$( content_tab_class ).addClass('active');
			}
		});
	};

	var wpws_settings_group_tab = function() {

		$('.wpws-setting-field-row-heading .dashicons-arrow-down').on('click', function(e) {
			e.preventDefault();

			$this 		= $(this),
			tab_class 	= $this.closest('.wpws-setting-field-row').find('.wpws-setting-field-row-content');

			if( $( tab_class ).length ) {
				$( tab_class ).toggleClass("setting-group-slidedown setting-group-slideup");
			}
		});
	};

	var wpws_select2_target_rules  = function( selector ) {

		$(selector).astselect2({

			placeholder: wpws.search_select2_text,

			ajax: {
			    url: wpws.ajax_url,
			    dataType: 'json',
			    method: 'post',
			    delay: 250,
			    data: function (params) {
			      	return {
			        	q: params.term, // search term
				        page: params.page,
						action: 'wpws_get_query_posts',
						'nonce': wpws.ajax_nonce,
			    	};
				},
				processResults: function (data) {
		            return {
		                results: data
		            };
		        },
			    cache: true
			},
			minimumInputLength: 2,
		});
	};

	var initColorPicker = function( widget ) {
		widget.find('.wpws-color-picker').wpColorPicker( {
			change: _.throttle( function() {
				$(this).trigger( 'change' );
			}, 3000 )
		});
	};

	function onFormUpdate( event, widget ) {

		initColorPicker( widget );
		wpws_settings_tab( widget );
		wpws_settings_group_tab();
		wpws_add_tool_tip_msg();

		jQuery('select.target-rule-select2').each(function(index, el) {
			wpws_select2_target_rules( el );
		});
	};

	$( document ).on( 'widget-added widget-updated', onFormUpdate );

	$(document).ready(function($) {

		"use strict";

		$( '#widgets-right .widget' ).each( function () {
			wpws_settings_tab( $( this ) );
		} );

		wpws_settings_group_tab();

		/* AJAX action to get all posts | pages | CPT */
		jQuery('select.target-rule-select2').each(function(index, el) {
			wpws_select2_target_rules( el );
		});

		$( '#widgets-right .widget:has(.wpws-color-picker)' ).each( function () {
			initColorPicker( $( this ) );
		} );

		wpws_add_tool_tip_msg();
	});

} ) ( jQuery );
