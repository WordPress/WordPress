<?php 

/*
*  acf_controller_post
*
*  This class contains the functionality to add ACF fields to a post edit form
*
*  @type	class
*  @date	5/09/13
*  @since	3.1.8
*
*/

class acf_controller_post
{
	
	/*
	*  Constructor
	*
	*  This function will construct all the neccessary actions and filters
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct()
	{
		// actions
		add_action('admin_enqueue_scripts',				array($this, 'admin_enqueue_scripts'));
		add_action('save_post', 						array($this, 'save_post'), 10, 1);
		
		
		// ajax
		add_action('wp_ajax_acf/post/render_fields',	array($this, 'ajax_render_fields'));
		add_action('wp_ajax_acf/post/get_style', 		array($this, 'ajax_get_style'));
	}
	
	
	/*
	*  validate_page
	*
	*  This function will check if the current page is for a post/page edit form
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	N/A
	*  @return	(boolean)
	*/
	
	function validate_page()
	{
		// global
		global $pagenow, $typenow;
		
		
		// vars
		$return = false;
		
		
		// validate page
		if( in_array( $pagenow, array('post.php', 'post-new.php') ) )
		{
		
			// validate post type
			global $typenow;
			
			if( $typenow != "acf" )
			{
				$return = true;
			}
			
		}
		
		
		// validate page (Shopp)
		if( $pagenow == "admin.php" && isset( $_GET['page'] ) && $_GET['page'] == "shopp-products" && isset( $_GET['id'] ) )
		{
			$return = true;
		}
		
		
		// return
		return $return;
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_enqueue_scripts()
	{
		// validate page
		if( ! $this->validate_page() )
		{
			return;
		}

		
		// actions
		do_action('acf/input/admin_enqueue_scripts');
		
		add_action('admin_head', array($this,'admin_head'));
	}
	
	
	/*
	*  admin_head
	*
	*  This action will find and add field groups to the current edit page
	*
	*  @type	action (admin_head)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_head()
	{
		// globals
		global $post, $pagenow, $typenow;
		
		
		// shopp
		if( $pagenow == "admin.php" && isset( $_GET['page'] ) && $_GET['page'] == "shopp-products" && isset( $_GET['id'] ) )
		{
			$typenow = "shopp_product";
		}
		
		
		// vars
		$post_id = $post ? $post->ID : 0;
		
			
		// get field groups
		$filter = array( 
			'post_id'	=> $post_id, 
			'post_type'	=> $typenow 
		);
		$metabox_ids = array();
		$metabox_ids = apply_filters( 'acf/location/match_field_groups', $metabox_ids, $filter );
		
		
		// get style of first field group
		$style = '';
		if( isset($metabox_ids[0]) )
		{
			$style = $this->get_style( $metabox_ids[0] );
		}
		
		
		// Style
		echo '<style type="text/css" id="acf_style" >' . $style . '</style>';
		
		
		// add user js + css
		do_action('acf/input/admin_head');
		
		
		// get field groups
		$acfs = apply_filters('acf/get_field_groups', array());
		
		
		if( $acfs )
		{
			foreach( $acfs as $acf )
			{
				// load options
				$acf['options'] = apply_filters('acf/field_group/get_options', array(), $acf['id']);
				
				
				// vars
				$show = in_array( $acf['id'], $metabox_ids ) ? 1 : 0;
				
				
				// priority
				$priority = 'high';
				if( $acf['options']['position'] == 'side' )
				{
					$priority = 'core';
				}
				$priority = apply_filters('acf/input/meta_box_priority', $priority, $acf);
				
				
				// add meta box
				add_meta_box(
					'acf_' . $acf['id'], 
					$acf['title'], 
					array($this, 'meta_box_input'), 
					$typenow, 
					$acf['options']['position'], 
					$priority, 
					array( 'field_group' => $acf, 'show' => $show, 'post_id' => $post_id )
				);
				
			}
			// foreach($acfs as $acf)
		}
		// if($acfs)
		
		
		// Allow 'acf_after_title' metabox position
		add_action('edit_form_after_title', array($this, 'edit_form_after_title'));
		
		
		// remove ACF from meta postbox
		add_filter( 'is_protected_meta', array($this, 'is_protected_meta'), 10, 3 );
	}
	
	
	/*
	*  edit_form_after_title
	*
	*  This action will allow ACF to render metaboxes after the title
	*
	*  @type	action
	*  @date	17/08/13
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function edit_form_after_title()
	{
		// globals
		global $post, $wp_meta_boxes;
		
		
		// render
		do_meta_boxes( get_current_screen(), 'acf_after_title', $post);
		
		
		// clean up
		unset( $wp_meta_boxes['post']['acf_after_title'] );
		
		
		// preview hack
		// the following code will add a hidden input which will trigger WP to create a revision apon save
		// http://support.advancedcustomfields.com/forums/topic/preview-solution/#post-4106
		?>
		<div style="display:none">
			<input type="hidden" name="acf_has_changed" id="acf-has-changed" value="0" />
		</div>
		<?php
	}
	
	
	/*
	*  meta_box_input
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function meta_box_input( $post, $args )
	{
		// extract $args
		extract( $args );
		
		
		// classes
		$class = 'acf_postbox ' . $args['field_group']['options']['layout'];
		$toggle_class = 'acf_postbox-toggle';
		
		
		if( ! $args['show'] )
		{
			$class .= ' acf-hidden';
			$toggle_class .= ' acf-hidden';
		}
		
		
		// HTML
		if( $args['show'] )
		{
			$fields = apply_filters('acf/field_group/get_fields', array(), $args['field_group']['id']);
	
			do_action('acf/create_fields', $fields, $args['post_id']);
		}
		else
		{
			echo '<div class="acf-replace-with-fields"><div class="acf-loading"></div></div>';
		}
		
		
		// nonce
		echo '<div style="display:none">';
			echo '<input type="hidden" name="acf_nonce" value="' . wp_create_nonce( 'input' ) . '" />';
			?>
<script type="text/javascript">
(function($) {
	
	$('#<?php echo $id; ?>').addClass('<?php echo $class; ?>').removeClass('hide-if-js');
	$('#adv-settings label[for="<?php echo $id; ?>-hide"]').addClass('<?php echo $toggle_class; ?>');
	
})(jQuery);	
</script>
			<?php
		echo '</div>';
	}
	
	
	/*
	*  get_style
	*
	*  @description: called by admin_head to generate acf css style (hide other metaboxes)
	*  @since 2.0.5
	*  @created: 23/06/12
	*/

	function get_style( $acf_id )
	{
		// vars
		$options = apply_filters('acf/field_group/get_options', array(), $acf_id);
		$html = '';
		
		
		// add style to html 
		if( in_array('permalink',$options['hide_on_screen']) )
		{
			$html .= '#edit-slug-box {display: none;} ';
		}
		if( in_array('the_content',$options['hide_on_screen']) )
		{
			$html .= '#postdivrich {display: none;} ';
		}
		if( in_array('excerpt',$options['hide_on_screen']) )
		{
			$html .= '#postexcerpt, #screen-meta label[for=postexcerpt-hide] {display: none;} ';
		}
		if( in_array('custom_fields',$options['hide_on_screen']) )
		{
			$html .= '#postcustom, #screen-meta label[for=postcustom-hide] { display: none; } ';
		}
		if( in_array('discussion',$options['hide_on_screen']) )
		{
			$html .= '#commentstatusdiv, #screen-meta label[for=commentstatusdiv-hide] {display: none;} ';
		}
		if( in_array('comments',$options['hide_on_screen']) )
		{
			$html .= '#commentsdiv, #screen-meta label[for=commentsdiv-hide] {display: none;} ';
		}
		if( in_array('slug',$options['hide_on_screen']) )
		{
			$html .= '#slugdiv, #screen-meta label[for=slugdiv-hide] {display: none;} ';
		}
		if( in_array('author',$options['hide_on_screen']) )
		{
			$html .= '#authordiv, #screen-meta label[for=authordiv-hide] {display: none;} ';
		}
		if( in_array('format',$options['hide_on_screen']) )
		{
			$html .= '#formatdiv, #screen-meta label[for=formatdiv-hide] {display: none;} ';
		}
		if( in_array('featured_image',$options['hide_on_screen']) )
		{
			$html .= '#postimagediv, #screen-meta label[for=postimagediv-hide] {display: none;} ';
		}
		if( in_array('revisions',$options['hide_on_screen']) )
		{
			$html .= '#revisionsdiv, #screen-meta label[for=revisionsdiv-hide] {display: none;} ';
		}
		if( in_array('categories',$options['hide_on_screen']) )
		{
			$html .= '#categorydiv, #screen-meta label[for=categorydiv-hide] {display: none;} ';
		}
		if( in_array('tags',$options['hide_on_screen']) )
		{
			$html .= '#tagsdiv-post_tag, #screen-meta label[for=tagsdiv-post_tag-hide] {display: none;} ';
		}
		if( in_array('send-trackbacks',$options['hide_on_screen']) )
		{
			$html .= '#trackbacksdiv, #screen-meta label[for=trackbacksdiv-hide] {display: none;} ';
		}
		
				
		return $html;
	}
	
	
	/*
	*  ajax_get_input_style
	*
	*  @description: called by input-actions.js to hide / show other metaboxes
	*  @since 2.0.5
	*  @created: 23/06/12
	*/
	
	function ajax_get_style()
	{
		// vars
		$options = array(
			'acf_id' => 0,
			'nonce' => ''
		);
		
		// load post options
		$options = array_merge($options, $_POST);
		
		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die(0);
		}
		
		
		// return style
		echo $this->get_style( $options['acf_id'] );
		
		
		// die
		die;
	}
	
	
	/*
	*  ajax_render_fields
	*
	*  @description: 
	*  @since 3.1.6
	*  @created: 23/06/12
	*/

	function ajax_render_fields()
	{
		
		// defaults
		$options = array(
			'acf_id' => 0,
			'post_id' => 0,
			'nonce' => ''
		);
		
		
		// load post options
		$options = array_merge($options, $_POST);
		
		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die(0);
		}
		
		
		// get acfs
		$acfs = apply_filters('acf/get_field_groups', array());
		if( $acfs )
		{
			foreach( $acfs as $acf )
			{
				if( $acf['id'] == $options['acf_id'] )
				{
					$fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);
					
					do_action('acf/create_fields', $fields, $options['post_id']);
					
					break;
				}
			}
		}

		die();
		
	}
	
	
	/*
	*  save_post
	*
	*  @description: Saves the field / location / option data for a field group
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function save_post( $post_id )
	{	
		
		// do not save if this is an auto save routine
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		{
			return $post_id;
		}
		
		
		// verify nonce
		if( !isset($_POST['acf_nonce'], $_POST['fields']) || !wp_verify_nonce($_POST['acf_nonce'], 'input') )
		{
			return $post_id;
		}
		
		
		// if save lock contains a value, the save_post action is already running for another post.
		// this would imply that the user is hooking into an ACF update_value or save_post action and inserting a new post
		// if this is the case, we do not want to save all the $POST data to this post.
		if( isset($GLOBALS['acf_save_lock']) && $GLOBALS['acf_save_lock'] )
		{
			return $post_id;
		}
		
		
		// update the post (may even be a revision / autosave preview)
		do_action('acf/save_post', $post_id);
        
	}
	
	
	/*
	*  is_protected_meta
	*
	*  This function will remove any ACF meta from showing in the meta postbox
	*
	*  @type	function
	*  @date	12/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function is_protected_meta( $protected, $meta_key, $meta_type ) {
		
		// globals
		global $post;
		
		
		// if acf_get_field_reference returns a valid key, this is an acf value, so protect it!
		if( !$protected ) {
			
			$reference = get_field_reference( $meta_key, $post->ID );
			
			if( substr($reference, 0, 6) === 'field_' ) {
				
				$protected = true;
				
			} 
			
		}
		
		
		// return
		return $protected;
				
	}
	
			
}

new acf_controller_post();

?>