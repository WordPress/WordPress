<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/* translators: Page title of the About WordPress page in the admin. */
$title = _x( 'About', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
	<div class="wrap about__container">

		<div class="about__header">
			<div class="about__header-image">
				<picture>
					<source media="(max-width: 782px)" srcset="data:image/svg+xml,<?php echo rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 205 109" width="205"><g fill="none" fill-rule="evenodd"><path fill="#000" fill-rule="nonzero" d="M65.901 108.433c2.546-5.254 4.927-9.195 7.143-11.822H1.246v-5.173h71.798c-2.216-2.627-4.597-6.568-7.143-11.822h4.31c5.173 5.993 10.592 10.427 16.257 13.3v2.217c-5.665 2.791-11.084 7.225-16.256 13.3h-4.31z"/><path fill="#000" fill-rule="nonzero" d="M29.345 26.834c-.129 1.365-.633 2.432-1.512 3.2-.879.76-2.048 1.142-3.507 1.142-1.02 0-1.919-.24-2.698-.72-.773-.487-1.371-1.176-1.793-2.066-.422-.891-.641-1.925-.659-3.103v-1.195c0-1.207.214-2.27.642-3.19.427-.92 1.04-1.63 1.836-2.128.803-.498 1.729-.747 2.778-.747 1.412 0 2.549.384 3.41 1.152.861.767 1.362 1.851 1.503 3.252H27.13c-.105-.92-.375-1.582-.809-1.987-.427-.41-1.057-.615-1.89-.615-.966 0-1.71.355-2.232 1.064-.515.703-.779 1.737-.79 3.102v1.134c0 1.383.245 2.437.738 3.164.498.727 1.224 1.09 2.18 1.09.872 0 1.529-.197 1.968-.59.44-.392.718-1.045.835-1.959h2.215zm14.008-1.898c0 1.253-.217 2.355-.65 3.304-.434.944-1.055 1.67-1.864 2.18-.803.504-1.728.756-2.777.756-1.037 0-1.963-.252-2.777-.756-.809-.51-1.436-1.234-1.881-2.171-.44-.938-.662-2.018-.668-3.243v-.72c0-1.249.22-2.35.659-3.306.445-.955 1.07-1.684 1.872-2.188.809-.51 1.734-.765 2.777-.765 1.043 0 1.966.252 2.769.756.808.498 1.432 1.219 1.872 2.162.44.938.662 2.03.668 3.279v.712zm-2.224-.668c0-1.418-.27-2.505-.808-3.261-.534-.756-1.292-1.134-2.277-1.134-.96 0-1.714.378-2.258 1.134-.54.75-.815 1.813-.827 3.19v.739c0 1.406.273 2.493.818 3.26.55.768 1.312 1.152 2.285 1.152.984 0 1.74-.375 2.267-1.125.534-.75.8-1.846.8-3.287v-.668zM47.368 31V18.203h3.78c1.13 0 2.132.252 3.005.756a5.146 5.146 0 012.039 2.145c.48.925.72 1.986.72 3.181v.642c0 1.213-.243 2.28-.729 3.199a5.057 5.057 0 01-2.065 2.127c-.89.498-1.913.747-3.068.747h-3.682zm2.223-11.004v9.229h1.45c1.167 0 2.06-.364 2.681-1.09.627-.733.947-1.782.958-3.147v-.712c0-1.388-.302-2.449-.905-3.181-.604-.733-1.48-1.099-2.628-1.099h-1.556zm18.816 5.291h-5.256v3.938h6.144V31h-8.368V18.203h8.306v1.793h-6.082v3.533h5.256v1.758zM81.534 31H79.32V18.203h2.215V31zm11.222-3.296c0-.562-.199-.996-.597-1.3-.393-.305-1.105-.613-2.136-.924-1.031-.31-1.852-.656-2.461-1.037-1.166-.732-1.75-1.687-1.75-2.865 0-1.031.42-1.88 1.258-2.549.843-.668 1.936-1.002 3.278-1.002.89 0 1.685.164 2.382.492a3.898 3.898 0 011.643 1.407c.399.603.598 1.274.598 2.013h-2.215c0-.669-.21-1.19-.633-1.565-.416-.38-1.013-.571-1.793-.571-.726 0-1.292.155-1.696.466-.398.31-.598.744-.598 1.3 0 .47.217.862.65 1.178.434.31 1.15.615 2.145.914.996.293 1.796.63 2.4 1.01.603.376 1.046.81 1.327 1.302.281.486.422 1.057.422 1.713 0 1.067-.41 1.916-1.23 2.55-.815.626-1.923.94-3.323.94-.926 0-1.778-.17-2.558-.51-.773-.346-1.376-.82-1.81-1.424-.428-.603-.642-1.306-.642-2.11h2.224c0 .727.24 1.29.72 1.688.481.399 1.17.598 2.066.598.774 0 1.354-.155 1.74-.466.393-.316.59-.732.59-1.248zm14.481-1.459V31h-2.224V18.203h4.896c1.43 0 2.563.372 3.401 1.116.844.745 1.266 1.729 1.266 2.954 0 1.253-.413 2.229-1.239 2.926-.821.697-1.972 1.046-3.454 1.046h-2.646zm0-1.784h2.672c.791 0 1.395-.185 1.811-.554.416-.375.624-.914.624-1.617 0-.691-.211-1.242-.633-1.652-.422-.416-1.002-.63-1.74-.642h-2.734v4.465zm21.303.475c0 1.253-.217 2.355-.65 3.304-.434.944-1.055 1.67-1.864 2.18-.802.504-1.728.756-2.777.756-1.037 0-1.963-.252-2.777-.756-.809-.51-1.436-1.234-1.881-2.171-.44-.938-.662-2.018-.668-3.243v-.72c0-1.249.22-2.35.659-3.306.445-.955 1.069-1.684 1.872-2.188.809-.51 1.734-.765 2.777-.765 1.043 0 1.966.252 2.769.756.809.498 1.433 1.219 1.872 2.162.44.938.662 2.03.668 3.279v.712zm-2.224-.668c0-1.418-.269-2.505-.808-3.261-.533-.756-1.292-1.134-2.277-1.134-.96 0-1.713.378-2.258 1.134-.539.75-.815 1.813-.827 3.19v.739c0 1.406.273 2.493.818 3.26.551.768 1.312 1.152 2.285 1.152.984 0 1.74-.375 2.268-1.125.533-.75.799-1.846.799-3.287v-.668zm13.718 1.02h-5.255v3.937h6.143V31h-8.367V18.203h8.306v1.793h-6.082v3.533h5.255v1.758zm13.92-5.292h-3.99V31h-2.206V19.996h-3.955v-1.793h10.151v1.793zm8.19 6.073h-2.478V31h-2.224V18.203h4.5c1.477 0 2.616.331 3.419.993.803.662 1.204 1.62 1.204 2.874 0 .856-.208 1.574-.624 2.154-.41.574-.984 1.016-1.723 1.327l2.874 5.335V31h-2.381l-2.567-4.93zm-2.478-1.784h2.285c.75 0 1.336-.187 1.758-.562.421-.381.632-.9.632-1.556 0-.686-.196-1.216-.588-1.59-.387-.376-.967-.57-1.741-.58h-2.346v4.288zm14.561-.017l2.945-6.065h2.461l-4.289 8.086V31h-2.233v-4.71l-4.298-8.087h2.47l2.944 6.065z"/><path stroke="#000" stroke-width="2" d="M2 1h201v46H2z"/></g></svg>' ); ?>" />
					<img alt="<?php _e( 'Code is Poetry' ); ?>" src="data:image/svg+xml,<?php echo rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 1210 48"><path fill="#000" d="M65.875 39.875c2.583-5.333 5-9.333 7.25-12H.25v-5.25h72.875c-2.25-2.667-4.667-6.667-7.25-12h4.375c5.25 6.083 10.75 10.583 16.5 13.5v2.25c-5.75 2.833-11.25 7.333-16.5 13.5h-4.375zM1035.34 26.834c-.12 1.365-.63 2.432-1.51 3.2-.88.76-2.04 1.142-3.5 1.142-1.02 0-1.92-.24-2.7-.72-.78-.487-1.37-1.176-1.79-2.066-.43-.891-.65-1.925-.66-3.103v-1.195c0-1.207.21-2.27.64-3.19.43-.92 1.04-1.63 1.83-2.128.81-.498 1.73-.747 2.78-.747 1.41 0 2.55.384 3.41 1.152.86.767 1.36 1.851 1.5 3.252h-2.21c-.11-.92-.37-1.582-.81-1.987-.43-.41-1.06-.615-1.89-.615-.96 0-1.71.355-2.23 1.064-.52.703-.78 1.737-.79 3.102v1.134c0 1.383.24 2.437.74 3.164.49.727 1.22 1.09 2.18 1.09.87 0 1.53-.197 1.97-.59.43-.392.71-1.045.83-1.959h2.21zm14.01-1.898c0 1.253-.21 2.355-.65 3.304-.43.944-1.05 1.67-1.86 2.18-.8.504-1.73.756-2.78.756-1.04 0-1.96-.252-2.78-.756-.8-.51-1.43-1.234-1.88-2.171-.44-.938-.66-2.018-.66-3.243v-.72c0-1.249.22-2.35.65-3.306.45-.955 1.07-1.684 1.88-2.188.81-.51 1.73-.765 2.77-.765 1.05 0 1.97.252 2.77.756.81.498 1.44 1.219 1.87 2.162.44.938.67 2.03.67 3.279v.712zm-2.22-.668c0-1.418-.27-2.505-.81-3.261-.53-.756-1.29-1.134-2.28-1.134-.96 0-1.71.378-2.25 1.134-.54.75-.82 1.813-.83 3.19v.739c0 1.406.27 2.493.82 3.26.55.768 1.31 1.152 2.28 1.152.99 0 1.74-.375 2.27-1.125s.8-1.846.8-3.287v-.668zm6.24 6.732V18.203h3.78c1.13 0 2.13.252 3 .756a5.138 5.138 0 012.04 2.145c.48.925.72 1.986.72 3.181v.642c0 1.213-.24 2.28-.73 3.199a5.08 5.08 0 01-2.06 2.127c-.89.498-1.92.747-3.07.747h-3.68zm2.22-11.004v9.229h1.45c1.17 0 2.06-.364 2.68-1.09.63-.733.95-1.782.96-3.147v-.712c0-1.388-.3-2.449-.9-3.181-.61-.733-1.48-1.099-2.63-1.099h-1.56zm18.82 5.291h-5.26v3.938h6.14V31h-8.36V18.203h8.3v1.793h-6.08v3.533h5.26v1.758zM1087.53 31h-2.21V18.203h2.21V31zm11.23-3.296c0-.562-.2-.996-.6-1.3-.39-.305-1.11-.613-2.14-.924-1.03-.31-1.85-.656-2.46-1.037-1.16-.732-1.75-1.687-1.75-2.865 0-1.031.42-1.88 1.26-2.549.84-.668 1.94-1.002 3.28-1.002.89 0 1.68.164 2.38.492.7.329 1.25.797 1.64 1.407.4.603.6 1.274.6 2.013h-2.21c0-.669-.21-1.19-.64-1.565-.41-.38-1.01-.571-1.79-.571-.73 0-1.29.155-1.7.466-.39.31-.59.744-.59 1.3 0 .47.21.862.65 1.178.43.31 1.15.615 2.14.914 1 .293 1.8.63 2.4 1.01.6.376 1.05.81 1.33 1.302.28.486.42 1.057.42 1.713 0 1.067-.41 1.916-1.23 2.55-.82.626-1.92.94-3.32.94-.93 0-1.78-.17-2.56-.51-.77-.346-1.38-.82-1.81-1.424-.43-.603-.64-1.306-.64-2.11h2.22c0 .727.24 1.29.72 1.688.48.399 1.17.598 2.07.598.77 0 1.35-.155 1.74-.466.39-.316.59-.732.59-1.248zm14.48-1.459V31h-2.23V18.203h4.9c1.43 0 2.56.372 3.4 1.116.84.745 1.27 1.729 1.27 2.954 0 1.253-.42 2.229-1.24 2.926-.82.697-1.97 1.046-3.46 1.046h-2.64zm0-1.784h2.67c.79 0 1.39-.185 1.81-.554.42-.375.62-.914.62-1.617 0-.691-.21-1.242-.63-1.652-.42-.416-1-.63-1.74-.642h-2.73v4.465zm21.3.475c0 1.253-.22 2.355-.65 3.304-.43.944-1.06 1.67-1.86 2.18-.81.504-1.73.756-2.78.756-1.04 0-1.96-.252-2.78-.756-.81-.51-1.43-1.234-1.88-2.171-.44-.938-.66-2.018-.67-3.243v-.72c0-1.249.22-2.35.66-3.306.45-.955 1.07-1.684 1.87-2.188.81-.51 1.74-.765 2.78-.765 1.04 0 1.97.252 2.77.756.81.498 1.43 1.219 1.87 2.162.44.938.66 2.03.67 3.279v.712zm-2.22-.668c0-1.418-.27-2.505-.81-3.261-.54-.756-1.29-1.134-2.28-1.134-.96 0-1.71.378-2.26 1.134-.54.75-.81 1.813-.82 3.19v.739c0 1.406.27 2.493.81 3.26.55.768 1.32 1.152 2.29 1.152.98 0 1.74-.375 2.27-1.125s.8-1.846.8-3.287v-.668zm13.71 1.02h-5.25v3.937h6.14V31h-8.37V18.203h8.31v1.793h-6.08v3.533h5.25v1.758zm13.92-5.292h-3.99V31h-2.2V19.996h-3.96v-1.793h10.15v1.793zm8.19 6.073h-2.47V31h-2.23V18.203h4.5c1.48 0 2.62.331 3.42.993.8.662 1.21 1.62 1.21 2.874 0 .856-.21 1.574-.63 2.154-.41.574-.98 1.016-1.72 1.327l2.87 5.335V31h-2.38l-2.57-4.93zm-2.47-1.784h2.28c.75 0 1.34-.187 1.76-.562.42-.381.63-.9.63-1.556 0-.686-.19-1.216-.59-1.59-.38-.376-.96-.57-1.74-.58h-2.34v4.288zm14.56-.017l2.94-6.065h2.46l-4.29 8.086V31h-2.23v-4.71l-4.3-8.087h2.47l2.95 6.065z"/><path stroke="#000" stroke-width="2" d="M1008 1h201v46h-201z"/></svg>' ); ?>" />
				</picture>
			</div>

			<div class="about__header-container">
				<div class="about__header-title">
					<p>
						<?php _e( 'WordPress' ); ?>
						<?php echo $display_version; ?>
					</p>
				</div>

				<div class="about__header-text">
					<?php _e( 'Sharing your stories has never been easier' ); ?>
				</div>
				<div class="about__header-badge">
					<img alt="<?php _e( 'New' ); ?>" src="data:image/svg+xml,<?php echo rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 153 153" width="115"><circle cx="76.5" cy="76.5" r="76.5" fill="#000"/><path fill="#D1CFE4" d="M53.333 86h-2.827L39.769 69.564V86H36.94V64.672h2.828L50.535 81.18V64.67h2.798V86zm26.05-9.858h-9.242V83.7h10.737V86h-13.55V64.672h13.403v2.314h-10.59v6.856h9.243v2.3zm18.596 3.134l.41 2.813.6-2.534 4.219-14.883h2.373l4.116 14.883.586 2.578.454-2.871 3.311-14.59h2.827L111.704 86h-2.563l-4.395-15.542-.337-1.626-.337 1.626L99.517 86h-2.564l-5.156-21.328h2.812l3.37 14.604z"/></svg>' ); ?>" />
				</div>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( 'What&#8217;s New' ); ?></a>
				<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
			</nav>
		</div>

		<div class="about__section is-feature">
			<h1 class="aligncenter">
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'Welcome to WordPress %s.' ),
					$display_version
				);
				?>
			</h1>
			<p>
				<?php
				printf(
					/* translators: %s: The current WordPress version number. */
					__( 'WordPress %s brings you countless ways to set your ideas free and bring them to life. With a brand-new default theme as your canvas, it supports an ever-growing collection of blocks as your brushes. Paint with words. Pictures. Sound. Or rich embedded media.' ),
					$display_version
				);
				?>
			</p>
		</div>

		<div class="about__section changelog">
			<div class="column has-border has-subtle-background-color">
				<h2 class="is-smaller-heading"><?php _e( 'Maintenance and Security Releases' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							5
						),
						'5.6.2',
						number_format_i18n( 5 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.6.2' )
						)
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: 1: WordPress version number, 2: Plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							27
						),
						'5.6.1',
						number_format_i18n( 27 )
					);
					?>
					<?php
					printf(
						/* translators: %s: HelpHub URL. */
						__( 'For more information, see <a href="%s">the release notes</a>.' ),
						sprintf(
							/* translators: %s: WordPress version. */
							esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
							sanitize_title( '5.6.1' )
						)
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="has-background-image" style="background-image: url('data:image/svg+xml,<?php echo rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 1035 884"><circle cx="503" cy="434" r="310" fill="#E3DAD1"/><circle cx="831" cy="204" r="204" fill="#D1CFE4"/><circle cx="113.5" cy="770.5" r="113.5" fill="#D1DEE4"/></svg>' ); ?>');">
			<div class="about__section has-2-columns is-wider-left has-transparent-background-color">
				<div class="column">
					<h2><?php _e( 'Greater layout flexibility' ); ?></h2>
					<p><?php _e( 'Bring your stories to life with more tools that let you edit your layout with or without code. Single column blocks, designs using mixed widths and columns, full-width headers, and videos in your cover block—make small changes or big statements with equal ease!' ); ?></p>
				</div>
			</div>
			<div class="about__section has-2-columns is-wider-right has-transparent-background-color">
				<div class="column"><!-- space for alignment. --></div>
				<div class="column">
					<h2><?php _e( 'More block patterns' ); ?></h2>
					<p><?php _e( 'In select themes, preconfigured block patterns make setting up standard pages on your site a breeze. Find the power of patterns to streamline your workflow, or share some of that power with your clients and save yourself a few clicks.' ); ?></p>
				</div>
			</div>
			<div class="about__section has-2-columns is-wider-left has-transparent-background-color">
				<div class="column">
					<h2><?php _e( 'Upload video captions directly in the block editor' ); ?></h2>
					<p><?php _e( 'To help you add subtitles or captions to your videos, you can now upload them within your post or page. This makes it easier than ever to make your videos accessible for anyone who needs or prefers to use subtitles.' ); ?></p>
				</div>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section has-1-column">
			<h2><?php _e( 'Twenty Twenty-One is here!' ); ?></h2>
			<p>
				<?php
				_e( 'Twenty Twenty-One is a blank canvas for your ideas, and the block editor is the best brush. It is built for the block editor and packed with brand-new block patterns you can only get in the default themes. Try different layouts in a matter of seconds, and let the theme’s eye-catching, yet timeless design make your work shine.' );
				?>
			</p>
		</div>

		<hr />

		<div class="about__section">
			<div class="column about__image is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.6/twentytwentyone-layouts.jpg" alt="" />
			</div>
		</div>

		<hr />

		<div class="about__section has-overlap-style">
			<div class="column is-vertically-aligned-center is-top-layer">
				<p>
					<?php
					printf(
						/* translators: 1: WordPress accessibility-ready guidelines link, 2: WCAG information link. */
						__( 'What’s more, this default theme puts accessibility at the heart of your website. It conforms to the <a href="%1$s">WordPress accessibility-ready guidelines</a> and addresses several more specialized standards from the <a href="%2$s">Web Content Accessibility Guidelines (WCAG) 2.1 at level AAA</a>. It will help you meet the highest level of international accessibility standards when you create accessible content and choose plugins which are accessible too!' ),
						'https://make.wordpress.org/themes/handbook/review/accessibility/',
						'https://www.w3.org/WAI/WCAG2AAA-Conformance'
					);
					?>
				</p>
			</div>
			<div class="column about__image aligncenter is-edge-to-edge">
				<img src="data:image/svg+xml,<?php echo rawurlencode( '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 440 291"><circle cx="294.5" cy="145.5" r="145.5" fill="#E5D1D1"/><circle cx="106.5" cy="106.5" r="106.5" fill="#EEEADD"/></svg>' ); ?>" style="max-width:25em" alt="" />
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<div class="column is-vertically-aligned-center">
				<h3 class="is-larger-heading"><?php _e( 'A rainbow of soft pastels' ); ?></h3>
			</div>
			<div class="column">
				<p><?php _e( 'Perfect for a new year, Twenty Twenty-One gives you a range of pre-selected color palettes in pastel, all of which meet AAA standards for contrast. You can also choose your own background color for the theme, and the theme chooses accessibility-conscious text colors for you — automatically!' ); ?></p>
				<p><?php _e( 'Need more flexibility than that? You can also choose your own color palette from the color picker.' ); ?></p>
			</div>
		</div>

		<div class="about__section">
			<div class="column about__image is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.6/twentytwentyone-rainbow.png" alt="" />
			</div>
		</div>

		<hr />
		<hr class="is-large" />

		<div class="about__section">
			<header class="column is-edge-to-edge">
				<h2><?php _e( 'Improvements for everyone' ); ?></h2>
			</header>
		</div>

		<div class="about__section has-3-columns has-gutters">
			<div class="column has-border" style="background-color:#eeeadd;background-color:var(--global--color-yellow)">
				<h3><?php _e( 'Expanding auto-updates' ); ?></h3>
				<p><?php _e( 'For years, only developers have been able to update WordPress automatically. But now, you have that option, right in your dashboard. If this is your first site, you have auto-updates ready to go, right now! Upgrading an existing site? No problem! Everything is the same as it was before.' ); ?></p>
			</div>
			<div class="column has-border" style="background-color:#e4d1d1;background-color:var(--global--color-red)">
				<h3><?php _e( 'Accessibility statement template' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: Accessibility statement feature plugin link. */
						__( 'Even if you’re not an expert, you can start letting folks know about your site’s commitment to accessibility at the click of a button! The new <a href="%s">feature plugin</a> includes template copy for you to update and publish, and it’s written to support different contexts and jurisdictions.' ),
						'https://github.com/10degrees/accessibility-statement-plugin'
					);
					?>
				</p>
			</div>
			<div class="column has-border" style="background-color:#d1d1e4;background-color:var(--global--color-purple)">
				<h3><?php _e( 'Built-in patterns' ); ?></h3>
				<p><?php _e( 'If you’ve not had the chance to play with block patterns yet, all default themes now feature a range of block patterns that let you master complex layouts with minimal effort. Customize the patterns to your liking with the copy, images and colors that fit your story or brand.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="about__section has-2-columns">
			<h2 class="is-section-header"><?php _e( 'For developers' ); ?></h2>
			<div class="column">
				<h3><?php _e( 'REST API authentication with Application Passwords' ); ?></h3>
				<p><?php _e( 'Thanks to the API’s new Application Passwords authorization feature, third-party apps can connect to your site seamlessly and securely. This new REST API feature lets you see what apps are connecting to your site and control what they do.' ); ?></p>
			</div>
			<div class="column">
				<h3><?php _e( 'More PHP 8 support' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress and PHP 8 dev note link. */
						__( '5.6 marks the first steps toward WordPress Core support for PHP 8. Now is a great time to start planning how your WordPress products, services and sites can support the latest PHP version. For more information about what to expect next, <a href="%s">read the PHP 8 developer note</a>.' ),
						'https://make.wordpress.org/core/2020/11/23/wordpress-and-php-8-0/'
					);
					?>
				</p>
			</div>
		</div>
		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'jQuery' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: jQuery update test plugin link. */
						__( 'Updates to jQuery in WordPress take place across three releases: 5.5, 5.6, and 5.7. As we reach the mid-point of this process, run the <a href="%s">update test plugin</a> to check your sites for errors ahead of time.' ),
						current_user_can( 'install_plugins' ) ?
							esc_url( network_admin_url( 'plugin-install.php?tab=search&type=term&s=slug:wp-jquery-update-test' ) ) :
							esc_url( __( 'https://wordpress.org/plugins/wp-jquery-update-test/' ) )
					);
					?>
				</p>
				<p>
					<?php
					printf(
						/* translators: %s: jQuery Migrate plugin link. */
						__( 'If you find issues with the way your site looks (e.g. a slider doesn’t work, a button is stuck — that sort of thing), install the <a href="%s">jQuery Migrate plugin</a>.' ),
						current_user_can( 'install_plugins' ) ?
							esc_url( network_admin_url( 'plugin-install.php?tab=search&type=term&s=slug:enable-jquery-migrate-helper' ) ) :
							esc_url( __( 'https://wordpress.org/plugins/enable-jquery-migrate-helper/' ) )
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-small" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( 'Check the Field Guide for more!' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: WordPress 5.6 Field Guide link. */
						__( 'Check out the latest version of the WordPress Field Guide. It highlights developer notes for each change you may want to be aware of. <a href="%s">WordPress 5.6 Field Guide.</a>' ),
						'https://make.wordpress.org/core/2020/11/20/wordpress-5-6-field-guide/'
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Go to Updates' ) : _e( 'Go to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>
	</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number. */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: Plural number of bugs. */
_n_noop(
	'<strong>Version %1$s</strong> addressed %2$s bug.',
	'<strong>Version %1$s</strong> addressed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. Singular security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.'
);

/* translators: 1: WordPress version number, 2: Plural number of bugs. More than one security issue. */
_n_noop(
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
	'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.'
);

/* translators: %s: Documentation URL. */
__( 'For more information, see <a href="%s">the release notes</a>.' );
