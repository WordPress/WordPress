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

require_once("class-wpfront-user-role-editor-add-edit.php");

if (!class_exists('WPFront_User_Role_Editor_List')) {

    /**
     * Lists Roles
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_List {

        const MENU_SLUG = 'wpfront-user-role-editor-all-roles';

        private $main;
        private $role_data = NULL;

        function __construct($main) {
            $this->main = $main;
        }

        public function list_roles() {
            if (!$this->can_list())
                $this->main->permission_denied();

            if (!empty($_GET['edit_role'])) {
                $obj = new WPFront_User_Role_Editor_Add_Edit($this->main);
                $obj->add_edit_role(trim($_GET['edit_role']));
                return;
            }

            if (!empty($_GET['delete_role'])) {
                $obj = new WPFront_User_Role_Editor_Delete($this->main);
                $obj->delete_role(array(trim($_GET['delete_role'])));
                return;
            }

            if (!empty($_GET['set_default_role'])) {
                $this->set_default_role($_GET['set_default_role']);
                printf('<script type="text/javascript">document.location="%s";</script>', $this->list_url());
                return;
            }

            $obj = new WPFront_User_Role_Editor_Delete($this->main);
            if ($obj->is_pending_action()) {
                return;
            }

            $action = '';
            if (!empty($_POST['doaction_top']) && !empty($_POST['action_top'])) {
                $action = $_POST['action_top'];
            } else if (!empty($_POST['doaction_bottom']) && !empty($_POST['action_bottom'])) {
                $action = $_POST['action_bottom'];
            }

            if ($action == 'delete') {
                if (!empty($_POST['selected-roles'])) {
                    $obj = new WPFront_User_Role_Editor_Delete($this->main);
                    $obj->delete_role(array_keys($_POST['selected-roles']));
                    return;
                }
            }

            include($this->main->pluginDIR() . 'templates/list-roles.php');
        }

        private function __($s) {
            return $this->main->__($s);
        }

        private function can_list() {
            return $this->main->current_user_can('list_roles');
        }

        private function can_create() {
            return $this->main->current_user_can('create_roles');
        }

        private function can_edit() {
            return $this->main->current_user_can('edit_roles');
        }

        private function can_delete() {
            return $this->main->current_user_can('delete_roles');
        }

        private function list_url() {
            return admin_url('admin.php') . '?page=' . self::MENU_SLUG;
        }

        private function add_new_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_Add_Edit::MENU_SLUG;
        }

        private function edit_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&edit_role=';
        }

        private function delete_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&delete_role=';
        }

        private function set_default_url() {
            return admin_url('admin.php') . '?page=' . WPFront_User_Role_Editor_List::MENU_SLUG . '&nonce=' . wp_create_nonce($this->list_url()) . '&set_default_role=';
        }

        private function set_default_role($default_role) {
            if (empty($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], $this->list_url())) {
                $this->main->permission_denied();
                return;
            }

            if (!current_user_can('manage_options')) {
                $this->main->permission_denied();
                return;
            }

            update_option('default_role', $default_role);
        }

        private function get_roles() {
            if ($this->role_data != NULL)
                return $this->role_data;

            $this->role_data = array();

            global $wp_roles;
            $roles = $wp_roles->role_names;
            asort($roles, SORT_STRING | SORT_FLAG_CASE);

            $editable_roles = get_editable_roles();

            $user_default = get_option('default_role');

            foreach ($roles as $key => $value) {
                $this->role_data[$key] = array(
                    'role_name' => $key,
                    'display_name' => $value,
                    'is_default' => in_array($key, WPFront_User_Role_Editor::$DEFAULT_ROLES),
                    'user_count' => count(get_users(array('role' => $key))),
                    'caps_count' => count($wp_roles->roles[$key]['capabilities']),
                    'user_default' => $key == $user_default
                );

                if ($this->can_edit()) {
                    $this->role_data[$key]['edit_url'] = $this->edit_url() . $key;

                    if ($key === 'administrator')
                        $this->role_data[$key]['is_editable'] = FALSE;
                    else {
                        $this->role_data[$key]['is_editable'] = array_key_exists($key, $editable_roles);
                    }
                }

                if ($this->can_delete()) {
                    $this->role_data[$key]['delete_url'] = $this->delete_url() . $key;

                    if ($key === 'administrator')
                        $this->role_data[$key]['is_deletable'] = FALSE;
                    else {
                        $this->role_data[$key]['is_deletable'] = array_key_exists($key, $editable_roles);
                    }
                }

                if ($key != $user_default && current_user_can('manage_options')) {
                    $this->role_data[$key]['set_default_url'] = $this->set_default_url() . $key;
                }
            }

            switch ($this->get_current_list_filter()) {
                case 'all':
                    break;
                case 'haveusers':
                    foreach ($this->role_data as $key => $value) {
                        if ($this->role_data[$key]['user_count'] == 0)
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'nousers':
                    foreach ($this->role_data as $key => $value) {
                        if (!$this->role_data[$key]['user_count'] == 0)
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'builtin':
                    foreach ($this->role_data as $key => $value) {
                        if (!$this->role_data[$key]['is_default'])
                            unset($this->role_data[$key]);
                    }
                    break;
                case 'custom':
                    foreach ($this->role_data as $key => $value) {
                        if ($this->role_data[$key]['is_default'])
                            unset($this->role_data[$key]);
                    }
                    break;
            }

            $search = $this->get_search_term();
            $search = strtolower(trim($search));
            if ($search !== '') {
                foreach ($this->role_data as $key => $value) {
                    if (strpos(strtolower($value['display_name']), $search) === FALSE)
                        unset($this->role_data[$key]);
                }
            }

            return $this->role_data;
        }

        private function table_header() {
            ?>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php echo $this->__('Select All'); ?></label>
                    <input id="cb-select-all-1" type="checkbox" />
                </th>
                <th scope="col" id="rolename" class="manage-column column-rolename">
                    <a><span><?php echo $this->__('Display Name'); ?></span></a>
                </th>
                <th scope="col" id="rolename" class="manage-column column-rolename">
                    <a><span><?php echo $this->__('Role Name'); ?></span></a>
                </th>
                <th scope="col" id="roletype" class="manage-column column-roletype">
                    <a><span><?php echo $this->__('Type'); ?></span></a>
                </th>
                <th scope="col" id="userdefault" class="manage-column column-userdefault num">
                    <a><span><?php echo $this->__('User Default'); ?></span></a>
                </th>
                <th scope="col" id="usercount" class="manage-column column-usercount num">
                    <a><span><?php echo $this->__('Users'); ?></span></a>
                </th>
                <th scope="col" id="capscount" class="manage-column column-capscount num">
                    <a><span><?php echo $this->__('Capabilities'); ?></span></a>
                </th>
            </tr>
            <?php
        }

        private function bulk_actions($position) {
            ?>
            <div class="tablenav <?php echo $position; ?>">
                <div class="alignleft actions bulkactions">
                    <select name="action_<?php echo $position; ?>">
                        <option value="" selected="selected"><?php echo $this->__('Bulk Actions'); ?></option>
                        <?php if ($this->can_delete()) { ?>
                            <option value="delete"><?php echo $this->__('Delete'); ?></option>
                        <?php } ?>
                    </select>
                    <input type="submit" name="doaction_<?php echo $position; ?>" class="button bulk action" value="<?php echo $this->__('Apply'); ?>">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo sprintf($this->__('%s item(s)'), count($this->get_roles())); ?></span>
                    <br class="clear">
                </div>
            </div>
            <?php
        }

        private function get_search_term() {
            if (empty($_POST['search']))
                return '';

            return $_POST['search'];
        }

        private function get_list_filters() {
            $filter_data = array();

            global $wp_roles;
            $role_data = $wp_roles->role_names;

            $page = admin_url('admin.php') . '?page=' . self::MENU_SLUG;

            $filter_data['all'] = array(
                'display' => $this->__('All'),
                'url' => $page,
                'count' => count($role_data)
            );

            $count = 0;
            foreach ($role_data as $key => $value) {
                if (count(get_users(array('role' => $key))) > 0)
                    $count++;
            }
            $filter_data['haveusers'] = array(
                'display' => $this->__('Having Users'),
                'url' => $page . '&list=haveusers',
                'count' => $count
            );

            $filter_data['nousers'] = array(
                'display' => $this->__('No Users'),
                'url' => $page . '&list=nousers',
                'count' => count($role_data) - $count
            );

            $count = 0;
            foreach ($role_data as $key => $value) {
                if (in_array($key, WPFront_User_Role_Editor::$DEFAULT_ROLES))
                    $count++;
            }
            $filter_data['builtin'] = array(
                'display' => $this->__('Built-In'),
                'url' => $page . '&list=builtin',
                'count' => $count
            );

            $filter_data['custom'] = array(
                'display' => $this->__('Custom'),
                'url' => $page . '&list=custom',
                'count' => count($role_data) - $count
            );

            return $filter_data;
        }

        private function get_current_list_filter() {
            if (empty($_GET['list']))
                return 'all';

            $list = $_GET['list'];

            switch ($list) {
                case 'all':
                case 'haveusers':
                case 'nousers':
                case 'builtin':
                case 'custom':
                    break;
                default:
                    $list = 'all';
                    break;
            }

            return $list;
        }

        private function footer() {
            $this->main->footer();
        }

        private function image_url() {
            return $this->main->pluginURL() . 'images/';
        }

    }

}
