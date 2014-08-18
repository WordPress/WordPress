<?php
/*
  WPFront Plugins Base Menu
  Copyright (C) 2013, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront Plugins are distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if (!class_exists('WPFront_Base_Menu')) {

    class WPFront_Base_Menu {

        const MENU_SLUG = 'wpfront-plugins';

        private static $wpfrontBase = NULL;
        private static $wpfrontBaseMenu = NULL;

        function __construct($wpfrontBase) {
            if (self::$wpfrontBase == NULL) {
                self::$wpfrontBase = $wpfrontBase;
                self::$wpfrontBaseMenu = $this;
            } else {
                if (version_compare($this->version(), self::$wpfrontBaseMenu->version()) > 0) {
                    self::$wpfrontBase = $wpfrontBase;
                    self::$wpfrontBaseMenu = $this;
                }
            }
        }

        protected function version() {
            return '1.0';
        }

        protected function print_column_headers($tag = 'thead') {
            echo '<' . $tag . '><tr>'
            . '<th class="check-column"></th>'
            . '<th scope="col" id="name" class="manage-column column-name">' . $this->__('Name') . '</th>'
            . '<th scope="col" id="version" class="manage-column column-version">' . $this->__('Version') . '</th>'
            . '<th scope="col" id="rating" class="manage-column column-rating">' . $this->__('Rating') . '</th>'
            . '<th scope="col" id="description" class="manage-column column-description">' . $this->__('Description') . '</th>'
            . '</tr></' . $tag . '>';
        }

        protected function print_column_footers() {
            $this->print_column_headers('tfoot');
        }

        protected function wp_star_rating($args) {
            if (function_exists('wp_star_rating')) {
                echo wp_star_rating($args);
                return;
            }

            echo '<div class="star-holder" title="' . sprintf($this->__('based on %s rating(s)'), number_format_i18n($args['number'])) . '">'
            . '<div class="star star-rating" style="width:' . esc_attr(str_replace(',', '.', $args['rating'])) . 'px"></div>'
            . '</div>';
        }

        protected function create_plugin_list() {
            if (isset($_GET['action'])) {
                if ($_GET['action'] == 'activate') {
                    if (isset($_GET['plugin'])) {
                        activate_plugin($_GET['plugin'] . '/' . $_GET['plugin'] . '.php');
//                        echo '<script type="text/javascript">window.location="' . WPFront_Static::self_admin_url('admin.php?page=' . self::MENU_SLUG) . '";</script>';
//                        wp_die();
//                        return;
                    }
                }
            }

            $plugins_allowedtags = array(
                'a' => array('href' => array(), 'title' => array(), 'target' => array()),
                'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
                'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
                'ul' => array(), 'ol' => array(), 'li' => array(), 'p' => array(), 'br' => array()
            );

            if (!function_exists('plugins_api'))
                require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
            add_thickbox();

            $args = array();
            $args['page'] = 1;
            $args['per_page'] = 30;
            $args['author'] = 'syammohanm';

            $api = plugins_api('query_plugins', $args);

            if (is_wp_error($api)) {
                wp_die($this->__('Unable to communicate with WordPress.org'));
                return;
            }
            ?>
            <style type="text/css">
                div.wpfront-container table.plugins {
                    margin-top: 16px;
                    margin-bottom: 16px;
                }
                div.wpfront-container table.plugins th.check-column {
                    width: 0px;
                }
                div.wpfront-container div.footer {
                    text-align: center;
                }
            </style>
            <?php
            echo '<div class="wrap wpfront-container">';
            echo '<h2>' . $this->__('WPFront Plugins') . '</h2>';
            echo '<table class="wp-list-table widefat plugins plugin-install">';
            $this->print_column_headers();

            foreach ((array) $api->plugins as $plugin) {
                if (is_object($plugin))
                    $plugin = (array) $plugin;

                $title = wp_kses($plugin['name'], $plugins_allowedtags);
                $description = strip_tags($plugin['description']);
                if (strlen($description) > 400)
                    $description = mb_substr($description, 0, 400) . '&#8230;';
                //remove any trailing entities
                $description = preg_replace('/&[^;\s]{0,6}$/', '', $description);
                //strip leading/trailing & multiple consecutive lines
                $description = trim($description);
                $description = preg_replace("|(\r?\n)+|", "\n", $description);
                //\n => <br>
                $description = nl2br($description);
                $version = wp_kses($plugin['version'], $plugins_allowedtags);

                $name = strip_tags($title . ' ' . $version);
                $author = $plugin['author'];
                if (!empty($plugin['author']))
                    $author = ' <cite>' . sprintf($this->__('By %s'), $author) . '.</cite>';

                $author = wp_kses($author, $plugins_allowedtags);

                $action_links = array();
                $action_links[] = '<a href="' . WPFront_Static::self_admin_url('plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
                                '&amp;TB_iframe=true&amp;width=600&amp;height=550') . '" class="thickbox" title="' .
                        esc_attr(sprintf($this->__('More information about %s'), $name)) . '">' . $this->__('Details') . '</a>';

                $class = 'inactive';
                if (current_user_can('install_plugins') || current_user_can('update_plugins')) {
                    $status = install_plugin_install_status($plugin);

                    switch ($status['status']) {
                        case 'install':
                            if ($status['url'])
                                $action_links[] = '<a class="install-now" href="' . $status['url'] . '" title="' . esc_attr(sprintf($this->__('Install %s'), $name)) . '">' . $this->__('Install Now') . '</a>';
                            $class = 'active';
                            break;
                        case 'update_available':
                            if ($status['url'])
                                $action_links[] = '<a href="' . $status['url'] . '" title="' . esc_attr(sprintf($this->__('Update to version %s'), $status['version'])) . '">' . sprintf($this->__('Update Now'), $status['version']) . '</a>';
                            $class = 'active update';
                            break;
                        case 'latest_installed':
                        case 'newer_installed':
                            $action_links[] = '<span title="' . esc_attr__($this->__('This plugin is already installed and is up to date')) . ' ">' . $this->__('Installed') . '</span>';
                            if (is_plugin_active($plugin['slug'] . '/' . $plugin['slug'] . '.php'))
                                $action_links[] = '<a href="' . WPFront_Static::self_admin_url('admin.php?page=' . $plugin['slug']) . '">' . $this->__('Settings') . '</a>';
                            else
                                $action_links[] = '<a href="' . WPFront_Static::self_admin_url('admin.php?page=' . self::MENU_SLUG . '&action=activate&plugin=' . $plugin['slug']) . '">' . $this->__('Activate') . '</a>';
                            break;
                    }
                }
                $action_links = apply_filters('plugin_install_action_links', $action_links, $plugin);
                ?>
                <tr class="<?php echo $class; ?>">
                    <th class="check-column"></th>
                    <td class="name column-name"><strong><?php echo $title; ?></strong>
                        <div class="action-links"><?php if (!empty($action_links)) echo implode(' | ', $action_links); ?></div>
                    </td>
                    <td class="vers column-version"><?php echo $version; ?></td>
                    <td class="vers column-rating">
                        <?php $this->wp_star_rating(array('rating' => $plugin['rating'], 'type' => 'percent', 'number' => $plugin['num_ratings'])); ?>
                    </td>
                    <td class="desc column-description"><?php echo $description, $author; ?></td>
                </tr>
                <?php
            }

            $this->print_column_footers();
            echo '</table>';
            echo '<div class="footer"><a href="http://wpfront.com/contact" target="_blank">' . $this->__('Feedback') . '</a> | <a href="http://wpfront.com" target="_blank">wpfront.com</a></div>';
            echo '</div>';
        }

        public static function plugin_list() {
            self::$wpfrontBaseMenu->create_plugin_list();
        }

        protected function __($key) {
            return self::$wpfrontBase->__($key);
        }

        protected function create_admin_menu($menu_data) {
            $menu_slug = self::MENU_SLUG;

            global $admin_page_hooks, $submenu;
            if (!isset($admin_page_hooks[$menu_slug])) {
                add_menu_page($this->__('WPFront'), $this->__('WPFront'), 'manage_options', $menu_slug, null, self::$wpfrontBase->pluginURL() . 'classes/base/images/wpfront_menu.png');
                add_submenu_page($menu_slug, $this->__('WPFront Plugins'), $this->__('All Plugins'), 'manage_options', $menu_slug, array('WPFront_Base_Menu', 'plugin_list'));
            }

            if (empty($submenu[$menu_slug])) {
                return;
            }

            //$extra_menu = array_pop($submenu[$menu_slug]);

            foreach ($menu_data as $value) {
                $flag = FALSE;

                foreach ($submenu[$menu_slug] as $s) {
                    if ($s[2] == $value['slug']) {
                        $flag = TRUE;
                        break;
                    }
                }

                if ($flag == TRUE)
                    continue;

                $page_hook_suffix = add_submenu_page($menu_slug, $value['title'], $value['link'], 'manage_options', $value['slug'], array($value['this'], 'options_page'));

                add_action('admin_print_scripts-' . $page_hook_suffix, array($value['this'], 'enqueue_options_scripts'));
                add_action('admin_print_styles-' . $page_hook_suffix, array($value['this'], 'enqueue_options_styles'));
            }

            //usort($submenu[$menu_slug], array('WPFront_Base', 'submenu_compare'));
            //$submenu[$menu_slug][] = $extra_menu;
        }

        public static function admin_menu($menu_data) {
            self::$wpfrontBaseMenu->create_admin_menu($menu_data);
        }

    }

}
    