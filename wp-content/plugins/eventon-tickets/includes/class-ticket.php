<?php
/**
 * 
 * eventon tickets front and admin class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-tickets/Classes
 * @version     0.1
 */

class evotx_ticket{
	
	function __construct(){
		//add_action('woocommerce_payment_complete', array($this, 'custom_process'), 10, 1);
		//add_action('woocommerce_order_status_completed', array($this, 'custom_process'), 10, 1);
		add_action('woocommerce_checkout_order_processed', array($this, 'custom_process'), 10, 1);
		//add_action('woocommerce_order_status_changed', array($this, 'order_status_change'), 10, 3);

		if ( is_admin() ){
			add_action("admin_init", array($this, "_evo_tx_remove_box"));

			add_filter( 'manage_edit-evo-tix_columns', array($this,'evo_tx_edit_event_columns') );
			add_action('manage_evo-tix_posts_custom_column', array($this,'evo_tx_custom_event_columns'), 2 );

			add_filter( 'manage_edit-evo-tix_sortable_columns', array($this,'ticket_sort') );
			add_filter( 'request', array($this,'ticket_order') );

			add_action( 'add_meta_boxes',  array($this,'add_meta_box') );
	   	}	
	}

	
	
	// when order payment is completed
		public function custom_process($order_id){
			
			$order = new WC_Order( $order_id );	
		    $items = $order->get_items();
		    

		    // add eventon ticket type value to order to later identify order easily
		    update_post_meta($order_id, '_order_type','evotix');
		    
		    foreach ($items as $item) {

		    	//check if this order item is for evo tickets order
		    	$eid = get_post_meta( $item['product_id'], '_eventid', true);

		    	// if event id exist
		    	if(!empty($eid)){

		    		$myuser_id = (int)$order->user_id;
		    		$usermeta = get_user_meta($myuser_id);

		        	// create new event ticket post
					if($created_tix_id = $this->create_post()){

						// variation product
							if(!empty($item['variation_id'])){
								$_product = new WC_Product_Variation($item['variation_id'] );
			        			$hh= $_product->get_variation_attributes( );

			        			foreach($hh as $f=>$v){
			        				$type = $v;
			        			}
			        		}else{ $type = 'Normal'; }

			        	// ticket ID(s)
			        	$tid = $created_tix_id.'-'.$order_id.'-'.( !empty($item['variation_id'])? $item['variation_id']: $item['product_id']);
						if($item['qty']>1){
							$_tid='';
							$str = 'A';
							for($x=0; $x<$item['qty']; $x++){
								$strng = ($x==0)? $str: ++$str;
								$_tid .= $tid.$strng. (($x+1==$item['qty'])?null:', ');
							}
							$tid = (!empty($_tid)? $_tid: $tid);
						}
		        	
						// save rsvp data								
						$this->create_custom_fields($created_tix_id, 'name', $usermeta['first_name'][0].' '.$usermeta['last_name'][0]);
						$this->create_custom_fields($created_tix_id, 'email', $usermeta['billing_email'][0]);
						$this->create_custom_fields($created_tix_id, 'qty', $item['qty'][0]);					
						$this->create_custom_fields($created_tix_id, 'cost', $order->get_line_subtotal($item) );					
						$this->create_custom_fields($created_tix_id, 'type', $type);
						$this->create_custom_fields($created_tix_id, 'tid', $tid);
						$this->create_custom_fields($created_tix_id, 'wcid', $item['product_id']);
						$this->create_custom_fields($created_tix_id, 'tix_status', 'none');
						$this->create_custom_fields($created_tix_id, '_eventid', $eid);
						$this->create_custom_fields($created_tix_id, '_orderid', $order_id);
						$this->create_custom_fields($created_tix_id, '_customerid', $myuser_id);
						
						// save event ticket id to order id
							$tixids = get_post_meta($order_id, '_tixids', true);
							$tixids[] = $tid;
							update_post_meta($order_id, '_tixids', $tixids);
					}
				}
		    }
			
			
		}

	// Support functions
		function create_post() {
			
			// tix post status
			$opt_draft = 'publish'; 
				
	        $type = 'evo-tix';
	        $valid_type = (function_exists('post_type_exists') &&  post_type_exists($type));

	        if (!$valid_type) {
	            $this->log['error']["type-{$type}"] = sprintf(
	                'Unknown post type "%s".', $type);
	        }
	       
	        $date = $this->get_event_post_date();
	        $title = 'TICKET '.date('M d Y @ h:i:sa', time());

	        $new_post = array(
	            'post_title'   => $title,
	            'post_status'  => $opt_draft,
	            'post_type'    => $type,
	            'post_date'    => $date,
	            'post_name'    => sanitize_title($title),
	            'post_author'  => $this->get_author_id(),
	        );
	       
	        // create!
	        $id = wp_insert_post($new_post);
	       
	        return $id;
	    }
		function create_custom_fields($post_id, $field, $value) {       
	        add_post_meta($post_id, $field, $value);
	    }
	    function update_custom_fields($post_id, $field, $value) {       
	        update_post_meta($post_id, $field, $value);
	    }
    	function get_author_id() {
			$current_user = wp_get_current_user();
	        return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
	    }	
	    function get_event_post_date() {
	        return date('Y-m-d H:i:s', time());        
	    }

	// additions

	    // tickets body for emails
			function get_ticket_email_body($args){
				global $eventon;

				ob_start();
				echo $eventon->get_email_part('header');

				echo $this->get_tickets($args, true);

				echo $eventon->get_email_part('footer');

				return ob_get_clean();
			}
		
		// reusable tickets HTML for an order
			function get_tickets($tix, $email=false){

				global $eventon, $evotx;

				$file = 'ticket_confirmation_email';
				$path = $evotx->addon_data['plugin_path']."/templates/";

				$args = array($tix, $email);

				$message = $eventon->get_email_body($file,$path, $args);

				return $message;

			}

		// get event date
			public function _event_date($post_meta, $DATE_start_val='', $DATE_end_val=''){

				global $eventon;		
				
				$evcal_lang_allday = eventon_get_custom_language( '','evcal_lang_allday', 'All Day');

				$date_array = $eventon->evo_generator->generate_time_($DATE_start_val,$DATE_end_val, $post_meta, $evcal_lang_allday);
				
				return $date_array;
			}

		// get ticket id from order id
			public function get_tixid_by_orderid($order_id, $product_code, $qty){

				$__tids = get_post_meta($order_id, '_tixids', true);

				$_code_mid = $order_id.'-'.$product_code;

				$tixid='';

				//find ticket id to append to front
				foreach($__tids as $__tid){
					$__tid_1 = explode(',', $__tid);
					//
					if(strpos($__tid_1[0], $_code_mid)){
						$tt = explode('-', $__tid_1[0]);					
						$tixid = $tt[0].'-';
					}
				}

				$code = $tixid.$_code_mid;

				if($qty>1){
					$_tid='';
					$str = 'A';
					for($x=0; $x<$qty; $x++){
						$strng = ($x==0)? $str: ++$str;
						$_tid .= $code.$strng. (($x+1==$qty)?null:', ');
					}
					$code = (!empty($_tid)? $_tid: $code);
				}

				return $code;

			}

	// check if an order have event tickets
		public function does_order_have_tickets($order_id){
			$meta = get_post_meta($order_id, '_tixids', true);

			return (!empty($meta))? true: false;
		}

	// Event Ticket Edit POST page
	    function _evo_tx_remove_box(){
	    	remove_post_type_support('evo-tix', 'title');
	   		remove_post_type_support('evo-tix', 'editor');
	    }
	    // columns for evo-tix
			function evo_tx_edit_event_columns( $existing_columns ) {
				global $eventon;
				
				// GET event type custom names
				
				if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
					$existing_columns = array();

				unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

				$columns = array();
				$columns["cb"] = "<input type=\"checkbox\" />";	

				$columns['tix'] = __( 'Ticket', 'eventon' );
				$columns["qty"] = __( 'Quantity', 'eventon' );
				$columns["cost"] = __( 'Subtotal', 'eventon' );
				$columns["event"] = __( 'Event', 'eventon' );
				$columns["type"] = __( 'Ticket Type', 'eventon' );				
				$columns["status"] = __( 'Status', 'eventon' );				
				

				return array_merge( $columns, $existing_columns );
			}

		// field values
			function evo_tx_custom_event_columns( $column ) {
				global $post, $eventon;

				//if ( empty( $ajde_events ) || $ajde_events->id != $post->ID )
					//$ajde_events = get_product( $post );

				$meta = get_post_meta($post->ID);

				switch ($column) {		
					case "tix":
						$edit_link = get_edit_post_link( $post->ID );
						$tid = (!empty($meta['tid']))? $meta['tid'][0]: null;

						echo "<strong><a class='row-title' href='".$edit_link."'>#{$post->ID}</a></strong> by ".$meta['name'][0]." ".$meta['email'][0]; 
						echo (!empty($tid))? '<br/>Ticket ID(s): <i>'.$tid.'</i>':null;
						//echo get_post_meta($post->ID, 'tix', true);
					break;
					case "event":
						$e_id = (!empty($meta['_eventid']))? $meta['_eventid'][0]: null;

						if($e_id){
							$edit_link = get_edit_post_link( $e_id );
							$title = get_the_title($e_id);
							
							echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
						}else{ echo '--';}

					break;
					case "type":
						$type = get_post_meta($post->ID, 'type', true);						
						echo (!empty($type))? $type: '-';

					break;
					case "qty":
						$qty = get_post_meta($post->ID, 'qty', true);						
						echo (!empty($qty))? $qty: '-';

					break;
					case "cost":
						$qty = get_post_meta($post->ID, 'cost', true);						
						echo (!empty($qty))? get_woocommerce_currency_symbol().apply_filters('woocommerce_get_price', $qty): '-';

					break;
					case "status":
						$_orderid = get_post_meta($post->ID, '_orderid', true);	

						if(!empty($_orderid)){	
							$order = new WC_Order( $_orderid );
							echo $order->status;
						}else{ echo '-';}

					break;				
						
				}
			}
	
	// event ticket meta box
		function add_meta_box(){
			add_meta_box('evo_mb1','Event Ticket', array($this,'metabox'),'evo-tix', 'normal', 'high');	
		}
		function metabox(){
			global $post;
			$pmv = get_post_meta($post->ID);

			//print_r($pmv);
?>	
<div class='eventon_mb' style='margin:-6px -12px -12px'>
<div style='background-color:#ECECEC; padding:15px;'>
	<div style='background-color:#fff; border-radius:8px;'>
	<table width='100%' class='evo_metatable' cellspacing="">
		<tr><td>Woocommerce Order ID #: </td><td><?php echo '<a href="'.get_edit_post_link($pmv['_orderid'][0]).'">'.$pmv['_orderid'][0].'</a>';?></td></tr>
		<tr><td>Ticket(s) #: </td><td><?php echo $pmv['tid'][0];?></td></tr>
		<tr><td>Ticket Type: </td><td><?php echo $pmv['type'][0];?></td></tr>
		<tr><td>Ticket Holder: </td><td><?php echo '<a href="'.get_edit_user_link($pmv['_customerid'][0]).'">'.$pmv['name'][0].'</a>';?></td></tr>
		<tr><td>Email Address: </td><td><?php echo $pmv['email'][0];?></td></tr>
		<tr><td>Quantity: </td><td><?php echo $pmv['qty'][0];?></td></tr>
		<tr><td>Cost for ticket(s): </td><td><?php echo get_woocommerce_currency_symbol().$pmv['cost'][0];?></td></tr>
		<tr><td>Event: </td><td><?php echo '<a href="'.get_edit_post_link($pmv['_eventid'][0]).'">'.get_the_title($pmv['_eventid'][0]).'</a>';?></td></tr>
		<?php
			$status = (!empty($pmv['status']))? $pmv['status'][0]: 'not checked-in';
		?>
		<tr><td>Ticket Status: </td><td><?php echo $status;?></td></tr>
		
	</table>
	</div>
</div>
</div>
<?php


		}

	// make ticket columns sortable
		function ticket_sort($columns) {
			$custom = array(
				'event'		=> 'event',
			);
			return wp_parse_args( $custom, $columns );
		}
		function ticket_order( $vars ) {
			if (isset( $vars['orderby'] )) :
				if ( 'event' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_eventid',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				
			endif;

			return $vars;
		}

}
new evotx_ticket();