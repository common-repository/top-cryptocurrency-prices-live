<?php
/**
 * @package Top_Cryptocurrency_prices_live
 * @version 1.0
 */
/*
Plugin Name: Top Cryptocurrency Prices Live
Plugin URI: http://wordpress.org/plugins/Top_Cryptocurrency_prices_live/
Description: Top Cryptocurrency prices list from bit234.com
Author: Bit234.com
Version: 1.0
Author URI: http://bit234.com
*/

function load_tcpl_js() {
    $upload_dir = wp_upload_dir();

    wp_enqueue_script(
        'jquery-webticker',
        plugin_dir_url( __FILE__ ) . ( WP_DEBUG ? 'assets/js/jquery.webticker.js' : 'assets/js/jquery.webticker.min.js' ),
        array( 'jquery' ),
        '2.2.0.1',
        true
    );
    //wp_enqueue_script('jquery-webticker');
    wp_enqueue_style(
        'topcryptocurrencypriceslive',
        plugin_dir_url( __FILE__ ) . 'assets/css/topcryptocurrencypriceslive.css',
        array()
    );
    //wp_enqueue_style(
    //    'topcryptocurrencypriceslive-custom',
    //    set_url_scheme( $upload_dir['baseurl'] ) . '/topcryptocurrencypriceslive-custom.css',
    //    array()
    // );

    wp_enqueue_script(
        'topcryptocurrencypriceslive',
        plugin_dir_url( __FILE__ ) . ( WP_DEBUG ? 'assets/js/jquery.topcryptocurrencypriceslive.js' : 'assets/js/jquery.topcryptocurrencypriceslive.min.js' ),
        array( 'jquery', 'jquery-webticker' ),
        '2.2',
        true
    );
    //wp_enqueue_script('topcryptocurrencypriceslive');
    wp_localize_script(
        'topcryptocurrencypriceslive',
        'topcryptocurrencypricesliveJs',
        array( 'ajax_url' => 'http://bit234.com/topcryptocurrencypriceslive' )
    );
    // Enqueue script parser
    if ( isset( $defaults['globalassets'] ) ) {
        wp_enqueue_script( 'topcryptocurrencypriceslive' );
    }

    // Register refresh script if option is enabled
    if ( ! empty( $defaults['refresh'] ) ) {
        wp_register_script(
            'topcryptocurrencypriceslive-refresh',
            set_url_scheme( $upload_dir['baseurl'] ) . '/topcryptocurrencypriceslive-refresh.js',
            array( 'jquery', 'jquery-webticker', 'topcryptocurrencypriceslive' ),
            false,
            true
        );
        wp_enqueue_script( 'topcryptocurrencypriceslive-refresh' );
    }
}

add_action('wp_enqueue_scripts', 'load_tcpl_js');

class Top_Cryptocurrency_Prices_Live extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'Top_Cryptocurrency_Prices_Live',
			'description' => __('Top Cryptocurrency Prices List','top_cryptocurrency_prices_live'),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'Top_Cryptocurrency_Prices_Live', __('Top Cryptocurrency Prices List', 'top_cryptocurrency_prices_live'), $widget_ops );
	}


	public function widget( $args, $instance ) {
		$title =  empty($instance['title']) ? 'Top Cryptocurrency Prices List2' : $instance['title'];
        $filters = explode(',',$instance['hooks']);
        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
        $atts = $instance;
        echo sprintf(
            '<div
             class="topcryptocurrencypriceslive-wrapper %5$s"
             data-topcryptocurrencypriceslive_symbols="%1$s"
             data-topcryptocurrencypriceslive_currency="%2$s"
             data-topcryptocurrencypriceslive_number_format="%4$s"
             data-topcryptocurrencypriceslive_decimals="%10$s"
             data-topcryptocurrencypriceslive_static="%3$s"
             data-topcryptocurrencypriceslive_class="%5$s"
             data-topcryptocurrencypriceslive_speed="%6$s"
             data-topcryptocurrencypriceslive_empty="%7$s"
             data-topcryptocurrencypriceslive_duplicate="%8$s"
            ><ul class="topcryptocurrencypriceslive"><li class="init"><span class="sqitem">%9$s</span></li></ul></div>',
            $instance['symbols'],         // 1
            $instance['currency'],        // 2
            $instance['static'],          // 3
            $instance['number_format'],   // 4
            $instance['class'],           // 5
            $instance['speed'],           // 6
            $empty,                       // 7
            $instance['duplicate'],       // 8
            $instance['loading_message'], // 9
            $instance['decimals']         // 10
        );
        echo $args['after_widget'];
	}

	public function form( $instance ) {

		// Get defaults.
		$defaults = array(
            'symbols'         => 'BTC,ETH,XRP,EOS',
            'currency'        => 'USD',
            'number_format'   => 'cd',
            'decimals'        => '2',
            'static'          => '1',
            'nolink'          => '0',
            'prefill'         => '0',
            'duplicate'       => '0',
            'speed'           => '50',
            'class'           => '',
            'loading_message' => 'Loading top cryptocurrency prices data...',
        );

		// Outputs the options form on admin.
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __("Cryptocurrency Prices",'top_cryptocurrency_prices_live');
		}
		if ( isset( $instance['symbols'] ) ) {
			$symbols = $instance['symbols'];
		} else {
			$symbols = $defaults['symbols'];
		}
		if ( isset( $instance['currency'] ) ) {
			$currency = $instance['currency'];
		} else {
            $currency = $defaults['currency'];
		}

		if ( isset( $instance['static'] ) ) {
			$static = $instance['static'];
		} else {
			$static = $defaults['static'];
		}

		$nolink = $defaults['nolink'];

		if ( isset( $instance['prefill'] ) ) {
			$prefill = $instance['prefill'];
		} else {
			$prefill = $defaults['prefill'];
		}

		if ( isset( $instance['duplicate'] ) ) {
			$duplicate = $instance['duplicate'];
		} else {
			$duplicate = $defaults['duplicate'];
		}

		if ( isset( $instance['class'] ) ) {
			$class = $instance['class'];
		} else {
			$class = $defaults['class'];
		}

		if ( isset( $instance['speed'] ) ) {
			$speed = $instance['speed'];
		} else {
			$speed = $defaults['speed'];
		}

		if ( isset( $instance['number_format'] ) ) {
			$number_format = $instance['number_format'];
		} else {
			$number_format = $defaults['number_format'];
		}
		if ( isset( $instance['decimals'] ) ) {
			$decimals = $instance['decimals'];
		} else {
			$decimals = $defaults['decimals'];
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_attr_e( 'Title' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'symbols' ); ?>"><?php esc_attr_e( 'Cryptocurrency Symbols(less than 10)', 'top_cryptocurrency_prices_live' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'symbols' ); ?>" name="<?php echo $this->get_field_name( 'symbols' ); ?>" type="text" value="<?php echo esc_attr( $symbols ); ?>" title="<?php esc_html_e( 'For currencies use format EURGBP=X; for Dow Jones use .DJI; for specific stock exchange use format EXCHANGE:SYMBOL like LON:FFX', 'top_cryptocurrency_prices_live' ); ?>" />
		</p>




		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		// Processes widget options to be saved.
		$instance = array();
		$instance['title']         = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['symbols']       = ( ! empty( $new_instance['symbols'] ) ) ? strip_tags( $new_instance['symbols'] ) : '';
		$instance['currency']      = ( ! empty( $new_instance['currency'] ) ) ? strip_tags( $new_instance['currency'] ) : '';
		$instance['number_format'] = ( ! empty( $new_instance['number_format'] ) ) ? strip_tags( $new_instance['number_format'] ) : 'dc';
		$instance['decimals']      = ( ! empty( $new_instance['decimals'] ) ) ? intval( $new_instance['decimals'] ) : 2;
		$instance['static']        = ( ! empty( $new_instance['static'] ) ) ? '1' : '0';
		$instance['prefill']       = ( ! empty( $new_instance['prefill'] ) ) ? '1' : '0';
		$instance['duplicate']     = ( ! empty( $new_instance['duplicate'] ) ) ? '1' : '0';
		$instance['class']         = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';
		$instance['speed']         = ( ! empty( $new_instance['speed'] ) ) ? intval( $new_instance['speed'] ) : 50;

		return $instance;
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "Top_Cryptocurrency_Prices_Live" );' ) );
?>
