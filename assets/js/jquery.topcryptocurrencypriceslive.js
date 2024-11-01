var topcryptocurrencypriceslive_loaded = false;
var topcryptocurrencypriceslives_load = function() {
	var topcryptocurrencypriceslives = jQuery('.topcryptocurrencypriceslive-wrapper');
	if ( 'undefined' !== typeof topcryptocurrencypriceslives ) {
		jQuery.each(topcryptocurrencypriceslives, function(i,v){
			var obj = jQuery(this);
			jQuery.ajax({
				type: 'post',
				dataType: 'json',
				url: topcryptocurrencypricesliveJs.ajax_url,
				data: {
					'action': 'topcryptocurrencypriceslive_load',
					'symbols': jQuery(this).data('topcryptocurrencypriceslive_symbols'),
					'currency': jQuery(this).data('topcryptocurrencypriceslive_currency'),
					'number_format': jQuery(this).data('topcryptocurrencypriceslive_number_format'),
					'decimals': jQuery(this).data('topcryptocurrencypriceslive_decimals'),
					'static': jQuery(this).data('topcryptocurrencypriceslive_static'),
					'class': jQuery(this).data('topcryptocurrencypriceslive_class'),
					'speed': jQuery(this).data('topcryptocurrencypriceslive_speed'),
					'empty': jQuery(this).data('topcryptocurrencypriceslive_empty'),
					'duplicate': jQuery(this).data('topcryptocurrencypriceslive_duplicate')
				},
				success: function(response) {
					if ( response.status == 'success' ) {
						topcryptocurrencypriceslive_loaded = true;
						obj.html(response.message);
						if ( ! obj.data('topcryptocurrencypriceslive_static') ) {
							jQuery(obj).find('.stock_ticker').topcryptocurrencypriceslive({ startEmpty:jQuery(obj).data('topcryptocurrencypriceslive_empty'), duplicate:jQuery(obj).data('topcryptocurrencypriceslive_duplicate'), speed:jQuery(obj).data('topcryptocurrencypriceslive_speed') });
						}
					}
				}
			});
		});
	}
};

jQuery(document).ready(function() {
	topcryptocurrencypriceslives_load();
	var topcryptocurrencypricesliveReload = setInterval(function() {
		if ( topcryptocurrencypriceslive_loaded ) {
			clearInterval(topcryptocurrencypricesliveReload);
		} else {
			topcryptocurrencypriceslives_load();
		}
	}, 5000);
	// Update AlphaVantage quotes
	setTimeout(function() {
		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			async: true,
			url: topcryptocurrencypricesliveJs.ajax_url,
			data: {
				'action': 'topcryptocurrencypriceslive_update_quotes'
			}
		}).done(function(response){
			console.log( 'Stock Ticker update quotes response: ' + response.message );
		});
	}, 2000);

	// Short-circuit selective refresh events if not in customizer preview or pre-4.5.
	if ( 'undefined' === typeof wp || ! wp.customize || ! wp.customize.selectiveRefresh ) {
		return;
	}
	// Re-load Stock Ticker widgets when a partial is rendered.
	wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
		if ( placement.container ) {
			topcryptocurrencypriceslives_load();
		}
	} );
});
