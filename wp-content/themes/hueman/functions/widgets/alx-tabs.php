<?php
/*
	AlxTabs Widget
	
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	
	Copyright: (c) 2013 Alexander "Alx" Agnarson - http://alxmedia.se
	
		@package AlxTabs
		@version 1.0
*/

class AlxTabs extends WP_Widget {

/*  Constructor
/* ------------------------------------ */
	function AlxTabs() {
		parent::__construct( false, 'AlxTabs', array('description' => 'List posts, comments, and/or tags with or without tabs.', 'classname' => 'widget_alx_tabs') );;	
	}

/*  Create tabs-nav
/* ------------------------------------ */
	private function _create_tabs($tabs,$count) {
		// Borrowed from Jermaine Maree, thanks mate!
		$titles = array(
			'recent'	=> __('Recent Posts','hueman'),
			'popular'	=> __('Popular Posts','hueman'),
			'comments'	=> __('Recent Comments','hueman'),
			'tags'		=> __('Tags','hueman')
		);
		$icons = array(
			'recent'   => 'fa fa-clock-o',
			'popular'  => 'fa fa-star',
			'comments' => 'fa fa-comments-o',
			'tags'     => 'fa fa-tags'
		);
		$output = sprintf('<ul class="alx-tabs-nav group tab-count-%s">', $count);
		foreach ( $tabs as $tab ) {
			$output .= sprintf('<li class="alx-tab tab-%1$s"><a href="#tab-%2$s" title="%4$s"><i class="%3$s"></i><span>%4$s</span></a></li>',$tab, $tab, $icons[$tab], $titles[$tab]);
		}
		$output .= '</ul>';
		return $output;
	}
	
/*  Widget
/* ------------------------------------ */
	public function widget($args, $instance) {
		extract( $args );
		$instance['title']?NULL:$instance['title']='';
		$title = apply_filters('widget_title',$instance['title']);
		$output = $before_widget."\n";
		if($title)
			$output .= $before_title.$title.$after_title;
		ob_start();
		
/*  Set tabs-nav order & output it
/* ------------------------------------ */
	$tabs = array();
	$count = 0;
	$order = array(
		'recent'	=> $instance['order_recent'],
		'popular'	=> $instance['order_popular'],
		'comments'	=> $instance['order_comments'],
		'tags'		=> $instance['order_tags']
	);
	asort($order);
	foreach ( $order as $key => $value ) {
		if ( $instance[$key.'_enable'] ) {
			$tabs[] = $key;
			$count++;
		}
	}
	if ( $tabs && ($count > 1) ) { $output .= $this->_create_tabs($tabs,$count); }
?>

	<div class="alx-tabs-container">

	
		<?php if($instance['recent_enable']) { // Recent posts enabled? ?>
			
			<?php $recent=new WP_Query(); ?>
			<?php $recent->query('showposts='.$instance["recent_num"].'&cat='.$instance["recent_cat_id"].'&ignore_sticky_posts=1');?>
			
			<ul id="tab-recent" class="alx-tab group <?php if($instance['recent_thumbs']) { echo 'thumbs-enabled'; } ?>">
				<?php while ($recent->have_posts()): $recent->the_post(); ?>
				<li>
					
					<?php if($instance['recent_thumbs']) { // Thumbnails enabled? ?>
					<div class="tab-item-thumbnail">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php if ( has_post_thumbnail() ): ?>
								<?php the_post_thumbnail('thumb-small'); ?>
							<?php else: ?>
								<img src="<?php echo get_template_directory_uri(); ?>/img/thumb-small.png" alt="<?php the_title(); ?>" />
							<?php endif; ?>
							<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-play"></i></span>'; ?>
							<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>'; ?>
							<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-star"></i></span>'; ?>
						</a>
					</div>
					<?php } ?>
					
					<div class="tab-item-inner group">
						<?php if($instance['tabs_category']) { ?><p class="tab-item-category"><?php the_category(' / '); ?></p><?php } ?>
						<p class="tab-item-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></p>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php the_time('j M, Y'); ?></p><?php } ?>
					</div>
					
				</li>
				<?php endwhile; ?>
			</ul><!--/.alx-tab-->

		<?php } ?>


		<?php if($instance['popular_enable']) { // Popular posts enabled? ?>
				
			<?php
				$popular = new WP_Query( array(
					'post_type'				=> array( 'post' ),
					'showposts'				=> $instance['popular_num'],
					'cat'					=> $instance['popular_cat_id'],
					'ignore_sticky_posts'	=> true,
					'orderby'				=> 'comment_count',
					'order'					=> 'dsc',
					'date_query' => array(
						array(
							'after' => $instance['popular_time'],
						),
					),
				) );
			?>
			<ul id="tab-popular" class="alx-tab group <?php if($instance['popular_thumbs']) { echo 'thumbs-enabled'; } ?>">
				
				<?php while ( $popular->have_posts() ): $popular->the_post(); ?>
				<li>
				
					<?php if($instance['popular_thumbs']) { // Thumbnails enabled? ?>
					<div class="tab-item-thumbnail">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php if ( has_post_thumbnail() ): ?>
								<?php the_post_thumbnail('thumb-small'); ?>
							<?php else: ?>
								<img src="<?php echo get_template_directory_uri(); ?>/img/thumb-small.png" alt="<?php the_title(); ?>" />
							<?php endif; ?>
							<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-play"></i></span>'; ?>
							<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-volume-up"></i></span>'; ?>
							<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fa fa-star"></i></span>'; ?>
						</a>
					</div>
					<?php } ?>
					
					<div class="tab-item-inner group">
						<?php if($instance['tabs_category']) { ?><p class="tab-item-category"><?php the_category(' / '); ?></p><?php } ?>
						<p class="tab-item-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></p>
						<?php if($instance['tabs_date']) { ?><p class="tab-item-date"><?php the_time('j M, Y'); ?></p><?php } ?>
					</div>
					
				</li>
				<?php endwhile; ?>
			</ul><!--/.alx-tab-->
			
		<?php } ?>
	

		<?php if($instance['comments_enable']) { // Recent comments enabled? ?>

			<?php $comments = get_comments(array('number'=>$instance["comments_num"],'status'=>'approve','post_status'=>'publish')); ?>
			
			<ul id="tab-comments" class="alx-tab group <?php if($instance['comments_avatars']) { echo 'avatars-enabled'; } ?>">
				<?php foreach ($comments as $comment): ?>
				<li>
					
						<?php if($instance['comments_avatars']) { // Avatars enabled? ?>
						<div class="tab-item-avatar">
							<a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
								<?php echo get_avatar($comment->comment_author_email,$size='96'); ?>
							</a>
						</div>
						<?php } ?>
						
						<div class="tab-item-inner group">
							<?php $str=explode(' ',get_comment_excerpt($comment->comment_ID)); $comment_excerpt=implode(' ',array_slice($str,0,11)); if(count($str) > 11 && substr($comment_excerpt,-1)!='.') $comment_excerpt.='...' ?>					
							<div class="tab-item-name"><?php echo $comment->comment_author; ?> <?php _e('says:','hueman'); ?></div>
							<div class="tab-item-comment"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>"><?php echo $comment_excerpt; ?></a></div>
							
						</div>

				</li>
				<?php endforeach; ?>
			</ul><!--/.alx-tab-->

		<?php } ?>

		<?php if($instance['tags_enable']) { // Tags enabled? ?>

			<ul id="tab-tags" class="alx-tab group">
				<li>
					<?php wp_tag_cloud(); ?>
				</li>
			</ul><!--/.alx-tab-->
				
		<?php } ?>
	</div>

<?php
		$output .= ob_get_clean();
		$output .= $after_widget."\n";
		echo $output;
	}
	
/*  Widget update
/* ------------------------------------ */
	public function update($new,$old) {
		$instance = $old;
		$instance['title'] = strip_tags($new['title']);
		$instance['tabs_category'] = $new['tabs_category']?1:0;
		$instance['tabs_date'] = $new['tabs_date']?1:0;
	// Recent posts
		$instance['recent_enable'] = $new['recent_enable']?1:0;
		$instance['recent_thumbs'] = $new['recent_thumbs']?1:0;
		$instance['recent_cat_id'] = strip_tags($new['recent_cat_id']);
		$instance['recent_num'] = strip_tags($new['recent_num']);
	// Popular posts
		$instance['popular_enable'] = $new['popular_enable']?1:0;
		$instance['popular_thumbs'] = $new['popular_thumbs']?1:0;
		$instance['popular_cat_id'] = strip_tags($new['popular_cat_id']);
		$instance['popular_time'] = strip_tags($new['popular_time']);
		$instance['popular_num'] = strip_tags($new['popular_num']);
	// Recent comments
		$instance['comments_enable'] = $new['comments_enable']?1:0;
		$instance['comments_avatars'] = $new['comments_avatars']?1:0;
		$instance['comments_num'] = strip_tags($new['comments_num']);
	// Tags
		$instance['tags_enable'] = $new['tags_enable']?1:0;
	// Order
		$instance['order_recent'] = strip_tags($new['order_recent']);
		$instance['order_popular'] = strip_tags($new['order_popular']);
		$instance['order_comments'] = strip_tags($new['order_comments']);
		$instance['order_tags'] = strip_tags($new['order_tags']);
		return $instance;
	}

/*  Widget form
/* ------------------------------------ */
	public function form($instance) {
		// Default widget settings
		$defaults = array(
			'title' 			=> '',
			'tabs_category' 	=> 1,
			'tabs_date' 		=> 1,
		// Recent posts
			'recent_enable' 	=> 1,
			'recent_thumbs' 	=> 1,
			'recent_cat_id' 	=> '0',
			'recent_num' 		=> '5',
		// Popular posts
			'popular_enable' 	=> 1,
			'popular_thumbs' 	=> 1,
			'popular_cat_id' 	=> '0',
			'popular_time' 		=> '0',
			'popular_num' 		=> '5',
		// Recent comments
			'comments_enable' 	=> 1,
			'comments_avatars' 	=> 1,
			'comments_num' 		=> '5',
		// Tags
			'tags_enable' 		=> 1,
		// Order
			'order_recent' 		=> '1',
			'order_popular' 	=> '2',
			'order_comments' 	=> '3',
			'order_tags' 		=> '4',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

	<style>
	.widget .widget-inside .alx-options-tabs .postform { width: 100%; }
	.widget .widget-inside .alx-options-tabs p { margin: 3px 0; }
	.widget .widget-inside .alx-options-tabs hr { margin: 20px 0 10px; }
	.widget .widget-inside .alx-options-tabs h4 { margin-bottom: 10px; }
	</style>
	
	<div class="alx-options-tabs">
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
		</p>

		<h4>Recent Posts</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('recent_enable'); ?>" name="<?php echo $this->get_field_name('recent_enable'); ?>" <?php checked( (bool) $instance["recent_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('recent_enable'); ?>">Enable recent posts</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('recent_thumbs'); ?>" name="<?php echo $this->get_field_name('recent_thumbs'); ?>" <?php checked( (bool) $instance["recent_thumbs"], true ); ?>>
			<label for="<?php echo $this->get_field_id('recent_thumbs'); ?>">Show thumbnails</label>
		</p>	
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("recent_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("recent_num"); ?>" name="<?php echo $this->get_field_name("recent_num"); ?>" type="text" value="<?php echo absint($instance["recent_num"]); ?>" size='3' />
		</p>
		<p>
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("recent_cat_id"); ?>">Category:</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("recent_cat_id"), 'selected' => $instance["recent_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>		
		</p>
		
		<hr>
		<h4>Most Popular</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('popular_enable'); ?>" name="<?php echo $this->get_field_name('popular_enable'); ?>" <?php checked( (bool) $instance["popular_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('popular_enable'); ?>">Enable most popular posts</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('popular_thumbs'); ?>" name="<?php echo $this->get_field_name('popular_thumbs'); ?>" <?php checked( (bool) $instance["popular_thumbs"], true ); ?>>
			<label for="<?php echo $this->get_field_id('popular_thumbs'); ?>">Show thumbnails</label>
		</p>	
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("popular_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("popular_num"); ?>" name="<?php echo $this->get_field_name("popular_num"); ?>" type="text" value="<?php echo absint($instance["popular_num"]); ?>" size='3' />
		</p>
		<p>
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("popular_cat_id"); ?>">Category:</label>
			<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("popular_cat_id"), 'selected' => $instance["popular_cat_id"], 'show_option_all' => 'All', 'show_count' => true ) ); ?>		
		</p>
		<p style="padding-top: 0.3em;">
			<label style="width: 100%; display: inline-block;" for="<?php echo $this->get_field_id("popular_time"); ?>">Post with most comments from:</label>
			<select style="width: 100%;" id="<?php echo $this->get_field_id("popular_time"); ?>" name="<?php echo $this->get_field_name("popular_time"); ?>">
			  <option value="0"<?php selected( $instance["popular_time"], "0" ); ?>>All time</option>
			  <option value="1 year ago"<?php selected( $instance["popular_time"], "1 year ago" ); ?>>This year</option>
			  <option value="1 month ago"<?php selected( $instance["popular_time"], "1 month ago" ); ?>>This month</option>
			  <option value="1 week ago"<?php selected( $instance["popular_time"], "1 week ago" ); ?>>This week</option>
			  <option value="1 day ago"<?php selected( $instance["popular_time"], "1 day ago" ); ?>>Past 24 hours</option>
			</select>	
		</p>
		
		<hr>
		<h4>Recent Comments</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('comments_enable'); ?>" name="<?php echo $this->get_field_name('comments_enable'); ?>" <?php checked( (bool) $instance["comments_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_enable'); ?>">Enable recent comments</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('comments_avatars'); ?>" name="<?php echo $this->get_field_name('comments_avatars'); ?>" <?php checked( (bool) $instance["comments_avatars"], true ); ?>>
			<label for="<?php echo $this->get_field_id('comments_avatars'); ?>">Show avatars</label>
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("comments_num"); ?>">Items to show</label>
			<input style="width:20%;" id="<?php echo $this->get_field_id("comments_num"); ?>" name="<?php echo $this->get_field_name("comments_num"); ?>" type="text" value="<?php echo absint($instance["comments_num"]); ?>" size='3' />
		</p>

		<hr>
		<h4>Tags</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('tags_enable'); ?>" name="<?php echo $this->get_field_name('tags_enable'); ?>" <?php checked( (bool) $instance["tags_enable"], true ); ?>>
			<label for="<?php echo $this->get_field_id('tags_enable'); ?>">Enable tags</label>
		</p>
	
		<hr>
		<h4>Tab Order</h4>
		
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_recent"); ?>">Recent posts</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_recent"); ?>" name="<?php echo $this->get_field_name("order_recent"); ?>" value="<?php echo $instance["order_recent"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_popular"); ?>">Most popular</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_popular"); ?>" name="<?php echo $this->get_field_name("order_popular"); ?>" value="<?php echo $instance["order_popular"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_comments"); ?>">Recent comments</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_comments"); ?>" name="<?php echo $this->get_field_name("order_comments"); ?>" value="<?php echo $instance["order_comments"]; ?>" />
		</p>
		<p>
			<label style="width: 55%; display: inline-block;" for="<?php echo $this->get_field_id("order_tags"); ?>">Tags</label>
			<input class="widefat" style="width:20%;" type="text" id="<?php echo $this->get_field_id("order_tags"); ?>" name="<?php echo $this->get_field_name("order_tags"); ?>" value="<?php echo $instance["order_tags"]; ?>" />
		</p>
		
		<hr>
		<h4>Tab Info</h4>
		
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('tabs_category'); ?>" name="<?php echo $this->get_field_name('tabs_category'); ?>" <?php checked( (bool) $instance["tabs_category"], true ); ?>>
			<label for="<?php echo $this->get_field_id('tabs_category'); ?>">Show categories</label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('tabs_date'); ?>" name="<?php echo $this->get_field_name('tabs_date'); ?>" <?php checked( (bool) $instance["tabs_date"], true ); ?>>
			<label for="<?php echo $this->get_field_id('tabs_date'); ?>">Show dates</label>
		</p>
		
		<hr>
		
	</div>
<?php

}

}

/*  Register widget
/* ------------------------------------ */
if ( ! function_exists( 'alx_register_widget_tabs' ) ) {

	function alx_register_widget_tabs() { 
		register_widget( 'AlxTabs' );
	}
	
}
add_action( 'widgets_init', 'alx_register_widget_tabs' );
