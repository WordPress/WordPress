<div id="akismet-plugin-container">
	<div class="akismet-masthead">
		<div class="akismet-masthead__inside-container">
			<div class="akismet-masthead__logo-container">
				<img class="akismet-masthead__logo" src="<?php echo esc_url( plugins_url( '../_inc/img/logo-full-2x.png', __FILE__ ) ); ?>" alt="Akismet" />
			</div>
		</div>
	</div>
	<div class="akismet-lower">
		<?php if ( Akismet::get_api_key() ) { ?>
			<?php Akismet_Admin::display_status(); ?>
		<?php } ?>
		<?php if ( ! empty( $notices ) ) { ?>
			<?php foreach ( $notices as $notice ) { ?>
				<?php Akismet::view( 'notice', $notice ); ?>
			<?php } ?>
		<?php } ?>
		<?php if ( $stat_totals && isset( $stat_totals['all'] ) && (int) $stat_totals['all']->spam > 0 ) : ?>
			<div class="akismet-card">
				<div class="akismet-section-header">
					<div class="akismet-section-header__label">
						<span><?php esc_html_e( 'Statistics' , 'akismet'); ?></span>
					</div>
					<div class="akismet-section-header__actions">
						<a href="<?php echo esc_url( Akismet_Admin::get_page_url( 'stats' ) ); ?>">
							<?php esc_html_e( 'Detailed Stats' , 'akismet');?>
						</a>
					</div>
				</div>
				
				<div class="akismet-new-snapshot">
					<iframe allowtransparency="true" scrolling="no" frameborder="0" style="width: 100%; height: 220px; overflow: hidden;" src="<?php printf( '//akismet.com/web/1.0/snapshot.php?blog=%s&api_key=%s&height=200&locale=%s', urlencode( get_option( 'home' ) ), Akismet::get_api_key(), get_locale() );?>"></iframe>
					<ul>
						<li>
							<h3><?php esc_html_e( 'Past six months' , 'akismet');?></h3>
							<span><?php echo number_format( $stat_totals['6-months']->spam );?></span>
							<?php echo esc_html( _n( 'Spam blocked', 'Spam blocked', $stat_totals['6-months']->spam, 'akismet' ) ); ?>
						</li>
						<li>
							<h3><?php esc_html_e( 'All time' , 'akismet');?></h3>
							<span><?php echo number_format( $stat_totals['all']->spam );?></span>
							<?php echo esc_html( _n( 'Spam blocked', 'Spam blocked', $stat_totals['all']->spam, 'akismet' ) ); ?>
						</li>
						<li>
							<h3><?php esc_html_e( 'Accuracy' , 'akismet');?></h3>
							<span><?php echo floatval( $stat_totals['all']->accuracy ); ?>%</span>
							<?php printf( _n( '%s missed spam', '%s missed spam', $stat_totals['all']->missed_spam, 'akismet' ), number_format( $stat_totals['all']->missed_spam ) ); ?>
							|
							<?php printf( _n( '%s false positive', '%s false positives', $stat_totals['all']->false_positives, 'akismet' ), number_format( $stat_totals['all']->false_positives ) ); ?>
						</li>
					</ul>
				</div>
			</div>
		<?php endif;?>

		<?php if ( $akismet_user ):?>
			<div class="akismet-card">
				<div class="akismet-section-header">
					<div class="akismet-section-header__label">
						<span><?php esc_html_e( 'Settings' , 'akismet'); ?></span>
					</div>
				</div>

				<div class="inside">
					<form action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="POST">
						<table cellspacing="0" class="akismet-settings">
							<tbody>
								<?php if ( ! Akismet::predefined_api_key() ) { ?>
								<tr>
									<th class="akismet-api-key" width="10%" align="left" scope="row"><?php esc_html_e('API Key', 'akismet');?></th>
									<td width="5%"/>
									<td align="left">
										<span class="api-key"><input id="key" name="key" type="text" size="15" value="<?php echo esc_attr( get_option('wordpress_api_key') ); ?>" class="<?php echo esc_attr( 'regular-text code ' . $akismet_user->status ); ?>"></span>
									</td>
								</tr>
								<?php } ?>
								<?php if ( isset( $_GET['ssl_status'] ) ) { ?>
									<tr>
										<th align="left" scope="row"><?php esc_html_e( 'SSL Status', 'akismet' ); ?></th>
										<td></td>
										<td align="left">
											<p>
												<?php

												if ( ! wp_http_supports( array( 'ssl' ) ) ) {
													?><b><?php esc_html_e( 'Disabled.', 'akismet' ); ?></b> <?php esc_html_e( 'Your Web server cannot make SSL requests; contact your Web host and ask them to add support for SSL requests.', 'akismet' ); ?><?php
												}
												else {
													$ssl_disabled = get_option( 'akismet_ssl_disabled' );

													if ( $ssl_disabled ) {
														?><b><?php esc_html_e( 'Temporarily disabled.', 'akismet' ); ?></b> <?php esc_html_e( 'Akismet encountered a problem with a previous SSL request and disabled it temporarily. It will begin using SSL for requests again shortly.', 'akismet' ); ?><?php
													}
													else {
														?><b><?php esc_html_e( 'Enabled.', 'akismet' ); ?></b> <?php esc_html_e( 'All systems functional.', 'akismet' ); ?><?php
													}
												}

												?>
											</p>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<th align="left" scope="row"><?php esc_html_e('Comments', 'akismet');?></th>
									<td></td>
									<td align="left">
										<p>
											<label for="akismet_show_user_comments_approved" title="<?php esc_attr_e( 'Show approved comments' , 'akismet'); ?>">
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
												<?php esc_html_e( 'Show the number of approved comments beside each comment author', 'akismet' ); ?>
											</label>
										</p>
									</td>
								</tr>
								<tr>
									<th class="strictness" align="left" scope="row"><?php esc_html_e('Strictness', 'akismet'); ?></th>
									<td></td>
									<td align="left">
										<fieldset><legend class="screen-reader-text"><span><?php esc_html_e('Akismet anti-spam strictness', 'akismet'); ?></span></legend>
										<p><label for="akismet_strictness_1"><input type="radio" name="akismet_strictness" id="akismet_strictness_1" value="1" <?php checked('1', get_option('akismet_strictness')); ?> /> <?php esc_html_e('Silently discard the worst and most pervasive spam so I never see it.', 'akismet'); ?></label></p>
										<p><label for="akismet_strictness_0"><input type="radio" name="akismet_strictness" id="akismet_strictness_0" value="0" <?php checked('0', get_option('akismet_strictness')); ?> /> <?php esc_html_e('Always put spam in the Spam folder for review.', 'akismet'); ?></label></p>
										</fieldset>
										<span class="akismet-note"><strong><?php esc_html_e('Note:', 'akismet');?></strong>
										<?php
									
										$delete_interval = max( 1, intval( apply_filters( 'akismet_delete_comment_interval', 15 ) ) );
									
										printf(
											_n(
												'Spam in the <a href="%1$s">spam folder</a> older than 1 day is deleted automatically.',
												'Spam in the <a href="%1$s">spam folder</a> older than %2$d days is deleted automatically.',
												$delete_interval,
												'akismet'
											),
											admin_url( 'edit-comments.php?comment_status=spam' ),
											$delete_interval
										);
									
										?>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="akismet-card-actions">
							<?php if ( ! Akismet::predefined_api_key() ) { ?>
							<div id="delete-action">
								<a class="submitdelete deletion" href="<?php echo esc_url( Akismet_Admin::get_page_url( 'delete_key' ) ); ?>"><?php esc_html_e('Disconnect this account', 'akismet'); ?></a>
							</div>
							<?php } ?>
							<?php wp_nonce_field(Akismet_Admin::NONCE) ?>
							<div id="publishing-action">
								<input type="hidden" name="action" value="enter-key">
								<input type="submit" name="submit" id="submit" class="akismet-button akismet-is-primary" value="<?php esc_attr_e('Save Changes', 'akismet');?>">
							</div>
							<div class="clear"></div>
						</div>
					</form>
				</div>
			</div>
			
			<?php if ( ! Akismet::predefined_api_key() ) { ?>
				<div class="akismet-card">
					<div class="akismet-section-header">
						<div class="akismet-section-header__label">
							<span><?php esc_html_e( 'Account' , 'akismet'); ?></span>
						</div>
					</div>
				
					<div class="inside">
						<table cellspacing="0" border="0" class="akismet-settings">
							<tbody>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Subscription Type' , 'akismet');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php echo esc_html( $akismet_user->account_name ); ?></p>
									</td>
								</tr>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Status' , 'akismet');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php 
											if ( 'cancelled' == $akismet_user->status ) :
												esc_html_e( 'Cancelled', 'akismet' ); 
											elseif ( 'suspended' == $akismet_user->status ) :
												esc_html_e( 'Suspended', 'akismet' );
											elseif ( 'missing' == $akismet_user->status ) :
												esc_html_e( 'Missing', 'akismet' ); 
											elseif ( 'no-sub' == $akismet_user->status ) :
												esc_html_e( 'No Subscription Found', 'akismet' );
											else :
												esc_html_e( 'Active', 'akismet' );  
											endif; ?></p>
									</td>
								</tr>
								<?php if ( $akismet_user->next_billing_date ) : ?>
								<tr>
									<th scope="row" align="left"><?php esc_html_e( 'Next Billing Date' , 'akismet');?></th>
									<td width="5%"/>
									<td align="left">
										<p><?php echo date( 'F j, Y', $akismet_user->next_billing_date ); ?></p>
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<div class="akismet-card-actions">
							<div id="publishing-action">
								<?php Akismet::view( 'get', array( 'text' => ( $akismet_user->account_type == 'free-api-key' && $akismet_user->status == 'active' ? __( 'Upgrade' , 'akismet') : __( 'Change' , 'akismet') ), 'redirect' => 'upgrade' ) ); ?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php endif;?>
	</div>
</div>