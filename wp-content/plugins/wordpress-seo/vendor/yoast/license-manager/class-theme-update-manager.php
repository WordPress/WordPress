<?php

if( class_exists( 'Yoast_Update_Manager' ) && ! class_exists( "Yoast_Theme_Update_Manager", false ) ) {

	class Yoast_Theme_Update_Manager extends Yoast_Update_Manager {

		/**
		* Constructor
		*
		* @param string $api_url
		* @param string $item_name
		* @param string $license_key
		* @param string $slug
		* @param string $theme_version
		* @param string $author (optional)
		*/
		public function __construct( Yoast_Product $product, $license_key ) {
			
			parent::__construct( $product, $license_key );

			// setup hooks
			$this->setup_hooks();
		}

		/**
		* Get the current theme version
		*
		* @return string The version number
		*/
		private function get_theme_version() {

			// if version was not set, get it from the Theme stylesheet
			if( $this->product->get_version() === '' ) {
				$theme = wp_get_theme( $this->product->get_slug() );
				return $theme->get( 'Version' );
			}

			return $this->product->get_version();
		}

		/**
		* Setup hooks
		*/
		private function setup_hooks() {
			add_filter( 'site_transient_update_themes', array( $this, 'set_theme_update_transient' ) );
			add_action( 'load-themes.php', array( $this, 'load_themes_screen' ) );
		}

		/**
		* Set "updates available" transient
		*/
		public function set_theme_update_transient( $value ) {

			$update_data = $this->get_update_data();

			if( $update_data === false ) {
				return $value;
			}
				
			// add update data to "updates available" array. convert object to array.
			$value->response[ $this->product->get_slug() ] = (array) $update_data;

			return $value;
		}

		/**
		* Add hooks and scripts to the Appearance > Themes screen
		*/
		public function load_themes_screen() {

			$update_data = $this->get_update_data();

			// only do if an update is available
			if( $update_data === false ) {
				return;
			}

			add_thickbox();
			add_action( 'admin_notices', array( $this, 'show_update_details' ) );
		}

		/**
		* Show update link. 
		* Opens Thickbox with Changelog.
		*/
		public function show_update_details() {
			
			$update_data = $this->get_update_data();

			// only show if an update is available
			if( $update_data === false ) {
				return;
			}

			$update_url = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->product->get_slug() ), 'upgrade-theme_' . $this->product->get_slug() );
			$update_onclick = ' onclick="if ( confirm(\'' . esc_js( __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update." ) ) . '\') ) {return true;}return false;"';
			?>
			<div id="update-nag">
				<?php
					printf( 
						__( '<strong>%s version %s</strong> is available. <a href="%s" class="thickbox" title="%s">Check out what\'s new</a> or <a href="%s" %s>update now</a>.' ),
					$this->product->get_item_name(),
						$update_data->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $this->product->get_slug() . '_changelog',
					$this->get_item_name(),
						$update_url,
						$update_onclick
					);
				?>
			</div>
			<div id="<?php echo $this->product->get_slug(); ?>_changelog" style="display: none;">
				<?php echo wpautop( $update_data->sections['changelog'] ); ?>
			</div>	
			<?php
		}

		
		/**
		* Get update data
		*
		* This gets the update data from a transient (12 hours), if set.
		* If not, it will make a remote request and get the update data.
		*
		* @return object $update_data Object containing the update data
		*/
		public function get_update_data() {

			$api_response = $this->get_remote_data();

			if( false === $api_response ) {
				return false;
			}

			$update_data = $api_response;

			// check if a new version is available. 
			if ( version_compare( $this->get_theme_version(), $update_data->new_version, '>=' ) ) {
				return false;
			}


			// an update is available
			return $update_data;
		}


	}

}