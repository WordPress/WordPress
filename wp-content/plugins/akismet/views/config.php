<?php

//phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.
$kses_allow_link_href = array(
	'a' => array(
		'href' => true,
	),
);
?>
<div id="akismet-plugin-container">
	<div class="akismet-masthead">
		<div class="akismet-masthead__inside-container">
			<?php Akismet::view( 'logo' ); ?>
		</div>
	</div>
	<div class="akismet-lower">
		<?php if ( Akismet::get_api_key() ) { ?>
			<?php Akismet_Admin::display_status(); ?>
		<?php } ?>
		<?php if ( ! empty( $notices ) ) { ?>
			<?php foreach ( $notices as $notice ) { ?>
				<?php Akismet::view( 'notice', array_merge( $notice, array( 'parent_view' => $name ) ) ); ?>
			<?php } ?>
		<?php } ?>

		<?php if ( isset( $stat_totals['all'] ) && isset( $stat_totals['6-months'] ) ) : ?>
			<div class="akismet-card">
				<div class="akismet-section-header">
					<h2 class="akismet-section-header__label">
						<span><?php esc_html_e( 'Statistics', 'akismet' ); ?></span>
					</h2>

					<div class="akismet-section-header__actions">
						<a href="<?php echo esc_url( Akismet_Admin::get_page_url( 'stats' ) ); ?>">
							<?php esc_html_e( 'Detailed stats', 'akismet' ); ?>
						</a>
					</div>
				</div> <!-- close akismet-section-header -->

				<div class="akismet-new-snapshot">
					<?php /* name attribute on iframe is used as a cache-buster here to force Firefox to load the new style charts: https://bugzilla.mozilla.org/show_bug.cgi?id=356558 */ ?>
					<div class="akismet-new-snapshot__chart">
						<iframe id="stats-iframe" allowtransparency="true" scrolling="no" frameborder="0" style="width: 100%; height: 220px; overflow: hidden;" src="<?php echo esc_url( sprintf( 'https://tools.akismet.com/1.0/snapshot.php?blog=%s&token=%s&height=200&locale=%s&is_redecorated=1', rawurlencode( get_option( 'home' ) ), rawurlencode( Akismet::get_access_token() ), get_user_locale() ) ); ?>" name="<?php echo esc_attr( 'snapshot-' . filemtime( __FILE__ ) ); ?>" title="<?php echo esc_attr__( 'Akismet stats', 'akismet' ); ?>"></iframe>
					</div>

					<ul class="akismet-new-snapshot__list">
						<li class="akismet-new-snapshot__item">
							<h3 class="akismet-new-snapshot__header"><?php esc_html_e( 'Past six months', 'akismet' ); ?></h3>
							<span class="akismet-new-snapshot__number"><?php echo number_format( $stat_totals['6-months']->spam ); ?></span>
							<span class="akismet-new-snapshot__text"><?php echo esc_html( _n( 'Spam blocked', 'Spam blocked', $stat_totals['6-months']->spam, 'akismet' ) ); ?></span>
						</li>
						<li class="akismet-new-snapshot__item">
							<h3 class="akismet-new-snapshot__header"><?php esc_html_e( 'All time', 'akismet' ); ?></h3>
							<span class="akismet-new-snapshot__number"><?php echo number_format( $stat_totals['all']->spam ); ?></span>
							<span class="akismet-new-snapshot__text"><?php echo esc_html( _n( 'Spam blocked', 'Spam blocked', $stat_totals['all']->spam, 'akismet' ) ); ?></span>
						</li>
						<li class="akismet-new-snapshot__item">
							<h3 class="akismet-new-snapshot__header"><?php esc_html_e( 'Accuracy', 'akismet' ); ?></h3>
							<span class="akismet-new-snapshot__number"><?php echo floatval( $stat_totals['all']->accuracy ); ?>%</span>
							<span class="akismet-new-snapshot__text">
							<?php
							/* translators: %s: number of spam missed by Akismet */
							echo esc_html( sprintf( _n( '%s missed spam', '%s missed spam', $stat_totals['all']->missed_spam, 'akismet' ), number_format( $stat_totals['all']->missed_spam ) ) ) . ', ';
							/* translators: %s: number of false positive spam flagged by Akismet */
							echo esc_html( sprintf( _n( '%s false positive', '%s false positives', $stat_totals['all']->false_positives, 'akismet' ), number_format( $stat_totals['all']->false_positives ) ) );
							?>
							</span>
						</li>
					</ul>
				</div> <!-- close akismet-new-snapshot -->
			</div> <!-- close akismet-card -->
		<?php endif; ?>

		<?php if ( apply_filters( 'akismet_show_compatible_plugins', true ) ) : ?>
			<?php Akismet::view( 'compatible-plugins' ); ?>
		<?php endif; ?>

		<?php if ( $akismet_user ) : ?>
			<div class="akismet-card">
				<div class="akismet-section-header">
					<h2 class="akismet-section-header__label">
						<span><?php esc_html_e( 'Settings', 'akismet' ); ?></span>
					</h2>
				</div>

				<div class="inside">
					<form action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" autocomplete="off" method="POST" id="akismet-settings-form">

						<div class="akismet-settings">
							<?php if ( ! Akismet::predefined_api_key() ) : ?>
								<div class="akismet-settings__row">
									<h3 class="akismet-settings__row-title">
										<label class="akismet-settings__row-label" for="key"><?php esc_html_e( 'API key', 'akismet' ); ?></label>
									</h3>
									<div class="akismet-settings__row-input">
										<span class="api-key"><input id="key" name="key" type="text" size="15" value="<?php echo esc_attr( get_option( 'wordpress_api_key' ) ); ?>" class="<?php echo esc_attr( 'regular-text code ' . $akismet_user->status ); ?>"></span>
									</div>
								</div>
							<?php endif; ?>

							<?php
							//phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( isset( $_GET['ssl_status'] ) ) :
								?>
								<div class="akismet-settings__row">
									<div class="akismet-settings__row-text">
										<h3 class="akismet-settings__row-title"><?php esc_html_e( 'SSL status', 'akismet' ); ?></h3>
										<div class="akismet-settings__row-description">
											<?php if ( ! wp_http_supports( array( 'ssl' ) ) ) : ?>
												<strong><?php esc_html_e( 'Disabled.', 'akismet' ); ?></strong>
												<?php esc_html_e( 'Your Web server cannot make SSL requests; contact your Web host and ask them to add support for SSL requests.', 'akismet' ); ?>
											<?php else : ?>
												<?php $ssl_disabled = get_option( 'akismet_ssl_disabled' ); ?>

												<?php if ( $ssl_disabled ) : ?>
													<strong><?php esc_html_e( 'Temporarily disabled.', 'akismet' ); ?></strong>
													<?php esc_html_e( 'Akismet encountered a problem with a previous SSL request and disabled it temporarily. It will begin using SSL for requests again shortly.', 'akismet' ); ?>
												<?php else : ?>
													<strong><?php esc_html_e( 'Enabled.', 'akismet' ); ?></strong>
													<?php esc_html_e( 'All systems functional.', 'akismet' ); ?>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>

							<div class="akismet-settings__row">
								<div class="akismet-settings__row-text">
									<h3 class="akismet-settings__row-title"><?php esc_html_e( 'Comments', 'akismet' ); ?></h3>
								</div>
								<div class="akismet-settings__row-input">
									<label class="akismet-settings__row-input-label" for="akismet_show_user_comments_approved">
										<input
										name="akismet_show_user_comments_approved"
										id="akismet_show_user_comments_approved"
										value="1"
										type="checkbox"
										<?php
										// If the option isn't set, or if it's enabled ('1'), or if it was enabled a long time ago ('true'), check the checkbox.
										checked( true, ( in_array( get_option( 'akismet_show_user_comments_approved' ), array( false, '1', 'true' ), true ) ) );
										?>
										/>
										<span class="akismet-settings__row-label-text">
											<?php esc_html_e( 'Show the number of approved comments beside each comment author.', 'akismet' ); ?>
										</span>
									</label>
								</div>
							</div>

							<div class="akismet-settings__row is-radio">
								<div class="akismet-settings__row-text">
									<h3 class="akismet-settings__row-title"><?php esc_html_e( 'Spam filtering', 'akismet' ); ?></h3>
								</div>
								<div class="akismet-settings__row-input">
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e( 'Akismet Anti-spam strictness', 'akismet' ); ?></span>
										</legend>
										<div>
											<label class="akismet-settings__row-input-label" for="akismet_strictness_1">
												<input type="radio" name="akismet_strictness" id="akismet_strictness_1" value="1" <?php checked( '1', get_option( 'akismet_strictness' ) ); ?> />
												<span class="akismet-settings__row-label-text">
													<?php esc_html_e( 'Silently discard the worst and most pervasive spam so I never see it.', 'akismet' ); ?>
												</span>
											</label>
										</div>
										<div>
											<label class="akismet-settings__row-input-label" for="akismet_strictness_0">
												<input type="radio" name="akismet_strictness" id="akismet_strictness_0" value="0" <?php checked( '0', get_option( 'akismet_strictness' ) ); ?> />
												<span class="akismet-settings__row-label-text">
													<?php esc_html_e( 'Always put spam in the Spam folder for review.', 'akismet' ); ?>
												</span>
											</label>
										</div>
									</fieldset>

									<div class="akismet-settings__row-note">
										<strong><?php esc_html_e( 'Note:', 'akismet' ); ?></strong>
										<?php
										$delete_interval = max( 1, intval( apply_filters( 'akismet_delete_comment_interval', 15 ) ) );

										$spam_folder_link = sprintf(
											'<a href="%s">%s</a>',
											esc_url( admin_url( 'edit-comments.php?comment_status=spam' ) ),
											esc_html__( 'spam folder', 'akismet' )
										);

										// The _n() needs to be on one line so the i18n tooling can extract the translator comment.
										/* translators: %1$s: spam folder link, %2$d: delete interval in days */
										$delete_message = _n( 'Spam in the %1$s older than %2$d day is deleted automatically.', 'Spam in the %1$s older than %2$d days is deleted automatically.', $delete_interval, 'akismet' );

										printf(
											wp_kses( $delete_message, $kses_allow_link_href ),
											wp_kses( $spam_folder_link, $kses_allow_link_href ),
											esc_html( $delete_interval )
										);
										?>
									</div>
								</div>
							</div>

							<div class="akismet-settings__row is-radio">
								<div class="akismet-settings__row-text">
									<h3 class="akismet-settings__row-title"><?php esc_html_e( 'Privacy', 'akismet' ); ?></h3>
								</div>
								<div class="akismet-settings__row-input">
									<fieldset>
										<legend class="screen-reader-text">
											<span><?php esc_html_e( 'Akismet privacy notice', 'akismet' ); ?></span>
										</legend>
										<div>
											<label class="akismet-settings__row-input-label" for="akismet_comment_form_privacy_notice_display">
												<input type="radio" name="akismet_comment_form_privacy_notice" id="akismet_comment_form_privacy_notice_display" value="display" <?php checked( 'display', get_option( 'akismet_comment_form_privacy_notice' ) ); ?> />
												<span class="akismet-settings__row-label-text">
													<?php esc_html_e( 'Display a privacy notice under your comment forms.', 'akismet' ); ?>
												</span>
											</label>
										</div>
										<div>
											<label class="akismet-settings__row-input-label" for="akismet_comment_form_privacy_notice_hide">
												<input type="radio" name="akismet_comment_form_privacy_notice" id="akismet_comment_form_privacy_notice_hide" value="hide" <?php echo in_array( get_option( 'akismet_comment_form_privacy_notice' ), array( 'display', 'hide' ), true ) ? checked( 'hide', get_option( 'akismet_comment_form_privacy_notice' ), false ) : 'checked="checked"'; ?> />
												<span class="akismet-settings__row-label-text">
													<?php esc_html_e( 'Do not display privacy notice.', 'akismet' ); ?>
												</span>
											</label>
										</div>
									</fieldset>

									<div class="akismet-settings__row-note">
										<?php esc_html_e( 'To help your site with transparency under privacy laws like the GDPR, Akismet can display a notice to your users under your comment forms.', 'akismet' ); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="akismet-card-actions">
							<?php if ( ! Akismet::predefined_api_key() ) : ?>
								<div id="delete-action" class="akismet-card-actions__secondary-action">
									<a class="submitdelete deletion" href="<?php echo esc_url( Akismet_Admin::get_page_url( 'delete_key' ) ); ?>"><?php esc_html_e( 'Disconnect this account', 'akismet' ); ?></a>
								</div>
							<?php endif; ?>

							<?php wp_nonce_field( Akismet_Admin::NONCE ); ?>

							<div id="publishing-action">
								<input type="hidden" name="action" value="enter-key">
								<input type="submit" name="submit" id="submit" class="akismet-button akismet-could-be-primary" value="<?php esc_attr_e( 'Save changes', 'akismet' ); ?>">
							</div>
						</div>
					</form>
				</div>
			</div>

			<?php if ( ! Akismet::predefined_api_key() ) : ?>
				<div class="akismet-card">
					<div class="akismet-section-header">
						<h2 class="akismet-section-header__label">
							<span><?php esc_html_e( 'Account', 'akismet' ); ?></span>
						</h2>
					</div>

					<div class="inside">
						<table class="akismet-account">
							<tbody>
								<tr>
									<th scope="row"><?php esc_html_e( 'Subscription type', 'akismet' ); ?></th>
									<td>
										<?php echo esc_html( $akismet_user->account_name ); ?>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Status', 'akismet' ); ?></th>
									<td>
										<?php
										if ( 'cancelled' === $akismet_user->status ) :
											esc_html_e( 'Cancelled', 'akismet' );
										elseif ( 'suspended' === $akismet_user->status ) :
											esc_html_e( 'Suspended', 'akismet' );
										elseif ( 'missing' === $akismet_user->status ) :
											esc_html_e( 'Missing', 'akismet' );
										elseif ( 'no-sub' === $akismet_user->status ) :
											esc_html_e( 'No subscription found', 'akismet' );
										else :
											esc_html_e( 'Active', 'akismet' );
										endif;
										?>
									</td>
								</tr>
								<?php if ( $akismet_user->next_billing_date ) : ?>
								<tr>
									<th scope="row"><?php esc_html_e( 'Next billing date', 'akismet' ); ?></th>
									<td>
										<?php echo esc_html( gmdate( 'F j, Y', $akismet_user->next_billing_date ) ); ?>
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<div class="akismet-card-actions">
							<?php if ( $akismet_user->status === 'active' ) : ?>
								<div class="akismet-card-actions__secondary-action">
									<a href="https://akismet.com/account" class="akismet-settings__external-link" aria-label="Account overview on akismet.com"><?php esc_html_e( 'Account overview', 'akismet' ); ?></a>
								</div>
							<?php endif; ?>
							<div id="publishing-action">
								<?php
								Akismet::view(
									'get',
									array(
										'text'     => ( $akismet_user->account_type === 'free-api-key' && $akismet_user->status === 'active' ? __( 'Upgrade', 'akismet' ) : __( 'Change', 'akismet' ) ),
										'redirect' => 'upgrade',
									)
								);
								?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
