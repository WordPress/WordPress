<?php

/**
 * Event Class
 *
 * The eventon event creating the right event object
 *
 * @class 		eventon_event
 * @version		0.1
 * @package		eventon/Classes
 * @category	Class
 * @author 		eventon
 */
class evo_event {
	
	public function get_event($the_event=''){
		global $post;
		
		if(!empty($the_event) && is_numeric($the_event)){
			
			$the_event = get_post($the_event);
			
		}else{		
			$the_event= $post;				
		}
		
		if(!$the_event)
			return false;
		
		
		$event_id = absint($the_event->ID);
		$post_type = $the_event->post_type;
		
		if($post_type=='ajde_events'){
			
			$event = new stdClass();
			$event->post_title = $the_event->post_title;
			
			$meta = get_post_custom($the_event->ID);
			
			if(!empty($meta)){
				
				$evcal_date_format = eventon_get_timeNdate_format();

				// FOREACH
				foreach($meta as $key=>$value){
					if(!in_array($key, array('_edit_lock','_edit_last','evcal_srow','evcal_erow')))
						$event->$key = $value[0];
					
					// GET DATE and TIME values
					if($key=='evcal_srow'){
						$_START=(!empty($value[0]))?
							eventon_get_editevent_kaalaya($value[0]):false;
						
						if(!empty($_START)){
							$event->evcal_start_date = $_START[0];
							$event->evcal_start_time_hour = $_START[1];
							$event->evcal_start_time_min = $_START[2];
							
							if(!empty($_START[3]))
								$event->evcal_st_ampm = $_START[3];
						}
					}
					
					if($key=='evcal_erow'){
						
						$erow = (!empty($value[0]))? ($value[0]) :
							( (!empty($meta['evcal_srow']))? $meta['evcal_srow'][0]: null );
						
						$_END=(!empty($erow))?
							eventon_get_editevent_kaalaya($value[0]):false;
						
						if(!empty($_END)){
							$event->evcal_end_date = $_END[0];
							$event->evcal_end_time_hour = $_END[1];
							$event->evcal_end_time_min = $_END[2];
							
							if(!empty($_END[3]))
								$event->evcal_et_ampm = $_END[3];
						}						
					}
					
					// delete unused meta values from old versions
					if(in_array($key, array('event_start_date','event_end_date', 'event_name'))){
						delete_post_meta($the_event->ID,$key); 
					}
					
				}// end foreach
				
				
				$event->_evo_date_format = $evcal_date_format[1];
				$event->_evo_time_format = ($evcal_date_format[2])?'24h':'12h';
			}
			
			return $event;
			
		}else{
			return false;
		}
		
	}
	
	public function get_event_fields_edit(){
		return $values  = array(
				'evcal_start_date',
				'evcal_start_time_hour',
				'evcal_start_time_min',
				'evcal_st_ampm',
				'evcal_end_date',
				'evcal_end_time_hour',
				'evcal_end_time_min',
				'evcal_et_ampm',
				'evcal_location',
				'evcal_organizer',
				'_featured',
				'_evo_date_format',
				'_evo_time_format'
			);
	}
	
}

?>