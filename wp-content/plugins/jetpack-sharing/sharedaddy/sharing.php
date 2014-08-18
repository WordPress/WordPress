<?php

class Sharing_Admin {
	public function __construct() {
		if ( !defined( 'WP_SHARING_PLUGIN_URL' ) ) {
			define( 'WP_SHARING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			define( 'WP_SHARING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		require_once WP_SHARING_PLUGIN_DIR.'sharing-service.php';

		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'subscription_menu' ) );

		// Insert our CSS and JS
		add_action( 'load-settings_page_sharing', array( &$this, 'sharing_head' ) );

		// Catch AJAX
		add_action( 'wp_ajax_sharing_save_services', array( &$this, 'ajax_save_services' ) );
		add_action( 'wp_ajax_sharing_save_options', array( &$this, 'ajax_save_options' ) );
		add_action( 'wp_ajax_sharing_new_service', array( &$this, 'ajax_new_service' ) );
		add_action( 'wp_ajax_sharing_delete_service', array( &$this, 'ajax_delete_service' ) );
	}

	public function sharing_head() {
		wp_enqueue_script( 'sharing-js', WP_SHARING_PLUGIN_URL.'admin-sharing.js', array( 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-form' ), 2 );
		wp_enqueue_style( 'sharing-admin', WP_SHARING_PLUGIN_URL.'admin-sharing.css', false, JETPACK_SHARING_VERSION ); // E-2
		wp_enqueue_style( 'sharing', WP_SHARING_PLUGIN_URL.'sharing.css', false, JETPACK_SHARING_VERSION ); // E-2
		wp_enqueue_style( 'genericons' );
		wp_enqueue_script( 'sharing-js-fe', WP_SHARING_PLUGIN_URL . 'sharing.js', array( ), 3 );

		add_thickbox();
	}

	public function admin_init() {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'sharing.php' || $_GET['page'] == 'sharing' ) )
			$this->process_requests();
	}

	public function process_requests() {
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'sharing-options' ) ) {
			$sharer = new Sharing_Service();
			$sharer->set_global_options( $_POST );
			do_action( 'sharing_admin_update' );

			wp_safe_redirect( admin_url( 'options-general.php?page=sharing&update=saved' ) );
			die();
		}
	}

	public function subscription_menu( $user ) {
		// if ( !defined( 'IS_WPCOM' ) || !IS_WPCOM ) {
		// 	$active = Jetpack::get_active_modules();
		// 	if ( !in_array( 'publicize', $active ) && !current_user_can( 'manage_options' ) )
		// 		return;
		// } // E-1

		if ( !current_user_can( 'manage_options' ) ) return; // E-1
		add_submenu_page( 'options-general.php', __( 'Sharing Settings', 'jetpack' ), __( 'Sharing', 'jetpack' ), 'publish_posts', 'sharing', array( &$this, 'management_page' ) );
	}

	public function ajax_save_services() {
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'sharing-options' ) && isset( $_POST['hidden'] ) && isset( $_POST['visible'] ) ) {
			$sharer = new Sharing_Service();

			$sharer->set_blog_services( explode( ',', $_POST['visible'] ), explode( ',', $_POST['hidden'] ) );
			die();
		}
	}

	public function ajax_new_service() {
		if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['sharing_name'] ) && isset( $_POST['sharing_url'] ) && isset( $_POST['sharing_icon'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'sharing-new_service' ) ) {
			$sharer = new Sharing_Service();
			if ( $service = $sharer->new_service( stripslashes( $_POST['sharing_name'] ), stripslashes( $_POST['sharing_url'] ), stripslashes( $_POST['sharing_icon'] ) ) ) {
				$this->output_service( $service->get_id(), $service );
				echo '<!--->';
				$service->button_style = 'icon-text';
				$this->output_preview( $service );

				die();
			}
		}

		// Fail
		die( '1' );
	}

	public function ajax_delete_service() {
		if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['service'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'sharing-options_'.$_POST['service'] ) ) {
			$sharer = new Sharing_Service();
			$sharer->delete_service( $_POST['service'] );
		}
	}

	public function ajax_save_options() {
		if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['service'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'sharing-options_'.$_POST['service'] ) ) {
			$sharer = new Sharing_Service();
			$service = $sharer->get_service( $_POST['service'] );

			if ( $service && $service instanceof Sharing_Advanced_Source ) {
				$service->update_options( $_POST );

				$sharer->set_service( $_POST['service'], $service );
			}

			$this->output_service( $service->get_id(), $service, true );
			echo '<!--->';
			$service->button_style = 'icon-text';
			$this->output_preview( $service );
			die();
		}
	}

	public function output_preview( $service ) {
		$klasses = array( 'advanced', 'preview-item' );

		if ( $service->button_style != 'text' || $service->has_custom_button_style() ) {
			$klasses[] = 'preview-'.$service->get_class();
			$klasses[] = 'share-'.$service->get_class();

			if ( $service->get_class() != $service->get_id() )
				$klasses[] = 'preview-'.$service->get_id();
		}

		echo '<li class="'.implode( ' ', $klasses ).'">';
		echo $service->display_preview();
		echo '</li>';
	}

	public function output_service( $id, $service, $show_dropdown = false ) {
?>
	<li class="service advanced share-<?php echo $service->get_class(); ?>" id="<?php echo $service->get_id(); ?>" tabindex="0">
		<span class="options-left"><?php echo esc_html( $service->get_name() ); ?></span>
		<?php if ( 0 === strpos( $service->get_id(), 'custom-' ) || $service->has_advanced_options() ) : ?>
		<span class="close"><a href="#" class="remove">&times;</a></span>
		<form method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
			<input type="hidden" name="action" value="sharing_delete_service" />
			<input type="hidden" name="service" value="<?php echo esc_attr( $id ); ?>" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'sharing-options_'.$id );?>" />
		</form>
		<?php endif; ?>
	</li>
<?php
	}

	public function management_page() {
		$sharer  = new Sharing_Service();
		$enabled = $sharer->get_blog_services();
		$global  = $sharer->get_global_options();

		$shows = array_values( get_post_types( array( 'public' => true ) ) );
		array_unshift( $shows, 'index' );

		if ( false == function_exists( 'mb_stripos' ) ) {
			echo '<div id="message" class="updated fade"><h3>' . __( 'Warning! Multibyte support missing!', 'jetpack' ) . '</h3>';
			echo "<p>" . sprintf( __( 'This plugin will work without it, but multibyte support is used <a href="%s">if available</a>. You may see minor problems with Tweets and other sharing services.', 'jetpack' ), "http://www.php.net/manual/en/mbstring.installation.php" ) . '</p></div>';
		}

		if ( isset( $_GET['update'] ) && $_GET['update'] == 'saved' )
			echo '<div class="updated"><p>'.__( 'Settings have been saved', 'jetpack' ).'</p></div>';

		if( !isset( $global['sharing_label'] ) || '' == $global['sharing_label']  ) {
			$global['sharing_label'] = __( 'Share this:', 'jetpack' );
		}
?>

	<div class="wrap">
	  	<div class="icon32" id="icon-options-general"><br /></div>
	  	<h2><?php _e( 'Sharing Settings', 'jetpack' ); ?></h2>

		<?php do_action( 'pre_admin_screen_sharing' ) ?>

		<?php if ( current_user_can( 'manage_options' ) ) : ?>

		<div class="share_manage_options">
	  	<h3><?php _e( 'Sharing Buttons', 'jetpack' ) ?></h3>
	  	<p><?php _e( 'Add sharing buttons to your blog and allow your visitors to share posts with their friends.', 'jetpack' ) ?></p>

	  	<div id="services-config">
	  		<table id="available-services">
					<tr>
		  			<td class="description">
		  				<h3><?php _e( 'Available Services', 'jetpack' ); ?></h3>
		  				<p><?php _e( "Drag and drop the services you'd like to enable into the box below.", 'jetpack' ); ?></p>
		  				<p><a href="#TB_inline?height=395&amp;width=600&amp;inlineId=new-service" class="thickbox" id="add-a-new-service"><?php _e( 'Add a new service', 'jetpack' ); ?></a></p>
		  			</td>
		  			<td class="services">
		  				<ul class="services-available" style="height: 100px;">
	  						<?php foreach ( $sharer->get_all_services_blog() AS $id => $service ) : ?>
	  							<?php
	  								if ( !isset( $enabled['all'][$id] ) )
											$this->output_service( $id, $service );
									?>
	  						<?php endforeach; ?>
		  				</ul>
						<?php
			  				if ( -1 == get_option( 'blog_public' ) )
								echo '<p><strong>'.__( 'Please note that your services have been restricted because your site is private.', 'jetpack' ).'</strong></p>';
		  				?>
		  				<br class="clearing" />
		  			</td>
					</tr>
	  		</table>

  			<table id="enabled-services">
  				<tr>
  					<td class="description">
						<h3>
							<?php _e( 'Enabled Services', 'jetpack' ); ?>
							<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" width="16" height="16" alt="loading" style="vertical-align: middle; display: none" />
						</h3>
						<p><?php _e( 'Services dragged here will appear individually.', 'jetpack' ); ?></p>
  					</td>
	  				<td class="services" id="share-drop-target">
			  				<h2 id="drag-instructions" <?php if ( count( $enabled['visible'] ) > 0 ) echo ' style="display: none"'; ?>><?php _e( 'Drag and drop available services here.', 'jetpack' ); ?></h2>

								<ul class="services-enabled">
									<?php foreach ( $enabled['visible'] as $id => $service ) : ?>
										<?php $this->output_service( $id, $service, true ); ?>
									<?php endforeach; ?>

									<li class="end-fix"></li>
								</ul>
					</td>
					<td id="hidden-drop-target" class="services">
			  				<p><?php _e( 'Services dragged here will be hidden behind a share button.', 'jetpack' ); ?></p>

			  				<ul class="services-hidden">
									<?php foreach ( $enabled['hidden'] as $id => $service ) : ?>
										<?php $this->output_service( $id, $service, true ); ?>
									<?php endforeach; ?>
									<li class="end-fix"></li>
			  				</ul>
					</td>
				</tr>
			</table>

			<table id="live-preview">
				<tr>
					<td class="description">
						<h3><?php _e( 'Live Preview', 'jetpack' ); ?></h3>
					</td>
					<td class="services">
						<h2<?php if ( count( $enabled['all'] ) > 0 ) echo ' style="display: none"'; ?>><?php _e( 'Sharing is off. Add services above to enable.', 'jetpack' ); ?></h2>
						<div class="sharedaddy sd-sharing-enabled">
							<?php if ( count( $enabled['all'] ) > 0 ) : ?>
							<h3 class="sd-title"><?php echo esc_html( $global['sharing_label'] ); ?></h3>
							<?php endif; ?>
							<div class="sd-content">
								<ul class="preview">
					                <?php foreach ( $enabled['visible'] as $id => $service ) : ?>
										<?php $this->output_preview( $service ); ?>
									<?php endforeach; ?>

									<?php if ( count( $enabled['hidden'] ) > 0 ) : ?>
					                <li class="advanced"><a href="#" class="sharing-anchor sd-button share-more"><span><?php _e( 'More', 'jetpack' ); ?></span></a></li>
					                <?php endif; ?>
					            </ul>

					            <?php if ( count( $enabled['hidden'] ) > 0 ) : ?>
								<div class="sharing-hidden">
									<div class="inner" style="display: none; <?php echo count( $enabled['hidden'] ) == 1 ? 'width:150px;' : ''; ?>">
									<?php if ( count( $enabled['hidden'] ) == 1 ) : ?>
										<ul style="background-image:none;">
									<?php else: ?>
										<ul>
									<?php endif; ?>

									<?php foreach ( $enabled['hidden'] as $id => $service ) {
											$this->output_preview( $service );
										}?>
										</ul>
									</div>
								</div>
								<?php endif; ?>

								<ul class="archive" style="display:none;">
								<?php
									foreach ( $sharer->get_all_services_blog() as $id => $service ) :
										if ( isset( $enabled['visible'][$id] ) )
											$service = $enabled['visible'][$id];
										elseif ( isset( $enabled['hidden'][$id] ) )
											$service = $enabled['hidden'][$id];

										$service->button_style = 'icon-text';   // The archive needs the full text, which is removed in JS later
										$service->smart = false;
										$this->output_preview( $service );
									endforeach; ?>
									<li class="advanced"><a href="#" class="sharing-anchor sd-button share-more"><span><?php _e( 'More', 'jetpack' ); ?></span></a></li>
								</ul>
							</div>
						</div>
						<br class="clearing" />
					</td>
				</tr>
			</table>

				<form method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" id="save-enabled-shares">
					<input type="hidden" name="action" value="sharing_save_services" />
					<input type="hidden" name="visible" value="<?php echo implode( ',', array_keys( $enabled['visible'] ) ); ?>" />
					<input type="hidden" name="hidden" value="<?php echo implode( ',', array_keys( $enabled['hidden'] ) ); ?>" />
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'sharing-options' );?>" />
				</form>
	  	</div>

	  	<form method="post" action="">
	  		<table class="form-table">
	  			<tbody>
	  				<tr valign="top">
	  					<th scope="row"><label><?php _e( 'Button style', 'jetpack' ); ?></label></th>
	  					<td>
	  						<select name="button_style" id="button_style">
	  							<option<?php if ( $global['button_style'] == 'icon-text' ) echo ' selected="selected"';?> value="icon-text"><?php _e( 'Icon + text', 'jetpack' ); ?></option>
	  							<option<?php if ( $global['button_style'] == 'icon' ) echo ' selected="selected"';?> value="icon"><?php _e( 'Icon only', 'jetpack' ); ?></option>
	  							<option<?php if ( $global['button_style'] == 'text' ) echo ' selected="selected"';?> value="text"><?php _e( 'Text only', 'jetpack' ); ?></option>
	  							<option<?php if ( $global['button_style'] == 'official' ) echo ' selected="selected"';?> value="official"><?php _e( 'Official buttons', 'jetpack' ); ?></option>
	  						</select>
	  					</td>
	  				</tr>
	  				<tr valign="top">
	  					<th scope="row"><label><?php _e( 'Sharing label', 'jetpack' ); ?></label></th>
	  					<td>
	  						<input type="text" name="sharing_label" value="<?php echo esc_attr( $global['sharing_label'] ); ?>" />
	  					</td>
	  				</tr>
	  				<tr valign="top">
	  					<th scope="row"><label><?php _e( 'Open links in', 'jetpack' ); ?></label></th>
	  					<td>
	  						<select name="open_links">
	  							<option<?php if ( $global['open_links'] == 'new' ) echo ' selected="selected"';?> value="new"><?php _e( 'New window', 'jetpack' ); ?></option>
	  							<option<?php if ( $global['open_links'] == 'same' ) echo ' selected="selected"';?> value="same"><?php _e( 'Same window', 'jetpack' ); ?></option>
	  						</select>
	  					</td>
	  				</tr>
	  				<?php echo apply_filters( 'sharing_show_buttons_on_row_start', '<tr valign="top">' ); ?>
	  					<th scope="row"><label><?php _e( 'Show buttons on', 'jetpack' ); ?></label></th>
	  					<td>
						<?php
							$br = false;
							foreach ( $shows as $show ) :
								if ( 'index' == $show ) {
									$label = __( 'Front Page, Archive Pages, and Search Results', 'jetpack' );
								} else {
									$post_type_object = get_post_type_object( $show );
									$label = $post_type_object->labels->name;
								}
						?>
							<?php if ( $br ) echo '<br />'; ?><label><input type="checkbox"<?php checked( in_array( $show, $global['show'] ) ); ?> name="show[]" value="<?php echo esc_attr( $show ); ?>" /> <?php echo esc_html( $label ); ?></label>
						<?php	$br = true; endforeach; ?>
	  					</td>
	  				<?php echo apply_filters( 'sharing_show_buttons_on_row_end', '</tr>' ); ?>

	  				<?php do_action( 'sharing_global_options' ); ?>
	  			</tbody>
	  		</table>

		  	<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="<?php _e( 'Save Changes', 'jetpack' ); ?>" />
				</p>

				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'sharing-options' );?>" />
	  	</form>

	  <div id="new-service" style="display: none">
	  	<form method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" id="new-service-form">
	  		<table class="form-table">
	  			<tbody>
	  				<tr valign="top">
	  					<th scope="row" width="100"><label><?php _e( 'Service name', 'jetpack' ); ?></label></th>
	  					<td>
	  						<input type="text" name="sharing_name" id="new_sharing_name" size="40" />
	  					</td>
	  				</tr>
	  				<tr valign="top">
	  					<th scope="row" width="100"><label><?php _e( 'Sharing URL', 'jetpack' ); ?></label></th>
	  					<td>
	  						<input type="text" name="sharing_url" id="new_sharing_url" size="40" />

	  						<p><?php _e( 'You can add the following variables to your service sharing URL:', 'jetpack' ); ?><br/>
	  						<code>%post_title%</code>, <code>%post_url%</code>, <code>%post_full_url%</code>, <code>%post_excerpt%</code>, <code>%post_tags%</code></p>
	  					</td>
	  				</tr>
	  				<tr valign="top">
	  					<th scope="row" width="100"><label><?php _e( 'Icon URL', 'jetpack' ); ?></label></th>
	  					<td>
	  						<input type="text" name="sharing_icon" id="new_sharing_icon" size="40" />
	  						<p><?php _e( 'Enter the URL of a 16x16px icon you want to use for this service.', 'jetpack' ); ?></p>
	  					</td>
	  				</tr>
	  				<tr valign="top" width="100">
	  					<th scope="row"></th>
	  					<td>
								<input type="submit" class="button-primary" value="<?php _e( 'Create Share Button', 'jetpack' ); ?>" />
	  						<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" width="16" height="16" alt="loading" style="vertical-align: middle; display: none" />
	  					</td>
	  				</tr>

	  				<?php do_action( 'sharing_new_service_form' ); ?>
	  			</tbody>
	  		</table>

		<?php do_action( 'post_admin_screen_sharing' ) ?>

				<div class="inerror" style="display: none; margin-top: 15px">
					<p><?php _e( 'An error occurred creating your new sharing service - please check you gave valid details.', 'jetpack' ); ?></p>
				</div>

	  		<input type="hidden" name="action" value="sharing_new_service" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'sharing-new_service' );?>" />
	  	</form>
	   </div>
	   </div>

	   <?php endif; ?>


	</div>

	<script type="text/javascript">
		var sharing_loading_icon = '<?php echo esc_js( admin_url( "/images/loading.gif" ) ); ?>';
		<?php if ( isset( $_GET['create_new_service'] ) && 'true' == $_GET['create_new_service'] ) : ?>
		jQuery(document).ready(function() {
			// Prefill new service box and then open it
			jQuery( '#new_sharing_name' ).val( '<?php echo esc_js( $_GET['name'] ); ?>' );
			jQuery( '#new_sharing_url' ).val( '<?php echo esc_js( $_GET['url'] ); ?>' );
			jQuery( '#new_sharing_icon' ).val( '<?php echo esc_js( $_GET['icon'] ); ?>' );
			jQuery( '#add-a-new-service' ).click();
		});
		<?php endif; ?>
	</script>
<?php
	}
}

function sharing_admin_init() {
	global $sharing_admin;

	$sharing_admin = new Sharing_Admin();
}

add_action( 'init', 'sharing_admin_init' );

/*
Edits by Anas H. Sulaiman:
E-1: disconnect from wordpress.com
E-2: disconnect from jetpack
*/
