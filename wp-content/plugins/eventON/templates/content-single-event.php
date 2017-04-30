<?php
/**
 * The template for displaying event content within loops.
 *
 * Override this template by copying it to yourtheme/eventon/content-event.php
 *
 * @author 		AJDE
 * @package 	EventON/Templates
 * @version    	0.1
 */
 
global $eventon;


?>

<div class='ajde_evcal_calendar'>
<div id='evcal_list' class='eventon_events_list eventon_single_event'>
<?php

	$content =  $eventon->evo_generator->get_single_event_data(get_the_ID());
	
	echo $content[0]['content'];
?>
</div>
</div>