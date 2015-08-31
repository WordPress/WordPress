<?php 

/*
*  acf_export
*
*  @description: controller for export sub menu page
*  @since: 3.6
*  @created: 25/01/13
*/

class acf_export
{
	
	var $action;
	
	
	/*
	*  __construct
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// vars
		$this->action = '';
		
		
		// actions
		add_action('admin_menu', array($this,'admin_menu'), 11, 0);
		
		
		// filters
		add_filter('acf/export/clean_fields', array($this,'clean_fields'), 10, 1);
	}
	
	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @created: 2/08/12
	*/
	
	function admin_menu()
	{
		// add page
		$page = add_submenu_page('edit.php?post_type=acf', __('Export','acf'), __('Export','acf'), 'manage_options', 'acf-export', array($this,'html'));
		
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		add_action('admin_print_scripts-' . $page, array($this, 'admin_print_scripts'));
		add_action('admin_print_styles-' . $page, array($this, 'admin_print_styles'));
		add_action('admin_head-' . $page, array($this,'admin_head'));
	}
	
	
	/*
	*  load
	*
	*  @description: 
	*  @since 3.5.2
	*  @created: 16/11/12
	*  @thanks: Kevin Biloski and Charlie Eriksen via Secunia SVCRP
	*/
	
	function load()
	{
		// vars
		$path = apply_filters('acf/get_info', 'path');
		
		
		// verify nonce
		if( isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'export') )
		{
			if( isset($_POST['export_to_xml']) )
			{
				$this->action = 'export_to_xml';
			}
			elseif( isset($_POST['export_to_php']) )
			{
				$this->action = 'export_to_php';
			}
		}
		
		
		// include export action
		if( $this->action == 'export_to_xml' )
		{
			include_once($path . 'core/actions/export.php');
			die;
		}
	}
	
	
	/*
	*  admin_print_scripts
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_print_scripts()
	{
		
	}
	
	
	/*
	*  admin_print_styles
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_print_styles()
	{
		wp_enqueue_style(array(
			'wp-pointer',
			'acf-global',
			'acf',
		));
	}
	
	
	/*
	*  admin_head
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_head()
	{
				
	}
	
	
	/*
	*  html
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function html()
	{	
		?>
<div class="wrap">

	<div class="icon32" id="icon-acf"><br></div>
	<h2 style="margin: 4px 0 25px;"><?php _e("Export",'acf'); ?></h2>
		<?php
		
		if( $this->action == "export_to_php" )
		{
			$this->html_php();
		}
		else
		{
			$this->html_index();
		}
		
		?>
</div>
		<?php
		
		return;
		
	}
	
	
	/*
	*  html_index
	*
	*  @description: 
	*  @created: 9/08/12
	*/
	
	function html_index()
	{
		// vars
		$acfs = get_posts(array(
			'numberposts' 	=> -1,
			'post_type' 	=> 'acf',
			'orderby' 		=> 'menu_order title',
			'order' 		=> 'asc',
		));

		// blank array to hold acfs
		$choices = array();
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				// find title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
				$title = apply_filters( 'the_title', $acf->post_title, $acf->ID );
				
				$choices[$acf->ID] = $title;
			}
		}
		
		?>
<form method="post">
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'export' ); ?>" />
<div class="wp-box">
	<div class="title">
		<h3><?php _e("Export Field Groups",'acf'); ?></h3>
	</div>
	<table class="acf_input widefat">
		<tr>
			<td class="label">
				<label><?php _e("Field Groups",'acf'); ?></label>
				<p class="description"><?php _e("Select the field groups to be exported",'acf'); ?></p>
			</td>
			<td>
				<?php do_action('acf/create_field', array(
					'type'	=>	'select',
					'name'	=>	'acf_posts',
					'value'	=>	'',
					'choices'	=>	$choices,
					'multiple'	=>	1,
				)); ?>
			</td>
		</tr>
		<tr>
			<td class="label"></td>
			<td>
				<ul class="hl clearfix">
					<li>
						<input type="submit" class="acf-button" name="export_to_xml" value="<?php _e("Export to XML",'acf'); ?>" />
					</li>
					<li>
						<input type="submit" class="acf-button" name="export_to_php" value="<?php _e("Export to PHP",'acf'); ?>" />
					</li>
				</ul>
			</td>
		</tr>
	</table>
</div>
</form>

<p><br /></p>
<h3><?php _e("Export to XML",'acf'); ?></h3>
<p><?php _e("ACF will create a .xml export file which is compatible with the native WP import plugin.",'acf'); ?></p>
<p><?php _e("Imported field groups <b>will</b> appear in the list of editable field groups. This is useful for migrating fields groups between Wp websites.",'acf'); ?></p>
<ol>
	<li><?php _e("Select field group(s) from the list and click \"Export XML\"",'acf'); ?></li>
	<li><?php _e("Save the .xml file when prompted",'acf'); ?></li>
	<li><?php _e("Navigate to Tools &raquo; Import and select WordPress",'acf'); ?></li>
	<li><?php _e("Install WP import plugin if prompted",'acf'); ?></li>
	<li><?php _e("Upload and import your exported .xml file",'acf'); ?></li>
	<li><?php _e("Select your user and ignore Import Attachments",'acf'); ?></li>
	<li><?php _e("That's it! Happy WordPressing",'acf'); ?></li>
</ol>

<p><br /></p>

<h3><?php _e("Export to PHP",'acf'); ?></h3>
<p><?php _e("ACF will create the PHP code to include in your theme.",'acf'); ?></p>
<p><?php _e("Registered field groups <b>will not</b> appear in the list of editable field groups. This is useful for including fields in themes.",'acf'); ?></p>
<p><?php _e("Please note that if you export and register field groups within the same WP, you will see duplicate fields on your edit screens. To fix this, please move the original field group to the trash or remove the code from your functions.php file.",'acf'); ?></p>
<ol>
	<li><?php _e("Select field group(s) from the list and click \"Create PHP\"",'acf'); ?></li>
	<li><?php _e("Copy the PHP code generated",'acf'); ?></li>
	<li><?php _e("Paste into your functions.php file",'acf'); ?></li>
	<li><?php _e("To activate any Add-ons, edit and use the code in the first few lines.",'acf'); ?></li>
</ol>
<?php

	}
	
	
	/*
	*  html_php
	*
	*  @description: 
	*  @created: 9/08/12
	*/
	
	function html_php()
	{
		
		?>
<div class="wp-box">
	<div class="title">
		<h3><?php _e("Export Field Groups to PHP",'acf'); ?></h3>
	</div>
	<table class="acf_input widefat">
		<tr>
			<td class="label">
<h3><?php _e("Instructions",'acf'); ?></h3>
<ol>
	<li><?php _e("Copy the PHP code generated",'acf'); ?></li>
	<li><?php _e("Paste into your functions.php file",'acf'); ?></li>
	<li><?php _e("To activate any Add-ons, edit and use the code in the first few lines.",'acf'); ?></li>
</ol>

<p><br /></p>

<h3><?php _e("Notes",'acf'); ?></h3>
<p><?php _e("Registered field groups <b>will not</b> appear in the list of editable field groups. This is useful for including fields in themes.",'acf'); ?></p>
<p><?php _e("Please note that if you export and register field groups within the same WP, you will see duplicate fields on your edit screens. To fix this, please move the original field group to the trash or remove the code from your functions.php file.",'acf'); ?></p>


<p><br /></p>

<h3><?php _e("Include in theme",'acf'); ?></h3>
<p><?php _e("The Advanced Custom Fields plugin can be included within a theme. To do so, move the ACF plugin inside your theme and add the following code to your functions.php file:",'acf'); ?></p>

<pre>
include_once('advanced-custom-fields/acf.php');
</pre>

<p><?php _e("To remove all visual interfaces from the ACF plugin, you can use a constant to enable lite mode. Add the following code to your functions.php file <b>before</b> the include_once code:",'acf'); ?></p>

<pre>
define( 'ACF_LITE', true );
</pre>
 
<p><br /></p>

<p><a href="">&laquo; <?php _e("Back to export",'acf'); ?></a></p>
			</td>
			<td>
				<textarea class="pre" readonly="true"><?php
		
		$acfs = array();
		
		if( isset($_POST['acf_posts']) )
		{
			$acfs = get_posts(array(
				'numberposts' 	=> -1,
				'post_type' 	=> 'acf',
				'orderby' 		=> 'menu_order title',
				'order' 		=> 'asc',
				'include'		=>	$_POST['acf_posts'],
				'suppress_filters' => false,
			));
		}
		if( $acfs )
		{
			?>
if(function_exists("register_field_group"))
{
<?php
			foreach( $acfs as $i => $acf )
			{
				// populate acfs
				$var = array(
					'id' => $acf->post_name,
					'title' => $acf->post_title,
					'fields' => apply_filters('acf/field_group/get_fields', array(), $acf->ID),
					'location' => apply_filters('acf/field_group/get_location', array(), $acf->ID),
					'options' => apply_filters('acf/field_group/get_options', array(), $acf->ID),
					'menu_order' => $acf->menu_order,
				);
				
				
				$var['fields'] = apply_filters('acf/export/clean_fields', $var['fields']);


				// create html
				$html = var_export($var, true);
				
				// change double spaces to tabs
				$html = str_replace("  ", "\t", $html);
				
				// correctly formats "=> array("
				$html = preg_replace('/([\t\r\n]+?)array/', 'array', $html);
				
				// Remove number keys from array
				$html = preg_replace('/[0-9]+ => array/', 'array', $html);
				
				// add extra tab at start of each line
				$html = str_replace("\n", "\n\t", $html);
				
				// add the WP __() function to specific strings for translation in theme
				//$html = preg_replace("/'label'(.*?)('.*?')/", "'label'$1__($2)", $html);
				//$html = preg_replace("/'instructions'(.*?)('.*?')/", "'instructions'$1__($2)", $html);
				
								
?>	register_field_group(<?php echo $html ?>);
<?php
			}
?>
}
<?php
		}
		else
		{
			_e("No field groups were selected",'acf');
		}
				?></textarea>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
(function($){
	
	var i = 0;
	
	$(document).on('click', 'textarea.pre', function(){
		
		if( i == 0 )
		{
			i++;
			
			$(this).focus().select();
			
			return false;
		}
				
	});
	
	$(document).on('keyup', 'textarea.pre', function(){
	
	    $(this).height( 0 );
	    $(this).height( this.scrollHeight );
	
	});

	$(document).ready(function(){
		
		$('textarea.pre').trigger('keyup');

	});

})(jQuery);
</script>
	<?php
	}
	
	
	/*
	*  clean_fields
	*
	*  @description: 
	*  @since: 3.5.7
	*  @created: 7/03/13
	*/
	
	function clean_fields( $fields )
	{
		// trim down the fields
		if( $fields )
		{
			foreach( $fields as $i => $field )
			{
				// unset unneccessary bits
				unset( $field['id'], $field['class'], $field['order_no'], $field['field_group'], $field['_name'] );
				
				
				// instructions
				if( !$field['instructions'] )
				{
					unset( $field['instructions'] );
				}
				
				
				// Required
				if( !$field['required'] )
				{
					unset( $field['required'] );
				}
				
				
				// conditional logic
				if( !$field['conditional_logic']['status'] )
				{
					unset( $field['conditional_logic'] );
				}
				
				
				// children
				if( isset($field['sub_fields']) )
				{
					$field['sub_fields'] = apply_filters('acf/export/clean_fields', $field['sub_fields']);
				}
				elseif( isset($field['layouts']) )
				{
					foreach( $field['layouts'] as $l => $layout )
					{
						$field['layouts'][ $l ]['sub_fields'] = apply_filters('acf/export/clean_fields', $layout['sub_fields']);
					}
				}

				
				// override field
				$fields[ $i ] = $field;
			}
		}
		
		return $fields;
	}	
}

new acf_export();

?>