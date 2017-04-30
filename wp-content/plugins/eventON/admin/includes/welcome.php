<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major).
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class EVO_Welcome_Page {


	public function __construct() {
		
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Add admin menus/screens
	 */
	public function admin_menus() {

		$welcome_page_title = __( 'Welcome to EventON', 'eventon' );

		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'evo-about', array( $this, 'about_screen' ) );

		
		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );
	}

	/**
	 * admin_css function.
	 */
	public function admin_css() {
		wp_enqueue_style( 'eventon-activation', AJDE_EVCAL_URL.'/assets/css/activation.css' );
	}
	
	/**
	 * Add styles just for this page, and remove dashboard page links.
	 */
	public function admin_head() {
		global $eventon;

		remove_submenu_page( 'index.php', 'evo-about' );
		
		?>
		<style type="text/css">
			
		</style>
		<?php
	}
	
	/**
	 * Into text/links shown on all about pages.
	 */
	private function intro() {
		global $eventon;
		

		// Drop minor version if 0
		//$major_version = substr( $eventon->version, 0, 3 );
		
	?>
		
		<div id='eventon_welcome_header'>
			
			<p class='logo'><img src='<?php echo AJDE_EVCAL_URL?>/assets/images/welcome/welcome_screen_logo.jpg'/><span>WordPress Event Calendar</span></p>
			
			
		</div>
		

		<p class="eventon-actions" style='margin:0'>		
			
			<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/documentation/" target='_blank'><?php _e( 'Documentation', 'eventon' ); ?></a>
			
			<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/support/" target='_blank'><?php _e( 'Support', 'eventon' ); ?></a>

			<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/news/" target='_blank'><?php _e( 'News', 'eventon' ); ?></a>
			<a class="evo_admin_btn btn_prime" href="http://www.myeventon.com/documentation/eventon-changelog/" target='_blank'><?php _e( 'Changelog', 'eventon' ); ?></a>
			<a href="http://www.twitter.com/myeventon" target='_blank' class="evo_admin_btn btn_prime"><?php _e( 'Follow on Twitter', 'eventon' ); ?></a>
			
		</p>
		
		
		<?php /*
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'evo-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'evo-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'eventon' ); ?>			
			</a>
		</h2>
		<?php */
	}
	
	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		global $eventon;
		?>
		
		<div class="wrap about-wrap eventon-welcome-box">

			<?php $this->intro(); ?>

			<!--<div class="changelog point-releases"></div>-->

			
			<div class="return-to-dashboard">
				<a class='evo_wel_btn' href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'eventon' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to myeventon Settings', 'eventon' ); ?></a>
			</div>
		</div>

		<div class='evowel_info1'>
			<p class='h3'>
			<?php
				if(!empty($_GET['evo-updated']))
					$message = __( 'Thank you for updating eventON to ver ', 'eventon' );
				else
					$message = __( 'Thank you for purchasing eventON ver ', 'eventon' );
					
				printf( __( '%s%s', 'eventon' ), $message,	$eventon->version );
			?></p>			
			<p class='h4'><?php 
				if(!empty($_GET['evo-updated']))
					printf( __( 'We hope you will enjoy the new features we have added!','eventon'));
				else
					printf( __( 'We hope you will enjoy eventON - an event calendar plugin for WordPress!','eventon'));
			?></p>
			
		</div>

		<div class='get_started'>
			<div class="get_started_in">
			<h2>Quick get started with EventON guide</h2>

			<h3>Step 1: Create Events</h3>
			<p>Go to <a href='<?php echo get_admin_url();?>post-new.php?post_type=ajde_events'>Add New Events</a> and create a new event. <br/>More information about creating an event can be found <a href='http://www.myeventon.com/documentation/getting-started-with-eventon-adding-events/' target='_blank'>here.</a></p>

			<h3>Step 2: Add eventON shortcode to a page</h3>
			<p>Go to <a href='<?php echo get_admin_url();?>edit.php?post_type=page'>Pages</a> and create a page or find a page you want to add eventON calendar. Using <b>EventON Shortcode Generator</b> create a shortcode with the options of your choice.<br/>More information about adding shortcode can be found <a href='http://www.myeventon.com/documentation/adding-calendar-to-site/' target='_blank'>here.</a></p>

			<h3>Step 3: Configure EventON Settings</h3>
			<p>Go to <a href='<?php echo get_admin_url();?>admin.php?page=eventon'>EventON Settings</a> and configure eventON calendar settings, appearance and various other options to your preferance.</p>
			
			<h3>That is it! Enjoy!</h3>
			</div>

		</div>

		<div class='evow_credits'>
			<p style='text-transform:uppercase; font-size:20px; margin:0; padding-bottom:3px;'><a href='http://www.ashanjay.com' target='_blank'>AshanJay Product</a></p>
			<p style='text-transform:uppercase; opacity:0.7; margin:0'>Made in Portland, OR</p>
		</div>
		
<?php
		
	}
	
	/** Sends user to the welcome page on first activation	 */
		public function welcome() {

			// Bail if no activation redirect transient is set
		    if ( ! get_transient( '_evo_activation_redirect' )  )
				return;

			// Delete the redirect transient
			delete_transient( '_evo_activation_redirect' );

			// Bail if we are waiting to install or update via the interface update/install links
			if ( get_option( '_evo_needs_update' ) == 1  )
				return;

			// Bail if activating from network, or bulk, or within an iFrame
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
				return;
			
			// plugin is updated
			if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'eventon.php' ) ) )
				return;
				//wp_safe_redirect( admin_url( 'index.php?page=evo-about&evo-updated=true' ) );
			
			wp_safe_redirect( admin_url( 'index.php?page=evo-about' ) );
				
			
			exit;
		}	
		
	
	
}

new EVO_Welcome_Page();
?>