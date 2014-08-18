<?php
require_once(OX_LIB . '/Tools.php');

class Advman_Tools
{
	function format_author_value(&$value)
	{
		if (is_array($value)) {
			$users = get_users_of_blog();
			$all = true;
			foreach ($users as $user) {
				if (!in_array($user->user_id, $value)) {
					$all = false;
					break;
				}
			}
			if ($all) {
				$value = '';
			}
		}
	}
	function format_category_value(&$value)
	{
		if (is_array($value)) {
			$categories = get_categories("hierarchical=0&hide_empty=0");
			$all = true;
			foreach ($categories as $category) {
				if (!in_array($category->cat_ID, $value)) {
					$all = false;
					break;
				}
			}
			if ($all) {
				$value = '';
			}
		}
	}
	function format_tag_value(&$value)
	{
		if (is_array($value)) {
			$tags = get_tags("hierarchical=0&hide_empty=0");
			$all = true;
			foreach ($tags as $tag) {
				if (!in_array($tag->term_id, $value)) {
					$all = false;
					break;
				}
			}
			if ($all) {
				$value = '';
			}
		}
	}
	/**
	 * Get the last edit of this ad
	 */
	function get_last_edit($revisions)
	{
		$last_user = __('Unknown', 'advman');
		$last_timestamp = 0;
		
		if (!empty($revisions)) {
			foreach($revisions as $t => $u) {
				$last_user = $u;
				$last_timestamp = $t;
				break; // just get first one - the array is sorted by reverse date
			}
		}
		
		if ((time() - $last_timestamp) < (30 * 24 * 60 * 60)) { // less than 30 days ago
			$last_timestamp =  human_time_diff($t);
			$last_timestamp2 = date('l, F jS, Y @ h:ia', $t);
		} else {
			$last_timestamp =  __('> 30 days', 'advman');
			$last_timestamp2 = '';
		}
		return array($last_user, $last_timestamp, $last_timestamp2);
	}
	
	/**
	 * Get a template based on the class of an object
	 */
	function get_template($name)
	{
		$namePath = str_replace('_', '/', $name);
		include_once(ADVMAN_TEMPLATE_PATH . "/{$namePath}.php");
		$className = "Advman_Template_{$name}";
		return new $className;
	}
	
	function organize_appearance($ad)
	{
		$defaults = $ad->get_network_property_defaults();
		
		$app = array();
		$app['color']['border'] = __('Border:', 'advman');
		$app['color']['bg'] = __('Background:', 'advman');
		$app['color']['title'] = __('Title:', 'advman');
		$app['color']['text'] = __('Text:', 'advman');
		$app['color']['link'] = __('Link:', 'advman');
		$app['font']['title'] = __('Title Font:', 'advman');
		$app['font']['text'] = __('Text Font:', 'advman');
		
		foreach ($app as $section => $app1) {
			foreach ($app1 as $name => $label) {
				if (!isset($defaults["{$section}-{$name}"])) {
					unset($app[$section][$name]);
					if (empty($app[$section])) {
						unset($app[$section]);
					}
				}
			}
		}
		
		return $app;
	}
	
	function organize_formats($tfs)
	{
		$types = array(
			'text' => __('Text ads', 'advman'),
			'image' => __('Image ads', 'advman'),
			'ref_text' => __('Text referrals', 'advman'),
			'ref_image' => __('Image referrals', 'advman'),
			'textimage' => __('Text and image ads', 'advman'),
			'link' => __('Ad links', 'advman'),
			'video' => __('Video ads', 'advman'),
			'all' => __('All ad types', 'advman'),
		);
		
		$sections = array(
			'horizontal' => __('Horizontal', 'advman'),
			'vertical' => __('Vertical', 'advman'),
			'square' => __('Square', 'advman'),
			'other' => __('Other ad formats', 'advman'),
			'custom' => __('Custom width and height', 'advman'),
		);
		
		$formats_horizontal = array(
			'800x90' => __('%1$s x %2$s Large Leaderboard', 'advman'),
			'728x90' => __('%1$s x %2$s Leaderboard', 'advman'),
			'600x90' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'550x250' => __('%1$s x %2$s Mega Unit', 'advman'),
			'550x120' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'550x90' => __('%1$s x %2$s Small Leaderboard', 'advman'),
			'468x180' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x120' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'468x60' => __('%1$s x %2$s Banner', 'advman'),
			'450x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'430x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'400x90' => __('%1$s x %2$s Tall Banner', 'advman'),
			'234x60' => __('%1$s x %2$s Half Banner', 'advman'),
			'200x90' => __('%1$s x %2$s Tall Half Banner', 'advman'),
			'150x50' => __('%1$s x %2$s Half Banner', 'advman'),
			'120x90' => __('%1$s x %2$s Button', 'advman'),
			'120x60' => __('%1$s x %2$s Button', 'advman'),
			'83x31' => __('%1$s x %2$s Micro Bar', 'advman'),
			'728x15#4' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'728x15#5' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'468x15#4' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
			'468x15#5' => __('%1$s x %2$s Thin Banner, %3$s Links', 'advman'),
		);
		
		$formats_vertical = array(
			'160x600' => __('%1$s x %2$s Wide Skyscraper', 'advman'),
			'120x600' => __('%1$s x %2$s Skyscraper', 'advman'),
			'200x360' => __('%1$s x %2$s Wide Half Banner', 'advman'),
			'240x400' => __('%1$s x %2$s Vertical Rectangle', 'advman'),
			'180x300' => __('%1$s x %2$s Tall Rectangle', 'advman'),
			'200x270' => __('%1$s x %2$s Tall Rectangle', 'advman'),
			'120x240' => __('%1$s x %2$s Vertical Banner', 'advman'),
		);
		
		$formats_square = array(
			'336x280' => __('%1$s x %2$s Large Rectangle', 'advman'),
			'336x160' => __('%1$s x %2$s Wide Rectangle', 'advman'),
			'334x100' => __('%1$s x %2$s Wide Rectangle', 'advman'),
			'300x250' => __('%1$s x %2$s Medium Rectangle', 'advman'),
			'300x150' => __('%1$s x %2$s Small Wide Rectangle', 'advman'),
			'300x125' => __('%1$s x %2$s Small Wide Rectangle', 'advman'),
			'300x70' => __('%1$s x %2$s Mini Wide Rectangle', 'advman'),
			'250x250' => __('%1$s x %2$s Square', 'advman'),
			'200x200' => __('%1$s x %2$s Small Square', 'advman'),
			'200x180' => __('%1$s x %2$s Small Rectangle', 'advman'),
			'180x150' => __('%1$s x %2$s Small Rectangle', 'advman'),
			'160x160' => __('%1$s x %2$s Small Square', 'advman'),
			'125x125' => __('%1$s x %2$s Button', 'advman'),
			'200x90#4' => __('%1$s x %2$s Tall Half Banner, %3$s Links', 'advman'),
			'200x90#5' => __('%1$s x %2$s Tall Half Banner, %3$s Links', 'advman'),
			'180x90#4' => __('%1$s x %2$s Half Banner, %3$s Links', 'advman'),
			'180x90#5' => __('%1$s x %2$s Half Banner, %3$s Links', 'advman'),
			'160x90#4' => __('%1$s x %2$s Tall Button, %3$s Links', 'advman'),
			'160x90#5' => __('%1$s x %2$s Tall Button, %3$s Links', 'advman'),
			'120x90#4' => __('%1$s x %2$s Button, %3$s Links', 'advman'),
			'120x90#5' => __('%1$s x %2$s Button, %3$s Links', 'advman'),
		);
		
		$sectforms = array(
			'horizontal' => $formats_horizontal,
			'vertical' => $formats_vertical,
			'square' => $formats_square,
		);
		
		$data = array();
		foreach ($tfs as $t => $fs) {
			foreach ($sectforms as $sect => $forms) {
				foreach ($forms as $form => $label) {
					$k = array_search($form, $fs);
					if ($k !== false) {
						$data[$t][$sect][] = $form;
						$formats[$form] = $label;
						unset($fs[$k]);
					}
				}
			}
			
			if (!empty($fs)) {
				foreach ($fs as $k => $f) {
					if ($f == 'custom') {
						$data[$t]['custom'][] = $f;
						$formats[$f] = __('Custom width and height', 'advman');
					} else {
						$data[$t]['other'][] = $f;
						$formats[$f] = (strpos($f, '#') === false) ? __('%1$s x %2$s Banner', 'advman') : __('%1$s x %2$s Banner, %3$s Links', 'advman');
					}
				}
			}
		}
		
		return array('data' => $data, 'types' => $types, 'sections' => $sections, 'formats' => $formats);
	}
	
	function get_properties_from_array($aAd)
	{
		$aProperties = array();
		$aOmit = array('name', 'id', 'active', 'class');
		foreach ($aAd as $n => $v) {
			if (!in_array($n, $aOmit)) {
				$aProperties[$n] = $v;
			}
		}
		
		return $aProperties;
	}

    function get_current_ad()
    {
        global $advman_engine;

        $target = OX_Tools::sanitize_request_var('advman-target');
        if (is_numeric($target)) {
            $id = intval($target);
            $ad = $advman_engine->getAd($id);
        }

        return $ad;
    }

    function get_current_network()
    {
        global $advman_engine;

        $target = OX_Tools::sanitize_request_var('advman-target');
        $network = $advman_engine->factory($target);

        return $network;
    }



    function get_current_ads()
    {
        global $advman_engine;

        $targets = OX_Tools::sanitize_request_var('advman-targets');
        if (is_array($targets)) {
            $ads = array();
            foreach ($targets as $target) {
                $ads[$target] = $advman_engine->getAd($target);
            }
        }

        return $ads;
    }
}
?>