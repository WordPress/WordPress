<?php
/**
 * The template for displaying event content within loops.
 *
 * Override this template by copying it to yourtheme/eventon/content-event.php
 *
 * @author 		AJDE
 * @package 	eventon-single-event/Templates
 * @version    	0.1
 */
 
global $eventon;
	$event_id = get_the_ID();
	$evopt1 = get_option('evcal_options_evcal_1');
	
	// Google maps format, zoom level and scroll wheel values
	$evcal_gmap_format = ($evopt1['evcal_gmap_format']!='')?$evopt1['evcal_gmap_format']:'roadmap';	
	$evcal_gmap_zooml = ($evopt1['evcal_gmap_zoomlevel']!='')?$evopt1['evcal_gmap_zoomlevel']:'12';	
	$evcal_gmap_scrollw = (!empty($evopt1['evcal_gmap_scroll']) && $evopt1['evcal_gmap_scroll']=='yes')?'false':'true';
	
	global $eventon_sin_event;
	
	$event_header = $eventon_sin_event->get_single_event_header($event_id);

?>
<div class='eventon_main_section' >
	<div id='evcal_single_event_<?php echo $event_id;?>' class='ajde_evcal_calendar eventon_single_event evo_sin_page ' >
		
		<div class='evo-data' data-mapformat='<?php echo $evcal_gmap_format ?>' data-mapzoom='<?php echo $evcal_gmap_zooml ?>' data-mapscroll='<?php echo$evcal_gmap_scrollw ?>' data-evc_open='1'></div>


		<div id='evcal_head' class='calendar_header'><p id='evcal_cur'><?php echo $event_header;?></p></div>
		<div id='evcal_list' class='eventon_events_list evo_sin_event_list'>
		<?php

			$lang = (isset($_GET['l']))? $_GET['l']: null;
			$content =  $eventon->evo_generator->get_single_event_data($event_id, $lang);
			
			echo $content[0]['content'];
		?>
		</div>
	</div>
</div>

<?php
	comments_template( '', true );
?>