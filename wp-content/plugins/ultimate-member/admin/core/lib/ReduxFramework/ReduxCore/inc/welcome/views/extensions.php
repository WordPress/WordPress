<?php
    /*
    repeater =>
    social profiles =>
    js button =>
    multi media =>
    css layout =>
    color schemes => adjust-alt
    custom fonts => fontsize
    code mirror => view-mode
    live search => search
    support faq's => question
    date time picker =>
    premium support =>
    metaboxes =>
    widget areas =>
    shortcodes =>
    icon select => gallery
    tracking =>
    * */
    $iconMap = array(
        'repeater'        => 'tags',
        'social-profiles' => 'group',
        'js-button'       => 'hand-down',
        'multi-media'     => 'picture',
        'css-layout'      => 'fullscreen',
        'color-schemes'   => 'adjust-alt',
        'custom-fonts'    => 'fontsize',
        //'codemirror'      => 'view-mode',
        'live-search'     => 'search',
        'support-faqs'    => 'question',
        'date-time'       => 'calendar',
        'premium-support' => 'fire',
        'metaboxes'       => 'magic',
        'widget-areas'    => 'inbox-box',
        'shortcodes'      => 'shortcode',
        'icon-select'     => 'gallery',
        'accordion'       => 'lines'
    );
    $colors  = array(
        '8CC63F',
        '8CC63F',
        '0A803B',
        '25AAE1',
        '0F75BC',
        'F7941E',
        'F1592A',
        'ED217C',
        'BF1E2D',
        '8569CF',
        '0D9FD8',
        '8AD749',
        'EECE00',
        'F8981F',
        'F80E27',
        'F640AE'
    );
    shuffle( $colors );
    echo '<style type="text/css">';
?>

<?php
    foreach ( $colors as $key => $color ) {
        echo '.theme-browser .theme.color' . esc_html($key) . ' .theme-screenshot{background-color:' . esc_html(Redux_Helpers::hex2rgba( $color, .45 )) . ';}';
        echo '.theme-browser .theme.color' . esc_html($key) . ':hover .theme-screenshot{background-color:' . esc_html(Redux_Helpers::hex2rgba( $color, .75 )) . ';}';

    }
    echo '</style>';
    $color = 1;

?>
<div class="wrap about-wrap">
    <h1><?php esc_html_e( 'Redux Framework - Extensions', 'redux-framework' ); ?></h1>

    <div class="about-text">
        <?php printf( __( 'Supercharge your Redux experience. Our extensions provide you with features that will take your products to the next level.', 'redux-framework' ), esc_html($this->display_version) ); ?>
    </div>
    <div class="redux-badge">
        <i class="el el-redux"></i>
        <span>
            <?php printf( __( 'Version %s', 'redux-framework' ), esc_html(ReduxFramework::$_version )); ?>
        </span>
    </div>

    <?php $this->actions(); ?>
    <?php $this->tabs(); ?>

    <p class="about-description">
        <?php esc_html_e( "While some are built specificially for developers, extensions such as Custom Fonts are sure to make any user happy.", 'redux-framework' ); ?>
    </p>

    <div class="extensions">
        <div class="feature-section theme-browser rendered" style="clear:both;">
<?php
            $data = get_transient( 'redux-extensions-fetch' );

            if ( empty( $data ) ) {
                $data = @wp_remote_retrieve_body( @wp_remote_get( 'http://reduxframework.com/wp-admin/admin-ajax.php?action=get_redux_extensions' ) );
                
                if ( ! empty( $data ) ) {
                    $data = json_decode( $data, true );
                    set_transient( 'redux-extensions-fetch', $data, 24 * HOUR_IN_SECONDS );
                }
            }
            function rdx_shuffle_assoc( $list ) {
                if ( ! is_array( $list ) ) {
                    return $list;
                }

                $keys = array_keys( $list );
                shuffle( $keys );
                $random = array();
                foreach ( $keys as $key ) {
                    $random[ $key ] = $list[ $key ];
                }

                return $random;
            }

            $data = rdx_shuffle_assoc( $data );

            if (!empty($data)) {
                foreach ( $data as $key => $extension ) {
?>
                    <div class="theme color<?php echo esc_html($color);?>">
                        <?php $color ++;?>
                        <div class="theme-screenshot">
                            <figure>
                                <i class="el <?php echo isset( $iconMap[ $key ] ) && ! empty( $iconMap[ $key ] ) ? 'el-' . esc_attr($iconMap[ $key ]) : 'el-redux'; ?>"></i>
                                <figcaption>
                                    <p><?php echo esc_html($extension['excerpt']);?></p>
                                    <a href="<?php echo esc_url($extension['url']); ?>" target="_blank">Learn more</a>
                                </figcaption>
                            </figure>
                        </div>
                        <h3 class="theme-name" id="classic"><?php echo esc_html($extension['title']); ?></h3>

                        <div class="theme-actions">
                            <a class="button button-primary button-install-demo"
                               data-demo-id="<?php echo esc_attr($key); ?>"
                               href="<?php echo esc_url($extension['url']); ?>" 
                               target="_blank">Learn More
                            </a>
                        </div>
                    </div>
<?php
                }
            }
?>
        </div>
    </div>
</div>