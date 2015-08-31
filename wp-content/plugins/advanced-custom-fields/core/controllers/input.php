<?php 

/*
*  acf_controller_input
*
*  This class contains the functionality for input actions used throughout ACF
*
*  @type	class
*  @date	5/09/13
*  @since	3.1.8
*
*/

class acf_controller_input
{

	/*
	*  __construct
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// actions
		add_action('acf/input/admin_head', array($this, 'input_admin_head'));
		add_action('acf/input/admin_enqueue_scripts', array($this, 'input_admin_enqueue_scripts'));
	}
	
		
	/*
	*  input_admin_head
	*
	*  action called when rendering the head of an admin screen. Used primarily for passing PHP to JS
	*
	*  @type	action
	*  @date	27/05/13
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function input_admin_head()
	{
		// global
		global $wp_version, $post;
		
				
		// vars
		$toolbars = apply_filters( 'acf/fields/wysiwyg/toolbars', array() );
		$post_id = 0;
		if( $post )
		{
			$post_id = intval( $post->ID );
		}
		
		
		// l10n
		$l10n = apply_filters( 'acf/input/admin_l10n', array(
			'core' => array(
				'expand_details' => __("Expand Details",'acf'),
				'collapse_details' => __("Collapse Details",'acf')
			),
			'validation' => array(
				'error' => __("Validation Failed. One or more fields below are required.",'acf')
			)
		));
		
		
		// options
		$o = array(
			'post_id'		=>	$post_id,
			'nonce'			=>	wp_create_nonce( 'acf_nonce' ),
			'admin_url'		=>	admin_url(),
			'ajaxurl'		=>	admin_url( 'admin-ajax.php' ),
			'wp_version'	=>	$wp_version
		);
		
		
		// toolbars
		$t = array();
		
		if( is_array($toolbars) ){ foreach( $toolbars as $label => $rows ){
			
			$label = sanitize_title( $label );
			$label = str_replace('-', '_', $label);
			
			$t[ $label ] = array();
			
			if( is_array($rows) ){ foreach( $rows as $k => $v ){
				
				$t[ $label ][ 'theme_advanced_buttons' . $k ] = implode(',', $v);
				
			}}
		}}
		
			
		?>
<script type="text/javascript">
(function($) {

	// vars
	acf.post_id = <?php echo is_numeric($post_id) ? $post_id : '"' . $post_id . '"'; ?>;
	acf.nonce = "<?php echo wp_create_nonce( 'acf_nonce' ); ?>";
	acf.admin_url = "<?php echo admin_url(); ?>";
	acf.ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
	acf.wp_version = "<?php echo $wp_version; ?>";
	
	
	// new vars
	acf.o = <?php echo json_encode( $o ); ?>;
	acf.l10n = <?php echo json_encode( $l10n ); ?>;
	acf.fields.wysiwyg.toolbars = <?php echo json_encode( $t ); ?>;

})(jQuery);	
</script>
		<?php
	}
	
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 30/01/13
	*/
	
	function input_admin_enqueue_scripts()
	{

		// scripts
		wp_enqueue_script(array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-tabs',
			'jquery-ui-sortable',
			'wp-color-picker',
			'thickbox',
			'media-upload',
			'acf-input',
			'acf-datepicker',	
		));

		
		// 3.5 media gallery
		if( function_exists('wp_enqueue_media') && !did_action( 'wp_enqueue_media' ))
		{
			wp_enqueue_media();
		}
		
		
		// styles
		wp_enqueue_style(array(
			'thickbox',
			'wp-color-picker',
			'acf-global',
			'acf-input',
			'acf-datepicker',	
		));
	}
			
}

new acf_controller_input();

?>