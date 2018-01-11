<?php

/**
 * Settings class for browser language preference detection
 *
 * @since 1.8
 */
class PLL_Settings_Browser extends PLL_Settings_Module {

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'        => 'browser',
			'title'         => __( 'Detect browser language', 'polylang' ),
			'description'   => __( 'When the front page is visited, set the language according to the browser preference', 'polylang' ),
			'active_option' => $this->is_available() ? 'browser' : false,
		) );

		if ( ! class_exists( 'PLL_Xdata_Domain', true ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'print_js' ) );
		}
	}

	/**
	 * Tells if the option is available
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	protected function is_available() {
		return ( 3 > $this->options['force_lang'] ) || class_exists( 'PLL_Xdata_Domain', true );
	}

	/**
	 * Tells if the module is active
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->is_available() ? parent::is_active() : false;
	}

	/**
	 * Displays the javascript to handle dynamically the change in url modifications
	 * as the preferred browser language is not used when the language is set from different domains
	 *
	 * @since 1.8
	 */
	public function print_js() {
		wp_enqueue_script( 'jquery' );

		if ( parent::is_active() && 3 > $this->options['force_lang'] ) {
			$func = 'removeClass( "inactive" ).addClass( "active" )';
			$link = sprintf( '<span class="deactivate">%s</span>', $this->action_links['deactivate'] );
		}
		else {
			$func = 'removeClass( "active" ).addClass( "inactive" )';
			$link = sprintf( '<span class="activate">%s</span>', $this->action_links['activate'] );
		}

		$deactivated = sprintf( '<span class="deactivated">%s</span>', $this->action_links['deactivated'] );

		?>
		<script type='text/javascript'>
			//<![CDATA[
			( function( $ ){
				$( "input[name='force_lang']" ).change( function() {
					var value = $( this ).val();
					if ( 3 > value ) {
						$( "#pll-module-browser" ).<?php echo $func; ?>.children( "td" ).children( ".row-actions" ).html( '<?php echo $link; ?>' );
					}
					else {
						$( "#pll-module-browser" ).removeClass( "active" ).addClass( "inactive" ).children( "td" ).children( ".row-actions" ).html( '<?php echo $deactivated; ?>' );
					}
				} );
			} )( jQuery );
			// ]]>
		</script>
		<?php
	}
}
