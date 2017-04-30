<?php 
	if ( $_POST['rps_opt_hidden'] == 'Y' ) {
		
		$width = $_POST['rps_width'];
		if ( is_numeric($width) )
			update_option('rps_width', $width);
		else
			$error[] = __('Please enter width in numbers.', 'rps');
		
		$height = $_POST['rps_height'];
		if ( is_numeric($height) )
			update_option('rps_height', $height);
		else
			$error[] = __('Please enter height in numbers.', 'rps');
		
		$post_per_slide = $_POST['rps_post_per_slide'];
		update_option('rps_post_per_slide', $post_per_slide);
		
		$total_posts = $_POST['rps_total_posts'];
		if ( is_numeric($total_posts) )
			update_option('rps_total_posts', $total_posts);
		else
			$error[] = __('Please enter total posts in numbers.', 'rps');
		
		$slider_content = $_POST['rps_slider_content'];
		update_option('rps_slider_content', $slider_content);
		
		$category_ids = $_POST['rps_category_ids'];
		update_option('rps_category_ids', $category_ids);
		
		$post_include_ids = $_POST['rps_post_include_ids'];
		update_option('rps_post_include_ids', $post_include_ids);
		
		$post_exclude_ids = $_POST['rps_post_exclude_ids'];
		update_option('rps_post_exclude_ids', $post_exclude_ids);
		
		$post_title_color = $_POST['rps_post_title_color'];
		update_option('rps_post_title_color', $post_title_color);
		
		$post_title_bg_color = $_POST['rps_post_title_bg_color'];
		update_option('rps_post_title_bg_color', $post_title_bg_color);
		
		$slider_speed = $_POST['rps_slider_speed'];
		update_option('rps_slider_speed', $slider_speed);
		
		$pagination_style = $_POST['rps_pagination_style'];
		update_option('rps_pagination_style', $pagination_style);
		
		$excerpt_words = $_POST['rps_excerpt_words'];
		update_option('rps_excerpt_words', $excerpt_words);
		
		$keep_excerpt_tags = $_POST['rps_keep_excerpt_tags'];
		update_option('rps_keep_excerpt_tags', $keep_excerpt_tags);
		
		$link_text = $_POST['rps_link_text'];
		update_option('rps_link_text', $link_text);
		
		$show_post_date = $_POST['rps_show_post_date'];
		update_option('rps_show_post_date', $show_post_date);
		
		$post_date_text = $_POST['rps_post_date_text'];
		update_option('rps_post_date_text', $post_date_text);
		
		$post_date_format = $_POST['rps_post_date_format'];
		update_option('rps_post_date_format', $post_date_format);
		
		$custom_css = $_POST['rps_custom_css'];
		update_option('rps_custom_css', $custom_css);
		
		if ( $slider_content== 1 || $slider_content== 3 )
			rps_post_img_thumb();
		?>
		<?php if( empty($error) ){ ?>
		<div class="updated"><p><strong><?php _e('Settings saved.', 'rps'); ?></strong></p></div>
		<?php }else{ ?>
		<div class="error"><p><strong><?php 
			foreach ( $error as $key=>$val ) {
				_e($val, 'rps'); 
				echo "<br/>";
			}
		?></strong></p></div>
		<?php }
	} else {
		$width = get_option('rps_width');
		$height = get_option('rps_height');
		$post_per_slide = get_option('rps_post_per_slide');
		$total_posts = get_option('rps_total_posts');
		$slider_content = get_option('rps_slider_content');
		$category_ids = get_option('rps_category_ids');
		$post_include_ids = get_option('rps_post_include_ids');
		$post_exclude_ids = get_option('rps_post_exclude_ids');
		$post_title_color = get_option('rps_post_title_color');
		$post_title_bg_color = get_option('rps_post_title_bg_color');
		$slider_speed = get_option('rps_slider_speed');
		$pagination_style = get_option('rps_pagination_style');
		$excerpt_words = get_option('rps_excerpt_words');
		$keep_excerpt_tags = get_option('rps_keep_excerpt_tags');
		$link_text = get_option('rps_link_text');
		$show_post_date = get_option('rps_show_post_date');
		$post_date_text = get_option('rps_post_date_text');
		$post_date_format = get_option('rps_post_date_format');
		$custom_css = get_option('rps_custom_css');
	}
?>

<div class="wrap">
<?php echo "<h2>" . __( 'Recent Posts Slider Options', 'rps') . "</h2>"; ?>
<p>
	<?php _e('In this page you can customize the plugin according to your needs. Having any issues', 'rps'); ?>
		<a href="http://rps.eworksphere.com/contact/" target="_blank"><?php _e('contact', 'rps'); ?></a> <?php _e('me1.', 'rps'); ?>
	<br/><?php _e('And feel free to', 'rps'); ?> <a href="http://rps.eworksphere.com/donate/" target="_blank"><?php _e('donate', 'rps'); ?></a> <?php _e('for this plugin', 'rps'); ?> :).
	<br/><br/>
	<a href="http://recent-posts-slider.com/installation-how-to-use/" target="_blank"><?php _e('Check our how to use instructions here.', 'rps'); ?></a>
</p>
<?php
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
	if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
?>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo admin_url('options-general.php').'?page='.$_GET['page']; ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'rps'); ?></a>
	<a href="<?php echo admin_url('options-general.php').'?page='.$_GET['page']; ?>&tab=pro" class="nav-tab <?php echo $active_tab == 'pro' ? 'nav-tab-active' : ''; ?>"><?php _e('Pro', 'rps'); ?></a>
</h2>
<?php if( $_GET['tab'] == "settings" || $_GET['tab']=='' ){ ?>
<form name="rps_form" method="post" action="<?php echo admin_url('options-general.php').'?page='.$_GET['page']; ?>">
	<input type="hidden" name="rps_opt_hidden" value="Y">
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3><?php _e('Slider Options', 'rps'); ?></h3>
				<div class="slide-opt-left wd1">
					<ul>
						<li>
							<label for="width"><?php _e('Width', 'rps'); ?></label>
							<input type="text" name="rps_width" value="<?php echo $width; ?>" size="9" /> <?php _e('px', 'rps'); ?>
							<span><?php _e('Total width of the slider (ex : 200)', 'rps'); ?></span>
						</li>
						<li>
							<label for="no_of_posts_per_slide"><?php _e('No. of post to show per slide', 'rps'); ?></label>
							<select name="rps_post_per_slide">
								<?php for( $i=1; $i<=10; $i++ ){ ?>
									<option value="<?php echo $i; ?>" <?php if($post_per_slide==$i){echo 'selected';} ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</li>
					</ul>
				</div>
				<div class="slide-opt-left wd1">
					<ul>
						<li>
							<label for="height"><?php _e('Height', 'rps'); ?></label>
							<input type="text" name="rps_height" value="<?php echo $height; ?>" size="9" /> <?php _e('px', 'rps'); ?>
							<span><?php _e('Total height of the slider (ex : 150)', 'rps'); ?></span>
						</li>
						<li>
							<label for="slider_speed"><?php _e('Slider Speed', 'rps'); ?></label>
							<input type="text" name="rps_slider_speed" value="<?php echo $slider_speed; ?>" size="9" />
							<span><?php _e('ex : 10 (in seconds)', 'rps'); ?></span>
						</li>
					</ul>
				</div>
				<div class="slide-opt-left">
					<ul>
						<li>
							<label for="total_posts"><?php _e('Total Posts', 'rps'); ?></label>
							<input type="text" name="rps_total_posts" value="<?php echo $total_posts; ?>" size="9" />
							<span><?php _e('No of posts to show in a slider', 'rps'); ?></span>
						</li>
						<li>
							<label for="pagination_style"><?php _e('Pagination Style', 'rps'); ?></label>
							<select name="rps_pagination_style">
								<option value="1" <?php if($pagination_style==1){echo 'selected';} ?>><?php _e('Numbers', 'rps'); ?></option>
								<option value="2" <?php if($pagination_style==2){echo 'selected';} ?>><?php _e('Dots', 'rps'); ?></option>
								<option value="3" <?php if($pagination_style==3){echo 'selected';} ?>><?php _e('No Pagination', 'rps'); ?></option>
							</select>
						</li>
					</ul>
				</div>
				<div class="div-clear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3><?php _e('Slider Content Options', 'rps'); ?></h3>
			<div class="slide-opt-left wd1">
				<ul>
					<li>
						<label for="slider_content"><?php _e('Slider content', 'rps'); ?></label>
						<select name="rps_slider_content">
							<option value="1" <?php if($slider_content==1){echo 'selected';} ?>><?php _e('Show Post Thumbnails', 'rps'); ?></option>
							<option value="2" <?php if($slider_content==2){echo 'selected';} ?>><?php _e('Show Excerpt', 'rps'); ?></option>
							<option value="3" <?php if($slider_content==3){echo 'selected';} ?>><?php _e('Show Both', 'rps'); ?></option>
						</select>
					</li>
					<li>
						<label for="category_id"><?php _e('Category IDs', 'rps'); ?></label>
						<input type="text" name="rps_category_ids" value="<?php echo $category_ids; ?>" size="40" />
						<span><?php _e('ex : 1,2,3,-4 (Use negative id to exclude)', 'rps'); ?></span>
					</li>
					<li>
						<label for="posts_title_color"><?php _e('Posts Title Color', 'rps'); ?></label>
						<input type="text" name="rps_post_title_color" value="<?php echo $post_title_color; ?>" size="40" class="rps-color-picker" data-default-color="#666666" />
						<span><?php _e('ex', 'rps'); ?> : ef4534</span>
					</li>
					<li>
						<label for="excerpt_words"><?php _e('Excerpt Words', 'rps'); ?></label>
						<input type="text" name="rps_excerpt_words" value="<?php echo $excerpt_words; ?>" size="40" />
						<span><?php _e('ex', 'rps'); ?> : 10</span>
						<?php _e("Don't remove tags", 'rps'); ?> &nbsp; <input type="checkbox" name="rps_keep_excerpt_tags" value="yes" <?php if ($keep_excerpt_tags=="yes") { echo 'checked="checked"';}  ?> />
					</li>
				</ul>
			</div>
			<div class="slide-opt-left wd1">
				<ul>
					<li>
						<label for="posts_to_include"><?php _e('Posts to include', 'rps'); ?></label>
						<input type="text" name="rps_post_include_ids" value="<?php echo $post_include_ids; ?>" size="40" />
						<span><?php _e('Seperated by commas', 'rps'); ?></span>
					</li>
					<li>
						<label for="posts_to_exclude"><?php _e('Posts to exclude', 'rps'); ?></label>
						<input type="text" name="rps_post_exclude_ids" value="<?php echo $post_exclude_ids; ?>" size="40" />
						<span><?php _e('Seperated by commas', 'rps'); ?></span>
					</li>
					<li>
						<label for="posts_title_bg_color"><?php _e('Posts Title Backgroud Color', 'rps'); ?></label>
						<input type="text" name="rps_post_title_bg_color" value="<?php echo $post_title_bg_color; ?>" size="40" class="rps-color-picker" />
						<span><?php _e('ex', 'rps'); ?> : ef4534</span>
					</li>
					<li>
						<label for="set_link_text"><?php _e('Set Link Text', 'rps'); ?></label>
						<input type="text" name="rps_link_text" value="<?php echo $link_text; ?>" size="40" />
						<span><?php _e('ex : [more]', 'rps'); ?></span>
					</li>
				</ul>
			</div>
			<div class="slide-opt-left">
				<ul>
					<li class="post_date">
						<label for="show_post_date"><?php _e('Post Date Settings', 'rps'); ?></label>
						<?php _e('Show', 'rps'); ?> &nbsp; <input type="checkbox" name="rps_show_post_date" value="yes" <?php if ($show_post_date=="yes") { echo 'checked="checked"';}  ?> />
						<span></span><?php _e('Text Before Date', 'rps'); ?> <br/><input type="text" name="rps_post_date_text" value="<?php echo $post_date_text; ?>" size="30" />
						<span><?php _e('ex : Posted On', 'rps'); ?></span>
						<?php _e('Date Format', 'rps'); ?> <br/><input type="text" name="rps_post_date_format" value="<?php echo $post_date_format; ?>" size="30" />
						<span><?php _e('ex. F j, Y', 'rps'); ?><br/><?php _e('(F = Month name | j = Day of the month', 'rps'); ?> <br/> <?php _e('S = ordinal suffix for the day of the month | Y = Year)', 'rps'); ?></span>
					</li>
				</ul>
			</div>
			<div class="div-clear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3><?php _e('Custom CSS', 'rps'); ?></h3>	
			<div class="div-left wd2 space">
				<label><?php _e('Modify the css to suite your needs.', 'rps'); ?></label><br/><br/>
				<textarea name="rps_custom_css" rows="15" cols="70" /><?php echo stripslashes($custom_css); ?></textarea>
			</div>
			<div class="div-left wd2 space">
				<br/><br/><?php _e('Ex.', 'rps'); ?> <br/>
				<br/>
				<?php _e('To change the color of post date.', 'rps'); ?><br/><br/>
				#rps .post-date{<br/><br/>
				&nbsp;&nbsp;&nbsp;color:#A92D20;<br/><br/>
				}
				
			</div>
			<div class="div-clear"></div>
		</div>
	</div>
	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes','rps') ?>" />
</form>
<?php  }else{ ?>
<br/>
<p class="pro_text">Premium version is available at just <span class="pro_price">$9</span> <a href="http://recent-posts-slider.com/rps-store/" target="_blank">(Buy via PayPal or CreditCard)</a></p>
<p class="pro_text">Check theme 1's preview <a href="http://www.recent-posts-slider.com/rps-premium-preview-theme-1/" target="_blank">here</a> & default <a href="http://www.recent-posts-slider.com/rps-premium-preview/" target="_blank">here</a> & try it by yourself <a href="http://www.recent-posts-slider.com/rps-pro-demo/wp-login.php" target="_blank">here</a> Username : demo - Password : rpsdemo</p>
<p class="pro_text">Having features as listed below:</p>
<table class="widefat rps_pro_table">
	<tr>
		<th width="60%"></th>
		<th width="20%">Lite</th>
		<th width="20%">Pro</th>
	</tr>
	<tr>
		<td class="left">Showcase your Recent Posts using slider</td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Customization Options</td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?><br/><span class="normal">(With few more)</span></td>
	</tr>
	<tr>
		<td class="left">Ready to use customized layouts</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?><br/>
			<span class="normal">
				<strong><a target="_blank" href="http://www.recent-posts-slider.com/rps-premium-preview-theme-1">(Theme 1's Preview)</a><br/>
				<a target="_blank" href="http://www.recent-posts-slider.com/rps-premium-preview">(Default Theme's Preview )</a></strong>
			</span>
		</td>
	</tr>
	<tr>
		<td class="left">Sliding Effects (Fade & Slide)</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Thumb of External Images</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Responsive</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Multiple slider on a same page of same width & height with different category & posts setting</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Random Posts</td>
		<td><?php echo '<img src="' . plugins_url( 'img/no.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
	<tr>
		<td class="left">Support</td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?><br/><span class="normal">(On Priority)</span></td>
	</tr>
	<tr>
		<td class="left">Free Updates</td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
		<td><?php echo '<img src="' . plugins_url( 'img/yes.png' , __FILE__ ) . '" > '; ?></td>
	</tr>
</table>
<?php } ?>
</div>