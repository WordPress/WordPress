<?php

/**
 * Settings class to advertize the Share slugs module
 *
 * @since 1.9
 */
class PLL_Settings_Share_Slug extends PLL_Settings_Module {

	/**
	 * constructor
	 *
	 * @since 1.9
	 *
	 * @param object $polylang polylang object
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'      => 'share-slugs',
			'title'       => __( 'Share slugs', 'polylang' ),
			'description' => __( 'Allows to share the same url slug across languages for posts and terms.', 'polylang' ),
		) );

		if ( class_exists( 'PLL_Share_Post_Slug', true ) && get_option( 'permalink_structure' ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'print_js' ) );
		}
	}

	/**
	 * tells if the module is active
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'PLL_Share_Post_Slug', true ) && $this->options['force_lang'] && get_option( 'permalink_structure' );
	}

	/**
	 * displays upgrade message
	 *
	 * @since 1.9
	 *
	 * @return string
	 */
	public function get_upgrade_message() {
		return class_exists( 'PLL_Share_Post_Slug', true ) ? '' : $this->default_upgrade_message();
	}

	/**
	 * displays the javascript to handle dynamically the change in url modifications
	 * as sharing slugs is not possible when the language is set from the content
	 *
	 * @since 1.9
	 */
	public function print_js() {
		wp_enqueue_script( 'jquery' );

		$activated = sprintf( '<span class="activated">%s</span>', $this->action_links['activated'] );
		$deactivated = sprintf( '<span class="deactivated">%s</span>', $this->action_links['deactivated'] );

		?>
		<script type='text/javascript'>
			//<![CDATA[
			( function( $ ){
				$( "input[name='force_lang']" ).change( function() {
					var value = $( this ).val();
					if ( value > 0 ) {
						$( "#pll-module-share-slugs" ).removeClass( "inactive" ).addClass( "active" ).children( "td" ).children( ".row-actions" ).html( '<?php echo $activated; ?>' );
					}
					else {
						$( "#pll-module-share-slugs" ).removeClass( "active" ).addClass( "inactive" ).children( "td" ).children( ".row-actions" ).html( '<?php echo $deactivated; ?>' );
					}
				} );
			} )( jQuery );
			// ]]>
		</script>
		<?php
	}
}
