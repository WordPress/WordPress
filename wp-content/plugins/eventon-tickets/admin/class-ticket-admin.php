<?php
/**
 * 
 * eventon tickets admin class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-tickets/Classes
 * @version     0.1
 */

class evotx_admin{
	private $addon_data;
	private $urls;
	private $evotx_opt;

	public $evotx_email_vals;
	public $test = 22;

	function __construct(){
		// HOOKs		
		add_action('eventon_save_meta', array(&$this, 'save_ticket_info'), 10, 2);
		
		$this->evotx_opt = get_option('evcal_options_evcal_tx');

		add_filter('evo_event_columns', array($this, 'add_column_title'), 10, 1);
		add_filter('evo_column_type_woo', array($this, 'column_content'), 10, 1);

		// actions when event moved to trash that have wc product
		add_action('wp_trash_post', array($this, 'move_to_trash'));
	}


	// GET attendee list per customer name
		public function get_attendees($product_id){
			ob_start();

			global $wpdb;

			$customers = array();


			$tickets = new WP_Query(array(
				'post_type'=>'evo-tix',
				'meta_key'=>'wcid',
				'meta_query' => array(
			       array(
			           'key' => 'wcid',
			           'value' => $product_id,
			       )
			   )
			));

			while($tickets->have_posts()): $tickets->the_post();
				$tixid = get_the_ID();
				$pmv = get_post_custom($tixid);

				$order_id = !empty($pmv['_orderid'])? $pmv['_orderid'][0]: false;

				if(!$order_id)
					continue;

				$order = new WC_Order( $order_id);
				if ( in_array( $order->status, array( 'completed' ) ) ) {
					$usermeta = get_user_meta($pmv['_customerid'][0]);

					$customers[$pmv['tid'][0]] = array( 
						'name'=> $usermeta['first_name'][0].' '.$usermeta['last_name'][0],
						'email'=>$usermeta['billing_email'][0],
						'qty'=>$pmv['qty'][0],
						'tid'=>$pmv['tid'][0],
						'type'=>$pmv['type'][0]
					);
				}

			endwhile;
			wp_reset_postdata();

			return $customers;

		}



	// save new ticket and create matching WC product
		public function save_ticket_info($arr, $post_id){			

			// if allowing woocommerce online odering
			if(!empty($_POST['evotx_tix']) && $_POST['evotx_tix']=='yes'){
				// check if woocommerce product id exist
				if(!empty($_POST['tx_woocommerce_product_id'])){
	
					$post_exists = $this->post_exist($_POST['tx_woocommerce_product_id']);					
					// add new
					if(!$post_exists){
						$this->add_new_woocommerce_product($post_id);
					}else{
						$this->update_woocommerce_product($_POST['tx_woocommerce_product_id'], $post_id);
					}	
				// if there isnt a woo product associated to this - add new one
				}else{
					$this->add_new_woocommerce_product($post_id);
				}
			}

			if(!empty($_POST['evotx_tix'])){
				update_post_meta( $post_id, 'evotx_tix',$_POST['evotx_tix']);
			}
			if(!empty($_POST['_show_remain_tix'])){
				update_post_meta( $post_id, '_show_remain_tix',$_POST['_show_remain_tix']);
			}


		}

	// ADD NEW
		function add_new_woocommerce_product($post_id){
			$user_ID = get_current_user_id();

			$post = array(
				'post_author' => $user_ID,
				'post_content' => (!empty($_REQUEST['_tx_desc']))? $_REQUEST['_tx_desc']: "Event Ticket",
				'post_status' => "publish",
				'post_title' => 'Ticket: '.$_REQUEST['post_title'],
				'post_type' => "product"
			);

			// create woocommerce product
			$woo_post_id = wp_insert_post( $post );
			if($woo_post_id){
				
				//wp_set_object_terms( $woo_post_id, $product->model, 'product_cat' );
				wp_set_object_terms($woo_post_id, $_REQUEST['tx_product_type'], 'product_type');
				

				update_post_meta( $post_id, 'tx_woocommerce_product_id', $woo_post_id);
				$this->save_product_meta_values($woo_post_id, $post_id);

				// add category 
				$this->assign_woo_cat($woo_post_id);
			}


		}

	// UPDATE
		function update_woocommerce_product($woo_post_id, $post_id){
			$user_ID = get_current_user_id();

			$post = array(
				'ID'=>$woo_post_id,
				'post_author' => $user_ID,
				'post_content' => (!empty($_REQUEST['_tx_desc']))? $_REQUEST['_tx_desc']: "Event Ticket",
				'post_status' => "publish",
				'post_title' => $_REQUEST['post_title'],
				'post_type' => "product",				
			);

			// create woocommerce product
			$woo_post_id = wp_update_post( $post );
			
			//update_post_meta( $post_id, 'tx_woocommerce_product_id', $woo_post_id);
			//wp_set_object_terms( $woo_post_id, $product->model, 'product_cat' );

			wp_set_object_terms($woo_post_id, $_POST['tx_product_type'], 'product_type');		

			$this->save_product_meta_values($woo_post_id, $post_id);
			
		}
			// Fcnt save values
			function save_product_meta_values($woo_post_id, $post_id){

				$update_metas = array(	
					'_sku'=>'_sku',
					'_regular_price'=>'_regular_price',
					'_sale_price'=>'_sale_price',
					'_price'=>'_price',
					'_visibility'=>'hidden',
					'_stock_status'=>'_stock_status',
					'_sold_individually'=>'_sold_individually',
					'_manage_stock'=>'_manage_stock',
					'_stock'=>'_stock',
					'_backorders'=>'_backorders',
					'evotx_price'=>'_regular_price',
					'_tx_desc'=>'_tx_desc',
					'_tx_text'=>'_tx_text',
					'_eventid'=>$post_id,
				);

				foreach($update_metas as $umeta=>$umetav){
					if($umeta == '_regular_price' || $umeta == '_sale_price'|| $umeta == '_price'){

						if($umeta == '_price'){
							update_post_meta($woo_post_id, $umeta, str_replace("$","",$_POST['_regular_price']) );
						}else{

							if(isset($_POST[$umetav]))
								update_post_meta($woo_post_id, $umeta, str_replace("$","",$_POST[$umetav]) );
						}
					}else if($umeta == '_eventid'){
						update_post_meta($woo_post_id, $umeta, $post_id);
					}else if($umeta == '_visibility'){
						update_post_meta($woo_post_id, $umeta, $umetav);
					}else if($umeta == 'evotx_price'){

						$__price = (!empty($_POST[$umetav]))? $_POST[$umetav]: ' ';

						update_post_meta($post_id, $umeta, $__price);
					}else{
						if(isset($_POST[$umetav]))
							update_post_meta($woo_post_id, $umeta, $_POST[$umetav]);
					}
				}
			}


		// create and assign woocommerce product category for foodpress items
			function assign_woo_cat($post_id){

				// check if term exist
				$terms = term_exists('Ticket', 'product_cat');
				if(!empty($terms) && $terms !== 0 && $terms !== null){
					wp_set_post_terms( $post_id, $terms, 'product_cat' );
				}else{
					// create term
					$new_termid = wp_insert_term(
					  	'Ticket', // the term 
					  	'product_cat',
					  	array(
					  		'slug'=>'ticket'
					 	)
					);

					// assign term to woo product
					wp_set_post_terms( $post_id, $new_termid, 'product_cat' );
				}
				
			}

	// SUPPORT
		// check if post exist for a ID
			function post_exist($ID){
				global $wpdb;

				$post_id = $ID;
				$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
				return $post_exists;
			}
		// add new column to menu items
			function add_column_title($columns){
				$columns['woo']= '<i title="Connected to woocommerce">Woo</i>';
				return $columns;
			}
			function column_content($post_id){
				$__woo = get_post_meta($post_id, 'tx_woocommerce_product_id', true);
				$__wo_perma = (!empty($__woo))? get_edit_post_link($__woo):null;
				$content = (!empty($__woo))?'<a href="'.$__wo_perma.'">Yes</a>':'No';
				return $content;
			}
		
	    // move a menu items to trash
		    function move_to_trash($post_id){
		    	$post_type = get_post_type( $post_id );
		    	$post_status = get_post_status( $post_id );
		    	if($post_type == 'ajde_events' && in_array($post_status, array('publish','draft','future')) ){
		    		$woo_product_id = get_post_meta($post_id, 'tx_woocommerce_product_id', true);

		    		if(!empty($woo_product_id)){
		    			$__product = array(
		    				'ID'=>$woo_product_id,
		    				'post_status'=>'trash'
		    			);
		    			wp_update_post( $__product );
		    		}	
		    	}

		    }

}

$GLOBALS['evotx_admin'] = new evotx_admin();