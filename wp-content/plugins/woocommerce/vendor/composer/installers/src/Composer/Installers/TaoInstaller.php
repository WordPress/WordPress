<?php
namespace Composer\Installers;

/**
 * An installer to handle TAO extensions.
 */
class TaoInstaller extends BaseInstaller
{
    const EXTRA_TAO_EXTENSION_NAME = 'tao-extension-name';

    protected $locations = array(
        'extension' => '{$name}'
    );
    
    public function inflectPackageVars($vars)
    {
        $extra = $this->package->getExtra();

        if (array_key_exists(self::EXTRA_TAO_EXTENSION_NAME, $extra)) {
            $vars['name'] = $extra[self::EXTRA_TAO_EXTENSION_NAME];
            return $vars;
        }

        $vars['name'] = str_replace('extension-', '', $vars['name']);
        $vars['name'] = str_replace('-', ' ', $vars['name']);
        $vars['name'] = lcfirst(str_replace(' ', '', ucwords($vars['name'])));

        return $vars;
    }
}
