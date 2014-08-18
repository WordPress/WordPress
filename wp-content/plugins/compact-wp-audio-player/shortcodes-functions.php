<?php

add_shortcode('sc_embed_player', 'sc_embed_player_handler');
add_shortcode('sc_embed_player_template1', 'sc_embed_player_templater1_handler');

if (!is_admin()) {
    add_filter('widget_text', 'do_shortcode');
}
add_filter('the_excerpt', 'do_shortcode', 11);

function sc_embed_player_handler($atts, $content = null) {
    extract(shortcode_atts(array(
        'fileurl' => '',
        'autoplay' => '',
        'volume' => '',
        'class' => '',
        'loops' => '',
                    ), $atts));
    if (empty($fileurl)) {
        return '<div style="color:red;font-weight:bold;">Compact Audio Player Error! You must enter the mp3 file URL via the "fileurl" parameter in this shortcode. Please check the documentation and correct the mistake.</div>';
    }
    if (empty($volume)) {
        $volume = '80';
    }
    if (empty($class)) {
        $class = "sc_player_container1";
    }//Set default container class
    if (empty($loops)) {
        $loops = "false";
    }
    $ids = uniqid();

    $player_cont = '<div class="' . $class . '">';
    $player_cont .= '<input type="button" id="btnplay_' . $ids . '" class="myButton_play" onClick="play_mp3(\'play\',\'' . $ids . '\',\'' . $fileurl . '\',\'' . $volume . '\',\'' . $loops . '\');show_hide(\'play\',\'' . $ids . '\');" />';
    $player_cont .= '<input type="button"  id="btnstop_' . $ids . '" style="display:none" class="myButton_stop" onClick="play_mp3(\'stop\',\'' . $ids . '\',\'\',\'' . $volume . '\',\'' . $loops . '\');show_hide(\'stop\',\'' . $ids . '\');" />';
    $player_cont .= '<div id="sm2-container"><!-- flash movie ends up here --></div>';
    $player_cont .= '</div>';

    if (!empty($autoplay)) {
        $path_to_swf = SC_AUDIO_BASE_URL . 'swf/soundmanager2.swf';
        $player_cont .= <<<EOT
<script type="text/javascript" charset="utf-8">
soundManager.setup({
	url: '$path_to_swf',
	onready: function() {
		var mySound = soundManager.createSound({
		id: 'btnplay_$ids',
		volume: '$volume',
		url: '$fileurl'
		});
		var auto_loop = '$loops';
		mySound.play({
    		onfinish: function() {
				if(auto_loop == 'true'){
					loopSound('btnplay_$ids');
				}
				else{
					document.getElementById('btnplay_$ids').style.display = 'inline';
					document.getElementById('btnplay_$ids').style.display = 'none';
				}
    		}
		});
		document.getElementById('btnplay_$ids').style.display = 'none';
        document.getElementById('btnstop_$ids').style.display = 'inline';
	},
	ontimeout: function() {
		// SM2 could not start. Missing SWF? Flash blocked? Show an error.
		alert('Error! Audio player failed to load.');
	}
});
</script>
EOT;
    }//End autopay code

    return $player_cont;
}

function sc_embed_player_templater1_handler($atts){
    extract(shortcode_atts(array(
        'fileurl' => '',
        'autoplay' => '',
        'volume' => '',
        'class' => '',
        'loops' => '',
                    ), $atts));
    if (empty($fileurl)) {
        return '<div style="color:red;font-weight:bold;">Compact Audio Player Error! You must enter the mp3 file URL via the "fileurl" parameter in this shortcode. Please check the documentation and correct the mistake.</div>';
    }
    
    if (empty($class)) {
        $class = "sc_fancy_player_container";//Set default container class
    }
    
    if (empty($autoplay)) {//Set autoplay value
        $autoplay = "";
    }else{
        $autoplay = "on";
    }
    
    if (empty($loops)) {//Set the loops value
        $loops = "";
    }else{
        $loops = "on";
    }

    $args = array(
        'src'      => $fileurl,
        'loop'     => $loops,
        'autoplay' => $autoplay,
        'preload'  => 'none'
    );

    $player_container = "";
    $player_container .= '<div class="'.$class.'">';    
    $player_container .= wp_audio_shortcode($args);
    $player_container .= '</div>';
    return $player_container;
}