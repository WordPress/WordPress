<?php
/**
 * @package WPSEO\Admin
 *
 * @var array $statistics {
 *      An array of statistics to display
 *
 *      @type string $seo_rank The SEO rank that this item represents
 *      @type string $title The title for this statistic
 *      @type string $class The class for the link
 *      @type int $count The amount of posts that meets the statistic requirements
 * }
 */

?>
<p><?php _e( 'Below are your published posts&#8217; SEO scores. Now is as good a time as any to start improving some of your posts!', 'wordpress-seo' ); ?></p>
<table>
	<?php foreach ( $statistics as $statistic ) : ?>
		<tr>
			<th>
				<div class="wpseo-score-icon <?php echo sanitize_html_class( $statistic['seo_rank'] ); ?>"></div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_status=publish&post_type=post&seo_filter=' . $statistic['seo_rank'] ) ); ?>"
				   class="wpseo-glance <?php echo esc_attr( $statistic['class'] ); ?>">
					<?php printf( $statistic['title'], intval( $statistic['count'] ) ); ?>
				</a>
			</th>
			<td class="post-count">
				<?php echo absint( $statistic['count'] ); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
