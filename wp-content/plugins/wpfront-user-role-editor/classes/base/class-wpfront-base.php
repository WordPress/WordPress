<?php

/*
  WPFront Plugins Base
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

require_once("class-wpfront-static.php");
require_once("class-wpfront-base-menu.php");

if (!class_exists('WPFront_Base')) {

    /**
     * Plugin framework base class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2013 WPFront.com
     */
    class WPFront_Base {

        private $plugin_slug;
        private $options_page_slug;
        protected $pluginURLRoot;
        protected $pluginDIRRoot;
        private static $menu_data = array();

        function __construct($file, $pluginSlug, $wpfrontBaseMenu = NULL) {
            $this->plugin_slug = $pluginSlug;
            $this->options_page_slug = $this->plugin_slug;
            if ($wpfrontBaseMenu == NULL)
                $wpfrontBaseMenu = new WPFront_Base_Menu($this);

            $this->pluginURLRoot = plugins_url() . '/' . $this->plugin_slug . '/';
            $this->pluginDIRRoot = dirname($file) . '/../';

            add_action('init', array(&$this, 'init'));
            add_action('plugins_loaded', array(&$this, 'plugins_loaded_base'));

            //register actions
            if (is_admin()) {
                add_action('admin_init', array(&$this, 'admin_init'));
                add_action('admin_menu', array(&$this, 'admin_menu'));
                add_filter('plugin_action_links', array(&$this, 'action_links'), 10, 2);
            } else {
                add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
                add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
            }
        }

        protected function add_menu($title, $link) {
            self::$menu_data[] = array(
                'title' => $title,
                'link' => $link,
                'this' => $this,
                'slug' => $this->options_page_slug
            );
        }

        public function init() {
            
        }

        public function plugins_loaded_base() {
            //for localization
            load_plugin_textdomain($this->plugin_slug, FALSE, $this->plugin_slug . '/languages/');

            $this->plugins_loaded();
        }

        public function plugins_loaded() {
            
        }

        public function admin_init() {
            
        }

        public function admin_menu() {
            WPFront_Base_Menu::admin_menu(self::$menu_data);
        }

        public static function submenu_compare($a, $b) {
            return strcmp($a[0], $b[0]);
        }

        public function action_links($links, $file) {
            if ($file == $this->plugin_slug . '/' . $this->plugin_slug . '.php') {
                $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . $this->options_page_slug . '">' . $this->__('Settings') . '</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

        public function enqueue_styles() {
            
        }

        public function enqueue_scripts() {
            
        }

        public function enqueue_options_styles() {
            
        }

        public function enqueue_options_scripts() {
            
        }

        //creates options page
        public function options_page() {
            if (!current_user_can('manage_options')) {
                wp_die($this->__('You do not have sufficient permissions to access this page.'));
                return;
            }

            include($this->pluginDIRRoot . 'templates/options-template.php');
        }

        protected function options_page_header($title, $optionsGroupName) {
            echo '<div class="wrap">';
            @screen_icon($this->options_page_slug);
            echo '<h2>' . $title . '</h2>';
            echo '<div id="' . $this->options_page_slug . '-options" class="inside">';
            echo '<form method="post" action="options.php">';
            @settings_fields($optionsGroupName);
            @do_settings_sections($this->options_page_slug);

            if ((isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') || (isset($_GET['updated']) && $_GET['updated'] == 'true')) {
                echo '
                <div class="updated">
                    <p>
                        <strong>' . $this->__('If you have a caching plugin, clear the cache for the new settings to take effect.') . '</strong>
                    </p>
                </div>
                ';
            }
        }

        protected function options_page_footer($settingsLink, $FAQLink, $extraLinks = NULL) {
            @$this->submit_button();

            if ($extraLinks != NULL) {
                foreach ($extraLinks as $value) {
                    echo '<a href="' . $value['href'] . '" target="' . $value['target'] . '">' . $value['text'] . '</a>';
                    echo ' | ';
                }
            }

            echo '
                <a href="http://wpfront.com/' . $settingsLink . '" target="_blank">' . $this->__('Settings Description') . '</a>
                |
                <a href="http://wpfront.com/' . $FAQLink . '" target="_blank">' . $this->__('Plugin FAQ') . '</a>
                |
                <a href="http://wpfront.com/contact/" target="_blank">' . $this->__('Feature Request') . '</a>
                |
                <a href="http://wpfront.com/contact/" target="_blank">' . $this->__('Report Bug') . '</a>
                |
                <a href="http://wordpress.org/support/view/plugin-reviews/' . $this->plugin_slug . '" target="_blank">' . $this->__('Write Review') . '</a>
                |
                <a href="http://wpfront.com/contact/" target="_blank">' . $this->__('Contact Me') . '</a>
                |
                <a href="http://wpfront.com/donate/" target="_blank">' . $this->__('Buy me a Beer or Coffee') . '</a>
            ';
            echo '</form>';
            echo '</div>';
            echo '</div>';
        }

        //returns localized string
        public function __($key) {
            return __($key, $this->plugin_slug);
        }

        //for compatibility
        public function submit_button() {
            if (function_exists('submit_button')) {
                submit_button();
            } else {
                echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="' . $this->__('Save Changes') . '" /></p>';
            }
        }

        public function pluginURL() {
            return $this->pluginURLRoot;
        }

        public function pluginDIR() {
            return $this->pluginDIRRoot;
        }

    }

}

