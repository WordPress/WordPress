<?php
/*
Plugin Name: Recents Posts Plus
Description: The same "Recent Posts", but plus.
Author: Damian Suarez
Version: 1.0
Author URI: http://retrofox.com.ar/
*/
/**
 * Recent Posts Plus widget class
 *
 * @since 0.1.0
 */


/**
 * Recent_Posts widget class
 *
 * @since 2.8.0
 */

function WP_Widget_Recent_Posts_Plus_importer () {

	class WP_Widget_Recent_Posts_Plus extends WP_Widget_Recent_Posts {

		function __construct() {
			$dir = plugin_dir_path( __FILE__ );

			$widget_ops = array(
				'classname' => 'widget_recent_entries_plus',
				'tpl' => $dir . '/recent_posts_plus/widget.tpl.php',
				'description' => "The most recent posts PLUS on your site"
			);

			parent::__construct('recent-posts-plus', __('Recent Posts Plus'), $widget_ops);
		}

		function time_since($date) {
			$ptime = date_timestamp_get(new DateTime($date));
			$etime = time() - $ptime;

			if ($etime < 1) {
				return '0 seconds';
			}

			$a = array(
				12 * 30 * 24 * 60 * 60	=> 'year',
				30 * 24 * 60 * 60				=> 'month',
				24 * 60 * 60						=> 'day',
				60 * 60									=> 'hour',
				60											=> 'minute',
				1												=> 'second'
			);

			foreach ($a as $secs => $str) {
			$d = $etime / $secs;
				if ($d >= 1) {
					$r = round($d);
					return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
				}
			}
		}
	}
}

add_action('widgets_init', function() {
	WP_Widget_Recent_Posts_Plus_importer();
	register_widget('WP_Widget_Recent_Posts_Plus');
});
