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

/**
 * Template for WPFront User Role Editor List Roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php $this->main->verify_nonce(); ?>

<div class="wrap list-roles">
    <h2>
        <?php
        echo $this->__('Roles');
        if ($this->can_create()) {
            ?>
            <a href="<?php echo $this->add_new_url(); ?>" class="add-new-h2"><?php echo $this->__('Add New'); ?></a>
            <?php
        }
        ?>
    </h2>

    <ul class="subsubsub">
        <li>
            <?php
            $filter_data = array();
            $current_filter = $this->get_current_list_filter();
            foreach ($this->get_list_filters() as $key => $value) {
                $filter_data[] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', $value['url'], ($current_filter == $key ? 'current' : ''), $value['display'], $value['count']);
            }
            echo implode('|</li><li>', $filter_data);
            ?>
        </li>
    </ul>

    <form method = "post">
        <?php $this->main->create_nonce(); ?>
        <p class = "search-box">
            <label class = "screen-reader-text" for = "role-search-input"><?php echo $this->__('Search Roles') . ':'; ?></label>
            <input type="search" id="role-search-input" name="search" value="<?php echo $this->get_search_term(); ?>">
            <input type="submit" name="search-submit" id="search-submit" class="button" value="<?php echo $this->__('Search Roles'); ?>">
        </p>
        <?php $this->bulk_actions('top'); ?>
        <table class="wp-list-table widefat fixed users">
            <thead>
                <?php $this->table_header(); ?>
            </thead>
            <tfoot>
                <?php $this->table_header(); ?>
            </tfoot>
            <tbody id="the-list">
                <?php
                $index = 0;
                foreach ($this->get_roles() as $key => $value) {
                    ?>
                    <tr id="<?php echo $key; ?>" class="<?php echo $index % 2 == 0 ? 'alternate' : ''; ?>">
                        <th scope="row" class="check-column">
                            <label class="screen-reader-text" for="cb-select-<?php echo $key; ?>"><?php echo sprintf('Select %s', $value['display_name']) ?></label>
                            <input type="checkbox" name="selected-roles[<?php echo $key; ?>]" id="cb-select-<?php echo $key; ?>" />
                        </th>
                        <td class="displayname column-displayname">
                            <strong>
                                <?php
                                if (empty($value['edit_url']))
                                    echo $value['display_name'];
                                else
                                    printf('<a href="%s">%s</a>', $value['edit_url'], $value['display_name']);
                                ?>
                            </strong>
                            <br />
                            <div class="row-actions">
                                <?php
                                $links = array();
                                if ($this->can_edit()) {
                                    $links[] = sprintf('<span class="edit"><a href="%s">%s</a></span>', $value['edit_url'], ($value['is_editable'] ? $this->__('Edit') : $this->__('View')));
                                }
                                if ($this->can_delete() && $value['is_deletable']) {
                                    $links[] = sprintf('<span class="delete"><a href="%s">%s</a></span>', $value['delete_url'], $this->__('Delete'));
                                }
                                if(!empty($value['set_default_url'])) {
                                    $links[] = sprintf('<span class="set-default"><a href="%s">%s</a></span>', $value['set_default_url'], $this->__('Default'));
                                }
                                echo implode('|', $links);
                                ?>
                            </div>
                        </td>
                        <td class="rolename column-rolename">
                            <?php echo $key; ?>
                        </td>
                        <td class="roletype column-roletype">
                            <?php echo $value['is_default'] ? $this->__('Built-In') : $this->__('Custom'); ?>
                        </td>
                        <td class="userdefault column-userdefault num">
                            <?php
                            if ($value['user_default']) {
                                printf('<img class="user-default" src="%s" />', $this->image_url() . 'check-icon.png');
                            }
                            ?>
                        </td>
                        <td class="usercount column-usercount num">
                            <?php echo $value['user_count']; ?>
                        </td>
                        <td class="capscount column-capscount num">
                            <?php echo $value['caps_count']; ?>
                        </td>
                    </tr>
                    <?php
                    $index++;
                }
                ?>
            </tbody>
        </table>
        <?php $this->bulk_actions('bottom'); ?>
    </form>
    <?php $this->footer(); ?>
</div>