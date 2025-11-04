<?php
/**
 * Performance settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';
require_once ABSPATH . 'wp-admin/includes/performance.php';

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );
}

$title       = __( 'Performance Settings' );
$parent_file = 'options-general.php';

$cache_settings   = wp_performance_get_persistent_cache_settings();
$page_settings    = wp_performance_get_page_cache_settings();
$asset_settings   = wp_performance_get_asset_optimization_settings();
$image_settings   = wp_performance_get_image_optimization_settings();

require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap">
        <h1><?php echo esc_html( $title ); ?></h1>

        <?php settings_errors(); ?>

        <form method="post" action="options.php" novalidate="novalidate">
                <?php settings_fields( 'performance' ); ?>

                <h2 class="title"><?php esc_html_e( 'Persistent Object Cache' ); ?></h2>
                <table class="form-table" role="presentation">
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Backend' ); ?></th>
                                <td>
                                        <select name="wp_performance_persistent_cache[backend]">
                                                <?php
                                                $backends = array(
                                                        'none'      => __( 'None' ),
                                                        'redis'     => __( 'Redis' ),
                                                        'memcached' => __( 'Memcached' ),
                                                        'lsmcd'     => __( 'LiteSpeed Memcached (LSMCD)' ),
                                                );
                                                foreach ( $backends as $backend => $label ) {
                                                        printf(
                                                                '<option value="%1$s" %3$s>%2$s</option>',
                                                                esc_attr( $backend ),
                                                                esc_html( $label ),
                                                                selected( isset( $cache_settings['backend'] ) ? $cache_settings['backend'] : 'none', $backend, false )
                                                        );
                                                }
                                                ?>
                                        </select>
                                        <p class="description"><?php esc_html_e( 'Select the object cache backend used for persistent caching.' ); ?></p>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Connection Settings' ); ?></th>
                                <td>
                                        <label>
                                                <span><?php esc_html_e( 'Host' ); ?></span>
                                                <input type="text" class="regular-text" name="wp_performance_persistent_cache[host]" value="<?php echo esc_attr( isset( $cache_settings['host'] ) ? $cache_settings['host'] : '127.0.0.1' ); ?>" />
                                        </label>
                                        <label>
                                                <span><?php esc_html_e( 'Port' ); ?></span>
                                                <input type="number" class="small-text" name="wp_performance_persistent_cache[port]" value="<?php echo esc_attr( isset( $cache_settings['port'] ) ? $cache_settings['port'] : '' ); ?>" />
                                        </label>
                                        <label>
                                                <span><?php esc_html_e( 'Database' ); ?></span>
                                                <input type="number" class="small-text" name="wp_performance_persistent_cache[database]" value="<?php echo esc_attr( isset( $cache_settings['database'] ) ? $cache_settings['database'] : '' ); ?>" />
                                        </label>
                                        <label>
                                                <span><?php esc_html_e( 'Password' ); ?></span>
                                                <input type="password" class="regular-text" name="wp_performance_persistent_cache[password]" value="<?php echo esc_attr( isset( $cache_settings['password'] ) ? $cache_settings['password'] : '' ); ?>" />
                                        </label>
                                        <p class="description"><?php esc_html_e( 'Connection details are used when connecting to Redis, Memcached, or LSMCD.' ); ?></p>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Compression' ); ?></th>
                                <td>
                                        <label for="wp_performance_persistent_cache_compression">
                                                <input type="checkbox" id="wp_performance_persistent_cache_compression" name="wp_performance_persistent_cache[compression]" value="1" <?php checked( ! empty( $cache_settings['compression'] ) ); ?> />
                                                <?php esc_html_e( 'Enable cache compression when supported by the backend.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Opcode Cache' ); ?></th>
                                <td>
                                        <select name="wp_performance_persistent_cache[opcode]">
                                                <?php
                                                $opcodes = array(
                                                        'opcache' => __( 'OPcache' ),
                                                        'apcu'    => __( 'APCu' ),
                                                );
                                                $selected_opcode = isset( $cache_settings['opcode']['engine'] ) ? $cache_settings['opcode']['engine'] : 'opcache';
                                                echo '<option value="">' . esc_html__( 'None' ) . '</option>';
                                                foreach ( $opcodes as $value => $label ) {
                                                        printf(
                                                                '<option value="%1$s" %3$s>%2$s</option>',
                                                                esc_attr( $value ),
                                                                esc_html( $label ),
                                                                selected( $selected_opcode, $value, false )
                                                        );
                                                }
                                                ?>
                                        </select>
                                </td>
                        </tr>
                </table>

                <h2 class="title"><?php esc_html_e( 'Page Cache' ); ?></h2>
                <table class="form-table" role="presentation">
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Page Cache' ); ?></th>
                                <td>
                                        <label for="wp_performance_page_cache_enabled">
                                                <input type="checkbox" id="wp_performance_page_cache_enabled" name="wp_performance_page_cache[enabled]" value="1" <?php checked( ! empty( $page_settings['enabled'] ) ); ?> />
                                                <?php esc_html_e( 'Serve cached responses for front-end visitors.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Segmentation' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_page_cache[mobile_segment]" value="1" <?php checked( ! empty( $page_settings['mobile_segment'] ) ); ?> />
                                                <?php esc_html_e( 'Maintain a dedicated mobile cache.' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_page_cache[desktop_segment]" value="1" <?php checked( ! empty( $page_settings['desktop_segment'] ) ); ?> />
                                                <?php esc_html_e( 'Maintain a dedicated desktop cache.' ); ?>
                                        </label>
                                        <p class="description"><?php esc_html_e( 'Segment cache entries to prevent device-specific responses from conflicting.' ); ?></p>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Time To Live (seconds)' ); ?></th>
                                <td>
                                        <input type="number" class="small-text" name="wp_performance_page_cache[ttl]" value="<?php echo esc_attr( isset( $page_settings['ttl'] ) ? $page_settings['ttl'] : 0 ); ?>" />
                                        <p class="description"><?php esc_html_e( 'Set 0 to use default cache headers.' ); ?></p>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'REST API Caching' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_page_cache[rest_cache]" value="1" <?php checked( ! empty( $page_settings['rest_cache'] ) ); ?> />
                                                <?php esc_html_e( 'Cache REST API responses based on request segmentation.' ); ?>
                                        </label>
                                </td>
                        </tr>
                </table>

                <h2 class="title"><?php esc_html_e( 'Asset Optimization' ); ?></h2>
                <table class="form-table" role="presentation">
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Enable Pipeline' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[enable_pipeline]" value="1" <?php checked( ! empty( $asset_settings['enable_pipeline'] ) ); ?> />
                                                <?php esc_html_e( 'Enable the minification and combination pipeline for styles and scripts.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Minification' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[minify_js]" value="1" <?php checked( ! empty( $asset_settings['minify_js'] ) ); ?> />
                                                <?php esc_html_e( 'Minify JavaScript' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[minify_css]" value="1" <?php checked( ! empty( $asset_settings['minify_css'] ) ); ?> />
                                                <?php esc_html_e( 'Minify CSS' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[minify_html]" value="1" <?php checked( ! empty( $asset_settings['minify_html'] ) ); ?> />
                                                <?php esc_html_e( 'Minify HTML output' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Combination' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[combine_js]" value="1" <?php checked( ! empty( $asset_settings['combine_js'] ) ); ?> />
                                                <?php esc_html_e( 'Combine JavaScript assets when possible.' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[combine_css]" value="1" <?php checked( ! empty( $asset_settings['combine_css'] ) ); ?> />
                                                <?php esc_html_e( 'Combine CSS assets when possible.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Async / Defer' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[async_js]" value="1" <?php checked( ! empty( $asset_settings['async_js'] ) ); ?> />
                                                <?php esc_html_e( 'Load JavaScript asynchronously.' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_asset_optimization[defer_js]" value="1" <?php checked( ! empty( $asset_settings['defer_js'] ) ); ?> />
                                                <?php esc_html_e( 'Defer JavaScript execution until after parsing.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Critical CSS Hook' ); ?></th>
                                <td>
                                        <input type="text" class="regular-text" name="wp_performance_asset_optimization[critical_css_hook]" value="<?php echo esc_attr( isset( $asset_settings['critical_css_hook'] ) ? $asset_settings['critical_css_hook'] : '' ); ?>" />
                                        <p class="description"><?php esc_html_e( 'Actions with this name will be fired after the style engine compiles a stylesheet.' ); ?></p>
                                </td>
                        </tr>
                </table>

                <h2 class="title"><?php esc_html_e( 'Image Optimization' ); ?></h2>
                <table class="form-table" role="presentation">
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Optimization Mode' ); ?></th>
                                <td>
                                        <select name="wp_performance_image_optimization[mode]">
                                                <?php
                                                $modes = array(
                                                        'lossless' => __( 'Lossless' ),
                                                        'lossy'    => __( 'Lossy' ),
                                                );
                                                foreach ( $modes as $value => $label ) {
                                                        printf(
                                                                '<option value="%1$s" %3$s>%2$s</option>',
                                                                esc_attr( $value ),
                                                                esc_html( $label ),
                                                                selected( isset( $image_settings['mode'] ) ? $image_settings['mode'] : 'lossless', $value, false )
                                                        );
                                                }
                                                ?>
                                        </select>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Format Conversion' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_image_optimization[convert_webp]" value="1" <?php checked( ! empty( $image_settings['convert_webp'] ) ); ?> />
                                                <?php esc_html_e( 'Generate WebP images when supported.' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <input type="checkbox" name="wp_performance_image_optimization[convert_avif]" value="1" <?php checked( ! empty( $image_settings['convert_avif'] ) ); ?> />
                                                <?php esc_html_e( 'Generate AVIF images when supported.' ); ?>
                                        </label>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Quality' ); ?></th>
                                <td>
                                        <input type="number" class="small-text" min="10" max="100" name="wp_performance_image_optimization[quality]" value="<?php echo esc_attr( isset( $image_settings['quality'] ) ? $image_settings['quality'] : 82 ); ?>" />
                                        <p class="description"><?php esc_html_e( 'Applies when using lossy compression and when editors support it.' ); ?></p>
                                </td>
                        </tr>
                        <tr>
                                <th scope="row"><?php esc_html_e( 'Placeholders & Lazy Loading' ); ?></th>
                                <td>
                                        <label>
                                                <input type="checkbox" name="wp_performance_image_optimization[placeholders]" value="1" <?php checked( ! empty( $image_settings['placeholders'] ) ); ?> />
                                                <?php esc_html_e( 'Generate responsive placeholders.' ); ?>
                                        </label>
                                        <br />
                                        <label>
                                                <span class="screen-reader-text"><?php esc_html_e( 'Lazy Loading Mode' ); ?></span>
                                                <select name="wp_performance_image_optimization[lazy_loading]">
                                                        <?php
                                                        $lazy_options = array(
                                                                'default' => __( 'Use WordPress defaults' ),
                                                                'lazy'    => __( 'Force lazy loading' ),
                                                                'eager'   => __( 'Disable lazy loading' ),
                                                        );
                                                        foreach ( $lazy_options as $value => $label ) {
                                                                printf(
                                                                        '<option value="%1$s" %3$s>%2$s</option>',
                                                                        esc_attr( $value ),
                                                                        esc_html( $label ),
                                                                        selected( isset( $image_settings['lazy_loading'] ) ? $image_settings['lazy_loading'] : 'default', $value, false )
                                                                );
                                                        }
                                                        ?>
                                                </select>
                                        </label>
                                </td>
                        </tr>
                </table>

                <?php submit_button(); ?>
        </form>
</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
