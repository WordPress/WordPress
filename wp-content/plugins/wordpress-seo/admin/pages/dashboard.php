<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( filter_input( INPUT_GET, 'intro' ) ) {
	require WPSEO_PATH . 'admin/views/about.php';
	return;
}

$options = get_option( 'wpseo' );

if ( isset( $_GET['allow_tracking'] ) && check_admin_referer( 'wpseo_activate_tracking', 'nonce' ) ) {
	$options['yoast_tracking'] = ( $_GET['allow_tracking'] == 'yes' );
	update_option( 'wpseo', $options );

	if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
		wp_safe_redirect( $_SERVER['HTTP_REFERER'], 307 );
		exit;
	}
}


// Fix metadescription if so requested.
if ( isset( $_GET['fixmetadesc'] ) && check_admin_referer( 'wpseo-fix-metadesc', 'nonce' ) && $options['theme_description_found'] !== '' ) {
	$path = false;
	if ( file_exists( get_stylesheet_directory() . '/header.php' ) ) {
		// Theme or child theme.
		$path = get_stylesheet_directory();
	}
	elseif ( file_exists( get_template_directory() . '/header.php' ) ) {
		// Parent theme in case of a child theme.
		$path = get_template_directory();
	}

	if ( is_string( $path ) && $path !== '' ) {
		$fcontent    = file_get_contents( $path . '/header.php' );
		$msg         = '';
		$backup_file = date( 'Ymd-H.i.s-' ) . 'header.php.wpseobak';
		if ( ! file_exists( $path . '/' . $backup_file ) ) {
			$backupfile = fopen( $path . '/' . $backup_file, 'w+' );
			if ( $backupfile ) {
				fwrite( $backupfile, $fcontent );
				fclose( $backupfile );
				$msg = __( 'Backed up the original file header.php to <strong><em>' . esc_html( $backup_file ) . '</em></strong>, ', 'wordpress-seo' );

				$count    = 0;
				$fcontent = str_replace( $options['theme_description_found'], '', $fcontent, $count );
				if ( $count > 0 ) {
					$header_file = fopen( $path . '/header.php', 'w+' );
					if ( $header_file ) {
						if ( fwrite( $header_file, $fcontent ) !== false ) {
							$msg .= __( 'Removed hardcoded meta description.', 'wordpress-seo' );
							$options['theme_has_description']   = false;
							$options['theme_description_found'] = '';
							update_option( 'wpseo', $options );
						}
						else {
							$msg .= '<span class="error">' . __( 'Failed to remove hardcoded meta description.', 'wordpress-seo' ) . '</span>';
						}
						fclose( $header_file );
					}
				}
				else {
					wpseo_description_test();
					$msg .= '<span class="warning">' . __( 'Earlier found meta description was not found in file. Renewed the description test data.', 'wordpress-seo' ) . '</span>';
				}
				add_settings_error( 'yoast_wpseo_dashboard_options', 'error', $msg, 'updated' );
			}
		}
	}

	// Clean up the referrer url for later use.
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'nonce', 'fixmetadesc' ), $_SERVER['REQUEST_URI'] );
	}
}

if ( ( ! isset( $options['theme_has_description'] ) || ( ( isset( $options['theme_has_description'] ) && $options['theme_has_description'] === true ) || $options['theme_description_found'] !== '' ) ) || ( isset( $_GET['checkmetadesc'] ) && check_admin_referer( 'wpseo-check-metadesc', 'nonce' ) ) ) {
	wpseo_description_test();
	// Renew the options after the test.
	$options = get_option( 'wpseo' );
}
if ( isset( $_GET['checkmetadesc'] ) ) {
	// Clean up the referrer url for later use.
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'nonce', 'checkmetadesc' ), $_SERVER['REQUEST_URI'] );
	}
}
$yform = Yoast_Form::get_instance();

$yform->admin_header( true, 'wpseo' );

do_action( 'wpseo_all_admin_notices' );

if ( is_array( $options['blocking_files'] ) && count( $options['blocking_files'] ) > 0 ) {
	echo '<p id="blocking_files" class="wrong">';
	echo '<a href="javascript:wpseoKillBlockingFiles(\'', esc_js( wp_create_nonce( 'wpseo-blocking-files' ) ), '\')" class="button fixit">', __( 'Fix it.', 'wordpress-seo' ), '</a>';
	echo __( 'The following file(s) is/are blocking your XML sitemaps from working properly:', 'wordpress-seo' ), '<br />';
	foreach ( $options['blocking_files'] as $file ) {
		echo esc_html( $file ), '<br/>';
	}
	unset( $file );
	/* translators: %1$s expands to Yoast SEO */
	echo '
			', sprintf( __( 'Either delete them (this can be done with the "Fix it" button) or disable %1$s XML sitemaps.', 'wordpress-seo' ), 'Yoast SEO' ), '
		</p>';
}


if ( $options['theme_description_found'] !== '' ) {
	echo '<p id="metadesc_found notice" class="wrong settings_error">';
	echo '<a href="', esc_url( add_query_arg( array( 'nonce' => wp_create_nonce( 'wpseo-fix-metadesc' ) ), admin_url( 'admin.php?page=wpseo_dashboard&fixmetadesc' ) ) ), '" class="button fixit">', __( 'Fix it.', 'wordpress-seo' ), '</a>';
	echo ' <a href="', esc_url( add_query_arg( array( 'nonce' => wp_create_nonce( 'wpseo-check-metadesc' ) ), admin_url( 'admin.php?page=wpseo_dashboard&checkmetadesc' ) ) ), '" class="button checkit">', __( 'Re-check theme.', 'wordpress-seo' ), '</a>';
	/* translators: %1$s expands to Yoast SEO */
	echo sprintf( __( 'Your theme contains a meta description, which blocks %1$s from working properly, please delete the following line, or press fix it:', 'wordpress-seo' ), 'Yoast SEO' ) . '<br />';
	echo '<code>', esc_html( $options['theme_description_found'] ), '</code>';
	echo '</p>';
}


if ( strpos( get_option( 'permalink_structure' ), '%postname%' ) === false && $options['ignore_permalink'] === false ) {
	echo '<p id="wrong_permalink" class="wrong">';
	echo '<a href="', esc_url( admin_url( 'options-permalink.php' ) ), '" class="button fixit">', __( 'Fix it.', 'wordpress-seo' ), '</a>';
	echo '<a href="javascript:wpseoSetIgnore(\'permalink\',\'wrong_permalink\',\'', esc_js( wp_create_nonce( 'wpseo-ignore' ) ), '\');" class="button fixit">', __( 'Ignore.', 'wordpress-seo' ), '</a>';
	echo __( 'You do not have your postname in the URL of your posts and pages, it is highly recommended that you do. Consider setting your permalink structure to <strong>/%postname%/</strong>.', 'wordpress-seo' ), '</p>';
}

if ( get_option( 'page_comments' ) && $options['ignore_page_comments'] === false ) {
	echo '<p id="wrong_page_comments" class="wrong">';
	echo '<a href="javascript:setWPOption(\'page_comments\',\'0\',\'wrong_page_comments\',\'', esc_js( wp_create_nonce( 'wpseo-setoption' ) ), '\');" class="button fixit">', __( 'Fix it.', 'wordpress-seo' ), '</a>';
	echo '<a href="javascript:wpseoSetIgnore(\'page_comments\',\'wrong_page_comments\',\'', esc_js( wp_create_nonce( 'wpseo-ignore' ) ), '\');" class="button fixit">', __( 'Ignore.', 'wordpress-seo' ), '</a>';
	echo __( 'Paging comments is enabled, this is not needed in 999 out of 1000 cases, so the suggestion is to disable it, to do that, simply uncheck the box before "Break comments into pages..."', 'wordpress-seo' ), '</p>';
}

?>
	<h2 class="nav-tab-wrapper" id="wpseo-tabs">
		<a class="nav-tab nav-tab-active" id="general-tab"
		   href="#top#general"><?php _e( 'General', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="knowledge-graph-tab"
		   href="#top#knowledge-graph"><?php echo ( 'company' === $options['company_or_person'] ) ? __( 'Company Info', 'wordpress-seo' ) : __( 'Your Info', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="webmaster-tools-tab"
		   href="#top#webmaster-tools"><?php _e( 'Webmaster Tools', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="security-tab" href="#top#security"><?php _e( 'Security', 'wordpress-seo' ); ?></a>
	</h2>

	<div id="general" class="wpseotab">
		<?php if ( get_user_meta( get_current_user_id(), 'wpseo_ignore_tour' ) ) { ?>
			<p>
				<strong><?php _e( 'Introduction Tour', 'wordpress-seo' ); ?></strong><br/>
				<?php _e( 'Take this tour to quickly learn about the use of this plugin.', 'wordpress-seo' ); ?>
			</p>
			<p>
				<a class="button-secondary"
				   href="<?php echo esc_url( admin_url( 'admin.php?page=wpseo_dashboard&wpseo_restart_tour=1' ) ); ?>"><?php _e( 'Start Tour', 'wordpress-seo' ); ?></a>
			</p>

			<br/>
		<?php } ?>

		<p>
			<strong><?php _e( 'Latest Changes', 'wordpress-seo' ); ?></strong><br/>
			<?php
			/* translators: %s expands to Yoast SEO */
			printf( __( 'We\'ve summarized the most recent changes in %s.', 'wordpress-seo' ), 'Yoast SEO' );
			?>
		</p>
		<p>
			<a class="button-secondary"
			   href="<?php echo esc_url( admin_url( 'admin.php?page=wpseo_dashboard&intro=1' ) ); ?>"><?php _e( 'View Changes', 'wordpress-seo' ); ?></a>
		</p>

		<br/>

		<p>
			<strong><?php _e( 'Restore Default Settings', 'wordpress-seo' ); ?></strong><br/>
			<?php
			/* translators: %s expands to Yoast SEO */
			printf( __( 'If you want to restore a site to the default %s settings, press this button.', 'wordpress-seo' ), 'Yoast SEO' );
			?>
		</p>

		<p>
			<a onclick="if( !confirm('<?php _e( 'Are you sure you want to reset your SEO settings?', 'wordpress-seo' ); ?>') ) return false;" class="button" href="<?php echo esc_url( add_query_arg( array( 'nonce' => wp_create_nonce( 'wpseo_reset_defaults' ) ), admin_url( 'admin.php?page=wpseo_dashboard&wpseo_reset_defaults=1' ) ) ); ?>"><?php _e( 'Restore Default Settings', 'wordpress-seo' ); ?></a>
		</p>
	</div>
	<div id="knowledge-graph" class="wpseotab">
		<h3><?php _e( 'Website name', 'wordpress-seo' ); ?></h3>
		<p>
			<?php
			_e( 'Google shows your website\'s name in the search results, we will default to your site name but you can adapt it here. You can also provide an alternate website name you want Google to consider.', 'wordpress-seo' );
			?>
		</p>
		<?php
		$yform->textinput( 'website_name', __( 'Website name', 'wordpress-seo' ), array( 'placeholder' => get_bloginfo( 'name' ) ) );
		$yform->textinput( 'alternate_website_name', __( 'Alternate name', 'wordpress-seo' ) );
		?>
		<h3><?php _e( 'Company or person', 'wordpress-seo' ); ?></h3>
		<p>
			<?php
			// @todo add KB link - JdV.
			_e( 'This data is shown as metadata in your site. It is intended to appear in Google\'s Knowledge Graph. You can be either a company, or a person, choose either:', 'wordpress-seo' );
			?>
		</p>
		<?php
		$yform->select( 'company_or_person', __( 'Company or person', 'wordpress-seo' ), array(
			''        => __( 'Choose whether you\'re a company or person', 'wordpress-seo' ),
			'company' => __( 'Company', 'wordpress-seo' ),
			'person'  => __( 'Person', 'wordpress-seo' ),
		) );
		?>
		<div id="knowledge-graph-company">
			<h2><?php _e( 'Company', 'wordpress-seo' ); ?></h2>
			<?php
			$yform->textinput( 'company_name', __( 'Company Name', 'wordpress-seo' ) );
			$yform->media_input( 'company_logo', __( 'Company Logo', 'wordpress-seo' ) );
			?>
		</div>
		<div id="knowledge-graph-person">
			<h2><?php _e( 'Person', 'wordpress-seo' ); ?></h2>
			<?php $yform->textinput( 'person_name', __( 'Your name', 'wordpress-seo' ) ); ?>
		</div>
	</div>
	<div id="webmaster-tools" class="wpseotab">
		<?php
		echo '<p>', __( 'You can use the boxes below to verify with the different Webmaster Tools, if your site is already verified, you can just forget about these. Enter the verify meta values for:', 'wordpress-seo' ), '</p>';
		$yform->textinput( 'alexaverify', '<a target="_blank" href="http://www.alexa.com/siteowners/claim">' . __( 'Alexa Verification ID', 'wordpress-seo' ) . '</a>' );
		$yform->textinput( 'msverify', '<a target="_blank" href="' . esc_url( 'http://www.bing.com/webmaster/?rfp=1#/Dashboard/?url=' . urlencode( str_replace( 'http://', '', get_bloginfo( 'url' ) ) ) ) . '">' . __( 'Bing Webmaster Tools', 'wordpress-seo' ) . '</a>' );
		$yform->textinput( 'googleverify', '<a target="_blank" href="' . esc_url( 'https://www.google.com/webmasters/verification/verification?hl=en&siteUrl=' . urlencode( get_bloginfo( 'url' ) ) . '/' ) . '">Google Search Console</a>' );
		$yform->textinput( 'yandexverify', '<a target="_blank" href="http://help.yandex.com/webmaster/service/rights.xml#how-to">' . __( 'Yandex Webmaster Tools', 'wordpress-seo' ) . '</a>' );
		?>
	</div>
	<div id="security" class="wpseotab">
		<?php
		echo '<p>', __( 'Unchecking this box allows authors and editors to redirect posts, noindex them and do other things you might not want if you don\'t trust your authors.', 'wordpress-seo' ), '</p>';
		/* translators: %1$s expands to Yoast SEO */
		$yform->checkbox( 'disableadvanced_meta', sprintf( __( 'Disable the Advanced part of the %1$s meta box', 'wordpress-seo' ), 'Yoast SEO' ) );
		?>
	</div>
<?php
do_action( 'wpseo_dashboard' );

$yform->admin_footer();
