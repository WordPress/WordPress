<?php
/*
	Event Tickets Admin init
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


include_once('evo-tx_meta_boxes.php');


// Initiate admin for tickets addon
	function evotx_admin_init(){

		// language
		add_filter('eventon_settings_lang_tab_content', 'evotx_language_additions', 10, 1);

		// eventCard inclusion
		add_filter( 'eventon_eventcard_boxes','evotx_add_toeventcard_order' , 10, 1);

		// icon in eventon settings
		add_filter( 'eventon_custom_icons','evotx_custom_icons' , 10, 1);

		global $pagenow, $typenow, $wpdb, $post;	
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = $post->post_type;
	    }
		
		if ( $typenow == '' || $typenow == "ajde_events" ) {

			// Event Post Only
			$print_css_on = array( 'post-new.php', 'post.php' );

			foreach ( $print_css_on as $page ){
				add_action( 'admin_print_styles-'. $page, 'evotx_event_post_styles' );		
			}

			include_once( 'class-ticket-admin.php' );
		}

		// settings
		add_filter('eventon_settings_tabs','evotx_tab_array' ,10, 1);
		add_action('eventon_settings_tabs_evcal_tx','evotx_tab_content' );

		
	}
	add_action('admin_init', 'evotx_admin_init');


// TABS SETTINGS
	function evotx_tab_array($evcal_tabs){
		$evcal_tabs['evcal_tx']='Tickets';		
		return $evcal_tabs;
	}

	function evotx_tab_content(){
		global $eventon;

		$eventon->load_ajde_backender();
				
		?>
			<form method="post" action=""><?php settings_fields('evoau_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_tx" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php

					$site_name = get_bloginfo('name');
					$site_email = get_bloginfo('admin_email');

					$cutomization_pg_array = array(
					
						array(
							'id'=>'evotx','display'=>'show',
							'name'=>'Email Templates',
							'tab_name'=>'Emails',
							'fields'=>array(
								array('type'=>'subheader','name'=>'Event Ticket Email'),
								
								array('id'=>'evotx_notfiemailfromN','type'=>'text','name'=>'"From" Name','default'=>$site_name),
								array('id'=>'evotx_notfiemailfrom','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
								
								array('id'=>'evotx_notfiesubjest','type'=>'text','name'=>'Email Subject line','default'=>'Event Ticket'),
								array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
								array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email template copy "eventon-tickets/templates/ticket_confirmation_email.php" to  "yourtheme/eventon/templates/email/ticket_confirmation_email.php.'),
						)),
					);
					
					
								
					$eventon->load_ajde_backender();		
					
					$evcal_opt = get_option('evcal_options_evcal_tx');


					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);
							
						
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
				<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
			</div>
			
			</form>	
		<?php
	}


// other hooks
	function evotx_event_post_styles(){
		global $evotx;
		wp_enqueue_style( 'evotx_admin_post',$evotx->plugin_url.'/assets/admin_evotx_post.css');
		wp_enqueue_script( 'evotx_admin_post_script',$evotx->plugin_url.'/assets/tx_admin_post_script.js');
		wp_localize_script( 
			'evotx_admin_post_script', 
			'evotx_admin_ajax_script', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
				'postnonce' => wp_create_nonce( 'evotx_nonce' )
			)
		);
	}
// event tickets to eventcard
	function evotx_add_toeventcard_order($array){
		$array['evotx']='<p val="evotx">Event Ticket Box</p>';

		//print_r($array);
		return $array;
	}
// even tticket eventcard icons
	function evotx_custom_icons($array){
		$array[] = array('id'=>'evcal__evotx_001','type'=>'icon','name'=>'Event Ticket Icon','default'=>'fa-tags');
		return $array;
	}	



// language settings additinos
	function evotx_language_additions($_existen){
		$new_ar = array(
			array('type'=>'togheader','name'=>'Event Tickets'),
				array('label'=>'Ticket section title', 'name'=>'evoTX_001', 'legend'=>''),	
				array('label'=>'Add to Cart', 'name'=>'evoTX_002', 'legend'=>''),

				array('label'=>'Successfully Added to Cart!', 'name'=>'evoTX_009', 'legend'=>''),
				array('label'=>'Checkout', 'name'=>'evoTX_010', 'legend'=>''),
				array('label'=>'View Cart', 'name'=>'evoTX_011', 'legend'=>''),
				array('label'=>'Sold Out!', 'name'=>'evoTX_012', 'legend'=>'Out of stock for tickets'),
				array('label'=>'Tickets remaining!', 'name'=>'evoTX_013',),


				array('label'=>'Ticket #', 'name'=>'evoTX_003', 'legend'=>''),
				array('label'=>'Primary Ticket Holder', 'name'=>'evoTX_004', 'legend'=>''),
				array('label'=>'Quantity', 'name'=>'evoTX_005', 'legend'=>''),
				array('label'=>'Ticket Type', 'name'=>'evoTX_006', 'legend'=>''),
				array('label'=>'We look forward to seeing you!', 'name'=>'evoTX_007', 'legend'=>''),
				array('label'=>'Contact us for questions and concerns', 'name'=>'evoTX_008', 'legend'=>''),
				
			array('type'=>'togend'),
		);
		return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}

	
