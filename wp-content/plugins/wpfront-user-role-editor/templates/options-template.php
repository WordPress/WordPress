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
 * Template for WPFront User Role Editor Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php @$this->options_page_header($this->__('WPFront User Role Editor Settings'), WPFront_User_Role_Editor::OPTIONS_GROUP_NAME); ?>

<table class="form-table">
    <tr>
        <th scope="row">
            <?php echo $this->options->display_deprecated_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->display_deprecated_name(); ?>" <?php echo $this->options->display_deprecated() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->options->enable_role_capabilities_label(); ?>
        </th>
        <td>
            <input type="checkbox" name="<?php echo $this->options->enable_role_capabilities_name(); ?>" <?php echo $this->options->enable_role_capabilities() ? 'checked' : ''; ?> />
        </td>
    </tr>
</table>

<input type="hidden" name="nonce" value="<?php echo wp_create_nonce($_SERVER['REQUEST_URI']); ?>" />
<input type="hidden" name="referer" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />

<?php @$this->options_page_footer('user-role-editor-plugin-settings/', 'user-role-editor-plugin-faq/'); ?>

<script type="text/javascript">
    (function($) {
        $("#wpfront-user-role-editor-options #submit").click(function() {
            $(this).prop("disabled", true);

            var fields = $("#wpfront-user-role-editor-options form").find("input");
            var data = {};
            fields.each(function(i, e) {
                var ele = $(e);
                if (ele.attr("type") == "checkbox") {
                    if (ele.prop("checked")) {
                        data[ele.attr("name")] = "on";
                    }
                }
                else
                    data[ele.attr("name")] = ele.val();
            });
            data["action"] = "wpfront_user_role_editor_update_options";

            $.post(ajaxurl, data, function(url) {
                $(location).attr("href", url);
            });

            return false;
        });
    })(jQuery);
</script>
