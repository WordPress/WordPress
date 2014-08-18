<?php
class OX_Tools
{
	function load_plugins($dir, &$obj)
	{
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				// Make sure that the first character does not start with a '.' (omit hidden files like '.', '..', '.svn', etc.)
				// as well as make sure the file is not a directory
				if ($file[0] != '.' && substr($file, -4) == '.php') {
					$require_file = is_dir("{$dir}/{$file}") ? "{$dir}/{$file}/{$file}.php" : "{$dir}/{$file}";
					
					if (file_exists($require_file)) {
						require_once $require_file;
						$fileName = split('[.]', $file);
						$class = 'OX_Plugin_' . $fileName[0];
						$plugin = new $class();
						$plugin->register_plugin($obj);
					}
				}
			}
			closedir($handle);
		}
	}
	
	function get_int_max()
	{
		$max=0x7fff;
		$probe = 0x7fffffff;
		while ($max == ($probe>>16)) {
			$max = $probe;
			$probe = ($probe << 16) + 0xffff;
		}
		return $max;
	}

	function sort($ads)
	{
		uasort($ads, array('OX_Tools', '_sort_ads'));
		return $ads;
	}
	
	/**
	 * Sort ads by network, then by ID
	 */
	function _sort_ads($ad1,$ad2)
	{
		$cmp = strcmp(get_class($ad1), get_class($ad2));
		if ($cmp == 0) {
			$cmp = strcmp($ad1->id, $ad2->id);
		}
		return $cmp;
	}
	
	function organize_colors($colors)
	{
		$clr = array();
		$clr['border'] = __('Border:', 'advman');
		$clr['bg'] = __('Background:', 'advman');
		$clr['title'] = __('Title:', 'advman');
		$clr['text'] = __('Text:', 'advman');
		
		foreach ($clr as $name => $label) {
			if (!in_array($name, $colors)) {
				unset($clr[$name]);
			}
		}
		
		return $clr;
	}
	
	function organize_formats($formats)
	{
		$fmt = array();
		$fmt['horizontal']['728x90'] = __('728 x 90 Leaderboard', 'advman');
		$fmt['horizontal']['468x60'] = __('468 x 60 Banner', 'advman');
		$fmt['horizontal']['234x60'] = __('234 x 60 Half Banner', 'advman');
		$fmt['vertical']['120x600'] = __('120 x 600 Skyscraper', 'advman');
		$fmt['vertical']['160x600'] = __('160 x 600 Wide Skyscraper', 'advman');
		$fmt['square']['300x250'] = __('300 x 250 Medium Rectangle', 'advman');
		$fmt['custom']['custom'] = __('Custom width and height', 'advman');

		foreach ($fmt as $section => $fmt1) {
			foreach ($fmt1 as $name => $label) {
				if (!in_array($name, $formats)) {
					unset($fmt[$section][$name]);
					if (empty($fmt[$section])) {
						unset($fmt[$section]);
					}
				}
			}
		}
		
		$sct['horizontal'] = __('Horizontal', 'advman');
		$sct['vertical'] = __('Vertical', 'advman');
		$sct['square'] = __('Square', 'advman');
		$sct['custom'] = __('Custom', 'advman');
		
		foreach ($sct as $section => $name) {
			if (!isset($fmt[$section])) {
				unset($sct[$section]);
			}
		}
		
		return array('sections' => $sct, 'formats' => $fmt);
	}

    function sanitize_request_var($field)
    {
        return OX_Tools::sanitize_arr_var($field, $_REQUEST);
    }
    function sanitize_post_var($field)
    {
        return OX_Tools::sanitize_arr_var($field, $_POST);
    }

    function sanitize_arr_var($field, $arr)
    {
        return (isset($arr[$field])) ?  OX_Tools::sanitize($arr[$field], 'key') : '';
    }
	function sanitize($field, $type = null)
	{
		if (is_array($field)) {
			$a = array();
			foreach ($field as $name => $value) {
				$n = OX_Tools::sanitize($name, 'key');
				$v = OX_Tools::sanitize($value, $type);
				$a[$n] = $v;
			}
			return $a;
		}
		switch ($type) {
			case 'n' :
			case 'number' :
			case 'int' :
				return preg_replace('#[^0-9\.\-]#i', '', $field);
				break;
			case 'format' :
				return $field == 'custom' ? $field : preg_replace('#[^0-9x]#i', '', $field);
				break;
			case 'key' :
				return preg_replace('#[^a-z0-9-_]#i', '', $field);

			default :
				return stripslashes(str_replace("\0", '', $field));
				break;
		}
	}
	
	function explode_format($format)
	{
		list($w, $h) = split('[x]', $format);
		list($h, $l) = split('[#]', $h);
		return array($w, $h, $l);
	}
	
	function post_url($url, $data, $optional_headers = null)
	{
		$params = array('http' => array(
			'method' => 'post',
			'content' => $data
		));
		if ($optional_headers!== null) {
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			//throw new Exception("Problem with $url, $php_errormsg");
			return false;  // silently fail
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			//throw new Exception("Problem reading data from $url, $php_errormsg");
			return false; //silently fail
		}
		return $response;
	}
	function generate_name($base = null)
	{
		global $advman_engine;
		$ads = $advman_engine->getAds();
		
		// Generate a unique name if no name was specified
		$unique = false;
		$i = 1;
		$name = $base;
		while (!$unique) {
			$unique = true;
			foreach ($ads as $ad) {
				if ($ad->name == $name) {
					$unique = false;
					break;
				}
			}
			if (!$unique) {
				$name = $base . '-' . $i++;
			}
		}
		
		return $name;
	}
}
?>