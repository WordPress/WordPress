<?php

/**
 * A class to manage URL modifications settings
 *
 * @since 1.8
 */
class PLL_Settings_Url extends PLL_Settings_Module {

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang, array(
			'module'      => 'url',
			'title'       => __( 'URL modifications', 'polylang' ),
			'description' => __( 'Decide how your URLs will look like.', 'polylang' ),
			'configure'   => true,
		) );

		$this->links_model = &$polylang->links_model;
		$this->page_on_front = &$polylang->static_pages->page_on_front;
	}

	/**
	 * Displays the fieldset to choose how the language is set
	 *
	 * @since 1.8
	 */
	protected function force_lang() {?>
		<label>
			<?php
			printf(
				'<input name="force_lang" type="radio" value="0" %s /> %s',
				$this->options['force_lang'] ? '' : 'checked="checked"',
				esc_html__( 'The language is set from content', 'polylang' )
			);
			?>
		</label>
		<p class="description"><?php esc_html_e( 'Posts, pages, categories and tags urls are not modified.', 'polylang' ); ?></p>
		<label>
			<?php
			printf(
				'<input name="force_lang" type="radio" value="1" %s/> %s',
				1 == $this->options['force_lang'] ? 'checked="checked"' : '',
				$this->links_model->using_permalinks ? esc_html__( 'The language is set from the directory name in pretty permalinks', 'polylang' ) : esc_html__( 'The language is set from the code in the URL', 'polylang' )
			);
			?>
		</label>
		<p class="description"><?php echo esc_html__( 'Example:', 'polylang' ) . ' <code>' . esc_html( home_url( $this->links_model->using_permalinks ? 'en/my-post/' : '?lang=en&p=1' ) ) . '</code>'; ?></p>
		<label>
			<?php
			printf(
				'<input name="force_lang" type="radio" value="2" %s %s/> %s',
				$this->links_model->using_permalinks ? '' : 'disabled="disabled"',
				2 == $this->options['force_lang'] ? 'checked="checked"' : '',
				esc_html__( 'The language is set from the subdomain name in pretty permalinks', 'polylang' )
			);
			?>
		</label>
		<p class="description"><?php echo esc_html__( 'Example:', 'polylang' ) . ' <code>' . esc_html( str_replace( array( '://', 'www.' ), array( '://en.', '' ), home_url( 'my-post/' ) ) ) . '</code>'; ?></p>
		<label>
			<?php
			printf(
				'<input name="force_lang" type="radio" value="3" %s %s/> %s',
				$this->links_model->using_permalinks ? '' : 'disabled="disabled"',
				3 == $this->options['force_lang'] ? 'checked="checked"' : '',
				esc_html__( 'The language is set from different domains', 'polylang' )
			);
			?>
		</label>
		<table id="pll-domains-table" class="form-table" <?php echo 3 == $this->options['force_lang'] ? '' : 'style="display: none;"'; ?>>
			<?php
			foreach ( $this->model->get_languages_list() as  $lg ) {
				printf(
					'<tr><td><label for="pll-domain[%1$s]">%2$s</label></td>' .
					'<td><input name="domains[%1$s]" id="pll-domain[%1$s]" type="text" value="%3$s" class="regular-text code" aria-required="true" /></td></tr>',
					esc_attr( $lg->slug ),
					esc_attr( $lg->name ),
					esc_url( isset( $this->options['domains'][ $lg->slug ] ) ? $this->options['domains'][ $lg->slug ] : ( $lg->slug == $this->options['default_lang'] ? $this->links_model->home : '' ) )
				);
			}
			?>
		</table>
		<?php
	}

	/**
	 * Displays the fieldset to choose to hide the default language information in url
	 *
	 * @since 1.8
	 */
	protected function hide_default() {
		?>
		<label>
			<?php
			printf(
				'<input name="hide_default" type="checkbox" value="1" %s /> %s',
				$this->options['hide_default'] ? 'checked="checked"' : '',
				esc_html__( 'Hide URL language information for default language', 'polylang' )
			);
			?>
		</label>
		<?php
	}

	/**
	 * Displays the fieldset to choose to hide /language/ in url
	 *
	 * @since 1.8
	 */
	protected function rewrite() {
		?>
		<label>
			<?php
			printf(
				'<input name="rewrite" type="radio" value="1" %s %s/> %s',
				$this->links_model->using_permalinks ? '' : 'disabled="disabled"',
				$this->options['rewrite'] ? 'checked="checked"' : '',
				esc_html__( 'Remove /language/ in pretty permalinks', 'polylang' )
			);
			?>
		</label>
		<p class="description"><?php echo esc_html__( 'Example:', 'polylang' ) . ' <code>' . esc_html( home_url( 'en/' ) ) . '</code>'; ?></p>
		<label>
			<?php
			printf(
				'<input name="rewrite" type="radio" value="0" %s %s/> %s',
				$this->links_model->using_permalinks ? '' : 'disabled="disabled"',
				$this->options['rewrite'] ? '' : 'checked="checked"',
				esc_html__( 'Keep /language/ in pretty permalinks', 'polylang' )
			);
			?>
		</label>
		<p class="description"><?php echo esc_html__( 'Example:', 'polylang' ) . ' <code>' . esc_html( home_url( 'language/en/' ) ) . '</code>'; ?></p>
		<?php
	}

	/**
	 * Displays the fieldset to choose to redirect the home page to language page
	 *
	 * @since 1.8
	 */
	protected function redirect_lang() {
		?>
		<label>
			<?php
			printf(
				'<input name="redirect_lang" type="checkbox" value="1" %s/> %s',
				$this->options['redirect_lang'] ? 'checked="checked"' : '',
				esc_html__( 'The front page url contains the language code instead of the page name or page id', 'polylang' )
			);
			?>
		</label>
		<p class="description">
			<?php
			// That's nice to display the right home urls but don't forget that the page on front may have no language yet
			$lang = $this->model->post->get_language( $this->page_on_front );
			$lang = $lang ? $lang : $this->model->get_language( $this->options['default_lang'] );
			printf(
				/* translators: %s are urls */
				esc_html__( 'Example: %s instead of %s', 'polylang' ),
				'<code>' . esc_html( $this->links_model->home_url( $lang ) ) . '</code>',
				'<code>' . esc_html( _get_page_link( $this->page_on_front ) ) . '</code>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Displays the settings
	 *
	 * @since 1.8
	 */
	public function form() {
		?>
		<div class="pll-settings-url-col">
			<fieldset class="pll-col-left pll-url" id="pll-force-lang">
				<?php $this->force_lang(); ?>
			</fieldset>
		</div>

		<div class="pll-settings-url-col">
			<fieldset class="pll-col-right pll-url" id="pll-hide-default" <?php echo 3 > $this->options['force_lang'] ? '' : 'style="display: none;"'; ?>>
			<?php $this->hide_default(); ?>
			</fieldset>
			<?php
			if ( $this->links_model->using_permalinks ) {
				?>
				<fieldset class="pll-col-right pll-url" id="pll-rewrite" <?php echo 2 > $this->options['force_lang'] ? '' : 'style="display: none;"'; ?>>
				<?php $this->rewrite(); ?>
				</fieldset>
				<?php
			}

			if ( $this->page_on_front ) {
				?>
				<fieldset class="pll-col-right pll-url" id="pll-redirect-lang" <?php echo 2 > $this->options['force_lang'] ? '' : 'style="display: none;"'; ?>>
				<?php $this->redirect_lang(); ?>
				</fieldset>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Sanitizes the settings before saving
	 *
	 * @since 1.8
	 *
	 * @param array $options
	 */
	protected function update( $options ) {
		foreach ( array( 'force_lang', 'rewrite' ) as $key ) {
			$newoptions[ $key ] = isset( $options[ $key ] ) ? (int) $options[ $key ] : 0;
		}

		if ( 3 == $options['force_lang'] && isset( $options['domains'] ) && is_array( $options['domains'] ) ) {
			foreach ( $options['domains'] as $key => $domain ) {
				if ( empty( $domain ) ) {
					$lang = $this->model->get_language( $key );
					add_settings_error( 'general', 'pll_invalid_domain', esc_html( sprintf(
						/* translators: %s is a native language name */
						__( 'Please enter a valid URL for %s.', 'polylang' ), $lang->name
					) ) );
				}
				else {
					$newoptions['domains'][ $key ] = esc_url_raw( trim( $domain ) );
				}
			}
		}

		foreach ( array( 'hide_default', 'redirect_lang' ) as $key ) {
			$newoptions[ $key ] = isset( $options[ $key ] ) ? 1 : 0;
		}

		if ( 3 == $options['force_lang'] ) {
			if ( ! class_exists( 'PLL_Xdata_Domain', true ) ) {
				$newoptions['browser'] = 0;
			}
			$newoptions['hide_default'] = 0;
		}

		// Check if domains exist
		if ( $newoptions['force_lang'] > 1 ) {
			$this->check_domains( $newoptions );
		}

		return $newoptions; // Take care to return only validated options
	}

	/**
	 * Check if subdomains or domains are accessible
	 *
	 * @since 1.8
	 *
	 * @param array $options new set of options to test
	 */
	protected function check_domains( $options ) {
		$options = array_merge( $this->options, $options );
		$model = new PLL_Model( $options );
		$links_model = $model->get_links_model();
		foreach ( $this->model->get_languages_list() as $lang ) {
			$url = add_query_arg( 'deactivate-polylang', 1, $links_model->home_url( $lang ) );
			// Don't redefine vip_safe_wp_remote_get() as it has not the same signature as wp_remote_get()
			$response = function_exists( 'vip_safe_wp_remote_get' ) ? vip_safe_wp_remote_get( esc_url_raw( $url ) ) : wp_remote_get( esc_url_raw( $url ) );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( 200 != $response_code ) {
				add_settings_error( 'general', 'pll_invalid_domain', esc_html( sprintf(
					/* translators: %s is an url */
					__( 'Polylang was unable to access the URL %s. Please check that the URL is valid.', 'polylang' ), $url
				) ) );
			}
		}
	}
}
