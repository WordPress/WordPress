<?php
/**
 * Dashboard Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once('./admin.php');

/** Load WordPress dashboard API */
require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

wp_dashboard_setup();

wp_enqueue_script( 'dashboard' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'media-upload' );
add_thickbox();

$title = __('Dashboard');
$parent_file = 'index.php';

if ( is_user_admin() )
	add_screen_option('layout_columns', array('max' => 4, 'default' => 1) );
else
	add_screen_option('layout_columns', array('max' => 4, 'default' => 2) );


$overview = '<p>' . __( 'Welcome to your WordPress Dashboard! This is the screen you will see when you log in to your site, and gives you access to all the site management features of WordPress. You can get help for any screen by clicking the Help tab in the top bar.' ) . '</p>';

add_screen_option( 'overview', $overview );

// Help tabs

$help_navigation  = '<p>' . __('The left-hand navigation menu provides links to all of the WordPress administration screens, with submenu items displayed on hover. You can minimize this menu to a narrow icon strip by clicking on the Collapse Menu arrow at the bottom.') . '</p>';
$help_navigation .= '<p>' . __('Links in the Toolbar at the top of the screen connect your dashboard and the front end of your site, and provide access to your profile and helpful WordPress information.') . '</p>';

get_current_screen()->add_help_tab( array(
        'id'      => 'help-navigation',
	'title'   => __('Navigation'),
	'content' => $help_navigation,
) );

$help_layout  = '<p>' . __('You can use the following controls to arrange your Dashboard screen to suit your workflow. This is true on most other administration screens as well.') . '</p>';
$help_layout .= '<p>' . __('<strong>Screen Options</strong> - Use the Screen Options tab to choose which Dashboard boxes to show, and how many columns to display.') . '</p>';
$help_layout .= '<p>' . __('<strong>Drag and Drop</strong> - To rearrange the boxes, drag and drop by clicking on the title bar of the selected box and releasing when you see a gray dotted-line rectangle appear in the location you want to place the box.') . '</p>';
$help_layout .= '<p>' . __('<strong>Box Controls</strong> - Click the title bar of the box to expand or collapse it. In addition, some box have configurable content, and will show a &#8220;Configure&#8221; link in the title bar if you hover over it.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'help-layout',
	'title'   => __('Layout'),
	'content' => $help_layout,
) );

$help_content  = '<p>' . __('The boxes on your Dashboard screen are:') . '</p>';
$help_content .= '<p>' . __('<strong>Right Now</strong> - Displays a summary of the content on your site and identifies which theme and version of WordPress you are using.') . '</p>';
$help_content .= '<p>' . __('<strong>Recent Comments</strong> - Shows the most recent comments on your posts (configurable, up to 30) and allows you to moderate them.') . '</p>';
$help_content .= '<p>' . __('<strong>Incoming Links</strong> - Shows links to your site found by Google Blog Search.') . '</p>';
$help_content .= '<p>' . __('<strong>QuickPress</strong> - Allows you to create a new post and either publish it or save it as a draft.') . '</p>';
$help_content .= '<p>' . __('<strong>Recent Drafts</strong> - Displays links to the 5 most recent draft posts you&#8217;ve started.') . '</p>';
$help_content .= '<p>' . __('<strong>WordPress Blog</strong> - Latest news from the official WordPress project.') . '</p>';
$help_content .= '<p>' . __('<strong>Other WordPress News</strong> - Shows the <a href="http://planet.wordpress.org" target="_blank">WordPress Planet</a> feed. You can configure it to show a different feed of your choosing.') . '</p>';
$help_content .= '<p>' . __('<strong>Plugins</strong> - Features the most popular, newest, and recently updated plugins from the WordPress.org Plugin Directory.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'help-content',
	'title'   => __('Content'),
	'content' => $help_content,
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Dashboard_Screen" target="_blank">Documentation on Dashboard</a>' ) . '</p>' .
	'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
);


include (ABSPATH . 'wp-admin/admin-header.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php wp_welcome_panel(); ?>

<div id="dashboard-widgets-wrap">

<?php wp_dashboard(); ?>

<div class="clear"></div>
</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php require(ABSPATH . 'wp-admin/admin-footer.php'); ?>
