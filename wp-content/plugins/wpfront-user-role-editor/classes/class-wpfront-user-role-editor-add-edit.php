<?php

/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
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


if (!class_exists('WPFront_User_Role_Editor_Add_Edit')) {

    /**
     * Add or Edit Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Add_Edit {

        const MENU_SLUG = 'wpfront-user-role-editor-add-new';

        private $main;
        private $role = null;
        private $is_editable = FALSE;
        private $role_exists = FALSE;
        private $error = FALSE;

        function __construct($main) {
            $this->main = $main;
        }

        public function ajax_register() {
            add_action('wp_ajax_wpfront_user_role_editor_copy_capabilities', array($this, 'copy_capabilities_callback'));
        }

        public function add_edit_role($role_name) {
            global $wp_roles;
            $roles = $wp_roles->role_names;

            if (array_key_exists($role_name, $roles)) {
                $this->role = get_role($role_name);
            }

            if ($this->role === NULL) {
                if (!$this->can_create()) {
                    $this->main->permission_denied();
                    return;
                }
            } else {
                if (!$this->can_edit()) {
                    $this->main->permission_denied();
                    return;
                }
            }

            if ($this->role == NULL) {
                $this->is_editable = TRUE;
            } else if ($role_name != 'administrator') {
                $this->is_editable = array_key_exists($role_name, get_editable_roles());
            }

            $success = FALSE;
            if (!empty($_POST['createrole'])) {
                while (TRUE) {
                    if (!$this->is_display_name_valid())
                        break;
                    if ($this->role == NULL && !$this->is_role_name_valid())
                        break;

                    $capabilities = array();
                    if (!empty($_POST['capabilities'])) {
                        foreach ($_POST['capabilities'] as $key => $value) {
                            $capabilities[$key] = TRUE;
                        }
                    }

                    if ($this->role == NULL) {
                        $role_name = $this->get_role_name();
                        if (array_key_exists($role_name, $roles)) {
                            $this->role_exists = TRUE;
                            break;
                        }
                        $error = add_role($role_name, $this->get_display_name(), $capabilities);
                        if ($error == NULL) {
                            $this->error = TRUE;
                            break;
                        }
                    } else {
                        global $wp_roles;
                        $wp_roles->roles[$this->role->name] = array(
                            'name' => $this->get_display_name(),
                            'capabilities' => $capabilities
                        );
                        update_option($wp_roles->role_key, $wp_roles->roles);
                        $wp_roles->role_objects[$this->role->name] = new WP_Role($this->role->name, $capabilities);
                        $wp_roles->role_names[$this->role->name] = $this->get_display_name();
                    }

                    $success = TRUE;
                    break;
                }
            }

            if ($success) {
                printf('<script type="text/javascript">document.location="%s";</script>', $this->list_roles_url());
            } else {
                include($this->main->pluginDIR() . 'templates/add-edit-role.php');
            }
        }

        private function can_create() {
            return $this->main->current_user_can('create_roles');
        }

        private function can_edit() {
            return $this->main->current_user_can('edit_roles');
        }

        private function __($s) {
            return $this->main->__($s);
        }

        private function add_new_url() {
            return admin_url('admin.php') . '?page=' . self::MENU_SLUG;
        }

        private function list_roles_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG;
        }

        private function is_display_name_valid() {
            if (empty($_POST['createrole']))
                return TRUE;

            if ($this->get_display_name() == '')
                return FALSE;

            return TRUE;
        }

        private function is_display_name_disabled() {
            return !$this->is_editable;
        }

        private function get_display_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['display_name']))
                    return '';

                return trim($_POST['display_name']);
            }

            if ($this->role == NULL)
                return '';
            global $wp_roles;
            return $wp_roles->role_names[$this->role->name];
        }

        private function is_role_name_valid() {
            if (empty($_POST['createrole']))
                return TRUE;

            if ($this->get_role_name() == '')
                return FALSE;

            return TRUE;
        }

        private function is_role_name_disabled() {
            if ($this->role != NULL)
                return TRUE;
            if (!$this->is_editable)
                return TRUE;
            return FALSE;
        }

        private function get_role_name() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['role_name']))
                    return '';

                return preg_replace('/\W/', '', preg_replace('/ /', '_', trim($_POST['role_name'])));
            }

            if ($this->role == NULL)
                return '';
            return $this->role->name;
        }

        private function is_submit_disabled() {
            if (!$this->is_editable)
                return TRUE;
            return FALSE;
        }

        private function get_capability_groups() {
            $caps_group = array();

            foreach ($this->main->get_capabilities() as $key => $value) {
                $deprecated = array_key_exists($key, WPFront_User_Role_Editor::$DEPRECATED_CAPABILITIES);
                $other = array_key_exists($key, WPFront_User_Role_Editor::$OTHER_CAPABILITIES);

                $caps_group[$key] = (OBJECT) array(
                            'caps' => $value,
                            'display_name' => $this->__($key),
                            'deprecated' => $deprecated,
                            'disabled' => !$this->is_editable, //!$this->is_editable || $deprecated, - to enable levels; for author drop down
                            'hidden' => $deprecated && !$this->main->display_deprecated(),
                            'key' => str_replace(' ', '-', $key),
                            'has_help' => !$other
                );
            }

            return $caps_group;
        }

        private function get_copy_from() {
            if (!$this->is_editable)
                return array();

            global $wp_roles;
            $roles = $wp_roles->role_names;
            asort($roles);
            return $roles;
        }

        private function is_role_exists() {
            return $this->role_exists;
        }

        private function is_error() {
            return $this->error;
        }

        public function copy_capabilities_callback() {
            if (empty($_POST['role'])) {
                echo '{}';
                die();
                return;
            }

            $role = get_role($_POST['role']);
            if ($role == NULL) {
                echo '{}';
                die();
                return;
            }

            echo json_encode($role->capabilities);
            die();
        }

        private function capability_checked($cap) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (empty($_POST['capabilities']))
                    return FALSE;

                return array_key_exists($cap, $_POST['capabilities']);
            }

            if ($this->role != NULL) {
                if (array_key_exists($cap, $this->role->capabilities))
                    return $this->role->capabilities[$cap];
            }

            return FALSE;
        }

        private function footer() {
            $this->main->footer();
        }

        private function image_url() {
            return $this->main->pluginURL() . 'images/';
        }

        private function get_help_url($cap) {
            return 'http://wpfront.com/wordpress-capabilities/#' . $cap;
        }

    }

}
