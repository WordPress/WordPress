<div class="wrap">

	<div class="wpsite_plugin_wrapper">

		<div class="wpsite_plugin_header">
				<!-- ** UPDATE THE UTM LINK BELOW ** -->
				<div class="announcement">
					<h2><?php _e('Check out the all new', self::$text_domain); ?> <strong><?php _e('WPsite.net', self::$text_domain); ?></strong> <?php _e('for more WordPress resources, plugins, and news.', self::$text_domain); ?></h2>
					<a  class="show-me" href="http://www.wpsite.net/?utm_source=follow-us-badges-plugin&amp;utm_medium=announce&amp;utm_campaign=top"><?php _e('Click Here', self::$text_domain); ?></a>
				</div>

				<header class="headercontent">
					<!-- ** UPDATE THE NAME ** -->
					<h1 class="logo"><?php _e('Follow Us Badges', self::$text_domain); ?></h1>
					<span class="slogan"><?php _e('by', self::$text_domain); ?> <a href="http://www.wpsite.com/?utm_source=topadmin&amp;utm_medium=announce&amp;utm_campaign=top"><?php _e('WPsite.net', self::$text_domain); ?></a></span>

					<!-- ** UPDATE THE 2 LINKS ** -->
					<div class="top-call-to-actions">
						<a class="tweet-about-plugin" href="https://twitter.com/intent/tweet?text=Neat%20and%20simple%20plugin%20for%20WordPress%20users.%20Check%20out%20the%20Follow%20Us%20plugin%20by%20@WPsite%20-%20&amp;url=http%3A%2F%2Fwpsite.net%2Fplugins%2F&amp;via=wpsite"><span></span><?php _e('Tweet About WPsite', self::$text_domain); ?></a>
						<a class="leave-a-review" href="http://wordpress.org/support/view/plugin-reviews/wpsite-follow-us-badges" target="_blank"><span></span> <?php _e('Leave A Review', self::$text_domain); ?></a>
					</div><!-- end .top-call-to-actions -->
				</header>
		</div> <!-- /wpsite_plugin_header -->

		<div id="wpsite_plugin_content">

			<span class="pluginmessage"><?php _e('The settings below will apply to the ', self::$text_domain); ?><a href="widgets.php"><?php _e('widget', self::$text_domain); ?></a><?php _e('.', self::$text_domain); ?></span>

			<div id="wpsite_plugin_settings">

				<form method="post">

					<div id="tabs">
						<ul>
							<li><a href="#wpsite_div_twitter"><span class="wpsite_admin_panel_content_tabs"><?php _e('Twitter', self::$text_domain); ?></span></a></li>
							<li><a href="#wpsite_div_facebook"><span class="wpsite_admin_panel_content_tabs"><?php _e('Facebook',self::$text_domain); ?></span></a></li>
							<li><a href="#wpsite_div_google"><span class="wpsite_admin_panel_content_tabs"><?php _e('Google+',self::$text_domain); ?></span></a></li>
							<li><a href="#wpsite_div_linkedin"><span class="wpsite_admin_panel_content_tabs"><?php _e('LinkedIn',self::$text_domain); ?></span></a></li>
							<li><a href="#wpsite_div_pinterest"><span class="wpsite_admin_panel_content_tabs"><?php _e('Pinterest',self::$text_domain); ?></span></a></li>
							<li><a href="#wpsite_div_order"><span class="wpsite_admin_panel_content_tabs"><?php _e('Order',self::$text_domain); ?></span></a></li>
						</ul>

						<div id="wpsite_div_twitter">

							<h3><?php _e('General', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Active -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Active', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_twitter_active" name="wpsite_follow_us_settings_twitter_active" type="checkbox" <?php echo isset($settings['twitter']['active']) && $settings['twitter']['active'] ? 'checked="checked"' : ''; ?> placeholder="your_username">
											</td>
										</th>
									</tr>

									<!-- User -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Username', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_twitter_user" name="wpsite_follow_us_settings_twitter_user" type="text" value="<?php echo esc_attr($settings['twitter']['user']); ?>"><br/>
												<em><label><?php _e('https://twitter.com/', self::$text_domain); ?></label><strong><label><?php _e('"example"', self::$text_domain); ?></label></strong></em>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Display', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Link Only -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Link Only', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_twitter_args_link" name="wpsite_follow_us_settings_twitter_args_link" type="checkbox" <?php echo isset($settings['twitter']['args']['link']) && $settings['twitter']['args']['link'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Followers Count Display -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Followers Count Display', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_twitter_args_followers_count_display" name="wpsite_follow_us_settings_twitter_args_followers_count_display" type="checkbox" <?php echo isset($settings['twitter']['args']['followers_count_display']) && $settings['twitter']['args']['followers_count_display'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Show Screen Name -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Show Screen Name', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_twitter_args_show_screen_name" name="wpsite_follow_us_settings_twitter_args_show_screen_name" type="checkbox" <?php echo isset($settings['twitter']['args']['show_screen_name']) && $settings['twitter']['args']['show_screen_name'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Alignment -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Alignment', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_twitter_args_alignment" name="wpsite_follow_us_settings_twitter_args_alignment">
													<option value="left" <?php echo isset($settings['twitter']['args']['alignment']) && $settings['twitter']['args']['alignment'] == 'left' ? 'selected' : '' ;?>><?php _e('left', self::$text_domain); ?></option>
													<option value="right" <?php echo isset($settings['twitter']['args']['alignment']) && $settings['twitter']['args']['alignment'] == 'right' ? 'selected' : '' ;?>><?php _e('right', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

									<!-- Width -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Width', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_twitter_args_width" name="wpsite_follow_us_settings_twitter_args_width" type="text" value="<?php echo esc_attr($settings['twitter']['args']['width']); ?>"><br/>
												<em><label><?php _e('Accepts px and % (e.g 100px or 100%)', self::$text_domain); ?></label></em>
											</td>
										</th>
									</tr>

									<!-- Size -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Size', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_twitter_args_size" name="wpsite_follow_us_settings_twitter_args_size">
													<option value="medium" <?php echo isset($settings['twitter']['args']['size']) && $settings['twitter']['args']['size'] == 'medium' ? 'selected' : '' ;?>><?php _e('medium', self::$text_domain); ?></option>
													<option value="large" <?php echo isset($settings['twitter']['args']['size']) && $settings['twitter']['args']['size'] == 'large' ? 'selected' : '' ;?>><?php _e('large', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Advanced', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Language -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Language', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_twitter_args_language" name="wpsite_follow_us_settings_twitter_args_language">
													<?php foreach (self::$twitter_supported_languages as $lang) { ?>
													<option value="<?php echo $lang; ?>" <?php echo isset($settings['twitter']['args']['language']) && $settings['twitter']['args']['language'] == $lang ? 'selected' : '' ;?>><?php _e($lang, self::$text_domain); ?></option>
													<?php } ?>
												</select>
											</td>
										</th>
									</tr>

									<!-- Opt Out -->

									<!--
<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Opt Out', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_twitter_args_opt_out" name="wpsite_follow_us_settings_twitter_args_opt_out" type="checkbox" <?php echo isset($settings['twitter']['args']['opt_out']) && $settings['twitter']['args']['opt_out'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>
-->

								</tbody>
							</table>

							<p><?php _e('Reference:', self::$text_domain); ?> <a href="https://dev.twitter.com/docs/follow-button" target="_blank"><?php _e('Twitter Follow Button API Details', self::$text_domain); ?></a></p>
						</div>

						<div id="wpsite_div_facebook">

							<h3><?php _e('General', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Active -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Active', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_facebook_active" name="wpsite_follow_us_settings_facebook_active" type="checkbox" <?php echo isset($settings['facebook']['active']) && $settings['facebook']['active'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- User -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<?php _e('User ID', self::$text_domain); ?>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_facebook_user" name="wpsite_follow_us_settings_facebook_user" type="text" value="<?php echo esc_attr($settings['facebook']['user']); ?>" ><br/>
												<em><label><?php _e('https://www.facebook.com/', self::$text_domain); ?></label><strong><label><?php _e('"example"', self::$text_domain); ?></label></strong></em><br/>
												<em><label><?php _e('https://www.facebook.com/', self::$text_domain); ?></label><strong><label><?php _e('"pages/example/112233"', self::$text_domain); ?></label></strong></em>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Display', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Link Only -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Link Only', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_facebook_args_link" name="wpsite_follow_us_settings_facebook_args_link" type="checkbox" <?php echo isset($settings['facebook']['args']['link']) && $settings['facebook']['args']['link'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Layout -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Layout', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_facebook_args_layout" name="wpsite_follow_us_settings_facebook_args_layout">
													<option value="standard" <?php echo isset($settings['facebook']['args']['layout']) && $settings['facebook']['args']['layout'] == 'standard' ? 'selected' : '' ;?>><?php _e('standard', self::$text_domain); ?></option>
													<option value="box_count" <?php echo isset($settings['facebook']['args']['layout']) && $settings['facebook']['args']['layout'] == 'box_count' ? 'selected' : '' ;?>><?php _e('box_count', self::$text_domain); ?></option>
													<option value="button_count" <?php echo isset($settings['facebook']['args']['layout']) && $settings['facebook']['args']['layout'] == 'button_count' ? 'selected' : '' ;?>><?php _e('button_count', self::$text_domain); ?></option>
													<option value="button" <?php echo isset($settings['facebook']['args']['layout']) && $settings['facebook']['args']['layout'] == 'button' ? 'selected' : '' ;?>><?php _e('button', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

									<!-- Action Type -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Action Type', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_facebook_args_action_type" name="wpsite_follow_us_settings_facebook_args_action_type">
													<option value="like" <?php echo isset($settings['facebook']['args']['action_type']) && $settings['facebook']['args']['action_type'] == 'like' ? 'selected' : '' ;?>><?php _e('like', self::$text_domain); ?></option>
													<option value="recommend" <?php echo isset($settings['facebook']['args']['action_type']) && $settings['facebook']['args']['action_type'] == 'recommend' ? 'selected' : '' ;?>><?php _e('recommend', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

									<!-- Color Scheme -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Color Scheme', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_facebook_args_colorscheme" name="wpsite_follow_us_settings_facebook_args_colorscheme">
													<option value="light" <?php echo isset($settings['facebook']['args']['colorscheme']) && $settings['facebook']['args']['colorscheme'] == 'light' ? 'selected' : '' ;?>><?php _e('light', self::$text_domain); ?></option>
													<option value="dark" <?php echo isset($settings['facebook']['args']['colorscheme']) && $settings['facebook']['args']['colorscheme'] == 'dark' ? 'selected' : '' ;?>><?php _e('dark', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

									<!-- Show Friends Faces -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Show Friends Faces', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_facebook_args_show_friends_faces" name="wpsite_follow_us_settings_facebook_args_show_friends_faces" type="checkbox" <?php echo isset($settings['facebook']['args']['show_friends_faces']) && $settings['facebook']['args']['show_friends_faces'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Include Share Button -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Include Share Button', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_facebook_args_include_share_button" name="wpsite_follow_us_settings_facebook_args_include_share_button" type="checkbox" <?php echo isset($settings['facebook']['args']['include_share_button']) && $settings['facebook']['args']['include_share_button'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Width -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Width', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_facebook_args_width" name="wpsite_follow_us_settings_facebook_args_width" type="text" value="<?php echo esc_attr($settings['facebook']['args']['width']); ?>"><br/>
												<em><label><?php _e('Accepts px only', self::$text_domain); ?></label></em>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Advanced', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Language -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Language', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_facebook_args_language" name="wpsite_follow_us_settings_facebook_args_language">
													<?php foreach (self::$facebook_supported_languages as $lang) { ?>
													<option value="<?php echo $lang; ?>" <?php echo isset($settings['facebook']['args']['language']) && $settings['facebook']['args']['language'] == $lang ? 'selected' : '' ;?>><?php _e($lang, self::$text_domain); ?></option>
													<?php } ?>
												</select>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<p><?php _e('Reference:', self::$text_domain); ?> <a href="https://developers.facebook.com/docs/plugins/like-button/" target="_blank"><?php _e('Facebook Like Button API Details', self::$text_domain); ?></a></p>
						</div>

						<div id="wpsite_div_google">

							<h3><?php _e('General', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Active -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Active', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_google_active" name="wpsite_follow_us_settings_google_active" type="checkbox" <?php echo isset($settings['google']['active']) && $settings['google']['active'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- User -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('User ID', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_google_user" name="wpsite_follow_us_settings_google_user" type="text" value="<?php echo esc_attr($settings['google']['user']); ?>"><br/>
												<em><label><?php _e('https://plus.google.com/u/0/', self::$text_domain); ?></label><strong><label><?php _e('"112233"', self::$text_domain); ?></label></strong><label><?php _e('/posts', self::$text_domain); ?></label></em><br/>
												<em><label><?php _e('https://plus.google.com/', self::$text_domain); ?></label><strong><label><?php _e('"+112233"', self::$text_domain); ?></label></strong></em>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Display', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Link Only -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Link Only', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_google_args_link" name="wpsite_follow_us_settings_google_args_link" type="checkbox" <?php echo isset($settings['google']['args']['link']) && $settings['google']['args']['link'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

									<!-- Size -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Size', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_google_args_size" name="wpsite_follow_us_settings_google_args_size">
													<option value="15" <?php echo isset($settings['google']['args']['size']) && $settings['google']['args']['size'] == '15' ? 'selected' : '' ;?>><?php _e('small', self::$text_domain); ?></option>
													<option value="20" <?php echo isset($settings['google']['args']['size']) && $settings['google']['args']['size'] == '20' ? 'selected' : '' ;?>><?php _e('medium', self::$text_domain); ?></option>
													<option value="24" <?php echo isset($settings['google']['args']['size']) && $settings['google']['args']['size'] == '24' ? 'selected' : '' ;?>><?php _e('large', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

									<!-- Annotation -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Annotation', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_google_args_annotation" name="wpsite_follow_us_settings_google_args_annotation">
													<option value="bubble" <?php echo isset($settings['google']['args']['annotation']) && $settings['google']['args']['annotation'] == 'bubble' ? 'selected' : '' ;?>><?php _e('Bubble Horizontal', self::$text_domain); ?></option>
													<option value="vertical-bubble" <?php echo isset($settings['google']['args']['annotation']) && $settings['google']['args']['annotation'] == 'vertical-bubble' ? 'selected' : '' ;?>><?php _e('Bubble Vertical', self::$text_domain); ?></option>
													<option value="none" <?php echo isset($settings['google']['args']['annotation']) && $settings['google']['args']['annotation'] == 'none' ? 'selected' : '' ;?>><?php _e('none', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Advanced', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Language -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Language', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_google_args_language" name="wpsite_follow_us_settings_google_args_language">
													<?php foreach (self::$google_supported_languages as $lang) { ?>
													<option value="<?php echo $lang; ?>" <?php echo isset($settings['google']['args']['language']) && $settings['google']['args']['language'] == $lang ? 'selected' : '' ;?>><?php _e($lang, self::$text_domain); ?></option>
													<?php } ?>
												</select><br/>
												<a href="https://developers.google.com/+/web/api/supported-languages" target="_blank"><label><?php _e('Supported Languages', self::$text_domain); ?></label></a>
											</td>
										</th>
									</tr>

									<!-- Asynchronous -->

									<!--
<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Asynchronous', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_google_asynchronous" name="wpsite_follow_us_settings_google_asynchronous" type="checkbox" <?php echo isset($settings['google']['args']['asynchronous']) && $settings['google']['args']['asynchronous'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>
-->

									<!-- Paresd Tags -->

									<!--
<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Paresd Tags', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_google_args_parse_tags" name="wpsite_follow_us_settings_google_args_parse_tags">
													<option value="default" <?php echo isset($settings['google']['args']['parse_tags']) && $settings['google']['args']['parse_tags'] == 'default' ? 'selected' : '' ;?>><?php _e('Default (on load)', self::$text_domain); ?></option>
													<option value="explicit" <?php echo isset($settings['google']['args']['parse_tags']) && $settings['google']['args']['parse_tags'] == 'explicit' ? 'selected' : '' ;?>><?php _e('Explicit', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>
-->
								</tbody>
							</table>

							<p><?php _e('Reference:', self::$text_domain); ?> <a href="https://developers.google.com/+/web/follow/" target="_blank"><?php _e('Google+ Button API Details', self::$text_domain); ?></a></p>
						</div>

						<div id="wpsite_div_linkedin">

							<h3><?php _e('General', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Active -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Active', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_linkedin_active" name="wpsite_follow_us_settings_linkedin_active" type="checkbox" <?php echo isset($settings['linkedin']['active']) && $settings['linkedin']['active'] ? 'checked="checked"' : ''; ?> placeholder="Your ID">
											</td>
										</th>
									</tr>

									<!-- User -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('User ID', self::$text_domain); ?></label><br/>
											<a href="https://developer.linkedin.com/plugins/follow-company" target="_blank"><label><?php _e('Get your ID', self::$text_domain); ?></label></a>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_linkedin_user" name="wpsite_follow_us_settings_linkedin_user" type="text" value="<?php echo esc_attr($settings['linkedin']['user']); ?>"><br/>
												<em><label><?php _e('http://www.linkedin.com/profile/view?id=', self::$text_domain); ?></label><strong><label><?php _e('"112233"', self::$text_domain); ?></label></strong> <?php _e('applies to link only', self::$text_domain); ?></em><br/>
												<em><label><?php _e('http://www.linkedin.com/company/', self::$text_domain); ?></label><strong><label><?php _e('"112233"', self::$text_domain); ?></label></strong></em>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Display', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Link Only -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Link Only', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_linkedin_args_link" name="wpsite_follow_us_settings_linkedin_args_link" type="checkbox" <?php echo isset($settings['linkedin']['args']['link']) && $settings['linkedin']['args']['link'] ? 'checked="checked"' : ''; ?>> <?php _e('for a', self::$text_domain); ?> <select id="wpsite_follow_us_settings_linkedin_args_type" name="wpsite_follow_us_settings_linkedin_args_type">
													<option value="company" <?php echo isset($settings['linkedin']['args']['type']) && $settings['linkedin']['args']['type'] == 'company' ? 'selected' : ''; ?>><?php _e('company', self::$text_domain); ?></option>
													<option value="person" <?php echo isset($settings['linkedin']['args']['type']) && $settings['linkedin']['args']['type'] == 'person' ? 'selected' : ''; ?>><?php _e('person', self::$text_domain); ?></option>
												</select> <?php _e('account', self::$text_domain); ?>
											</td>
										</th>
									</tr>

									<!-- Count Mode -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Count Mode', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_linkedin_args_count_mode" name="wpsite_follow_us_settings_linkedin_args_count_mode">
													<option value="right" <?php echo isset($settings['linkedin']['args']['count_mode']) && $settings['linkedin']['args']['count_mode'] == 'right' ? 'selected' : '' ;?>><?php _e('right', self::$text_domain); ?></option>
													<option value="top" <?php echo isset($settings['linkedin']['args']['count_mode']) && $settings['linkedin']['args']['count_mode'] == 'top' ? 'selected' : '' ;?>><?php _e('top', self::$text_domain); ?></option>
													<option value="none" <?php echo isset($settings['linkedin']['args']['count_mode']) && $settings['linkedin']['args']['count_mode'] == 'none' ? 'selected' : '' ;?>><?php _e('none', self::$text_domain); ?></option>
												</select>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<h3><?php _e('Advanced', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Language -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Language', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<select id="wpsite_follow_us_settings_linkedin_args_language" name="wpsite_follow_us_settings_linkedin_args_language">
													<?php foreach (self::$linkedin_supported_languages as $lang) { ?>
													<option value="<?php echo $lang; ?>" <?php echo isset($settings['linkedin']['args']['language']) && $settings['linkedin']['args']['language'] == $lang ? 'selected' : '' ;?>><?php _e($lang, self::$text_domain); ?></option>
													<?php } ?>
												</select>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<p><?php _e('Reference:', self::$text_domain); ?> <a href="https://developer.linkedin.com/plugins/follow-company" target="_blank"><?php _e('LinkedIn Button API Details', self::$text_domain); ?></a></p>
						</div>

						<div id="wpsite_div_pinterest">

							<h3><?php _e('General', self::$text_domain); ?></h3>

							<table>
								<tbody>

									<!-- Active -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Active', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_pinterest_active" name="wpsite_follow_us_settings_pinterest_active" type="checkbox" <?php echo isset($settings['pinterest']['active']) && $settings['pinterest']['active'] ? 'checked="checked"' : ''; ?> placeholder="Your ID">
											</td>
										</th>
									</tr>

									<!-- User URL -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('User URL', self::$text_domain); ?></label><br/>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_pinterest_user" name="wpsite_follow_us_settings_pinterest_user" type="url" value="<?php echo esc_url($settings['pinterest']['user']); ?>">
											</td>
										</th>
									</tr>

									<!-- Name -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Name', self::$text_domain); ?></label><br/>
											<td class="wpsite_follow_us_admin_table_td">
												<input size="30" id="wpsite_follow_us_settings_pinterest_args_name" name="wpsite_follow_us_settings_pinterest_args_name" type="text" value="<?php echo esc_attr($settings['pinterest']['args']['name']); ?>">
											</td>
										</th>
									</tr>

									<!-- Link Only -->

									<tr>
										<th class="wpsite_follow_us_admin_table_th">
											<label><?php _e('Link Only', self::$text_domain); ?></label>
											<td class="wpsite_follow_us_admin_table_td">
												<input id="wpsite_follow_us_settings_pinterest_args_link" name="wpsite_follow_us_settings_pinterest_args_link" type="checkbox" <?php echo isset($settings['pinterest']['args']['link']) && $settings['pinterest']['args']['link'] ? 'checked="checked"' : ''; ?>>
											</td>
										</th>
									</tr>

								</tbody>
							</table>

							<p><?php _e('Reference:', self::$text_domain); ?> <a href="http://business.pinterest.com/en/widget-builder#do_follow_me_button" target="_blank"><?php _e('Pinterest Button API Details', self::$text_domain); ?></a></p>
						</div>

						<div id="wpsite_div_order">
							<h3><?php _e('Drag & Drop to Order', self::$text_domain); ?></h3>
							<table>
								<tbody>

									<!-- Sortables -->

									<ul id="sortable">

										<?php

										if (!isset($settings['order'])) {
											$settings['order'] = self::$default['order'];
										}

										//$settings['order'][] = 'pinterest';

										foreach ($settings['order'] as $order) { ?>
											<li id="<?php echo $order; ?>" name="<?php echo $order; ?>" class="wpsite_follow_us_sort_item dragable"><?php _e($order, self::$text_domain); ?></li>
										<?php } ?>

									</ul>

								</tbody>
							</table>
						</div>

					</div>

					<?php wp_nonce_field('wpsite_follow_us_admin_settings'); ?>

					<?php submit_button(); ?>

				</form>

			</div>  <!-- /wpsite_plugin_settings -->

			<div id="wpsite_plugin_sidebar">
				<div class="wpsite_feed">
					<h3><?php _e('Must-Read Articles', self::$text_domain); ?></h3>
					<script src="http://feeds.feedburner.com/wpsite?format=sigpro" type="text/javascript" ></script><noscript><p><?php _e('Subscribe to WPsite Feed:', self::$text_domain); ?> <a href="http://feeds.feedburner.com/wpsite"></a><br/><?php _e('Powered by FeedBurner', self::$text_domain); ?></p> </noscript>
				</div>

				<div class="mktg-banner">
					<a target="_blank" href="http://www.wpsite.net/custom-wordpress-development/#utm_source=plugin-config&utm_medium=banner&utm_campaign=custom-development-banner"><img src="<?php echo WPSITE_FOLLOW_US_PLUGIN_URL . '/img/ad-custom-development.png' ?>"></a>
				</div>

				<div class="mktg-banner">
					<a target="_blank" href="http://www.wpsite.net/services/#utm_source=plugin-config&utm_medium=banner&utm_campaign=plugin-request-banner"><img src="<?php echo WPSITE_FOLLOW_US_PLUGIN_URL . '/img/ad-plugin-request.png' ?>"></a>
				</div>

				<div class="mktg-banner">
					<a target="_blank" href="http://www.wpsite.net/themes/#utm_source=plugin-config&utm_medium=banner&utm_campaign=themes-banner"><img src="<?php echo WPSITE_FOLLOW_US_PLUGIN_URL . '/img/ad-themes.png' ?>"></a>
				</div>

<!--
				<div class="mktg-banner">
					<a target="_blank" href="http://www.wpsite.net/services/#utm_source=plugin-config&utm_medium=banner&utm_campaign=need-support-banner"><img src="<?php echo WPSITE_FOLLOW_US_PLUGIN_URL . '/img/ad-need-support.png' ?>"></a>
				</div>
-->

			</div> <!-- wpsite_plugin_sidebar -->

		</div> <!-- /wpsite_plugin_content -->

	</div> 	<!-- /wpsite_plugin_wrapper -->

</div> 	<!-- /wrap -->