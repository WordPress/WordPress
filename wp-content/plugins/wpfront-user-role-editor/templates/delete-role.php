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
 * Template for WPFront User Role Editor Delete Roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>



<div class="wrap delete-roles">
    <form method="post">
        <?php $this->main->create_nonce(); ?>
        <h2><?php echo $this->__('Delete Roles'); ?></h2>
        <p><?php echo $this->__('You have specified these roles for deletion'); ?>:</p>
        <ul>
            <?php
            foreach ($this->get_deleting_roles() as $key => $value) {
                ?>
                <li>
                    <?php
                    printf('%s: <strong>%s</strong> [<strong>%s</strong>]', $this->__('Role'), $key, $value->display_name);
                    if($value->status_message != '') {
                        printf(' - <strong>%s</strong>', $value->status_message);
                    }
                    ?>
                    <input type="hidden" name="delete-roles[<?php echo $key; ?>]" value="1" />
                </li>
                <?php
            }
            ?>
        </ul>
        <p class="submit">
            <input type="submit" name="confirm-delete" id="submit" class="button" value="<?php echo $this->__('Confirm Deletion'); ?>" <?php echo $this->is_submit_allowed() ? '' : 'disabled'; ?>>
        </p>
    </form>
</div>


