<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WordPress;

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuTop;
use Piwik\Piwik;
use Piwik\View;
use WpMatomo\Capabilities;

class Menu extends \Piwik\Plugin\Menu
{

    public function configureAdminMenu(MenuAdmin $menu)
    {
        $menu->remove('CoreAdminHome_MenuMeasurables', 'SitesManager_MenuManage');
        $menu->remove('SitesManager_Sites', 'SitesManager_MenuManage');
        $menu->remove('CoreAdminHome_MenuSystem', 'UsersManager_MenuUsers');
        $menu->remove('UsersManager_MenuPersonal', 'General_Security');
        $menu->remove('CoreAdminHome_MenuMeasurables', 'CoreAdminHome_TrackingCode');
        $menu->remove('CoreAdminHome_MenuMeasurables', 'General_Settings');
        $menu->remove('CorePluginsAdmin_MenuPlatform', 'General_API');
        $menu->remove('CoreAdminHome_MenuSystem', Piwik::translate('General_Plugins'));
        $menu->remove('CoreAdminHome_MenuSystem', 'General_Plugins');
        $menu->remove('General_Plugins');
        $menu->remove('CoreAdminHome_MenuDiagnostic', 'Diagnostics_ConfigFileTitle');
        $menu->remove('CoreAdminHome_MenuDiagnostic', 'Installation_SystemCheck');
    }

    public function configureTopMenu(MenuTop $menu)
    {
        if (is_multisite()) {

            $view = new View("@WordPress/blogselection");
            $sites = array();
            foreach ( get_sites() as $matomo_site ) {
                /** @var \WP_Site $matomo_site */
                switch_to_blog( $matomo_site->blog_id );
                if (function_exists('is_plugin_active')
                    && is_plugin_active('matomo/matomo.php')
                    && current_user_can(Capabilities::KEY_VIEW)) {
                    $sites[] = array('id' => $matomo_site->blog_id, 'name' => $matomo_site->blogname, 'url' => admin_url( 'admin.php?page=matomo-reporting' ));
                }
                restore_current_blog();
            }
            if (!empty($sites) && count($sites) > 1) {
                $view->currentId = get_current_blog_id();
                $view->sites = $sites;
                $menu->addHtml('BlogSelector', $view->render(), true, $order = 30, false);
            }
        }

    	$menu->addItem(__('WordPress Admin', 'matomo'), null, $this->urlForAction('goToWordPress'), '500', __('Go back to WordPress Admin Dashboard', 'matomo'), 'icon-close');
	    $menu->remove('General_Help');
    }
}
