<?php
/**
 * Class used for the lite-specific metabox.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Metabox_Snippets_Lite.
 */
class WPCode_Metabox_Snippets_Lite extends WPCode_Metabox_Snippets {

	/**
	 * Override the header tab content to make it specific to this class.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_tab_header( $post ) {
		$this->form_for_scripts(
			__( 'Header', 'insert-headers-and-footers' )
		);
	}

	/**
	 * Override the footer tab content to make it specific to this class.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_tab_footer( $post ) {
		$this->form_for_scripts(
			__( 'Footer', 'insert-headers-and-footers' )
		);
	}

	/**
	 * Override the body tab content to make it specific to this class.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_tab_body( $post ) {
		$this->form_for_scripts(
			__( 'Body', 'insert-headers-and-footers' )
		);
	}

	/**
	 * Override the code tab content to make it specific to this class.
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function output_tab_code( $post ) {
		?>
		<div class="wpcode-blur-area">
			<p>
				<?php esc_html_e( 'Choose the snippets you want to run on this page. Please note: only active snippets will be executed.', 'wpcode-premium' ); ?>
			</p>
			<div class="wpcode-metabox-snippets">
				<div id="wpcode-snippet-chooser">
					<h3>
						<?php esc_html_e( 'Select snippets', 'wpcode-premium' ); ?>
						<button class="wpcode-button-just-icon wpcode-drawer-toggle" id="wpcode-close-drawer">
							<?php wpcode_icon( 'close' ); ?>
						</button>
					</h3>
					<div class="wpcode-snippets-search">
						<input type="text" id="wpcode-search-snippets" class="wpcode-input-text" placeholder="<?php esc_attr_e( 'Search snippets', 'wpcode-premium' ); ?>"/>
						<span class="wpcode-loading-spinner" id="wpcode-chooser-spinner"></span>
					</div>
					<div class="wpcode-chooser-fixed-height">
						<div id="wpcode-choose-snippets"></div>
						<div class="wpcode-choose-actions">
							<button type="button" class="wpcode-button wpcode-button-secondary" id="wpcode-metabox-load-more"><?php esc_html_e( 'Load more snippets', 'wpcode-premium' ); ?></button>
						</div>
					</div>
				</div>
				<div class="wpcode-picked-snippets-area">
					<h3>
						<button class="wpcode-button wpcode-drawer-toggle" id="wpcode-add-snippet-toggle" type="button">
							<?php esc_html_e( '+ Choose Snippet', 'wpcode-premium' ); ?>
						</button>
					</h3>
					<div id="wpcode-picked-snippets">
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Show site currency</h3>
							<label>Page location</label>
							<select>
								<option>Insert After Post</option>
							</select>
						</div>
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Banner Ad</h3>
							<label>Page location</label>
							<select>
								<option>Insert Before Post</option>
							</select>
						</div>
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Subscribe Reminder</h3>
							<label>Page location</label>
							<select>
								<option>Insert Before Content</option>
							</select>
						</div>
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Event Countdown</h3>
							<label>Page location</label>
							<select>
								<option>Insert After Post</option>
							</select>
						</div>
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Banner Ad 2</h3>
							<label>Page location</label>
							<select>
								<option>Insert After Content</option>
							</select>
						</div>
						<div class="wpcode-list-item wpcode-selected-snippet-item wpcode-list-item-has-pill">
							<h3>Free Download Button</h3>
							<label>Page location</label>
							<select>
								<option>Insert After Post</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo WPCode_Admin_Page::get_upsell_box( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			__( 'Page Scripts is a Pro Feature', 'insert-headers-and-footers' ),
			sprintf(
				'<p>%s</p>',
				esc_html__( 'While you can always use global snippets, in the PRO version you can easily add page-specific scripts and snippets directly from the post edit screen.', 'insert-headers-and-footers' )
			),
			array(
				'text' => esc_html__( 'Upgrade to Pro and Unlock Page Scripts', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'post-editor-metabox', 'custom-snippets', 'upgrade-to-pro' ),
			),
			array(
				'text' => esc_html__( 'Learn more about all the features', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'post-editor-metabox', 'custom-snippets', 'features' ),
			)
		);
	}

	/**
	 * Get the markup for a form using a disabled CodeMirror instance (to avoid loading a script that won't be used).
	 *
	 * @param string $label The label for this tab section.
	 *
	 * @return void
	 */
	public function form_for_scripts( $label ) {
		wp_enqueue_style( 'code-editor' );
		?>
		<div class="wpcode-blur-area">
			<p>
				<?php
				printf(
				// Translators: placeholder for the name of the section (header or footer).
					esc_html__( 'Add scripts below to the %s section of this page.', 'insert-headers-and-footers' ),
					esc_html( $label )
				);
				?>
			</p>
			<p>
				<label>
					<input type="checkbox"/>
					<?php
					printf(
					// Translators: placeholder for the name of the section (header or footer).
						esc_html__( 'Disable global %s scripts on this page', 'insert-headers-and-footers' ),
						esc_html( $label )
					);
					?>
				</label>
			</p>
			<div class="wpcode-input-row">
				<label>
					<?php
					printf(
					// Translators: placeholder for the name of the section (header or footer).
						esc_html__( '%s - any device type', 'insert-headers-and-footers' ),
						esc_html( $label )
					);
					?>
				</label>
				<div class="wpcode-smart-tags">
					<button class="wpcode-smart-tags-toggle">
						<?php wpcode_icon( 'tags', 20, 16, '0 0 20 16' ); ?>
						<span class="wpcode-text-default">
					<?php esc_html_e( 'Show Smart Tags', 'insert-headers-and-footers' ); ?>
					</span>
					</button>
				</div>
				<div class="CodeMirror cm-s-default CodeMirror-wrap">
					<div class="CodeMirror-vscrollbar" style="width: 18px; pointer-events: none;">
						<div style="min-width: 1px; height: 0;"></div>
					</div>
					<div class="CodeMirror-hscrollbar" style="height: 18px; pointer-events: none;">
						<div style="height: 100%; min-height: 1px; width: 0;"></div>
					</div>
					<div class="CodeMirror-scrollbar-filler"></div>
					<div class="CodeMirror-gutter-filler"></div>
					<div class="CodeMirror-scroll" tabindex="-1">
						<div class="CodeMirror-sizer" style="margin-left: 50px; margin-bottom: 0; border-right-width: 30px; min-height: 165px; padding-right: 0; padding-bottom: 0;">
							<div style="position: relative; top: 0;">
								<div class="CodeMirror-lines" role="presentation">
									<div role="presentation" style="position: relative; outline: none;">
										<div class="CodeMirror-measure"></div>
										<div class="CodeMirror-measure"></div>
										<div style="position: relative; z-index: 1;"></div>
										<div class="CodeMirror-cursors"></div>
										<div class="" role="presentation" tabindex="0" style="">
											<div class="CodeMirror-activeline" style="position: relative;">
												<div class="CodeMirror-activeline-background CodeMirror-linebackground"></div>
												<div class="CodeMirror-gutter-background CodeMirror-activeline-gutter" style="left: -46px; width: 46px;"></div>
												<div class="CodeMirror-gutter-wrapper CodeMirror-activeline-gutter" style="left: -46px;">
													<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 16px; width: 21px;">
														1
													</div>
												</div>
												<pre class="CodeMirror-line" role="presentation"><span role="presentation" style="padding-right: 0.1px;"><span class="cm-tag cm-bracket CodeMirror-matchingtag">&lt;</span><span class="cm-tag CodeMirror-matchingtag">script</span><span class=" CodeMirror-matchingtag"> </span><span class="cm-attribute CodeMirror-matchingtag">type</span><span class=" CodeMirror-matchingtag">=</span><span class="cm-string CodeMirror-matchingtag">"text/javascript"</span><span class="cm-tag cm-bracket CodeMirror-matchingtag">&gt;</span></span></pre>
											</div>
											<div style="position: relative;">
												<div class="CodeMirror-gutter-wrapper" style="left: -46px;">
													<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 16px; width: 21px;">
														2
													</div>
												</div>
												<pre class="CodeMirror-line" role="presentation"><span role="presentation" style="padding-right: 0.1px;"><span class="cm-tab" role="presentation">    </span><span class="cm-variable">console</span>.<span class="cm-property">log</span>( <span class="cm-string">'header'</span> );</span></pre>
											</div>
											<div style="position: relative;">
												<div class="CodeMirror-gutter-wrapper" style="left: -46px;">
													<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 16px; width: 21px;">
														3
													</div>
												</div>
												<pre class="CodeMirror-line" role="presentation"><span role="presentation" style="padding-right: 0.1px;"><span class="cm-tag cm-bracket CodeMirror-matchingtag">&lt;/</span><span class="cm-tag CodeMirror-matchingtag">script</span><span class="cm-tag cm-bracket CodeMirror-matchingtag">&gt;</span></span></pre>
											</div>
											<div style="position: relative;">
												<div class="CodeMirror-gutter-wrapper" style="left: -46px;">
													<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 16px; width: 21px;">
														4
													</div>
												</div>
												<pre class="CodeMirror-line" role="presentation"><span role="presentation" style="padding-right: 0.1px;"></pre>
											</div>
											<div style="position: relative;">
												<div class="CodeMirror-gutter-wrapper" style="left: -46px;">
													<div class="CodeMirror-linenumber CodeMirror-gutter-elt" style="left: 16px; width: 21px;">
														5
													</div>
												</div>
												<pre class="CodeMirror-line" role="presentation"><span role="presentation" style="padding-right: 0.1px;"></pre>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div style="position: absolute; height: 30px; width: 1px; border-bottom: 0 solid transparent; top: 165px;"></div>
						<div class="CodeMirror-gutters" style="height: 195px;">
							<div class="CodeMirror-gutter CodeMirror-lint-markers"></div>
							<div class="CodeMirror-gutter CodeMirror-linenumbers" style="width: 29px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo WPCode_Admin_Page::get_upsell_box( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			__( 'Page Scripts is a Pro Feature', 'insert-headers-and-footers' ),
			sprintf(
				'<p>%s</p>',
				esc_html__( 'While you can always use global snippets, in the PRO version you can easily add page-specific scripts and snippets directly from the post edit screen.', 'insert-headers-and-footers' )
			),
			array(
				'text' => esc_html__( 'Upgrade to Pro and Unlock Page Scripts', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'post-editor-metabox', 'main-' . sanitize_title( $label ), 'upgrade-to-pro' ),
			),
			array(
				'text' => esc_html__( 'Learn more about all the features', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'post-editor-metabox', 'main-' . sanitize_title( $label ), 'features' ),
			)
		);
	}
}
