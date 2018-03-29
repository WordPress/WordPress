<?php

class UM_FontIcons {

	function __construct() {
		
		if ( !get_option('um_cache_fonticons') ) {
		
			$files['ii'] = um_path . 'assets/css/um-fonticons-ii.css';
			$files['fa'] = um_path . 'assets/css/um-fonticons-fa.css';
			
			foreach( $files as $c => $file ) {
			
				$css = file_get_contents($file);
				
				if ( $c == 'fa' ) {
					preg_match_all('/(um-faicon-.*?)\s?\{/', $css, $matches);
				} else {
					preg_match_all('/(um-icon-.*?)\s?\{/', $css, $matches);
				}

				unset($matches[1][0]);
				foreach($matches[1] as $match) {
					$icon = str_replace(':before','',$match);
					$array[] = $icon;
				}

			}
			
			update_option('um_cache_fonticons', $array);
		}
		
		$this->all = get_option('um_cache_fonticons');
		
	}

}