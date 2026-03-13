<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

class TastyIgniterInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = ['module' => 'app/{$name}/', 'extension' => 'extensions/{$vendor}/{$name}/', 'theme' => 'themes/{$name}/'];
    /**
     * Format package name.
     *
     * Cut off leading 'ti-ext-' or 'ti-theme-' if present.
     * Strip vendor name of characters that is not alphanumeric or an underscore
     *
     */
    public function inflectPackageVars(array $vars) : array
    {
        $extra = $this->package->getExtra();
        if ($vars['type'] === 'tastyigniter-module') {
            return $this->inflectModuleVars($vars);
        }
        if ($vars['type'] === 'tastyigniter-extension') {
            return $this->inflectExtensionVars($vars, $extra);
        }
        if ($vars['type'] === 'tastyigniter-theme') {
            return $this->inflectThemeVars($vars, $extra);
        }
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @return array<string, string>
     */
    protected function inflectModuleVars(array $vars) : array
    {
        $vars['name'] = $this->pregReplace('/^ti-module-/', '', $vars['name']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @param array<string, mixed> $extra
     * @return array<string, string>
     */
    protected function inflectExtensionVars(array $vars, array $extra) : array
    {
        if (!empty($extra['tastyigniter-extension']['code'])) {
            $parts = \explode('.', $extra['tastyigniter-extension']['code']);
            $vars['vendor'] = (string) $parts[0];
            $vars['name'] = (string) ($parts[1] ?? '');
        }
        $vars['vendor'] = $this->pregReplace('/[^a-z0-9_]/i', '', $vars['vendor']);
        $vars['name'] = $this->pregReplace('/^ti-ext-/', '', $vars['name']);
        return $vars;
    }
    /**
     * @param array<string, string> $vars
     * @param array<string, mixed> $extra
     * @return array<string, string>
     */
    protected function inflectThemeVars(array $vars, array $extra) : array
    {
        if (!empty($extra['tastyigniter-theme']['code'])) {
            $vars['name'] = $extra['tastyigniter-theme']['code'];
        }
        $vars['name'] = $this->pregReplace('/^ti-theme-/', '', $vars['name']);
        return $vars;
    }
}
