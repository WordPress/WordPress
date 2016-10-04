<?php
/**
 * Tools Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __('Tools');

get_current_screen()->add_help_tab( array(
	'id'      => 'press-this',
	'title'   => __('Press This'),
	'content' => '<p>' . __('Press This is a bookmarklet that makes it easy to blog about something you come across on the web. You can use it to just grab a link, or to post an excerpt. Press This will even allow you to choose from images included on the page and use them in your post. Just drag the Press This link on this screen to your bookmarks bar in your browser, and you&#8217;ll be on your way to easier content creation. Clicking on it while on another website opens a popup window with all these options.') . '</p>',
) );
get_current_screen()->add_help_tab( array(
	'id'      => 'converter',
	'title'   => __('Categories and Tags Converter'),
	'content' => '<p>' . __('Categories have hierarchy, meaning that you can nest sub-categories. Tags do not have hierarchy and cannot be nested. Sometimes people start out using one on their posts, then later realize that the other would work better for their content.' ) . '</p>' .
	'<p>' . __( 'The Categories and Tags Converter link on this screen will take you to the Import screen, where that Converter is one of the plugins you can install. Once that plugin is installed, the Activate Plugin &amp; Run Importer link will take you to a screen where you can choose to convert tags into categories or vice versa.' ) . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Tools_Screen">Documentation on Tools</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/">Support Forums</a>') . '</p>'
);

require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<?php if ( current_user_can('edit_posts') ) : ?>
<div class="card pressthis">
	<h2><?php _e('Press This') ?></h2>
	<p><?php _e( 'Press This is a little tool that lets you grab bits of the web and create new posts with ease.' );?></p>
	<p><?php _e( 'Use Press This to clip text, images and videos from any web page. Then edit and add more straight from Press This before you save or publish it in a post on your site.' ); ?></p>


	<form>
		<h3><?php _e( 'Install Press This' ); ?></h3>
		<h4><?php _e( 'Bookmarklet' ); ?></h4>
		<p><?php _e( 'Drag the bookmarklet below to your bookmarks bar. Then, when you&#8217;re on a page you want to share, simply &#8220;press&#8221; it.' ); ?></p>

		<p class="pressthis-bookmarklet-wrapper">
			<a class="pressthis-bookmarklet" onclick="return false;" href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>"><span><?php _e( 'Press This' ); ?></span></a>
			<button type="button" class="button pressthis-js-toggle js-show-pressthis-code-wrap" aria-expanded="false" aria-controls="pressthis-code-wrap">
				<span class="dashicons dashicons-clipboard"></span>
				<span class="screen-reader-text"><?php _e( 'Copy &#8220;Press This&#8221; bookmarklet code' ) ?></span>
			</button>
		</p>

		<div class="hidden js-pressthis-code-wrap clear" id="pressthis-code-wrap">
			<p id="pressthis-code-desc">
				<?php _e( 'If you can&#8217;t drag the bookmarklet to your bookmarks, copy the following code and create a new bookmark. Paste the code into the new bookmark&#8217;s URL field.' ) ?>
			</p>
			<p>
				<textarea class="js-pressthis-code" rows="5" cols="120" readonly="readonly" aria-labelledby="pressthis-code-desc"><?php echo htmlspecialchars( get_shortcut_link() ); ?></textarea>
			</p>
		</div>

		<h4><?php _e( 'Direct link (best for mobile)' ); ?></h4>
		<p><?php _e( 'Follow the link to open Press This. Then add it to your device&#8217;s bookmarks or home screen.' ); ?></p>

		<p>
			<a class="button" href="<?php echo htmlspecialchars( admin_url( 'press-this.php' ) ); ?>"><?php _e( 'Open Press This' ) ?></a>
		</p>
		<script>
			jQuery( document ).ready( function( $ ) {
				var $showPressThisWrap = $( '.js-show-pressthis-code-wrap' );
				var $pressthisCode = $( '.js-pressthis-code' );

				$showPressThisWrap.on( 'click', function( event ) {
					var $this = $( this );

					$this.parent().next( '.js-pressthis-code-wrap' ).slideToggle( 200 );
					$this.attr( 'aria-expanded', $this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
				});

				// Select Press This code when focusing (tabbing) or clicking the textarea.
				$pressthisCode.on( 'click focus', function() {
					var self = this;
					setTimeout( function() { self.select(); }, 50 );
				});

			});
		</script>
	</form>
</div>
<?php
endif;

if ( current_user_can( 'import' ) ) :
$cats = get_taxonomy('category');
$tags = get_taxonomy('post_tag');
if ( current_user_can($cats->cap->manage_terms) || current_user_can($tags->cap->manage_terms) ) : ?>
<div class="card">
	<h2 class="title"><?php _e( 'Categories and Tags Converter' ) ?></h2>
	<p><?php printf( __('If you want to convert your categories to tags (or vice versa), use the <a href="%s">Categories and Tags Converter</a> available from the Import screen.'), 'import.php' ); ?></p>
</div>
<?php
endif;
endif;

/**
 * Fires at the end of the Tools Administration screen.
 *
 * @since 2.8.0
 */
do_action( 'tool_box' );
?>
</div>
<?php
include( ABSPATH . 'wp-admin/admin-footer.php' );
