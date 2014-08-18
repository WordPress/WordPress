<?php
	// Confirmation email

	global $eventon, $evotx;

	// $args are passed to this page
	$email = $args[1];
	$args = $args[0];
	
	$evo_options = get_option('evcal_options_evcal_1');
	$evo_options_2 = get_option('evcal_options_evcal_2');

	$tix__ = new evotx_ticket();

	// styles
		$__styles_01 = "font-size:30px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; font-style:italic; margin: 4px 0; line-height:110%";
		$__sty_lh = "line-height:110%;";
		$__styles_02a = "color:#afafaf; text-transform:none";
		$__styles_03 = "color:#afafaf; font-style:italic;font-size:14px; margin:0 0 10px 0";
		$__styles_04 = "color:#303030; text-transform:uppercase; font-size:18px; font-style:italic; padding-bottom:0px; margin-bottom:0px; line-height:110%;";
		$__styles_05 = "padding-bottom:40px; ";
		$__styles_06 = "border-bottom:1px dashed #d1d1d1; padding:5px 20px";
		$__sty_td ="padding:0px;border:none";
		$__sty_pt20 ="padding-top:20px;";
		$__sty_m0 ="margin:0px;";

		$__styles_button = "font-size:14px; background-color:#".( ($evo_options['evcal_gen_btn_bgc'])? $evo_options['evcal_gen_btn_bgc']: "237ebd")."; color:#".( ($evo_options['evcal_gen_btn_fc'])? $evo_options['evcal_gen_btn_fc']: "ffffff")."; padding: 5px 10px; text-decoration:none; border-radius:4px;";

?>

<table width='100%' style='width:100%; margin:0'>
<?php 
$count = 1;
foreach($args['tickets'] as $tix):

	$event_id = get_post_meta($tix['product_id'], '_eventid', true);
	$e_pmv = get_post_custom($event_id);


	// location data
	$location = (!empty($e_pmv['evcal_location_name'])? $e_pmv['evcal_location_name'][0].' ': null).(!empty($e_pmv['evcal_location'])? $e_pmv['evcal_location'][0]:null);

	// event time
		$event_start_unix = $e_pmv['evcal_srow'][0];
		$event_end_unix = $e_pmv['evcal_erow'][0];
		$DATE_start_val=eventon_get_formatted_time($event_start_unix);
			if(empty($event_end_unix)){
				$DATE_end_val= $DATE_start_val;
			}else{
				$DATE_end_val=eventon_get_formatted_time($event_end_unix);
			}

		$__date = $tix__->_event_date($e_pmv, $DATE_start_val,$DATE_end_val);


	// ticket ID	
		$tid_product_code = ( !empty($tix['variation_id'])? $tix['variation_id']: $tix['product_id']);				
		$tid = $tix__->get_tixid_by_orderid($args['orderid'], $tid_product_code, $tix['qty']);
		
		
?>
	<tr>
		<td style='<?php echo $__sty_td;?>'>
			
			<div class='event_date' style='<?php echo $__styles_06;?>'>
				<p style='padding-top:10px;color:#555555; text-transform:uppercase; font-size:12px; margin:0px; line-height:100%'><?php echo $DATE_start_val['D'];?></p>
				<p style='margin:0px; text-transform:uppercase; font-size:20px;<?php echo $__sty_lh;?>'><?php echo $__date['html_date'];?></p>
				<p style='margin:0px; padding-bottom:10px; font-style:italic; color:#838383;<?php echo $__sty_lh;?>'><?php echo $__date['html_fromto'];?></p>
			</div>
			<div style="padding:20px; font-family:'open sans'">
				<p style='<?php echo $__styles_01.$__sty_lh;?>'><?php echo $tix['name'];?></p>

				<!-- ticket id-->
				<p style='<?php echo $__styles_02;?>'><span style='<?php echo $__styles_02a;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_003', 'Ticket #');?>:</span> <?php echo $tid;?></p>
				<p style='<?php echo $__styles_02;?>'><span style='<?php echo $__styles_02a;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_004', 'Primary ticket holder');?>:</span> <?php echo $args['customer'];?></p>
				<!-- quantity-->
				<p style='<?php echo $__styles_02;?>'><span style='<?php echo $__styles_02a;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_005', 'Quantity');?>:</span> <?php echo $tix['qty'];?></p>
				
				<?php if(!empty($tix['variation_id'])):
					$_product = new WC_Product_Variation($tix['variation_id'] );
        			$hh= $_product->get_variation_attributes( );

        			foreach($hh as $f=>$v):
				?>
					<p style='<?php echo $__styles_02.$__styles_05.$__sty_lh;?>'><span style='<?php echo $__styles_02a;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_006', 'Type');?>:</span> <?php echo $v;?></p>
				<?php endforeach; endif;?>
				<!-- location -->
				<?php if(!empty($location)):?>
					<p style='<?php echo $__styles_04.$__sty_pt20;?>'>Location</p>
					<p style='<?php echo $__styles_03;?>'><?php echo $location;?></p>
				<?php endif;?>

				
				<p style='margin:0px; <?php echo (empty($location))? $__sty_pt20:null;?><?php echo (count($args['tickets'])>1)? "margin-bottom:30px":null;?>'><a style='<?php echo $__styles_button;?>' href='<?php echo admin_url();?>admin-ajax.php?action=eventon_ics_download&event_id=<?php echo $event_id;?>&sunix=<?php echo $e_pmv['evcal_srow'][0];?>&eunix=<?php echo $e_pmv['evcal_erow'][0];?>' target='_blank'><?php echo eventon_get_custom_language( $evo_options_2,'evcal_evcard_addics', 'Add to calendar');?></a></p>

				<?php do_action('evotx_ticket_template_end', $event_id, $tix);?>
			</div>
		</td>
	</tr>
<?php endforeach;?>
<?php if($email):?>
	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>
			<?php
				$__link = (!empty($evo_options['evors_contact_link']))? $evo_options['evors_contact_link']:site_url();
			?>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_007', 'We look forward to seeing you!')?></p>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='' href='<?php echo $__link;?>'><?php echo eventon_get_custom_language( $evo_options_2,'evoTX_008', 'Contact Us for questions and concerns')?></a></p>
		</td>
	</tr>
<?php endif;?>
</table>

<?php
	
?>

