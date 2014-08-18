<?php
/**
 * Event Tickets Ajax Handletx
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-TX/Functions/AJAX
 * @vetxion     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**	AJAX	 */

// get attendeed count
	function evotx_get_attendees(){
		
		$nonce = $_POST['postnonce'];
		$status = 0;
		$message = $content = '';

		if(! wp_verify_nonce( $nonce, 'evotx_nonce' ) ){
			$status = 1;	$message ='Invalid Nonce';
			
		}else{

			global $evotx_admin;

			$customers = $evotx_admin->get_attendees($_POST['wcid']);

			// customers with completed orders
			if(!empty($customers)){


				echo "<div class='evotx'>";
				echo "<p class='header'>Attendee Name <span class='txcount'>Ticket Count</span></p>";

				foreach($customers as $cus){

					$tid = get_post_meta($cus['tid'], 'status', true);
					$_status = (!empty($tid))? $tid: 'check-in';
					echo "<p class='attendee'><span class='txcount'>{$cus['qty']}</span><span class='checkin ".( $_status=='checked'? 'checked':null)."' data-tid='".$cus['tid']."' data-status='".$_status."'>".$_status."</span>{$cus['name']} ({$cus['email']}) - <b>{$cus['type']}</b> <br/><span class='tixid'>Ticket #{$cus['tid']}</span></p>";
					
				}
				echo "</div>";
			}else{
				echo "<div class='evotx'>";
				echo "<p class='header nada'>Could not find attendees with completed orders.</p>";	
				echo "</div>";
			}
			
			$content = ob_get_clean();
		}
				
		$return_content = array(
			'message'=> $message,
			'status'=>$status,
			'content'=>$content,
		);
		
		echo json_encode($return_content);		
		exit;
	}
	add_action('wp_ajax_the_ajax_evotx_a1', 'evotx_get_attendees');
	add_action('wp_ajax_nopriv_the_ajax_evotx_a1', 'evotx_get_attendees');


// CHECK in attendee
	function evoTX_checkin(){

		$tid = $_POST['tid'];
		$status = $_POST['status'];

		update_post_meta($tid, 'status',$status);

		$return_content = array(
			'status'=>'0'
		);
		
		echo json_encode($return_content);		
		exit;
	}
	add_action('wp_ajax_the_ajax_evotx_a4', 'evoTX_checkin');
	add_action('wp_ajax_nopriv_the_ajax_evotx_a4', 'evoTX_checkin');

// Download csv list of attendees
	function evoTX_generate_csv(){

		$e_id = $_REQUEST['e_id'];
		$event = get_post($e_id, ARRAY_A);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$event['post_name']."_".date("d-m-y").".csv");
		header("Pragma: no-cache");
		header("Expires: 0");


		global $evotx_admin;
		$customers = $evotx_admin->get_attendees($_REQUEST['pid']);

		if(!empty($customers)){

			//$fp = fopen('file.csv', 'w');

			echo "Name, Email Address, Ticket IDs, Quantity, Ticket Type\n";

			foreach($customers as $cus){
				$tids = str_replace(", ", "/ ", $cus['tid']);
				echo $cus['name'].",".$cus['email'].",".$tids.",".$cus['qty'].",".$cus['type']."\n";
			}

			
		}

	}
	add_action('wp_ajax_the_ajax_evotx_a3', 'evoTX_generate_csv');
	add_action('wp_ajax_nopriv_the_ajax_evotx_a3', 'evoTX_generate_csv');

// ADD to cart for variable items
	function evotx_woocommerce_ajax_add_to_cart() {
		 global $woocommerce;
		 //check_ajax_referer( 'add-to-cart', 'security' );
		 $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		 $variation_id     = apply_filters( 'woocommerce_add_to_cart_variation_id', absint( $_POST['variation_id'] ) );
		 $quantity          = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
		 $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		 


		 // variations	 
		 $variation= apply_filters( 'woocommerce_add_to_cart_variation',  $_POST['variable_name']  );
		 //$attri=$_POST['attri'];

		if(isset($_POST['variations'])){
			$att=array();
			foreach($_POST['variations'] as $varF=>$varV){
				$att[$varF]=$varV;
			}
		}

		
		 if ($variation_id > 0){
			$woocommerce->cart->add_to_cart( $product_id, $quantity, $variation_id ,$att);
			 
			do_action( 'woocommerce_ajax_added_to_cart', $product_id ,$quantity, $variation_id ,$variation);

			// Return fragments
			$frags = new WC_AJAX( );
        	$frags->get_refreshed_fragments( );

			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
			 	woocommerce_add_to_cart_message( $product_id );
			 	$woocommerce->set_messages();
			}

		 }else{
		 
			if ( $passed_validation && $woocommerce->cart->add_to_cart( $product_id, $quantity) ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				 
				if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				 	woocommerce_add_to_cart_message( $product_id );
				 	$woocommerce->set_messages();
				}
				 
				// Return fragments
				$frags = new WC_AJAX( );
        		$frags->get_refreshed_fragments( );
			 
			} else {
			 
				header( 'Content-Type: application/json; charset=utf-8' );
				 
				// If there was an error adding to the cart, redirect to the product page to show any errors
				$data = array(
				 	'error' => true,
				 	'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
				);
				 
				$woocommerce->set_messages();
				 
				echo json_encode( $data );
			 
			}
			die();
		}
	 }
	 add_action('wp_ajax_evotx_woocommerce_add_to_cart', 'evotx_woocommerce_ajax_add_to_cart');
	 add_action('wp_ajax_nopriv_evotx_woocommerce_add_to_cart', 'evotx_woocommerce_ajax_add_to_cart');


?>