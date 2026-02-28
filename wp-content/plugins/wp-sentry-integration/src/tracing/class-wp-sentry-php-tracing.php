<?php

use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\SpanStatus;
use Sentry\Tracing\TransactionSource;
use function Sentry\continueTrace;
use function Sentry\getBaggage;
use function Sentry\getTraceparent;
use function Sentry\getW3CTraceparent;

/**
 * Sentry for WordPress PHP Tracing.
 *
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
final class WP_Sentry_Php_Tracing {
	use WP_Sentry_Tracks_Pushed_Scopes_And_Spans;

	/** @var class-string[] */
	private const FEATURES = [
		WP_Sentry_Tracing_Feature_DB::class,
		WP_Sentry_Tracing_Feature_HTTP::class,
		WP_Sentry_Tracing_Feature_Theme::class,
		WP_Sentry_Tracing_Feature_Plugins::class,
		WP_Sentry_Tracing_Feature_Transients::class,
	];

	/** @var WP_Sentry_Php_Tracing|null */
	private static $instance;

	public static function get_instance(): WP_Sentry_Php_Tracing {
		return self::$instance ?: self::$instance = new self;
	}

	/** @var string|null */
	private $transaction_name;

	/** @var \Sentry\Tracing\Transaction|null */
	private $transaction;

	/** @var \Sentry\Tracing\Span|null */
	private $bootstrap_span;

	/** @var \Sentry\Tracing\Span|null */
	private $app_span;

	/** @var bool */
	private $tracingEnabled;

	/** @var bool */
	private $profilingEnabled;

	private function __construct() {
		$this->tracingEnabled   = false;
		$this->profilingEnabled = false;

		$hub = WP_Sentry_Php_Tracker::get_instance()->get_client();

		if ( $hub->getClient() !== null ) {
			$options = $hub->getClient()->getOptions();

			if ( $options->isTracingEnabled() || $options->isSpotlightEnabled() ) {
				$this->tracingEnabled   = true;
				$this->profilingEnabled = $options->getProfilesSampleRate() > 0;

				$this->start_transaction( $hub );
				$this->register_hooks();
			}
		}
	}

	public function is_tracing_enabled(): bool {
		return $this->tracingEnabled;
	}

	public function is_profiling_enabled(): bool {
		return $this->profilingEnabled;
	}

	private function start_transaction( HubInterface $sentry ): bool {
		if ( $this->transaction !== null ) {
			return false;
		}

		$requestStartTime = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime( true );

		/** @var \GuzzleHttp\Psr7\ServerRequest $request */
		$request = $this->resolve_request_from_globals();

		$context = continueTrace(
			$request->getHeaderLine( 'sentry-trace' ) ?: $request->getHeaderLine( 'traceparent' ),
			$request->getHeaderLine( 'baggage' )
		);

		$requestPath = '/' . ltrim( $request->getUri()->getPath(), '/' );

		$context->setOp( 'http.server' );
		$context->setName( $requestPath );
		$context->setSource( TransactionSource::url() );
		$context->setStartTimestamp( $requestStartTime );

		$context->setData( [
			'url'                 => $requestPath,
			'http.request.method' => strtoupper( $request->getMethod() ),
		] );

		$transaction = $sentry->startTransaction( $context );

		SentrySdk::getCurrentHub()->setSpan( $transaction );

		$this->transaction = $transaction;

		if ( $transaction->getSampled() === true ) {
			$initSpanContext = new SpanContext;
			$initSpanContext->setOp( 'app.bootstrap' );
			$initSpanContext->setStartTimestamp( $transaction->getStartTimestamp() );

			$this->bootstrap_span = $transaction->startChild( $initSpanContext );

			SentrySdk::getCurrentHub()->setSpan( $this->bootstrap_span );
		}

		return true;
	}

	private function register_hooks(): void {
		// Always register the features because they will also collect breadcrumbs
		foreach ( self::FEATURES as $feature ) {
			new $feature();
		}

		add_action( 'parse_request', [ $this, 'handle_parse_request' ] );

		add_filter( 'rest_dispatch_request', [ $this, 'handle_rest_dispatch_request' ], 9999, 4 );

		add_action( 'parse_query', [ $this, 'handle_parse_query' ] );

		if ( ! defined( 'WP_SENTRY_BROWSER_TRACE_PROPAGATION' ) || WP_SENTRY_BROWSER_TRACE_PROPAGATION ) {
			// Why are we not using `wp_head`? Because we want to make sure we render the meta tags before the scripts are printed
			add_action( 'wp_print_scripts', [ $this, 'render_trace_propagation_meta' ] );
			add_action( 'admin_print_scripts', [ $this, 'render_trace_propagation_meta' ] );
		}

		if ( $this->transaction === null || $this->transaction->getSampled() === false ) {
			return;
		}

		add_filter( 'status_header', [ $this, 'handle_status_header' ], 9999, 2 );

		add_action( 'wp_loaded', [ $this, 'handle_wp_loaded' ] );

		add_action( 'shutdown', [ $this, 'handle_shutdown' ] );
	}

	public function handle_parse_request( WP $request ): void {
		// We only want to handle the transaction for the frontend
		if ( is_admin() || is_network_admin() ) {
			return;
		}

		// Match the request against the matches rule, but this time we capture the offsets of the capture groups
		preg_match( "#^{$request->matched_rule}#", $request->request, $matches, PREG_OFFSET_CAPTURE );

		// Parse the matched query into an array, the `query_vars` provided are not in the correct order and we need that
		// @TODO: Validate that `matched_query` always contains the query params in the correct order
		parse_str( $request->matched_query, $parsed_query );

		// Start with the original request path as the transaction name
		$transaction = $request->request;

		// Since the part of the transaction that we are
		$offset_delta = 0;

		foreach ( array_keys( $parsed_query ) as $query_index => $query_key ) {
			// Not all matches have a query value, so we need to check if there is one
			if ( ! isset( $matches[ $query_index + 1 ] ) ) {
				break;
			}

			[ $query_value, $query_offset ] = $matches[ $query_index + 1 ];

			// Check if the query value starts with a slash, if so we want to remove it so it doesn't remove it from the final transaction name
			if ( strpos( $query_value, '/' ) === 0 ) {
				$query_value = ltrim( $query_value, '/' );

				// We need to increase the offset by one because we removed the slash
				++ $query_offset;
			}

			// Normalize the query key to a slug so we can use it as a placeholder in the transaction name
			$query_key = $this->slugify_string( $query_key );

			$placeholder = "{{$query_key}}";

			$transaction = substr_replace( $transaction, $placeholder, $offset_delta + (int) $query_offset, strlen( $query_value ) );

			$offset_delta += strlen( $placeholder ) - strlen( $query_value );
		}

		$this->set_transaction_name( '/' . $transaction );
	}

	public function handle_rest_dispatch_request( $dispatch_result, WP_REST_Request $request, string $route, array $handler ) {
		preg_match( "#^{$route}#", $transaction = $request->get_route(), $matches, PREG_OFFSET_CAPTURE );

		$matches = array_filter( $matches, function ( $key ) {
			return is_string( $key );
		}, ARRAY_FILTER_USE_KEY );

		$matches = array_map( function ( array $match, string $key ) {
			$match[2] = $key;

			return $match;
		}, $matches, array_keys( $matches ) );

		usort( $matches, function ( $a, $b ) {
			return $a[1] <=> $b[1];
		} );

		$offset_delta = 0;

		foreach ( $matches as $match ) {
			[ $value, $offset, $key ] = $match;

			$placeholder = "{{$key}}";

			$transaction = substr_replace( $transaction, $placeholder, $offset_delta + (int) $offset, strlen( $value ) );

			$offset_delta += strlen( $placeholder ) - strlen( $value );
		}

		$this->set_transaction_name( $transaction );

		return $dispatch_result;
	}

	public function handle_parse_query( WP_Query $query ): void {
		// The application can do many queries, we are only interested in the main query
		if ( ! $query->is_main_query() ) {
			return;
		}

		// Test if the current page is the search page so we can set the transaction name accordingly
		if ( $query->is_search && ! ( is_admin() || is_network_admin() ) ) {
			$this->set_transaction_name( '/?s={search_query}' );
		}
	}

	public function render_trace_propagation_meta(): void {
		// Little memo to make sure we only print the meta tags once
		static $printed = false;

		if ( $printed === true ) {
			return;
		}

		$printed = true;

		echo sprintf( '<meta name="sentry-trace" content="%s" />' . "\n", getTraceparent() );
		echo sprintf( '<meta name="traceparent" content="%s" />' . "\n", getW3CTraceparent() );
		echo sprintf( '<meta name="baggage" content="%s" />' . "\n", getBaggage() );
	}

	public function handle_status_header( string $status_header, int $code ): string {
		if ( $this->transaction !== null ) {
			$this->transaction->setHttpStatus( $code );
		}

		return $status_header;
	}

	public function handle_wp_loaded(): void {
		if ( $this->bootstrap_span !== null ) {
			$this->bootstrap_span->finish();
		}

		$appContextStart = new SpanContext;
		$appContextStart->setOp( 'wp.handle' );
		$appContextStart->setStartTimestamp( $this->bootstrap_span ? $this->bootstrap_span->getEndTimestamp() : microtime( true ) );

		$this->app_span = $this->transaction->startChild( $appContextStart );

		SentrySdk::getCurrentHub()->setSpan( $this->app_span );

		$this->bootstrap_span = null;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$action = $_POST['action'] ?? $_GET['action'] ?? null;

			if ( $action !== null ) {
				$this->set_transaction_name( "/wp-admin/admin-ajax.php?action={$action}" );
			}
		}
	}

	public function handle_shutdown(): void {
		if ( $this->transaction === null ) {
			return;
		}

		// We should skip sending the transaction if the response code is 404
		if ( $this->transaction->getStatus() === SpanStatus::notFound() ) {
			$this->app_span    = null;
			$this->transaction = null;

			return;
		}

		if ( $this->transaction->getStatus() === null ) {
			$this->transaction->setHttpStatus( http_response_code() );
		}

		if ( $this->app_span !== null ) {
			$this->app_span->finish();
			$this->app_span = null;
		}

		$this->transaction->finish();
		$this->transaction = null;
	}

	private function set_transaction_name( string $transaction ): void {
		$this->transaction_name = $transaction;

		if ( $this->transaction !== null ) {
			$this->transaction->setName( $transaction );
			$this->transaction->getMetadata()->setSource( TransactionSource::route() );
		}
	}

	public function get_transaction_name(): ?string {
		return $this->transaction_name;
	}

	private function resolve_request_from_globals() {
		if ( class_exists( WPSentry\ScopedVendor\GuzzleHttp\Psr7\ServerRequest::class ) ) {
			return WPSentry\ScopedVendor\GuzzleHttp\Psr7\ServerRequest::fromGlobals();
		}

		if ( class_exists( GuzzleHttp\Psr7\ServerRequest::class ) ) {
			return GuzzleHttp\Psr7\ServerRequest::fromGlobals();
		}

		throw new RuntimeException( 'Cannot find a PSR-7 implementation to create a request from globals.' );
	}

	private function slugify_string( string $string ): string {
		return strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '_', $string ) );
	}
}
