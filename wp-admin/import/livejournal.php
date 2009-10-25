<?php

/**
 * LiveJournal API Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

// XML-RPC library for communicating with LiveJournal API
require_once( ABSPATH . WPINC . '/class-IXR.php' );

/**
 * LiveJournal API Importer class
 *
 * Imports your LiveJournal contents into WordPress using the LJ API
 *
 * @since 2.8
 */
class LJ_API_Import {

	var $comments_url = 'http://www.livejournal.com/export_comments.bml';
	var $ixr_url      = 'http://www.livejournal.com/interface/xmlrpc';
	var $ixr;
	var $username;
	var $password;
	var $comment_meta;
	var $comments;
	var $usermap;
	var $postmap;
	var $commentmap;
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
			<input type="hidden" name="step" value="<?php echo esc_attr( get_option( 'ljapi_step' ) ) ?>" />
			<p><?php _e( 'It looks like you attempted to import your LiveJournal posts previously and got interrupted.' ) ?></p>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Continue previous import' ) ?>" />
			</p>
			<p class="submitbox"><a href="<?php echo esc_url($_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . esc_attr( $_SERVER['REQUEST_URI'] )) ?>" class="deletion submitdelete"><?php _e( 'Cancel &amp; start a new import' ) ?></a></p>
			<p>
		<?php else : ?>
			<input type="hidden" name="step" value="1" />
			<input type="hidden" name="login" value="true" />
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
			<p><?php _e( 'If you don&#8217;t enter a password, ALL ENTRIES from your LiveJournal will be imported as public posts in WordPress.' ) ?></p>
			<p><?php _e( 'Enter the password you would like to use for all protected entries here:' ) ?></p>
			<table class="form-table">

			<tr>
			<th scope="row"><label for="protected_password"><?php _e( 'Protected Post Password' ) ?></label></th>
			<td><input type="text" name="protected_password" id="protected_password" class="regular-text" /></td>
			</tr>

			</table>

			<p><?php _e( "<strong>WARNING:</strong> This can take a really long time if you have a lot of entries in your LiveJournal, or a lot of comments. Ideally, you should only start this process if you can leave your computer alone while it finishes the import." ) ?></p>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Connect to LiveJournal and Import' ) ?>" />
			</p>

			<p><?php _e( '<strong>NOTE:</strong> If the import process is interrupted for <em>any</em> reason, come back to this page and it will continue from where it stopped automatically.' ) ?></p>

			<noscript>
				<p><?php _e( '<strong>NOTE:</strong> You appear to have JavaScript disabled, so you will need to manually click through each step of this importer. If you enable JavaScript, it will step through automatically.' ) ?></p>
			</noscript>
		<?php endif; ?>
		</form>
		</div>
		<?php
	}

	function download_post_meta() {
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
			if ( is_wp_error( $synclist ) )
				return $synclist;

			// Keep track of if we've downloaded everything
			$total = $synclist['total'];
			$count = $synclist['count'];

			foreach ( $synclist['syncitems'] as $event ) {
				if ( substr( $event['item'], 0, 2 ) == 'L-' ) {
					$sync_item_times[ str_replace( 'L-', '', $event['item'] ) ] = $event['time'];
					if ( $event['time'] > $lastsync ) {
						$lastsync = $event['time'];
						update_option( 'ljapi_lastsync', $lastsync );
					}
				}
			}
		} while ( $total > $count );
		// endwhile - all post meta is cached locally
		unset( $synclist );
		update_option( 'ljapi_sync_item_times', $sync_item_times );
		update_option( 'ljapi_total', $total );
		update_option( 'ljapi_count', $count );

		echo '<p>' . __( 'Post metadata has been downloaded, proceeding with posts...' ) . '</p>';
	}

	function download_post_bodies() {
		$imported_count  = (int) get_option( 'ljapi_imported_count' );
		$sync_item_times = get_option( 'ljapi_sync_item_times' );
		$lastsync        = get_option( 'ljapi_lastsync_posts' );
		if ( !$lastsync )
			update_option( 'ljapi_lastsync_posts', date( 'Y-m-d H:i:s', 0 ) );

		$count = 0;
		echo '<ol>';
		do {
			$lastsync = date( 'Y-m-d H:i:s', strtotime( get_option( 'ljapi_lastsync_posts' ) ) );

			// Get the batch of items that match up with the syncitems list
			$itemlist = $this->lj_ixr( 'getevents', array( 'ver' => 1,
															'selecttype' => 'syncitems',
															'lineendings' => 'pc',
															'lastsync' => $lastsync ) );
			if ( is_wp_error( $itemlist ) )
				return $itemlist;

			if ( $num = count( $itemlist['events'] ) ) {
				for ( $e = 0; $e < count( $itemlist['events'] ); $e++ ) {
					$event = $itemlist['events'][$e];
					$imported_count++;
					$inserted = $this->import_post( $event );
					if ( is_wp_error( $inserted ) )
						return $inserted;
					if ( $sync_item_times[ $event['itemid'] ] > $lastsync )
						$lastsync = $sync_item_times[ $event['itemid'] ];
					wp_cache_flush();
				}
				update_option( 'ljapi_lastsync_posts',  $lastsync );
				update_option( 'ljapi_imported_count',  $imported_count );
				update_option( 'ljapi_last_sync_count', $num );
			}
			$count++;
		} while ( $num > 0 && $count < 3 ); // Doing up to 3 requests at a time to avoid memory problems

		// Used so that step1 knows when to stop posting back on itself
		update_option( 'ljapi_last_sync_count', $num );

		// Counter just used to show progress to user
		update_option( 'ljapi_post_batch', ( (int) get_option( 'ljapi_post_batch' ) + 1 ) );

		echo '</ol>';
	}

	function _normalize_tag( $matches ) {
		return '<' . strtolower( $match[1] );
	}

	function import_post( $post ) {
		global $wpdb;

		// Make sure we haven't already imported this one
		if ( $this->get_wp_post_ID( $post['itemid'] ) )
			return;

		$user = wp_get_current_user();
		$post_author      = $user->ID;
		$post['security'] = !empty( $post['security'] ) ? $post['security'] : '';
		$post_status      = ( 'private' == trim( $post['security'] ) ) ? 'private' : 'publish'; // Only me
		$post_password    = ( 'usemask' == trim( $post['security'] ) ) ? $this->protected_password : ''; // "Friends" via password

		// For some reason, LJ sometimes sends a date as "2004-04-1408:38:00" (no space btwn date/time)
		$post_date = $post['eventtime'];
		if ( 18 == strlen( $post_date ) )
			$post_date = substr( $post_date, 0, 10 ) . ' ' . substr( $post_date, 10 );

		// Cleaning up and linking the title
		$post_title = isset( $post['subject'] ) ? trim( $post['subject'] ) : '';
		$post_title = $this->translate_lj_user( $post_title ); // Translate it, but then we'll strip the link
		$post_title = strip_tags( $post_title ); // Can't have tags in the title in WP
		$post_title = $wpdb->escape( $post_title );

		// Clean up content
		$post_content = $post['event'];
		$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_content );
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
		//$post_content = force_balance_tags( $post_content );
		$post_content = $wpdb->escape( $post_content );

		// Handle any tags associated with the post
		$tags_input = !empty( $post['props']['taglist'] ) ? $post['props']['taglist'] : '';

		// Check if comments are closed on this post
		$comment_status = !empty( $post['props']['opt_nocomments'] ) ? 'closed' : 'open';

		echo '<li>';
		if ( $post_id = post_exists( $post_title, $post_content, $post_date ) ) {
			printf( __( 'Post <strong>%s</strong> already exists.' ), stripslashes( $post_title ) );
		} else {
			printf( __( 'Imported post <strong>%s</strong>...' ), stripslashes( $post_title ) );
			$postdata = compact( 'post_author', 'post_date', 'post_content', 'post_title', 'post_status', 'post_password', 'tags_input', 'comment_status' );
			$post_id = wp_insert_post( $postdata, true );
			if ( is_wp_error( $post_id ) ) {
				if ( 'empty_content' == $post_id->get_error_code() )
					return; // Silent skip on "empty" posts
				return $post_id;
			}
			if ( !$post_id ) {
				_e( 'Couldn&#8217;t get post ID (creating post failed!)' );
				echo '</li>';
				return new WP_Error( 'insert_post_failed', __( 'Failed to create post.' ) );
			}

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

	// Set up a session (authenticate) with LJ
	function get_session() {
		// Get a session via XMLRPC
		$cookie = $this->lj_ixr( 'sessiongenerate', array( 'ver' => 1, 'expiration' => 'short' ) );
		if ( is_wp_error( $cookie ) )
			return new WP_Error( 'cookie', __( 'Could not get a cookie from LiveJournal. Please try again soon.' ) );
		return new WP_Http_Cookie( array( 'name' => 'ljsession', 'value' => $cookie['ljsession'] ) );
	}

	// Loops through and gets comment meta from LJ in batches
	function download_comment_meta() {
		$cookie = $this->get_session();
		if ( is_wp_error( $cookie ) )
			return $cookie;

		// Load previous state (if any)
		$this->usermap = (array) get_option( 'ljapi_usermap' );
		$maxid         = get_option( 'ljapi_maxid' ) ? get_option( 'ljapi_maxid' ) : 1;
		$highest_id    = get_option( 'ljapi_highest_id' ) ? get_option( 'ljapi_highest_id' ) : 0;

		// We need to loop over the metadata request until we have it all
		while ( $maxid > $highest_id ) {
			// Now get the meta listing
			$results = wp_remote_get( $this->comments_url . '?get=comment_meta&startid=' . ( $highest_id + 1 ),
										array( 'cookies' => array( $cookie ), 'timeout' => 20 ) );
			if ( is_wp_error( $results ) )
				return new WP_Error( 'comment_meta', __( 'Failed to retrieve comment meta information from LiveJournal. Please try again soon.' ) );

			$results = wp_remote_retrieve_body( $results );

			// Get the maxid so we know if we have them all yet
			preg_match( '|<maxid>(\d+)</maxid>|', $results, $matches );
			if ( 0 == $matches[1] ) {
				// No comment meta = no comments for this journal
				echo '<p>' . __( 'You have no comments to import!' ) . '</p>';
				update_option( 'ljapi_highest_id', 1 );
				update_option( 'ljapi_highest_comment_id', 1 );
				return false; // Bail out of comment importing entirely
			}
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

			wp_cache_flush();
		}
		// endwhile - should have seen all comment meta at this point

		update_option( 'ljapi_usermap',    $this->usermap );
		update_option( 'ljapi_maxid',      $maxid );
		update_option( 'ljapi_highest_id', $highest_id );

		echo '<p>' . __( ' Comment metadata downloaded successfully, proceeding with comment bodies...' ) . '</p>';

		return true;
	}

	// Downloads actual comment bodies from LJ
	// Inserts them all directly to the DB, with additional info stored in "spare" fields
	function download_comment_bodies() {
		global $wpdb;
		$cookie = $this->get_session();
		if ( is_wp_error( $cookie ) )
			return $cookie;

		// Load previous state (if any)
		$this->usermap = (array) get_option( 'ljapi_usermap' );
		$maxid         = get_option( 'ljapi_maxid' ) ? (int) get_option( 'ljapi_maxid' ) : 1;
		$highest_id    = (int) get_option( 'ljapi_highest_comment_id' );
		$loop = 0;
		while ( $maxid > $highest_id && $loop < 5 ) { // We do 5 loops per call to avoid memory limits
			$loop++;

			// Get a batch of comments, using the highest_id we've already got as a starting point
			$results = wp_remote_get( $this->comments_url . '?get=comment_body&startid=' . ( $highest_id + 1 ),
										array( 'cookies' => array( $cookie ), 'timeout' => 20 ) );
			if ( is_wp_error( $results ) )
				return new WP_Error( 'comment_bodies', __( 'Failed to retrieve comment bodies from LiveJournal. Please try again soon.' ) );

			$results = wp_remote_retrieve_body( $results );

			// Parse out each comment and insert directly
			preg_match_all( '|<comment id=\'(\d+)\'.*</comment>|iUs', $results, $matches );
			for ( $c = 0; $c < count( $matches[0] ); $c++ ) {
				// Keep track of highest id seen
				if ( $matches[1][$c] > $highest_id ) {
					$highest_id = $matches[1][$c];
					update_option( 'ljapi_highest_comment_id', $highest_id );
				}

				$comment = $matches[0][$c];

				// Filter out any captured, deleted comments (nothing useful to import)
				$comment = preg_replace( '|<comment id=\'\d+\' jitemid=\'\d+\' posterid=\'\d+\' state=\'D\'[^/]*/>|is', '', $comment );

				// Parse this comment into an array and insert
				$comment = $this->parse_comment( $comment );
				$id = wp_insert_comment( $comment );

				// Clear cache
				clean_comment_cache( $id );
			}

			// Clear cache to preseve memory
			wp_cache_flush();
		}
		// endwhile - all comments downloaded and ready for bulk processing

		// Counter just used to show progress to user
		update_option( 'ljapi_comment_batch', ( (int) get_option( 'ljapi_comment_batch' ) + 1 ) );

		return true;
	}

	// Takes a block of XML and parses out all the elements of the comment
	function parse_comment( $comment ) {
		global $wpdb;

		// Get the top-level attributes
		preg_match( '|<comment([^>]+)>|i', $comment, $attribs );
		preg_match( '| id=\'(\d+)\'|i', $attribs[1], $matches );
		$lj_comment_ID = $matches[1];
		preg_match( '| jitemid=\'(\d+)\'|i', $attribs[1], $matches );
		$lj_comment_post_ID = $matches[1];
		preg_match( '| posterid=\'(\d+)\'|i', $attribs[1], $matches );
		$comment_author_ID = isset( $matches[1] ) ? $matches[1] : 0;
		preg_match( '| parentid=\'(\d+)\'|i', $attribs[1], $matches ); // optional
		$lj_comment_parent = isset( $matches[1] ) ? $matches[1] : 0;
		preg_match( '| state=\'([SDFA])\'|i', $attribs[1], $matches ); // optional
		$lj_comment_state = isset( $matches[1] ) ? $matches[1] : 'A';

		// Clean up "subject" - this will become the first line of the comment in WP
		preg_match( '|<subject>(.*)</subject>|is', $comment, $matches );
		if ( isset( $matches[1] ) ) {
			$comment_subject = $wpdb->escape( trim( $matches[1] ) );
			if ( 'Re:' == $comment_subject )
				$comment_subject = '';
		}

		// Get the body and HTMLize it
		preg_match( '|<body>(.*)</body>|is', $comment, $matches );
		$comment_content = !empty( $comment_subject ) ? $comment_subject . "\n\n" . $matches[1] : $matches[1];
		$comment_content = @html_entity_decode( $comment_content, ENT_COMPAT, get_option('blog_charset') );
		$comment_content = str_replace( '&apos;', "'", $comment_content );
		$comment_content = wpautop( $comment_content );
		$comment_content = str_replace( '<br>', '<br />', $comment_content );
		$comment_content = str_replace( '<hr>', '<hr />', $comment_content );
		$comment_content = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $comment_content );
		$comment_content = $wpdb->escape( trim( $comment_content ) );

		// Get and convert the date
		preg_match( '|<date>(.*)</date>|i', $comment, $matches );
		$comment_date = trim( str_replace( array( 'T', 'Z' ), ' ', $matches[1] ) );

		// Grab IP if available
		preg_match( '|<property name=\'poster_ip\'>(.*)</property>|i', $comment, $matches ); // optional
		$comment_author_IP = isset( $matches[1] ) ? $matches[1] : '';

		// Try to get something useful for the comment author, especially if it was "my" comment
		$author = ( empty( $comment_author_ID ) || empty( $this->usermap[$comment_author_ID] ) || substr( $this->usermap[$comment_author_ID], 0, 4 ) == 'ext_' ) ? __( 'Anonymous' ) : $this->usermap[$comment_author_ID];
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
						'comment_author_email' => '',
						'comment_content' => $comment_content,
						'comment_date' => $comment_date,
						'comment_author_IP' => ( !empty( $comment_author_IP ) ? $comment_author_IP : '' ),
						'comment_approved' => ( in_array( $lj_comment_state, array( 'A', 'F' ) ) ? 1 : 0 ),
						'comment_karma' => $lj_comment_ID, // Need this and next value until rethreading is done
						'comment_agent' => $lj_comment_parent,
						'comment_type' => 'livejournal',  // Custom type, so we can find it later for processing
						'user_ID' => $user_id
					);
	}


	// Gets the post_ID that a LJ post has been saved as within WP
	function get_wp_post_ID( $post ) {
		global $wpdb;

		if ( empty( $this->postmap[$post] ) )
		 	$this->postmap[$post] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'lj_itemid' AND meta_value = %d", $post ) );

		return $this->postmap[$post];
	}

	// Gets the comment_ID that a LJ comment has been saved as within WP
	function get_wp_comment_ID( $comment ) {
		global $wpdb;
		if ( empty( $this->commentmap[$comment] ) )
		 	$this->commentmap[$comment] = $wpdb->get_var( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_karma = %d", $comment ) );
		return $this->commentmap[$comment];
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
			return new WP_Error( 'IXR', __( 'LiveJournal is not responding to authentication requests. Please wait a while and then try again.' ) );
		}

		$args = func_get_args();
        $method = array_shift( $args );
		if ( isset( $args[0] ) )
			$params = array_merge( $params, $args[0] );
		if ( $this->ixr->query( 'LJ.XMLRPC.' . $method, $params ) ) {
			return $this->ixr->getResponse();
		} else {
			return new WP_Error( 'IXR', __( 'XML-RPC Request Failed -- ' ) . $this->ixr->getErrorCode() . ': ' . $this->ixr->getErrorMessage() );
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
			case 3 :
				check_admin_referer( 'lj-api-import' );
				$result = $this->{ 'step' . $step }();
				if ( is_wp_error( $result ) ) {
					$this->throw_error( $result, $step );
				}
				break;
		}

		$this->footer();
	}

	// Technically the first half of step 1, this is separated to allow for AJAX
	// calls. Sets up some variables and options and confirms authentication.
	function setup() {
		global $verified;
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

		// Log in to confirm the details are correct
		if ( empty( $this->username ) || empty( $this->password ) ) {
			?>
			<p><?php _e( 'Please enter your LiveJournal username <em>and</em> password so we can download your posts and comments.' ) ?></p>
			<p><a href="<?php echo esc_url($_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . esc_attr( str_replace( '&step=1', '', $_SERVER['REQUEST_URI'] ) ) ) ?>"><?php _e( 'Start again' ) ?></a></p>
			<?php
			return false;
		}
		$verified = $this->lj_ixr( 'login' );
		if ( is_wp_error( $verified ) ) {
			if ( 100 == $this->ixr->getErrorCode() || 101 == $this->ixr->getErrorCode() ) {
				delete_option( 'ljapi_username' );
				delete_option( 'ljapi_password' );
				delete_option( 'ljapi_protected_password' );
				?>
				<p><?php _e( 'Logging in to LiveJournal failed. Check your username and password and try again.' ) ?></p>
				<p><a href="<?php echo esc_url($_SERVER['PHP_SELF'] . '?import=livejournal&amp;step=-1&amp;_wpnonce=' . wp_create_nonce( 'lj-api-import' ) . '&amp;_wp_http_referer=' . esc_attr( str_replace( '&step=1', '', $_SERVER['REQUEST_URI'] ) ) ) ?>"><?php _e( 'Start again' ) ?></a></p>
				<?php
				return false;
			} else {
				return $verified;
			}
		} else {
			update_option( 'ljapi_verified', 'yes' );
		}

		// Set up some options to avoid them autoloading (these ones get big)
		add_option( 'ljapi_sync_item_times',  '', '', 'no' );
		add_option( 'ljapi_usermap',          '', '', 'no' );
		update_option( 'ljapi_comment_batch', 0 );

		return true;
	}

	// Check form inputs and start importing posts
	function step1() {
		global $verified;
		set_time_limit( 0 );
		update_option( 'ljapi_step', 1 );
		if ( !$this->ixr ) $this->ixr = new IXR_Client( $this->ixr_url, false, 80, 30 );
		if ( empty( $_POST['login'] ) ) {
			// We're looping -- load some details from DB
			$this->username = get_option( 'ljapi_username' );
			$this->password = get_option( 'ljapi_password' );
			$this->protected_password = get_option( 'ljapi_protected_password' );
		} else {
			// First run (non-AJAX)
			$setup = $this->setup();
			if ( !$setup ) {
				return false;
			} else if ( is_wp_error( $setup ) ) {
				$this->throw_error( $setup, 1 );
				return false;
			}
		}

		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Importing Posts' ) . '</h3>';
		echo '<p>' . __( 'We&#8217;re downloading and importing your LiveJournal posts...' ) . '</p>';
		if ( get_option( 'ljapi_post_batch' ) && count( get_option( 'ljapi_sync_item_times' ) ) ) {
			$batch = count( get_option( 'ljapi_sync_item_times' ) );
			$batch = $count > 300 ? ceil( $batch / 300 ) : 1;
			echo '<p><strong>' . sprintf( __( 'Imported post batch %d of <strong>approximately</strong> %d' ), ( get_option( 'ljapi_post_batch' ) + 1 ), $batch ) . '</strong></p>';
		}
		ob_flush(); flush();

		if ( !get_option( 'ljapi_lastsync' ) || '1900-01-01 00:00:00' == get_option( 'ljapi_lastsync' ) ) {
			// We haven't downloaded meta yet, so do that first
			$result = $this->download_post_meta();
			if ( is_wp_error( $result ) ) {
				$this->throw_error( $result, 1 );
				return false;
			}
		}

		// Download a batch of actual posts
		$result = $this->download_post_bodies();
		if ( is_wp_error( $result ) ) {
			if ( 406 == $this->ixr->getErrorCode() ) {
				?>
				<p><strong><?php _e( 'Uh oh &ndash; LiveJournal has disconnected us because we made too many requests to their servers too quickly.' ) ?></strong></p>
				<p><strong><?php _e( 'We&#8217;ve saved where you were up to though, so if you come back to this importer in about 30 minutes, you should be able to continue from where you were.' ) ?></strong></p>
				<?php
				echo $this->next_step( 1, __( 'Try Again' ) );
				return false;
			} else {
				$this->throw_error( $result, 1 );
				return false;
			}
		}

		if ( get_option( 'ljapi_last_sync_count' ) > 0 ) {
		?>
			<form action="admin.php?import=livejournal" method="post" id="ljapi-auto-repost">
			<?php wp_nonce_field( 'lj-api-import' ) ?>
			<input type="hidden" name="step" id="step" value="1" />
			<p><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Import the next batch' ) ?>" /> <span id="auto-message"></span></p>
			</form>
			<?php $this->auto_ajax( 'ljapi-auto-repost', 'auto-message', 0 ); ?>
		<?php
		} else {
			echo '<p>' . __( 'Your posts have all been imported, but wait &#8211; there&#8217;s more! Now we need to download &amp; import your comments.' ) . '</p>';
			echo $this->next_step( 2, __( 'Download my comments &raquo;' ) );
			$this->auto_submit();
		}
		echo '</div>';
	}

	// Download comments to local XML
	function step2() {
		set_time_limit( 0 );
		update_option( 'ljapi_step', 2 );
		$this->username = get_option( 'ljapi_username' );
		$this->password = get_option( 'ljapi_password' );
		$this->ixr = new IXR_Client( $this->ixr_url, false, 80, 30 );

		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Downloading Comments' ) . '</h3>';
		echo '<p>' . __( 'Now we will download your comments so we can import them (this could take a <strong>long</strong> time if you have lots of comments)...' ) . '</p>';
		ob_flush(); flush();

		if ( !get_option( 'ljapi_usermap' ) ) {
			// We haven't downloaded meta yet, so do that first
			$result = $this->download_comment_meta();
			if ( is_wp_error( $result ) ) {
				$this->throw_error( $result, 2 );
				return false;
			}
		}

		// Download a batch of actual comments
		$result = $this->download_comment_bodies();
		if ( is_wp_error( $result ) ) {
			$this->throw_error( $result, 2 );
			return false;
		}

		$maxid      = get_option( 'ljapi_maxid' ) ? (int) get_option( 'ljapi_maxid' ) : 1;
		$highest_id = (int) get_option( 'ljapi_highest_comment_id' );
		if ( $maxid > $highest_id ) {
			$batch = $maxid > 5000 ? ceil( $maxid / 5000 ) : 1;
		?>
			<form action="admin.php?import=livejournal" method="post" id="ljapi-auto-repost">
			<p><strong><?php printf( __( 'Imported comment batch %d of <strong>approximately</strong> %d' ), get_option( 'ljapi_comment_batch' ), $batch ) ?></strong></p>
			<?php wp_nonce_field( 'lj-api-import' ) ?>
			<input type="hidden" name="step" id="step" value="2" />
			<p><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Import the next batch' ) ?>" /> <span id="auto-message"></span></p>
			</form>
			<?php $this->auto_ajax( 'ljapi-auto-repost', 'auto-message', 0 ); ?>
		<?php
		} else {
			echo '<p>' . __( 'Your comments have all been imported now, but we still need to rebuild your conversation threads.' ) . '</p>';
			echo $this->next_step( 3, __( 'Rebuild my comment threads &raquo;' ) );
			$this->auto_submit();
		}
		echo '</div>';
	}

	// Re-thread comments already in the DB
	function step3() {
		global $wpdb;
		set_time_limit( 0 );
		update_option( 'ljapi_step', 3 );

		echo '<div id="ljapi-status">';
		echo '<h3>' . __( 'Threading Comments' ) . '</h3>';
		echo '<p>' . __( 'We are now re-building the threading of your comments (this can also take a while if you have lots of comments)...' ) . '</p>';
		ob_flush(); flush();

		// Only bother adding indexes if they have over 5000 comments (arbitrary number)
		$imported_comments = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_type = 'livejournal'" );
		$added_indices = false;
		if ( 5000 < $imported_comments ) {
			include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$added_indices = true;
			add_clean_index( $wpdb->comments, 'comment_type'  );
			add_clean_index( $wpdb->comments, 'comment_karma' );
			add_clean_index( $wpdb->comments, 'comment_agent' );
		}

		// Get LJ comments, which haven't been threaded yet, 5000 at a time and thread them
		while ( $comments = $wpdb->get_results( "SELECT comment_ID, comment_agent FROM {$wpdb->comments} WHERE comment_type = 'livejournal' AND comment_agent != '0' LIMIT 5000", OBJECT ) ) {
			foreach ( $comments as $comment ) {
				$wpdb->update( $wpdb->comments,
								array( 'comment_parent' => $this->get_wp_comment_ID( $comment->comment_agent ), 'comment_type' => 'livejournal-done' ),
								array( 'comment_ID' => $comment->comment_ID ) );
			}
			wp_cache_flush();
			$wpdb->flush();
		}

		// Revert the comments table back to normal and optimize it to reclaim space
		if ( $added_indices ) {
			drop_index( $wpdb->comments, 'comment_type'  );
			drop_index( $wpdb->comments, 'comment_karma' );
			drop_index( $wpdb->comments, 'comment_agent' );
			$wpdb->query( "OPTIMIZE TABLE {$wpdb->comments}" );
		}

		// Clean up database and we're out
		$this->cleanup();
		do_action( 'import_done', 'livejournal' );
		if ( $imported_comments > 1 )
			echo '<p>' . sprintf( __( "Successfully re-threaded %s comments." ), number_format( $imported_comments ) ) . '</p>';
		echo '<h3>';
		printf( __( 'All done. <a href="%s">Have fun!</a>' ), get_option( 'home' ) );
		echo '</h3>';
		echo '</div>';
	}

	// Output an error message with a button to try again.
	function throw_error( $error, $step ) {
		echo '<p><strong>' . $error->get_error_message() . '</strong></p>';
		echo $this->next_step( $step, __( 'Try Again' ) );
	}

	// Returns the HTML for a link to the next page
	function next_step( $next_step, $label, $id = 'ljapi-next-form' ) {
		$str  = '<form action="admin.php?import=livejournal" method="post" id="' . $id . '">';
		$str .= wp_nonce_field( 'lj-api-import', '_wpnonce', true, false );
		$str .= wp_referer_field( false );
		$str .= '<input type="hidden" name="step" id="step" value="' . esc_attr($next_step) . '" />';
		$str .= '<p><input type="submit" class="button-primary" value="' . esc_attr( $label ) . '" /> <span id="auto-message"></span></p>';
		$str .= '</form>';

		return $str;
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
						str = '<?php _e( "Continuing" ) ?> <img src="images/wpspin_light.gif" alt="" id="processing" align="top" />';
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
						str = '<?php _e( "Processing next batch." ) ?> <img src="images/wpspin_light.gif" alt="" id="processing" align="top" />';
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

	// Remove all options used during import process and
	// set wp_comments entries back to "normal" values
	function cleanup() {
		global $wpdb;

		delete_option( 'ljapi_username' );
		delete_option( 'ljapi_password' );
		delete_option( 'ljapi_protected_password' );
		delete_option( 'ljapi_verified' );
		delete_option( 'ljapi_total' );
		delete_option( 'ljapi_count' );
		delete_option( 'ljapi_lastsync' );
		delete_option( 'ljapi_last_sync_count' );
		delete_option( 'ljapi_sync_item_times' );
		delete_option( 'ljapi_lastsync_posts' );
		delete_option( 'ljapi_post_batch' );
		delete_option( 'ljapi_imported_count' );
		delete_option( 'ljapi_maxid' );
		delete_option( 'ljapi_usermap' );
		delete_option( 'ljapi_highest_id' );
		delete_option( 'ljapi_highest_comment_id' );
		delete_option( 'ljapi_comment_batch' );
		delete_option( 'ljapi_step' );

		$wpdb->update( $wpdb->comments,
						array( 'comment_karma' => 0, 'comment_agent' => 'WP LJ Importer', 'comment_type' => '' ),
						array( 'comment_type' => 'livejournal-done' ) );
		$wpdb->update( $wpdb->comments,
						array( 'comment_karma' => 0, 'comment_agent' => 'WP LJ Importer', 'comment_type' => '' ),
						array( 'comment_type' => 'livejournal' ) );
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
