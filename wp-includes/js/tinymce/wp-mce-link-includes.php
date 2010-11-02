<?php
	
class WP_Tab_Bar {
	var $tabs = array();
	
	var $id = '';
	var $classes = array();
	
	var $selected = '';
	
	function add( $id, $label, $url='' ) {
		array_push( $this->tabs, array(
			'label' => $label,
			'for' => $id,
			'url' => $url
		));
	}
	
	function select( $id ) {
		$this->selected = $id;
	}
	
	function render( $echo=true ) {
		if ( empty( $this->selected ) )
			$this->selected = $this->tabs[0]['for'];

		array_unshift( $this->classes, 'wp-tab-bar' );
		
		$out = "<ul id='$this->id' class='" . esc_attr( implode( ' ', $this->classes ) ) . "'>";
		foreach( $this->tabs as $tab ) {
			if ( !isset($tab['url']) )
				$tab['url'] = '';

			$out.= "<li class='";
			$out.= $this->selected == $tab['for'] ? 'wp-tab-active' : '';
			$out.= "'><input type='hidden' class='wp-tab-for-id' value='{$tab['for']}' />";
			$out.= "<a href='{$tab['url']}#{$tab['for']}'>";
			$out.= "{$tab['label']}</a></li>";
		}
		$out.= "</ul>";

		if ( $echo )
			echo $out;

		return $out;
	}
}

function wp_link_panel_custom() { ?>
	<div id="link-panel-id-custom" class="link-panel link-panel-custom link-panel-active">
		<input type="hidden" class="link-panel-type" value="custom" />
		<label>
			<span><?php _e('URL:'); ?></span><input class="url-field" type="text" />
		</label>
	</div>
<?php }

function wp_link_panel_structure( $panel_type, $name, $queries ) {
	$id = $panel_type . '-' . $name;
	
	?>
	<div id="link-panel-id-<?php echo $id; ?>" class="link-panel link-panel-<?php echo $panel_type; ?>">
		<!-- <input type="hidden" class="link-panel-type" value="<?php echo $panel_type; ?>" /> -->
		<!-- <input type="hidden" class="link-panel-id" value="" /> -->
		<?php
		
		$tb = new WP_Tab_Bar();
		foreach( $queries as $i => $query ) {
			$queries[$i]['id'] = "$id-{$query['preset']}";
			$tb->add( $queries[$i]['id'], $query['label'] );
		}
		$tb->render();

		foreach( $queries as $query ): ?>
			<div id="<?php echo $query['id']; ?>" class="wp-tab-panel">
				<input type="hidden" class="wp-tab-panel-query" value="<?php echo $query['preset']; ?>" />
				
			<?php if ( 'search' == $query['preset'] ): ?>
				<label for="<?php echo $id; ?>-search-field" class="link-search-wrapper">
					<span><?php _e('Search:'); ?></span>
					<input type="text" id="<?php echo $id; ?>-search-field" class="link-search-field" />
					<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				</label>
				<ul id="<?php echo $id; ?>-search-results" class="link-search-results"></ul>
				
			<?php else: ?>
				<div class="wp-tab-panel-pagelinks wp-tab-panel-pagelinks-top"></div>
				<ul>
					<li class="wp-tab-panel-loading unselectable"><em><?php _e('Loading...'); ?></em></li>
				</ul>
				<div class="wp-tab-panel-pagelinks wp-tab-panel-pagelinks-bottom"></div>
			<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php }

function wp_link_query_post_type( $pt_obj, $preset='all', $opts=array() ) {
	$args_base = array(
		'post_type' => $pt_obj->name,
		'suppress_filters' => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'post_status' => 'publish',
	);
	
	switch( $preset ) {
	case 'all':
		$args = array_merge( $args_base, array(
			'order' => 'ASC',
			'orderby' => 'title',
			'posts_per_page' => 20,
		));
		break;
	case 'recent':
		$args = array_merge( $args_base, array(
			'order' => 'DESC',
			'orderby' => 'post_date',
			'posts_per_page' => 15,
		));
		break;
	case 'search':
		$args = array_merge( $args_base, array(
			's' => isset($opts['search']) ? $opts['search'] : '',
			'posts_per_page' => 10
		));
		break;
	}
	
	// Handle pages if a page number is specified.
	if ( isset( $opts['pagenum'] ) && isset( $args['posts_per_page'] ) ) {
		$pages = array(
			'current' => $opts['pagenum'],
			'per_page' => $args['posts_per_page']
		);
		
		if ( ! isset( $args['offset'] ) )
			$args['offset'] = 0 < $opts['pagenum'] ? $args['posts_per_page'] * ( $opts['pagenum'] - 1 ) : 0;
		$pages['offset'] = $args['offset'];
	}
	
	// Allow args to be extended.
	if ( isset( $opts['args'] ) )
		$args = array_merge( $args, $opts['args'] );
	
	// Do main query.
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $args );
	// Check if any posts were found.
	if ( ! $get_posts->post_count )
		return false;
	
	// Build results.
	$results = array();
	foreach ( $posts as $post ) {
		$results[] = array(
			'ID' => $post->ID,
			'title' => $post->post_title,
			'permalink' => get_permalink( $post->ID )
		);
	}
	// Build response.
	$resp = array(
		'query' => $get_posts,
		'objects' => $posts,
		'results' => $results
	);
	
	// Set remaining pages values.
	if ( isset( $pages ) ) {
		$pages['max'] = $resp['query']->max_num_pages;
		$pages['page_links'] = paginate_links( array(
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $pages['max'],
			'current' => $pages['current']
		));
		$resp['pages'] = $pages;
	}
	
	return $resp;
}

function wp_link_ajax( $request ) {
	if ( !isset($request['type']) || !isset($request['name']) || !isset($request['preset']) )
		die('-1');

	// Run only presets we recognize.
	if ( 'pt' != $request['type'] || ! in_array( $request['preset'], array('all','search','recent') ) )
		die('-1');
	// Searches must have a search term.
	else if ( 'search' == $request['preset'] && !isset($request['title']) )
		die('-1');

	$opts = array();
	if ( 'search' == $request['preset'] ) {
		$opts['search'] = $request['title'];
	} else if ( ! empty( $request['page'] ) ) {
		$opts['pagenum'] = $request['page'];
	}
		
	if ( 'pt' == $request['type'] && $obj = get_post_type_object($request['name']) )
		$resp = wp_link_query_post_type( $obj, $request['preset'], $opts );

	if ( ! isset( $resp ) )
		die('0');

	$json = array( 'results' => $resp['results'] );
	if ( isset( $resp['pages'] ) && !empty( $resp['pages']['page_links'] ) )
		$json['page_links'] = $resp['pages']['page_links'];
	
	echo json_encode( $json );
	echo "\n";
}
?>