<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

use WpMatomo\Admin\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
?>
<h2 style="margin-bottom:16px;"><?php esc_html_e( 'How can we help?', 'matomo' ); ?></h2>

<form method="get" action="https://matomo.org" target="_blank" rel="noreferrer noopener">
	<input type="text" name="s" style="width:300px; margin-right: 8px;" placeholder="<?php esc_attr_e( 'Enter search term', 'matomo' ); ?>">
	<input type="submit" class="button-secondary" value="Search on matomo.org">
</form>
<ul class="matomo-list">
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://matomo.org/docs/"><?php esc_html_e( 'User guides', 'matomo' ); ?></a>
		- <?php esc_html_e( 'Learn how to configure Matomo and how to effectively analyse your data', 'matomo' ); ?>
	</li>
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://matomo.org/faq/wordpress/"><?php esc_html_e( 'Matomo for WordPress FAQs', 'matomo' ); ?></a>
		- <?php esc_html_e( 'Get answers to frequently asked questions', 'matomo' ); ?>
	</li>
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://matomo.org/faq/"><?php esc_html_e( 'General FAQs', 'matomo' ); ?></a>
		- <?php esc_html_e( 'Get answers to frequently asked questions', 'matomo' ); ?>
	</li>
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://forum.matomo.org/"><?php esc_html_e( 'Forums', 'matomo' ); ?></a>
		- <?php esc_html_e( 'Get help directly from the community of Matomo users', 'matomo' ); ?>
	</li>
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://glossary.matomo.org"><?php esc_html_e( 'Glossary', 'matomo' ); ?> </a>
		- <?php esc_html_e( 'Learn about commonly used terms to make the most of Matomo Analytics', 'matomo' ); ?>
	</li>
	<li><a target="_blank" rel="noreferrer noopener"
		   href="https://matomo.org/support-plans/"><?php esc_html_e( 'Support Plans', 'matomo' ); ?></a>
		- <?php esc_html_e( 'Let our experienced team assist you online on how to best utilise Matomo', 'matomo' ); ?>
	</li>
	<?php if ( ! empty( $show_troubleshooting_link ) ) { ?>
		<li><a
					href="<?php echo esc_url( add_query_arg( [ 'tab' => 'troubleshooting' ], menu_page_url( Menu::SLUG_SYSTEM_REPORT, false ) ) ); ?>"><?php esc_html_e( 'Troubleshooting', 'matomo' ); ?></a>
			- <?php esc_html_e( 'Click here if you are having Trouble with Matomo', 'matomo' ); ?>
		</li>
	<?php } ?>
</ul>
