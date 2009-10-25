<?php
/**
 * Blogger Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * How many records per GData query
 *
 * @package WordPress
 * @subpackage Blogger_Import
 * @var int
 * @since unknown
 */
define( 'MAX_RESULTS',        50 );

/**
 * How many seconds to let the script run
 *
 * @package WordPress
 * @subpackage Blogger_Import
 * @var int
 * @since unknown
 */
define( 'MAX_EXECUTION_TIME', 20 );

/**
 * How many seconds between status bar updates
 *
 * @package WordPress
 * @subpackage Blogger_Import
 * @var int
 * @since unknown
 */
define( 'STATUS_INTERVAL',     3 );

/**
 * Blogger Importer class
 *
 * @since unknown
 */
class Blogger_Import {

	// Shows the welcome screen and the magic auth link.
	function greet() {
		$next_url = get_option('siteurl') . '/wp-admin/index.php?import=blogger&amp;noheader=true';
		$auth_url = "https://www.google.com/accounts/AuthSubRequest";
		$title = __('Import Blogger');
		$welcome = __('Howdy! This importer allows you to import posts and comments from your Blogger account into your WordPress blog.');
		$prereqs = __('To use this importer, you must have a Google account and an upgraded (New, was Beta) blog hosted on blogspot.com or a custom domain (not FTP).');
		$stepone = __('The first thing you need to do is tell Blogger to let WordPress access your account. You will be sent back here after providing authorization.');
		$auth = esc_attr__('Authorize');

		echo "
		<div class='wrap'>
		".screen_icon()."
		<h2>$title</h2>
		<p>$welcome</p><p>$prereqs</p><p>$stepone</p>
			<form action='$auth_url' method='get'>
				<p class='submit' style='text-align:left;'>
					<input type='submit' class='button' value='$auth' />
					<input type='hidden' name='scope' value='http://www.blogger.com/feeds/' />
					<input type='hidden' name='session' value='1' />
					<input type='hidden' name='secure' value='0' />
					<input type='hidden' name='next' value='$next_url' />
				</p>
			</form>
		</div>\n";
	}

	function uh_oh($title, $message, $info) {
		echo "<div class='wrap'>";
		screen_icon();
		echo "<h2>$title</h2><p>$message</p><pre>$info</pre></div>";
	}

	function auth() {
		// We have a single-use token that must be upgraded to a session token.
		$token = preg_replace( '/[^-_0-9a-zA-Z]/', '', $_GET['token'] );
		$headers = array(
			"GET /accounts/AuthSubSessionToken HTTP/1.0",
			"Authorization: AuthSub token=\"$token\""
		);
		$request = join( "\r\n", $headers ) . "\r\n\r\n";
		$sock = $this->_get_auth_sock( );
		if ( ! $sock ) return false;
		$response = $this->_txrx( $sock, $request );
		preg_match( '/token=([-_0-9a-z]+)/i', $response, $matches );
		if ( empty( $matches[1] ) ) {
			$this->uh_oh(
				__( 'Authorization failed' ),
				__( 'Something went wrong. If the problem persists, send this info to support:' ),
				htmlspecialchars($response)
			);
			return false;
		}
		$this->token = $matches[1];

		wp_redirect( remove_query_arg( array( 'token', 'noheader' ) ) );
	}

	function get_token_info() {
		$headers = array(
			"GET /accounts/AuthSubTokenInfo  HTTP/1.0",
			"Authorization: AuthSub token=\"$this->token\""
		);
		$request = join( "\r\n", $headers ) . "\r\n\r\n";
		$sock = $this->_get_auth_sock( );
		if ( ! $sock ) return;
		$response = $this->_txrx( $sock, $request );
		return $this->parse_response($response);
	}

	function token_is_valid() {
		$info = $this->get_token_info();

		if ( $info['code'] == 200 )
			return true;

		return false;
	}

	function show_blogs($iter = 0) {
		if ( empty($this->blogs) ) {
			$headers = array(
				"GET /feeds/default/blogs HTTP/1.0",
				"Host: www.blogger.com",
				"Authorization: AuthSub token=\"$this->token\""
			);
			$request = join( "\r\n", $headers ) . "\r\n\r\n";
			$sock = $this->_get_blogger_sock( );
			if ( ! $sock ) return;
			$response = $this->_txrx( $sock, $request );

			// Quick and dirty XML mining.
			list( $headers, $xml ) = explode( "\r\n\r\n", $response );
			$p = xml_parser_create();
			xml_parse_into_struct($p, $xml, $vals, $index);
			xml_parser_free($p);

			$this->title = $vals[$index['TITLE'][0]]['value'];

			// Give it a few retries... this step often flakes out the first time.
			if ( empty( $index['ENTRY'] ) ) {
				if ( $iter < 3 ) {
					return $this->show_blogs($iter + 1);
				} else {
					$this->uh_oh(
						__('Trouble signing in'),
						__('We were not able to gain access to your account. Try starting over.'),
						''
					);
					return false;
				}
			}

			foreach ( $index['ENTRY'] as $i ) {
				$blog = array();
				while ( ( $tag = $vals[$i] ) && ! ( $tag['tag'] == 'ENTRY' && $tag['type'] == 'close' ) ) {
					if ( $tag['tag'] == 'TITLE' ) {
						$blog['title'] = $tag['value'];
					} elseif ( $tag['tag'] == 'SUMMARY' ) {
						$blog['summary'] == $tag['value'];
					} elseif ( $tag['tag'] == 'LINK' ) {
						if ( $tag['attributes']['REL'] == 'alternate' && $tag['attributes']['TYPE'] == 'text/html' ) {
							$parts = parse_url( $tag['attributes']['HREF'] );
							$blog['host'] = $parts['host'];
						} elseif ( $tag['attributes']['REL'] == 'edit' )
							$blog['gateway'] = $tag['attributes']['HREF'];
					}
					++$i;
				}
				if ( ! empty ( $blog ) ) {
					$blog['total_posts'] = $this->get_total_results('posts', $blog['host']);
					$blog['total_comments'] = $this->get_total_results('comments', $blog['host']);
					$blog['mode'] = 'init';
					$this->blogs[] = $blog;
				}
			}

			if ( empty( $this->blogs ) ) {
				$this->uh_oh(
					__('No blogs found'),
					__('We were able to log in but there were no blogs. Try a different account next time.'),
					''
				);
				return false;
			}
		}
//echo '<pre>'.print_r($this,1).'</pre>';
		$start    = esc_js( __('Import') );
		$continue = esc_js( __('Continue') );
		$stop     = esc_js( __('Importing...') );
		$authors  = esc_js( __('Set Authors') );
		$loadauth = esc_js( __('Preparing author mapping form...') );
		$authhead = esc_js( __('Final Step: Author Mapping') );
		$nothing  = esc_js( __('Nothing was imported. Had you already imported this blog?') );
		$stopping = ''; //Missing String used below.
		$title    = __('Blogger Blogs');
		$name     = __('Blog Name');
		$url      = __('Blog URL');
		$action   = __('The Magic Button');
		$posts    = __('Posts');
		$comments = __('Comments');
		$noscript = __('This feature requires Javascript but it seems to be disabled. Please enable Javascript and then reload this page. Don&#8217;t worry, you can turn it back off when you&#8217;re done.');

		$interval = STATUS_INTERVAL * 1000;

		foreach ( $this->blogs as $i => $blog ) {
			if ( $blog['mode'] == 'init' )
				$value = $start;
			elseif ( $blog['mode'] == 'posts' || $blog['mode'] == 'comments' )
				$value = $continue;
			else
				$value = $authors;
			$value = esc_attr($value);
			$blogtitle = esc_js( $blog['title'] );
			$pdone = isset($blog['posts_done']) ? (int) $blog['posts_done'] : 0;
			$cdone = isset($blog['comments_done']) ? (int) $blog['comments_done'] : 0;
			$init .= "blogs[$i]=new blog($i,'$blogtitle','{$blog['mode']}'," . $this->get_js_status($i) . ');';
			$pstat = "<div class='ind' id='pind$i'>&nbsp;</div><div id='pstat$i' class='stat'>$pdone/{$blog['total_posts']}</div>";
			$cstat = "<div class='ind' id='cind$i'>&nbsp;</div><div id='cstat$i' class='stat'>$cdone/{$blog['total_comments']}</div>";
			$rows .= "<tr id='blog$i'><td class='blogtitle'>$blogtitle</td><td class='bloghost'>{$blog['host']}</td><td class='bar'>$pstat</td><td class='bar'>$cstat</td><td class='submit'><input type='submit' class='button' id='submit$i' value='$value' /><input type='hidden' name='blog' value='$i' /></td></tr>\n";
		}

		echo "<div class='wrap'><h2>$title</h2><noscript>$noscript</noscript><table cellpadding='5px'><thead><tr><td>$name</td><td>$url</td><td>$posts</td><td>$comments</td><td>$action</td></tr></thead>\n$rows</table></div>";
		echo "
		<script type='text/javascript'>
		/* <![CDATA[ */
			var strings = {cont:'$continue',stop:'$stop',stopping:'$stopping',authors:'$authors',nothing:'$nothing'};
			var blogs = {};
			function blog(i, title, mode, status){
				this.blog   = i;
				this.mode   = mode;
				this.title  = title;
				this.status = status;
				this.button = document.getElementById('submit'+this.blog);
			};
			blog.prototype = {
				start: function() {
					this.cont = true;
					this.kick();
					this.check();
				},
				kick: function() {
					++this.kicks;
					var i = this.blog;
					jQuery.post('admin.php?import=blogger&noheader=true',{blog:this.blog},function(text,result){blogs[i].kickd(text,result)});
				},
				check: function() {
					++this.checks;
					var i = this.blog;
					jQuery.post('admin.php?import=blogger&noheader=true&status=true',{blog:this.blog},function(text,result){blogs[i].checkd(text,result)});
				},
				kickd: function(text, result) {
					if ( result == 'error' ) {
						// TODO: exception handling
						if ( this.cont )
							setTimeout('blogs['+this.blog+'].kick()', 1000);
					} else {
						if ( text == 'done' ) {
							this.stop();
							this.done();
						} else if ( text == 'nothing' ) {
							this.stop();
							this.nothing();
						} else if ( text == 'continue' ) {
							this.kick();
						} else if ( this.mode = 'stopped' )
							jQuery(this.button).attr('value', strings.cont);
					}
					--this.kicks;
				},
				checkd: function(text, result) {
					if ( result == 'error' ) {
						// TODO: exception handling
					} else {
						eval('this.status='+text);
						jQuery('#pstat'+this.blog).empty().append(this.status.p1+'/'+this.status.p2);
						jQuery('#cstat'+this.blog).empty().append(this.status.c1+'/'+this.status.c2);
						this.update();
						if ( this.cont || this.kicks > 0 )
							setTimeout('blogs['+this.blog+'].check()', $interval);
					}
					--this.checks;
				},
				update: function() {
					jQuery('#pind'+this.blog).width(((this.status.p1>0&&this.status.p2>0)?(this.status.p1/this.status.p2*jQuery('#pind'+this.blog).parent().width()):1)+'px');
					jQuery('#cind'+this.blog).width(((this.status.c1>0&&this.status.c2>0)?(this.status.c1/this.status.c2*jQuery('#cind'+this.blog).parent().width()):1)+'px');
				},
				stop: function() {
					this.cont = false;
				},
				done: function() {
					this.mode = 'authors';
					jQuery(this.button).attr('value', strings.authors);
				},
				nothing: function() {
					this.mode = 'nothing';
					jQuery(this.button).remove();
					alert(strings.nothing);
				},
				getauthors: function() {
					if ( jQuery('div.wrap').length > 1 )
						jQuery('div.wrap').gt(0).remove();
					jQuery('div.wrap').empty().append('<h2>$authhead</h2><h3>' + this.title + '</h3>');
					jQuery('div.wrap').append('<p id=\"auth\">$loadauth</p>');
					jQuery('p#auth').load('index.php?import=blogger&noheader=true&authors=1',{blog:this.blog});
				},
				init: function() {
					this.update();
					var i = this.blog;
					jQuery(this.button).bind('click', function(){return blogs[i].click();});
					this.kicks = 0;
					this.checks = 0;
				},
				click: function() {
					if ( this.mode == 'init' || this.mode == 'stopped' || this.mode == 'posts' || this.mode == 'comments' ) {
						this.mode = 'started';
						this.start();
						jQuery(this.button).attr('value', strings.stop);
					} else if ( this.mode == 'started' ) {
						return false; // let it run...
						this.mode = 'stopped';
						this.stop();
						if ( this.checks > 0 || this.kicks > 0 ) {
							this.mode = 'stopping';
							jQuery(this.button).attr('value', strings.stopping);
						} else {
							jQuery(this.button).attr('value', strings.cont);
						}
					} else if ( this.mode == 'authors' ) {
						document.location = 'index.php?import=blogger&authors=1&blog='+this.blog;
						//this.mode = 'authors2';
						//this.getauthors();
					}
					return false;
				}
			};
			$init
			jQuery.each(blogs, function(i, me){me.init();});
		/* ]]> */
		</script>\n";
	}

	// Handy function for stopping the script after a number of seconds.
	function have_time() {
		global $importer_started;
		if ( time() - $importer_started > MAX_EXECUTION_TIME )
			die('continue');
		return true;
	}

	function get_total_results($type, $host) {
		$headers = array(
			"GET /feeds/$type/default?max-results=1&start-index=2 HTTP/1.0",
			"Host: $host",
			"Authorization: AuthSub token=\"$this->token\""
		);
		$request = join( "\r\n", $headers ) . "\r\n\r\n";
		$sock = $this->_get_blogger_sock( $host );
		if ( ! $sock ) return;
		$response = $this->_txrx( $sock, $request );
		$response = $this->parse_response( $response );
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $response['body'], $struct, $index);
		xml_parser_free($parser);
		$total_results = $struct[$index['OPENSEARCH:TOTALRESULTS'][0]]['value'];
		return (int) $total_results;
	}

	function import_blog($blogID) {
		global $importing_blog;
		$importing_blog = $blogID;

		if ( isset($_GET['authors']) )
			return print($this->get_author_form());

		header('Content-Type: text/plain');

		if ( isset($_GET['status']) )
			die($this->get_js_status());

		if ( isset($_GET['saveauthors']) )
			die($this->save_authors());

		$blog = $this->blogs[$blogID];
		$total_results = $this->get_total_results('posts', $blog['host']);
		$this->blogs[$importing_blog]['total_posts'] = $total_results;

		$start_index = $total_results - MAX_RESULTS + 1;

		if ( isset( $this->blogs[$importing_blog]['posts_start_index'] ) )
			$start_index = (int) $this->blogs[$importing_blog]['posts_start_index'];
		elseif ( $total_results > MAX_RESULTS )
			$start_index = $total_results - MAX_RESULTS + 1;
		else
			$start_index = 1;

		// This will be positive until we have finished importing posts
		if ( $start_index > 0 ) {
			// Grab all the posts
			$this->blogs[$importing_blog]['mode'] = 'posts';
			$query = "start-index=$start_index&max-results=" . MAX_RESULTS;
			do {
				$index = $struct = $entries = array();
				$headers = array(
					"GET /feeds/posts/default?$query HTTP/1.0",
					"Host: {$blog['host']}",
					"Authorization: AuthSub token=\"$this->token\""
				);
				$request = join( "\r\n", $headers ) . "\r\n\r\n";
				$sock = $this->_get_blogger_sock( $blog['host'] );
				if ( ! $sock ) return; // TODO: Error handling
				$response = $this->_txrx( $sock, $request );

				$response = $this->parse_response( $response );

				// Extract the entries and send for insertion
				preg_match_all( '/<entry[^>]*>.*?<\/entry>/s', $response['body'], $matches );
				if ( count( $matches[0] ) ) {
					$entries = array_reverse($matches[0]);
					foreach ( $entries as $entry ) {
						$entry = "<feed>$entry</feed>";
						$AtomParser = new AtomParser();
						$AtomParser->parse( $entry );
						$result = $this->import_post($AtomParser->entry);
						if ( is_wp_error( $result ) )
							return $result;
						unset($AtomParser);
					}
				} else break;

				// Get the 'previous' query string which we'll use on the next iteration
				$query = '';
				$links = preg_match_all('/<link([^>]*)>/', $response['body'], $matches);
				if ( count( $matches[1] ) )
					foreach ( $matches[1] as $match )
						if ( preg_match('/rel=.previous./', $match) )
							$query = @html_entity_decode( preg_replace('/^.*href=[\'"].*\?(.+)[\'"].*$/', '$1', $match), ENT_COMPAT, get_option('blog_charset') );

				if ( $query ) {
					parse_str($query, $q);
					$this->blogs[$importing_blog]['posts_start_index'] = (int) $q['start-index'];
				} else
					$this->blogs[$importing_blog]['posts_start_index'] = 0;
				$this->save_vars();
			} while ( !empty( $query ) && $this->have_time() );
		}

		$total_results = $this->get_total_results( 'comments', $blog['host'] );
		$this->blogs[$importing_blog]['total_comments'] = $total_results;

		if ( isset( $this->blogs[$importing_blog]['comments_start_index'] ) )
			$start_index = (int) $this->blogs[$importing_blog]['comments_start_index'];
		elseif ( $total_results > MAX_RESULTS )
			$start_index = $total_results - MAX_RESULTS + 1;
		else
			$start_index = 1;

		if ( $start_index > 0 ) {
			// Grab all the comments
			$this->blogs[$importing_blog]['mode'] = 'comments';
			$query = "start-index=$start_index&max-results=" . MAX_RESULTS;
			do {
				$index = $struct = $entries = array();
				$headers = array(
					"GET /feeds/comments/default?$query HTTP/1.0",
					"Host: {$blog['host']}",
					"Authorization: AuthSub token=\"$this->token\""
				);
				$request = join( "\r\n", $headers ) . "\r\n\r\n";
				$sock = $this->_get_blogger_sock( $blog['host'] );
				if ( ! $sock ) return; // TODO: Error handling
				$response = $this->_txrx( $sock, $request );

				$response = $this->parse_response( $response );

				// Extract the comments and send for insertion
				preg_match_all( '/<entry[^>]*>.*?<\/entry>/s', $response['body'], $matches );
				if ( count( $matches[0] ) ) {
					$entries = array_reverse( $matches[0] );
					foreach ( $entries as $entry ) {
						$entry = "<feed>$entry</feed>";
						$AtomParser = new AtomParser();
						$AtomParser->parse( $entry );
						$this->import_comment($AtomParser->entry);
						unset($AtomParser);
					}
				}

				// Get the 'previous' query string which we'll use on the next iteration
				$query = '';
				$links = preg_match_all('/<link([^>]*)>/', $response['body'], $matches);
				if ( count( $matches[1] ) )
					foreach ( $matches[1] as $match )
						if ( preg_match('/rel=.previous./', $match) )
							$query = @html_entity_decode( preg_replace('/^.*href=[\'"].*\?(.+)[\'"].*$/', '$1', $match), ENT_COMPAT, get_option('blog_charset') );

				parse_str($query, $q);

				$this->blogs[$importing_blog]['comments_start_index'] = (int) $q['start-index'];
				$this->save_vars();
			} while ( !empty( $query ) && $this->have_time() );
		}
		$this->blogs[$importing_blog]['mode'] = 'authors';
		$this->save_vars();
		if ( !$this->blogs[$importing_blog]['posts_done'] && !$this->blogs[$importing_blog]['comments_done'] )
			die('nothing');
		do_action('import_done', 'blogger');
		die('done');
	}

	function convert_date( $date ) {
	    preg_match('#([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(?:\.[0-9]+)?(Z|[\+|\-][0-9]{2,4}){0,1}#', $date, $date_bits);
	    $offset = iso8601_timezone_to_offset( $date_bits[7] );
		$timestamp = gmmktime($date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1]);
		$timestamp -= $offset; // Convert from Blogger local time to GMT
		$timestamp += get_option('gmt_offset') * 3600; // Convert from GMT to WP local time
		return gmdate('Y-m-d H:i:s', $timestamp);
	}

	function no_apos( $string ) {
		return str_replace( '&apos;', "'", $string);
	}

	function min_whitespace( $string ) {
		return preg_replace( '|\s+|', ' ', $string );
	}

	function _normalize_tag( $matches ) {
		return '<' . strtolower( $matches[1] );
	}

	function import_post( $entry ) {
		global $importing_blog;

		// The old permalink is all Blogger gives us to link comments to their posts.
		if ( isset( $entry->draft ) )
			$rel = 'self';
		else
			$rel = 'alternate';
		foreach ( $entry->links as $link ) {
			if ( $link['rel'] == $rel ) {
				$parts = parse_url( $link['href'] );
				$entry->old_permalink = $parts['path'];
				break;
			}
		}

		$post_date    = $this->convert_date( $entry->published );
		$post_content = trim( addslashes( $this->no_apos( @html_entity_decode( $entry->content, ENT_COMPAT, get_option('blog_charset') ) ) ) );
		$post_title   = trim( addslashes( $this->no_apos( $this->min_whitespace( $entry->title ) ) ) );
		$post_status  = isset( $entry->draft ) ? 'draft' : 'publish';

		// Clean up content
		$post_content = preg_replace_callback('|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_content);
		$post_content = str_replace('<br>', '<br />', $post_content);
		$post_content = str_replace('<hr>', '<hr />', $post_content);

		// Checks for duplicates
		if ( isset( $this->blogs[$importing_blog]['posts'][$entry->old_permalink] ) ) {
			++$this->blogs[$importing_blog]['posts_skipped'];
		} elseif ( $post_id = post_exists( $post_title, $post_content, $post_date ) ) {
			$this->blogs[$importing_blog]['posts'][$entry->old_permalink] = $post_id;
			++$this->blogs[$importing_blog]['posts_skipped'];
		} else {
			$post = compact('post_date', 'post_content', 'post_title', 'post_status');

			$post_id = wp_insert_post($post);
			if ( is_wp_error( $post_id ) )
				return $post_id;

			wp_create_categories( array_map( 'addslashes', $entry->categories ), $post_id );

			$author = $this->no_apos( strip_tags( $entry->author ) );

			add_post_meta( $post_id, 'blogger_blog', $this->blogs[$importing_blog]['host'], true );
			add_post_meta( $post_id, 'blogger_author', $author, true );
			add_post_meta( $post_id, 'blogger_permalink', $entry->old_permalink, true );

			$this->blogs[$importing_blog]['posts'][$entry->old_permalink] = $post_id;
			++$this->blogs[$importing_blog]['posts_done'];
		}
		$this->save_vars();
		return;
	}

	function import_comment( $entry ) {
		global $importing_blog;

		// Drop the #fragment and we have the comment's old post permalink.
		foreach ( $entry->links as $link ) {
			if ( $link['rel'] == 'alternate' ) {
				$parts = parse_url( $link['href'] );
				$entry->old_permalink = $parts['fragment'];
				$entry->old_post_permalink = $parts['path'];
				break;
			}
		}

		$comment_post_ID = (int) $this->blogs[$importing_blog]['posts'][$entry->old_post_permalink];
		preg_match('#<name>(.+?)</name>.*(?:\<uri>(.+?)</uri>)?#', $entry->author, $matches);
		$comment_author  = addslashes( $this->no_apos( strip_tags( (string) $matches[1] ) ) );
		$comment_author_url = addslashes( $this->no_apos( strip_tags( (string) $matches[2] ) ) );
		$comment_date    = $this->convert_date( $entry->updated );
		$comment_content = addslashes( $this->no_apos( @html_entity_decode( $entry->content, ENT_COMPAT, get_option('blog_charset') ) ) );

		// Clean up content
		$comment_content = preg_replace_callback('|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $comment_content);
		$comment_content = str_replace('<br>', '<br />', $comment_content);
		$comment_content = str_replace('<hr>', '<hr />', $comment_content);

		// Checks for duplicates
		if (
			isset( $this->blogs[$importing_blog]['comments'][$entry->old_permalink] ) ||
			comment_exists( $comment_author, $comment_date )
		) {
			++$this->blogs[$importing_blog]['comments_skipped'];
		} else {
			$comment = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_date', 'comment_content');

			$comment_id = wp_insert_comment($comment);

			$this->blogs[$importing_blog]['comments'][$entry->old_permalink] = $comment_id;

			++$this->blogs[$importing_blog]['comments_done'];
		}
		$this->save_vars();
	}

	function get_js_status($blog = false) {
		global $importing_blog;
		if ( $blog === false )
			$blog = $this->blogs[$importing_blog];
		else
			$blog = $this->blogs[$blog];
		$p1 = isset( $blog['posts_done'] ) ? (int) $blog['posts_done'] : 0;
		$p2 = isset( $blog['total_posts'] ) ? (int) $blog['total_posts'] : 0;
		$c1 = isset( $blog['comments_done'] ) ? (int) $blog['comments_done'] : 0;
		$c2 = isset( $blog['total_comments'] ) ? (int) $blog['total_comments'] : 0;
		return "{p1:$p1,p2:$p2,c1:$c1,c2:$c2}";
	}

	function get_author_form($blog = false) {
		global $importing_blog, $wpdb, $current_user;
		if ( $blog === false )
			$blog = & $this->blogs[$importing_blog];
		else
			$blog = & $this->blogs[$blog];

		if ( !isset( $blog['authors'] ) ) {
			$post_ids = array_values($blog['posts']);
			$authors = (array) $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = 'blogger_author' AND post_id IN (" . join( ',', $post_ids ) . ")");
			$blog['authors'] = array_map(null, $authors, array_fill(0, count($authors), $current_user->ID));
			$this->save_vars();
		}

		$directions = __('All posts were imported with the current user as author. Use this form to move each Blogger user&#8217;s posts to a different WordPress user. You may <a href="users.php">add users</a> and then return to this page and complete the user mapping. This form may be used as many times as you like until you activate the &#8220;Restart&#8221; function below.');
		$heading = __('Author mapping');
		$blogtitle = "{$blog['title']} ({$blog['host']})";
		$mapthis = __('Blogger username');
		$tothis = __('WordPress login');
		$submit = esc_js( __('Save Changes') );

		foreach ( $blog['authors'] as $i => $author )
			$rows .= "<tr><td><label for='authors[$i]'>{$author[0]}</label></td><td><select name='authors[$i]' id='authors[$i]'>" . $this->get_user_options($author[1]) . "</select></td></tr>";

		return "<div class='wrap'><h2>$heading</h2><h3>$blogtitle</h3><p>$directions</p><form action='index.php?import=blogger&amp;noheader=true&saveauthors=1' method='post'><input type='hidden' name='blog' value='" . esc_attr($importing_blog) . "' /><table cellpadding='5'><thead><td>$mapthis</td><td>$tothis</td></thead>$rows<tr><td></td><td class='submit'><input type='submit' class='button authorsubmit' value='$submit' /></td></tr></table></form></div>";
	}

	function get_user_options($current) {
		global $importer_users;
		if ( ! isset( $importer_users ) )
			$importer_users = (array) get_users_of_blog();

		foreach ( $importer_users as $user ) {
			$sel = ( $user->user_id == $current ) ? " selected='selected'" : '';
			$options .= "<option value='$user->user_id'$sel>$user->display_name</option>";
		}

		return $options;
	}

	function save_authors() {
		global $importing_blog, $wpdb;
		$authors = (array) $_POST['authors'];

		$host = $this->blogs[$importing_blog]['host'];

		// Get an array of posts => authors
		$post_ids = (array) $wpdb->get_col( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'blogger_blog' AND meta_value = %s", $host) );
		$post_ids = join( ',', $post_ids );
		$results = (array) $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'blogger_author' AND post_id IN ($post_ids)");
		foreach ( $results as $row )
			$authors_posts[$row->post_id] = $row->meta_value;

		foreach ( $authors as $author => $user_id ) {
			$user_id = (int) $user_id;

			// Skip authors that haven't been changed
			if ( $user_id == $this->blogs[$importing_blog]['authors'][$author][1] )
				continue;

			// Get a list of the selected author's posts
			$post_ids = (array) array_keys( $authors_posts, $this->blogs[$importing_blog]['authors'][$author][0] );
			$post_ids = join( ',', $post_ids);

			$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_author = %d WHERE id IN ($post_ids)", $user_id) );
			$this->blogs[$importing_blog]['authors'][$author][1] = $user_id;
		}
		$this->save_vars();

		wp_redirect('edit.php');
	}

	function _get_auth_sock() {
		// Connect to https://www.google.com
		if ( !$sock = @ fsockopen('ssl://www.google.com', 443, $errno, $errstr) ) {
			$this->uh_oh(
				__('Could not connect to https://www.google.com'),
				__('There was a problem opening a secure connection to Google. This is what went wrong:'),
				"$errstr ($errno)"
			);
			return false;
		}
		return $sock;
	}

	function _get_blogger_sock($host = 'www2.blogger.com') {
		if ( !$sock = @ fsockopen($host, 80, $errno, $errstr) ) {
			$this->uh_oh(
				sprintf( __('Could not connect to %s'), $host ),
				__('There was a problem opening a connection to Blogger. This is what went wrong:'),
				"$errstr ($errno)"
			);
			return false;
		}
		return $sock;
	}

	function _txrx( $sock, $request ) {
		fwrite( $sock, $request );
		while ( ! feof( $sock ) )
			$response .= @ fread ( $sock, 8192 );
		fclose( $sock );
		return $response;
	}

	function revoke($token) {
		$headers = array(
			"GET /accounts/AuthSubRevokeToken HTTP/1.0",
			"Authorization: AuthSub token=\"$token\""
		);
		$request = join( "\r\n", $headers ) . "\r\n\r\n";
		$sock = $this->_get_auth_sock( );
		if ( ! $sock ) return false;
		$this->_txrx( $sock, $request );
	}

	function restart() {
		global $wpdb;
		$options = get_option( 'blogger_importer' );

		if ( isset( $options['token'] ) )
			$this->revoke( $options['token'] );

		delete_option('blogger_importer');
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'blogger_author'");
		wp_redirect('?import=blogger');
	}

	// Returns associative array of code, header, cookies, body. Based on code from php.net.
	function parse_response($this_response) {
		// Split response into header and body sections
		list($response_headers, $response_body) = explode("\r\n\r\n", $this_response, 2);
		$response_header_lines = explode("\r\n", $response_headers);

		// First line of headers is the HTTP response code
		$http_response_line = array_shift($response_header_lines);
		if(preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line, $matches)) { $response_code = $matches[1]; }

		// put the rest of the headers in an array
		$response_header_array = array();
		foreach($response_header_lines as $header_line) {
			list($header,$value) = explode(': ', $header_line, 2);
			$response_header_array[$header] .= $value."\n";
		}

		$cookie_array = array();
		$cookies = explode("\n", $response_header_array["Set-Cookie"]);
		foreach($cookies as $this_cookie) { array_push($cookie_array, "Cookie: ".$this_cookie); }

		return array("code" => $response_code, "header" => $response_header_array, "cookies" => $cookie_array, "body" => $response_body);
	}

	// Step 9: Congratulate the user
	function congrats() {
		$blog = (int) $_GET['blog'];
		echo '<h1>'.__('Congratulations!').'</h1><p>'.__('Now that you have imported your Blogger blog into WordPress, what are you going to do? Here are some suggestions:').'</p><ul><li>'.__('That was hard work! Take a break.').'</li>';
		if ( count($this->import['blogs']) > 1 )
			echo '<li>'.__('In case you haven&#8217;t done it already, you can import the posts from your other blogs:'). $this->show_blogs() . '</li>';
		if ( $n = count($this->import['blogs'][$blog]['newusers']) )
			echo '<li>'.sprintf(__('Go to <a href="%s" target="%s">Authors &amp; Users</a>, where you can modify the new user(s) or delete them. If you want to make all of the imported posts yours, you will be given that option when you delete the new authors.'), 'users.php', '_parent').'</li>';
		echo '<li>'.__('For security, click the link below to reset this importer.').'</li>';
		echo '</ul>';
	}

	// Figures out what to do, then does it.
	function start() {
		if ( isset($_POST['restart']) )
			$this->restart();

		$options = get_option('blogger_importer');

		if ( is_array($options) )
			foreach ( $options as $key => $value )
				$this->$key = $value;

		if ( isset( $_REQUEST['blog'] ) ) {
			$blog = is_array($_REQUEST['blog']) ? array_shift( $keys = array_keys( $_REQUEST['blog'] ) ) : $_REQUEST['blog'];
			$blog = (int) $blog;
			$result = $this->import_blog( $blog );
			if ( is_wp_error( $result ) )
				echo $result->get_error_message();
		} elseif ( isset($_GET['token']) )
			$this->auth();
		elseif ( isset($this->token) && $this->token_is_valid() )
			$this->show_blogs();
		else
			$this->greet();

		$saved = $this->save_vars();

		if ( $saved && !isset($_GET['noheader']) ) {
			$restart = __('Restart');
			$message = __('We have saved some information about your Blogger account in your WordPress database. Clearing this information will allow you to start over. Restarting will not affect any posts you have already imported. If you attempt to re-import a blog, duplicate posts and comments will be skipped.');
			$submit = esc_attr__('Clear account information');
			echo "<div class='wrap'><h2>$restart</h2><p>$message</p><form method='post' action='?import=blogger&amp;noheader=true'><p class='submit' style='text-align:left;'><input type='submit' class='button' value='$submit' name='restart' /></p></form></div>";
		}
	}

	function save_vars() {
		$vars = get_object_vars($this);
		update_option( 'blogger_importer', $vars );

		return !empty($vars);
	}

	function admin_head() {
?>
<style type="text/css">
td { text-align: center; line-height: 2em;}
thead td { font-weight: bold; }
.bar {
	width: 200px;
	text-align: left;
	line-height: 2em;
	padding: 0px;
}
.ind {
	position: absolute;
	background-color: #83B4D8;
	width: 1px;
	z-index: 9;
}
.stat {
	z-index: 10;
	position: relative;
	text-align: center;
}
</style>
<?php
	}

	function Blogger_Import() {
		global $importer_started;
		$importer_started = time();
		if ( isset( $_GET['import'] ) && $_GET['import'] == 'blogger' ) {
			wp_enqueue_script('jquery');
			add_action('admin_head', array(&$this, 'admin_head'));
		}
	}
}

$blogger_import = new Blogger_Import();

register_importer('blogger', __('Blogger'), __('Import posts, comments, and users from a Blogger blog.'), array ($blogger_import, 'start'));

class AtomEntry {
	var $links = array();
	var $categories = array();
}

class AtomParser {

	var $ATOM_CONTENT_ELEMENTS = array('content','summary','title','subtitle','rights');
	var $ATOM_SIMPLE_ELEMENTS = array('id','updated','published','draft','author');

	var $depth = 0;
	var $indent = 2;
	var $in_content;
	var $ns_contexts = array();
	var $ns_decls = array();
	var $is_xhtml = false;
	var $skipped_div = false;

	var $entry;

	function AtomParser() {
		$this->entry = new AtomEntry();
	}

	function _map_attrs_func( $k, $v ) {
		return "$k=\"$v\"";
	}

	function _map_xmlns_func( $p, $n ) {
		$xd = "xmlns";
		if ( strlen( $n[0] ) > 0 )
			$xd .= ":{$n[0]}";

		return "{$xd}=\"{$n[1]}\"";
	}

	function parse($xml) {

		global $app_logging;
		array_unshift($this->ns_contexts, array());

		$parser = xml_parser_create_ns();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, "start_element", "end_element");
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_set_character_data_handler($parser, "cdata");
		xml_set_default_handler($parser, "_default");
		xml_set_start_namespace_decl_handler($parser, "start_ns");
		xml_set_end_namespace_decl_handler($parser, "end_ns");

		$contents = "";

		xml_parse($parser, $xml);

		xml_parser_free($parser);

		return true;
	}

	function start_element($parser, $name, $attrs) {

		$tag = array_pop(split(":", $name));

		array_unshift($this->ns_contexts, $this->ns_decls);

		$this->depth++;

		if(!empty($this->in_content)) {
			$attrs_prefix = array();

			// resolve prefixes for attributes
			foreach($attrs as $key => $value) {
				$attrs_prefix[$this->ns_to_prefix($key)] = $this->xml_escape($value);
			}
			$attrs_str = join(' ', array_map( array( &$this, '_map_attrs_func' ), array_keys($attrs_prefix), array_values($attrs_prefix)));
			if(strlen($attrs_str) > 0) {
				$attrs_str = " " . $attrs_str;
			}

			$xmlns_str = join(' ', array_map( array( &$this, '_map_xmlns_func' ), array_keys($this->ns_contexts[0]), array_values($this->ns_contexts[0])));
			if(strlen($xmlns_str) > 0) {
				$xmlns_str = " " . $xmlns_str;
			}

			// handle self-closing tags (case: a new child found right-away, no text node)
			if(count($this->in_content) == 2) {
				array_push($this->in_content, ">");
			}

			array_push($this->in_content, "<". $this->ns_to_prefix($name) ."{$xmlns_str}{$attrs_str}");
		} else if(in_array($tag, $this->ATOM_CONTENT_ELEMENTS) || in_array($tag, $this->ATOM_SIMPLE_ELEMENTS)) {
			$this->in_content = array();
			$this->is_xhtml = $attrs['type'] == 'xhtml';
			array_push($this->in_content, array($tag,$this->depth));
		} else if($tag == 'link') {
			array_push($this->entry->links, $attrs);
		} else if($tag == 'category') {
			array_push($this->entry->categories, $attrs['term']);
		}

		$this->ns_decls = array();
	}

	function end_element($parser, $name) {

		$tag = array_pop(split(":", $name));

		if(!empty($this->in_content)) {
			if($this->in_content[0][0] == $tag &&
			$this->in_content[0][1] == $this->depth) {
				array_shift($this->in_content);
				if($this->is_xhtml) {
					$this->in_content = array_slice($this->in_content, 2, count($this->in_content)-3);
				}
				$this->entry->$tag = join('',$this->in_content);
				$this->in_content = array();
			} else {
				$endtag = $this->ns_to_prefix($name);
				if (strpos($this->in_content[count($this->in_content)-1], '<' . $endtag) !== false) {
					array_push($this->in_content, "/>");
				} else {
					array_push($this->in_content, "</$endtag>");
				}
			}
		}

		array_shift($this->ns_contexts);

		#print str_repeat(" ", $this->depth * $this->indent) . "end_element('$name')" ."\n";

		$this->depth--;
	}

	function start_ns($parser, $prefix, $uri) {
		#print str_repeat(" ", $this->depth * $this->indent) . "starting: " . $prefix . ":" . $uri . "\n";
		array_push($this->ns_decls, array($prefix,$uri));
	}

	function end_ns($parser, $prefix) {
		#print str_repeat(" ", $this->depth * $this->indent) . "ending: #" . $prefix . "#\n";
	}

	function cdata($parser, $data) {
		#print str_repeat(" ", $this->depth * $this->indent) . "data: #" . $data . "#\n";
		if(!empty($this->in_content)) {
			// handle self-closing tags (case: text node found, need to close element started)
			if (strpos($this->in_content[count($this->in_content)-1], '<') !== false) {
				array_push($this->in_content, ">");
			}
			array_push($this->in_content, $this->xml_escape($data));
		}
	}

	function _default($parser, $data) {
		# when does this gets called?
	}


	function ns_to_prefix($qname) {
		$components = split(":", $qname);
		$name = array_pop($components);

		if(!empty($components)) {
			$ns = join(":",$components);
			foreach($this->ns_contexts as $context) {
				foreach($context as $mapping) {
					if($mapping[1] == $ns && strlen($mapping[0]) > 0) {
						return "$mapping[0]:$name";
					}
				}
			}
		}
		return $name;
	}

	function xml_escape($string)
	{
			 return str_replace(array('&','"',"'",'<','>'),
				array('&amp;','&quot;','&apos;','&lt;','&gt;'),
				$string );
	}
}

?>
