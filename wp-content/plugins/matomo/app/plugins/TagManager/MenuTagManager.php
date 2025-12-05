<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager;

use Piwik\Menu\MenuAbstract;
/**
 * Contains menu entries for the Tag Manager menu.
 * Plugins can implement the `configureTagManagerMenu()` method of the `Menu` plugin class to add, rename of remove
 * items. If your plugin does not have a `Menu` class yet you can create one using `./console generate:menu`.
 *
 * @method static \Piwik\Menu\MenuAdmin getInstance()
 */
class MenuTagManager extends MenuAbstract
{
    /**
     * Triggers the Menu.MenuAdmin.addItems hook and returns the admin menu.
     *
     * @return array
     */
    public function getMenu()
    {
        if (!$this->menu) {
            foreach ($this->getAllMenus() as $menu) {
                if (method_exists($menu, 'configureTagManagerMenu')) {
                    $menu->configureTagManagerMenu($this);
                }
            }
        }
        return parent::getMenu();
    }
}
