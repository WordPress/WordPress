<?php

add_action( 'admin_init', 'wr2x_admin_init' );

/**
 *
 * SETTINGS PAGE
 *
 */
 
function wr2x_settings_page() {
    global $wr2x_settings_api;
	echo '<div class="wrap">';
    jordy_meow_donation(true);
	$method = wr2x_getoption( "method", "wr2x_advanced", 'retina.js' );

	echo "<div id='icon-options-general' class='icon32'><br></div><h2>WP Retina 2x";
    by_jordy_meow();
    echo "</h2>";
	if ( $method == 'retina.js' ) {
		echo "<p><span style='color: blue;'>" . __( "Current method:", 'wp-retina-2x' ) . " <u>" . __( "Client side", 'wp-retina-2x' ) . "</u>.</span>";
	}
	if ( $method == 'Retina-Images' ) {
        echo "<p><span style='color: blue;'>" . __( "Current method:", 'wp-retina-2x' ) . " <u>" . __( "Server side", 'wp-retina-2x' ) . "</u>.</span>";
        if ( defined( 'MULTISITE' ) && MULTISITE == true  ) {
            if ( get_site_option( 'ms_files_rewriting' ) ) {
                // MODIFICATION: Craig Foster
                // 'ms_files_rewriting' support
                echo " <span style='color: red;'>" . __( "By the way, you are using a <b>WordPress Multi-Site installation</b>! You must edit your .htaccess manually and add '<b>RewriteRule ^files/(.+) wp-content/plugins/wp-retina-2x/wr2x_image.php?ms=true&file=$1 [L]</b>' as the first RewriteRule if you want the server-side to work.", 'wp-retina-2x' ) . "</span>";
            }
            else
                echo " <span style='color: red;'>" . __( "By the way, you are using a <b>WordPress Multi-Site installation</b>! You must edit your .htaccess manually and add '<b>RewriteRule ^(wp-content/.+\.(png|gif|jpg|jpeg|bmp|PNG|GIF|JPG|JPEG|BMP)) wp-content/plugins/wp-retina-2x/wr2x_image.php?ms=true&file=$1 [L]</b>' as the first RewriteRule if you want the server-side to work.", 'wp-retina-2x' ) . "</span>";   
        }
		echo "</p>";
		if ( !get_option('permalink_structure') )
			echo "<p><span style='color: red;'>" . __( "The permalinks are not enabled. They need to be enabled in order to use the server-side method.", 'wp-retina-2x' ) . "</span>";
	}
	
    //settings_errors();
    $wr2x_settings_api->show_navigation();
    $wr2x_settings_api->show_forms();
    echo '</div>';
	jordy_meow_footer();
}

function wr2x_getoption( $option, $section, $default = '' ) {
	$options = get_option( $section );
	if ( isset( $options[$option] ) ) {
        if ( $options[$option] == "off" ) {
            return false;
        }
        if ( $options[$option] == "on" ) {
            return true;
        }
		return $options[$option];
    }
	return $default;
}
 
function wr2x_admin_init() {

	require( 'wr2x_class.settings-api.php' );

	if ( delete_transient( 'wr2x_flush_rules' ) ) {
		global $wp_rewrite;
		wr2x_generate_rewrite_rules( $wp_rewrite, true );
	}
	
	$sections = array(
        array(
            'id' => 'wr2x_basics',
            'title' => __( 'Basics', 'wp-retina-2x' )
        ),
		array(
            'id' => 'wr2x_advanced',
            'title' => __( 'Advanced', 'wp-retina-2x' )
        )
    );
	
	$wpsizes = wr2x_get_image_sizes();
	$sizes = array();
	foreach ( $wpsizes as $name => $attr )
		$sizes["$name"] = sprintf( "%s (%dx%d)", $name, $attr['width'], $attr['height'] );
	
	$fields = array(
        'wr2x_basics' => array(
			array(
                'name' => 'ignore_sizes',
                'label' => __( 'Disabled Sizes', 'wp-retina-2x' ),
                'desc' => __( '<br />The selected sizes will not have their retina equivalent generated.', 'wp-retina-2x' ),
                'type' => 'multicheck',
                'options' => $sizes
            ),
			array(
                'name' => 'auto_generate',
                'label' => __( 'Auto Generate', 'wp-retina-2x' ),
                'desc' => __( 'Generate Retina images automatically when images are uploaded to the Media Library.', 'wp-retina-2x' ),
                'type' => 'checkbox',
                'default' => false
            )
        ),
		'wr2x_advanced' => array(
			array(
                'name' => 'method',
                'label' => __( 'Method', 'wp-retina-2x' ),
                'desc' => __( 
                	'<br />
                        The <b>Picturefill</b> method rewrites the HTML on-the-fly in order to use the new SRCSET. Since it is not supported by the browsers yet, the JS polyfill <a href="http://scottjehl.github.io/picturefill/">Picturefill</a> is used to load the images. <b>It is now the recommended method.</b><br /><br />
                        The <b>Retina JS</b> method is a 100% JS solution. The HTML loads the normal images, then if a retina device is detected, the retina images will be loaded. It is fail-safe but not efficient (images are loaded twice).<br /><br />
                        The <b>IMG Rewrite</b> method rewrites IMG\'s SRC tags on-the-fly with the retina images directly if the device supports them. This method does not work with most caching solutions.<br /><br />
                        The <b>Retina-Images method</b> uses a server handler: the images will be loaded through the <a href="https://github.com/Retina-Images/Retina-Images/">Retina-Images</a> PHP handler. Your .htaccess will be modified automatically. It might be too difficult to set-up if it does not work right away.<br /><br />
                        The <b>HTML srcset method</b> has been deprecated and has been replaced by <b>Picturefill</b>.
                	', 'wp-retina-2x' ),
                'type' => 'radio',
                'default' => 'retina.js',
                'options' => array(
                    'Picturefill' => __( "Picturefill", 'wp-retina-2x' ),
                	'retina.js' => __( "Retina.js", 'wp-retina-2x' ),
                    'HTML Rewrite' => __( "IMG Rewrite", 'wp-retina-2x' ),
					'Retina-Images' => __( "Retina-Images", 'wp-retina-2x' ),
                    'srcset' => __( "HTML srcset", 'wp-retina-2x' ),
					'none' => __( "None", 'wp-retina-2x' )
                )
            ),
            array(
                'name' => 'image_quality',
                'label' => __( 'Quality', 'wp-retina-2x' ),
                'desc' => __( 'Image Compression quality (between 0 and 100).', 'wp-retina-2x' ),
                'type' => 'text',
                'default' => 90
            ),
			array(
                'name' => 'debug',
                'label' => __( 'Debug Mode', 'wp-retina-2x' ),
                'desc' => __( 'If checked, the client will be always served Retina images. <br />Please use it for testing purposes. It also generates a <a href="' . plugins_url("wp-retina-2x") . '/wp-retina-2x.log">log file</a> in the plugin folder.', 'wp-retina-2x' ),
                'type' => 'checkbox',
                'default' => false
            ),
			array(
                'name' => 'hide_retina_column',
                'label' => __( 'Hide \'Retina\' column', 'wp-retina-2x' ),
                'desc' => __( 'Will hide the \'Retina Column\' from the Media Library.', 'wp-retina-2x' ),
                'type' => 'checkbox',
                'default' => false
            ),
			array(
                'name' => 'hide_retina_dashboard',
                'label' => __( 'Hide Retina Dashboard', 'wp-retina-2x' ),
                'desc' => __( 'Doesn\'t show the Retina Dashboard menu and tools.', 'wp-retina-2x' ),
                'type' => 'checkbox',
                'default' => false
            ),
            array(
                'name' => 'ignore_mobile',
                'label' => __( 'Ignore Mobile', 'wp-retina-2x' ),
                'desc' => __( 'Doesn\'t deliver Retina images to mobiles.<br />Does not work with Picturefill since it is managed by an external JS.', 'wp-retina-2x' ),
                'type' => 'checkbox',
                'default' => false
            )
		)
    );
    global $wr2x_settings_api;
	$wr2x_settings_api = new WeDevs_Settings_API;
    $wr2x_settings_api->set_sections( $sections );
    $wr2x_settings_api->set_fields( $fields );
    $wr2x_settings_api->admin_init();
}

function wr2x_update_option( $option ) {
	if ($option == 'wr2x_advanced') {
		set_transient( 'wr2x_flush_rules', true );
	}
}

function wr2x_generate_rewrite_rules( $wp_rewrite, $flush = false ) {
	global $wp_rewrite;
	$method = wr2x_getoption( "method", "wr2x_advanced", "retina.js" );
	if ($method == "Retina-Images") {

		// MODIFICATION: docwhat
		// get_home_url() -> trailingslashit(site_url())
		// REFERENCE: http://wordpress.org/support/topic/plugin-wp-retina-2x-htaccess-generated-with-incorrect-rewriterule
		
		// MODIFICATION BY h4ir9 
		// .*\.(jpg|jpeg|gif|png|bmp) -> (.+.(?:jpe?g|gif|png))
		// REFERENCE: http://wordpress.org/support/topic/great-but-needs-a-little-update
		
		$handlerurl = str_replace( trailingslashit(site_url()), '', plugins_url( 'wr2x_image.php', __FILE__ ) );
		add_rewrite_rule( '(.+.(?:jpe?g|gif|png))', $handlerurl, 'top' );		
	}
	if ( $flush == true ) {
		$wp_rewrite->flush_rules();
	}
}

?>
