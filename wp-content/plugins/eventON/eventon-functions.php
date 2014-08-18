<?php
/**
 * eventON Functions
 *
 * Hooked-in functions for eventON related events on the front-end.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	eventON/Functions
 * @version     0.1
 */
 
 
/**
 * PHP tag driven event calendar 
 */
if( !function_exists ('ajde_evcal_calendar')){
	function ajde_evcal_calendar($args=''){		
		global $eventon,$EVO_generator;
		
		$content =$eventon->evo_generator->eventon_generate_calendar($args);		
		
		echo $content;
	} 
}

function add_eventon($args=''){		
	global $eventon,$EVO_generator;
	
	$content =$eventon->evo_generator->eventon_generate_calendar($args);		
	
	echo $content;
} 



	
?>