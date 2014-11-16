<div class="wrap">

	<h2><?php esc_html_e( 'Akismet' , 'akismet');?></h2>

	<div class="have-key">

		<?php if ( $stat_totals && isset( $stat_totals['all'] ) && (int) $stat_totals['all']->spam > 0 ) : ?>

			<div class="new-snapshot stats">

				<span style="float:right;margin:10px 15px -5px 0px">
					<a href="<?php echo esc_url( Akismet_Admin::get_page_url( 'stats' ) ); ?>" class=""><?php esc_html_e( 'Summaries' , 'akismet');?></a>
				</span>

				<iframe allowtransparency="true" scrolling="no" frameborder="0" style="width: 100%; height: 215px; overflow: hidden;" src="<?php printf( '//akismet.com/web/1.0/snapshot.php?blog=%s&api_key=%s&height=180&locale=%s', urlencode( get_bloginfo('url') ), Akismet::get_api_key(), get_locale() );?>"></iframe>
				<ul>
					<li>
						<h3><?php esc_html_e( 'Past six months' , 'akismet');?></h3>
						<span><?php echo number_format( $stat_totals['6-months']->spam );?></span>
						<?php esc_html_e( 'Spam blocked' , 'akismet');?>
					</li>
					<li>
						<h3><?php esc_html_e( 'All time' , 'akismet');?></h3>
						<span><?php echo number_format( $stat_totals['all']->spam );?></span>
						<?php esc_html_e( 'Spam blocked' , 'akismet');?>
					</li>
					<li>
						<h3><?php esc_html_e( 'Accuracy' , 'akismet');?></h3>
						<span><?php echo $stat_totals['all']->accuracy; ?>%</span>
						<?php printf(
							esc_html(
								_n( '%s missed spam, %s false positive', '%s missed spam, %s false positives', $stat_totals['all']->false_positives , 'akismet')
							),
							number_format( $stat_totals['all']->missed_spam ),
							number_format( $stat_totals['all']->false_positives )
						); ?>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
		<?php endif;?>

		<?php if ( $akismet_user ):?>

			<div id="wpcom-stats-meta-box-container" class="metabox-holder"><?php
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				?>
				<script type="text/javascript">
				jQuery(document).ready( function($) {
					jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
					if(typeof postboxes !== 'undefined')
						postboxes.add_postbox_toggles( 'plugins_page_akismet-key-config' );
				});
				</script>
				<div class="postbox-container" style="width: 55%;margin-right: 10px;">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div id="referrers" class="postbox ">
							<div class="handlediv" title="Click to toggle"><br></div>
							<h3 class="hndle"><span><?php esc_html_e( 'Settings' , 'akismet');?></span></h3>
							<form name="akismet_conf" id="akismet-conf" action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="POST">
								<div class="inside">
									<table cellspacing="0" class="akismet-settings">
										<tbody>
											<?php if ( !defined( 'WPCOM_API_KEY' ) ):?>
											<tr>
												<th width="10%" align="left" scope="row"><?php esc_html_e('API Key', 'akismet');?></th>
												<td width="5%"/>
												<td align="left">
													<span class="api-key"><input id="key" name="key" type="text" size="15" maxlength="12" value="<?php echo esc_attr( get_option('wordpress_api_key') ); ?>" class="regular-text code <?php echo $akismet_user->status;?>"></span>
												</td>
											</tr>
											<?php endif; ?>
											<tr>
												<th align="left" scope="row"><?php esc_html_e('Comments', 'akismet');?></th>
												<td></td>
												<td align="left">
													<p>
														<label for="akismet_show_user_comments_approved" title="<?php esc_attr_e( 'Show approved comments' , 'akismet'); ?>"><input name="akismet_show_user_comments_approved" id="akismet_show_user_comments_approved" value="1" type="checkbox" <?php checked('1', get_option('akismet_show_user_comments_approved')); ?>> <?php esc_html_e('Show the number of approved comments beside each comment author', 'akismet'); ?></label>
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
													<span class="note"><strong><?php esc_html_e('Note:', 'akismet');?></strong> <?php printf( __( 'Spam in the <a href="%s">spam folder</a> older than 15 days is deleted automatically.' , 'akismet'), admin_url( 'edit-comments.php?comment_status=spam' ) );?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div id="major-publishing-actions">
									<?php if ( !defined( 'WPCOM_API_KEY' ) ):?>
									<div id="delete-action">
										<a class="submitdelete deletion" href="<?php echo esc_url( Akismet_Admin::get_page_url( 'delete_key' ) ); ?>"><?php esc_html_e('Disconnect this account', 'akismet'); ?></a>
									</div>
									<?php endif; ?>
									<?php wp_nonce_field(Akismet_Admin::NONCE) ?>
									<div id="publishing-action">
											<input type="hidden" name="action" value="enter-key">
											<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'akismet');?>">

									</div>
									<div class="clear"></div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="postbox-container" style="width:44%;">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div id="referrers" class="postbox ">
							<div class="handlediv" title="Click to toggle"><br></div>
							<h3 class="hndle"><span><?php esc_html_e( 'Account' , 'akismet');?></span></h3>
							<div class="inside">
								<table cellspacing="0">
									<tbody>
										<tr>
											<th scope="row" align="left"><?php esc_html_e( 'Subscription Type' , 'akismet');?></th>
											<td width="5%"/>
											<td align="left">
												<span><?php echo $akismet_user->account_name; ?></span>
											</td>
										</tr>
										<tr>
											<th scope="row" align="left"><?php esc_html_e( 'Status' , 'akismet');?></th>
											<td width="5%"/>
											<td align="left">
												<span><?php 
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
													endif; ?></span>
											</td>
										</tr>
										<?php if ( $akismet_user->next_billing_date ) : ?>
										<tr>
											<th scope="row" align="left"><?php esc_html_e( 'Next Billing Date' , 'akismet');?></th>
											<td width="5%"/>
											<td align="left">
												<span><?php echo date( 'F j, Y', $akismet_user->next_billing_date ); ?></span>
											</td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
							<div id="major-publishing-actions">
								<div id="publishing-action">
									<?php Akismet::view( 'get', array( 'text' => ( $akismet_user->account_type == 'free-api-key' && $akismet_user->status == 'active' ? __( 'Upgrade' , 'akismet') : __( 'Change' , 'akismet') ), 'redirect' => 'upgrade' ) ); ?>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

		<?php endif;?>

	</div>
</div>