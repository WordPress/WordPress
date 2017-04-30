<?php

// $flipbooks = get_option('flipbooks');
	
// foreach($flipbooks as $flipbook ){
	// if($flipbook["lightboxCssClass"] != ""){
		// $atts = array();
		// $atts["id"] = $flipbook["id"];
							
		// real3dflipbook_shortcode($atts);	
	// }		
// }
	
	
	function real3dflipbook_shortcode($atts){
		$args = shortcode_atts( 
			array(
				'id'   => '-1'
			), 
			$atts
		);
		$id = (int) $args['id'];
		$flipbooks = get_option('flipbooks');
		$flipbook = $flipbooks[$id];
		$flipbook['rootFolder'] = plugins_url()."/real3d-flipbook/";
		$output = ('<div class="real3dflipbook" id="'.$id.'" ><div id="options" style="display:none;">'.json_encode($flipbook).'</div></div>');
		// trace("shortcode");
		// trace(json_encode($flipbook));
		return $output;
	}
	add_shortcode('real3dflipbook', 'real3dflipbook_shortcode');