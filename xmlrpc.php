<?php
/**
 * XML-RPC protocol support for WordPress
 *
 * @package WordPress
 */

/**
 * Whether this is an XML-RPC Request.
 *
 * @var bool
 */
define( 'XMLRPC_REQUEST', true );

// Discard unneeded cookies sent by some browser-embedded clients.
$_COOKIE = array();

// $HTTP_RAW_POST_DATA was deprecated in PHP 5.6 and removed in PHP 7.0.
// phpcs:disable PHPCompatibility.Variables.RemovedPredefinedGlobalVariables.http_raw_post_dataDeprecatedRemoved
if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
	$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
}

// Fix for mozBlog and other cases where '<?xml' isn't on the very first line.
$HTTP_RAW_POST_DATA = trim( $HTTP_RAW_POST_DATA );
// phpcs:enable

/** Include the bootstrap for setting up WordPress environment */
require_once __DIR__ . '/wp-load.php';

if ( ! wp_validate_boolean( get_option( 'xmlrpc_enabled', 1 ) ) ) {
	$remote_identifiers = array();

	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$remote_address = wp_unslash( $_SERVER['REMOTE_ADDR'] );

		if ( $remote_address ) {
			$remote_identifiers[] = $remote_address;

			if ( filter_var( $remote_address, FILTER_VALIDATE_IP ) ) {
				$reverse_lookup = @gethostbyaddr( $remote_address );

				if ( $reverse_lookup && $reverse_lookup !== $remote_address ) {
					$remote_identifiers[] = $reverse_lookup;
				}
			}
		}
	}

	if ( isset( $_SERVER['REMOTE_HOST'] ) ) {
		$remote_identifiers[] = wp_unslash( $_SERVER['REMOTE_HOST'] );
	}

	$remote_identifiers = array_values( array_unique( array_filter( $remote_identifiers ) ) );

	/**
	 * Filters the hosts that are allowed to access XML-RPC when it is disabled.
	 *
	 * Returning `true` from the filter will bypass the restriction entirely.
	 *
	 * @since 6.7.0
	 *
	 * @param string[]|true $allowed_hosts      Array of allowed host names or IP addresses. Default empty array.
	 * @param string[]      $remote_identifiers Array of detected remote identifiers for the request.
	 */
	$allowed_hosts = apply_filters( 'xmlrpc_allowed_hosts', array(), $remote_identifiers );

	$request_allowed = true === $allowed_hosts;

	if ( ! $request_allowed ) {
		$allowed_hosts = array_map( 'strtolower', (array) $allowed_hosts );

		foreach ( $remote_identifiers as $remote_identifier ) {
			if ( in_array( strtolower( $remote_identifier ), $allowed_hosts, true ) ) {
				$request_allowed = true;
				break;
			}
		}
	}

	if ( ! $request_allowed ) {
		status_header( 403 );
		nocache_headers();
		header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
		echo esc_html__( 'XML-RPC services are disabled on this site.' );
		exit;
	}
}

if ( isset( $_GET['rsd'] ) ) { // https://cyber.harvard.edu/blogs/gems/tech/rsd.html
	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
	echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
	?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
	<service>
		<engineName>WordPress</engineName>
		<engineLink>https://wordpress.org/</engineLink>
		<homePageLink><?php bloginfo_rss( 'url' ); ?></homePageLink>
		<apis>
			<api name="WordPress" blogID="1" preferred="true" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="Movable Type" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="Blogger" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<?php
			/**
			 * Fires when adding APIs to the Really Simple Discovery (RSD) endpoint.
			 *
			 * @link https://cyber.harvard.edu/blogs/gems/tech/rsd.html
			 *
			 * @since 3.5.0
			 */
			do_action( 'xmlrpc_rsd_apis' );
			?>
		</apis>
	</service>
</rsd>
	<?php
	exit;
}

require_once ABSPATH . 'wp-admin/includes/admin.php';
require_once ABSPATH . WPINC . '/class-IXR.php';
require_once ABSPATH . WPINC . '/class-wp-xmlrpc-server.php';

/**
 * Posts submitted via the XML-RPC interface get that title
 *
 * @name post_default_title
 * @var string
 */
$post_default_title = '';

/**
 * Filters the class used for handling XML-RPC requests.
 *
 * @since 3.1.0
 *
 * @param string $class The name of the XML-RPC server class.
 */
$wp_xmlrpc_server_class = apply_filters( 'wp_xmlrpc_server_class', 'wp_xmlrpc_server' );
$wp_xmlrpc_server       = new $wp_xmlrpc_server_class();

// Fire off the request.
$wp_xmlrpc_server->serve_request();

exit;

/**
 * logIO() - Writes logging info to a file.
 *
 * @since 1.2.0
 * @deprecated 3.4.0 Use error_log()
 * @see error_log()
 *
 * @global int|bool $xmlrpc_logging Whether to enable XML-RPC logging.
 *
 * @param string $io  Whether input or output.
 * @param string $msg Information describing logging reason.
 */
function logIO( $io, $msg ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
	if ( ! empty( $GLOBALS['xmlrpc_logging'] ) ) {
		error_log( $io . ' - ' . $msg );
	}
}
