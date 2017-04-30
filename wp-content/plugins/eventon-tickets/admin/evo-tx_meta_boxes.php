<?php
/**
 * Ticket meta boxes for event page
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/evo-tix
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Init the meta boxes. */
	function evotx_meta_boxes(){

		add_meta_box('evotx_mb1','Event Tickets', 'evotx_metabox_content','ajde_events', 'normal', 'high');	
		
		do_action('evotx_add_meta_boxes');
	}
	add_action( 'add_meta_boxes', 'evotx_meta_boxes' );


	
/**  Meta box for tickets  */	
	function evotx_metabox_content(){
		global $post, $evotx, $eventon;
		$woometa='';

		$fmeta = get_post_meta($post->ID);
		$woo_product = (!empty($fmeta['tx_woocommerce_product_id']))? $fmeta['tx_woocommerce_product_id'][0]:null;

		// if woocommerce ticket has been created
		if($woo_product){
			$woometa =  get_post_custom($woo_product);
		}
		$__woo_currencySYM = get_woocommerce_currency_symbol();

		ob_start();

		$evotx_tix = (!empty($fmeta['evotx_tix']))? $fmeta['evotx_tix'][0]:null;

		

		// TESTINGs
		

		//$evotx->sync_tix_count($post->ID);
		?>
		<div class='eventon_mb'>
		<div class="evotx">
			<input type='hidden' name='tx_woocommerce_product_id' value="<?php echo evo_meta($fmeta, 'tx_woocommerce_product_id');?>"/>

			<p class='yesno_leg_line ' style='padding:10px'>
				<?php echo eventon_html_yesnobtn(array('id'=>'evotx_activate','var'=>$evotx_tix, 
					'attr'=>array('afterstatement'=>'evotx_details'))); ?>				
				<input type='hidden' name='evotx_tix' value="<?php echo ($evotx_tix=='yes')?'yes':'no';?>"/>
				<label for='evotx_tix'><?php _e('Activate tickets for this Event','eventon'); echo $eventon->throw_guide('You can allow ticket selling via Woocommerce for this event in here.','',false); ?></label>
			</p>
			<div id='evotx_details' class='evotx_details evomb_body ' <?php echo ( $evotx_tix=='yes')? null:'style="display:none"'; ?>>
				
				
				<div class="evotx_tickets" >
				
					<h4>Ticket Info for this event</h4>
					<table width='100%' border='0' cellspacing='0'>
						<?php
							// product type
							if($woo_product && function_exists('get_product')):
								$product = get_product( $woo_product );
						?>
							<tr><td>Ticket Pricing Type</td><td><?php echo  $product->product_type;?></td></tr>
						<?php endif;?>

						<input type='hidden' name='tx_product_type' value='<?php echo (!empty($product->product_type))? $product->product_type:'simple';?>'/>

						<!-- Price-->
						<?php if(!empty($product->product_type) && $product->product_type=='variable'):?>
							<tr><td><?php printf( __('Ticket price (%s)','eventon'), $__woo_currencySYM);?></td><td><p><?php echo $__woo_currencySYM.' '.evo_meta($woometa, '_min_variation_price').' - '.evo_meta($woometa, '_max_variation_price');?></p>
							<p class='marb20'><a href='<?php echo get_edit_post_link($woo_product);?>' style='color:#fff'><?php _e('Edit Price Variations')?></a></p></td></tr>				
							
						<?php else:?>
							<!-- Regular Price-->
							<tr><td><?php printf( __('Ticket price (%s)','eventon'), $__woo_currencySYM);?></td><td><input type='text' id='_regular_price' name='_regular_price' value="<?php echo evo_meta($woometa, '_regular_price');?>"/></td></tr>

							<!-- Sale Price-->
							<tr><td><?php printf( __('Sale price (%s)','eventon'), $__woo_currencySYM);?></td><td><input type='text' id='_sale_price' name='_sale_price' value="<?php echo evo_meta($woometa, '_sale_price');?>"/></td></tr>
						<?php endif;?>			
						


						<!-- SKU-->
						<tr><td><?php _e('Ticket SKU', 'eventon'); echo $eventon->throw_guide('SKU refers to a Stock-keeping unit, a unique identifier for each distinct menu item that can be ordered.','',false);?></td><td><input type='text' name='_sku' value='<?php echo evo_meta($woometa, '_sku');?>'/></td></tr>

						

						<!-- Desc-->
						<tr><td><?php _e('Short Ticket Detail', 'eventon'); ?></td><td><input type='text' name='_tx_desc' value='<?php echo evo_meta($woometa, '_tx_desc');?>'/></td></tr>
						
						<!-- manage capacity -->
						<?php
							$_manage_cap = evo_meta_yesno($woometa,'_manage_stock','yes','yes','no' );
						?>
						<tr><td colspan='2'>
							<p class='yesno_leg_line ' >
								<?php echo eventon_html_yesnobtn(array('id'=>'evotx_mcap',
								'var'=>$_manage_cap, 'attr'=>array('afterstatement'=>'exotc_cap'))); ?>
								<input type='hidden' name='_manage_stock' value="<?php echo $_manage_cap;?>"/>
								<label for='_manage_stock'><?php _e('Manage Capacity')?></label>
							</p>
						</td></tr>
						
						<tbody id='exotc_cap' style='display:<?php echo evo_meta_yesno($woometa,'_manage_stock','yes','','none' );?>'>
						<tr ><td>Capacity</td><td><input type='text' id="_stock" name="_stock" value="<?php echo evo_meta($woometa, '_stock');?>"/></td></tr>
						<!-- show remaining -->
						<?php
							$remain_tix = evo_meta_yesno($fmeta,'_show_remain_tix','yes','yes','no' );
						?>
						<tr><td colspan='2'>
							<p class='yesno_leg_line ' >
								<?php echo eventon_html_yesnobtn(array('id'=>'evotx_mcap',
								'var'=>$_manage_cap, )); ?>
								<input type='hidden' name='_show_remain_tix' value="<?php echo $_manage_cap;?>"/>
								<label for='_show_remain_tix'><?php _e('Show remaining tickets'); echo $eventon->throw_guide('This will show remaining tickets for this event on front-end','',false)?></label>
							</p>
						</td></tr>
						</tbody>
							

						
						<!-- sold individually -->
						<?php
							$_sold_ind = evo_meta_yesno($woometa,'_sold_individually','yes','yes','no' );
						?>
						<tr><td colspan='2'>
							<p class='yesno_leg_line ' >
								<?php echo eventon_html_yesnobtn(array('id'=>'evotx_mcap','var'=>$_sold_ind,)); ?>				
								<input type='hidden' name="_sold_individually" value="<?php echo $_sold_ind;?>"/>
								<label for='_sold_individually'><?php _e('Sold Individually', 'eventon'); echo $eventon->throw_guide('Enable this to only allow one ticket per person','',false)?></label>
							</p>
						</td></tr>	

											
						

						<!-- Field details-->
						<tr><td style='padding:5px 25px;' colspan='2'><?php _e('Ticket Field description', 'eventon'); echo $eventon->throw_guide('Use this to type instruction text that will appear above add to cart section on calendar.','',false);?><br/><input style='width:100%; margin-top:5px'type='text' name='_tx_text' value='<?php echo evo_meta($woometa, '_tx_text');?>'/></td></tr>
											
						
					</table>
					<?php
						// DOWNLOAD CSV link 
						$exportURL = add_query_arg(array(
						    'action' => 'the_ajax_evotx_a3',
						    'e_id' => $post->ID,
						    'pid'=> $woo_product
						), admin_url('admin-ajax.php'));
					?>

					<?php if($woo_product):?>
						<p class='actions'>
							<?php if(!empty($woometa['total_sales']) && $woometa['total_sales']>0):?>
							<a id='evotx_attendees' data-eid='<?php echo $post->ID;?>' data-wcid='<?php echo evo_meta($fmeta, 'tx_woocommerce_product_id');?>' data-popc='evotx_lightbox' class='button_evo attendees eventon_popup_trig' title='<?php _e('View Attendees','eventon');?>'><?php _e('View Attendees','eventon');?></a><a class='button_evo download' href="<?php echo $exportURL;?>">Download (CSV)</a><?php endif;?><a class='button_evo edit' href='<?php echo get_edit_post_link($woo_product);?>'  title='<?php _e('Further Edit','eventon');?>'></a></p>
					<?php endif;?>

					<div class='clear'></div>		
				</div>
				<?php echo $eventon->output_eventon_pop_window(array('class'=>'evotx_lightbox', 'content'=>'Loading...', 'title'=>'Event Attendees', 'type'=>'padded', 'max_height'=>'350' ));?>

				
			</div>
		</div>
		</div>

		<?php

		echo ob_get_clean();

	}

