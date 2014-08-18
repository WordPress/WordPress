<?php
/**
	Tab that has advanced extra settings that should only be editted by people that know what they are doing.
**/

if( isset($_POST['evcal_noncename_extra']) && isset( $_POST ) ){
	if ( wp_verify_nonce( $_POST['evcal_noncename_extra'], AJDE_EVCAL_BASENAME ) ){
		
		if( isset($_POST['eventon_events_page_id']) ){
			add_option('eventon_events_page_id',
				strip_tags(stripslashes ($_POST['eventon_events_page_id']) ) );
				
		}
	}
}

?>
<div id="extra" class="postbox evcal_admin_meta">	
	
	<div class="inside">
		<h2><?php _e('Advnaced Settings','eventon');?></h2>
		
		<table width='100%'>
			<tr><td >
				<?php
					$event_pg = get_option('eventon_events_page_id');					
				?>
				<form method="post" action=""><?php settings_fields('evcal_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename_extra' );
				?>
				<p>Events page ID <i>(option variable name =eventon_events_page_id)</i>: <strong><?php 
				
					if(empty($event_pg)){
						
						echo "<select name='eventon_events_page_id'>";
						
						$pages = new WP_Query(array('post_type'=>'page'));
						
						while($pages->have_posts()	){ $pages->the_post();
												
							$page_id = get_the_ID();
							echo "<option value='".$page_id."'>".get_the_title($page_id)."</option>";
						}
						
						echo "</select>";
						
					}else{
						echo $event_pg;
					}
				
				?></strong></p>
				<p>EventON Plugin version: <strong><?php echo get_option('eventon_plugin_version');?></strong></p>
				
				<?php if(empty($event_pg)):?>
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				<?php endif;?>
				</form>
			</tr>		
		</table>			
	</div>
</div>
