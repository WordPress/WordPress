<?php
/**
 * 
 * eventon tickets front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-tickets/Classes
 * @version     0.1
 */

class evotx_front{
	
	function __construct(){
		add_filter('eventon_eventCard_evotx', array($this, 'frontend_box'), 10, 2);
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 3);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
		
		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 10 );


		// thank you page tickets
		add_action('woocommerce_thankyou', array( $this, 'wc_order_tix' ), 10 ,1);
		add_action('woocommerce_view_order', array( $this, 'wc_order_tix' ), 10 ,1);

		

	}


	// show tickets in front-end customer account pages
		public function wc_order_tix($oid){
			
			$order = new WC_Order( $oid );

			if ( in_array( $order->status, array( 'completed' ) ) ) {

				$tix = new evotx_ticket();

				if($tix->does_order_have_tickets($oid)){

					$tickets = $order->get_items();
					

					if($tickets && count($tickets)>0){

						$customer = get_post_meta($oid, '_customer_user');
						$usermeta = get_user_meta($customer[0]);

						$email_body_arguments = array(
							'orderid'=>$oid,
							'tickets'=>$tickets, 
							'customer'=>$usermeta['first_name'][0].' '.$usermeta['last_name'][0],
							'email'=>''
						);

						$wrapper = "background-color: #e6e7e8;-webkit-text-size-adjust:none !important;margin:0;padding: 20px 20px 20px 20px;";

						$innner = "background-color: #ffffff; -webkit-text-size-adjust:none !important; margin:0;border-radius:5px;";
						
						ob_start();
						?>
						<h2><?php _e('Your event Tickets','eventon');?></h2>
						<div style="<?php echo $wrapper; ?>">
						<div style='<?php echo $innner;?>'>
						<?php
						echo $tix->get_tickets($email_body_arguments);

						echo "</div></div>";

						echo ob_get_clean();
						
					}
				} // does order have tickets
			}

		}

	


	// styles are scripts
		public function load_styles(){
			global $evotx;

			wp_register_script('tx_wc_simple', $evotx->plugin_url.'/assets/tx_wc_simple.js', array('jquery'), 1.0, true);
			wp_enqueue_script('tx_wc_simple');
			wp_register_script('tx_wc_variable', $evotx->plugin_url.'/assets/tx_wc_variable.js', array('jquery'), 1.0, true);
			wp_enqueue_script('tx_wc_variable');
		}
		public function register_styles_scripts(){	
			global $evotx;	
				
			wp_register_style( 'evo_TX_styles',$evotx->plugin_url.'/assets/tx_styles.css');
			//wp_register_script('evo_TX_script',$this->plugin_url.'/assets/tx_script.js', array('jquery'), 1.0, true );	

			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
				
		}
		public function print_scripts(){
			wp_enqueue_script('evo_TX_ease');	
			//wp_enqueue_script('evo_RS_mobile');	
			//wp_enqueue_script('evo_TX_script');	
		}

		function print_styles(){
			wp_enqueue_style( 'evo_TX_styles');	
		}

	// add Ticket box to front end
		function frontend_box($object, $helpers){

			global $evotx, $woocommerce;
			
			$txmeta = get_post_custom($object->event_id);		

			if( !empty($txmeta['evotx_tix']) && $txmeta['evotx_tix'][0]=='yes'):

				// get options array
				$woo_product_id = $txmeta['tx_woocommerce_product_id'][0];
				$woometa = get_post_custom($woo_product_id);

				$opt = $helpers['evoOPT2'];




			ob_start();?>

				<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_tix <?php echo $helpers['end_row_class']?>' data-tx='' data-event_id='<?php echo $object->event_id ?>'>
					<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__evotx_001', 'fa-tags',$helpers['evOPT'] );?>'></i></span>
					<div class='evcal_evdata_cell'>							
						<h3 class='evo_h3'><?php echo eventon_get_custom_language($opt, 'evoTX_001','Ticket Section Title');?></h3>
						<p class='evo_data_val'><?php echo evo_meta($woometa,'_tx_text');?></p>
						<div class='evoTX_wc'>

						<?php

							
							$_pf = new WC_Product_Factory();
							$product = $_pf->get_product( $woo_product_id );

							if ( $product->is_in_stock() ) :

								// SIMPLE product
								if($product->product_type == 'simple'):

									$url = $evotx->addon_data['plugin_path'].'/templates/template-add-to-cart-single.php';
									include($url);
									
								endif; // end simple product

								// VARIABLE Product
								if($product->product_type=='variable'):

									include($evotx->addon_data['plugin_path'].'/templates/template-add-to-cart-variable.php');

								endif;

							else:

								echo "<p class='evotx_soldout'>". eventon_get_custom_language($opt, 'evoTX_012','Sold Out!')."</p>";

							endif; // in_in_stock()	

						?>
							
						</div>
						<?php
							// show remaining tickets or not
							if(!empty($txmeta['_show_remain_tix']) && $txmeta['_show_remain_tix'][0]=='yes' && !empty($woometa['_manage_stock']) && $woometa['_manage_stock'][0]=='yes' && !empty($woometa['_stock'])){
								echo "<p class='evotx_remaining'>".$woometa['_stock'][0]." ".eventon_get_custom_language($opt, 'evoTX_013','Tickets remaining!')."</p>";
							}
						?>
					</div>
					<div class='tx_wc_notic' style='display:none'>
						<p><span><?php echo eventon_get_custom_language($opt, 'evoTX_009','Successfully added to cart!');?></span> <a class='evcal_btn ' href='<?php echo $woocommerce->cart->get_cart_url();?>'><?php echo eventon_get_custom_language($opt, 'evoTX_010','View cart');?></a> <a class='evcal_btn ' href='<?php echo $woocommerce->cart->get_checkout_url();?>'><?php echo eventon_get_custom_language($opt, 'evoTX_011','Checkout');?></a><em></em></p>
					</div>
				<?php echo $helpers['end'];?> 
				</div>


			<?php 
			$output = ob_get_clean();

			return $output;

			endif;
		}
		function eventcard_array($array, $pmv, $eventid){
			$array['evotx']= array(
				'event_id' => $eventid,
				'value'=>'tt'
			);
			return $array;
		}
		function eventcard_adds($array){
			$array[] = 'evotx';

			return $array;
		}
}
new evotx_front();