<?php
/**
 * Handles adding "more tools" tab in AO admin settings page which promotes (future) AO
 * addons and/or affiliate services.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class autoptimizePartners
{
    public function __construct()
    {
        $this->run();
    }

    public function run()
    {
        if ( $this->enabled() ) {
            add_filter( 'autoptimize_filter_settingsscreen_tabs', array( $this, 'add_partner_tabs' ), 10, 1 );
        }
        if ( is_multisite() && is_network_admin() && autoptimizeOptionWrapper::is_ao_active_for_network() ) {
            add_action( 'network_admin_menu', array( $this, 'add_admin_menu' ) );
        } else {
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        }
    }

    protected function enabled()
    {
        return apply_filters( 'autoptimize_filter_show_partner_tabs', true );
    }

    public function add_partner_tabs( $in )
    {
        $in = array_merge(
            $in,
            array(
                'ao_partners' => __( 'Optimize More!', 'autoptimize' ),
            )
        );

        return $in;
    }

    public function add_admin_menu()
    {
        if ( $this->enabled() ) {
            add_submenu_page( '', 'AO partner', 'AO partner', 'manage_options', 'ao_partners', array( $this, 'ao_partners_page' ) );
        }
    }

    protected function get_ao_partner_feed_markup()
    {
        $no_feed_text = __( 'Have a look at <a href="http://optimizingmatters.com/">optimizingmatters.com</a> for Autoptimize power-ups!', 'autoptimize' );
        $output       = '';
        if ( apply_filters( 'autoptimize_settingsscreen_remotehttp', true ) ) {
            $rss      = fetch_feed( 'http://feeds.feedburner.com/OptimizingMattersDownloads' );
            $maxitems = 0;

            if ( ! is_wp_error( $rss ) ) {
                $maxitems  = $rss->get_item_quantity( 20 );
                $rss_items = $rss->get_items( 0, $maxitems );
            }

            if ( 0 == $maxitems ) {
                $output .= $no_feed_text;
            } else {
                $output .= '<ul>';
                foreach ( $rss_items as $item ) {
                    $item_url  = esc_url( $item->get_permalink() );
                    $enclosure = $item->get_enclosure();

                    $output .= '<li class="itemDetail">';
                    $output .= '<h3 class="itemTitle"><a href="' . $item_url . '" target="_blank">' . esc_html( $item->get_title() ) . '</a></h3>';

                    if ( $enclosure && ( false !== strpos( $enclosure->get_type(), 'image' ) ) ) {
                        $img_url = esc_url( $enclosure->get_link() );
                        $output .= '<div class="itemImage"><a href="' . $item_url . '" target="_blank"><img src="' . $img_url . '"></a></div>';
                    }

                    $output .= '<div class="itemDescription">' . wp_kses_post( $item->get_description() ) . '</div>';
                    $output .= '<div class="itemButtonRow"><div class="itemButton button-secondary"><a href="' . $item_url . '" target="_blank">' . __( 'More info', 'autoptimize' ) . '</a></div></div>';
                    $output .= '</li>';
                }
                $output .= '</ul>';
            }
        } else {
            $output .= $no_feed_text;
        }

        return $output;
    }

    public function ao_partners_page()
    {
        ?>
<style>
    .itemDetail {
        background: #fff;
        width: 250px;
        min-height: 290px;
        border: 1px solid #ccc;
        float: left;
        padding: 15px;
        position: relative;
        margin: 0 10px 10px 0;
    }
    .itemTitle {
        margin-top:0px;
        margin-bottom:10px;
    }
    .itemImage {
        text-align: center;
    }
    .itemImage img {
        max-width: 95%;
        max-height: 150px;
    }
    .itemDescription {
        margin-bottom:30px;
    }
    .itemButtonRow {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width:100%;
    }
    .itemButton {
        float:right;
    }
    .itemButton a {
        text-decoration: none;
        color: #555;
    }
    .itemButton a:hover {
        text-decoration: none;
        color: #23282d;
    }
    </style>
    <script>document.title = "Autoptimize: <?php _e( 'Optimize More!', 'autoptimize' ); ?> " + document.title;</script>
    <div class="wrap">
        <h1><?php apply_filters( 'autoptimize_filter_settings_is_pro', false ) ? _e( 'Autoptimize Pro Settings', 'autoptimize' ) : _e( 'Autoptimize Settings', 'autoptimize' ); ?></h1>
        <?php echo autoptimizeConfig::ao_admin_tabs(); ?>
        <?php echo '<h2>' . __( "These Autoptimize power-ups and related services will improve your site's performance even more!", 'autoptimize' ) . '</h2>'; ?>
        <div>
            <?php echo $this->get_ao_partner_feed_markup(); ?>
        </div>
    </div>
        <?php
    }
}
