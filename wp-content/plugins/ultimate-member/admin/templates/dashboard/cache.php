<?php 

$all_options = wp_load_alloptions();

$count = 0;
foreach( $all_options as $k => $v ) {
	
	if ( strstr( $k, 'um_cache_userdata_' ) ) {
		$count++;
	}
	
}

 ?>

<p>Run this task from time to time to keep your DB clean.</p>
<p><a href="<?php echo add_query_arg( 'um_adm_action', 'user_cache' ); ?>" class="button">Clear cache of <?php echo $count; ?> users</a></p>