<?php

namespace WPSentry\ScopedVendor\Composer\Installers;

use WPSentry\ScopedVendor\Composer\Util\Filesystem;
/**
 * Installer for Bitrix Framework. Supported types of extensions:
 * - `bitrix-d7-module` — copy the module to directory `bitrix/modules/<vendor>.<name>`.
 * - `bitrix-d7-component` — copy the component to directory `bitrix/components/<vendor>/<name>`.
 * - `bitrix-d7-template` — copy the template to directory `bitrix/templates/<vendor>_<name>`.
 *
 * You can set custom path to directory with Bitrix kernel in `composer.json`:
 *
 * ```json
 * {
 *      "extra": {
 *          "bitrix-dir": "s1/bitrix"
 *      }
 * }
 * ```
 *
 * @author Nik Samokhvalov <nik@samokhvalov.info>
 * @author Denis Kulichkin <onexhovia@gmail.com>
 */
class BitrixInstaller extends \WPSentry\ScopedVendor\Composer\Installers\BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'module' => '{$bitrix_dir}/modules/{$name}/',
        // deprecated, remove on the major release (Backward compatibility will be broken)
        'component' => '{$bitrix_dir}/components/{$name}/',
        // deprecated, remove on the major release (Backward compatibility will be broken)
        'theme' => '{$bitrix_dir}/templates/{$name}/',
        // deprecated, remove on the major release (Backward compatibility will be broken)
        'd7-module' => '{$bitrix_dir}/modules/{$vendor}.{$name}/',
        'd7-component' => '{$bitrix_dir}/components/{$vendor}/{$name}/',
        'd7-template' => '{$bitrix_dir}/templates/{$vendor}_{$name}/',
    );
    /**
     * @var string[] Storage for informations about duplicates at all the time of installation packages.
     */
    private static $checkedDuplicates = array();
    public function inflectPackageVars(array $vars) : array
    {
        /** @phpstan-ignore-next-line */
        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (isset($extra['bitrix-dir'])) {
                $vars['bitrix_dir'] = $extra['bitrix-dir'];
            }
        }
        if (!isset($vars['bitrix_dir'])) {
            $vars['bitrix_dir'] = 'bitrix';
        }
        return parent::inflectPackageVars($vars);
    }
    /**
     * {@inheritdoc}
     */
    protected function templatePath(string $path, array $vars = array()) : string
    {
        $templatePath = parent::templatePath($path, $vars);
        $this->checkDuplicates($templatePath, $vars);
        return $templatePath;
    }
    /**
     * Duplicates search packages.
     *
     * @param array<string, string> $vars
     */
    protected function checkDuplicates(string $path, array $vars = array()) : void
    {
        $packageType = \substr($vars['type'], \strlen('bitrix') + 1);
        $localDir = \explode('/', $vars['bitrix_dir']);
        \array_pop($localDir);
        $localDir[] = 'local';
        $localDir = \implode('/', $localDir);
        $oldPath = \str_replace(array('{$bitrix_dir}', '{$name}'), array($localDir, $vars['name']), $this->locations[$packageType]);
        if (\in_array($oldPath, static::$checkedDuplicates)) {
            return;
        }
        if ($oldPath !== $path && \file_exists($oldPath) && $this->io->isInteractive()) {
            $this->io->writeError('    <error>Duplication of packages:</error>');
            $this->io->writeError('    <info>Package ' . $oldPath . ' will be called instead package ' . $path . '</info>');
            while (\true) {
                switch ($this->io->ask('    <info>Delete ' . $oldPath . ' [y,n,?]?</info> ', '?')) {
                    case 'y':
                        $fs = new \WPSentry\ScopedVendor\Composer\Util\Filesystem();
                        $fs->removeDirectory($oldPath);
                        break 2;
                    case 'n':
                        break 2;
                    case '?':
                    default:
                        $this->io->writeError(array('    y - delete package ' . $oldPath . ' and to continue with the installation', '    n - don\'t delete and to continue with the installation'));
                        $this->io->writeError('    ? - print help');
                        break;
                }
            }
        }
        static::$checkedDuplicates[] = $oldPath;
    }
}
