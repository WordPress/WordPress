<?php
/***
 * Debug Functions
 *
 * When logged in as a super admin, these functions will run to provide
 * debugging information when specific super admin menu items are selected.
 *
 * They are not used when a regular user is logged in.
 */

function wp_admin_bar_debug_menu() {
	global $wp_admin_bar, $wpdb;

	if ( ! is_super_admin() || ! apply_filters('wp_admin_bar_enable_debug_menu', false ) )
		return;

	$queries = $wpdb->num_queries;
	$seconds = timer_stop();

	/* Add the main siteadmin menu item */
	$wp_admin_bar->add_menu( array( 'id' => 'queries', 'title' => "{$queries}q/{$seconds}", 'href' => 'javascript:toggle_query_list()', 'meta' => array( 'class' => 'ab-sadmin' ) ) );
}
add_action( 'wp_before_admin_bar_render', 'wp_admin_bar_debug_menu', 1000 );

function wp_admin_bar_query_debug_list() {
	global $wpdb, $wp_object_cache;

	if ( !is_super_admin() )
		return false;

	$debugs = array();

	if ( defined('SAVEQUERIES') && SAVEQUERIES )
		$debugs['wpdb'] = array( __('Queries'), 'wp_admin_bar_debug_queries' );

	if ( is_object($wp_object_cache) && method_exists($wp_object_cache, 'stats') )
		$debugs['object-cache'] = array( __('Object Cache'), 'wp_admin_bar_debug_object_cache' );

	$debugs = apply_filters( 'wp_admin_bar_debugs_list', $debugs );

	if ( empty($debugs) )
		return;

?>
	<script type="text/javascript">
	/* <![CDATA[ */
	var toggle_query_list = function() { 
		var querylist = document.getElementById( 'querylist' );
		if( querylist && querylist.style.display == 'block' ) {
			querylist.style.display='none';
		} else {
			querylist.style.display='block';
		}
	}

	var clickDebugLink = function( targetsGroupId, obj) {
		var sectionDivs = document.getElementById( targetsGroupId ).childNodes;
		for ( var i = 0; i < sectionDivs.length; i++ ) {
			if ( 1 != sectionDivs[i].nodeType ) {
				continue;
			}
			sectionDivs[i].style.display = 'none';
		}
		document.getElementById( obj.href.substr( obj.href.indexOf( '#' ) + 1 ) ).style.display = 'block';

		for ( var i = 0; i < obj.parentNode.parentNode.childNodes.length; i++ ) {
			if ( 1 != obj.parentNode.parentNode.childNodes[i].nodeType ) {
				continue;
			}
			obj.parentNode.parentNode.childNodes[i].removeAttribute( 'class' );
		}
		obj.parentNode.setAttribute( 'class', 'current' );
		return false;
	};
	/* ]]> */
	</script>
	<div align='left' id='querylist'>

	<h1>Debugging blog #<?php echo $GLOBALS['blog_id']; ?> on <?php echo php_uname( 'n' ); ?></h1>
	<div id="debug-status">
		<p class="left"></p>
		<p class="right">PHP Version: <?php echo phpversion(); ?></p>
	</div>
	<ul class="debug-menu-links">

<?php	$current = ' class="current"'; foreach ( $debugs as $debug => $debug_output ) : ?>

		<li<?php echo $current; ?>><a id="debug-menu-link-<?php echo $debug; ?>" href="#debug-menu-target-<?php echo $debug; ?>" onclick="try { return clickDebugLink( 'debug-menu-targets', this ); } catch (e) { return true; }"><?php echo $debug_output[0] ?></a></li>

<?php	$current = ''; endforeach; ?>

	</ul>

	<div id="debug-menu-targets">

<?php	$current = ' style="display: block"'; foreach ( $debugs as $debug => $debug_output ) : ?>

	<div id="debug-menu-target-<?php echo $debug; ?>" class="debug-menu-target"<?php echo $current; ?>>
		<?php echo str_replace( '&nbsp;', '', call_user_func( $debug_output[1] ) ); ?>
	</div>

<?php	$current = ''; endforeach; ?>

	</div>

<?php	do_action( 'wp_admin_bar_debug' ); ?>

	</div>

<?php
}
add_action( 'wp_after_admin_bar_render', 'wp_admin_bar_query_debug_list' );

function wp_admin_bar_debug_queries() {
	global $wpdb;

	$queries = array();
	$out = '';
	$total_time = 0;

	if ( !empty($wpdb->queries) ) {
		$show_many = isset($_GET['debug_queries']);

		if ( $wpdb->num_queries > 500 && !$show_many )
			$out .= "<p>There are too many queries to show easily! <a href='" . add_query_arg( 'debug_queries', 'true' ) . "'>Show them anyway</a>.</p>";

		$out .= '<ol id="wpd-queries">';
		$first_query = 0;
		$counter = 0;

		foreach ( $wpdb->queries as $q ) {
			list($query, $elapsed, $debug) = $q;

			$total_time += $elapsed;

			if ( !$show_many && ++$counter > 500 )
				continue;

			$query = nl2br(esc_html($query));

			// $dbhname, $host, $port, $name, $tcp, $elapsed
			$out .= "<li>$query<br/><div class='qdebug'>$debug <span>#{$counter} (" . number_format(sprintf('%0.1f', $elapsed * 1000), 1, '.', ',') . "ms)</span></div></li>\n";
		}
		$out .= '</ol>';
	} else {
		$out .= "<p><strong>There are no queries on this page, you won the prize!!! :)</strong></p>";
	}

	$query_count = '<h2><span>Total Queries:</span>' . number_format( $wpdb->num_queries ) . "</h2>\n";
	$query_time = '<h2><span>Total query time:</span>' . number_format(sprintf('%0.1f', $total_time * 1000), 1) . "ms</h2>\n";
	$memory_usage = '<h2><span>Peak Memory Used:</span>' . number_format( memory_get_peak_usage( ) ) . " bytes</h2>\n";

	$out = $query_count . $query_time . $memory_usage . $out;

	return $out;
}

function wp_admin_bar_debug_object_cache() {
	global $wp_object_cache;
	ob_start();
	echo "<div id='object-cache-stats'>";
		$wp_object_cache->stats();
	echo "</div>";
	$out = ob_get_contents();
	ob_end_clean();

	return $out;
}

?>
