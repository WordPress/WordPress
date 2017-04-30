<?php
/**
 * Web interface for generating the WooCommerce POT files
 *
 * @since 2.0
 * @package WooCommerce
 * @author Geert De Deckere
 */

/**
 * Note: this file is locked by default since it should not be publicly accessible
 * on a live website. You can unlock it by temporarily removing the following line.
 */
exit( 'Locked' );

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

if(isset($_SERVER['SHELL'])){
	$is_shell = True;
} else {
	$is_shell = False;
}

// Load the makepot generator
require 'makepot.php';
$makepot = new WC_Makepot;

// Regeneration requested
if ( (isset($_GET) and ! empty( $_GET['generate']) || (isset($argc, $argv) && $argc==2 && $argv[1]=="generate")) ) {
	// Generate woocommerce and woocommerce-admin POT files
	$results = array();
	foreach ( $makepot->projects as $name => $project ) {
		$results[ $name ] = $makepot->generate_pot( $name );
	}
}

// Load WooCommerce POT-files info
$pot_files = array();
foreach( $makepot->projects as $name => $project ) {
	$pot_files[ $name ] = array(
		'file'        => $project['file'],
		'file_exists' => file_exists( $project['file'] ),
		'is_readable' => is_readable( $project['file'] ),
		'is_writable' => is_writable( $project['file'] ),
		'filemtime'   => ( is_readable( $project['file'] ) ) ? filemtime( $project['file'] ) : false,
		'filesize'    => ( is_readable( $project['file'] ) ) ? filesize( $project['file'] ) : false,
	);
}

if($is_shell) {
	printf("WooCommerce %s POT Generator\n\n", $makepot->woocommerce_version());
	if ( ! empty( $results ) ) {
		foreach ( $results as $pot_file => $succeeded ) {
			printf(" * %s %s\n", basename( $pot_files[ $pot_file ]['file'] ), $succeeded ? 'successfully generated' : 'could not be generated');
		}
		echo "\n";
	}
	echo "This tool will (re)generate and overwrite the following WooCommerce POT-files:\n\n";
	foreach ( $pot_files as $pot_file ) {
		printf(" - %-30s\t[%swritable]\n", basename( $pot_file['file'] ), $pot_file['is_writable'] ? "" : "not ");
		printf("   * Path: %s\n", dirname( $pot_file['file'] ) . '/');
		printf("   * Size: %s\n", $pot_file['file_exists'] ? number_format( $pot_file['filesize'], 0 ) : '--');
		printf("   * Last updated: %s\n", $pot_file['filemtime'] ? @date( 'F jS Y H:i:s', $pot_file['filemtime'] ) : '--');
	}
	
	printf("\nTo Generate POT-files now you must run:\n\n");
	printf("\tphp %s generate\n\n", $argv[0]);
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
	<title>WooCommerce POT Generator</title>
	<style>
	html { font-family:monospace; }
	ul { margin-bottom:1em; }
	ul ul { padding-left:0; list-style:none; color:#999; }

	.success { color:green; }
	.error { color:red; }
	.button { display:inline-block; padding:0.5em 1em; background:#ddd; font-weight:bold; color:#000; text-decoration:none; }
	.button:hover { background:#ccc; }
	.button.disabled { background:#eee; color:#888; cursor:default; }

	@-webkit-keyframes rotate { from { -webkit-transform:rotate(0deg); } to { -webkit-transform:rotate(360deg); } }
	   @-moz-keyframes rotate { from {    -moz-transform:rotate(0deg); } to {    -moz-transform:rotate(360deg); } }
	        @keyframes rotate { from {         transform:rotate(0deg); } to {         transform:rotate(360deg); } }
	.spinner { display:inline-block;
		-webkit-animation:rotate 0.4s linear infinite;
		   -moz-animation:rotate 0.4s linear infinite;
		        animation:rotate 0.4s linear infinite;
	}
	</style>

</head>
<body>

	<h1>WooCommerce <?php echo $makepot->woocommerce_version() ?> POT Generator</h1>

	<?php if ( ! empty( $results ) ) { ?>
		<ul>
			<?php foreach ( $results as $pot_file => $succeeded ) { ?>
				<?php if ( $succeeded ) { ?>
					<li class="success"><strong><?php echo basename( $pot_files[ $pot_file ]['file'] ) ?> successfully generated.</strong></li>
				<?php } else { ?>
					<li class="error"><strong><?php echo basename( $pot_files[ $pot_file ]['file'] ) ?> could not be generated.</strong></li>
				<?php } ?>
			<?php } ?>
		</ul>
	<?php } ?>

	<p>This tool will (re)generate and overwrite the following WooCommerce POT-files:</p>

	<ul>
		<?php foreach ( $pot_files as $pot_file ) { ?>
			<li>
				<strong><?php echo basename( $pot_file['file'] ) ?></strong>
				<?php if ( $pot_file['is_writable'] ) { ?>
					<strong class="success">[writable]</strong>
				<?php } else { ?>
					<strong class="error">[not writable]</strong>
				<?php } ?>
				<ul>
					<li>Path: <?php echo dirname( $pot_file['file'] ) . '/' ?></li>
					<li>Size: <?php echo ( $pot_file['file_exists'] ) ? number_format( $pot_file['filesize'], 0 ) . ' bytes' : '--' ?></li>
					<li>Last updated: <?php echo ( $pot_file['filemtime'] ) ? @date( 'F jS Y H:i:s', $pot_file['filemtime'] ) : '--' ?></li>
				</ul>
			</li>
		<?php } ?>
	</ul>

	<p>
		<a id="submit" class="button" href="?generate=1">Generate POT-files now</a>
	</p>

	<script>
	// Show a loading animation
	document.getElementById('submit').onclick = function () {
		this.className += ' disabled';
		this.innerHTML = 'Generating POT-files <span class="spinner">/</span>';
	};
	</script>

</body>
</html>
<?php } ?>
