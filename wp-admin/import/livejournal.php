<?php
/**
 * LiveJournal API Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

// XML-RPC library for communicating with LiveJournal API
require_once( ABSPATH . WPINC . '/class-IXR.php' );

// Snoopy for getting comments (with cookies)
require_once( ABSPATH . WPINC . '/class-snoopy.php' );

/**
 * LiveJournal API Importer class
 *
 * Imports your LiveJournal contents into WordPress using the LJ API
 *
 * @since 2.7.1
 */
class LJ_API_Import {

	var $comments_url = 'http://www.livejournal.com/export_comments.bml';
	var $ixr_url      = 'http://www.livejournal.com/interface/xmlrpc';
	var $ixr;
	var $username;
	var $password;
	var $snoop;
	var $comment_meta;
	var $comments;
	var $usermap;
	var $postmap;
	var $pointers = array();
	
	// This list taken from LJ, they don't appear to have an API for it
	var $moods = array( '1' => 'aggravated',
						'10' => 'discontent',
						'100' => 'rushed',
						'101' => 'contemplative',
						'102' => 'nerdy',
						'103' => 'geeky',
						'104' => 'cynical',
						'105' => 'quixotic',
						'106' => 'crazy',
						'107' => 'creative',
						'108' => 'artistic',
						'109' => 'pleased',
						'11' => 'energetic',
						'110' => 'bitchy',
						'111' => 'guilty',
						'112' => 'irritated',
						'113' => 'blank',
						'114' => 'apathetic',
						'115' => 'dorky',
						'116' => 'impressed',
						'117' => 'naughty',
						'118' => 'predatory',
						'119' => 'dirty',
						'12' => 'enraged',
						'120' => 'giddy',
						'121' => 'surprised',
						'122' => 'shocked',
						'123' => 'rejected',
						'124' => 'numb',
						'125' => 'cheerful',
						'126' => 'good',
						'127' => 'distressed',
						'128' => 'intimidated',
						'129' => 'crushed',
						'13' => 'enthralled',
						'130' => 'devious',
						'131' => 'thankful',
						'132' => 'grateful',
						'133' => 'jealous',
						'134' => 'nervous',
						'14' => 'exhausted',
						'15' => 'happy',
						'16' => 'high',
						'17' => 'horny',
						'18' => 'hungry',
						'19' => 'infuriated',
						'2' => 'angry',
						'20' => 'irate',
						'21' => 'jubilant',
						'22' => 'lonely',
						'23' => 'moody',
						'24' => 'pissed off',
						'25' => 'sad',
						'26' => 'satisfied',
						'27' => 'sore',
						'28' => 'stressed',
						'29' => 'thirsty',
						'3' => 'annoyed',
						'30' => 'thoughtful',
						'31' => 'tired',
						'32' => 'touched',
						'33' => 'lazy',
						'34' => 'drunk',
						'35' => 'ditzy',
						'36' => 'mischievous',
						'37' => 'morose',
						'38' => 'gloomy',
						'39' => 'melancholy',
						'4' => 'anxious',
						'40' => 'drained',
						'41' => 'excited',
						'42' => 'relieved',
						'43' => 'hopeful',
						'44' => 'amused',
						'45' => 'determined',
						'46' => 'scared',
						'47' => 'frustrated',
						'48' => 'indescribable',
						'49' => 'sleepy',
						'5' => 'bored',
						'51' => 'groggy',
						'52' => 'hyper',
						'53' => 'relaxed',
						'54' => 'restless',
						'55' => 'disappointed',
						'56' => 'curious',
						'57' => 'mellow',
						'58' => 'peaceful',
						'59' => 'bouncy',
						'6' => 'confused',
						'60' => 'nostalgic',
						'61' => 'okay',
						'62' => 'rejuvenated',
						'63' => 'complacent',
						'64' => 'content',
						'65' => 'indifferent',
						'66' => 'silly',
						'67' => 'flirty',
						'68' => 'calm',
						'69' => 'refreshed',
						'7' => 'crappy',
						'70' => 'optimistic',
						'71' => 'pessimistic',
						'72' => 'giggly',
						'73' => 'pensive',
						'74' => 'uncomfortable',
						'75' => 'lethargic',
						'76' => 'listless',
						'77' => 'recumbent',
						'78' => 'exanimate',
						'79' => 'embarrassed',
						'8' => 'cranky',
						'80' => 'envious',
						'81' => 'sympathetic',
						'82' => 'sick',
						'83' => 'hot',
						'84' => 'cold',
						'85' => 'worried',
						'86' => 'loved',
						'87' => 'awake',
						'88' => 'working',
						'89' => 'productive',
						'9' => 'depressed',
						'90' => 'accomplished',
						'91' => 'busy',
						'92' => 'blah',
						'93' => 'full',
						'95' => 'grumpy',
						'96' => 'weird',
						'97' => 'nauseated',
						'98' => 'ecstatic',
						'99' => 'chipper' );

	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>' . __( 'Import LiveJournal' ) . '</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		?>
		<div class="narrow">
		<form action="admin.php?import=livejournal" method="post">
		<?php wp_nonce_field( 'lj-api-import' ) ?>
		<?php if ( get_option( 'ljapi_username' ) && get_option( 'ljapi_password' ) ) : ?>
			<input type="hidden" name="step" value="<?php echo get_option( 'ljapi_step' ) ?>" />
			<p><?php _e( 'It looks like you attempted to import your LiveJournal posts previously and got interrupted.' ) ?></p>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo attribute_escape( __( 'Continue previous import' ) ) ?>" />
			</p>
			<p><a href="<?php echo $_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . attribute_escape( $_SERVER['REQUEST_URI'] ) ?>"><?php _e( 'Cancel &amp; start a new import' ) ?></a></p>
			<p>
		<?php else : ?>
			<input type="hidden" name="step" value="1" />
			<p><?php _e( 'Howdy! This importer allows you to connect directly to LiveJournal and download all your entries and comments' ) ?></p>
			<p><?php _e( 'Enter your LiveJournal username and password below so we can connect to your account:' ) ?></p>
		
			<table class="form-table">

			<tr>
			<th scope="row"><label for="lj_username"><?php _e( 'LiveJournal Username' ) ?></label></th>
			<td><input type="text" name="lj_username" id="lj_username" class="regular-text" /></td>
			</tr>

			<tr>
			<th scope="row"><label for="lj_password"><?php _e( 'LiveJournal Password' ) ?></label></th>
			<td><input type="password" name="lj_password" id="lj_password" class="regular-text" /></td>
			</tr>
      
			</table>
      
			<p><?php _e( 'If you have any entries on LiveJournal which are marked as private, they will be password-protected when they are imported so that only people who know the password can see them.' ) ?></p>
			<p><?php _e( "If you don't enter a password, ALL ENTRIES from your LiveJournal will be imported as public posts in WordPress." ) ?></p>
			<p><?php _e( 'Enter the password you would like to use for all protected entries here:' ) ?></p>
			<table class="form-table">

			<tr>
			<th scope="row"><label for="protected_password"><?php _e( 'Protected Post Password' ) ?></label></th>
			<td><input type="text" name="protected_password" id="protected_password" class="regular-text" /></td>
			</tr>

			</table>

			<p><?php _e( "<strong>WARNING:</strong> This can take a really long time if you have a lot of entries in your LiveJournal, or a lot of comments. Ideally, you should only start this process if you can leave your computer alone while it finishes the import." ) ?></p>
		
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo attribute_escape( __( 'Connect to LiveJournal and Import' ) ) ?>" />
			</p>
		
			<p><?php _e( '<strong>NOTE:</strong> If the import process is interrupted for <em>any</em> reason, come back to this page and it will continue from where it stopped automatically.' ) ?></p>
		<?php endif; ?>
		</form>
		</div>
		<?php
	}
	
	function unhtmlentities($string) { // From php.net for < 4.3 compat
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}
	
	function import_posts() {
		$total           = (int) get_option( 'ljapi_total' );
		$count           = (int) get_option( 'ljapi_count' );
		$lastsync        = get_option( 'ljapi_lastsync' );
		if ( !$lastsync ) {
			update_option( 'ljapi_lastsync', '1900-01-01 00:00:00' );
		}
		$sync_item_times = get_option( 'ljapi_sync_item_times' );
		if ( !is_array( $sync_item_times ) )
			$sync_item_times = array();
		
		do {
			$lastsync = date( 'Y-m-d H:i:s', strtotime( get_option( 'ljapi_lastsync' ) ) );
			$synclist = $this->lj_ixr( 'syncitems', array( 'ver' => 1, 'lastsync' => $lastsync ) );
			$this->log( $synclist, 'ljimport-items-' . $total . '.txt' );
			
			// Keep track of if we've downloaded everything
			$total = $synclist['total'];
			$count = $synclist['count'];
		
			foreach ( $synclist['syncitems'] as $event ) {
				if ( substr( $event['item'], 0, 2 ) == 'L-' ) {
					$sync_item_times[ str_replace( 'L-', '', $event['item'] ) ] = $event['time'];
					if ( $event['time'] > $lastsync )
						$lastsync = $event['time'];
				}
			}

			update_option( 'ljapi_sync_item_times', $sync_item_times );
			update_option( 'ljapi_total', $total );
			update_option( 'ljapi_count', $count );
			update_option( 'ljapi_lastsync', $lastsync );
		} while ( $total > $count );
		// endwhile - all post meta is cached locally
		$this->log( $sync_item_times, 'ljimport-post-mod-times.txt' );
		
		echo '<ol>';
		
		$imported_count = (int) get_option( 'ljapi_imported_count' );
		$lastsync = get_option( 'ljapi_lastsync_posts' );
		if ( !$lastsync )
			update_option( 'ljapi_lastsync_posts', date( 'Y-m-d H:i:s', 0 ) );
		
		do {
			$lastsync = date( 'Y-m-d H:i:s', strtotime( get_option( 'ljapi_lastsync_posts' ) ) );
			
			// Get the batch of items that match up with the syncitems list
			$itemlist = $this->lj_ixr( 'getevents', array( 'ver' => 1,
															'selecttype' => 'syncitems',
															'lineendings' => 'pc',
															'lastsync' => $lastsync ) );
			$this->log( $itemlist, 'ljimport-posts-' . $imported_count . '.txt' );
			if ( is_wp_error( $itemlist ) )
				return $itemlist;
			if ( $num = count( $itemlist['events'] ) ) {
				foreach ( $itemlist['events'] as $event ) {
					$imported_count++;
					$this->import_post( $event );
					if ( $sync_item_times[ $event['itemid'] ] > $lastsync )
						$lastsync = $sync_item_times[ $event['itemid'] ];
				}
				update_option( 'ljapi_lastsync_posts',  $lastsync );
				update_option( 'ljapi_imported_count',  $imported_count );
				update_option( 'ljapi_last_sync_count', $num );
			}
		} while ( $num > 0 );

		echo '</ol>';
	}
	
	function import_post( $post ) {
		global $wpdb;
		
		// Make sure we haven't already imported this one
		if ( $this->get_wp_post_ID( $post['itemid'] ) )
			return;
		
		$user = wp_get_current_user();
		$post_author   = $user->ID;
		$post_status   = ( 'private' == trim( $post['security'] ) ) ? 'private' : 'publish'; // Only me
		$post_password = ( 'usemask' == trim( $post['security'] ) ) ? $this->protected_password : ''; // "Friends" via password

		// For some reason, LJ sometimes sends a date as "2004-04-1408:38:00" (no space btwn date/time)
		$post_date = $post['eventtime'];
		if ( 18 == strlen( $post_date ) )
			$post_date = substr( $post_date, 0, 10 ) . ' ' . substr( $post_date, 10 );
		
		// Cleaning up and linking the title
		$post_title = trim( $post['subject'] );
		$post_title = $this->translate_lj_user( $post_title ); // Translate it, but then we'll strip the link
		$post_title = strip_tags( $post_title ); // Can't have tags in the title in WP
		$post_title = $wpdb->escape( $post_title );
		
		// Clean up content
		$post_content = $post['event'];
		$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', create_function( '$match', 'return "<" . strtolower( $match[1] );' ), $post_content );
		// XHTMLize some tags
		$post_content = str_replace( '<br>', '<br />', $post_content );
		$post_content = str_replace( '<hr>', '<hr />', $post_content );
		// lj-cut ==>  <!--more-->
		$post_content = preg_replace( '|<lj-cut text="([^"]*)">|is', '<!--more $1-->', $post_content );
		$post_content = str_replace( array( '<lj-cut>', '</lj-cut>' ), array( '<!--more-->', '' ), $post_content );
		$first = strpos( $post_content, '<!--more' );
		$post_content = substr( $post_content, 0, $first + 1 ) . preg_replace( '|<!--more(.*)?-->|sUi', '', substr( $post_content, $first + 1 ) );
		// lj-user ==>  a href
		$post_content = $this->translate_lj_user( $post_content );
		$post_content = force_balance_tags( $post_content );
		$post_content = $wpdb->escape( $post_content );
		
		// Handle any tags associated with the post
		$tags_input = !empty( $post['props']['taglist'] ) ? $post['props']['taglist'] : '';
		
		// Check if comments are closed on this post
		$comment_status = !empty( $post['props']['opt_nocomments'] ) ? 'closed' : 'open';

		echo '<li>';
		if ( $post_id = post_exists( $post_title, $post_content, $post_date ) ) {
			printf( __( 'Post <strong>%s</strong> already exists.' ), stripslashes( $post_title ) );
		} else {
			printf( __( 'Importing post <strong>%s</strong>...' ), stripslashes( $post_title ) );
			$postdata = compact( 'post_author', 'post_date', 'post_content', 'post_title', 'post_status', 'post_password', 'tags_input', 'comment_status' );
			$post_id = wp_insert_post( $postdata );
			if ( is_wp_error( $post_id ) )
				return $post_id;
			if ( !$post_id ) {
				_e( "Couldn't get post ID" );
				echo '</li>';
				break;
			}
			$postdata['post_ID']   = $post_id;
			$postdata['lj_itemid'] = $post['itemid'];
			$this->log( $postdata, 'ljimport-post-' . $post_id . '.txt' );
			
			// Handle all the metadata for this post
			$this->insert_postmeta( $post_id, $post );
		}
		echo '</li>';
	}
	
	// Convert lj-user tags to links to that user
	function translate_lj_user( $str ) {
		return preg_replace( '|<lj\s+user\s*=\s*["\']([\w-]+)["\']>|', '<a href="http://$1.livejournal.com/" class="lj-user">$1</a>', $str );
	}
	
	function insert_postmeta( $post_id, $post ) {
		// Need the original LJ id for comments
		add_post_meta( $post_id, 'lj_itemid', $post['itemid'] );
		
		// And save the permalink on LJ in case we want to link back or something
		add_post_meta( $post_id, 'lj_permalink', $post['url'] );
		
		// Supports the following "props" from LJ, saved as lj_<prop_name> in wp_postmeta
		// 		Adult Content - adult_content
		// 		Location - current_coords + current_location
		// 		Mood - current_mood (translated from current_moodid)
		// 		Music - current_music
		// 		Userpic - picture_keyword
		foreach ( array( 'adult_content', 'current_coords', 'current_location', 'current_moodid', 'current_music', 'picture_keyword' ) as $prop ) {
			if ( !empty( $post['props'][$prop] ) ) {
				if ( 'current_moodid' == $prop ) {
					$prop = 'current_mood';
					$val = $this->moods[ $post['props']['current_moodid'] ];
				} else {
					$val = $post['props'][$prop];
				}
				add_post_meta( $post_id, 'lj_' . $prop, $val );
			}
		}
	}
	
	// Loops through and gets comment meta and content from LJ in batches
	// Writes raw XML files to disk for later processing
	function download_comments() {
		// Get a session via XMLRPC
		$cookie = $this->lj_ixr( 'sessiongenerate', array( 'ver' => 1, 'expiration' => 'short' ) );
		
		// Comment Meta
		
		// Load previous state (if any)
		$this->usermap = (array) get_option( 'ljapi_usermap' );
		$maxid         = (int) get_option( 'ljapi_maxid' ) || 1;
		$highest_id    = (int) get_option( 'ljapi_highest_id' );

		// Snoopy is required to handle the cookie
		$this->snoop = new Snoopy();
		$this->snoop->cookies = $cookie;
		
		// We need to loop over the metadata request until we have it all
		while ( $maxid > $highest_id ) {
			// Now get the meta listing
			if ( !$this->snoop->fetch( $this->comments_url . '?get=comment_meta&startid=' . ( $highest_id + 1 ) ) )
				return new WP_Error( 'Snoopy', __( 'Failed to retrieve comment meta information from LiveJournal. Please try again soon.' ) );

			// Snoopy doesn't provide an accessor for results...
			$results = $this->snoop->results;
			
			// Get the maxid so we know if we have them all yet
			preg_match( '|<maxid>(\d+)</maxid>|', $results, $matches );
			$maxid = !empty( $matches[1] ) ? $matches[1] : $maxid;
			
			// Parse comments and get highest id available
			preg_match_all( '|<comment id=\'(\d+)\'|is', $results, $matches );
			foreach ( $matches[1] as $id ) {
				if ( $id > $highest_id )
					$highest_id = $id;
			}

			// Parse out the list of user mappings, and add it to the known list
			preg_match_all( '|<usermap id=\'(\d+)\' user=\'([^\']+)\' />|', $results, $matches );
			foreach ( $matches[1] as $count => $userid )
				$this->usermap[$userid] = $matches[2][$count]; // need this in memory for translating ids => names
				
			update_option( 'ljapi_usermap',    $this->usermap );
			update_option( 'ljapi_maxid',      $maxid );
			update_option( 'ljapi_highest_id', $highest_id );
		}
		// endwhile - should have seen all comment meta at this point
		
		
		// Download Comment XML
		
		// Load previous state (if any)
		$highest_id          = (int) get_option( 'ljapi_highest_comment_id' );
		$comment_xml_files   = get_option( 'ljapi_comment_xml_files' );
		if ( !is_array( $comment_xml_files ) ) {
			update_option( 'ljapi_comment_xml_files', array() );
			$comment_xml_files = array();
		}
		
		echo '<ol>';
		
		// And now request the actual comments, and keep going until we have them all
		while ( $maxid > $highest_id ) {
			// Get a batch of comments, using the highest_id we've already got as a starting point
			if ( !$this->snoop->fetch( $this->comments_url . '?get=comment_body&startid=' . ( $highest_id + 1 ) ) )
				return new WP_Error( 'Snoopy', __( 'Failed to retrieve comment bodies from LiveJournal. Please try again soon.' ) );
			
			// Get the highest post ID in this batch (required for loop control)
			$results = $this->snoop->results;
			preg_match_all( '|<comment id=\'(\d+)\'|i', $results, $comments );
			for ( $r = 0; $r < count( $comments[1] ); $r++ ) {
				if ( $comments[1][$r] > $highest_id )
					$highest_id = $comments[1][$r];
			}
			
			// $this->snoop-results is where the actual response is stored
			$this->log( $this->snoop->results, 'ljimport-comment-bodies-' . $highest_id . '.txt' );
			
			// Store in uploads dir. Can't use *.xml because it's not allowed
			$results = wp_upload_bits( 'raw-comments-' . $highest_id . '.txt', null, $results );
			if ( !empty( $results['error'] ) )
				return new WP_Error( 'xml', $results['error'] );
			$comment_xml_files[] = $results['file'];
			
			echo '<li>' . sprintf( __( 'Downloaded <strong>%s</strong>' ), basename( $results['file'] ) ) . '</li>';
			ob_flush(); flush();
			
			$comment_xml_files = array_unique( $comment_xml_files );
			update_option( 'ljapi_comment_xml_files', $comment_xml_files );
			update_option( 'ljapi_comment_xml_files_count', count( $comment_xml_files ) );
		}
		// endwhile - all comments downloaded and ready for bulk processing
		
		echo '</ol>';
		
		return true;
	}
	
	function parse_comment_xml( $xml_file ) {
		if ( !is_file( $xml_file ) || !is_readable( $xml_file ) )
			return new WP_Error( 'file', sprintf( __( 'Could not access comment XML file: %s'), $filename ) );
			
		// Get content from file
		$xml = @file_get_contents( $xml_file );

		$cache_files = get_option( 'ljapi_comment_cache_files' );
		if ( !is_array( $cache_files ) )
			$cache_files = array();
		
		// Parse XML into comments
		preg_match_all( '|<comment id.*</comment>|iUs', $xml, $matches );
		unset( $xml );
		for ( $c = 0; $c < count( $matches[0] ); $c++ ) {
			$comment = $matches[0][$c];
			
			// Filter out any captured, deleted comments (nothing useful to import)
			$comment = preg_replace( '|<comment id=\'\d+\' jitemid=\'\d+\' posterid=\'\d+\' state=\'D\'[^/]*/>|is', '', $comment );
			
			// Parse this comment into an array
			$comment = $this->parse_comment( $comment );
			if ( empty( $comment['comment_post_ID'] ) )
				continue;
			
			// Add this comment to the appropriate cache file
			$filename = $this->full_path( 'ljimport-comments-' . $comment['comment_post_ID'] . '.php' );
			if ( $this->write_file( '<?php $comments[] = ' . var_export( $comment, true ) . '; ?>' . "\n", 
								$filename, 
								$comment['comment_post_ID'], 
								'a' ) )
			{
				// Keep track of files used
				$cache_files[] = $filename;
			}
		}
		
		// Update list of files in the DB
		sort( $cache_files );
		$cache_files = array_unique( $cache_files );
		update_option( 'ljapi_comment_cache_files', $cache_files );
		update_option( 'ljapi_comment_cache_files_count', count( $cache_files ) );
		$this->close_file_pointers();
		
		// Don't need this XML file anymore
		unlink( $xml_file );
		
		return true;
	}
	
	function parse_comment( $comment ) {
		global $wpdb;
		
		// Get the top-level attributes
		preg_match( '|<comment([^>]+)>|i', $comment, $attribs );
		preg_match( '| id=\'(\d+)\'|i', $attribs[1], $matches );
		$lj_comment_ID = $matches[1];
		preg_match( '| jitemid=\'(\d+)\'|i', $attribs[1], $matches );
		$lj_comment_post_ID = $matches[1];
		preg_match( '| posterid=\'(\d+)\'|i', $attribs[1], $matches );
		$comment_author_ID = $matches[1];
		preg_match( '| parentid=\'(\d+)\'|i', $attribs[1], $matches );
		$lj_comment_parent = $matches[1];
		preg_match( '| state=\'([SDFA])\'|i', $attribs[1], $matches );
		$lj_comment_state = !empty( $matches[1] ) ? $matches[1] : 'A';
		
		// Clean up "subject" - this will become the first line of the comment in WP
		preg_match( '|<subject>(.*)</subject>|is', $comment, $matches );
		$comment_subject = $wpdb->escape( trim( $matches[1] ) );
		if ( 'Re:' == $comment_subject )
			$comment_subject = '';
		
		// Get the body and HTMLize it
		preg_match( '|<body>(.*)</body>|is', $comment, $matches );
		$comment_content = !empty( $comment_subject ) ? $comment_subject . "\n\n" . $matches[1] : $matches[1];
		$comment_content = $this->unhtmlentities( $comment_content );
		$comment_content = wpautop( $comment_content );
		$comment_content = str_replace( '<br>', '<br />', $comment_content );
		$comment_content = str_replace( '<hr>', '<hr />', $comment_content );
		$comment_content = preg_replace_callback( '|<(/?[A-Z]+)|', create_function( '$match', 'return "<" . strtolower( $match[1] );' ), $comment_content );
		$comment_content = $wpdb->escape( trim( $comment_content ) );
		
		// Get and convert the date
		preg_match( '|<date>(.*)</date>|i', $comment, $matches );
		$comment_date = trim( str_replace( array( 'T', 'Z' ), ' ', $matches[1] ) );
		
		// Grab IP if available
		preg_match( '|<property name=\'poster_ip\'>(.*)</property>|i', $comment, $matches );
		$comment_author_IP = $matches[1];
		
		// Try to get something useful for the comment author, especially if it was "my" comment
		$author = ( substr( $this->usermap[$comment_author_ID], 0, 4 ) == 'ext_' || empty( $comment_author_ID ) ) ? __( 'Anonymous' ) : $this->usermap[$comment_author_ID];
		if ( get_option( 'ljapi_username' ) == $author ) {
			$user    = wp_get_current_user();
			$user_id = $user->ID;
			$author  = $user->display_name;
			$url     = trailingslashit( get_option( 'home' ) );
		} else {
			$user_id = 0;
			$url     = ( __( 'Anonymous' ) == $author ) ? '' : 'http://' . $author . '.livejournal.com/';			
		}
		
		// Send back the array of details
		return array( 'lj_comment_ID' => $lj_comment_ID,
						'lj_comment_post_ID' => $lj_comment_post_ID,
						'lj_comment_parent' => ( !empty( $lj_comment_parent ) ? $lj_comment_parent : 0 ),
						'lj_comment_state' => $lj_comment_state,
						'comment_post_ID' => $this->get_wp_post_ID( $lj_comment_post_ID ),
						'comment_author' => $author,
						'comment_author_url' => $url,
						'comment_content' => $comment_content,
						'comment_date' => $comment_date,
						'comment_author_IP' => ( !empty( $comment_author_IP ) ? $comment_author_IP : '' ),
						'comment_approved' => ( in_array( $lj_comment_state, array( 'A', 'F' ) ) ? 1 : 0 ),
						'comment_agent' => 'WP LJ Importer',
						'user_id' => $user_id
						);
	}
	
	
	// Gets the post_ID that a LJ post has been saved as within WP
	function get_wp_post_ID( $post ) {
		global $wpdb;
		if ( empty( $this->postmap[$post] ) )
		 	$this->postmap[$post] = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'lj_itemid' AND meta_value = %d", $post ) );
		return $this->postmap[$post];
	}
	
	// Re-build the threading within a single cache file
	function thread_comments( $filename ) {
		if ( !is_file( $filename ) || !is_readable( $filename ) )
			return new WP_Error( 'File', __( sprintf( 'Cannot access file %s', $filename ) ) );
		
		$comments = array();
		@include( $filename );
		$this->comments = $comments;
		unset( $comments );
		if ( !is_array( $this->comments ) )
			$this->comments = array();
			
		$count = count( $this->comments );
		for ( $c = 0; $c < $count; $c++ ) {
			// Skip anything that's not "top-level" for now
			if ( 0 != $this->comments[$c]['lj_comment_parent'] )
				continue;
			$this->comments[$c]['children'] = $this->get_child_comments( $this->comments[$c]['lj_comment_ID'] );
		}
		
		// Remove anything that's not supposed to be at top level
		$top_comments = array();
		for ( $c = 0; $c < $count; $c++ ) {
			if ( 0 == $this->comments[$c]['lj_comment_parent'] ) {
				$top_comments[] = $this->comments[$c];
			}
		}
		
		// Write back to file
		@unlink( $filename );
		$this->write_file( '<?php $comments = ' . var_export( $top_comments, true ) . '; ?>', $filename, $count, 'w' );
		unset( $top_comments );
		$this->close_file_pointers();
		
		// Reference this file as being threaded
		$files = get_option( 'ljapi_comment_threaded_files' );
		$files[] = $filename;
		array_unique( $files );
		update_option( 'ljapi_comment_threaded_files', $files );
		update_option( 'ljapi_comment_threaded_files_count', count( $files ) );
		
		return true;
	}
	
	function get_child_comments( $id ) {
		$children = array();
		$count = count( $this->comments );
		for ( $c = 0; $c < $count; $c++ ) {
			// This comment is a child of the $id
			if ( $id == $this->comments[$c]['lj_comment_parent'] ) {
				$this->comments[$c]['children'] = $this->get_child_comments( $this->comments[$c]['lj_comment_ID'] );
				$children[] = $this->comments[$c];
			}
		}
		return $children;
	}
	
	// Inserts the contents of each cache file (should be threaded already)
	function insert_comments( $filename ) {
		echo '<ol>';

		if ( !is_file( $filename ) || !is_readable( $filename ) )
			return new WP_Error( 'File', __( sprintf( 'Cannot access file %s', $filename ) ) );
		
		$comments = array();
		@include( $filename );
		$this->comments = $comments;
		unset( $comments );
		if ( !is_array( $this->comments ) )
			$this->comments = array();
			
		$count = count( $this->comments );
		for ( $c = 0; $c < $count; $c++ ) {
			$comment =& $this->comments[$c];
			echo '<li>';
			printf( __( 'Imported comment from <strong>%s</strong> on %s' ), $comment['comment_author'], $comment['comment_date'] );

			$id = wp_insert_comment( $comment );
			$comment['comment_ID'] = $id;
			if ( count( $comment['children'] ) ) {
				_e( ' and replies:' );
				$this->insert_child_comments( $comment['children'], $id );
			}
			
			echo '</li>';
		}
		
		// Remove the file now that we're done with it
		@unlink( $filename );

		echo '</ol>';
		
		return true;
	}
	
	function insert_child_comments( &$comments, $parent ) {
		echo '<ol>';
		$count = count( $comments );
		for ( $c = 0; $c < $count; $c++ ) {
			$comment =& $comments[$c];
			$comment['comment_parent'] = $parent;
			echo '<li>';
			printf( __( 'Imported reply from <strong>%s</strong> on %s' ), $comment['comment_author'], $comment['comment_date'] );

			$id = wp_insert_comment( $comment );
			$comment['comment_ID'] = $id;
			if ( count( $comment['children'] ) ) {
				_e( ' and replies:' );
				$this->insert_child_comments( $comment['children'], $id );
			}
			
			echo '</li>';
		}
		echo '</ol>';
	}
			
	function lj_ixr() {
		if ( $challenge = $this->ixr->query( 'LJ.XMLRPC.getchallenge' ) ) {
			$challenge = $this->ixr->getResponse();
		}
		if ( isset( $challenge['challenge'] ) ) {
			$params = array( 'username' => $this->username,
							'auth_method' => 'challenge',
							'auth_challenge' => $challenge['challenge'],
							'auth_response' => md5( $challenge['challenge'] . md5( $this->password ) ) );
		} else {
			return new WP_Error( 'IXR', __( 'LiveJournal does not appear to be responding right now. Please try again later.' ) );
		}
		
		$args = func_get_args();
        $method = array_shift( $args );
		if ( isset( $args[0] ) )
			$params = array_merge( $params, $args[0] );
		if ( $this->ixr->query( 'LJ.XMLRPC.' . $method, $params ) ) {
			return $this->ixr->getResponse();
		} else {
			$this->log( $this->ixr->message, 'ljimport-error-' . $method . '.txt' );
			return new WP_Error( 'IXR', __( 'XML-RPC Request Failed - ' ) . $this->ixr->getErrorCode() . ': ' . $this->ixr->getErrorMessage() );
		}
	}
	
	function dispatch() {
		if ( empty( $_REQUEST['step'] ) )
			$step = 0;
		else
			$step = (int) $_REQUEST['step'];

		$this->header();
		
		switch ( $step ) {
			case -1 :
				$this->cleanup();
				// Intentional no break
			case 0 :
				$this->greet();
				break;
			case 1 :
			case 2 :
				$this->ixr = new IXR_Client( $this->ixr_url );
				// Intentional no break
			case 3 :
			case 4 :
			case 5 :
				check_admin_referer( 'lj-api-import' );
				$result = $this->{ 'step' . $step }();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}

		$this->footer();
	}

	// Check form inputs and start importing posts
	function step1() {
		// Get details from form or from DB
		if ( !empty( $_POST['lj_username'] ) && !empty( $_POST['lj_password'] ) ) {
			// Store details for later
			$this->username = $_POST['lj_username'];
			$this->password = $_POST['lj_password'];
			update_option( 'ljapi_username', $this->username );
			update_option( 'ljapi_password', $this->password );
		} else {
			$this->username = get_option( 'ljapi_username' );
			$this->password = get_option( 'ljapi_password' );
		}
	
		// This is the password to set on protected posts
		if ( !empty( $_POST['protected_password'] ) ) {
			$this->protected_password = $_POST['protected_password'];
			update_option( 'ljapi_protected_password', $this->protected_password );
		} else {
			$this->protected_password = get_option( 'ljapi_protected_password' );
		}
		
		// Login to confirm the details are correct
		if ( empty( $this->username ) || empty( $this->password ) ) {
			?>
			<p><?php _e( 'Please enter your LiveJournal username <em>and</em> password so we can download your posts and comments.' ) ?></p>
			<p><a href="<?php echo $_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . attribute_escape( str_replace( '&step=1', '', $_SERVER['REQUEST_URI'] ) ) ?>"><?php _e( 'Start again' ) ?></a></p>
			<?php
			return;
		}
		$login = $this->lj_ixr( 'login' );
		if ( is_wp_error( $login ) ) {
			if ( 100 == $this->ixr->getErrorCode() || 101 == $this->ixr->getErrorCode() ) {
				?>
				<p><?php _e( 'Logging in to LiveJournal failed. Check your username and password and try again.' ) ?></p>
				<p><a href="<?php echo $_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . attribute_escape( str_replace( '&step=1', '', $_SERVER['REQUEST_URI'] ) ) ?>"><?php _e( 'Start again' ) ?></a></p>
				<?php
				return;
			} else {
				return $login;
			}
		}
		
		// Set up some options to avoid them autoloading (these ones get big)
		add_option( 'ljapi_sync_item_times',        '', '', 'no' );
		add_option( 'ljapi_usermap',                '', '', 'no' );
		add_option( 'ljapi_comment_xml_files',      '', '', 'no' );
		add_option( 'ljapi_comment_cache_files',    '', '', 'no' );
		add_option( 'ljapi_comment_threaded_files', '', '', 'no' );
		
		echo '<h3>' . __( 'Importing Posts' ) . '</h3>';
		echo '<p>' . __( "We're downloading and importing all your LiveJournal posts..." ) . '</p>';
		ob_flush(); flush();
		
		// Now do the grunt work
		set_time_limit( 0 );
		$result = $this->import_posts();
		if ( is_wp_error( $result ) ) {
			if ( 406 == $this->ixr->getErrorCode() ) {
				?>
				<p><strong><?php _e( 'Uh oh &ndash; LiveJournal has disconnected us because we made too many requests to their servers too quickly.' ) ?></strong></p>
				<p><strong><?php _e( "We've saved where you were up to though, so if you come back to this importer in about 30 minutes, you should be able to continue from where you were." ) ?></strong></p>
				<?php
				return;
			} else {
				return $result;
			}
		}
		
		echo '<p>' . __( "Your posts have all been imported, but wait - there's more! Now we need to process &amp; import your comments." ) . '</p>';
		echo $this->next_step( 2, __( 'Download my comments &raquo;' ) );
		$this->auto_submit();
	}
	
	// Download comments to local XML
	function step2() {
		set_time_limit( 0 );
		update_option( 'ljapi_step', 2 );
		$this->username = get_option( 'ljapi_username' );
		$this->password = get_option( 'ljapi_password' );
		
		echo '<h3>' . __( 'Downloading Comments' ) . '</h3>';
		echo '<p>' . __( 'Now we will download your comments so we can process and import them...' ) . '</p>';
		ob_flush(); flush();
		
		$result = $this->download_comments();
		if ( is_wp_error( $result ) )
			return $result;

		echo '<p>' . __( 'Your comments have all been downloaded to this server now, so we can process them and get them ready for importing.' ) . '</p>';
		echo $this->next_step( 3, __( 'Process my comment files &raquo;' ) );
		$this->auto_submit();
	}

	// Parse XML into comment cache files	
	function step3() {

		set_time_limit( 0 );
		update_option( 'ljapi_step', 3 );
		
		$this->usermap = get_option( 'ljapi_usermap' );

		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Parsing Comments' ) . '</h3>';
		echo '<p>' . __( 'Time to clean up your comments and get them into a format WordPress understands...' ) . '</p>';
		ob_flush(); flush();
		
		$files = get_option( 'ljapi_comment_xml_files' );
		if ( count( $files ) ) {
			$file = array_pop( $files );
		
			$result = $this->parse_comment_xml( $file );
			if ( is_wp_error( $result ) )
				return $result;

			update_option( 'ljapi_comment_xml_files', $files );
		}
		
		if ( count( $files ) ) {
			?>
				<form action="admin.php?import=livejournal" method="post" id="ljapi-auto-repost">
				<p><strong><?php printf( __( 'Processed comment file %d of %d' ), ( get_option( 'ljapi_comment_xml_files_count' ) - count( $files ) ), get_option( 'ljapi_comment_xml_files_count' ) ) ?></strong></p>
				<?php wp_nonce_field( 'lj-api-import' ) ?>
				<input type="hidden" name="step" id="step" value="3" />
				<p><input type="submit" class="button-primary" value="<?php echo attribute_escape( __( 'Process the next comment file &raquo;' ) ) ?>" /> <span id="auto-message"></span></p>
				</form>
				<?php $this->auto_ajax( 'ljapi-auto-repost', 'auto-message', 0 ); ?>
			<?php
		} else {
			echo '<p>' . __( 'Yay, we finished processing all of your comment files! Now we need to re-build your conversation threads.' ) . '</p>';
			echo $this->next_step( 4, __( 'Thread my comments &raquo;' ) );
			$this->auto_submit();
		}
		echo '</div>';
	}

	// Thread comments within their cache files	
	function step4() {
		set_time_limit( 0 );
		update_option( 'ljapi_step', 4 );
		
		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Threading Comments' ) . '</h3>';
		echo '<p>' . __( 'Re-building your conversation threads ready for import...' ) . '</p>';
		ob_flush(); flush();
		
		$files = get_option( 'ljapi_comment_cache_files' );
		if ( count( $files ) ) {
			$file = array_pop( $files );
		
			$result = $this->thread_comments( $file );
			if ( is_wp_error( $result ) )
				return $result;
			
			update_option( 'ljapi_comment_cache_files', $files );
		}
		
		if ( count( $files ) ) {
			?>
				<form action="admin.php?import=livejournal" method="post" id="ljapi-auto-repost">
				<p><strong><?php printf( __( 'Threaded cache file %d of %d' ), ( get_option( 'ljapi_comment_cache_files_count' ) - count( $files ) ), get_option( 'ljapi_comment_cache_files_count' ) ) ?></strong></p>
				<?php wp_nonce_field( 'lj-api-import' ) ?>
				<input type="hidden" name="step" id="step" value="4" />
				<p><input type="submit" class="button-primary" value="<?php echo attribute_escape( __( 'Thread the next cache file &raquo;' ) ) ?>" /> <span id="auto-message"></span></p>
				</form>
				<?php $this->auto_ajax( 'ljapi-auto-repost', 'auto-message', 0 ); ?>
			<?php
		} else {
			echo '<p>' . __( "Alrighty, your comments are all threaded. There's just one last step -- time to actually import them all now!" ) . '</p>';
			echo '<p>' . __( 'This last part in particular can take a really long time if you have a lot of comments. You might want to go and do something else while you wait.' ) . '</p>';
			echo $this->next_step( 5, __( 'Import my threaded comments into WordPress &raquo;' ) );
			$this->auto_submit();
		}
		echo '</div>';
	}

	// Import comments from cache files into WP
	function step5() {
		set_time_limit( 0 );
		update_option( 'ljapi_step', 5 );
		
		
		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Importing Comments' ) . '</h3>';
		echo '<p>' . __( 'This is the big one -- we are now inserting your comment threads into WordPress...' ) . '</p>';
		
		$files = get_option( 'ljapi_comment_threaded_files' );
		echo '<p><strong>' . sprintf( __( 'Importing cache file %d of %d' ), ( get_option( 'ljapi_comment_threaded_files_count' ) - count( $files ) + 1 ), get_option( 'ljapi_comment_threaded_files_count' ) ) . '</strong></p>';
		ob_flush(); flush();
		
		if ( count( $files ) ) {
			$file = array_pop( $files );
		
			$result = $this->insert_comments( $file );
			if ( is_wp_error( $result ) )
				return $result;
			
			update_option( 'ljapi_comment_threaded_files', $files );
		}
		
		if ( count( $files ) ) {
			?>
				<form action="admin.php?import=livejournal" method="post" id="ljapi-auto-repost">
				<?php wp_nonce_field( 'lj-api-import' ) ?>
				<input type="hidden" name="step" id="step" value="5" />
				<p><input type="submit" class="button-primary" value="<?php echo attribute_escape( __( 'Import the next cache file &raquo;' ) ) ?>" /> <span id="auto-message"></span></p>
				</form>
				<?php $this->auto_ajax( 'ljapi-auto-repost', 'auto-message', 0 ); ?>
			<?php
		} else {
			// Clean up database and we're out
			$this->cleanup();
			do_action( 'import_done', 'livejournal' );
			echo '<h3>';
			printf( __( 'All done. <a href="%s">Have fun!</a>' ), get_option( 'home' ) );
			echo '</h3>';
		}
		echo '</div>';
	}
	
	// Returns the HTML for a link to the next page
	function next_step( $next_step, $label, $id = 'ljapi-next-form' ) {
		$str  = '<form action="admin.php?import=livejournal" method="post" id="' . $id . '">';
		$str .= wp_nonce_field( 'lj-api-import', '_wpnonce', true, false );
		$str .= wp_referer_field( false );
		$str .= '<input type="hidden" name="step" id="step" value="' . $next_step . '" />';
		$str .= '<p><input type="submit" class="button-primary" value="' . attribute_escape( $label ) . '" /> <span id="auto-message"></span></p>';
		$str .= '</form>';
		
		return $str;
	}

	// Automatically submit the form with #id to continue the process
	// Hide any submit buttons to avoid people clicking them
	// Display a countdown in the element indicated by $msg for "Continuing in x"
	function auto_ajax( $id = 'ljapi-next-form', $msg = 'auto-message', $seconds = 5 ) {
		?><script type="text/javascript">
			next_counter = <?php echo $seconds ?>;
			jQuery(document).ready(function(){
				ljapi_msg();
			});
			
			function ljapi_msg() {
				str = '<?php _e( "Continuing in %d" ) ?>';
				jQuery( '#<?php echo $msg ?>' ).text( str.replace( /%d/, next_counter ) );
				if ( next_counter <= 0 ) {
					if ( jQuery( '#<?php echo $id ?>' ).length ) {
						jQuery( "#<?php echo $id ?> input[type='submit']" ).hide();
						jQuery.ajaxSetup({'timeout':3600000});
						str = '<?php _e( "Processing next file." ) ?> <img src="images/loading-publish.gif" alt="" id="processing" align="top" />';
						jQuery( '#<?php echo $msg ?>' ).html( str );
						jQuery('#ljapi-status').load(ajaxurl, {'action':'lj-importer',
																'step':jQuery('#step').val(),
																'_wpnonce':'<?php echo wp_create_nonce( 'lj-api-import' ) ?>',
																'_wp_http_referer':'<?php echo $_SERVER['REQUEST_URI'] ?>'});
						return;
					}
				}
				next_counter = next_counter - 1;
				setTimeout('ljapi_msg()', 1000);
			}
		</script><?php
	}
	
	// Automatically submit the specified form after $seconds
	// Include a friendly countdown in the element with id=$msg
	function auto_submit( $id = 'ljapi-next-form', $msg = 'auto-message', $seconds = 10 ) {
		?><script type="text/javascript">
			next_counter = <?php echo $seconds ?>;
			jQuery(document).ready(function(){
				ljapi_msg();
			});
			
			function ljapi_msg() {
				str = '<?php _e( "Continuing in %d" ) ?>';
				jQuery( '#<?php echo $msg ?>' ).text( str.replace( /%d/, next_counter ) );
				if ( next_counter <= 0 ) {
					if ( jQuery( '#<?php echo $id ?>' ).length ) {
						jQuery( "#<?php echo $id ?> input[type='submit']" ).hide();
						str = '<?php _e( "Continuing" ) ?> <img src="images/loading-publish.gif" alt="" id="processing" align="top" />';
						jQuery( '#<?php echo $msg ?>' ).html( str );
						jQuery( '#<?php echo $id ?>' ).submit();
						return;
					}
				}
				next_counter = next_counter - 1;
				setTimeout('ljapi_msg()', 1000);
			}
		</script><?php
	}

	// Remove all options used during import process
	function cleanup() {
		delete_option( 'ljapi_username' );
		delete_option( 'ljapi_password' );
		delete_option( 'ljapi_protected_password' );
		delete_option( 'ljapi_total' );
		delete_option( 'ljapi_count' );
		delete_option( 'ljapi_lastsync' );
		delete_option( 'ljapi_last_sync_count' );
		delete_option( 'ljapi_sync_item_times' );
		delete_option( 'ljapi_lastsync_posts' );
		delete_option( 'ljapi_imported_count' );
		delete_option( 'ljapi_maxid' );
		delete_option( 'ljapi_usermap' );
		delete_option( 'ljapi_highest_id' );
		delete_option( 'ljapi_highest_comment_id' );
		delete_option( 'ljapi_comment_xml_files' );
		delete_option( 'ljapi_comment_xml_files_count' );
		delete_option( 'ljapi_comment_cache_files' );
		delete_option( 'ljapi_comment_cache_files_count' );
		delete_option( 'ljapi_comment_threaded_files' );
		delete_option( 'ljapi_comment_threaded_files_count' );
		delete_option( 'ljapi_step' );
	}
	
	// Dump a string to a log file (appends to existing file)
	function log( $string, $name ) {
		return; // remove this to enable "debugging" output to files in /wp-content/ljimport
		$path = wp_upload_dir();
		$path = $path['path'];
		if ( get_option( 'uploads_use_yearmonth_folders' ) )
			$path = substr( $path, 0, -8 );

		if ( !is_dir( $path . '/ljimport' ) )
			mkdir( $path . '/ljimport' );
			
		$fh = @fopen( $path . '/ljimport/' . $name, 'a' );
		if ( $fh ) {
			if ( is_array( $string ) || is_object( $string ) )
				fwrite( $fh, var_export( $string, true ) . "\n\n" );
			else
				fwrite( $fh, $string . "\n\n" );
			fclose( $fh );
		}
	}
	
	function write_file( $data, $name, $id, $mode = 'a' ) {
		if ( empty( $this->pointers[$id] ) )
			$this->pointers[$id] = @fopen( $name, $mode );
		if ( $this->pointers[$id] )
			return fwrite( $this->pointers[$id], $data );
		return false;
	}
	
	function full_path( $basename ) {
		$uploads = wp_upload_dir();
		return $uploads['path'] . '/' . $basename;
	}
	
	function close_file_pointers() {
		foreach ( $this->pointers as $p )
			@fclose( $p );
	}

	function LJ_API_Import() {
		$this->__construct();
	}
	
	function __construct() {
		// Nothing
	}
}

$lj_api_import = new LJ_API_Import();

register_importer( 'livejournal', __( 'LiveJournal' ), __( 'Import posts from LiveJournal using their API.' ), array( $lj_api_import, 'dispatch' ) );
?>
